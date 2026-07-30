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