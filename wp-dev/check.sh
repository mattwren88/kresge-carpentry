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

for check in ${@+"$@"}; do "check_$check"; done
