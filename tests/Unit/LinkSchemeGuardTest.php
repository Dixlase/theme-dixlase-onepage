<?php

/**
 * This file is part of the Dixlase OnePage theme.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
 * https://exc-d.com
 */

namespace Themes\DixlaseOnePage\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;
use Themes\DixlaseOnePage\App\Http\Requests\UpdateThemeSettingsRequest;
use Themes\DixlaseOnePage\App\Providers\DixlaseOnePageServiceProvider;

/**
 * The hero call-to-action links and the footer SNS links are rendered into
 * href="" on the front page, which every unauthenticated visitor sees. Blade
 * escapes the HTML but does nothing about the scheme, so a stored
 * `javascript:` URL executes for anyone who clicks.
 *
 * Two separate holes were involved:
 *
 *  - the hero links were validated as `nullable|string|max:500`, i.e. not at
 *    all, so plain `javascript:alert(1)` was accepted;
 *  - generateSnsUrl() returned any value FILTER_VALIDATE_URL accepted, and
 *    that function accepts `javascript://%0aalert(1)`.
 *
 * They need different fixes, so both are pinned here.
 */
class LinkSchemeGuardTest extends TestCase
{
    /** Run a value through the Form Request's link rule. Returns true when accepted. */
    private function ruleAccepts(string $value): bool
    {
        $request = new UpdateThemeSettingsRequest();
        $method = new ReflectionMethod($request, 'safeLinkRule');
        $method->setAccessible(true);

        $failed = false;
        ($method->invoke($request))('hero_button_link', $value, function () use (&$failed) {
            $failed = true;
        });

        return ! $failed;
    }

    /** Run a value through the SNS URL builder. */
    private function snsUrl(string $platform, string $value): ?string
    {
        // Built without the constructor: generateSnsUrl is pure string
        // handling and needs none of the provider's container wiring.
        $provider = (new ReflectionClass(DixlaseOnePageServiceProvider::class))
            ->newInstanceWithoutConstructor();

        $method = new ReflectionMethod($provider, 'generateSnsUrl');
        $method->setAccessible(true);

        return $method->invoke($provider, $platform, $value);
    }

    /**
     * @return list<array{0: string}>
     */
    public static function dangerousLinks(): array
    {
        return [
            'javascript' => ['javascript:alert(1)'],
            'mixed case' => ['JavaScript:alert(1)'],
            'tab in scheme' => ["java\tscript:alert(1)"],
            'newline in scheme' => ["java\nscript:alert(1)"],
            'data uri' => ['data:text/html,<script>alert(1)</script>'],
            'vbscript' => ['vbscript:msgbox(1)'],
            'protocol relative' => ['//evil.example.com'],
        ];
    }

    #[DataProvider('dangerousLinks')]
    public function test_hero_links_refuse_executable_schemes(string $value): void
    {
        $this->assertFalse(
            $this->ruleAccepts($value),
            "The hero button link rule must refuse {$value}."
        );
    }

    /**
     * @return list<array{0: string}>
     */
    public static function legitimateLinks(): array
    {
        return [
            'anchor' => ['#contact'],
            'root relative' => ['/about'],
            'https' => ['https://example.com/landing'],
            'http' => ['http://example.com'],
            'mailto' => ['mailto:hello@example.com'],
            'tel' => ['tel:+81312345678'],
            'relative' => ['contact'],
        ];
    }

    #[DataProvider('legitimateLinks')]
    public function test_hero_links_still_accept_ordinary_targets(string $value): void
    {
        $this->assertTrue(
            $this->ruleAccepts($value),
            "The hero button link rule must still accept {$value}. Blocking normal links is not a fix."
        );
    }

    /**
     * The payload that motivated this: FILTER_VALIDATE_URL treats it as a
     * valid URL, so the old early return handed it straight to href.
     */
    public function test_sns_url_refuses_the_filter_validate_url_bypass(): void
    {
        $this->assertNull(
            $this->snsUrl('x', 'javascript://%0aalert(1)'),
            'generateSnsUrl() must not pass through a javascript: URL just because FILTER_VALIDATE_URL accepts it.'
        );
    }

    public function test_sns_url_keeps_working_for_real_links_and_handles(): void
    {
        $this->assertSame(
            'https://twitter.com/example',
            $this->snsUrl('x', 'https://twitter.com/example'),
            'A complete https URL must be returned unchanged.'
        );

        $this->assertSame(
            'https://twitter.com/example',
            $this->snsUrl('x', '@example'),
            'A bare handle must still be expanded into the platform URL.'
        );
    }

    /**
     * `javascript:alert(1)` is not a URL to FILTER_VALIDATE_URL, so it skipped
     * the scheme check and fell through to the per-platform match, where the
     * Discord arm returned the value unchanged into the footer of every page.
     *
     * @return array<string, array{0: string}>
     */
    public static function discordPayloads(): array
    {
        return [
            'javascript' => ['javascript:alert(document.cookie)'],
            'tab in scheme' => ["java\tscript:alert(1)"],
            'data url' => ['data:text/html,<script>alert(1)</script>'],
            'vbscript' => ['vbscript:msgbox(1)'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('discordPayloads')]
    public function test_discord_never_returns_an_executable_link(string $value): void
    {
        $this->assertNull($this->snsUrl('discord', $value));
    }

    public function test_discord_accepts_an_invite_url_or_code(): void
    {
        $this->assertSame('https://discord.gg/abc123', $this->snsUrl('discord', 'https://discord.gg/abc123'));
        $this->assertSame('https://discord.gg/abc-123', $this->snsUrl('discord', 'abc-123'));
    }

    public function test_an_unknown_platform_never_echoes_the_raw_value(): void
    {
        $this->assertNull($this->snsUrl('myspace', 'javascript:alert(1)'));
    }

    public function test_every_sns_field_uses_the_safe_link_rule(): void
    {
        $rules = (new UpdateThemeSettingsRequest())->rules();

        foreach ($rules as $field => $rule) {
            if (! str_starts_with($field, 'footer_sns_')) {
                continue;
            }

            $this->assertIsArray($rule, "{$field} must carry the safe-link closure");
            $this->assertNotEmpty(
                array_filter($rule, fn ($r) => $r instanceof \Closure),
                "{$field} must carry the safe-link closure"
            );
        }
    }
}
