# Tests

Run against a **local or staging** WordPress site with Elementor and this plugin active. Several suites create data (QA projects/reviews, test leads); none of them belong on a live dealer site.

| Suite | Command | What it proves |
|---|---|---|
| Render | `wp eval-file tests/php/render-pages.php` | 12 templates render, one H1 each, no unresolved tokens, no editor markup live, approval gates closed |
| Elementor | `wp eval-file tests/php/elementor.php` | 21 widgets + category, Page Composer controls, settings round-trip, per-template isolation, order, device visibility, merge of new sections, single H1 |
| Security | `tests/security.sh` | Nonce, time token, honeypot, validation, upload type checks, EXIF stripping, random private storage, duplicate guard, rate limit, file access, REST/search exposure |
| Pages | `node tests/browser/pages.js` | 12 pages × 5 widths: HTTP 200, no horizontal overflow, no JS errors, one H1, title, meta description, valid JSON-LD; full-page screenshots |
| Interactions | `node tests/browser/interactions.js` | Gallery filters + URL state + no-JS fallback, lightbox keyboard/focus, before/after, ARIA tabs, dropdown keyboard, mobile drawer + scroll lock |
| Form | `node tests/browser/form.js` | Preselect, inline errors + focus + `aria-invalid`, commercial fields, client file checks, previews, AJAX success, no delivery claim |
| Accessibility | `node tests/browser/a11y.js` | axe-core WCAG 2.0/2.1/2.2 A + AA on all pages at 1440 px and 375 px |
| Design settings | `WP="wp …" node tests/browser/design.js` | No hard-coded brand colors; a new palette + fonts reach every widget/header/footer (computed-style scan), contrast still passes, no overflow with a wide font, Elementor global and Google fonts, settings screen preview/warnings/reset. Restores settings afterwards |
| Photos & interaction | `node tests/browser/ux.js` | Bundled photos load with width/height and srcset and carry no EXIF/XMP/ICC metadata; theme-style shield up to date; hover motion only for mouse/trackpad, keyboard focus mirrors it, whole overlay cards clickable, focus rings not clipped, reduced motion; Everyday/Holiday comparison; mobile call & quote bar (first screen, footer, quote form) |
| Editor | `HOME_ID=<id> node tests/browser/editor.js` | Real Elementor editor: panel scope, live preview, pending badges editor-only, save → live, then reverts |

## Setup

```bash
# QA content the browser tests expect (4 projects, 4 reviews incl. one without permission)
wp eval-file tests/php/seed-qa-data.php

# Browser tooling
cd tests && npm install && npx playwright install chromium && cd ..
```

Environment variables:

| Variable | Default | Used by |
|---|---|---|
| `BASE_URL` | `http://localhost:8080` | all HTTP/browser suites |
| `WP` | `wp` | `security.sh`, `design.js` (e.g. `WP="wp --path=/srv/www --allow-root"`) |
| `OUT_DIR` | `test-results/` | screenshots |
| `ADMIN_USER` / `ADMIN_PASS` | `admin` / `admin` | `editor.js`, `design.js` |
| `HOME_ID` | — | `editor.js` (from `wp xlights status`) |
| `PLAYWRIGHT_MODULE`, `CHROMIUM_PATH`, `AXE_PATH` | resolved from `tests/node_modules` | override tool locations |

## Generated CSS

The end of `assets/css/xlights.css` holds a generated block that keeps Elementor *Theme Style* colors off the widgets' links and buttons. After changing any link/button rule:

```bash
node bin/build-theme-shield.js          # regenerate
node bin/build-theme-shield.js --check  # CI: exit 1 when stale (also checked by ux.js)
```

## Clean up

```bash
wp eval-file tests/php/cleanup-qa-data.php   # QA projects/reviews/images + @example.com leads and their photos
```

## Notes

- `security.sh` reports a **WARN** (not a failure) when lead photos are downloadable by direct URL. That means the web server ignores `.htaccess` (Nginx, PHP's built-in server) — fix it per `docs/DEPLOYMENT.md` §7 before launch. The plugin shows the same warning in wp-admin.
- Fixtures are generated at run time (`tests/fixtures/make-fixtures.php`); no customer photos are stored in the repository.
- Email delivery can't be verified automatically. Use **X LIGHTS → Settings → Leads & Form → Send test email** and check the inbox.
