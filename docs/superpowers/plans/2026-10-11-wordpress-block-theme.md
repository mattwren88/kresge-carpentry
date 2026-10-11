# Kresge WordPress Block Theme Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** A WordPress block theme in `wp-theme/kresge/` that reproduces the static Kresge Carpentry page, is owner-editable, and boots locally with `npm run wp`.

**Architecture:** Block theme (theme.json + HTML templates + PHP patterns). Homepage = a "Home" page built from locked patterns. Portfolio = `project` CPT rendered by two Query Loops. Business contact details live in one option set exposed through a Block Bindings source. Local dev runs WordPress Playground CLI under a project-local Node 22, seeded by a blueprint + PHP seed script kept outside the theme.

**Tech Stack:** WordPress 7.1 (Playground latest), PHP 8.3 (WASM), `@wp-playground/cli@3.1.55`, `node@22` (npm package), Fluent Forms (wp.org), plain CSS.

**Spec:** `docs/superpowers/specs/2026-10-11-wordpress-block-theme-design.md`

## Global Constraints

- Static site files (`index.html`, `assets/`) are not modified.
- Theme slug/text domain: `kresge`. Prefix PHP functions `kresge_`. CSS component classes prefixed `kc-`.
- Palette hex values, fonts, breakpoints (900px, 600px) exactly as the spec / current `assets/css/styles.css`.
- Tap targets ≥ 44×44px; no horizontal scroll at 390px; eyebrow text uses sage-dark (#3A6347).
- No paid plugins. Only Fluent Forms (runtime) and Theme Check (dev).
- Pin `@wp-playground/cli` to `3.1.55` (≥2 weeks old). Node 26 can't load its native `fs-ext` module, so scripts run it with `node_modules/node/bin/node` (Node 22).

## Deviations from spec (decided during planning)

- **Dev files live in `wp-dev/`, not the theme** (`blueprint.json`, `seed.php`) so the shipped theme zip has no seed code.
- **Mobile nav uses the core Navigation block's built-in overlay** instead of porting `main.js`. Less code; still keyboard/ARIA-correct. CSS gives the toggle a 44px target.
- **Form:** Fluent Forms auto-creates "Contact Form Demo" (id 1) on activation (verified in spike). The contact pattern embeds `[fluentform id="1"]`; the owner edits fields in Fluent Forms. If the plugin is inactive, the theme registers an empty `fluentform` shortcode so nothing raw is printed.

## Review Focus

1. **Owner saves the Home page** → pattern blocks become real blocks; layout must stay locked (contentOnly) and still render identically. Test in Task 7 by editing + saving hero text.
2. **Phone entered with formatting** (e.g. "(570) 555-0100") → `tel:` link must strip to digits/+ only. Test in Task 4.
3. **Fewer than 4 projects** (e.g. 1) → featured renders, card grid renders empty without broken layout. Test in Task 7 by trashing 3 projects.
4. **Project without featured image** → card shows the mist background, not a collapsed card. Test in Task 7.
5. **Fluent Forms deactivated** → contact section shows details, no `[fluentform` text. Test in Task 7.

---

## File structure

```
package.json                         # new — dev deps + "wp" script
.gitignore                           # new/modify — node_modules/
.claude/launch.json                  # modify — add "wordpress" config
wp-dev/blueprint.json                # Playground recipe
wp-dev/seed.php                      # content seeding (terms, media, projects, Home page, options)
wp-theme/kresge/style.css            # theme header
wp-theme/kresge/theme.json           # tokens
wp-theme/kresge/functions.php        # requires inc/*
wp-theme/kresge/inc/setup.php        # supports, enqueue, fonts, fluentform fallback
wp-theme/kresge/inc/post-types.php   # project CPT + project_type taxonomy
wp-theme/kresge/inc/business-details.php  # settings page, bindings source, JSON-LD
wp-theme/kresge/inc/patterns.php     # pattern category
wp-theme/kresge/parts/header.html, footer.html
wp-theme/kresge/templates/{index,front-page,page,single-project,archive-project,404}.html
wp-theme/kresge/patterns/{hero,trust-strip,services,work,process,testimonial,faq,contact}.php
wp-theme/kresge/assets/css/theme.css
wp-theme/kresge/assets/images/{hero.jpg,logo.jpg}   # copies of static assets
```

---

### Task 1: Tooling + minimal theme boots

**Files:** Create `package.json`, `.gitignore`, `wp-dev/blueprint.json`, `wp-dev/seed.php` (stub), `wp-theme/kresge/style.css`, `theme.json` (stub), `functions.php`, `templates/index.html`.

- [ ] **Step 1:** `package.json`
```json
{
  "name": "kresge-carpentry",
  "private": true,
  "scripts": {
    "wp": "node_modules/node/bin/node node_modules/@wp-playground/cli/wp-playground.js server --port=9400 --mount=./wp-theme/kresge:/wordpress/wp-content/themes/kresge --mount=./wp-dev:/wordpress/wp-content/kresge-dev --mount=./assets/images:/wordpress/wp-content/kresge-dev/images --blueprint=./wp-dev/blueprint.json --login"
  },
  "devDependencies": {
    "@wp-playground/cli": "3.1.55",
    "node": "22"
  }
}
```
- [ ] **Step 2:** `.gitignore` with `node_modules/`. Install: `npm install --ignore-scripts && npm rebuild node` (node package needs its install script; fs-ext uses its prebuilt for Node 22).
- [ ] **Step 3:** `style.css` header (Theme Name: Kresge Carpentry, Requires at least: 6.7, Requires PHP: 8.0, Version: 0.1.0, License: GPLv2 or later, Text Domain: kresge). `functions.php` requires `inc/*.php` (empty files OK for now). `templates/index.html` = `<!-- wp:post-content /-->`. `theme.json` = `{"$schema":"https://schemas.wp.org/trunk/theme.json","version":3}`.
- [ ] **Step 4:** `wp-dev/blueprint.json`
```json
{
  "$schema": "https://playground.wordpress.net/blueprint-schema.json",
  "landingPage": "/",
  "preferredVersions": { "php": "8.3", "wp": "latest" },
  "steps": [
    { "step": "login" },
    { "step": "installPlugin", "pluginData": { "resource": "wordpress.org/plugins", "slug": "fluentform" } },
    { "step": "installPlugin", "pluginData": { "resource": "wordpress.org/plugins", "slug": "theme-check" } },
    { "step": "activateTheme", "themeFolderName": "kresge" },
    { "step": "runPHP", "code": "<?php require '/wordpress/wp-load.php'; require '/wordpress/wp-content/kresge-dev/seed.php';" }
  ]
}
```
`seed.php` stub: `<?php update_option( 'blogname', 'Kresge Carpentry' );`
- [ ] **Step 5: Verify** — `npm run wp` (background). Expected log line `Ready! WordPress is running on http://127.0.0.1:9400`. `curl -s http://127.0.0.1:9400/ | grep -o '<title>[^<]*'` → `<title>Kresge Carpentry`.
- [ ] **Step 6: Commit** `feat(wp): scaffold block theme and Playground dev env`.

### Task 2: Design tokens, header, footer, base CSS

**Files:** `theme.json`, `inc/setup.php`, `parts/header.html`, `parts/footer.html`, `assets/css/theme.css`, `assets/images/{hero,logo}.jpg`.

- [ ] **Step 1:** `theme.json` v3: settings.color.palette (forest #1C2B1E, forest-deep #0F1A12, sage #4A7C59, sage-dark #3A6347, paper #F5F7F2, mist #F0F3EC, white #FFFFFF, muted #5A6B5C, line #C8D5C4), `defaultPalette:false`, `custom:false`, `customGradient:false`; typography.fontFamilies serif (`"DM Serif Display", Georgia, serif`) and sans (`Inter, Avenir, system-ui, sans-serif`); fontSizes small 0.875rem, base 1rem, large 1.25rem, x-large `clamp(1.8rem,3.2vw,2.5rem)`, hero `clamp(2.2rem,4.5vw,3.4rem)`, `customFontSize:false`; spacing.spacingSizes 20→0.5rem, 30→1rem, 40→1.5rem, 50→2rem, 60→3rem, 70→3.5rem; layout contentSize 760px, wideSize 1200px. styles: body bg paper, text forest, font sans, lineHeight 1.6; elements h1–h4 serif/400; link sage-dark; button pill radius 999px, sage bg, white text, padding 0.8rem 1.2rem, :hover sage-dark.
- [ ] **Step 2:** `inc/setup.php`
```php
<?php
add_action( 'after_setup_theme', function () {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/theme.css' );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'kresge-fonts', 'https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap', array(), null );
	wp_enqueue_style( 'kresge-theme', get_theme_file_uri( 'assets/css/theme.css' ), array(), wp_get_theme()->get( 'Version' ) );
} );

// Without Fluent Forms the contact pattern's shortcode would print raw text.
add_action( 'init', function () {
	if ( ! shortcode_exists( 'fluentform' ) ) {
		add_shortcode( 'fluentform', '__return_empty_string' );
	}
}, 20 );
```
- [ ] **Step 3:** `parts/header.html`: dark group `kc-header` (bg forest) → row: site-logo (44px) + site-title; navigation block (`overlayMenu:"mobile"`, links `/#services` Services, `/#work` Work, `/#process` Process, `/#faq` FAQ, `/#contact` Contact); buttons: `kc-phone` (bound, Task 4 — use plain button for now) + "Request a quote" → `/#contact`. `parts/footer.html`: forest-deep group `kc-footer` with logo/title/tagline and outline button "Schedule a visit" + phone/email (bound later).
- [ ] **Step 4:** `theme.css` sections: header (port `.topbar`, `.nav a::after` underline, sticky + blur ≤900px), mobile overlay styles (white panel, ink links), `.wp-block-navigation__responsive-container-open` min 44×44, phone icon-only ≤600px (hide text, mask-image phone SVG, 44×44), footer (port `.footer*`). Port values verbatim from `assets/css/styles.css`.
- [ ] **Step 5: Verify** — reload; Playwright screenshot at 1440 and 390: header dark with nav; 390 shows hamburger ≥44px; `scrollWidth === 390`.
- [ ] **Step 6: Commit** `feat(wp): tokens, header and footer`.

### Task 3: Projects post type + seeded projects

**Files:** `inc/post-types.php`, `wp-dev/seed.php`.

- [ ] **Step 1:** `inc/post-types.php`
```php
<?php
add_action( 'init', function () {
	register_post_type( 'project', array(
		'labels'       => array( 'name' => __( 'Projects', 'kresge' ), 'singular_name' => __( 'Project', 'kresge' ), 'add_new_item' => __( 'Add New Project', 'kresge' ) ),
		'public'       => true,
		'has_archive'  => 'projects',
		'rewrite'      => array( 'slug' => 'projects' ),
		'menu_icon'    => 'dashicons-hammer',
		'show_in_rest' => true,
		'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
	) );
	register_taxonomy( 'project_type', 'project', array(
		'labels'            => array( 'name' => __( 'Project types', 'kresge' ), 'singular_name' => __( 'Project type', 'kresge' ) ),
		'hierarchical'      => false,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'project-type' ),
	) );
} );
```
Add `add_theme_support( 'post-thumbnails' )` in setup.php.
- [ ] **Step 2:** `seed.php` — set permalinks `/%postname%/` + `flush_rewrite_rules()`; helper `kresge_seed_image( $file, $alt )` copies `/wordpress/wp-content/kresge-dev/images/$file` into uploads via `wp_upload_bits`, `wp_insert_attachment`, `wp_generate_attachment_metadata`, sets `_wp_attachment_image_alt`; create terms Decks, Structural, Repairs, Outdoor living; insert 4 projects with titles/excerpts/alt text from current `index.html` Work section, `post_date` staggered so "Elevated deck & stair rebuild" is newest; set thumbnail + term. Delete sample post/page. Wrap in `if ( get_option( 'kresge_seeded' ) ) return;` and set it at the end.
- [ ] **Step 3: Verify** — `curl -s 'http://127.0.0.1:9400/?rest_route=/wp/v2/project&_fields=title,featured_media'` → 4 items, all `featured_media` > 0, first title "Elevated deck &#038; stair rebuild".
- [ ] **Step 4: Commit** `feat(wp): projects post type and seed content`.

### Task 4: Business details + bindings + JSON-LD

**Files:** `inc/business-details.php`, `wp-dev/seed.php`, parts.

- [ ] **Step 1:** `inc/business-details.php`
```php
<?php
const KRESGE_BUSINESS_FIELDS = array(
	'phone'        => 'Phone',
	'email'        => 'Email',
	'service_area' => 'Service area',
);

function kresge_business( $key ) {
	$value = get_option( 'kresge_business', array() )[ $key ] ?? '';
	return is_string( $value ) ? $value : '';
}

function kresge_tel_href( $phone ) {
	$digits = preg_replace( '/[^0-9+]/', '', $phone );
	return $digits ? 'tel:' . $digits : '';
}

add_action( 'admin_init', function () {
	register_setting( 'kresge_business', 'kresge_business', array(
		'type'              => 'array',
		'sanitize_callback' => function ( $input ) {
			return array(
				'phone'        => sanitize_text_field( $input['phone'] ?? '' ),
				'email'        => sanitize_email( $input['email'] ?? '' ),
				'service_area' => sanitize_text_field( $input['service_area'] ?? '' ),
			);
		},
	) );
} );

add_action( 'admin_menu', function () {
	add_options_page( __( 'Business details', 'kresge' ), __( 'Business details', 'kresge' ), 'manage_options', 'kresge-business', 'kresge_render_business_page' );
} );

function kresge_render_business_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Business details', 'kresge' ); ?></h1>
		<p><?php esc_html_e( 'Shown in the header, contact section, footer and search results.', 'kresge' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'kresge_business' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( KRESGE_BUSINESS_FIELDS as $key => $label ) : ?>
					<tr>
						<th scope="row"><label for="kresge-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
						<td><input class="regular-text" type="<?php echo 'email' === $key ? 'email' : 'text'; ?>" id="kresge-<?php echo esc_attr( $key ); ?>" name="kresge_business[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( kresge_business( $key ) ); ?>"></td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

add_action( 'init', function () {
	register_block_bindings_source( 'kresge/business', array(
		'label'              => __( 'Business details', 'kresge' ),
		'get_value_callback' => function ( array $args ) {
			$key = $args['key'] ?? '';
			switch ( $key ) {
				case 'phone_href':
					return kresge_tel_href( kresge_business( 'phone' ) );
				case 'email_href':
					$email = kresge_business( 'email' );
					return $email ? 'mailto:' . $email : '';
				default:
					$value = kresge_business( $key );
					return '' !== $value ? esc_html( $value ) : esc_html__( 'Add in Settings → Business details', 'kresge' );
			}
		},
	) );
} );

add_action( 'wp_head', function () {
	$data = array_filter( array(
		'@context'   => 'https://schema.org',
		'@type'      => 'LocalBusiness',
		'name'       => get_bloginfo( 'name' ),
		'url'        => home_url( '/' ),
		'image'      => get_theme_file_uri( 'assets/images/logo.jpg' ),
		'telephone'  => kresge_business( 'phone' ),
		'email'      => kresge_business( 'email' ),
		'areaServed' => kresge_business( 'service_area' ),
	) );
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES ) . "</script>\n";
} );
```
- [ ] **Step 2:** Bind in parts: phone button `{"metadata":{"bindings":{"text":{"source":"kresge/business","args":{"key":"phone"}},"url":{"source":"kresge/business","args":{"key":"phone_href"}}}}}`; footer phone/email the same pattern (`email`/`email_href`).
- [ ] **Step 3:** seed.php: `update_option( 'kresge_business', array( 'phone' => '(555) 123-4567', 'email' => 'hello@kresgecarpentry.com', 'service_area' => 'Local towns within ~30 mi' ) );`
- [ ] **Step 4: Verify** — homepage HTML contains `href="tel:5551234567"` and `"telephone":"(555) 123-4567"`. Review-focus #2: set phone to `(570) 555-0100` via wp-admin → header href `tel:5705550100`.
- [ ] **Step 5: Commit** `feat(wp): business details settings, bindings and JSON-LD`.

### Task 5: Homepage patterns + Home page

**Files:** `inc/patterns.php`, `patterns/*.php`, `templates/front-page.html`, `assets/css/theme.css`, `wp-dev/seed.php`.

- [ ] **Step 1:** `inc/patterns.php` registers category `kresge` ("Kresge sections"). Each pattern file header: `Title`, `Slug: kresge/<name>`, `Categories: kresge`, `Inserter: yes`.
- [ ] **Step 2:** Patterns — outer group `{"tagName":"section","anchor":"<id>","className":"kc-<name>","templateLock":"contentOnly","align":"full"}`; copy text from current `index.html`:
  - `hero`: columns `kc-hero` (forest bg): eyebrow paragraph `kc-eyebrow`, h1, lede, buttons (Request a site visit → `#contact`, "See recent work →" `is-style-outline`→ `#work`); image column = `get_theme_file_uri('assets/images/hero.jpg')`, alt "".
  - `trust-strip`: list `kc-trust` of 5 items `<strong>15+</strong> years building` …
  - `services` (`#services`): header group (eyebrow, h2, sub) + grid group (`layout:{type:grid,minimumColumnWidth:250px}`) of 4 `kc-card` groups (h3, p, list). Lists: `templateLock:false` on the card's list.
  - `work` (`#work`, mist bg): header; query 1 (`{"query":{"postType":"project","perPage":1,"orderBy":"date","order":"desc","inherit":false}}`) → post-template → columns `kc-featured`: post-featured-image (aspect 4/3, cover), column: post-terms project_type `kc-eyebrow`, post-title h3 linked, post-excerpt (no more link), read-more "View project →"; subsection header `kc-subhead` h3 "More recent builds"; query 2 (`perPage:3,offset:1`) post-template grid 3 cols → `kc-project-card`: featured image (200px cover), post-terms `kc-tag`, title h3 linked, excerpt.
  - `process` (`#process`): header + group `kc-timeline` of 3 `kc-step` groups (number paragraph `kc-step-num`, h3, p).
  - `testimonial`: quote `kc-quote` with cite "Sarah K. — Maplewood, Basement build-out".
  - `faq` (`#faq`): header + group `kc-faq` (`templateLock:false`) of 4 core/details.
  - `contact` (`#contact`): group `kc-contact-card` columns: left eyebrow/h2/sub + 3 label/value pairs bound to `phone`/`email`/`service_area` (`kc-contact-label` small caps, values as linked paragraphs); right `<!-- wp:shortcode -->[fluentform id="1"]<!-- /wp:shortcode -->`.
- [ ] **Step 3:** `templates/front-page.html` = header part, `<!-- wp:post-content {"layout":{"type":"constrained"}} /-->` in `main#main`, footer part. Seed "Home" page whose content is the 8 `<!-- wp:pattern {"slug":"kresge/<name>"} /-->` blocks; `update_option('show_on_front','page')`, `page_on_front`.
- [ ] **Step 4:** `theme.css`: port hero split (+ ≤900px stack), trust strip (+ ≤600 2-col), section headers, `kc-card`, featured + project cards, `kc-tag`, subsection rule, timeline, quote mark, details FAQ (+/− marker), contact card, Fluent Forms field styling (inputs/select 47px, radius 6px, line border, sage focus ring, submit as pill button), eyebrow sage-dark, reduced-motion.
- [ ] **Step 5: Verify** — Playwright 1440 + 390 full-page screenshots vs `.playwright-mcp/critique-desktop.jpg` / `after-mobile.jpg`; same section order and look; `scrollWidth === innerWidth`; eyebrow color `rgb(58, 99, 71)`; select height ≈ input height.
- [ ] **Step 6: Commit** `feat(wp): homepage patterns`.

### Task 6: Remaining templates

**Files:** `templates/{page,single-project,archive-project,404}.html`.

- [ ] **Step 1:** `page.html` header + title + content + footer. `single-project.html`: header; constrained main: post-terms eyebrow, post-title h1, featured image (wide, radius 12), post-content, button "Plan a similar project" → `/#contact`; footer. `archive-project.html`: title "Recent work" + query (inherit) grid of `kc-project-card`. `404.html`: heading + "Back to home" button.
- [ ] **Step 2: Verify** — `curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:9400/projects/` → 200; one project URL → 200 with its title; `/nope/` → 404 and body contains "Back to home".
- [ ] **Step 3: Commit** `feat(wp): page, project and 404 templates`.

### Task 7: Owner flows, edge cases, Theme Check

- [ ] **Step 1:** In browser pane (logged in): edit Home → change hero headline → Save → frontend shows it; layout unchanged; block toolbar shows no move/delete for sections (contentOnly). (Review focus #1)
- [ ] **Step 2:** Add project "Test project" with image → it becomes featured; old featured moves to first card.
- [ ] **Step 3:** Project with no featured image → card shows mist placeholder area (CSS `kc-project-card` image wrapper min-height + mist bg). (#4)
- [ ] **Step 4:** Trash 3 projects → page renders, no layout break. (#3) Restore.
- [ ] **Step 5:** Deactivate Fluent Forms → contact shows details, page text has no `[fluentform`. (#5) Reactivate.
- [ ] **Step 6:** Appearance → Theme Check → run on Kresge Carpentry → no REQUIRED errors (fix any, e.g. missing `readme.txt`/`screenshot.png`: add `readme.txt`; screenshot 1200×900 from Playwright).
- [ ] **Step 7: Commit** fixes `fix(wp): owner-flow and theme-check fixes`.

### Task 8: Launch config + docs

- [ ] **Step 1:** `.claude/launch.json` add `{"name":"wordpress","runtimeExecutable":"npm","runtimeArgs":["run","wp"],"port":9400}`.
- [ ] **Step 2:** README "WordPress theme" section: `npm install --ignore-scripts && npm rebuild node`, `npm run wp`, login is automatic, data resets each start, how to zip/deploy, Business details location.
- [ ] **Step 3: Commit** `docs: WordPress theme dev instructions`.
