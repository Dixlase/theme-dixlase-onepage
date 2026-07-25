<?php

/**
 * This file is part of Dixlase OnePage.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
 * https://exc-d.com
 *
 * Dixlase OnePage is dual-licensed. You may use this file under either:
 *
 *   (a) the GNU General Public License version 3 or later, as published
 *       by the Free Software Foundation; or
 *
 *   (b) a commercial license agreement obtained from exc-D inc.
 *
 * Unless you have entered into a commercial license agreement, this
 * file is governed by the GPL terms below.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Themes\DixlaseOnePage\Tests\Unit;

use App\Contracts\Multilingual\SingletonTranslationResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Themes\DixlaseOnePage\App\Models\ThemeSetting;
use Themes\DixlaseOnePage\App\Multilingual\DixlaseOnePageSettingsProvider;

/**
 * Singleton-cardinality localization for the DixlaseOnePage theme's four
 * translatable hero settings.
 *
 * Covers the two read paths that bridge the central translation resolver
 * to theme-owned storage:
 *
 * 1. {@see DixlaseOnePageSettingsProvider} — primary-locale source the
 *    Multilingual plugin calls when no translation exists for the
 *    requested locale.
 * 2. `dls_onepage_localized_setting()` — front-side helper that drives
 *    the actual lookup chain: current locale via
 *    `SingletonTranslationResolver`, then site default locale, then
 *    primary value.
 *
 * Also asserts the theme.json shape matches the singleton contract
 * (cardinality + provider + 4 expected hero fields).
 */
class DixlaseOnePageSettingsLocalizationTest extends TestCase
{
    use RefreshDatabase;

    private const TYPE_KEY = 'dixlase-onepage:settings';

    private const EXPECTED_FIELDS = [
        'hero_main_title',
        'hero_sub_title',
        'hero_button_text',
        'hero_button_secondary_text',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Helpers file is normally loaded by DixlaseOnePageServiceProvider's
        // boot(), but the test environment may not boot the theme provider
        // (no active-theme row, INSTALLED=false, etc.). require_once is
        // idempotent.
        require_once __DIR__.'/../../app/Helpers/DixlaseOnePageHelpers.php';

        // Core's TestCase::migratePluginsUnderTest() runs `migrate` against
        // plugins/*/database/migrations but does NOT scan theme migrations
        // (themes have no analogous discovery hook yet). Run the theme's
        // own migrations here so ThemeSetting reads/writes hit a real
        // table in the in-memory SQLite RefreshDatabase set up.
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--path' => 'themes/DixlaseOnePage/database/migrations',
            '--realpath' => false,
            '--force' => true,
        ]);
    }

    public function test_provider_returns_primary_value_from_settings_table(): void
    {
        ThemeSetting::setValue('hero_main_title', 'プライマリ見出し');

        $this->assertSame(
            'プライマリ見出し',
            (new DixlaseOnePageSettingsProvider())->getPrimaryValue('hero_main_title'),
        );
    }

    public function test_provider_returns_null_for_missing_setting(): void
    {
        $this->assertNull((new DixlaseOnePageSettingsProvider())->getPrimaryValue('hero_main_title'));
    }

    public function test_helper_returns_primary_value_when_no_resolver_bound(): void
    {
        ThemeSetting::setValue('hero_main_title', 'プライマリ見出し');

        // No SingletonTranslationResolver bound — helper should fall
        // straight through to ThemeSetting::getValue().
        $this->assertFalse(app()->bound(SingletonTranslationResolver::class));

        $this->assertSame('プライマリ見出し', dls_onepage_localized_setting('hero_main_title'));
    }

    public function test_helper_returns_resolver_value_for_current_locale(): void
    {
        ThemeSetting::setValue('hero_main_title', 'プライマリ見出し');
        // Pin the primary locale to 'en' so the ja-viewer path below is
        // forced through the resolver — without an explicit default_locale,
        // the primary-locale short-circuit could match `ja` when the site
        // default locale is `ja`, and the resolver would be skipped.
        ThemeSetting::setValue('default_locale', 'en');

        $this->bindResolverReturning('hero_main_title', 'ja', '日本語見出し');
        app()->setLocale('ja');

        $this->assertSame('日本語見出し', dls_onepage_localized_setting('hero_main_title'));
    }

    public function test_helper_falls_back_to_primary_when_no_translation_exists(): void
    {
        ThemeSetting::setValue('hero_main_title', 'プライマリ見出し');

        // Resolver returns null for every (typeKey, field, locale) so
        // the helper exhausts the resolver chain and falls back to
        // the primary value.
        $this->bindResolverReturning('hero_main_title', '__nope__', null);
        app()->setLocale('en');

        $this->assertSame('プライマリ見出し', dls_onepage_localized_setting('hero_main_title'));
    }

    public function test_helper_returns_null_when_neither_translation_nor_primary_exists(): void
    {
        $this->bindResolverReturning('hero_main_title', '__nope__', null);
        app()->setLocale('en');

        $this->assertNull(dls_onepage_localized_setting('hero_main_title'));
    }

    /**
     * Empty-string translations must be treated as "no translation".
     *
     * The central translation manager UI stores every field of a locale
     * row even when the operator leaves them blank, so a brand-new locale
     * tab persists `""` for each field on first save. Without the
     * empty-string guard the helper would return `""` and the blade
     * would render an empty hero block instead of falling back to the
     * primary value.
     */
    public function test_helper_treats_empty_translation_as_fallthrough(): void
    {
        ThemeSetting::setValue('hero_main_title', 'プライマリ見出し');
        // Pin the primary locale away from 'en' so the en-viewer path
        // below is forced through the resolver (where the empty-string
        // guard is the relevant code path), not the primary-locale
        // short-circuit.
        ThemeSetting::setValue('default_locale', 'ja');

        // Resolver returns '' for the current locale (en) — exactly what
        // the multilingual UI stores when the operator opens an EN tab
        // and saves without filling it in. Helper must NOT return '';
        // it must fall through to the primary value.
        $this->bindResolverReturning('hero_main_title', 'en', '');
        app()->setLocale('en');

        $this->assertSame('プライマリ見出し', dls_onepage_localized_setting('hero_main_title'));
    }

    /**
     * When the current locale equals the provider's primary locale, the
     * helper must short-circuit straight to the primary value — no
     * resolver call. This prevents the resolver UI's empty-locale row
     * for the primary locale from rendering as blank content.
     */
    public function test_helper_short_circuits_primary_locale_to_primary_value(): void
    {
        ThemeSetting::setValue('hero_main_title', 'Primary heading');
        ThemeSetting::setValue('default_locale', 'en');

        // Bind a resolver that would return a different value if called —
        // if the short-circuit works, it should NOT be called for `en`.
        $this->bindResolverReturning('hero_main_title', 'en', 'should-not-be-returned');
        app()->setLocale('en');

        $this->assertSame('Primary heading', dls_onepage_localized_setting('hero_main_title'));
    }

    public function test_provider_primary_locale_uses_default_locale_setting_when_explicit(): void
    {
        ThemeSetting::setValue('default_locale', 'ja');

        $this->assertSame('ja', (new DixlaseOnePageSettingsProvider())->getPrimaryLocale());
    }

    public function test_provider_primary_locale_returns_null_when_setting_is_auto(): void
    {
        // 'auto' means: the theme settings text is a locale-neutral
        // default and every enabled locale (including the site default)
        // is translatable. The UI honors null by NOT excluding any
        // locale from the selector, and the helper walks the full
        // resolver chain instead of short-circuiting.
        ThemeSetting::setValue('default_locale', 'auto');

        $this->assertNull((new DixlaseOnePageSettingsProvider())->getPrimaryLocale());
    }

    public function test_provider_primary_locale_returns_null_when_setting_is_empty(): void
    {
        ThemeSetting::setValue('default_locale', '');

        $this->assertNull((new DixlaseOnePageSettingsProvider())->getPrimaryLocale());
    }

    public function test_theme_json_declares_singleton_with_provider(): void
    {
        $manifest = json_decode(
            (string) file_get_contents(__DIR__.'/../../theme.json'),
            true,
        );

        $this->assertIsArray($manifest);
        $this->assertContains('multilingual-content', $manifest['capabilities'] ?? []);

        $types = $manifest['multilingual_content']['types'] ?? [];
        $this->assertIsArray($types);
        $this->assertCount(1, $types);

        $type = $types[0];
        $this->assertSame(self::TYPE_KEY, $type['key']);
        $this->assertSame('singleton', $type['cardinality']);
        $this->assertSame(
            'Themes\\DixlaseOnePage\\App\\Multilingual\\DixlaseOnePageSettingsProvider',
            $type['provider'],
        );
        $this->assertArrayNotHasKey(
            'model',
            $type,
            'Singleton types must not carry the legacy `model` key.',
        );

        $declared = array_map(static fn (array $f): string => $f['name'], $type['fields']);
        $this->assertSame(
            self::EXPECTED_FIELDS,
            $declared,
            'Translatable field list must match the design scope: hero text '
            .'and button labels — copyright and other footer text are '
            .'intentionally excluded.',
        );
    }

    /**
     * Bind an in-memory {@see SingletonTranslationResolver} that returns
     * `$value` for the matching `(field, locale)` and null otherwise.
     */
    private function bindResolverReturning(string $field, string $locale, ?string $value): void
    {
        $resolver = new class($field, $locale, $value) implements SingletonTranslationResolver
        {
            public function __construct(
                private readonly string $expectedField,
                private readonly string $expectedLocale,
                private readonly ?string $value,
            ) {}

            public function resolve(string $typeKey, string $field, string $locale): mixed
            {
                if ($field === $this->expectedField && $locale === $this->expectedLocale) {
                    return $this->value;
                }

                return null;
            }

            public function store(string $typeKey, string $field, mixed $value, string $locale): void {}

            public function all(string $typeKey, string $field): array
            {
                return [];
            }

            public function exists(string $typeKey, string $field, string $locale): bool
            {
                return false;
            }

            public function delete(string $typeKey, string $field, ?string $locale = null): void {}

            public function getAvailableLocales(string $typeKey): array
            {
                return [];
            }
        };

        app()->instance(SingletonTranslationResolver::class, $resolver);
    }
}
