<?php

/**
 * This file is part of Dixlase OnePage.
 *
 * Copyright (C) 2026 exc-D inc.
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
    }

    public function test_provider_returns_primary_value_from_settings_table(): void
    {
        ThemeSetting::setValue('hero_main_title', 'プライマリ見出し');

        $this->assertSame(
            'プライマリ見出し',
            (new DixlaseOnePageSettingsProvider)->getPrimaryValue('hero_main_title'),
        );
    }

    public function test_provider_returns_null_for_missing_setting(): void
    {
        $this->assertNull((new DixlaseOnePageSettingsProvider)->getPrimaryValue('hero_main_title'));
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
