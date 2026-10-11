#!/usr/bin/env bash
# Smoke checks against the running Playground site (npm run wp).
set -euo pipefail
BASE=${BASE:-http://127.0.0.1:9400}
JAR=$(mktemp)
get() { curl -s -m 30 -L -c "$JAR" -b "$JAR" "$BASE$1"; }
code() { curl -s -m 30 -L -c "$JAR" -b "$JAR" -o /dev/null -w '%{http_code}' "$BASE$1"; }
fail() { echo "FAIL: $1"; exit 1; }
ok() { echo "ok: $1"; }

home=$(get /)
grep -q '<title>Kresge Carpentry' <<<"$home" || fail "home title"; ok "home title"

check_header() {
	grep -q 'class="[^"]*kc-header' <<<"$home" || fail "header part"
	grep -q 'wp-block-navigation' <<<"$home" || fail "navigation"
	grep -q 'kresge/assets/css/theme.css' <<<"$home" || fail "theme.css enqueued"
	grep -q 'class="[^"]*kc-footer' <<<"$home" || fail "footer part"
	ok "header, nav, footer, theme.css"
}

check_projects() {
	local json
	json=$(get '/?rest_route=/wp/v2/project&_fields=title,featured_media,project_type')
	[ "$(grep -o '"title"' <<<"$json" | wc -l | tr -d ' ')" = 4 ] || fail "4 projects ($json)"
	grep -q '"featured_media":0' <<<"$json" && fail "every project has an image"
	grep -q '^\[{"title":{"rendered":"Elevated deck' <<<"$json" || fail "newest project is the featured one"
	ok "4 seeded projects with images, newest first"
}

check_business() {
	grep -q 'href="tel:5551234567"' <<<"$home" || fail "phone binding renders tel: link"
	grep -q 'href="mailto:hello@kresgecarpentry.com"' <<<"$home" || fail "email binding renders mailto: link"
	grep -q 'Call us' <<<"$home" && fail "static fallback replaced"
	grep -q '"@type":"LocalBusiness"' <<<"$home" || fail "LocalBusiness JSON-LD"
	grep -q '"telephone":"(555) 123-4567"' <<<"$home" || fail "JSON-LD telephone"
	ok "business bindings + JSON-LD"
}

check_homepage() {
	local id
	grep -q 'class="[^"]*kc-hero' <<<"$home" || fail "hero section"
	for id in services work process faq contact; do
		grep -q "id=\"$id\"" <<<"$home" || fail "section #$id"
	done
	grep -q 'class="[^"]*kc-trust' <<<"$home" || fail "trust strip"
	grep -q 'class="[^"]*kc-quote' <<<"$home" || fail "testimonial"
	[ "$(grep -o 'kc-featured' <<<"$home" | wc -l | tr -d ' ')" -ge 1 ] || fail "featured project"
	grep -q 'Elevated deck &amp; stair rebuild\|Elevated deck &#038; stair rebuild' <<<"$home" || fail "newest project featured"
	[ "$(grep -o 'class="[^"]*kc-project-card' <<<"$home" | wc -l | tr -d ' ')" = 3 ] || fail "3 project cards"
	grep -q 'fluentform' <<<"$home" || fail "contact form"
	grep -q '\[fluentform' <<<"$home" && fail "raw shortcode printed"
	grep -q '<details' <<<"$home" || fail "FAQ details"
	ok "homepage sections, projects, form"
}

for check in ${@+"$@"}; do "check_$check"; done
