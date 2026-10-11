# Kresge Carpentry — WordPress block theme

**Date:** 2026-10-11
**Status:** Approved design, pending implementation plan

## Goal

Turn the static one-page Kresge Carpentry site into a WordPress block theme that the business owner (non-technical) can edit, and let Matt test it locally with no hosting. Success = the WordPress homepage looks like the static site at 390px and 1440px, and the owner can (1) edit homepage text/photos, (2) add a project, (3) change the phone number in one place.

## Decisions

| Decision | Choice | Why |
|---|---|---|
| Theme type | Block theme (FSE) | Modern WP default; owner edits visually |
| Homepage editing | Locked patterns (`templateLock: "contentOnly"`) in a normal "Home" page | Owner can change text/images, can't break layout |
| Portfolio | `project` custom post type + `project_type` taxonomy | Adding a project = writing a post; each gets a page |
| Featured project | Newest published project | Zero custom fields; owner controls via publish date |
| Shared business info | "Business details" settings page + Block Bindings source | Phone/email/area edited once, shown everywhere |
| Styling | Plain CSS, no build step | Matches repo; one stylesheet doesn't justify SCSS/esbuild |
| Fonts | Google Fonts (DM Serif Display, Inter), as today | No change from static site |
| Contact form | Fluent Forms (free) | Stores entries + emails, spam protection, free |
| Local dev | WordPress Playground CLI via `npm run wp` | Node only; no PHP/Docker/LocalWP install |
| Static site | Kept unchanged at repo root as the reference design | User choice |

## Repo layout

```
kresge-carpentry/
├── index.html, assets/            # static site — untouched
├── package.json                   # adds "wp" script (@wp-playground/cli)
├── .claude/launch.json            # adds "wordpress" config
└── wp-theme/kresge/
    ├── style.css                  # theme header only
    ├── theme.json
    ├── functions.php              # bootstraps inc/*
    ├── inc/
    │   ├── setup.php              # theme supports, enqueue CSS/JS + Google Fonts
    │   ├── post-types.php         # project CPT + project_type taxonomy
    │   ├── business-details.php   # settings page, block bindings source, LocalBusiness JSON-LD
    │   └── patterns.php           # pattern category
    ├── templates/                 # front-page, page, single-project, archive-project, index, 404
    ├── parts/                     # header, footer
    ├── patterns/                  # hero, trust-strip, services, work, process, testimonial, faq, contact
    ├── assets/css/theme.css
    ├── assets/js/main.js          # mobile nav (ported from static site)
    ├── assets/images/             # copies of the 7 project photos + logo, for seeding
    └── blueprint.json             # Playground setup recipe
```

## theme.json

- **Palette** (from current `:root` tokens): forest `#1C2B1E`, forest-deep `#0F1A12`, sage `#4A7C59`, sage-dark `#3A6347`, paper `#F5F7F2`, mist `#F0F3EC`, white `#FFFFFF`, ink `#1C2B1E`, muted `#5A6B5C`, line `#C8D5C4`. Default palette and custom colors disabled.
- **Typography:** families `serif` (DM Serif Display, Georgia fallback) and `sans` (Inter, system fallback); headings serif, body sans. Font sizes: small, base, large, x-large, hero (clamp values from current CSS). Custom font sizes disabled.
- **Spacing:** presets mapped from `--space-*` scale.
- **Layout:** contentSize 760px, wideSize 1200px.
- **Elements:** button (pill, sage bg, white text), link (sage-dark), headings.

## Content model

### Home page
A page titled "Home", set as the static front page. Its content is the eight patterns in order. Each pattern's outer group has `templateLock: "contentOnly"`. Groups that hold growable lists (FAQ items, service bullets) set `templateLock: false` on that inner group so the owner can add/remove items.

`front-page.html` = header part + `post-content` + footer part.

### Projects
- CPT `project`: public, `show_in_rest`, supports title, editor, excerpt, thumbnail; archive at `/projects/`; menu icon `dashicons-hammer`.
- Taxonomy `project_type`: non-hierarchical, `show_in_rest`. Seed terms: Decks, Structural, Repairs, Outdoor living.
- **Work pattern:** Query Loop #1 (`postType: project`, `perPage: 1`, order date desc) → featured layout: image, type term, title, excerpt, "View project →". Query Loop #2 (`perPage: 3`, `offset: 1`) → cards. Both have no-results blocks left empty; if there are no projects the section heading is still shown but loops render nothing. (Hiding the whole section when empty requires PHP; out of scope.)
- `single-project.html`: featured image, title, type, content, "Plan a similar project" button → `/#contact`.

### Business details
- Settings page under **Settings → Business details** with three fields: phone, email, service area (`register_setting`, sanitized).
- Block Bindings source `kresge/business` with keys `phone`, `phone_href` (`tel:` digits), `email`, `email_href` (`mailto:`), `service_area`. Bound in header phone button, contact section, footer. If a value is empty, the binding returns a placeholder like "Add phone in Settings → Business details".
- `wp_head` outputs LocalBusiness JSON-LD (name, phone, email, areaServed, url = home_url, image = logo). FAQPage schema is dropped.

## Styling

`theme.css` ports the static site's component styles to block markup. Patterns set stable class names (`kc-hero`, `kc-trust`, `kc-card`, `kc-featured`, `kc-timeline`, `kc-quote`, `kc-faq`, `kc-contact`) and CSS targets those, not WordPress-generated classes. Includes the responsive rules from the critique fixes (hero/featured stacking ≤900px, icon-only phone ≤600px, 44px targets, 2-column trust strip). FAQ uses core `details` blocks styled like the current FAQ.

Header part: logo (site logo block), site title, navigation block (links `/#services`, `/#work`, `/#process`, `/#faq`, `/#contact`), phone link, "Request a quote" button. Mobile nav: `main.js` toggle ported to target the navigation block's markup, or use the navigation block's built-in overlay if it can be styled to match. Decide during implementation; must keep 44px toggle and `aria-expanded`.

## Contact form

Fluent Forms installed by the blueprint. Blueprint attempts to create the form (name, email, project type select, message) via a PHP step and insert its block into the contact pattern. If programmatic creation is unreliable, the contact pattern shows the phone/email and a placeholder paragraph "Add your Fluent Forms form here", and the README documents the one-time setup. Contact section always shows phone and email regardless.

## Local dev

- `package.json` script: `"wp": "wp-playground-cli server --mount=./wp-theme/kresge:/wordpress/wp-content/themes/kresge --blueprint=./wp-theme/kresge/blueprint.json --login"` (exact CLI flags verified during implementation).
- `blueprint.json` steps: set site title, activate theme, install + activate Fluent Forms, install Theme Check (dev only), create `project_type` terms, create 4 projects (current Work section content) with featured images imported from `assets/images/`, create "Home" page from patterns, set it as front page, set business details to the current placeholder values.
- `.claude/launch.json` gets a `wordpress` configuration so it opens in the browser pane.
- Playground state is temporary; each start re-seeds from the blueprint.

## Deployment (later, out of scope)

Zip `wp-theme/kresge`, upload via Appearance → Themes on any WordPress host (e.g. kresge.mattwren.dev via Porkbun DNS). Seed content is not shipped with the theme; the owner creates their real projects.

## Testing

1. `npm run wp` boots with no errors; homepage renders.
2. Playwright screenshots at 390×844 and 1440×900 of WP homepage vs static `index.html`; visually equivalent.
3. Re-run critique checks: `scrollWidth === innerWidth` at 390; eyebrow contrast ≥ 4.5:1; nav toggle and phone ≥ 44×44.
4. Owner flows in wp-admin: edit hero headline → shows on homepage; add a new project with image → becomes featured, previous featured moves to cards; change phone in Business details → header, contact, footer, JSON-LD all update.
5. Theme Check plugin: no required-level errors.

## Out of scope

Blog, extra pages, Yoast/meta tags, hosting/deploy, real content (phone, testimonial, project details), image optimization pipeline.
