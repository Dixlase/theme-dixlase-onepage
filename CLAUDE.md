# Dixlase OnePage - Dixlase CMS Theme

> **Type**: Dixlase Theme | **Version**: 0.1.1-dryrun.1
> **Namespace**: `Themes\DixlaseOnePage`

## Theme Overview

Dixlase OnePage theme. Plugin-driven single page / landing page theme.

## Architecture

Theme for **Dixlase CMS** (Laravel 12). The core application is located at `../../` relative to this theme directory.

### Key Paths
- **Core Root**: `../../` (includes `vendor/`, `artisan`, core `app/`)
- **This Theme**: `themes/DixlaseOnePage/`
- **Artisan Commands**: `docker exec -i <your-php-container> php artisan <command>`

### Theme Features
- dark_mode
- responsive
- front_page_builder
- custom_colors

## Development Rules

### Guidelines
- **No business logic in themes** — display only, no Eloquent queries in views
- **View namespace**: all theme views use `themes::`
- **Translation namespace**: all theme translations use `themes::`
- **Table prefix**: theme DB tables use `thm_`
- **Dark mode required**: support `dark:` Tailwind variant in all components
- **Responsive required**: mobile-first, test across all breakpoints
- **Plugin compatible**: integrate with plugins but function without them

### `custom/` Override Sync — required whenever a theme view is changed

Sites that use this theme (Brand, docs, demo…) may shadow individual theme blade files via the `custom/themes/DixlaseOnePage/…` mirror. Overrides are per-site injections (e.g. Brand's footer override adds a DixlaseMultilingual language switcher between the SNS row and the site name) built by copying the whole theme file and inserting the site-specific block. The theme deliberately stays plugin-agnostic; the sites carry their own overrides.

**When editing any file under `resources/views/`, check for a matching `custom/themes/DixlaseOnePage/…` mirror at every consuming site and apply the same change to each mirror in the same commit.** The deploy pipeline updates `themes/` via `git pull` but never touches `custom/`, so leaving the two out of sync silently ships the pre-update content on the production front page while the theme itself carries the new code.

- Locate mirrors: `find … -path '*/custom/themes/DixlaseOnePage/resources/views/<relative-path>' -type f`
- Local Brand dev mirror: `/Volumes/Data/Works/Dixlase/Sites/Brand/docker/html/custom/themes/DixlaseOnePage/…`
- Production Brand mirror: `/srv/dixlase/brand/custom/themes/DixlaseOnePage/…` — sync via `scp` after the theme deploy runs (`docker exec compose-php-1 php /var/www/apps/brand/artisan view:clear && … view:cache` afterwards to invalidate compiled blade)
- Sites without a matching mirror render the theme file directly — no action needed there

### PHP Standards
- PHP 8.3, Laravel 12, Livewire 4
- Use constructor property promotion
- Declare explicit return types for all methods
- Prefer PHPDoc blocks over inline comments
- Use `config()` instead of `env()` directly

### Frontend Stack
- **Tailwind CSS 3**: Utility-first, spacing with `gap-*`, dark mode with `dark:`
- **Alpine.js 3**: `x-data`, `@click`, `x-show`, `x-transition`, `x-cloak`
- **Vite**: Dev: `npm run dev`, Prod: `npm run build`

### Blade Components (Core)
Available: `x-ui-maintenance-banner`, `x-ui-admin-bar`, `x-form-text`,
`x-form-textarea`, `x-front.button`, `components.media-picker`, `components.save`

### Translation
- Always provide both `en/` and `ja/` translation files

### Asset Bundling
- Run `npm run dev` in theme directory during development
- Run `npm run build` before committing asset changes

### Testing
- Run tests: `docker exec -i <your-php-container> php artisan test`

### Code Formatting
- Pint auto-runs via hook after edits


## MCP Tools (Laravel Boost)
- `search-docs`: Search Laravel/Tailwind/Livewire documentation
- `tinker`: Debug
- `database-query`: Read-only database queries