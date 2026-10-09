=== X LIGHTS Elementor Widgets ===
Contributors: avixdigital
Tags: elementor, widgets, lighting, dealer, lead form
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Elementor page system for X LIGHTS dealer websites: one Page Composer widget for 12 pages, reusable sections, projects, reviews and a secure quote form.

== Description ==

X LIGHTS Elementor Widgets builds complete X LIGHTS dealer marketing sites (permanent architectural and seasonal lighting) with the free Elementor plugin.

* **Page Composer** widget: choose one of 12 page templates (Home, About Us, X LIGHTS App, Reviews, Lighting Solutions, Permanent Soffit, Hanging Bulb Bistro, Deck, Landscape, Commercial, Projects, Contact) and edit every section, reorder sections, and hide them per device.
* **18 section widgets** (Hero, Card Grid, App Showcase, Projects Gallery, Before/After, Reviews, FAQ, Quote Form …) plus Header and Footer widgets, all in the "X LIGHTS Widgets" category.
* **Dealer settings**: name, market, service areas, phone, lead recipients, credentials, financing, app and social links. Nothing is hard-coded in page content, so the same plugin serves any dealer.
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

= 1.1.0 =
* Design system aligned with the live X LIGHTS website: official logo with a dealer lockup, black/white, navy and slate surfaces, signal-blue accent, Barlow and Barlow Condensed typography, inset hero frame, rectangular buttons.
* Colors still on the 1.0.0 defaults are migrated automatically; customized colors are kept.
* New hero "Frame" option (inset or full bleed).

= 1.0.0 =
* Initial release.
