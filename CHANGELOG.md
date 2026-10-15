# Changelog

All notable changes to the Dixlase OnePage theme are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this theme follows Semantic Versioning.

## [Unreleased]

### Fixed

- The theme settings preview renders the theme itself (#79). It was a hand-built
  copy of the header, hero, contact section and footer — a second implementation
  of the theme's appearance, drifting from the first with every change. It read
  the untranslated setting, so the hero previewed in the wrong language on a
  translated site; it never resolved media ids, so the hero background was
  missing; and the appearance switcher added in 0.3.0 did not exist in it at
  all. The preview now goes through the theme's own partials, so a `custom/`
  override shows up in it exactly as on the live page, and a setting changed in
  the form is still reflected without saving.
- The preview layout shows the appearance switcher fixed in the corner, which it
  had been missing since that placement was added.
- The preview no longer disappears when the operator's language differs from the
  front page's.

## [0.4.0] — 2026-10-13

### Added

- A `head` stack in the theme's `<head>`, so a plugin or a page's own view can push
  page-specific tags and have them rendered: hreflang alternates, a feed link, a
  page-level `<meta>` (#66). Only `@stack('styles')` existed before, so a tag pushed
  to `head` was dropped without a word and DixlaseMultilingual's hreflang alternates
  never reached the page. The preview layout carries the same stack, so a view renders
  the same in the admin preview frame as on the live page. A page that pushes nothing
  renders exactly as before.

### Fixed

- The appearance switcher fixed in the corner is easier to hit on a phone (#74).
  The collapsed trigger and each of the three mode buttons are now a 44px tap
  target below 768px, instead of 34px and 32px — the same target the footer's
  SNS row is built on. From 768px up nothing changes.

### Development

- A smoke test opens every admin GET route of the theme as the admin and the editor
  role and checks that none returns a server error (#73, fixes #72). No runtime code
  changed.

## [0.3.0] — 2026-10-11

### Added

- A fixed appearance switcher in the corner of the screen (#68).

### Changed

- The appearance master toggle is folded into the two placements, so one setting
  controls where the switcher appears instead of a separate on/off plus a position
  (#70).

### Fixed

- Re-signed. The signature no longer matched the tree, so sites reported the theme
  as `signature_invalid` and deducted from its health score. Changes had reached
  `main` through pull-request merges, which do not run the local signing hook.

## [0.2.0] — 2026-10-08

### Added

- Visitors can switch the appearance mode (auto / light / dark) from a control in the footer
  (#57, fixes #56). The operator turns it on under the theme settings; it is **off by
  default**. The appearance mode in the theme settings stays the default, and a visitor's
  choice is remembered in their browser. While the switcher is off, stored choices are
  ignored, so turning it off returns every visitor to the operator's mode. Colours
  cross-fade on change, except with reduced motion.

### Upgrade notes

No action needed: nothing changes until the switcher is turned on. Re-signed with
`dixlase-authority-2026`.

## [0.1.2] — 2026-10-08

### Fixed

- The social icons in the footer dim on hover instead of taking on the link colour, so each
  mark keeps its own shape and colour (#59, fixes #58). Other footer links still change
  colour on hover.

## [0.1.1] — 2026-10-01

### Changed

- Build tooling: `vite` 5 → 8.3.1, with `esbuild` and `postcss` 8.5.28 updated
  alongside (#52), and `immutable` 5.1.9 (#50). This clears the Dependabot advisories for those packages,
  all of which affect only the development server and the asset build — nothing in
  them is shipped to sites. The prebuilt assets in the release ZIP are produced by
  the same build as before; only their hashed file names change.

## [0.1.0] — 2026-10-01
Initial release. Requires Dixlase `^0.1.0` (Plugin API `^0.1`), PHP `>= 8.3`.

### Added

- Plugin-driven single-page / landing-page theme.
- Light and dark mode (`dark_mode`) and a fully responsive layout (`responsive`).
- Front-page builder support (`front_page_builder`).
- Customizable colors (`custom_colors`).
