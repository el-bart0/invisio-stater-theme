# Invisio

Invisio is our starter theme for customer WordPress sites: a modern classic-PHP
base with a full `_s`-style template hierarchy and an ACF-block workflow, built
with [`@wordpress/scripts`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/)
and Sass.

- **Architecture:** classic PHP templates (no FSE, no `theme.json`)
- **Content:** ACF blocks — source in `src/blocks/`, compiled to `build/blocks/`, auto-registered
- **PHP:** plain partials in `inc/`, loaded by `functions.php` (no Composer)
- **Distribution:** copy this repo per project and rename
- **Build output:** `build/` is committed, so a fresh copy runs without a build step

This repo also works as a GitHub **template repository** — use "Use this template"
to start a new project instead of cloning + stripping history by hand.

## Starting a new project

1. Copy this repository into `wp-content/themes/` and rename the folder (or start
   from **Use this template** on GitHub).
2. Rename the theme:
   ```
   npm run rename -- <slug> "<Theme Name>"
   ```
   e.g. `npm run rename -- acme "Acme Co"`. This rewrites the text domain, PHP
   function/constant prefixes, ACF block namespace and field/group prefixes, and
   `@package` tags across the codebase, and renames any matching files. It does
   **not** touch `node_modules/`, `.git/`, `build/`, or `bin/`. Review the diff
   afterwards and fill in `Author` / `Theme URI` in `style.css`.
3. `npm install`
4. `npm run build` — only needed when you change anything under `src/`
5. `npm run make-pot` — regenerate translations for the new text domain
6. Activate the theme in **Appearance → Themes**.

## Development

```
npm start        # wp-scripts watch build + BrowserSync live reload (one command)
npm run build    # one-shot production build → build/
npm run lint-js
npm run lint-style
npm run format
```

`npm start` proxies the site set in `bs-config.js` (`WP_DEV_URL`, default
`https://starter-tema.local`). Override per project inline:

```
WP_DEV_URL=https://acme.local npm start
```

After any change under `src/`, run `npm run build` and commit the updated
`build/` directory.

## Versioning

Bump the version in **three** places together, then tag `vX.Y.Z`:

- `style.css` (`Version:`)
- `functions.php` (`INVISIO_VERSION`)
- `package.json` (`version`)

Record the change in `CHANGELOG.md` ([Keep a Changelog](https://keepachangelog.com/en/1.1.0/) /
[SemVer](https://semver.org/)).

## Layout

| Path | Purpose |
|---|---|
| `functions.php` | Loads the `inc/` partials |
| `inc/` | `setup.php`, `enqueue.php`, `helpers.php` (more added per bucket) |
| `template-parts/` | Reusable template fragments |
| `src/blocks/<name>/` | ACF block source (`block.json`, `render.php`, `style.scss`, `index.js`) |
| `build/blocks/<name>/` | Compiled blocks — auto-registered by `inc/blocks.php` |
| `acf-json/` | ACF field-group sync (load + save point) |
| `src/scss/`, `src/js/` | Theme-wide source assets |
| `build/` | Compiled assets — **committed** |
| `languages/` | Translation files |

## Blocks

ACF blocks live in `src/blocks/<name>/`. Each folder has a `block.json` (with an
`"acf"` key), a `render.php` server template, a `style.scss`, and a one-line
`index.js` that only exists so the SCSS compiles. `npm run build` compiles them
into `build/blocks/<name>/`; `inc/blocks.php` registers everything it finds there
on `init`, under an **Invisio** category in the inserter.

Field groups are **acf-json only**: build them in **Custom Fields → Field Groups**
with a location rule of *Block is equal to* your block. ACF auto-saves the group
to `acf-json/` — commit that JSON file. The example ships
`acf-json/group_invisio_hero.json`.

See `src/blocks/hero/README.md` for the copy-to-create-a-block steps. Requires ACF
PRO; without it the blocks are skipped and an admin notice is shown.

## Requirements

| | |
|---|---|
| WordPress | 6.5+ |
| PHP | 8.1+ |
| Node | 22+ (development only) |
| Plugins | Advanced Custom Fields PRO (for blocks) |

Invisio is based on [Underscores (`_s`)](https://underscores.me/), © Automattic,
Inc., GPL-2.0-or-later.

## Screenshot

`screenshot.png` is a flat placeholder — replace it with a real 1200×900 shot of
the theme before shipping a project built from this repo.
