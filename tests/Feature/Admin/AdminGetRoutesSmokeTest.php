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

use App\Enums\MemberRole;
use App\Enums\MemberStatus;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Admin GET smoke test.
 *
 * Opens every admin GET route this theme registers and asserts the
 * server does not return 5xx, for both the admin and the editor role.
 *
 *   - **In scope**: status < 500 for every admin GET route under
 *     `admin.settings.themes.*` for admin and editor roles.
 *   - **Out of scope**: 200 vs 403 role boundaries, write flows,
 *     MySQL vs SQLite behaviour differences, CSP / inline-handler
 *     scanning.
 *
 * The route list is read from Route::getRoutes() by name prefix, so a
 * new admin GET route added to the theme later is picked up with no
 * change here.
 */
class AdminGetRoutesSmokeTest extends TestCase
{
    use RefreshDatabase;

    /** Route-name prefix the theme stamps on every admin route. */
    private const ROUTE_NAME_PREFIX = 'admin.settings.themes.';

    private Member $admin;

    private Member $editor;

    protected function setUp(): void
    {
        parent::setUp();

        $_ENV['INSTALLED'] = 'true';
        $_SERVER['INSTALLED'] = 'true';

        // Pin a known-good 32-byte AES-256-CBC key (see the plugin
        // smoke-test suite for the full rationale). actingAs() writes
        // to the session, which goes through the Encrypter; some test
        // envs supply an APP_KEY whose decoded length does not satisfy
        // AES-256-CBC.
        $rawKey = random_bytes(32);
        config([
            'app.key' => 'base64:'.base64_encode($rawKey),
            'app.cipher' => 'AES-256-CBC',
        ]);
        $this->app->instance('encrypter', new \Illuminate\Encryption\Encrypter($rawKey, 'AES-256-CBC'));

        // Core's AppServiceProvider registers these namespaces at boot,
        // but boot runs with INSTALLED=false in the test harness, so
        // the namespaces are not populated by the time setUp runs.
        \Illuminate\Support\Facades\View::addNamespace(
            'admin',
            [resource_path('views/'.config('themes.admin_theme', 'admin'))],
        );
        \Illuminate\Support\Facades\View::addNamespace(
            'components',
            [resource_path('views/components')],
        );
        \Illuminate\Support\Facades\View::addNamespace(
            'layouts',
            [resource_path('views/layouts')],
        );

        // Register the theme's own ServiceProvider so view / translator
        // namespaces and any theme-level bindings are in place for the
        // real request lifecycle.
        $this->app->register(\Themes\DixlaseOnePage\App\Providers\DixlaseOnePageServiceProvider::class);

        // Register the theme's admin routes under the admin URL prefix
        // with a reduced middleware stack. Core's `web + auth:member`
        // is enough to reach the controller; stripping the role-gate
        // middleware (check.menu.access, check.menu.edit) keeps the
        // test deterministic without pulling in permission fixtures.
        // The smoke test only cares about 5xx; role boundaries are a
        // separate concern.
        $adminUrl = (string) config('admin.admin_url', 'admin');
        $router = app('router');
        $router->group([
            'prefix' => $adminUrl,
            'middleware' => ['web', 'auth:member'],
        ], function () {
            include base_path('themes/DixlaseOnePage/routes/admin.php');
        });
        $router->getRoutes()->refreshNameLookups();
        $router->getRoutes()->refreshActionLookups();

        $this->admin = $this->makeMember('admin-user', MemberRole::ADMIN);
        $this->editor = $this->makeMember('editor-user', MemberRole::EDITOR);
    }

    protected function tearDown(): void
    {
        $_ENV['INSTALLED'] = 'false';
        $_SERVER['INSTALLED'] = 'false';
        parent::tearDown();
    }

    public function test_no_admin_get_route_returns_5xx_for_admin_role(): void
    {
        $urls = $this->adminGetRouteUrls();

        foreach ($urls as $name => $url) {
            $response = $this->actingAs($this->admin, 'member')->get($url);
            $status = $response->baseResponse->getStatusCode();

            $this->assertLessThan(
                500,
                $status,
                "GET {$url} ({$name}) returned {$status} as admin",
            );
        }
    }

    public function test_no_admin_get_route_returns_5xx_for_editor_role(): void
    {
        $urls = $this->adminGetRouteUrls();

        foreach ($urls as $name => $url) {
            $response = $this->actingAs($this->editor, 'member')->get($url);
            $status = $response->baseResponse->getStatusCode();

            $this->assertLessThan(
                500,
                $status,
                "GET {$url} ({$name}) returned {$status} as editor",
            );
        }
    }

    private function makeMember(string $slug, MemberRole $role): Member
    {
        return Member::create([
            'account_name' => $slug,
            'display_name' => ucfirst($slug),
            'email' => $slug.'@example.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => $role,
            'status' => MemberStatus::Active,
            'two_fa_mode' => 0,
        ]);
    }

    /**
     * Discover every admin GET route the theme registered.
     *
     * @return array<string, string> Map of route name to request URL.
     */
    private function adminGetRouteUrls(): array
    {
        $urls = [];
        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if ($name === null || ! str_starts_with($name, self::ROUTE_NAME_PREFIX)) {
                continue;
            }
            if (! in_array('GET', $route->methods(), true)) {
                continue;
            }
            $urls[$name] = '/'.ltrim($route->uri(), '/');
        }

        $this->assertNotEmpty(
            $urls,
            'No admin GET routes were discovered; did routes/admin.php load correctly in setUp?',
        );

        return $urls;
    }
}
