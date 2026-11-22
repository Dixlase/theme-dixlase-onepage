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

namespace Themes\DixlaseOnePage\Tests\Feature\Admin;

use App\Contracts\Front\FrontPageBodyProviderInterface;
use App\DTO\Front\FrontPageBodyContext;
use App\DTO\Front\FrontPageBodyDTO;
use App\DTO\Front\FrontPageBodyResult;
use App\Enums\MemberRole;
use App\Enums\MemberStatus;
use App\Models\Member;
use App\Services\Front\FrontPageBodyResolver;
use App\Services\Plugin\PluginServiceResolver;
use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Themes\DixlaseOnePage\App\Models\ThemeSetting;
use Themes\DixlaseOnePage\App\Providers\DixlaseOnePageServiceProvider;

/**
 * The write side of the front page body setting (#151).
 *
 * `FrontPageBodyTest` covers reading a stored choice and every way the
 * resolution falls back. Nothing covered the other direction: that the
 * chooser's value is actually saved, and that it reaches the live preview
 * before it is saved.
 *
 * The save failure is silent. Drop `front_page_body` from
 * `UpdateThemeSettingsRequest::rules()` and `ThemeSetting::setValues()` stops
 * receiving it — the form still renders, the select still shows the stored
 * value, and saving reports success having changed nothing.
 *
 * The preview does not share that fate, and it is tested separately rather
 * than assumed to follow. See test_an_unsaved_choice_reaches_the_preview.
 */
class FrontPageBodySettingSaveTest extends TestCase
{
    use RefreshDatabase;

    private Member $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $_ENV['INSTALLED'] = 'true';
        $_SERVER['INSTALLED'] = 'true';

        // Pin a known-good 32-byte AES-256-CBC key: actingAs() writes to the
        // session, which goes through the Encrypter, and some test envs supply
        // an APP_KEY whose decoded length does not satisfy the cipher. Same
        // reasoning as AdminGetRoutesSmokeTest.
        $rawKey = random_bytes(32);
        config([
            'app.key' => 'base64:'.base64_encode($rawKey),
            'app.cipher' => 'AES-256-CBC',
        ]);
        $this->app->instance('encrypter', new Encrypter($rawKey, 'AES-256-CBC'));

        // Core's AppServiceProvider registers these at boot, but boot runs
        // with INSTALLED=false in the harness, so they are not populated yet.
        View::addNamespace('admin', [resource_path('views/'.config('themes.admin_theme', 'admin'))]);
        View::addNamespace('components', [resource_path('views/components')]);
        View::addNamespace('layouts', [resource_path('views/layouts')]);

        // The theme's own settings table. RefreshDatabase migrates core only.
        Artisan::call('migrate', [
            '--path' => 'themes/DixlaseOnePage/database/migrations',
            '--realpath' => false,
            '--force' => true,
        ]);

        $this->app->register(DixlaseOnePageServiceProvider::class);

        // The theme's own namespaces. The provider registers them at boot on
        // a real request; the harness boots with INSTALLED=false, so the
        // preview frame would not resolve without this.
        View::addNamespace('themes', base_path('themes/DixlaseOnePage/resources/views'));
        $this->app['translator']->addNamespace('themes', base_path('themes/DixlaseOnePage/lang'));

        // The admin URL comes from the `admin_url` setting; it is read from
        // config here and never written down.
        $adminUrl = (string) config('admin.admin_url', 'admin');
        $router = app('router');
        // `as` matters: routes/admin.php names its group `settings.themes.`
        // and core supplies the `admin.` prefix in a real request. Core's
        // route registration does not run in the harness (it boots with
        // INSTALLED=false), so route() would not resolve without this.
        $router->group([
            'prefix' => $adminUrl,
            'as' => 'admin.',
            'middleware' => ['web', 'auth:member'],
        ], function () {
            include base_path('themes/DixlaseOnePage/routes/admin.php');
        });
        $router->getRoutes()->refreshNameLookups();
        $router->getRoutes()->refreshActionLookups();

        $this->admin = Member::create([
            'account_name' => 'admin-user',
            'display_name' => 'Admin user',
            'email' => 'admin-user@example.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => MemberRole::ADMIN,
            'status' => MemberStatus::Active,
            'two_fa_mode' => 0,
        ]);
    }

    protected function tearDown(): void
    {
        $this->app->forgetInstance(DixlaseOnePageServiceProvider::PREVIEW_SETTINGS);
        $_ENV['INSTALLED'] = 'false';
        $_SERVER['INSTALLED'] = 'false';

        parent::tearDown();
    }

    /**
     * The two fields the settings form requires, plus whatever the test is
     * actually about. Posting only the field under test would fail validation
     * on `hero_main_title` and `appearance_mode` and prove nothing.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'hero_main_title' => 'A title',
            'appearance_mode' => '0',
        ], $overrides);
    }

    private function savedChoice(): ?string
    {
        return ThemeSetting::getValue('front_page_body');
    }

    // =========================================================================
    // Saving
    // =========================================================================

    public function test_a_chosen_body_is_saved(): void
    {
        $this->actingAs($this->admin, 'member')
            ->post(route('admin.settings.themes.settings.update'), $this->payload([
                'front_page_body' => 'dls-test-bodies:listing',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame('dls-test-bodies:listing', $this->savedChoice());
    }

    /**
     * Going back to core's front page content is a value like any other, not
     * an empty field, so it has to survive the round trip too.
     */
    public function test_choosing_cores_content_is_saved(): void
    {
        ThemeSetting::setValues(['front_page_body' => 'dls-test-bodies:listing']);

        $this->actingAs($this->admin, 'member')
            ->post(route('admin.settings.themes.settings.update'), $this->payload([
                'front_page_body' => DLS_ONEPAGE_FRONT_PAGE_BODY_CORE,
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame(DLS_ONEPAGE_FRONT_PAGE_BODY_CORE, $this->savedChoice());
    }

    /**
     * The rule is `nullable|string|max:255`. The length bound is the one part
     * of it that can be observed from outside, and observing it proves the
     * field is validated at all rather than passed through.
     */
    public function test_an_over_long_choice_is_rejected_and_changes_nothing(): void
    {
        ThemeSetting::setValues(['front_page_body' => 'dls-test-bodies:listing']);

        $this->actingAs($this->admin, 'member')
            ->post(route('admin.settings.themes.settings.update'), $this->payload([
                'front_page_body' => str_repeat('x', 256),
            ]))
            ->assertSessionHasErrors('front_page_body');

        $this->assertSame('dls-test-bodies:listing', $this->savedChoice());
    }

    // =========================================================================
    // The preview, before anything is saved
    // =========================================================================

    /**
     * The chooser is only usable if the frame shows the choice before it is
     * saved.
     *
     * This does **not** fail together with the save tests above, and the
     * difference is worth writing down. The save path reads
     * `$request->validated()`, which returns only attributes that have a
     * rule, so dropping `front_page_body` from `rules()` silently stops it
     * saving. `preview()` reads `Validator::valid()`, which returns all input
     * minus the attributes that *failed* — a key with no rule at all passes
     * straight through. So the preview keeps working with the rule gone, and
     * only the save tests catch that. Measured against this Laravel version,
     * not inferred from the names.
     */
    public function test_an_unsaved_choice_reaches_the_preview(): void
    {
        $this->withProvider();
        ThemeSetting::setValues(['front_page_body' => DLS_ONEPAGE_FRONT_PAGE_BODY_CORE]);

        $response = $this->actingAs($this->admin, 'member')
            ->post(route('admin.settings.themes.settings.preview'), $this->payload([
                'front_page_body' => 'dls-test-bodies:listing',
            ]));

        $response->assertStatus(200);
        $response->assertSee('PROVIDER BODY', false);

        // The stored value is untouched: the frame renders the posted value,
        // it does not save it. Asserted against the stored value rather than
        // against null, because a setting with no row reads as its default.
        $this->assertSame(
            DLS_ONEPAGE_FRONT_PAGE_BODY_CORE,
            $this->savedChoice(),
            'The preview must render the posted choice without saving it.'
        );
    }

    public function test_the_preview_falls_back_to_cores_content_without_a_choice(): void
    {
        $this->withProvider();

        $this->actingAs($this->admin, 'member')
            ->post(route('admin.settings.themes.settings.preview'), $this->payload([
                'front_page_body' => DLS_ONEPAGE_FRONT_PAGE_BODY_CORE,
            ]))
            ->assertStatus(200)
            ->assertDontSee('PROVIDER BODY', false);
    }

    // =========================================================================
    // Wiring
    // =========================================================================

    /**
     * @return array<string, array{0: string}>
     */
    public static function settingWiring(): array
    {
        return [
            'seeder' => ['database/seeders/DixlaseOnePageSettingsSeeder.php'],
            'provider defaults' => ['app/Providers/DixlaseOnePageServiceProvider.php'],
            'validation rules' => ['app/Http/Requests/UpdateThemeSettingsRequest.php'],
            'admin controller' => ['app/Http/Controllers/Admin/Settings/AdminThemeSettingsController.php'],
            'settings sidebar' => ['resources/views/admin/settings/themes/partials/sidebar.blade.php'],
        ];
    }

    #[DataProvider('settingWiring')]
    public function test_the_front_page_body_setting_is_wired(string $relativePath): void
    {
        $this->assertStringContainsString(
            'front_page_body',
            (string) file_get_contents(base_path('themes/DixlaseOnePage/'.$relativePath)),
            "{$relativePath} does not name `front_page_body`. A theme setting missing from any one "
            .'of these fails silently — dropped on save, read as null, or with no row on a fresh '
            .'install — so each file is asserted separately.'
        );
    }

    /**
     * `settings.blade.php` is deliberately absent from the list above, and
     * `hero_height`'s equivalent test does assert it. The difference is not an
     * oversight, so it is pinned rather than left to look like one.
     *
     * That file seeds the Alpine `x-data` state, which a control needs only
     * where Alpine writes the value — a toggle, a range, a hidden input set as
     * a property. `front_page_body` is a plain `<select name="front_page_body">`
     * inside `#theme-settings-form`, and the preview samples
     * `new FormData(form)` on a timer instead of listening for events, so every
     * field the form will submit is picked up with no Alpine entry.
     *
     * If the preview ever stops sampling the form, this fails — and that is
     * exactly when the select would need wiring after all.
     */
    public function test_the_preview_samples_the_whole_form_rather_than_listening(): void
    {
        $preview = (string) file_get_contents(
            base_path('themes/DixlaseOnePage/resources/views/admin/settings/themes/partials/preview.blade.php')
        );

        $this->assertStringContainsString('new FormData(form)', $preview);

        $settingsForm = (string) file_get_contents(
            base_path('themes/DixlaseOnePage/resources/views/admin/settings/themes/settings.blade.php')
        );

        $this->assertStringNotContainsString(
            'front_page_body',
            $settingsForm,
            'settings.blade.php has gained a front_page_body entry. If the chooser now needs '
            .'Alpine state, add that file to settingWiring() as well; if not, remove the entry.'
        );
    }

    /**
     * Register a provider under the capability tag the resolver reads.
     */
    private function withProvider(): void
    {
        $provider = new class implements FrontPageBodyProviderInterface
        {
            public function getPluginSlug(): string
            {
                return 'dls-test-bodies';
            }

            public function isCapabilityAvailable(): bool
            {
                return true;
            }

            public function getFrontPageBodyLabel(): string
            {
                return 'Test bodies';
            }

            public function availableFrontPageBodies(int $limit = 100): array
            {
                return [new FrontPageBodyDTO(id: 'listing', label: 'A listing')];
            }

            public function renderFrontPageBody(
                string $bodyId,
                FrontPageBodyContext $context,
            ): ?FrontPageBodyResult {
                return $bodyId === 'listing'
                    ? new FrontPageBodyResult(content: '<div>PROVIDER BODY</div>', title: 'A listing')
                    : null;
            }
        };

        $this->app->bind('dls.test.front-page-body-provider', fn () => $provider);
        $this->app->tag(['dls.test.front-page-body-provider'], PluginServiceResolver::CAPABILITY_TAG);
        $this->app->forgetInstance(FrontPageBodyResolver::class);
    }
}
