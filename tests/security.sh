#!/usr/bin/env bash
#
# Black-box security tests for the quote form, lead storage and lead files.
#
#   BASE_URL=http://localhost:8080 WP="wp --path=/srv/www" tests/security.sh
#
# LOCAL / STAGING ONLY: creates test leads (emails @example.com), consumes
# and then resets the form rate limit for this IP. Clean up afterwards with
#   wp eval-file tests/php/cleanup-qa-data.php
#
# Requires: curl, php (CLI with GD), WP-CLI pointed at the same site.
set -uo pipefail

BASE_URL="${BASE_URL:-http://localhost:8080}"
WP="${WP:-wp}"
AJAX="$BASE_URL/wp-admin/admin-ajax.php"
HERE="$(cd "$(dirname "$0")" && pwd)"
TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT

pass=0
fail=0
warn=0
check() { # actual expected message
	if [ "$1" = "$2" ]; then
		echo "PASS $3"
		pass=$((pass + 1))
	else
		echo "FAIL $3 (expected '$2', got '$1')"
		fail=$((fail + 1))
	fi
}
wpq() { $WP "$@" 2>/dev/null; }
jget() { # dotted.path < json
	php -r '$d = json_decode(stream_get_contents(STDIN), true); foreach (explode(".", $argv[1]) as $k) { $d = is_array($d) && array_key_exists($k, $d) ? $d[$k] : null; } echo is_bool($d) ? ($d ? "true" : "false") : (is_array($d) ? implode(",", array_keys($d)) : (string) $d);' "$1"
}
lead_count() { wpq post list --post_type=xlights_lead --post_status=any --format=count; }
reset_limits() {
	wpq eval 'global $wpdb; $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE \"\\_transient\\_%xl\\_rl\\_%\" OR option_name LIKE \"\\_transient\\_%xl\\_dup\\_%\"" ); wp_cache_flush();' >/dev/null
}
fresh_token() {
	local json
	json="$(curl -s "$AJAX?action=xlights_quote_token")"
	NONCE="$(echo "$json" | jget data.nonce)"
	TOKEN="$(echo "$json" | jget data.token)"
}
# submit <outfile> [extra curl args...] — prints HTTP status
submit() {
	local out="$1"
	shift
	curl -s -o "$out" -w '%{http_code}' "$AJAX" -F action=xlights_quote "$@"
}
valid_fields() { # unique-suffix → fills the VALID array
	VALID=(-F "_xl_nonce=$NONCE" -F "_xl_t=$TOKEN" -F first_name=QA -F last_name=Security
		-F phone=949-795-2206 -F "email=qa-sec-$1@example.com" -F "address=1 QA Way" -F city=Testville
		-F state=CA -F zip=92672 -F property_type=residential -F "interests[]=soffit" -F privacy=1
		-F "message=Security test $1")
}

echo "Target: $BASE_URL"
php "$HERE/fixtures/make-fixtures.php" "$TMP" >/dev/null || { echo "Cannot build fixtures"; exit 1; }
reset_limits
start_count="$(lead_count)"

# 1–3. Request authenticity -------------------------------------------------
check "$(submit "$TMP/r" -F first_name=x)" 403 "missing nonce rejected (403)"
check "$(jget data.code <"$TMP/r")" nonce "missing nonce → code=nonce"
check "$(submit "$TMP/r" -F _xl_nonce=abcdef1234 -F _xl_t=1.x)" 403 "forged nonce rejected (403)"
fresh_token
check "$(submit "$TMP/r" -F "_xl_nonce=$NONCE" -F _xl_t=1700000000.deadbeefdeadbeefdead)" 403 "tampered time token rejected (403)"

# 4–5. Bot traps answer with a fake success and store nothing ---------------
fresh_token
valid_fields fast
check "$(submit "$TMP/r" "${VALID[@]}")" 200 "sub-3-second submission gets a silent 200"
check "$(lead_count)" "$start_count" "…and creates no lead"
sleep 3.2
valid_fields honeypot
check "$(submit "$TMP/r" "${VALID[@]}" -F xl_website=http://spam.example)" 200 "honeypot submission gets a silent 200"
check "$(lead_count)" "$start_count" "…and creates no lead"

# 6. Server-side validation -------------------------------------------------
check "$(submit "$TMP/r" -F "_xl_nonce=$NONCE" -F "_xl_t=$TOKEN" -F email=not-an-email -F phone=123 -F zip=1)" 400 "invalid fields rejected (400)"
fields="$(jget data.fields <"$TMP/r")"
for f in first_name email phone zip interests privacy; do
	case ",$fields," in *",$f,"*) check ok ok "per-field error returned for $f" ;; *) check missing ok "per-field error returned for $f" ;; esac
done

# 7. Uploads ----------------------------------------------------------------
valid_fields fakeimg
check "$(submit "$TMP/r" "${VALID[@]}" -F "photos[]=@$TMP/fake-image.jpg;type=image/jpeg")" 400 "PHP file disguised as .jpg rejected"
check "$(jget data.fields <"$TMP/r")" photos "…with an error on the photos field"

# 8. Valid submission with an EXIF-tagged photo -----------------------------
valid_fields good1
check "$(submit "$TMP/r" "${VALID[@]}" -F "photos[]=@$TMP/photo-exif.jpg;type=image/jpeg")" 200 "valid submission accepted"
check "$(jget success <"$TMP/r")" true "…success=true"
check "$(lead_count)" "$((start_count + 1))" "…exactly one lead stored"
check "$(jget data.lead_id <"$TMP/r")" "" "…response does not reveal the lead ID"
lead_id="$(wpq post list --post_type=xlights_lead --post_status=any --orderby=ID --order=DESC --posts_per_page=1 --field=ID)"
check "$(wpq post get "$lead_id" --field=post_status)" private "lead stored as private"
info="$(wpq eval '
$l = \XLights\PostTypes\Leads::get( '"$lead_id"' );
$f = $l["files"] ? \XLights\Forms\Upload_Handler::absolute_path( $l["files"][0]["path"] ) : "";
$raw = $f && is_readable( $f ) ? file_get_contents( $f ) : "";
$exif = $f ? @exif_read_data( $f ) : false;
echo ( $f && is_file( $f ) ? "stored" : "missing" ), "|", ( false === strpos( $raw, "XL-EXIF-MARKER" ) ? "clean" : "leaked" ), "|", ( empty( $exif["ImageDescription"] ) ? "noexif" : "exif" ), "|", ( preg_match( "#^\d{4}/\d{2}/[a-f0-9]{32}/[a-f0-9]{24}\.jpg$#", $l["files"][0]["path"] ?? "" ) ? "random" : "named" ), "|", str_replace( ABSPATH, "/", $f );
')"
IFS='|' read -r stored marker exif naming relpath <<<"$info"
check "$stored" stored "photo stored in private directory"
check "$marker" clean "EXIF/GPS metadata stripped from stored photo"
check "$exif" noexif "stored photo has no EXIF ImageDescription"
check "$naming" random "stored photo renamed to random path"

# 9. Duplicate guard --------------------------------------------------------
valid_fields good1
check "$(submit "$TMP/r" "${VALID[@]}")" 200 "duplicate resubmission answered with 200"
check "$(lead_count)" "$((start_count + 1))" "…but not stored twice"

# 10. Rate limit (default 5 successful submissions / 30 min) -----------------
max="$(wpq xlights settings get rate_limit_max)"
max="${max:-5}"
for i in $(seq 2 "$max"); do
		valid_fields "good$i"
		submit "$TMP/r" "${VALID[@]}" >/dev/null
done
valid_fields over
check "$(submit "$TMP/r" "${VALID[@]}")" 429 "submission #$((max + 1)) rate-limited (429)"
check "$(lead_count)" "$((start_count + max))" "…$max leads stored in total"

# 11. Lead files and lead data are not public ------------------------------
file_url="$BASE_URL/wp-admin/admin-post.php?action=xlights_lead_file&lead=$lead_id&file=0"
code="$(curl -s -o /dev/null -w '%{http_code}' "$file_url")"
loc="$(curl -s -o /dev/null -w '%{redirect_url}' "$file_url")"
check "$code" 302 "anonymous lead-file request redirected"
case "$loc" in *wp-login.php*) check ok ok "…to the login page" ;; *) check "$loc" wp-login "…to the login page" ;; esac

if [ -n "$relpath" ] && [ "${relpath#/wp-content/}" != "$relpath" ]; then
	direct="$(curl -s -o /dev/null -w '%{http_code}' "$BASE_URL$relpath")"
	if [ "$direct" = 403 ] || [ "$direct" = 404 ]; then
		check ok ok "direct URL to stored photo blocked ($direct)"
	else
		echo "WARN direct URL to stored photo returned $direct — this web server ignores .htaccess; add the Nginx rule or define XLIGHTS_LEADS_DIR (docs/DEPLOYMENT.md §7)"
		warn=$((warn + 1))
	fi
else
	check ok ok "photos stored outside the web root (XLIGHTS_LEADS_DIR)"
fi

check "$(curl -s -o /dev/null -w '%{http_code}' "$BASE_URL/wp-json/wp/v2/xlights_lead")" 404 "leads not exposed in the REST API"
search="$(curl -s "$BASE_URL/?s=Security+test+good1")"
case "$search" in *"qa-sec-good1@example.com"* | *"Security test good1"*"xlights_lead"*) check leaked hidden "leads absent from public search" ;; *) check ok ok "leads absent from public search" ;; esac

reset_limits
echo
echo "Passed: $pass  Failed: $fail  Warnings: $warn"
[ "$fail" -eq 0 ]
