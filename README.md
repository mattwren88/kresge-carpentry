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
