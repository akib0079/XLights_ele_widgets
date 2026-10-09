# Testing report — v1.1.0

**TL;DR:** the 1.1.0 brand redesign was re-verified with every suite: **all passing**. The same expected warning remains: PHP's dev server ignores `.htaccess`, so production needs the Nginx rule or `XLIGHTS_LEADS_DIR` (the plugin detects this and warns in wp-admin). Email *delivery* still has to be checked on the real host.

## v1.1.0 — design system aligned with the live X LIGHTS website

| Suite | Result |
|---|---|
| Render (`tests/php/render-pages.php`) | ✅ 84/84 |
| Elementor (`tests/php/elementor.php`) | ✅ 32/32, with and without published projects/reviews |
| Security (`tests/security.sh`) | ✅ 34/34, 1 expected warning |
| Pages — 12 pages × 5 widths: overflow, JS errors, one H1, title, meta, JSON-LD | ✅ 276/276 |
| Interactions (incl. new: inset hero, solid header, dealer lockup, open-dropdown contrast) | ✅ 30/30 |
| Form | ✅ 13/13 |
| Accessibility — axe WCAG 2.0/2.1/2.2 A+AA, 12 pages × 1440/375 | ✅ 24/24, zero violations |
| Editor round-trip | ✅ 9/9 |
| Header width check at 1920/1440/1366/1320/1301/1300/1280/1024/390 px | ✅ no overflow |
| WPCS security sniffs | ✅ clean |

Issues found and fixed during the redesign:

| Issue | Fix |
|---|---|
| Logo rendered at natural size (`.xl-root img { height: auto }` outranked the logo rule), widening the page | Two-class selector for the logo |
| Uppercase nav + lockup + phone + CTA overflowed between 1181 and 1290 px | Wider header container, tighter tracking below 1440 px, phone collapses to an icon below 1500 px, drawer below 1300 px |
| Desktop dropdown links were dark text on navy (`.xl-root a { color: inherit }` won) | Two-class selector; regression check added to `interactions.js` |
| Dark panels inside light sections (CTA, quote sidebar, footer) inherited light-context tokens (black button on navy) | One shared on-dark token set for all dark surfaces |
| "Call us / Email" labels in the quote sidebar below 4.5:1 | Explicit muted color instead of stacked opacity |
| Font preload still pointed at the removed Montserrat file | Preloads Barlow Condensed 300 + Barlow 400 |

---

# Testing report — v1.0.0

## Environment

| Component | Version |
|---|---|
| WordPress | 7.1.3 (SQLite integration 3.1.0) |
| Elementor | 4.0.8 (built from source) |
| PHP | 8.3, built-in server |
| Browser | Chromium via Playwright 1.56.1 |
| axe-core | 4.10.3 |
| WPCS | 3.x (security sniffs) |
| Data | `tests/php/seed-qa-data.php` (4 projects, 4 reviews incl. one without permission) |

## Results

| Suite | Checks | Result | Notes |
|---|---|---|---|
| Render (`tests/php/render-pages.php`) | 84 | ✅ 84/84 | 12 templates; one H1 each; no stray tokens; no editor markup live; financing, credentials and store links hidden while gated (verified to reappear when the gate opens) |
| Elementor (`tests/php/elementor.php`) | 33 | ✅ 33/33 | 21 widgets; 2,521 Page Composer controls built in ~45 ms; editor config 642 KB |
| Security (`tests/security.sh`) | 34 | ✅ 34/34, 1 warn | Warn = dev server ignores `.htaccess` (see below) |
| Pages (`tests/browser/pages.js`) | 276 | ✅ 276/276 | 12 pages × 375/768/1024/1440/1920 px |
| Interactions (`tests/browser/interactions.js`) | 27 | ✅ 27/27 | |
| Form (`tests/browser/form.js`) | 13 | ✅ 13/13 | |
| Accessibility (`tests/browser/a11y.js`) | 24 | ✅ 24/24 | Zero WCAG 2.0/2.1/2.2 A+AA violations, 12 pages × 2 widths |
| Editor (`tests/browser/editor.js`) | 9 | ✅ 9/9 | Real Elementor editor ready in ~4.8 s |
| WPCS security sniffs | — | ✅ clean | Escaping, nonces, sanitization, SQL, safe redirects |
| PHP 7.4 compatibility scan | — | ✅ | No PHP 8-only syntax |
| WP-CLI commands | manual | ✅ | `status`, `setup` (idempotent, skips existing pages), `settings get/set` (secrets masked, unknown keys rejected), `purge-leads --dry-run`, `test-email` |

### Security detail

| Attack / failure | Expected | Observed |
|---|---|---|
| Missing / forged nonce | 403, nothing stored | ✅ |
| Tampered time token | 403 | ✅ |
| Submit in < 3 s (bot) | Fake 200, nothing stored | ✅ |
| Honeypot filled | Fake 200, nothing stored | ✅ |
| Real success vs fake success distinguishable? | No | ✅ (fixed: response no longer includes the lead ID) |
| Invalid fields | 400 + per-field errors (first name, email, phone, ZIP, interests, privacy) | ✅ |
| PHP file renamed `.jpg` | Rejected on `photos` field | ✅ |
| Photo with EXIF/GPS | Stored re-encoded, metadata gone | ✅ |
| Stored file naming | `YYYY/MM/<32 hex>/<24 hex>.jpg`, `index.php` in every folder | ✅ |
| Double submit | Fake 200, stored once | ✅ |
| 6th submission in 30 min from one IP | 429 | ✅ |
| Anonymous request for a lead photo | 302 → login | ✅ |
| Subscriber request for a lead photo | 403 | ✅ (manual, cookie session) |
| Leads via REST API / site search | Not exposed | ✅ |
| CSV export without nonce | Rejected | ✅ (manual) |
| Lead deletion | Photos and the lead's folder removed | ✅ |
| Direct URL to a stored photo | 403/404 | ⚠️ 200 on PHP's dev server (ignores `.htaccess`) → covered by deploy step + admin warning |

### Browser detail

- **Responsive:** no horizontal overflow at any width; header CTA never wraps; card grids reflow without orphans; mobile hero overlay keeps text ≥ 4.5:1.
- **Keyboard:** dropdowns (Enter/Escape), drawer (focus trap, Escape, scroll lock), tabs (arrow keys, roving focus), lightbox (`<dialog>`, arrows, Escape, focus return), FAQ disclosure.
- **No-JS:** gallery filters via `?xl_filter=`; form posts to `admin-post.php` with a redirect status message.
- **Editor:** panel lists only the current template's sections; preview re-renders on edit; pending badges appear only in the editor; save publishes; zero editor JS errors.

## Bugs found and fixed during QA

| Issue | Fix |
|---|---|
| Page Setup cached a "missing" page ID → child pages created without parents | Cache only positive lookups; refresh on assignment |
| `.xl-btn` display rule overrode `hidden` (Load more visible with nothing to load) | Scoped `[hidden]{display:none!important}` |
| Header CTA wrapping; current-page nav item low contrast over dark hero; icon-only phone link without a name | `nowrap`; accent color for current item; `aria-label` |
| Transparent-header white links applied before JS ran | Restricted to `.is-over-dark` |
| Connected-system diagram cramped; orphan card rows; duplicate CTA band above footer | Layout fixes; footer band auto-hidden on composer pages |
| Editor guidance and one unverified claim ("No visible cords or clips") leaked into public copy | Moved to editor-only notes; claim removed |
| Success response exposed the lead ID (leaked volume; let bots detect the honeypot) | Removed from public responses |
| Lead photo folders listable on servers with autoindex; empty folders left after deletion | `index.php` in every folder; folder removed with the lead |
| Exposed lead folder only caught if someone read the deploy guide | Loopback self-check → admin notice + `wp xlights status` warning |
| `wp xlights settings set <unknown>` gave a usage error | Explicit "Unknown setting" message |
| Before/after photos kept their natural height inside Elementor (`.elementor img { height: auto }` won), leaving a black band when before/after aspect ratios differ; comparison could exceed one screen | Higher-specificity rule; comparison capped to fit the viewport at 4:3 |

## Not covered automatically

| Area | Why | How to verify |
|---|---|---|
| Email delivery to `info@xlightsled.com` | Sandbox has no mail transport (the plugin correctly recorded those sends as *Failed*) | Send test email + one real quote after SMTP is configured |
| `.htaccess` / Nginx protection | Dev server ignores both | `tests/security.sh` on staging; wp-admin warning |
| Real devices / Safari | Chromium only | Spot-check iOS Safari and Android Chrome on staging |
| Turnstile | Needs real Cloudflare keys | Enable on staging, submit once |
| Elementor Pro Theme Builder | Not installed | Header/Footer widgets are standard widgets; test if the client uses Pro |
| Load / performance under traffic | No production host | Run PageSpeed Insights on staging; target LCP < 2.5 s on mobile |

## Known limitations

- Bundled default photos are 886 px wide (from the client upload). They look fine in cards, but they're soft as full-width heroes on large screens. Replace them with hi-res originals or real project photos.
- Elementor 4's image-optimization module adds a duplicate `loading="lazy"` attribute to some images (upstream, harmless).
- The Page Composer's editor configuration is 642 KB because all 12 templates are in one widget. The editor stays responsive (~4.8 s cold load), and the front end isn't affected.
- "Interests" are checkboxes; selecting several is labeled "Multiple services" in the lead, instead of a separate dropdown option.
