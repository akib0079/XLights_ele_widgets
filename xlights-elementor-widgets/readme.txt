=== X LIGHTS Elementor Widgets ===
Contributors: avixdigital
Tags: elementor, widgets, lighting, dealer, lead form
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Elementor page system for X LIGHTS dealer websites: one Page Composer widget for 12 pages, reusable sections, projects, reviews and a secure quote form.

== Description ==

X LIGHTS Elementor Widgets builds complete X LIGHTS dealer marketing sites (permanent architectural and seasonal lighting) with the free Elementor plugin.

* **Page Composer** widget: choose one of 12 page templates (Home, About Us, X LIGHTS App, Reviews, Lighting Solutions, Permanent Soffit, Hanging Bulb Bistro, Deck, Landscape, Commercial, Projects, Contact) and edit every section, reorder sections, and hide them per device.
* **18 section widgets** (Hero, Card Grid, App Showcase, Projects Gallery, Before/After, Reviews, FAQ, Quote Form …) plus Header and Footer widgets, all in the "X LIGHTS Widgets" category.
* **Dealer settings**: name, market, service areas, phone, lead recipients, credentials, financing, app and social links. Nothing is hard-coded in page content, so the same plugin serves any dealer.
* **Colors & Fonts** in one place: five brand colors (every other shade derived with contrast checks) and heading/body fonts, applied to every widget, the header and the footer.
* **Approval gates**: unverified claims (licensing, financing, app links, product specs) stay hidden on the live site and show a "pending" badge in the editor.
* **Projects and Reviews** content types with category filters, a lightbox gallery, before/after comparisons and permission-gated reviews.
* **Quote form**: residential/commercial, multi-photo upload, nonce + honeypot + time trap + rate limiting + duplicate guard (optional Cloudflare Turnstile), private EXIF-stripped photo storage, HTML email notifications, lead manager with CSV export and retention.
* **SEO**: one H1 per page, breadcrumbs, LocalBusiness/Service/FAQPage/BreadcrumbList structured data, meta description fallback (defers to Yoast, Rank Math and similar).
* **Accessibility and performance**: WCAG 2.2 AA tested, keyboard-friendly navigation, no jQuery on the front end, no external CDNs, self-hosted variable font.

Elementor Pro and ACF are not required.

== Installation ==

1. Install and activate Elementor.
2. Upload the plugin ZIP under Plugins → Add New → Upload Plugin and activate it.
3. Fill in X LIGHTS → Settings (dealer details, phone, lead recipients, service areas).
4. Run X LIGHTS → Page Setup to create the 12 pages as drafts.
5. Edit any page with Elementor, review, and publish.

For SMTP, private lead storage on Nginx and subdomain/DNS setup, see docs/DEPLOYMENT.md in the source repository.

== Frequently Asked Questions ==

= Does it change my existing pages? =

No. Page Setup only creates missing pages. An existing page is replaced only if you tick "Replace" for it, and its previous Elementor content is backed up first.

= Where are customer photos stored? =

In wp-content/uploads/xlights-leads/ (denied to web requests by .htaccess/web.config), or in any folder outside the web root that you set with the XLIGHTS_LEADS_DIR constant. They never enter the Media Library and can only be opened by logged-in users with lead access. On servers that ignore .htaccess (Nginx), the plugin detects the exposure and warns in wp-admin.

= Are lead emails guaranteed to arrive? =

The plugin records whether the mail server accepted each notification and never claims delivery. Configure SMTP (constants or an SMTP plugin) and use "Send test email" to verify. Leads are always saved, even if email fails.

= Does uninstalling delete my projects, reviews and leads? =

No, unless XLIGHTS_UNINSTALL_DELETE_DATA is defined as true in wp-config.php.

== Changelog ==

= 1.7.2 =
* Leads: every quote request is emailed to info@xlightsled.com. On update the address is added first to the saved recipient list if it is missing (other recipients are kept), and an empty recipient setting now falls back to it instead of the WordPress admin address.
* The customer confirmation email has Reply-To: info@xlightsled.com, so replies reach the lead inbox instead of the site's no-reply sender.

= 1.7.1 =
* Projects gallery: the Cottonmouth Dispensary project is added once as a featured commercial project (three photos in the Media Library, the dealer's own copy, no location). It appears on Projects (Commercial filter), in Featured projects and in the Commercial page's project grid, and has its own project page. It is a real project, not a sample: "Remove sample projects" never deletes it, and it is not added back if deleted or if a project with the same name already exists.

= 1.7.0 =
* Client feedback: snow photos removed everywhere (bundled photos, sample projects; on update, the snow sample project and its imported photos are removed unless something else uses them).
* About: the brand story uses the installer in the X LIGHTS cap and T-shirt (old-packaging installer photos removed); "No more ladders" shows the light itself in the low-profile track.
* Footer: "Become an X LIGHTS dealer" link to xlightsled.com/dealer (Settings → Header & Footer; blank hides it).
* Home: Everyday ↔ Holiday comparison slider; new Luxury Home Automation section (Control4, Lutron and Crestron; Bond partnership for outdoor patio systems).
* Solutions: "Control it all from your phone" is now a scrolling gallery of all six real app screens (new Gallery layout for the App Showcase).
* Reviews: three Google reviews of X LIGHTS in Canada added as brand-level reviews, text verbatim, labelled as from another X LIGHTS location. The Home heading now reads "What X LIGHTS customers say".
* Commercial: the Cottonmouth sign is the main image; the sections that used residential photos are replaced by a "Lighting for the whole property" overview; the case study follows the introduction.
* Landscape: photo for Modern bollard lights.
* Updates now reach saved pages: when a page is saved in Elementor, the editor stores every text field and card list. On update, stored values that are exactly an earlier version's default are cleared so the current default shows; anything an editor changed is kept, and the previous page data is backed up in post meta (`wp xlights refresh-defaults` runs it by hand).

= 1.6.1 =
* Commercial page: new featured project case study — Cottonmouth Dispensary (client copy, project facts, takeaway and three photos that open in the lightbox). Existing Commercial pages show it automatically after "Permanent Architectural Lighting"; it can be hidden, moved or edited like any section.
* New "Project Case Study" section and standalone widget for future featured projects.

= 1.6.0 =
* Real content only: the drawn app mock-ups and illustrations are gone. App sections show the official X LIGHTS app screenshots (color, patterns, music, timers, controllers, family management); the service-area map uses a photo.
* Official X LIGHTS app icon in the "one connected system" hub (Hub icon field) and next to the store badges.
* App Store and Google Play badges (official artwork) link to the X LIGHTS LED app by default. Sites saved with empty store links get them filled on update; links a dealer entered are kept.
* Bond Bridge smart-home section (official copy) on the App and Soffit pages: Control4, Savant, Crestron, Alexa, Google Home and more.
* Deck and Landscape collections follow the official X LIGHTS product sheets, with photos; new "All fixtures are" strip (RGBW, IP67, quick connect, premium build, expandable, low voltage). Soffit and Bistro product details from the official site.
* Every call to action now has a background photo, including the footer band.
* 29 new photos from the client (metadata removed, compressed, 640/1024 px copies). Explorer sections use the sharper photos.
* Default project gallery: on sites with no projects, 12 editable projects (48 photos in the Media Library) are added in the background. Add or remove them in X LIGHTS → Page Setup, or with `wp xlights samples [--remove]`.
* Editor: switching a section off in "Sections — Visibility & Order" also hides its settings panel; switching it back on brings it back.
* Image cards without a photo show a product icon instead of a drawn illustration.

= 1.5.0 =
* Layout system: Split sections gain an Editorial layout (large photo with an overlapping text card) and a Cinematic layout (full-width inset photo with a glass text panel).
* New Feature Explorer section and widget: numbered, keyboard-accessible tabs beside a large photo that cross-fades between features; chips on phones; a plain list without JavaScript.
* Pages recomposed so no page shows two side-by-side sections in a row: Home (editorial soffit feature + bento of bistro, commercial, deck and landscape), Solutions (cinematic signature section instead of four repeated splits), Soffit, Bistro, Commercial and About (editorial and cinematic sections), Deck and Landscape (feature explorers instead of three splits each), App (removed six splits that repeated the app showcase tabs).
* Fix: the hover underline on card "Explore" links spanned the whole card and ran under the arrow; it now hugs the label. Text links underline the label only.
* Existing pages pick up the new layouts automatically; edits to removed sections are no longer shown (their content now lives in the new sections).

= 1.4.0 =
* Real client photos replace the illustrations on every service page (21 photos, metadata and GPS removed, house numbers removed, compressed WebP with 640/1024 px copies served through srcset). See docs/PHOTOS.md.
* Soffit page: Everyday ↔ Holiday comparison slider using two aligned photos of the same home.
* Hover and focus: motion only for mouse/trackpad (no stuck hover on touch), keyboard focus gets the same feedback, whole overlay cards are clickable, "lights on" glow on card photos, photos brighten as they wipe in, button sheen and press, dropdowns open/close with a short intent delay.
* Overlay cards: stronger scrim so text stays readable over bright photos; whole-card focus ring now visible (it was clipped).
* New: mobile Call / Get a Quote bar (Settings → Header & Footer), shown after the first screen and hidden over quote forms, the footer, the menu and while typing.
* Theme compatibility: widgets are shielded from Hello Elementor resets and Elementor Site Settings → Theme Style link/button colors (fixes an invisible secondary button on hover, pink dropdown toggles and dark-blue links on photos).
* Pages saved before this update pick up the new card photos automatically; images picked in Elementor are never replaced.

= 1.3.0 =
* New Settings → Colors & Fonts tab: five brand colors and the heading/body fonts for every widget, the header and the footer, with a live preview and a reset to the X LIGHTS defaults.
* Every other shade (text-safe accent, accent on dark, tints, hover text, lighter/darker navy, muted text) is derived automatically with WCAG contrast checks; saving an unreadable combination shows a warning.
* Fonts: bundled Barlow (default), device fonts, Elementor Site Settings → Global Fonts, any font from Elementor's list (loaded through Elementor, so "Load Google Fonts Locally" applies), or a family the theme already loads. Heading letter case and heading size settings.
* The stylesheet no longer hard-codes any brand color outside its token block.
* Fix: the compact 68 px header height on tablets and phones now applies.
* Older free-text font settings are migrated to the new format automatically.
* Fix: the hero quick-estimate card now reaches the quote form when WordPress uses plain permalinks (?page_id=…); an admin notice recommends "Post name" permalinks.

= 1.2.0 =
* Quote form in three steps (Project, Property, Details) with a progress stepper, per-step validation and focus management; without JavaScript it stays one complete form.
* Contact page booking card: photo panel beside the stepped form.
* Floating header: a rounded white card that sits inside the framed hero and follows on scroll.
* Optional quick-estimate card in photo heroes (lighting type + ZIP) that opens step 1 of the quote form prefilled; on by default on Home.
* New card style "Stacked numbered cards" (used on Lighting Solutions): large numbered cards that stack as you scroll.
* FAQ rows redesigned as rounded rows with a rotating plus and an animated open/close.
* Footer redesigned as an inset navy panel with icon contact rows and a centered legal line.
* Motion: images wipe in as they enter, a short opening sequence on the first hero, sliding nav underline, smooth in-page anchors that land below the header. All motion is off under "reduce motion".

= 1.1.0 =
* Design system aligned with the live X LIGHTS website: official logo with a dealer lockup, black/white, navy and slate surfaces, signal-blue accent, Barlow and Barlow Condensed typography, inset hero frame, rectangular buttons.
* Colors still on the 1.0.0 defaults are migrated automatically; customized colors are kept.
* New hero "Frame" option (inset or full bleed).

= 1.0.0 =
* Initial release.
