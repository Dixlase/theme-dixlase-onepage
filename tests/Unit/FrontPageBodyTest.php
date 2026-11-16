<?php

/**
 * This file is part of the Dixlase OnePage theme.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
 * https://exc-d.com
 */

namespace Themes\DixlaseOnePage\Tests\Unit;

use App\Contracts\Front\FrontPageBodyProviderInterface;
use App\DTO\Front\FrontPageBodyContext;
use App\DTO\Front\FrontPageBodyDTO;
use App\DTO\Front\FrontPageBodyResult;
use App\Services\Front\FrontPageBodyResolver;
use App\Services\Plugin\PluginServiceResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Themes\DixlaseOnePage\App\Providers\DixlaseOnePageServiceProvider;

/**
 * The front page body: what the page shows between the hero and the footer.
 *
 * Until #146 the body was always core's front page content. A theme setting
 * now chooses it, and a plugin can offer its own through core's
 * `FrontPageBodyProviderInterface` (core 0.2.18).
 *
 * What is held still here is the fallback and the handover, because neither
 * fails loudly:
 *
 *   - **Every failure falls back to core's content.** Nothing chosen, a core
 *     without the contract, a provider that is gone, declines the id, or
 *     throws. A front page that goes blank because a plugin was disabled is
 *     the one outcome this setting must not be able to cause.
 *   - **`showHeading` follows the hero.** With a hero the body must not print
 *     its own `<h1>`; without one it must.
 *   - **The theme's mirrored constants match core's.** They are written as
 *     literals so the helpers work on a core that predates the contract, and
 *     nothing else would catch them drifting.
 *
 *   - **Out of scope**: the admin HTTP layer and the chooser's markup
 *     (AdminGetRoutesSmokeTest covers the screen rendering), and what any
 *     real provider renders.
 */
class FrontPageBodyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        require_once __DIR__.'/../../app/Helpers/DixlaseOnePageHelpers.php';

        // Theme migrations are not picked up by core's plugin migration
        // discovery, so the settings table is created here (same reason as
        // ThemePreviewSettingsOverrideTest).
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

    /**
     * Bind the preview-settings override, which dls_onepage_primary_setting()
     * prefers over the stored row. Used here to set the setting without
     * writing to the database, exactly as the settings preview does.
     *
     * @param  array<string, mixed>  $values
     */
    private function withSettings(array $values): void
    {
        $this->app->instance(DixlaseOnePageServiceProvider::PREVIEW_SETTINGS, $values);
    }

    /**
     * Register a provider under the capability tag the resolver reads, and
     * return a fresh resolver bound in the container.
     */
    private function withProvider(FrontPageBodyProviderInterface $provider): void
    {
        $this->app->bind('dls.test.front-page-body-provider', fn () => $provider);
        $this->app->tag(['dls.test.front-page-body-provider'], PluginServiceResolver::CAPABILITY_TAG);

        $this->app->forgetInstance(FrontPageBodyResolver::class);
    }

    // ---- the constants this theme mirrors ---------------------------------

    public function test_the_mirrored_core_body_value_matches_core(): void
    {
        $this->assertSame(FrontPageBodyResolver::BODY_CORE, DLS_ONEPAGE_FRONT_PAGE_BODY_CORE);
    }

    public function test_the_mirrored_surface_values_match_core(): void
    {
        $this->assertSame(
            FrontPageBodyProviderInterface::SURFACE_FRONT,
            DLS_ONEPAGE_FRONT_PAGE_SURFACE_FRONT
        );
        $this->assertSame(
            FrontPageBodyProviderInterface::SURFACE_PREVIEW,
            DLS_ONEPAGE_FRONT_PAGE_SURFACE_PREVIEW
        );
    }

    // ---- the stored choice ------------------------------------------------

    public function test_nothing_stored_reads_as_cores_front_page_content(): void
    {
        $this->assertSame(DLS_ONEPAGE_FRONT_PAGE_BODY_CORE, dls_onepage_front_page_body_choice());
    }

    public function test_an_empty_row_reads_as_cores_front_page_content(): void
    {
        $this->withSettings(['front_page_body' => '']);

        $this->assertSame(DLS_ONEPAGE_FRONT_PAGE_BODY_CORE, dls_onepage_front_page_body_choice());
    }

    public function test_a_stored_provider_choice_is_returned_as_stored(): void
    {
        $this->withSettings(['front_page_body' => 'dixlase-blog:index']);

        $this->assertSame('dixlase-blog:index', dls_onepage_front_page_body_choice());
    }

    // ---- the fallback -----------------------------------------------------

    public function test_cores_choice_renders_no_provider_body(): void
    {
        $this->withSettings(['front_page_body' => DLS_ONEPAGE_FRONT_PAGE_BODY_CORE]);

        $this->assertNull(dls_onepage_front_page_body());
    }

    public function test_a_choice_whose_provider_is_absent_renders_no_provider_body(): void
    {
        $this->withSettings(['front_page_body' => 'not-installed:index']);

        $this->assertNull(dls_onepage_front_page_body());
    }

    public function test_a_provider_that_declines_the_id_renders_no_provider_body(): void
    {
        $this->withProvider($this->provider(declines: true));
        $this->withSettings(['front_page_body' => 'dls-test-bodies:listing']);

        $this->assertNull(dls_onepage_front_page_body());
    }

    public function test_a_provider_that_throws_renders_no_provider_body(): void
    {
        $provider = $this->provider(throws: true);
        $this->withProvider($provider);
        $this->withSettings(['front_page_body' => 'dls-test-bodies:listing']);

        // The resolver answers null for the failures it can see, but a
        // third-party render is arbitrary code. An exception escaping it would
        // blank the front page, so the helper catches it and falls back.
        $this->assertNull(dls_onepage_front_page_body());

        // And it is the catch that produced that null, not a provider the
        // test failed to register: the render was reached and threw.
        $this->assertNotNull($provider->lastContext);
    }

    public function test_a_provider_that_declines_was_actually_reached(): void
    {
        $provider = $this->provider(declines: true);
        $this->withProvider($provider);
        $this->withSettings(['front_page_body' => 'dls-test-bodies:listing']);

        $this->assertNull(dls_onepage_front_page_body());
        $this->assertNotNull($provider->lastContext);
    }

    public function test_an_unavailable_provider_renders_no_provider_body(): void
    {
        $this->withProvider($this->provider(available: false));
        $this->withSettings(['front_page_body' => 'dls-test-bodies:listing']);

        $this->assertNull(dls_onepage_front_page_body());
    }

    // ---- the handover -----------------------------------------------------

    public function test_a_chosen_provider_supplies_the_body(): void
    {
        $this->withProvider($this->provider());
        $this->withSettings(['front_page_body' => 'dls-test-bodies:listing']);

        $result = dls_onepage_front_page_body();

        $this->assertInstanceOf(FrontPageBodyResult::class, $result);
        $this->assertStringContainsString('BODY FROM THE PROVIDER', $result->html());
        $this->assertSame('A listing', $result->title);
    }

    public function test_the_surface_reaches_the_provider(): void
    {
        $provider = $this->provider();
        $this->withProvider($provider);
        $this->withSettings(['front_page_body' => 'dls-test-bodies:listing']);

        dls_onepage_front_page_body(DLS_ONEPAGE_FRONT_PAGE_SURFACE_PREVIEW);

        $this->assertSame(
            FrontPageBodyProviderInterface::SURFACE_PREVIEW,
            $provider->lastContext?->surface
        );
    }

    public function test_show_heading_is_false_while_the_hero_carries_the_heading(): void
    {
        $provider = $this->provider();
        $this->withProvider($provider);
        $this->withSettings([
            'front_page_body' => 'dls-test-bodies:listing',
            'hero_height' => 'full',
        ]);

        dls_onepage_front_page_body();

        $this->assertFalse($provider->lastContext?->showHeading);
    }

    public function test_show_heading_is_true_with_no_hero(): void
    {
        $provider = $this->provider();
        $this->withProvider($provider);
        $this->withSettings([
            'front_page_body' => 'dls-test-bodies:listing',
            'hero_height' => 'none',
        ]);

        dls_onepage_front_page_body();

        $this->assertTrue($provider->lastContext?->showHeading);
    }

    // ---- the chooser's options -------------------------------------------

    public function test_no_provider_offers_no_groups(): void
    {
        $this->assertSame([], dls_onepage_front_page_body_groups());
    }

    public function test_a_provider_is_grouped_under_its_own_label(): void
    {
        $this->withProvider($this->provider());

        $groups = dls_onepage_front_page_body_groups();

        $this->assertArrayHasKey('dls-test-bodies', $groups);
        $this->assertSame('Test bodies', $groups['dls-test-bodies']['label']);
        $this->assertSame(
            ['dls-test-bodies:listing' => 'A listing'],
            $groups['dls-test-bodies']['choices']
        );
    }

    // ---- pagination -------------------------------------------------------

    public function test_the_page_number_comes_from_the_query_string(): void
    {
        $this->app['request'] = \Illuminate\Http\Request::create('/?page=3');

        $this->assertSame(3, dls_onepage_front_page_body_page());
    }

    public function test_a_page_number_below_one_reads_as_the_first_page(): void
    {
        $this->app['request'] = \Illuminate\Http\Request::create('/?page=-5');

        $this->assertSame(1, dls_onepage_front_page_body_page());
    }

    public function test_no_page_in_the_query_string_reads_as_the_first_page(): void
    {
        $this->app['request'] = \Illuminate\Http\Request::create('/');

        $this->assertSame(1, dls_onepage_front_page_body_page());
    }

    /**
     * A provider standing in for a plugin, recording the context it was
     * handed so the tests above can assert on it.
     */
    private function provider(
        bool $declines = false,
        bool $throws = false,
        bool $available = true,
    ): FrontPageBodyProviderInterface {
        return new class($declines, $throws, $available) implements FrontPageBodyProviderInterface
        {
            public ?FrontPageBodyContext $lastContext = null;

            public function __construct(
                private bool $declines,
                private bool $throws,
                private bool $available,
            ) {}

            public function getPluginSlug(): string
            {
                return 'dls-test-bodies';
            }

            public function isCapabilityAvailable(): bool
            {
                return $this->available;
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
                $this->lastContext = $context;

                if ($this->throws) {
                    throw new \RuntimeException('the provider blew up');
                }

                if ($this->declines) {
                    return null;
                }

                return new FrontPageBodyResult(
                    content: '<p>BODY FROM THE PROVIDER</p>',
                    title: 'A listing',
                );
            }
        };
    }
}
