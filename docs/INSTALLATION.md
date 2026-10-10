# Installation

**TL;DR:** install Elementor (free) → upload the plugin ZIP → fill in **X LIGHTS → Settings** → run **X LIGHTS → Page Setup** → review drafts in Elementor → publish.

## 1. Requirements

| Component | Minimum | Tested |
|---|---|---|
| WordPress | 6.4 | 7.1.3 |
| PHP | 7.4 (8.1+ recommended) | 8.3 |
| Elementor | 3.20 | 4.0.8 |
| Elementor Pro | optional | — |
| Theme | any; **Hello Elementor** recommended | Hello Elementor |
| PHP extensions | GD or Imagick (photo re-encoding / EXIF stripping), `fileinfo` | GD |

Also needed before launch: a working outbound mail path (SMTP, see [DEPLOYMENT.md](DEPLOYMENT.md#6-email-delivery)) and HTTPS.

## 2. Build or obtain the ZIP

```bash
bin/build-zip.sh            # → dist/xlights-elementor-widgets-<version>.zip
```

The script lints every PHP file, honours `xlights-elementor-widgets/.distignore` and refuses to build on a lint error. Requires `php` and `zip`.

## 3. Install

1. **Plugins → Add New → Upload Plugin** → choose the ZIP → **Install** → **Activate**.
   - Or WP-CLI: `wp plugin install dist/xlights-elementor-widgets-<version>.zip --activate`
2. Install and activate **Elementor** if it isn't already. Without it an admin notice explains what is missing; pages still render their default content through the `[xlights_page]` shortcode but can't be edited visually.
3. Activation:
   - creates the private lead-photo folder (`wp-content/uploads/xlights-leads/`, denied to web requests) — or uses `XLIGHTS_LEADS_DIR` if defined;
   - grants administrators the `manage_xlights_leads` capability;
   - schedules the daily retention job (`xlights_daily`);
   - stores default settings. **It creates no pages and publishes nothing.**

## 4. Configure dealer settings

**X LIGHTS → Settings.** Every phone number, email, CTA label, service area and link on the site comes from here — nothing is hard-coded in pages.

| Tab | Must check before launch |
|---|---|
| Dealer | Dealer name, market name, primary area, **service areas** (one per line), legal name |
| Contact & CTAs | Phone, public email, address (hidden unless *Show address* is on), hours, CTA labels |
| Leads & Form | **Lead recipients**, From name/address (must be allowed by your SMTP provider), privacy policy URL, retention days |
| Credentials & Financing | Leave *Verified* and *Financing enabled* **off** until the client supplies written details |
| Brand | Logo (dark + light), favicon, dealer lockup on/off |
| Colors & Fonts | Five brand colors (accent, headings & buttons, body text, dark sections, light grey sections) and the heading/body fonts, heading case and size — for every widget, the header and the footer at once. Defaults follow the live X LIGHTS website; leave them unless the client asks |
| App & Social | App Store / Google Play / Google Reviews URLs — leave blank until confirmed; related blocks stay hidden |
| Map & SEO | Map on/off, LocalBusiness schema, business type, meta fallback, breadcrumbs |
| Header & Footer | Mode (see below), sticky/transparent header, phone in header, footer note |

Use **Send test email** at the bottom of *Leads & Form* (or `wp xlights test-email you@example.com`). "Accepted by the mail transport" only means the server took the message — confirm it actually arrives in the inbox and isn't in spam.

### Header & footer mode

| Mode | Use when |
|---|---|
| **Elementor Canvas pages** (default) | Any theme. The plugin's header/footer render only on pages using the *Elementor Canvas* template — which is what Page Setup assigns. |
| **Every page** | Hello Elementor (or list the theme's header/footer selectors to hide). Header/footer also appear on posts, project singles, 404, etc. |
| **Off** | You build the header/footer with Elementor Pro Theme Builder, using the *X LIGHTS Header* / *X LIGHTS Footer* widgets. |

## 5. Create the pages

**X LIGHTS → Page Setup** (or WP-CLI, below).

- Choose which of the 12 pages to create. Each gets a Page Composer widget pre-set to its template.
- **Drafts by default.** Publish only after the client has reviewed the content.
- Options: set Home as the static front page; create the *X LIGHTS Header* menu (skipped if a header menu is already assigned); page template *Elementor Canvas + X LIGHTS header/footer* vs *Elementor Full Width*.
- **Existing pages with content are never changed** unless you tick *Replace* for that page. When you do, the previous Elementor data is backed up to the `_xlights_backup_elementor_data` post meta first.

| Page | Slug |
|---|---|
| Home | `/` (front page) |
| About Us | `/about-us/` |
| X LIGHTS App | `/x-lights-app/` |
| Reviews | `/reviews/` |
| Lighting Solutions | `/lighting-solutions/` |
| Permanent Soffit Lighting | `/lighting-solutions/permanent-soffit-lighting/` |
| Hanging Bulb Bistro Lighting | `/lighting-solutions/bistro-lighting/` |
| Deck Lighting | `/lighting-solutions/deck-lighting/` |
| Landscape Lighting | `/lighting-solutions/landscape-lighting/` |
| Commercial Lighting | `/commercial-lighting/` |
| Projects | `/projects/` |
| Contact Us | `/contact-us/` |

After setup, visit **Settings → Permalinks** once and click **Save** (flushes rewrite rules for the project URLs).

## 6. Add content

- **X LIGHTS → Projects** — title, *City / area* (never a homeowner's street address), Lighting Categories + Property Type (sidebar), featured image, gallery, optional before/after pair, *Featured* flag, Order (Page Attributes).
- **X LIGHTS → Reviews** — only real reviews with the customer's permission. Reviews without *Customer permission confirmed* ticked never render, even when published.
- **X LIGHTS → Overview** shows the client-approval checklist and what is still missing.

## 7. WP-CLI reference

```bash
wp xlights status                                   # each page: path, ID, status, composer yes/no
wp xlights setup                                    # create missing pages as drafts
wp xlights setup --pages=home,contact --publish     # selected pages, published
wp xlights setup --front-page --menu                # also set front page + header menu
wp xlights setup --overwrite=home                   # replace Home's Elementor data (backed up first)
wp xlights setup --template=header_footer           # Elementor Full Width instead of Canvas
wp xlights settings get [<key>]
wp xlights settings set phone "949-795-2206"
wp xlights test-email you@example.com
wp xlights purge-leads --dry-run
```

## 8. Updating

Upload the new ZIP (**Plugins → Add New → Upload → Replace current with uploaded**). Settings, pages, projects, reviews and leads are preserved. Page Composer widgets keep their saved content; newly added sections in an update appear automatically at their default position.

## 9. Uninstalling

Deleting the plugin removes its options, transients, cron job and capability. **Projects, reviews, leads and lead photos are kept** unless `define( 'XLIGHTS_UNINSTALL_DELETE_DATA', true );` is in `wp-config.php` at the time of deletion.
