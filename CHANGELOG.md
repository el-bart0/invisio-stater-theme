# Changelog

All notable changes to Invisio are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/); this project adheres to
[Semantic Versioning](https://semver.org/).

When releasing, bump the version in **three** places together — `style.css`
(`Version:`), `functions.php` (`INVISIO_VERSION`), `package.json` (`version`) —
then tag `vX.Y.Z`.

## [0.1.0] — 2026-09-01

Initial release.

### Added

- **Build pipeline** — `@wordpress/scripts` + Sass, committed `build/`, one-command
  dev via `npm start` (watch build + BrowserSync live reload over the site's HTTPS
  cert).
- **Template hierarchy** — full `_s`-parity classic templates (`index`, `single`,
  `page`, `singular`, `archive`, `search`, `404`, `comments`, `sidebar`), content
  template parts, and template tags in `inc/template-tags.php`.
- **ACF blocks** — auto-registration from `src/blocks/*` (compiled to
  `build/blocks/*`), `acf-json/` load + save sync, an "Invisio" inserter category,
  and an example **Hero** block. Requires ACF PRO.
- **Editor lockdown** — inserter limited to `acf/invisio-*`; core block patterns,
  the remote pattern directory, and the front-end core block stylesheets are
  removed.
- **Baseline CSS** — deliberately minimal: reset, readable typography, form and
  media consistency, content measure, alignment classes, navigation, print. No
  design system.
- **Navigation** — dependency-free responsive menu (toggle, outside-click and
  resize close, keyboard submenu focus).
- **Tooling** — `bin/rename.mjs` (re-slug the theme for a new project),
  `bin/makepot.mjs` / `npm run make-pot` (generate the `.pot`, no WP-CLI needed),
  `.editorconfig`, ESLint/Stylelint via `@wordpress/scripts`.
