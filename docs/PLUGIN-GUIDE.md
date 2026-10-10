# X LIGHTS Elementor Widgets — Plugin Guide (v1.3.0)

**TL;DR:** install the ZIP (Elementor required), fill in **X LIGHTS → Settings**, run **X LIGHTS → Page Setup** to create the 12 pages, then edit any page in Elementor through its **Page Composer** widget. Every phone number, color, font, link and approval switch lives in **Settings** and flows to every page, the header and the footer. Projects, reviews and incoming quote requests are managed under **X LIGHTS** in wp-admin.

---

## 1. How the plugin works

```
X LIGHTS → Settings ──────────────┐  dealer details, CTAs, colors & fonts, approvals
                                  ▼
Elementor page ─► Page Composer widget ─► chosen template (e.g. "Home")
                                  │        └─ sections (Hero, Cards, FAQ …) with built-in content
                                  │           + your edits + {tokens} replaced from Settings
                                  ▼
                       rendered page + header + footer
                                  │
Projects / Reviews (wp-admin) ────┤  galleries, before/after, reviews pull from these
                                  ▼
Quote form ─► validation + spam checks ─► Lead saved (X LIGHTS → Leads) ─► email + optional webhook
```

- **One settings store.** Dealer name, phone, service areas, CTA labels, quote URL, colors and fonts are entered once. Page copy uses placeholders such as `{dealer}` or `{phone}`, so changing a setting updates every page at once.
- **Page Composer = a whole page in one widget.** Each of the 12 templates is a stack of sections with approved default copy. In Elementor you edit any section, reorder sections, hide them, or hide them per device. Elementor only stores what you changed, so plugin updates can improve untouched defaults without overwriting your edits.
- **Section widgets.** Every section type is also its own widget (Hero, Card Grid, FAQ …) for building custom pages.
- **Content types.** Projects (gallery, before/after), Reviews (only shown with customer permission) and Leads (private quote requests) are WordPress post types under the X LIGHTS menu.
- **Approval gates.** Claims the client hasn't confirmed (license, financing, app links, specs, individual items) stay hidden on the live site and show a "pending" badge only inside Elementor.
- **Design tokens.** Five colors and two fonts in *Colors & Fonts* drive every widget, the header and the footer; all other shades are derived with contrast checks.
- **No external requests by default.** Fonts are self-hosted, the map loads only when a visitor clicks it, and there is no jQuery on the front end.

## 2. Install & first run

1. **Requirements:** WordPress 6.4+, PHP 7.4+ (8.1+ recommended), **Elementor** (free; Pro optional). Hello Elementor theme recommended.
2. **Plugins → Add New → Upload Plugin →** `xlights-elementor-widgets-1.3.0.zip` **→ Install → Activate.**
3. **Settings → Permalinks → "Post name"** (readable URLs like `/contact-us/`; the plugin reminds you if this is off).
4. **X LIGHTS → Settings:** at least *Dealer*, *Contact & CTAs* (phone) and *Leads & Form* (lead recipients). Leave approval switches off until the client confirms in writing.
5. **X LIGHTS → Page Setup:** tick the pages → *Create pages* (drafts by default). Options: make Home the front page, create the header menu, page template.
6. **Edit each page with Elementor**, review, **Publish**.
7. **Before launch:** configure SMTP and use *Send test email* (Leads & Form tab), add real projects/reviews, check **X LIGHTS → Overview** (client approval checklist). Server-specific steps (Nginx rule for private photos, DNS) are in `docs/DEPLOYMENT.md`.

Activation itself creates **no pages and publishes nothing**; it creates the private lead-photo folder, grants administrators lead access and stores default settings.

## 3. Where to find everything

| Where (wp-admin) | What you do there |
|---|---|
| **X LIGHTS → Overview** | Counts (Page Composer pages, published projects and reviews, new leads) with shortcuts, and the **client approval checklist** showing what is done and where to change it. |
| **X LIGHTS → Projects** | Add installations: photos, city/area, categories, featured, before/after. |
| **X LIGHTS → Reviews** | Add real reviews; nothing shows without *Customer permission confirmed*. |
| **X LIGHTS → Leads** | Quote requests: details, private photos, status, internal notes, resend email, **Export CSV**. |
| **X LIGHTS → Settings** | 9 tabs: Dealer · Contact & CTAs · Leads & Form · Credentials & Financing · Brand · **Colors & Fonts** · App & Social · Map & SEO · Header & Footer (full list in §4). |
| **X LIGHTS → Page Setup** | Create/replace the 12 pages (existing pages are only replaced if you tick *Replace*; old content is backed up). |
| **Pages → Edit with Elementor** | Page Composer: pick the template, edit sections, order/visibility, per-page styling. |
| **Elementor panel → "X LIGHTS Widgets"** | All 21 widgets for custom layouts. |
| **Appearance → Menus** | Menu locations **X LIGHTS Header** and **X LIGHTS Footer** (a sensible default menu is used if none is assigned). |
| **Settings → Permalinks** | Choose *Post name*. |
| **Elementor → Settings → Performance** | *Load Google Fonts Locally* — keeps Google fonts on your own server. |
| **wp-config.php** | Optional constants for SMTP, private photo folder, uninstall behaviour (§12). |

### Inside the Elementor editor (Page Composer)

| Panel | Contains |
|---|---|
| **Content → Page template** | Which of the 12 templates this page shows. |
| **Content → 01 · Hero, 02 · …** | One panel per section of the chosen template: headline, text, buttons, images, lists (repeaters: add, drag, duplicate, delete). Common to every section: **Section background** (Light / Light grey / Dark), **Vertical spacing**, and per-item **Publication** (Always show · Only when credentials are verified · Only when financing is approved · Pending client approval). |
| **Content → Sections — Visibility & Order** | Drag to reorder, switch sections off, hide on desktop/tablet/mobile. |
| **Style** | Overrides for this page only: Global Layout (width, spacing, gutter), Colors, Typography, Buttons, Cards, Images, Section Backgrounds, Animations & Hover. Leave empty to use *Settings → Colors & Fonts*. |
| **Advanced** | Standard Elementor options (margins, CSS classes, responsive). |

Tips: leave an image empty to keep the built-in photo/illustration; links accept `{page:contact}`-style tokens so they survive slug changes; `<em>…</em>` in a headline switches that phrase to the bold weight.

## 4. Settings reference

🔒 = needs written client approval before publishing (also listed on the Overview checklist).

### Settings → Dealer

Identity of this dealer website. Values are reused everywhere through tokens such as {dealer}, {market} and {area}.

| Setting | Default | What it does |
|---|---|---|
| **Dealer / location identifier** | socal | Short internal ID added to lead emails and exports, e.g. "socal" or "newjersey". |
| **Brand name** | X LIGHTS |  |
| **Dealer display name** | X LIGHTS Southern California |  |
| **Market / location name** | Southern California | Used by the {market} token. |
| **Primary local focus** | San Clemente and South Orange County | Used by the {area} token in headlines and copy. |
| **Service areas (one per line) 🔒** | San Clemente South Orange County | List only verified areas. Shown in the footer, service-area sections and LocalBusiness schema. |
| **Service-area note** | Not sure whether we serve your neighborhood? Get in touch… |  |
| **Legal business name 🔒** | — | Optional. Used in the footer copyright and schema when provided. |
| **Short company description** | {dealer} designs and installs permanent soffit, bistro, d… |  |
| **Corporate website** | https://www.xlightsled.com/ |  |

### Settings → Contact & CTAs

| Setting | Default | What it does |
|---|---|---|
| **Dealer phone** | 949-795-2206 |  |
| **Public email** | info@xlightsled.com |  |
| **Show public email on the site** | On |  |
| **Street address 🔒** | — |  |
| **City** | — |  |
| **State** | CA |  |
| **ZIP code** | — |  |
| **Publish the street address** | Off | Leave off for service-area businesses without a public office. |
| **Business hours** | — | Optional, one line per entry, e.g. "Mon–Fri: 8am – 6pm". |
| **Primary CTA label** | Get a Quote |  |
| **Secondary CTA label** | Schedule a Consultation |  |
| **Consultation / quote URL** | {page:contact}#quote | Where Get a Quote buttons point. Supports {page:contact}. Replace with a scheduling link if one is configured. |

### Settings → Leads & Form

| Setting | Default | What it does |
|---|---|---|
| **Lead notification recipients** | info@xlightsled.com | Comma-separated list. |
| **Email "From" name** | — |  |
| **Email "From" address** | — | Must be an address your SMTP provider is allowed to send from. Leave blank to use the WordPress default. |
| **Attach uploaded photos to the notification email** | Off | Off by default: emails contain secure, login-protected links instead. |
| **Send the customer a confirmation email** | Off |  |
| **Confirmation subject** | We received your request — {dealer} |  |
| **Confirmation message** | Thank you for contacting {dealer}. We have received your … |  |
| **On-screen success message** | Thank you — your request has been received. A member of o… |  |
| **Privacy Policy URL 🔒** | — | Leave blank to use the WordPress Privacy Policy page. |
| **Privacy acknowledgment text** | I agree to be contacted about this request and understand… |  |
| **Default state in the form** | CA |  |
| **Maximum photos per request** | 6 |  |
| **Maximum size per photo (MB)** | 10 |  |
| **Submissions allowed per visitor per window** | 5 |  |
| **Rate-limit window (minutes)** | 30 |  |
| **Trust proxy headers (CF-Connecting-IP / X-Forwarded-For) for rate limiting** | Off | Enable only when the site is behind Cloudflare or a trusted reverse proxy. |
| **Delete leads and their photos after (days) 🔒** | 0 | 0 keeps leads until deleted manually. Confirm the retention period with the client. |
| **Cloudflare Turnstile site key (optional)** | — | Adds a privacy-friendly bot check when both keys are set. |
| **Cloudflare Turnstile secret key** | — |  |
| **CRM webhook URL (optional)** | — | Receives a JSON copy of each lead (no photo files). HTTPS only. |
| **Webhook signing secret** | — | Used to sign the X-XLights-Signature header (HMAC-SHA256). |

### Settings → Credentials & Financing

Credential and financing content stays hidden on the live site until it is marked as approved. Editors still see it in Elementor with a "pending approval" badge.

| Setting | Default | What it does |
|---|---|---|
| **Licensing / insurance statement 🔒** | Locally owned, licensed and insured. |  |
| **License number 🔒** | — |  |
| **Credentials verified — publish licensing/insurance statements** | Off | Tick only after the dealer has confirmed the wording and license details. |
| **Financing approved — publish financing callouts** | Off | Never publish rates, monthly payments or approval guarantees unless supplied by the lender. |
| **Financing heading** | Financing is available |  |
| **Financing text** | Ask about financing options during your consultation. Our… |  |
| **Financing button label** | Ask About Financing |  |
| **Financing button URL** | {page:contact}#quote |  |
| **Financing partner name (optional) 🔒** | — |  |
| **Financing disclaimer 🔒** | Financing is subject to credit approval. Terms and availa… |  |

### Settings → Brand

Logos and favicon. Colors and fonts are under the Colors & Fonts tab.

| Setting | Default | What it does |
|---|---|---|
| **Logo (for light backgrounds) 🔒** | bundled | SVG or PNG. If empty, the official X LIGHTS logo bundled with the plugin is used. |
| **Logo (for dark backgrounds)** | bundled | White or light version. If empty, the bundled white X LIGHTS logo is used. |
| **Show the market name next to the logo** | On | Dealer lockup, e.g. the X LIGHTS logo followed by "Southern California". Confirm with X LIGHTS that dealers may use this lockup. |
| **Favicon** | — | Used only when no Site Icon is set under Appearance → Customize. |

### Settings → Colors & Fonts

One place for the look of every X LIGHTS widget, the header and the footer. Pick five colors; hover shades, tints, text-safe accents and on-dark variants are derived automatically and checked for readable contrast. Defaults follow the live X LIGHTS website. Individual widgets can still override these under Style in Elementor.

| Setting | Default | What it does |
|---|---|---|
| **Accent** | #3367FE | Links, active states, highlights and button hover. A darker shade is used automatically wherever the accent is small text. |
| **Headings & buttons** | #05070B | Headlines and primary buttons on light backgrounds (button text is white). |
| **Body text** | #1B2130 | Paragraphs and form fields. Secondary (muted) text is derived from it. |
| **Dark sections** | #131C2D | Dark (navy) sections, the footer, panels and dropdown menus. Text on it is white. |
| **Light grey sections** | #E9EAED | Sections set to "Light grey" and light panels. |
| **Heading font** | Barlow Condensed | Headlines, card titles and the footer menu. Google Fonts are loaded through Elementor; to keep them on your own server turn on Elementor → Settings → Performance → Load Google Fonts Locally. |
| **Body font** | Barlow | Paragraphs, navigation, buttons, labels and forms. |
| **Heading letter case** | UPPERCASE (X LIGHTS style) | Options: UPPERCASE (X LIGHTS style), As typed. |
| **Heading size (%)** | 100 | 100 = X LIGHTS default. Barlow Condensed is narrow; wider fonts usually look best at 80–90. |

### Settings → App & Social

Leave a field blank to hide it. Do not enter placeholder URLs.

| Setting | Default | What it does |
|---|---|---|
| **Apple App Store URL 🔒** | — |  |
| **Google Play URL 🔒** | — |  |
| **Google Reviews URL** | — | Link to the dealer's Google Business Profile reviews. |
| **Facebook** | — |  |
| **Instagram** | — |  |
| **YouTube** | — |  |
| **TikTok** | — |  |
| **LinkedIn** | — |  |
| **Pinterest** | — |  |
| **Houzz** | — |  |
| **Nextdoor** | — |  |

### Settings → Map & SEO

| Setting | Default | What it does |
|---|---|---|
| **Offer an interactive map in service-area sections** | On | The Google map loads only after the visitor clicks "Show map" (no third-party requests on page load). |
| **Map location query** | San Clemente, CA |  |
| **Custom map embed URL (optional)** | — | Paste a Google My Maps embed URL to show a drawn service-area boundary. |
| **Output LocalBusiness / Service structured data** | On |  |
| **Schema business type** | HomeAndConstructionBusiness | Options: HomeAndConstructionBusiness, Electrician, GeneralContractor, LocalBusiness. |
| **Price range (schema, optional)** | — |  |
| **Latitude (optional)** | — |  |
| **Longitude (optional)** | — |  |
| **Output meta descriptions when no SEO plugin is active** | On | Automatically disabled when Yoast SEO, Rank Math, SEOPress, All in One SEO or The SEO Framework is active. |
| **Show breadcrumbs in inner-page heroes** | On |  |

### Settings → Header & Footer

| Setting | Default | What it does |
|---|---|---|
| **Global header & footer** | Elementor Canvas pages (works with any theme) | "Every page" works best with the Hello Elementor theme. For other themes, list the theme header/footer selectors below so they are hidden. Options: Elementor Canvas pages (works with any theme), Every page (replace the theme header/footer), Off (Elementor Pro Theme Builder or manual widgets). |
| **Theme header/footer selectors to hide** | — |  |
| **Sticky header** | On |  |
| **Header overlaps the first hero (floats inside framed heroes, transparent over full-bleed ones)** | On |  |
| **Show phone number in the header** | On |  |
| **Footer note (optional)** | — | Small print below the footer links, e.g. an independent-dealer statement. |


## 5. The 12 page templates

| Page | URL | Sections (in default order) |
|---|---|---|
| **Home** | `/` | Premium Hero, Local Introduction, Permanent Soffit Lighting Feature, Hanging Bulb Bistro Lighting, Deck & Landscape Lighting, One Connected Lighting System, X LIGHTS App Showcase, Residential & Commercial Entry Points, Why Choose X LIGHTS, Financing, Project Gallery, Reviews, Service Area Preview, Final Consultation CTA |
| **About Us** | `/about-us/` | About Hero, Meet X LIGHTS, Local Operation, About the X LIGHTS Brand, Design-First Philosophy, Why Permanent Lighting Was Developed, Product & Technology Philosophy, Local Team Photos & Bios, Reasons Customers Choose X LIGHTS, Licensed & Insured Trust Section, Financing, Final Consultation CTA |
| **X LIGHTS App** | `/x-lights-app/` | App Hero, One Connected Lighting System, App Interface Showcase, Supported Lighting Categories, Save & Customize Preferences, Smart Scheduling & Timers, Seasonal Light Shows & Patterns, Remote Management, Shared Access, Color & Brightness Control, Custom Scenes & Patterns, Smart-Home Integration, App Download, Final Consultation CTA |
| **Reviews** | `/reviews/` | Reviews Hero, Trust Introduction, Featured Testimonials, Review Grid, Project Gallery, Local Review Attribution, Google Reviews Link, Final Consultation CTA |
| **Lighting Solutions** | `/lighting-solutions/` | Lighting Solutions Hero, System Overview, Permanent Soffit Lighting, Hanging Bulb Bistro Lighting, Deck Lighting, Landscape Lighting, One Connected Outdoor Lighting System, App Showcase, Benefits & Differentiators, Financing, Project Gallery, Final Consultation CTA |
| **Permanent Soffit Lighting** | `/lighting-solutions/permanent-soffit-lighting/` | Soffit Lighting Hero, Product Introduction, Daytime Appearance / Low-Profile Design, Architectural Everyday Lighting, Seasonal & Holiday Lighting, Color-Matched Track Benefits, Smart App Control, Key Product Features, Technical Specifications, Lifestyle & Home Examples, Day / Night Comparison, Why Permanent Installation Makes Sense, Financing, Project Gallery, FAQ, Final Consultation CTA |
| **Hanging Bulb Bistro Lighting** | `/lighting-solutions/bistro-lighting/` | Bistro Hero, Lifestyle Introduction, Premium Patio Atmosphere, Residential Applications, Restaurants & Outdoor Dining, Custom Installation Layouts, Warm Ambient Illumination, Special-Event Lighting, X LIGHTS Connected System, Project Gallery, FAQ, Bistro Product Details, Final Consultation CTA |
| **Deck Lighting** | `/lighting-solutions/deck-lighting/` | Deck Lighting Hero, Service Introduction, Deck Step Lighting, Railing & Walkway Lighting, Outdoor Safety & Usability, Subtle Architectural Illumination, Compatible Smart Control, Product Style Options, Project Gallery, FAQ, Final Consultation CTA |
| **Landscape Lighting** | `/lighting-solutions/landscape-lighting/` | Landscape Hero, Introduction, Tree & Architectural Accent Lighting, Garden-Bed Illumination, Pathway Lighting, Outdoor Feature Highlights, Lighting Design Benefits, Smart Color & Brightness Control, Product Options, Project Gallery, FAQ, Final Consultation CTA |
| **Commercial Lighting** | `/commercial-lighting/` | Commercial Hero, Why Commercial Exterior Lighting Matters, Restaurant Patio & Bistro Lighting, Permanent Architectural Lighting, Commercial Landscape & Pathway Lighting, Connected Lighting Management, Custom Design & Layout Process, Commercial Property Applications, Benefits of Professional Installation, Local Dealer Trust Section, Financing, Project Gallery, Commercial FAQ, Commercial Consultation Form |
| **Projects** | `/projects/` | Projects Hero, Portfolio Introduction, Filters & Project Grid, Before / After Comparisons, Project Gallery, Final Consultation CTA |
| **Contact Us** | `/contact-us/` | Contact Hero, Consultation Introduction, Quote Request Form, Phone & Email, Local Service Area Map, Why Provide Photos & Address, Financing, Local Dealer Trust Statement, Final Consultation CTA |

## 6. Widgets (Elementor panel → "X LIGHTS Widgets")

- **Page Composer** — a complete page from one of the 12 templates (recommended for the standard pages).
- **Header**, **Footer** — for Elementor Pro Theme Builder or manual placement (Settings → Header & Footer → mode *Off*).
- **Section widgets (18):** Hero, Intro / Editorial Text, Split Image & Content, Card Grid, Connected System Diagram, App Showcase (tabs), Projects Gallery, Before / After Comparison, Customer Reviews, Financing Callout, FAQ Accordion, Service Area & Map, Contact Information, Quote Request Form, CTA Banner, Local Team, Technical Specifications, Local Trust Band. Each has the same content fields as its Page Composer section, plus *Use as the page H1* and *Section ID (anchor)*.

Card Grid styles: Icon cards · Image cards · Large photo cards · Numbered steps · Stacked numbered cards · Minimal list · Bento · Icon strip. The Hero has an optional **Quick estimate card** (lighting type + ZIP → opens the quote form prefilled; on by default on Home).

## 7. Placeholders (tokens)

Type these in any text, heading, link or button field:

| Token | Becomes |
|---|---|
| `{brand}` · `{dealer}` · `{market}` · `{area}` | Brand name · dealer name · market · primary area (Settings → Dealer) |
| `{phone}` · `{tel}` · `{email}` · `{mailto}` | Phone · `tel:` link · email · `mailto:` link |
| `{cta}` · `{cta2}` · `{quote_url}` | Primary/secondary CTA labels · quote/consultation URL |
| `{page:contact}` (any template key) | URL of that plugin page, e.g. `{page:soffit}` |
| `{google_reviews}` · `{year}` | Google Reviews URL · current year |

## 8. Shortcodes (for non-Elementor pages)

| Shortcode | Output |
|---|---|
| `[xlights_page template="home"]` | A full page template with default content |
| `[xlights_quote_form]` | The quote form (`variant`, `heading` attributes) |
| `[xlights_header]` · `[xlights_footer]` | Header / footer |
| `[xlights_phone]` | Click-to-call phone link |

## 9. Projects, reviews and leads

**Projects** (X LIGHTS → Projects): title, main photo (featured image), gallery (drag to order; alt text in the Media Library), **city/area only — never an address**, completion date, *Featured*, **Lighting categories** and **Property type** (sidebar), optional *Before & after* pair. They feed the Projects gallery (filters + lightbox, works without JavaScript), Home "Featured projects", each solution page (filtered) and the Before/After section. Each project also gets its own page under `/projects/<name>/`.

**Reviews** (X LIGHTS → Reviews): customer display name, exact review text, rating (only if given), source + link, date, city, related project, attribution (*local* or *brand-level*, labeled on the site), featured. **Never shown without "Customer permission confirmed".** Reviews sections hide themselves when nothing is approved.

**Leads** (X LIGHTS → Leads): every quote request is saved even if email fails. Status: New → Contacted → Consultation scheduled → Quote sent → Won / Closed – not proceeding / Spam. Internal notes, click-to-call/email, open address in Maps, private photos (only users with lead access can open them), notification status with *Resend*, **Export CSV**, automatic deletion after the retention period (Settings → Leads & Form).

## 10. The quote form — what happens on submit

1. Three steps: **Project** (property type, lighting interests, message) → **Property** (address, city, ZIP, photos) → **Details** (name, phone, email, privacy consent). Errors appear on the step where they belong; without JavaScript it is one complete form.
2. Protection: nonce + time token, honeypot, minimum fill time, rate limit per visitor, duplicate guard, optional Cloudflare Turnstile.
3. Photos: type and size checked, re-encoded to strip EXIF/GPS, stored in a private folder (outside the Media Library).
4. The lead is saved → notification email to the recipients (photos as login-protected links unless attachments are enabled) → optional customer confirmation → optional signed JSON webhook to a CRM.
5. The visitor sees the success message; the site never claims the email was delivered.

## 11. What stays hidden until approved

| Content | Unlocks when |
|---|---|
| Licensed/insured statements, license number | Settings → Credentials & Financing → *Credentials verified* |
| Financing callouts | *Financing approved* + approved wording |
| App Store / Google Play badges | Store URLs entered (App & Social) |
| "Read our Google reviews" links | Google Reviews URL entered |
| Individual cards/claims marked *Pending client approval* | Set that item's *Publication* to *Always show* |
| Technical specification rows | Row status set to *Verified* |
| Reviews | *Customer permission confirmed* on each review |
| Team, projects, before/after | Real entries exist |

## 12. Developer & ops reference

**WP-CLI**

| Command | Purpose |
|---|---|
| `wp xlights status` | The 12 pages (path, ID, status, Page Composer) and the private lead-folder check |
| `wp xlights setup [--pages=home,about] [--publish] [--front-page] [--menu] [--overwrite=keys] [--template=canvas\|header_footer]` | Create pages |
| `wp xlights settings get [key]` · `wp xlights settings set <key> <value>` | Read/write settings (secrets masked, unknown keys rejected) |
| `wp xlights test-email <to>` | Send a test notification |
| `wp xlights purge-leads [--dry-run]` | Apply the retention period now |

**wp-config.php constants:** `XLIGHTS_SMTP_HOST`, `XLIGHTS_SMTP_PORT`, `XLIGHTS_SMTP_USER`, `XLIGHTS_SMTP_PASS`, `XLIGHTS_SMTP_SECURE` (SMTP without a plugin) · `XLIGHTS_LEADS_DIR` (store lead photos outside the web root) · `XLIGHTS_UNINSTALL_DELETE_DATA` (true = uninstall also deletes projects, reviews, leads and settings; default keeps them).

**Filters/actions:** `xlights/settings`, `xlights/tokens`, `xlights/components`, `xlights/page_template/sections`, `xlights/section_template`, `xlights/elementor/widgets`, `xlights/nav/default_tree`, `xlights/projects/query_args`, `xlights/schema/business`, `xlights/store_badges`, `xlights/webhook/payload`, `xlights/turnstile/fail_open`, `xlights/uploads/allow_non_uploaded`, action `xlights/lead_created`.

**SEO built in:** one H1 per page, breadcrumbs (+ BreadcrumbList), LocalBusiness/Service/FAQPage structured data, meta-description fallback that switches off when Yoast, Rank Math, SEOPress, AIOSEO or The SEO Framework is active.

## 13. Troubleshooting

| Symptom | Fix |
|---|---|
| A settings change doesn't show | Purge your page/CDN cache; if it still looks old, Elementor → Tools → *Clear Files & Data*. |
| URLs look like `?page_id=12` | Settings → Permalinks → *Post name*. |
| Lead emails don't arrive | Configure SMTP (plugin or `XLIGHTS_SMTP_*`), set a From address your provider allows, *Send test email*. Leads are saved regardless. |
| Red notice "Customer photos are publicly accessible" | The server ignores .htaccess (Nginx): add the rule from DEPLOYMENT.md §7 or define `XLIGHTS_LEADS_DIR`. |
| Requests to fonts.googleapis.com | Elementor's own default fonts or a Google font you chose: enable *Load Google Fonts Locally* (Elementor → Settings → Performance). |
| Header/footer missing or doubled | Settings → Header & Footer → mode (Canvas pages / Every page / Off) and, for *Every page* with non-Hello themes, the selectors to hide. |
| Content shows "pending" in the editor but not live | Working as designed — confirm with the client, then unlock (§11). |

## 14. Related documents (repository `docs/`)

INSTALLATION.md (step by step) · EDITOR-GUIDE.md (editing in Elementor) · DEPLOYMENT.md (hosting, SMTP, Nginx, DNS) · DEALER-ONBOARDING.md (setting up another dealer) · CLIENT-APPROVALS.md (what the client must confirm) · ARCHITECTURE.md (code structure) · TESTING.md (test results).
