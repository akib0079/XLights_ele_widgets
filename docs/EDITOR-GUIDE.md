# Editor guide (for the dealer's site editor)

**TL;DR:** every page is one **X LIGHTS — Page Composer** widget. Pick the page in *Page template*, edit text and images section by section in the left panel, drag sections to reorder, and use **Style** for colors/typography. Business details (phone, email, service areas, links) are edited once in **X LIGHTS → Settings**, not on each page.

## Editing a page

1. **Pages** → hover the page → **Edit with Elementor**.
2. Click anywhere on the page content to select the Page Composer widget.
3. Left panel, **Content** tab:

| Panel | What it does |
|---|---|
| **Page Configuration → Page template** | Which of the 12 page layouts this widget renders. Already set by Page Setup — normally leave it alone. |
| **Sections — Visibility & Order** | One row per section. **Drag** to reorder. Open a row to turn *Show section* off, or *Hide on desktop / tablet / mobile*. Hidden rows show "— hidden". |
| **01 · Hero**, **02 · …** | One panel per section with its headline, text, buttons, images and lists. Only the current template's sections are shown. |

4. **Update** (bottom-left) saves and publishes changes; use the arrow next to it for *Save Draft*.

Tips:

- Leave an image empty to keep the built-in default (photo or illustration).
- Lists (cards, FAQs, steps, specs) use repeaters: **+ Add Item**, drag to reorder, duplicate, delete.
- Links accept a full URL or a page token like `{page:contact}` so links keep working if a page slug changes.

## Brand look (what the design system does for you)

- **Headlines** are set in Barlow Condensed capitals. Wrap the phrase to stress in `<em>…</em>`: it switches from Light to Medium weight (no color change). Example: `Beautiful every night. <em>Ready for every celebration.</em>`
- **Hero frame**: each hero has a *Frame* option — *Inset with rounded corners* (default, like the X LIGHTS website) or *Full bleed*. Over an inset hero the header floats inside the frame as a white card; over a full-bleed hero it turns transparent. Both follow **X LIGHTS → Settings → Header & Footer → Header overlaps the first hero**.
- **Quick estimate card** (photo heroes): *Hero → Quick estimate card* adds a small card with a lighting-type dropdown and a ZIP field. It opens the quote form at step 1 with both answers filled in. You can change its title, text, button and a preselected lighting type. It is on by default on Home.
- **Quote form**: the form runs in three steps (*Project → Property → Details*). Visitors can go back without losing anything, and errors are shown on the step where they happen. The Contact page shows it as a booking card with a photo; change the photo under *Quote Form → Side panel photo*.
- **Card styles**: *Card Grid → Card style* includes **Stacked numbered cards** — large cards numbered 01, 02 … with the image on the right, which stack under the header as visitors scroll (used for the four solutions on Lighting Solutions). Best with 3–5 cards.
- **Motion**: sections fade up, images wipe in, and the first hero plays a short opening sequence. Turn motion or hover effects off per widget under *Style → Animations & Hover*; visitors who ask their device to reduce motion never see it.
- **Colors** follow the live X LIGHTS website: black/white, navy and light grey sections, and signal blue for links and active states. Warm light appears only as light (the dotted soffit line, glows) — not as text or button color.
- **Section backgrounds**: *Light*, *Light grey* or *Dark (navy)* per section.
- **Logo**: the official X LIGHTS logo with "Southern California" beside it. Upload SVG versions or turn the market label off under **X LIGHTS → Settings → Brand**.

## Placeholders (tokens)

Write these in any text field; they're replaced with the current values from **X LIGHTS → Settings**:

| Token | Becomes |
|---|---|
| `{brand}` | Brand name — *X LIGHTS* |
| `{dealer}` | Dealer name, e.g. *X LIGHTS Southern California* |
| `{market}` | Market name, e.g. *Southern California* |
| `{area}` | Primary area, e.g. *Orange County* |
| `{phone}` / `{email}` | Phone number / public email |
| `{cta}` / `{cta2}` | Primary / secondary CTA labels |
| `{year}` | Current year |
| `{tel}` / `{mailto}` | `tel:` / `mailto:` link (use in link fields) |
| `{quote_url}` | Quote form URL (default: Contact page `#quote`) |
| `{google_reviews}` | Google Reviews URL |
| `{page:key}` | URL of a dealer page — keys: `home`, `about`, `app`, `reviews`, `solutions`, `soffit`, `bistro`, `deck`, `landscape`, `commercial`, `projects`, `contact` |

Using tokens is what makes the same pages work for the next dealer without rewriting copy.

## "Pending" badges and hints — what you see in the editor but visitors don't

| You see | Meaning |
|---|---|
| **Eye-off badge** ("Hidden on the live site until …") | Content that needs client confirmation (licensing, financing, app store links, Google Reviews link, unverified specs). It's visible in the editor so you can prepare it; it stays off the live site until the setting it names is turned on/filled in. |
| **ⓘ hint box** | Guidance for editors (e.g. "Add real project photos under X LIGHTS → Projects"). Never shown to visitors. |
| An empty section on the live site | Sections with nothing to show (no projects yet, no reviews with permission, no team members) drop out automatically instead of showing a blank block. |

## Style

**Style** tab of the Page Composer (applies to this page; set the same values on each page or use Elementor's *Site Settings → Global Colors / Fonts*, which the color pickers accept):

- **Global Layout** — content width, section padding, side gutter, grid gap (all responsive).
- **Colors** — accent, dark (navy) / light / light-grey surfaces, headings, body, muted, text-on-dark.
- **Typography** — H1, H2, card titles, eyebrows, body.
- **Buttons** — radius, padding, primary/secondary colors.
- **Cards** — background, border, radius, shadow.

The **Advanced** tab (margins, custom CSS classes, motion, responsive) works as with any Elementor widget.

Brand-wide defaults (accent color, logo, fonts) are in **X LIGHTS → Settings → Brand** and apply to every page and the header/footer.

## Projects gallery

- Add projects in **X LIGHTS → Projects** — they appear automatically on Projects, Home ("Featured"), and each solution page (filtered by category).
- Location field: **city or neighborhood only** — never a homeowner's address.
- Mark the best 3–6 as **Featured**.
- A before **and** after photo puts the project in the Before & After section.
- Alt text: set it on each photo in the Media Library ("Two-story home with warm white soffit lighting at dusk").

## Reviews

- Only real customer reviews, pasted exactly as written, with **Customer permission confirmed** ticked.
- Only enter a star rating if the customer gave one.
- No reviews → the Reviews section on Home hides itself and the Reviews page shows an honest "reviews will appear here" message (editable; per section you can choose *hide* or *message*). Nothing fake is ever shown.

## Leads

**X LIGHTS → Leads** (administrators and anyone granted `manage_xlights_leads`).

- New submissions arrive as *New*; move them through *Contacted → Consultation scheduled → Quote sent → Won / Closed – not proceeding* (or *Spam*) and add internal notes.
- Photos open only for logged-in users with lead access.
- **Export CSV** for the CRM. Exports are protected against spreadsheet formula injection.
- *Email* column showing **Failed** means the notification didn't go out — the lead is safe; fix SMTP and use **Resend notification** on the lead.

## Standalone widgets

The **X LIGHTS Widgets** category also contains each section as its own widget (*X LIGHTS — Hero*, *— Card Grid*, *— FAQ Accordion*, *— Quote Request Form* …) plus *X LIGHTS — Header* / *— Footer*. Use them to build a one-off landing page in a regular Elementor layout. For the 12 core pages, stay with the Page Composer so the heading structure and schema remain correct.

## Don'ts

- Don't add a second H1. The hero heading is the page's only H1.
- Don't publish testimonials, star ratings, license numbers, financing terms, warranty terms or specs that the client hasn't confirmed in writing.
- Don't use photos of homes with visible house numbers or street signs.
- Don't present the corporate founding story (Calgary) as the California dealer's own history.
