# Hero block

Reference implementation for an Invisio ACF block.

**To add a new block**, copy this folder to `src/blocks/<name>/` and:

1. `block.json` — change `name` (`acf/invisio-<name>`), `title`, `icon`, `keywords`.
2. `render.php` — the server-side markup. Read fields with `get_field()`; escape everything.
3. `style.scss` — block styles (`@use "../../scss/settings/variables" as *;` for tokens).
4. `index.js` — leave as-is (`import './style.scss';`); it only exists so the SCSS compiles.
5. Run `npm run build`. The loader in `inc/blocks.php` auto-registers anything in `build/blocks/`.
6. Create the field group in **Custom Fields → Field Groups**, location rule
   *Block is equal to* your block. It auto-saves to `acf-json/` — commit that file.
