# Kresge Carpentry marketing site

Custom single-page marketing site built with vanilla HTML, CSS, and JS. Highlights recent work, services, testimonials, process, and contact info for Kresge Carpentry LLC.

## File structure
- `index.html` – main page with hero, services, projects, process, testimonials, gallery, FAQ schema, and contact section.
- `assets/css/styles.css` – global styles, responsive layouts, animations, and performance tweaks.
- `assets/js/main.js` – mobile nav toggle, body scroll locking, and smooth scrolling.
- `assets/images/` – optimized WebP + JPEG assets (logo and project photos).
- `quote-scope.md` – itemized scope/proposal outline.
- `contract-draft.md` – draft agreement with pricing/milestones.

## Run locally
- Open `index.html` directly in a browser, or serve the folder (e.g., `python3 -m http.server 8000`) for clean relative URLs.

## Performance & SEO
- `<picture>` with WebP fallbacks, lazy loading, explicit dimensions, non-blocking stylesheet load.
- Meta tags for OpenGraph/Twitter, canonical URL, LocalBusiness + FAQ structured data.

## Customization checklist
- Update phone/email in `index.html` (`tel:` / `mailto:` links) and JSON-LD.
- Swap in higher-res project photos (drop replacements in `assets/images/`).
- Adjust service copy, testimonials, and service area text as needed.
- Hook the contact form to Formspree/Basin/Netlify Forms or another provider.
- Replace canonical/OG URLs once the final domain is live.

## WordPress version

A block theme (`wp-theme/kresge/`) plus a small companion plugin (`wp-plugin/kresge-core/`) that rebuild this page in WordPress so the owner can edit it.

- **Theme** – look and layout: `theme.json` tokens, header/footer, homepage section patterns (locked so text and photos are editable but the layout isn't), project and 404 templates.
- **Kresge Core plugin** – content that should survive a theme change: the Projects post type with "Project type" tags, and **Settings → Business details** (phone, email, service area), which feed the header, contact section, footer and LocalBusiness search data.
- **Contact form** – Fluent Forms (free). The contact section embeds form id 1, the demo contact form Fluent Forms creates on activation; edit its fields under Fluent Forms.

### Run it locally

Needs only Node (no PHP, MySQL or Docker). The Playground CLI's native module doesn't load on Node 26, so it runs on a project-local Node 22:

```bash
npm install --ignore-scripts && npm rebuild node
```

```bash
npm run wp
```

Open http://127.0.0.1:9400 (you're logged in as admin automatically). Each start builds a fresh site from `wp-dev/blueprint.json` and `wp-dev/seed.php`: the 4 projects, homepage and business details from this page. **Changes made in wp-admin are lost when the server stops.**

`wp-dev/check.sh header projects business homepage templates` runs smoke checks against the running site. `wp-dev/review-checks.js` covers the editing and empty-state edge cases; run it through Playwright (`browser_run_code` with that file) while the site is up.

### Deploy

Zip `wp-theme/kresge` and `wp-plugin/kresge-core` separately. On the host: Plugins → Add New → Upload (Kresge Core), Appearance → Themes → Add New → Upload (Kresge Carpentry), install Fluent Forms, then create a page from the Kresge section patterns and set it as the homepage (Settings → Reading). Seed content does not ship with the theme.

Sections inserted from the pattern inserter use WordPress's own section editing: text and photos are editable in place, and structural changes (like adding an FAQ question) go through the section's **Edit pattern** button. The local seed inserts the sections as plain blocks, where the FAQ list accepts new questions directly.
