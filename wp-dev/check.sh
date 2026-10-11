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

for check in "${@}"; do "check_$check"; done
