# Architecture

**TL;DR:** one component library feeds three entry points: the **Page Composer** widget, the **standalone section widgets**, and the `[xlights_page]` shortcode. Copy and section order live in plain PHP page-definition files; business facts live in one settings option; everything renders server-side with no JS dependency for content.

```text
               ┌──────────────────────────────────────────────┐
 Settings ───▶ │ Tokens  ({dealer} {phone} {page:contact} …)  │
 (1 option)    └──────────────────────────────────────────────┘
                                   │
 templates/pages/<key>.php ──▶ Page_Registry ──▶ Section_Renderer ──▶ Component::prepare()
   (section list + defaults)       │                    │                 │
                                   │                    │                 ▼
 Elementor settings ───────────────┘                    │      templates/sections/<type>.php
 (only non-default values)                              │
                                                        ▼
                                Context gates (credentials / financing / app_links /
                                google_reviews / pending) → hidden live, badged in editor
```

## Modules (`includes/`)

| Namespace | Responsibility |
|---|---|
| `Plugin` | Boots modules on `plugins_loaded`; Elementor integration only when Elementor ≥ 3.20 is active |
| `Settings` | Schema-driven settings (`xlights_settings` option), sanitization per field type, admin screen with tabs and test email |
| `Helpers` | `Tokens` (placeholder replacement), `Context` (editor detection, approval gates, badges), `Html` (escaping/allow-lists, responsive images), `Icons` (inline SVG), `Illustrations` (SVG scene fallbacks) |
| `Pages` | `Page_Registry` (12 templates: key, slug, parent, section definitions, page ↔ post ID map in `xlights_pages`), `Defaults` (shared section defaults) |
| `Sections` | `Component` base + 19 components, `Component_Registry`, `Section_Renderer` (ordering, visibility, wrappers, single-H1), `Render_Context` |
| `Elementor` | `Integration` (category, widget registration, editor assets), `Controls_Builder` (component schema → Elementor controls), `Style_Controls` (CSS-variable-based style tab), `Widgets/*` |
| `PostTypes` | `xlights_project` (+ `xlights_project_category`, `xlights_property_type`), `xlights_review`, `xlights_lead` (private, capability-gated) |
| `Forms` | `Quote_Form_Handler`, `Upload_Handler`, `Rate_Limiter`, `Mailer`, `Webhook`, `File_Access` |
| `Frontend` | `Assets` (conditional enqueue, font preload, CSS vars), `Header_Footer`, `Breadcrumbs`, `Store_Badges`, `Social_Links`, `Shortcodes`, `Projects_Ajax` (REST), `Project_Single` |
| `SEO` | `Schema` (LocalBusiness, Service, FAQPage, BreadcrumbList), `Meta` (description fallback; skipped when Yoast, Rank Math, SEOPress, AIOSEO or The SEO Framework is active) |
| `Admin` | Overview + approval checklist, meta boxes, leads list/detail/CSV, Page Setup screen, notices |
| `Setup` | Activation/upgrade/deactivation, daily retention cron, `Page_Setup` |
| `CLI` | `wp xlights status | setup | settings | test-email | purge-leads` |

## Component model

A component declares a **field schema** once; that schema drives the Elementor controls, defaults, sanitization and the template:

```php
class Cta extends Component {
    public function type()  { return 'cta'; }
    public function label() { return __( 'CTA Banner', … ); }
    protected function define_fields() {
        return array_merge(
            array( 'style' => self::select( __( 'Style', … ), array( 'panel' => …, 'image' => … ), 'panel' ) ),
            self::head_fields( false ),                       // eyebrow / heading / text
            self::button_fields( 'primary', __( 'Primary button', … ) ),
            array( 'gate' => self::gate_field() )             // approval gate
        );
    }
    public function prepare( array $data, Render_Context $ctx ) { … return $data; /* or null to skip */ }
}
```

Field factories: `text`, `textarea`, `rich`, `url`, `select`, `toggle`, `media`, `heading`, plus composites `head_fields`, `button_fields`, `visual_fields`, `gate_field`, `icon_field`. Repeaters and sliders are declared as plain arrays (`'type' => 'repeater'` with `fields`, `'type' => 'slider'`).

### Page Composer control IDs

Controls are namespaced `{template}__{section}__{field}` so one widget holds all 12 templates without collisions, and Elementor's `condition` shows only the current template's panels. Elementor saves only values that differ from defaults, so stored data stays small and **default copy updates flow to existing pages** unless the editor changed that field.

Order and visibility are a per-template repeater (`{template}__order`); `Section_Renderer::merge_order()` appends sections added in later plugin versions so existing pages never lose new sections.

### Rendering rules

- Exactly one H1: the first hero renders `<h1>`; every other section heading is `<h2>`, cards `<h3>`.
- `prepare()` returning `null` drops the section (empty gallery, no permitted reviews, gated content on the live site).
- In the editor, gated content renders with a pending badge; `editor_note` hints render only in the editor.
- Images: Media Library IDs → `wp_get_attachment_image()` (srcset/sizes, lazy except the hero, which gets `fetchpriority="high"`). Bundled defaults ship with fixed alt text.

## Data model

| Store | Key | Notes |
|---|---|---|
| Option | `xlights_settings` | All dealer settings (autoloaded, cached per request) |
| Option | `xlights_pages` | template key → page ID |
| Option | `xlights_version` | Upgrade routine trigger |
| Post meta (pages) | `_elementor_data` | Page Composer settings |
| Post meta (pages) | `_xlights_backup_elementor_data` | Backup before a *Replace* in Page Setup |
| `xlights_project` meta | `_xl_city`, `_xl_featured`, `_xl_gallery`, `_xl_before`, `_xl_after`, `_xl_has_ba`, `_xl_completed` | Public, REST-readable via the plugin endpoint only |
| `xlights_review` meta | `_xl_rating`, `_xl_source`, `_xl_source_url`, `_xl_review_date`, `_xl_location`, `_xl_project`, `_xl_photo`, `_xl_attribution`, `_xl_featured`, `_xl_permission` | Rendered only when `_xl_permission = 1` |
| `xlights_lead` | post status `private`, meta `_xl_*` | Not public, not in REST, not searchable; capability `manage_xlights_leads` |
| Files | `uploads/xlights-leads/YYYY/MM/<32 hex>/<24 hex>.<ext>` or `XLIGHTS_LEADS_DIR` | Random names, re-encoded, deny rules, streamed via `File_Access` |
| Transients | `xl_rl_*`, `xl_dup_*` … | Rate limit and duplicate guard |

## Endpoints

| Endpoint | Auth | Purpose |
|---|---|---|
| `POST admin-ajax.php?action=xlights_quote` | nonce + time token + honeypot + rate limit | Quote submission (AJAX) |
| `POST admin-post.php?action=xlights_quote` | same | No-JS fallback (PRG redirect with status) |
| `GET admin-ajax.php?action=xlights_quote_token` | public | Fresh nonce + time token for cached pages |
| `GET admin-post.php?action=xlights_lead_file&lead=&file=` | logged in + `manage_xlights_leads` (read-only GET; anonymous → login redirect) | Streams a lead photo |
| `GET /wp-json/xlights/v1/projects?filter=&page=` | public, read-only | Gallery "Load more" / filter |
| `admin-post.php?action=xlights_resend_lead` | capability + nonce | Resend a lead notification |

## Security model (quote form)

1. **Nonce** (refreshable) and **signed time token**: submissions under 3 s or with an invalid token are treated as bots.
2. **Honeypot** field: bots get a fake success and no lead is created (no signal to tune against).
3. **Rate limit**: 5 submissions per IP per 30 minutes by default (configurable). Proxy headers are trusted only when enabled.
4. **Duplicate guard**: identical payload fingerprint within a short window is absorbed.
5. **Optional Cloudflare Turnstile** (fails open by default if Cloudflare is unreachable; filter `xlights/turnstile/fail_open`).
6. **Validation**: server-side per field (required fields, email, phone, ZIP, allowed values for selects/checkboxes, length caps); HTML stripped from free text; all output escaped.
7. **Uploads**: count/size limits, extension + magic bytes + decode check (JPG/PNG/WEBP only), decompression-bomb guard, re-encode via WP image editor (drops EXIF/GPS), random names, `index.php` in every folder, private directory, never in the Media Library. A daily loopback self-check (`Upload_Handler::is_publicly_accessible()`) warns in wp-admin and `wp xlights status` if the server serves the folder anyway (Nginx ignoring `.htaccess`).
8. **Responses** never include the lead ID, so a bot can't tell a real success from a trap.
9. **Mail**: header-injection-safe names, `Reply-To` = customer, photo **links** (login-protected) instead of attachments by default. Delivery result recorded honestly (`accepted` / `failed` + error) — the UI never claims delivery.
10. **CSV export**: nonce + capability, cells starting with `= + - @` are prefixed to prevent formula injection.
11. **Retention**: daily cron deletes leads and their files older than the configured days.

## Front end

- **CSS**: one stylesheet (`assets/css/xlights.css`) scoped under `.xl-root` / `.xl-header` / `.xl-footer`, driven by CSS custom properties (`--xl-accent`, `--xl-dark`, …) that Settings and the Style tab override. No theme CSS resets leak in or out.
- **JS**: `xlights.js` (nav dropdowns, mobile drawer with focus trap, ARIA tabs, FAQ, gallery filters with URL state, `<dialog>` lightbox, before/after slider, reveal-on-scroll, click-to-load map) and `xlights-form.js` (validation, previews, AJAX submit, token refresh). Vanilla, no jQuery, enqueued only on pages that use them.
- **Fonts**: self-hosted Barlow (400/500/600) and Barlow Condensed (300/500), latin subset, ~20 KB per face; only the faces a page uses are downloaded, and the two above-the-fold faces are preloaded. No external CDNs.
- **Brand tokens**: palette from the live X LIGHTS website — ink `#05070B`, navy `#131C2D`, slate `#39414E`, light grey `#E9EAED`, signal blue `#3367FE` (interactive only; darkened to `#2952CB` for small text on light backgrounds), periwinkle `#C8D7FE` on dark. Warm light `#FFD79A` is used only for light effects (soffit-dot line, glows). Dark surfaces that are not dark sections (footer, CTA panels, quote sidebar, bento cards) share one on-dark token set.
- **Global design settings**: the stylesheet uses no brand color literals outside its token block (enforced by `tests/browser/design.js`). Five colors from *Settings → Colors & Fonts* go through `Helpers\Color`, which derives every other token (text-safe accent, accent on dark, glow and tint channels, text on accent, lighter/darker navy, muted text) with WCAG contrast checks, and `Frontend\Assets::root_vars()` prints them on `:root,.xl-root` (nothing is printed for the defaults). Fonts resolve in `Frontend\Typography` (bundled, device stacks, Elementor Global Fonts, Elementor's font list — loaded through `Elementor\Frontend::enqueue_font()` so its local-fonts option applies — or a theme-loaded family). Elementor per-widget Style controls are more specific and still override.
- **Theme isolation**: Hello Elementor's reset (pink button hover/focus, label/figcaption styles, black `[tabindex]` focus outline) is neutralized inside `.xl-root` with `:where()` rules. Elementor *Site Settings → Theme Style* colors links and buttons with selectors like `.elementor-kit-N a:hover` (0,2,1), which outrank most widget states, so `bin/build-theme-shield.js` appends a generated block that repeats the color declarations of every plugin link/button rule prefixed with `html ` (one element more specific), in source order. Run it after changing link/button CSS; `node bin/build-theme-shield.js --check` fails when the block is stale.
- **Bundled photos**: client photos ship as `assets/images/photos/<name>.webp` with `-640`/`-1024` copies; `Html::image()` builds `srcset`/`sizes` for bundled files automatically and adds width/height. Elementor saves repeater rows whole (each gets an `_id`), so `Page_Composer::backfill_photos()` gives saved card rows that still carry their default title the template's new bundled photo when they have none. Processing notes in `docs/PHOTOS.md`.
- **Layouts**: `Components\Split::layout()` resolves *standard / offset / cinematic* (offset and cinematic only with a photo) and adds `xl-layout--*` to the section; `templates/sections/split.php` renders one shared content block in the chosen frame. `Components\Showcase` (Feature Explorer) renders every feature as a panel; `initShowcase()` turns them into WAI-ARIA tabs (automatic activation, inactive panels `inert` and `visibility: hidden`), otherwise they stay listed. `tests/browser/ux.js` fails if any page renders two `xl-layout--standard` splits back to back.
- **Interaction**: hover motion is gated by `(hover: hover) and (pointer: fine)`; keyboard focus mirrors it via `:has(:focus-visible)`; only transform/opacity animate (plus a one-off `filter` in the reveal). The mobile Call / Quote bar is rendered with the footer (`Header_Footer::sticky_cta()`) and shown by `initStickyCta()` only on phones after the first screen, hidden while a form or the footer intersects the viewport, while an input has focus, or while the drawer is open. Rationale and sources: `docs/UX-RESEARCH.md`.
- **Progressive enhancement**: all content is server-rendered; filters work without JS via `?xl_filter=`; the form works without JS via `admin-post.php`.

## Extension points

| Hook | Type | Signature | Use |
|---|---|---|---|
| `xlights/settings` | filter | `( array $settings )` | Override settings in code (e.g. per environment) |
| `xlights/tokens` | filter | `( array $map )` | Add placeholders (`'{license}' => '…'`) |
| `xlights/components` | filter | `( array $type_to_class )` | Register or replace a section component |
| `xlights/elementor/widgets` | filter | `( string[] $classes )` | Add/remove Elementor widgets (e.g. a widget for a custom component — extend `Section_Widget`) |
| `xlights/page_template/sections` | filter | `( array $sections, string $template_key )` | Add, remove or re-default sections of a page template |
| `xlights/section_template` | filter | `( string $file, Component $component )` | Override a section's markup from a theme/child plugin |
| `xlights/projects/query_args` | filter | `( array $query_args, array $args )` | Tune gallery queries |
| `xlights/nav/default_tree` | filter | `( array $tree )` | Header nav when no menu is assigned |
| `xlights/store_badges` | filter | `( string $html )` | Replace App Store / Google Play badge markup |
| `xlights/schema/business` | filter | `( array $node )` | Extend LocalBusiness JSON-LD |
| `xlights/webhook/payload` | filter | `( array $payload, int $lead_id )` | Shape the CRM webhook body |
| `xlights/turnstile/fail_open` | filter | `( bool $open )` | Reject submissions when Turnstile can't be verified |
| `xlights/uploads/allow_non_uploaded` | filter | `( bool $allow )` | Tests only — accept files not from `$_FILES` |
| `xlights/lead_created` | action | `( int $lead_id, array $lead )` | CRM sync, Slack alerts, analytics |

### Example: add a section to one page

```php
add_filter( 'xlights/page_template/sections', function ( $sections, $key ) {
    if ( 'deck' === $key ) {
        $sections['hoa'] = array(
            'type'     => 'intro',
            'label'    => 'HOA note',
            'defaults' => array( 'heading' => 'Planning around HOA guidelines', 'text' => '<p>…</p>' ),
        );
    }
    return $sections;
}, 10, 2 );
```

The new section appears in the Page Composer panel and in the order list (appended at the end) for that template.

## Constants (`wp-config.php`)

| Constant | Effect |
|---|---|
| `XLIGHTS_LEADS_DIR` | Store lead photos outside the web root |
| `XLIGHTS_SMTP_HOST`, `_PORT`, `_SECURE`, `_USER`, `_PASS` | Authenticated SMTP without a mail plugin |
| `XLIGHTS_UNINSTALL_DELETE_DATA` | Delete projects, reviews, leads and lead files on uninstall |
