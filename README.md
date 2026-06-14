# Dixlase OnePage

For Japanese, see [README.ja.md](./README.ja.md).

The default theme that ships with every Dixlase install: a plugin-driven single-page / landing-page theme with a configurable hero (image or video background, headline, sub-headline, primary / secondary call-to-action buttons), header logo and favicon, a footer with description / copyright / SNS links, dark-mode support, a primary-color picker, optional language-switcher and inquiry-form integration, and translatable hero text via the multilingual runtime.

## Features

- **Single-page layout** — Hero, optional inline front-page content, and footer rendered from one route.
- **Hero section** — Background image or video, main title, sub title, and two configurable CTA buttons (each toggleable from settings).
- **Header** — Logo and favicon, with the active site menu rendered from DixlaseMenus when installed.
- **Footer** — Description, copyright with auto-updating year, link list, and ~12 SNS link slots (Instagram / X / Facebook / TikTok / Bluesky / Threads / LinkedIn / YouTube / Pinterest / Discord / GitHub).
- **Dark mode** — Auto (follows OS), Light, or Dark, picked from settings.
- **Primary color** — Choose the accent color used for buttons and links from a fixed palette.
- **Multilingual hero text** — Hero title / sub-title / button labels are translatable singletons under `dixlase-onepage:settings`, with an explicit default-locale setting that excludes the authoring language from the translation editor.
- **Plugin integration** — DixlaseMenus for header / footer navigation, DixlaseInquiry for the contact form above the footer, DixlaseMultilingual for the language switcher.
- **Front-page builder** — When DixlasePages and the multilingual runtime are present, the front-page content from the page builder is rendered inline between the hero and the footer.

## Installation

**Bundled with Dixlase Core.** The theme is installed and activated automatically the first time Core is installed — there is no separate "download and enable" step in the admin panel. The install wizard runs the theme's migrations and seeders and switches the active theme to OnePage as the final step before the welcome screen.

The theme can still be re-applied or rolled back through the standard Dixlase theme management screens once the site is up.

## Usage

Once installed, the theme renders the home route as the OnePage layout. Open **Dashboard → Appearance → Theme Settings** to edit:

- Hero background (image / video), main title, sub title, button text and link, and button visibility.
- Header logo and favicon.
- Footer description, links, copyright suffix, and per-platform SNS URLs.
- Default locale (the language hero text is authored in), primary color, and appearance mode.
- Plugin slots: header menu, footer menu, and the inquiry-form toggle.

Translations for the hero text live in the central translation manager (under DixlaseMultilingual) when enabled.

## Capabilities

This theme declares the following capabilities in `theme.json`:

- **`multilingual-content`** — Singleton-cardinality registration for the theme's translatable hero text. The provider (`DixlaseOnePageSettingsProvider`) reads the primary-locale value from the theme's own settings table and short-circuits the translation lookup when the current locale matches the configured default.

## Documentation

A dedicated documentation site for the theme is planned; until it ships, the theme's behavior is described inline in the admin **Theme Settings** screen and in this README.

## License

Dixlase OnePage is distributed under a **dual license**:

- **Open Source License**: [GNU General Public License v3](./LICENSE)
- **Commercial License**: A separate commercial license is planned for use cases where GPL v3 compliance is not feasible. **It is not yet available** — only a draft of the eventual terms is present in [LICENSE.commercial](./LICENSE.commercial). For availability timing or other questions, contact **info@dixlase.org**.

A short overview of how these files fit together is in [NOTICE](./NOTICE) ([日本語](./NOTICE.ja)).

## Contributing

The Contributor License Agreement (CLA) is still under review, so code Pull Requests are not being accepted at this time. Once the CLA is finalized, contributions will open under the [Dixlase Copyright Policy](https://github.com/Dixlase/dixlase-core/blob/main/COPYRIGHT-POLICY.md) and the Dixlase CLA (see [CONTRIBUTING.md](./CONTRIBUTING.md)). Bug reports and proposals via Issues are welcome in the meantime.

---

(C) exc-D inc.
