# X LIGHTS — Elementor Widget System

A production WordPress plugin that builds complete X LIGHTS dealer websites with Elementor: one **Page Composer** widget renders any of the 12 sitemap pages, backed by reusable section components, a central dealer-settings store, projects and reviews content types, and a secure quote/lead pipeline.

First deployment: **X LIGHTS Southern California** (`socal.xlightsled.com`). The plugin is location-independent, so a future dealer (e.g. `newjersey.xlightsled.com`) is a settings change, not a code change.

> The corporate Wix site (`www.xlightsled.com`) and its dealer-ordering backend are not touched. This plugin powers separate WordPress marketing sites on subdomains.

## Repository layout

```text
.
├── xlights-elementor-widgets/    ← the WordPress plugin (this folder is what gets zipped)
│   ├── xlights-elementor-widgets.php   bootstrap, requirement checks
│   ├── uninstall.php                   safe uninstall (content kept unless opted in)
│   ├── readme.txt                      WordPress.org-format readme
│   ├── includes/
│   │   ├── Plugin.php                  wires all modules
│   │   ├── Settings/                   dealer settings store + admin screen
│   │   ├── Helpers/                    tokens, approval gates, HTML, icons, illustrations
│   │   ├── Pages/                      12 page-template registry + shared defaults
│   │   ├── Sections/                   component base, registry, renderer, 18 components
│   │   ├── Elementor/                  integration, controls builder, style controls, widgets
│   │   ├── PostTypes/                  projects, reviews, private leads
│   │   ├── Forms/                      quote handler, uploads, rate limiter, mailer, webhook, file access
│   │   ├── Frontend/                   assets, header/footer, breadcrumbs, shortcodes, REST gallery
│   │   ├── SEO/                        JSON-LD schema, meta description fallback
│   │   ├── Admin/                      overview/checklist, meta boxes, leads, page setup, notices
│   │   ├── Setup/                      activation, retention cron, page setup routine
│   │   └── CLI/                        `wp xlights …` commands
│   ├── templates/
│   │   ├── pages/                      section sequence + default copy for each of the 12 pages
│   │   ├── sections/                   markup for each component
│   │   ├── partials/                   quote form
│   │   └── email/                      lead notification + confirmation emails
│   └── assets/                         CSS, JS, Barlow fonts (OFL), official logo, default client imagery
├── docs/                         installation, deployment, onboarding, editor guide, approvals, testing
├── tests/                        smoke, security and browser test scripts
└── bin/build-zip.sh              produces dist/xlights-elementor-widgets-<version>.zip
```

## Quick start

1. `bin/build-zip.sh` → upload `dist/xlights-elementor-widgets-<version>.zip` in **Plugins → Add New → Upload**.
2. Activate **Elementor** (free is enough) and **X LIGHTS Elementor Widgets**.
3. **X LIGHTS → Settings**: confirm dealer name, phone, lead recipients, service areas.
4. **X LIGHTS → Page Setup**: create the 12 pages (drafts by default), set Home as the front page, create the header menu.
5. Open any page with Elementor → the **X LIGHTS — Page Composer** widget is already configured for that page.
6. Add projects and reviews under **X LIGHTS → Projects / Reviews**.

Full guides: [Installation](docs/INSTALLATION.md) · [Deployment & DNS](docs/DEPLOYMENT.md) · [Editor guide](docs/EDITOR-GUIDE.md) · [New dealer onboarding](docs/DEALER-ONBOARDING.md) · [Client approvals](docs/CLIENT-APPROVALS.md) · [Architecture](docs/ARCHITECTURE.md) · [Testing report](docs/TESTING.md)

## What's included

| Area | Highlights |
|---|---|
| Elementor | "X LIGHTS Widgets" category · Page Composer (12 templates, per-section panels, drag-to-reorder + device visibility) · 18 standalone section widgets · Header & Footer widgets · native Style controls (typography, colors incl. Elementor globals, borders, shadows, backgrounds, responsive spacing) |
| Pages | Home, About, X LIGHTS App, Reviews, Lighting Solutions, Permanent Soffit, Bistro, Deck, Landscape, Commercial, Projects, Contact — all with default copy from the client content plan |
| Content | Projects CPT (categories, residential/commercial, gallery, before/after, featured, order) · Reviews CPT (permission-gated, brand-level labeling) |
| Leads | Validated quote form (residential/commercial, multi-photo upload) · nonce + honeypot + time trap + rate limit + duplicate guard (+ optional Turnstile) · private storage, EXIF stripped · HTML email with login-protected photo links · admin lead manager, CSV export, retention · optional signed webhook |
| Trust & accuracy | Unverified claims (licensing, financing, app links, specs, bistro capabilities) stay hidden on the live site until approved, but stay visible in Elementor with a "pending" badge |
| SEO | One H1 per page, semantic headings, breadcrumbs, LocalBusiness/Service/FAQPage/BreadcrumbList JSON-LD, meta fallbacks, Yoast/Rank Math aware |
| Performance | Assets load only where used · no jQuery on the front end · no carousel/animation libraries · responsive images · click-to-load map · self-hosted 38 KB variable font |
| Accessibility | Keyboard dropdowns, focus-trapped mobile drawer, ARIA tabs, native `<dialog>` lightbox, labeled form errors, reduced-motion support |

## Requirements

WordPress 6.4+ · PHP 7.4+ · Elementor 3.20+ (tested with Elementor 4.0.8 on WordPress 7.1.3, PHP 8.3). Elementor Pro is optional.

## License

GPL-2.0-or-later. Barlow and Barlow Condensed are licensed under the SIL Open Font License 1.1 (`assets/fonts/OFL-Barlow.txt`). The X LIGHTS logo is the client's trademark, bundled for X LIGHTS dealer sites only.
