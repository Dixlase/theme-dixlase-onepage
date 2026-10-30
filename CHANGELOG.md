# Changelog

All notable changes to the Dixlase OnePage theme are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this theme follows Semantic Versioning.

## [0.4.3] — 2026-10-30

### Changed

- The header, the hero and the error pages ask core whether a member is signed in, through
  `App\Services\Auth\SignedInMemberResolver`, to decide whether to leave room for the admin bar
  (#101, fixes #83). **The theme now needs core 0.1.13 or later**, the first release with that
  service; `requires.dixlase` says so, written as a floor (`>=0.1.13`) rather than a caret, so a
  core 0.2 release does not refuse to install or update it (#113; Dixlase/dixlase-core#790).
- The front stylesheet no longer scans the views of plugins that have admin screens only, so classes
  used only in the admin stay out of the stylesheet visitors load (#104, fixes #103).
- The preview frame renders its `<head>` from the same partial as the live layout, and both compute
  the appearance settings once, in shared partials (#102, fixes #97; #107, fixes #106). A setting
  now reaches the preview and the live page from one place.

### Development

- Pull requests against `next`, the development branch, run the checks (#95, fixes #94).
- The release build compares the files with the signature manifest at the tag and stops before
  building the ZIP on a mismatch (#96; part of Dixlase/dixlase-core#520).
- The deploy workflow recognises a checkout of this repository by its identity rather than by the
  text of its URL, and a site it refuses no longer holds the update back from the others (#110;
  fixes Dixlase/dixlase-core#518 and #519).
- The deploy workflow runs one deploy at a time; a second run waits for the first (#99, fixes #78).
- A stylesheet comment gives the header's real height (#100, fixes #98).

## [0.4.2] — 2026-10-24

### Fixed

- The theme settings preview renders the theme itself (#80, fixes #79). It was a hand-built
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
- In-page links stop just below everything fixed at the top of the page (the header and any
  banner) instead of under it (#93, fixes #92).

### Changed

- The front page's Markdown renders through core's `ContentPreviewService`, so the published
  front page matches the editor's preview and renders the way the blog and the pages do (#91,
  fixes #90): `{#id}` and `{.class}` give a block an id or classes, a single line break renders
  as `<br>`, and `#Heading` without a space becomes a heading.
- The seeders write `Powered by Dixlase` as the default footer description instead of
  `Powered by Dixlase CMS` (#88). A site keeps the description it already stored.
- The README, this changelog and the comments no longer name plugins that are not distributed
  publicly (#85, fixes #84; #87, fixes #86). No behaviour changed.

### Development

- Bump `vite` from 8.3.1 to 8.3.3, a build-time dependency that is not shipped to sites (#89).

## [0.4.1] — 2026-10-16

### Development

- A self-audit test runs the theme's views through the CSP strict-mode scanner and fails
  when one breaks it (#82, fixes #81). No runtime code changed.

## [0.4.0] — 2026-10-13

### Added

- A `head` stack in the theme's `<head>`, so a plugin or a page's own view can push
  page-specific tags and have them rendered: hreflang alternates, a feed link, a
  page-level `<meta>` (#66). Only `@stack('styles')` existed before, so a tag pushed
  to `head` was dropped without a word and hreflang alternates never reached the
  page. The preview layout carries the same stack, so a view renders
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
