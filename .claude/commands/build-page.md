---
description: Build a custom page template (or front-page.php) from an uploaded design image, section by section, then wire it up to ACF fields
argument-hint: [slug | front-page] ["Template Name"]
---

Build a new WordPress page template from a design image, following this
project's conventions (see `CLAUDE.md` for the full architecture). Work in two
phases and stop for confirmation between them — do not start Phase 2 until the
user has explicitly approved the markup from Phase 1.

Arguments: `$ARGUMENTS` — a filename slug and, optionally, a human-readable
template name in quotes, e.g. `landing-page "Landing Page"`. If not given, ask
for them before starting.

**Special case — the site's front page:** if the slug is `front-page` (or the
user otherwise says this is for the site's front page), this is
`front-page.php`, not a selectable custom template. It works differently
throughout — see the "front-page.php" callouts in each phase below instead of
the default page-template behavior.

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
   `php -l` on the file you created (`page-templates/<slug>.php` or
   `front-page.php`) — all must pass before you consider a section done.
5. When the full template is built and all checks pass, summarize what you
   built section by section and **stop before touching ACF**. Ask the user
   to confirm it matches the design:
   - Normal template: ask them to select it on a page (**Page Attributes →
     Template**) in wp-admin and confirm.
   - `front-page.php`: ask them to confirm a static page is set as the
     homepage (Settings → Reading) and check the site's front page directly
     — there's no template to select.
   Do not proceed to Phase 2 in the same turn.

## Phase 2 — Wire up ACF (only after the user confirms Phase 1)

1. Go back through the template file section by section and replace the
   hardcoded placeholder text/images from Phase 1 with ACF field calls
   (`get_field()`), escaping every output per the project's existing PHP
   conventions (see `inc/template-tags.php` for the escaping style used
   elsewhere in this theme). Leave the `#ccc` placeholder `<div>`s as the
   fallback markup when an image field is empty.
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
3. Remind the user that once they create the field group in
   **Custom Fields → Field Groups**, ACF will auto-save it to `acf-json/` —
   that file should get committed.
4. Run `npm run build`, the two lint commands, and `php -l` again after the
   edits, same as Phase 1.
