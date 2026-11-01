<?php

/**
 * This file is part of the Dixlase OnePage theme.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
 * https://exc-d.com
 */

namespace Themes\DixlaseOnePage\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Themes\DixlaseOnePage\App\Providers\DixlaseOnePageServiceProvider;

/**
 * The social links rendered in one fixed order and the settings form listed
 * them in another — the footer opened Facebook, X, Instagram and the form
 * Instagram, X, Facebook, so the order an operator filled the boxes in was
 * not the order a visitor saw. Neither could be changed (#116).
 *
 * Both now read one catalogue and one resolved order.
 *
 * The resolver is where the care is. The stored value outlives the
 * catalogue, so it is rebuilt rather than trusted: a slug that is no longer
 * a service is dropped, a repeated one counts once, and a service the theme
 * has **gained** since the operator last saved is appended rather than lost.
 * That last one is the dangerous direction — adding a service to the theme
 * must not make it invisible to every site that had already saved an order.
 */
class SnsOrderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        require_once __DIR__.'/../../app/Helpers/DixlaseOnePageHelpers.php';

        Artisan::call('migrate', [
            '--path' => 'themes/DixlaseOnePage/database/migrations',
            '--realpath' => false,
            '--force' => true,
        ]);

        $this->app->register(DixlaseOnePageServiceProvider::class);
    }

    protected function tearDown(): void
    {
        $this->app->forgetInstance(DixlaseOnePageServiceProvider::PREVIEW_SETTINGS);

        parent::tearDown();
    }

    private function storedOrder(?string $value): void
    {
        $this->app->instance(
            DixlaseOnePageServiceProvider::PREVIEW_SETTINGS,
            $value === null ? [] : ['sns_links_order' => $value]
        );
    }

    private function contents(string $relativePath): string
    {
        $path = dirname(__DIR__, 2).'/'.$relativePath;

        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }

    public function test_a_site_that_has_never_ordered_them_gets_the_catalogue_order(): void
    {
        $this->storedOrder(null);

        $this->assertSame(
            array_keys(dls_onepage_sns_services()),
            dls_onepage_sns_order(),
            'With nothing saved the order must be the catalogue\'s own, which is what the footer '
            .'rendered before this setting existed. An existing site must not rearrange itself.'
        );
    }

    public function test_a_saved_order_is_honoured(): void
    {
        $this->storedOrder('github,x');

        $order = dls_onepage_sns_order();

        $this->assertSame(['github', 'x'], array_slice($order, 0, 2));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function awkwardStoredValues(): array
    {
        return [
            'empty' => [''],
            'only slugs that are not services' => ['myspace,orkut'],
            'the same slug repeatedly' => ['x,x,x'],
            'trailing and doubled separators' => ['x,,github,'],
            'padded with spaces' => [' x , github '],
        ];
    }

    #[DataProvider('awkwardStoredValues')]
    public function test_every_service_survives_an_awkward_stored_value(string $stored): void
    {
        $this->storedOrder($stored);

        $order = dls_onepage_sns_order();

        $this->assertEqualsCanonicalizing(
            array_keys(dls_onepage_sns_services()),
            $order,
            "Stored as '{$stored}', the order must still name every service exactly once. "
            .'Losing one hides it from the footer with nothing to say so.'
        );
        $this->assertSame(count($order), count(array_unique($order)), 'No slug may appear twice.');
    }

    public function test_a_service_the_catalogue_has_gained_is_appended_not_lost(): void
    {
        // What a site that saved an order before the service existed holds.
        $services = array_keys(dls_onepage_sns_services());
        $withoutLast = array_slice($services, 0, -1);
        $missing = end($services);

        $this->storedOrder(implode(',', $withoutLast));

        $order = dls_onepage_sns_order();

        $this->assertContains(
            $missing,
            $order,
            'A service missing from the saved order must still render. Adding one to the theme '
            .'otherwise makes it invisible to every site that had already saved an order.'
        );
        $this->assertSame($missing, end($order), 'and it belongs at the end, not the front.');
    }

    public function test_the_catalogue_describes_every_service_completely(): void
    {
        foreach (dls_onepage_sns_services() as $slug => $service) {
            foreach (['label', 'icon', 'solid', 'prefix'] as $field) {
                $this->assertArrayHasKey($field, $service, "{$slug} is missing `{$field}`.");
            }

            $this->assertNotSame('', $service['prefix'], "{$slug} needs the prefix the settings form shows.");
            $this->assertStringStartsWith('fa-', $service['icon'], "{$slug} needs a Font Awesome brand icon.");
        }
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function viewsThatListTheServices(): array
    {
        return [
            'footer' => ['resources/views/partials/footer.blade.php'],
            'settings sidebar' => ['resources/views/admin/settings/themes/partials/sidebar.blade.php'],
        ];
    }

    #[DataProvider('viewsThatListTheServices')]
    public function test_a_view_reads_the_catalogue_rather_than_keeping_its_own(string $relativePath): void
    {
        $view = $this->contents($relativePath);

        $this->assertStringContainsString(
            'dls_onepage_sns_services()',
            $view,
            "{$relativePath} must take the services from the catalogue. Two lists is what #116 "
            .'found already drifted — the footer and the form disagreed on the first three.'
        );
        $this->assertStringNotContainsString(
            "'fa-x-twitter'",
            $view,
            "{$relativePath} still spells a brand icon out, so it is keeping a second catalogue."
        );
    }
}
