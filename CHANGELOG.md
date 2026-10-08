# Changelog

All notable changes to the Dixlase OnePage theme are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this theme follows Semantic Versioning.

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
