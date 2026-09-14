---
description: Build a page/CPT single/archive template (or front-page.php) from an uploaded design image, section by section, then wire it up to ACF fields
argument-hint: [slug | front-page | single-<cpt> | archive-<cpt>] ["Template Name"]
---

Build a new WordPress template from a design image, following this project's
conventions (see `CLAUDE.md` for the full architecture). Work in two phases
and stop for confirmation between them — do not start Phase 2 until the user
has explicitly approved the markup from Phase 1.

Arguments: `$ARGUMENTS` — a filename slug and, optionally, a human-readable
template name in quotes, e.g. `landing-page "Landing Page"`. If not given, ask
for them before starting. The slug decides which kind of template this is —
match it against these four cases before doing anything else:

| Slug given | File(s) created | What it is |
|---|---|---|
| `front-page` | `front-page.php` | The site's static front page |
| `single-<cpt>` (e.g. `single-product`) | `single-<cpt>.php` (+ maybe a template-part) | Single view of one post of CPT `<cpt>` |
| `archive-<cpt>` (e.g. `archive-product`) | `archive-<cpt>.php` + `template-parts/content/content-<cpt>.php` | Archive/loop listing of CPT `<cpt>` |
| anything else | `page-templates/<slug>.php` | A selectable custom page template |

For `single-<cpt>` and `archive-<cpt>`: confirm the post type `<cpt>` is
already registered somewhere in the codebase (grep for
`register_post_type( '<cpt>'` — likely in `inc/setup.php` or a plugin). If
it isn't registered anywhere, stop and ask the user whether it's registered
by a plugin you can't see, or whether they want you to add
`register_post_type()` to `inc/setup.php` first — don't guess its args.

If no design image is attached to this message, stop and ask the user to
attach one before doing anything else.

## Phase 1 — Build the markup from the design

1. Look at the attached design image and break it into its distinct sections
   (hero, feature grid, testimonials, CTA, etc.). List the sections you've
   identified before writing any code, so the user can correct you early if
   you've misread the design.
2. Create `page-templates/<slug>.php` with a standard WP template header:
   ```php
   <?php
   /**
    * Template Name: <Template Name>
    *
    * @package <theme package name from style.css>
    */
   ```
   Follow the structure of the existing top-level templates (`page.php`,
   `single.php`) for `get_header()`/`get_footer()` wrapping and the
   `while ( have_posts() )` loop.

   **front-page.php:** create `front-page.php` at the theme root instead —
   not under `page-templates/`. It gets **no `Template Name:` header** (WP
   picks it up automatically for the site's front page; a header would just
   make it show up, confusingly, as a selectable template too). Use the same
   plain docblock style as `index.php`/`single.php`. WordPress falls back to
   `page.php` then `index.php` if `front-page.php` doesn't exist, and only
   uses it once a *static* front page is configured — mention to the user
   that they'll need **Settings → Reading → "Your homepage displays" → A
   static page** set, if it isn't already.

   **single-`<cpt>`.php:** create `single-<cpt>.php` at the theme root, same
   as `front-page.php` — **no `Template Name:` header**, WP picks it up
   automatically for any single post of that post type. Model it on
   `single.php`'s structure (`get_header()`, the loop, `get_footer()`) rather
   than `page.php`'s.

   **archive-`<cpt>`.php:** create `archive-<cpt>.php` at the theme root,
   again **no header**, picked up automatically for that CPT's archive (only
   works if the post type was registered with `has_archive` true — check
   this when you confirm the CPT registration above). Model the overall
   file on `archive.php`, not `page.php`/`single.php` — it loops over
   `have_posts()` printing one repeating "card" per post, not one block of
   content. Extract that repeating card into its own
   `template-parts/content/content-<cpt>.php`, loaded via
   `get_template_part( 'template-parts/content/content', '<cpt>' )` inside
   the loop, matching how `content.php`/`content-page.php` already work for
   the default post type. Keep `the_posts_pagination()` (see `archive.php`)
   at the end.
3. Build the page **section by section**, in the order they appear in the
   design.

   **archive-`<cpt>`.php only:** the design will typically show a static
   intro/header area plus one repeating "card" design (for a grid or list of
   posts). Treat the intro as a normal section built directly in
   `archive-<cpt>.php`; treat the card as a single section built once in
   `template-parts/content/content-<cpt>.php` and looped — don't hand-copy
   the card markup multiple times to match how many cards appear in the
   design mockup.

   For each section:
   - Use the project's existing markup/CSS conventions — check
     `src/scss/layout/_grid.scss` (`.row`/`.flex-row`/`.flex-col-*`) before
     reaching for custom layout CSS, and use `tools/_breakpoints.scss`'s
     `respond-to()` for any responsive rules, keyed off the existing
     `$breakpoints` map in `settings/_variables.scss` (extend it, don't
     hardcode pixel values or add a second breakpoint system).
   - Write a new SCSS partial per section (or reuse existing patterns from
     `base/`/`components/` where a section is generic enough), `@use`-d into
     `main.scss` the same way existing partials are.
   - Match the design's spacing/typography/proportions as closely as
     reasonable using the project's existing design tokens in
     `settings/_variables.scss` first; only add a new token if nothing
     existing fits, and don't invent a parallel design system (the project's
     CSS is deliberately minimal — see CLAUDE.md's Sass architecture note).
   - **Placeholder images:** don't source real images. Use empty
     `<div>`s (or `<img>`-shaped elements) with `background-color: #ccc`,
     sized/proportioned to match the design's image areas. No stock photos,
     no `<img src>` placeholders from external services.
   - All copy from the design goes in as static hardcoded text for now —
     nothing is dynamic yet. That comes in Phase 2.
4. After each section (or once the whole template is drafted, whichever
   reads more naturally for the amount of content), run `npm run build` and
   confirm no errors, then `npm run lint-js`, `npm run lint-style`, and
   `php -l` on every PHP file you created or touched this session (the
   template itself, plus `template-parts/content/content-<cpt>.php` for
   archives) — all must pass before you consider a section done.
5. When the full template is built and all checks pass, summarize what you
   built section by section and **stop before touching ACF**. Ask the user
   to confirm it matches the design:
   - Normal template: ask them to select it on a page (**Page Attributes →
     Template**) in wp-admin and confirm.
   - `front-page.php`: ask them to confirm a static page is set as the
     homepage (Settings → Reading) and check the site's front page directly
     — there's no template to select.
   - `single-<cpt>.php`: ask them to open (or publish) any post of that CPT
     and confirm the single view.
   - `archive-<cpt>.php`: ask them to confirm the CPT's archive is reachable
     (needs `has_archive` true and at least one published post of that type)
     and check it directly.
   Do not proceed to Phase 2 in the same turn.

## Phase 2 — Wire up ACF (only after the user confirms Phase 1)

1. Go back through the template file(s) section by section and replace the
   hardcoded placeholder text/images from Phase 1 with ACF field calls
   (`get_field()`), escaping every output per the project's existing PHP
   conventions (see `inc/template-tags.php` for the escaping style used
   elsewhere in this theme). Leave the `#ccc` placeholder `<div>`s as the
   fallback markup when an image field is empty. For `archive-<cpt>.php`,
   this means editing `template-parts/content/content-<cpt>.php` (the card),
   not the archive file itself, for anything that varies per post.
2. Do **not** create the field group yourself and do not write a
   `fields.php` — this project is acf-json-only, built through the admin UI
   (see CLAUDE.md's "ACF blocks" section for why). Instead, after editing the
   template, give the user a clear list of every field to create, grouped by
   section, with for each one: field name/key, field type (text, image,
   WYSIWYG, repeater, etc.), and any notes needed to configure it correctly
   (e.g. "repeater, sub-fields: title (text), icon (image)"). Tell them the
   location rule to use:
   - Normal template: *Page Template is equal to `<Template Name>`*.
   - `front-page.php`: there's no template to key off, so use *Page is equal
     to* the specific page the user has set as their static homepage — ask
     them which page that is if it's not obvious from the conversation.
   - `single-<cpt>.php`: *Post Type is equal to `<cpt>`*.
   - `archive-<cpt>.php`: per-post/card fields use the same *Post Type is
     equal to `<cpt>`* rule as `single-<cpt>.php` — if that field group
     already exists from an earlier `single-<cpt>` run, tell the user to
     reuse it rather than duplicating fields. Any field that belongs to the
     archive page itself (an intro heading/description, not tied to one
     post) needs its own field group with *Post Type Archive is equal to
     `<cpt>`* (an ACF PRO location rule).
3. Remind the user that once they create the field group in
   **Custom Fields → Field Groups**, ACF will auto-save it to `acf-json/` —
   that file should get committed.
4. Run `npm run build`, the two lint commands, and `php -l` again on every
   file touched this phase, same as Phase 1.
