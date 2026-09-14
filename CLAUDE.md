# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Invisio is an internal WordPress starter theme, meant to replace the team's
Underscores (`_s`) base. It is infrastructure, not a product: the goal is that
every new customer site starts from a copy of this repo instead of `_s`, so
every project shares the same structure, build commands, and ACF-block
workflow. Distribution is copy/rename per project (see "Renaming for a new
project" below) — there is no update channel, and per-project divergence after
copying is expected and fine.

Classic PHP templates throughout — no FSE, no block templates, no
`theme.json`. Content on pages is built from ACF blocks, not core blocks (see
"Block editor lockdown" below).

## Commands

```
npm install
npm start          # wp-scripts watch build + BrowserSync live reload, one process
npm run build       # one-shot production build -> build/ (run after any src/ change)
npm run lint-js
npm run lint-style
npm run format       # wp-scripts format (prettier)
npm run make-pot     # regenerate languages/<text-domain>.pot (no WP-CLI needed)
npm run rename -- <slug> "<Theme Name>"   # re-slug the theme for a new project
```

There is no test suite. `php -l` on changed PHP files and the two lint
commands above are the correctness bar; `npm run build` succeeding (webpack
exits 0) is the build correctness bar.

`npm start` proxies the site at `WP_DEV_URL` (set in `bs-config.js`, default
`https://starter-tema.local`). Override per shell invocation:
`WP_DEV_URL=https://acme.local npm start`. BrowserSync auto-detects the Local
by Flywheel router cert for that hostname
(`%APPDATA%\Local\run\router\nginx\certs\<hostname>.{crt,key}` on Windows,
equivalent paths under `~/Library/Application Support/Local/...` on macOS and
`~/.config/Local/...` on Linux) — without a matching cert, BrowserSync falls
back to its own SAN-less bundled cert, which Chrome rejects as
`ERR_EMPTY_RESPONSE`. If that error resurfaces, check `WP_DEV_URL` matches the
actual Local site hostname before re-diagnosing.

## Architecture

### Build output is committed

`build/` is checked into git (see `.gitignore`'s note) so a freshly copied
theme runs immediately with no `npm install`/build step. **Any change under
`src/` requires `npm run build` and committing the updated `build/` output
before the change takes effect on a site**, since `functions.php` only ever
enqueues from `build/`, never from `src/`. `inc/enqueue.php` /
`inc/helpers.php` degrade gracefully (skip enqueuing) if `build/` doesn't
exist yet, so the theme won't fatal before a first build.

### CSS minification is explicit, not a wp-scripts default

`@wordpress/scripts`' default webpack config only lists `TerserPlugin` (JS) in
`optimization.minimizer` — it does not minify extracted CSS. `webpack.config.js`
appends `css-minimizer-webpack-plugin` to that array so `build/*.css` ships
minified from `npm run build`. It only runs in production mode (tied to
`optimization.minimize`), so `npm start`'s dev/watch build is unaffected. If
you ever replace or reset `optimization.minimizer` in `webpack.config.js`,
make sure to keep both entries — providing a custom array replaces webpack's
defaults entirely rather than merging with them.

### PHP loading (no Composer, no autoloader)

`functions.php` requires a fixed, ordered list of files under `inc/` — see the
`$invisio_includes` array there. Order matters (e.g. `helpers.php` before
anything that calls `invisio_asset_uri()`). New cross-cutting PHP concerns get
their own `inc/*.php` file added to that list, not folded into an existing one
or autoloaded.

### ACF blocks: source vs. compiled, and why both exist

Block source lives in `src/blocks/<name>/` (`block.json` with an `"acf"` key,
`render.php`, `style.scss`, and a near-empty `index.js` that only exists
because wp-scripts' entry-point scanner tracks JS script fields, not
`style`/`editorStyle` SCSS directly — so every block needs that trivial
`import './style.scss'` file to get its SCSS compiled). `webpack.config.js`
auto-discovers these as entries and compiles them to `build/blocks/<name>/`.

`inc/blocks.php` scans `build/blocks/*/block.json` on `init` and calls
`register_block_type()` on each — **it registers whatever it finds in
`build/`, not `src/`**, so a block only appears after a build. If
`acf_register_block_type` doesn't exist (ACF PRO inactive/not installed),
registration is skipped entirely and an admin notice is shown instead of
fataling.

`webpack.config.js` also sets `process.env.WP_COPY_PHP_FILES_TO_DIST = 'true'`
— by default wp-scripts only copies PHP referenced by the core block.json
`render` key, not ACF's `acf.renderTemplate`, so without this override
`render.php` files would never reach `build/`.

Field groups are **acf-json only** — there is no `fields.php` convention here.
Build a field group in the admin with a location rule of _Block is equal to_
your block; ACF auto-saves it to `acf-json/`, which is committed. The load and
save points are both wired to `acf-json/` in `inc/blocks.php`
(`acf/settings/load_json` / `acf/settings/save_json`).

`src/blocks/hero/README.md` has the step-by-step for adding a new block
(copy the `hero` folder, change the `block.json` name/title, write
`render.php`, run `npm run build`, create the field group).

### Block editor lockdown

Invisio intentionally does not use core blocks for content. `inc/editor.php`:

- Restricts the inserter to `acf/invisio-*` blocks only, via
  `allowed_block_types_all` — but only when at least one Invisio block is
  actually registered; otherwise it falls back to allowing everything, so the
  editor is never bricked (e.g. before a block is built, or ACF PRO missing).
- Removes core block patterns and the remote pattern directory.
- Dequeues `wp-block-library`, `wp-block-library-theme`, `global-styles`, and
  `classic-theme-styles` on the front end only (they stay enqueued in the
  editor, since the editor still needs to render whatever's registered).

Existing content using non-Invisio blocks still renders on the front end —
only the inserter is restricted, not block execution.

### Sass architecture

`src/scss/main.scss` is the front-end entry, `editor.scss` the editor-only
entry (both listed in `webpack.config.js`'s explicit `entry()`). Partials are
organized by purpose, each `@use`-d explicitly (this project uses the modern
Sass module system, not `@import` — every partial that needs a variable or
mixin from elsewhere declares its own `@use "../path" as *;`):

- `settings/_variables.scss` — the only place design tokens live (colors,
  type, spacing, `$breakpoints` map). Meant to be edited per project when
  rebranding; nothing here is structural.
- `tools/_breakpoints.scss` — `respond-to($name)`, the single canonical
  media-query mixin, keyed off the `$breakpoints` map. Don't add a second
  breakpoint mixin/map; extend `$breakpoints` instead.
- `tools/_mixins.scss` — general-purpose helpers (`center-block`,
  `column-width`, `side-padding`, `list-style`, `margin-bottom-block`).
- `base/` — reset, typography, form/element defaults.
- `layout/_layout.scss` — content measure and WordPress alignment classes
  (`.alignwide`/`.alignfull`/etc.).
- `layout/_grid.scss` — an opt-in 12-column flex grid (`.row`, `.flex-row`,
  `.flex-col-{xs,sm,md,lg,xl}-{1..12}`). Not used by any existing template;
  available for blocks/pages that want it. `.row` is a plain max-width +
  side-padding wrapper (no flex); `.flex-row` is the same wrapper but as a
  flex container for laying out `.flex-col-*` children. Modifiers:
  `.flex-row--align-h-center`, `.flex-row--align-h-right`,
  `.flex-row--align-v-center`, `.flex-row--align-v-bottom`,
  `.flex-row--reverse`, `.flip`.

  ```html
  <!-- Single column, centered, narrower than full row width -->
  <section>
    <div class="site-main flex-row flex-row--align-h-center">
      <div class="flex-col-lg-6">Narrower content, center aligned</div>
    </div>
  </section>

  <!-- Two columns side by side (from the lg breakpoint up) -->
  <section>
    <div class="site-main flex-row">
      <div class="flex-col-lg-6">Side by side content</div>
      <div class="flex-col-lg-6">Side by side content</div>
    </div>
  </section>

  <!-- Full page-width content, no columns -->
  <section>
    <div class="site-main row">Page-width content</div>
  </section>
  ```

- `layout/_header.scss`, `layout/_navigation.scss` — site chrome; the nav
  toggle behavior lives in `src/js/navigation.js` (outside-click and
  resize-to-desktop close, no dependencies).
- `components/_media.scss`, `_print.scss` — captions/galleries, print
  stylesheet.

The CSS surface is deliberately minimal ("fix what's broken, don't build a
design system") — this is a locked decision, not an oversight. Don't add a
utility-class framework or expand the base styles speculatively; per-project
visual design is expected to be layered on top after copying the theme.

`$breakpoints`'s `sm`/`md`/`lg`/`xl` keys are load-bearing for the header and
nav components specifically — changing their values changes those components'
responsive behavior. The `xs`/`xxl`/`xxxl`/`ultra` keys exist only for the
grid and `side-padding` mixin.

### Renaming for a new project

`bin/rename.mjs` (`npm run rename -- <slug> "<Theme Name>"`) does the
find/replace across the whole tree: text domain, `invisio_`/`INVISIO_`
function/constant prefixes, `acf/invisio-*` block namespaces,
`group_invisio_`/`field_invisio_` ACF prefixes, `@package` tags, and renames
matching files. It skips `node_modules/`, `.git/`, `build/`, and `bin/` itself
(so it's safe to re-run, and the tool's own source never gets mangled). After
running it: `npm install && npm run build && npm run make-pot`, then review
the diff and fill in `Author`/`Theme URI` in `style.css` by hand (the script
doesn't guess those).

### Versioning

Bump three places together, then tag `vX.Y.Z`: `style.css`'s `Version:`
header, `INVISIO_VERSION` in `functions.php`, and `package.json`'s `version`.
Record the change in `CHANGELOG.md` (Keep a Changelog / SemVer).

## Requirements

WordPress 6.5+, PHP 8.1+, Node 22+ (dev only, `.nvmrc` pins 24), Advanced
Custom Fields PRO (for blocks — the theme itself won't fatal without it, but
its blocks won't register).
