# New dealer onboarding (e.g. `newjersey.xlightsled.com`)

**TL;DR:** same plugin, new WordPress site, new settings. No code changes. All dealer-specific facts (name, market, areas, phone, recipients, links, credentials) are settings; all page copy is written with tokens (`{dealer}`, `{market}`, `{area}`, `{phone}` …) and resolves per site. Budget **~1 day** of setup plus the client's content turnaround.

## Recommended topology

| Option | When | Trade-off |
|---|---|---|
| **One WordPress install per dealer** (recommended) | Up to ~10 dealers, dealers with different editors | Full isolation (leads, users, plugins, outages). Updates per site — script with WP-CLI or the host's bulk tools. |
| WordPress Multisite (subdomain) | Many dealers, central team edits all | One codebase to update; network-activate the plugin, settings are per site. Shared database and plugin set — a bad plugin update hits every dealer. |

Both are supported: settings, pages, projects, reviews and leads are stored per site.

## Checklist

### 1. Collect from the dealer (send [CLIENT-APPROVALS.md](CLIENT-APPROVALS.md) request template)

- Dealer display name, market name, primary area, service-area list
- Phone, public email, lead recipients
- Logo (if dealer-specific), license/insurance (if shown), financing (if offered)
- Their Google Reviews URL and social URLs
- Project photos (city only), reviews with permission, team

### 2. Provision

1. Hosting + WordPress + Hello Elementor + Elementor ([DEPLOYMENT.md](DEPLOYMENT.md)).
2. Upload and activate `xlights-elementor-widgets.zip`.
3. DNS record for the dealer subdomain — **after approval from the domain owner**.

### 3. Configure (≈15 minutes with WP-CLI)

```bash
wp xlights settings set dealer_id newjersey
wp xlights settings set dealer_name "X LIGHTS New Jersey"
wp xlights settings set market_name "New Jersey"
wp xlights settings set primary_area "Monmouth County"
wp xlights settings set service_areas "$(printf 'Monmouth County\nOcean County\nMiddlesex County')"
wp xlights settings set phone "000-000-0000"
wp xlights settings set lead_recipients "info@xlightsled.com, nj@example.com"
wp xlights settings set default_state NJ
wp xlights settings set google_reviews_url "https://g.page/r/…"
wp xlights setup --front-page --menu        # creates 12 draft pages
wp xlights test-email you@example.com
```

(Values above are placeholders — use what the dealer confirms. Each setting is also in **X LIGHTS → Settings**.)

Reset anything carried over from a cloned site — **credentials, financing and app/review links must be re-confirmed per dealer**:

```bash
wp xlights settings set trust_verified 0
wp xlights settings set financing_enabled 0
wp xlights settings set license_number ""
```

### 4. Content pass

- Open each page in Elementor. Default copy is region-neutral and token-based, so it already reads correctly for the new market. Adjust anything local (climate, architecture style, neighborhoods).
- Replace the default imagery with the dealer's own project photos.
- Add projects and reviews.
- Check the About page: dealer story and team are the dealer's own, never borrowed from another dealer or corporate.

### 5. Launch

Follow the launch checklist in [DEPLOYMENT.md](DEPLOYMENT.md#9-launch-checklist).

## Copying an existing dealer site as a starting point

Faster when the new dealer wants the same layout edits:

1. Clone the site at the host (or `wp db export` / `import` + files).
2. `wp search-replace 'https://socal.xlightsled.com' 'https://newjersey.xlightsled.com' --all-tables`
3. **Delete** carried-over dealer data: `wp post delete $(wp post list --post_type=xlights_lead,xlights_review,xlights_project --post_status=any --format=ids) --force` and the `xlights-leads` photo folder. Projects and reviews belong to the original dealer and must not appear on the new site.
4. Update every setting in §3, including the reset block.
5. Elementor → Tools → Regenerate CSS & Data.

## What's shared vs per dealer

| Shared (plugin code — update once) | Per dealer (settings / content) |
|---|---|
| Page layouts, components, design system | Names, areas, phone, email, recipients |
| Default copy (token-based) | Logo / colors (if dealer-specific) |
| Form security, lead handling, schema logic | Credentials, financing, links |
| Accessibility & performance work | Projects, reviews, team, photos, leads |

## Extending without forking

Need something dealer-specific in code? Use a small site-specific plugin with the hooks listed in [ARCHITECTURE.md](ARCHITECTURE.md#extension-points) (e.g. `xlights/tokens` for a new token, `xlights/page_template/sections` to add a section to one page, `xlights/schema/business` for extra schema). Never edit the plugin in place — updates would overwrite it.
