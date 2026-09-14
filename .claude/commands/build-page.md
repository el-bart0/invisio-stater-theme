---
description: Build a custom page template from an uploaded design image, section by section, then wire it up to ACF fields
argument-hint: [slug] ["Template Name"]
---

Build a new custom WordPress page template from a design image, following this
project's conventions (see `CLAUDE.md` for the full architecture). Work in two
phases and stop for confirmation between them — do not start Phase 2 until the
user has explicitly approved the markup from Phase 1.

Arguments: `$ARGUMENTS` — a filename slug and, optionally, a human-readable
template name in quotes, e.g. `landing-page "Landing Page"`. If not given, ask
for them before starting.

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
3. Build the page **section by section**, in the order they appear in the
   design. For each section:
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
   `php -l page-templates/<slug>.php` — all must pass before you consider a
   section done.
5. When the full template is built and all checks pass, summarize what you
   built section by section and **stop. Ask the user to activate the
   template on a page in wp-admin and confirm it matches the design** before
   touching ACF. Do not proceed to Phase 2 in the same turn.

## Phase 2 — Wire up ACF (only after the user confirms Phase 1)

1. Go back through `page-templates/<slug>.php` section by section and
   replace the hardcoded placeholder text/images from Phase 1 with ACF field
   calls (`get_field()`), escaping every output per the project's existing
   PHP conventions (see `inc/template-tags.php` for the escaping style used
   elsewhere in this theme). Leave the `#ccc` placeholder `<div>`s as the
   fallback markup when an image field is empty.
2. Do **not** create the field group yourself and do not write a
   `fields.php` — this project is acf-json-only, built through the admin UI
   (see CLAUDE.md's "ACF blocks" section for why). Instead, after editing the
   template, give the user a clear list of every field to create, grouped by
   section, with for each one: field name/key, field type (text, image,
   WYSIWYG, repeater, etc.), and any notes needed to configure it correctly
   (e.g. "repeater, sub-fields: title (text), icon (image)"). Tell them the
   location rule to use: *Page Template is equal to `<Template Name>`*.
3. Remind the user that once they create the field group in
   **Custom Fields → Field Groups**, ACF will auto-save it to `acf-json/` —
   that file should get committed.
4. Run `npm run build`, the two lint commands, and `php -l` again after the
   edits, same as Phase 1.
