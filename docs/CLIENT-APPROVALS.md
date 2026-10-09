# Client approvals — what X LIGHTS must confirm before launch

**TL;DR:** the site can launch without any of these. Everything unconfirmed is either hidden on the live site or uses neutral copy, and shows a "pending" badge in Elementor. Each item below unlocks content once the client confirms it **in writing**. The live status of most items is on **X LIGHTS → Overview → Client approval checklist**.

## How gating works

| Mechanism | Behavior | How to unlock |
|---|---|---|
| **Credentials** gate | "Licensed and insured" cards (About, Commercial) and license number are hidden | Settings → Credentials & Financing → enter details, tick *Verified* |
| **Financing** gate | Financing callouts (Home, Soffit, Contact …) hidden | Settings → Credentials & Financing → *Financing enabled* + approved heading/text/partner/disclaimer |
| **App links** gate | App Store / Google Play badges hidden | Settings → App & Social → store URLs |
| **Google Reviews** gate | "Read our Google reviews" links hidden | Settings → App & Social → Google Reviews URL |
| **Pending** (per item) | Individual claims hidden (see table below) | In Elementor, change that item's *Publication* to *Always show* |
| **Spec rows** | Each technical-spec row stays hidden until its status is *Verified* | Elementor → Technical Specifications → row → status |
| **Permission** | Reviews never show without *Customer permission confirmed* | X LIGHTS → Reviews |
| **Empty content** | Projects/Reviews/Team sections with no entries hide or show a neutral message | Add the content |

## Approval list

### Business & legal

| # | Item | Where it goes | Status in code |
|---|---|---|---|
| 1 | Dealer legal / business name | Settings → Dealer; schema, footer | Optional; brand name used until provided |
| 2 | **Confirmed service areas** (cities/regions) | Settings → Dealer; Service Area section, schema `areaServed` | Defaults: *San Clemente*, *South Orange County* — **confirm** |
| 3 | Contractor license number + insurance wording | Settings → Credentials & Financing | Hidden (credentials gate) |
| 4 | Business address — publish or not | Settings → Contact | Hidden unless *Show address* on |
| 5 | Business hours | Settings → Contact | Blank → not shown |
| 6 | Privacy policy + lead retention period | WP Privacy page; Settings → Leads & Form | Form links WP privacy page when it exists; retention 0 = keep until deleted |
| 7 | Independent-dealer statement for the footer (if required by the dealer agreement) | Settings → Header & Footer → Footer note | Blank |

### Brand

| # | Item | Status |
|---|---|---|
| 8 | Official logo files (SVG preferred, dark + light versions) and favicon; approval of the dealer lockup (logo + "Southern California") | The PNG logo from the live website is bundled (plus a white version for dark backgrounds). The lockup can be turned off in Settings → Brand |
| 9 | Brand colors & fonts confirmation | Defaults follow the live X LIGHTS website: black/white, navy `#131C2D`, slate `#39414E`, light grey `#E9EAED`, signal blue `#3367FE`; Barlow / Barlow Condensed. The amber in the dealer flyers is not used as a UI color |
| 9a | **Phone number** | The live corporate contact page lists (949) 685-7173; this site uses 949-795-2206 from the content plan. Confirm which number belongs to the Southern California dealer |

### Product facts

| # | Item | Where | Status |
|---|---|---|---|
| 10 | **Soffit specs** — voltage, lights per run, diodes per light, IP rating, warranty, install time | Soffit → Technical Specifications | Pre-filled from the corporate site (48 V, up to 200 lights/run, 6 diodes/light) but **hidden** until each row is set to *Verified*. IP rating, warranty, install time blank. |
| 11 | Soffit feature claims: *individually programmable*, *weather-resistant*, *low-voltage* | Soffit → feature cards | Hidden (pending) |
| 12 | **Bistro** product: bulb specs, color capability, app control | Bistro → "special occasions" section + specs | Hidden (pending) |
| 13 | Deck & landscape product specs and warranty | Deck / Landscape → specs | Rows blank/hidden |
| 14 | **Bond Bridge / smart-home compatibility** per product | X LIGHTS App page | Shown as general capability with editor note — confirm or set to pending |
| 15 | Commercial terms: warranty, maintenance plans, timelines, minimums | Commercial FAQ | Neutral copy ("we'll walk you through …"), no specifics |
| 16 | Financing partner, terms and disclaimer | Settings → Credentials & Financing | Hidden (financing gate) |

### Links

| # | Item | Status |
|---|---|---|
| 17 | App Store URL / Google Play URL for the X LIGHTS app | Hidden badges |
| 18 | Google Business Profile reviews URL | Hidden links |
| 19 | Social profile URLs (Facebook, Instagram, YouTube …) — the dealer's own, not corporate, unless approved | Blank → icons not shown |

### Content & media

| # | Item | Status |
|---|---|---|
| 20 | **Approved project photos** with city/area only (no addresses, no visible house numbers), category, residential/commercial | 3 client photos bundled as defaults; Projects gallery empty until projects are added |
| 21 | Matching **before/after** pairs (same angle, day + night) | Before/After section hidden until a pair exists |
| 22 | **Real customer reviews** with written permission | None. No reviews are invented; sections hide or show a neutral message |
| 23 | Local team: names, roles, bios, photos | About → Local Team hidden until added |
| 24 | Dealer's own story / year established | About intro uses neutral copy; corporate founding story (Calgary, Laura Ericson) is not presented as the dealer's history |
| 25 | Current app screenshots | App page uses illustrated phone screens until provided |
| 26 | Product photos for solution cards | Illustrations until provided |
| 27 | Hi-res originals of the 3 bundled photos | Current files are 886 px wide (from the client upload); fine for cards, soft as full-width hero on large screens |

### Operations

| # | Item | Status |
|---|---|---|
| 28 | Lead recipients (default `info@xlightsled.com`) and who has admin access to leads | Settings → Leads & Form |
| 29 | Hosting account, DNS approval for `socal` record, SMTP provider | See [DEPLOYMENT.md](DEPLOYMENT.md) |
| 30 | Search Console / analytics ownership | Not installed by the plugin |

## Request template (copy into an email to the client)

> To finish the Southern California site we need written confirmation of: (1) service-area cities; (2) contractor license number and insurance wording, if you want it shown; (3) financing partner and approved wording, if offered; (4) the soffit, bistro, deck and landscape specs and warranty terms for the products you sell; (5) App Store / Google Play / Google Reviews links; (6) logo files; (7) project photos you're happy to publish, with the city for each; (8) any customer reviews you have permission to publish; (9) team names/photos for the About page. Anything we don't hear back on stays hidden — the site is safe to launch without it.
