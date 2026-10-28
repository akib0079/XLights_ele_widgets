# Deployment — `socal.xlightsled.com` alongside the Wix site

**TL;DR:** host WordPress anywhere. Add **one** DNS record for the `socal` subdomain at whoever hosts DNS for `xlightsled.com`. Issue SSL on the WordPress host. The Wix site, apex/`www` records, nameservers and email (MX) records stay exactly as they are.

> ⚠️ **Authorization required.** No DNS record, Wix setting or nameserver may be changed without explicit written approval from the domain owner (X LIGHTS). This guide describes the change; it doesn't authorize it. Rollback is deleting the one record you added.

## 1. Target architecture

```text
www.xlightsled.com     → Wix (unchanged) — corporate site + dealer ordering backend
xlightsled.com         → Wix (unchanged)
socal.xlightsled.com   → WordPress host (new) — this plugin
newjersey.xlightsled.com → (future) another WordPress install or multisite site
```

Each dealer subdomain is an independent WordPress site, so a problem on one never touches Wix or another dealer.

## 2. Pick hosting

| Option | Fit | Notes |
|---|---|---|
| **Managed WordPress** (Kinsta, WP Engine, Rocket.net, SiteGround GoGeek) | ✅ Recommended | Daily backups, staging, free Let's Encrypt, server cache, PHP 8.x. ~$30–$50/mo per site. |
| VPS + panel (Cloudways, RunCloud, GridPane) | ✅ Good for 5+ dealers | Cheaper per site at scale; you own patching. |
| Shared hosting | ⚠️ Avoid | Mail deliverability and performance are poor; uploads limits often too low. |

Server minimums: PHP 8.1+, `upload_max_filesize` ≥ 12M and `post_max_size` ≥ 70M (6 photos × 10 MB plus form data — or lower the plugin limits in *Leads & Form*), GD or Imagick, HTTPS.

## 3. Build on staging first

1. Build on the host's temporary URL or a staging subdomain.
2. **Settings → Reading → "Discourage search engines"** on, and HTTP basic-auth if the host offers it.
3. Install per [INSTALLATION.md](INSTALLATION.md), load content, get client sign-off.
4. Push staging → live with the host's tool, then do the launch checklist (§8).

## 4. DNS — add the subdomain (after approval)

### 4.1 Find out who hosts DNS

Before touching anything, check the nameservers:

```bash
dig +short NS xlightsled.com
```

| Nameservers look like | DNS is managed at | Where to add the record |
|---|---|---|
| `ns*.wixdns.net` | Wix | Wix dashboard → **Domains** → `xlightsled.com` → **Manage DNS records**. Adding a subdomain record doesn't change the Wix site, but it **is** a Wix settings change → needs explicit approval. |
| `*.domaincontrol.com` | GoDaddy | GoDaddy → DNS |
| `*.ns.cloudflare.com` | Cloudflare | Cloudflare → DNS (set the record to **DNS only** / grey cloud until SSL is issued) |
| Anything else | That registrar / DNS provider | Their DNS editor |

**Never change the nameservers.** Moving nameservers off Wix would disconnect the Wix site and the domain's email.

### 4.2 The record

Use whichever the host gives you:

| Type | Host / Name | Value | TTL |
|---|---|---|---|
| `A` | `socal` | host's IPv4 address | 3600 (300 during launch) |
| `AAAA` | `socal` | host's IPv6 address (only if provided) | same |
| — or — `CNAME` | `socal` | host's target hostname (e.g. `xyz.kinsta.cloud`) | same |

Do **not** edit or delete: `@`/apex, `www`, `MX`, existing `TXT` (SPF/DKIM/DMARC/verification), or any Wix-created records.

Check propagation: `dig +short socal.xlightsled.com` returns the host's IP or CNAME.

### 4.3 CAA

```bash
dig +short CAA xlightsled.com
```

Empty → nothing to do. If CAA records exist and don't list your host's CA (usually `letsencrypt.org`), certificate issuance will fail; ask for approval to add `0 issue "letsencrypt.org"` (adding a CAA value doesn't affect the existing certificates on Wix).

## 5. SSL

1. Once DNS resolves, enable the host's free Let's Encrypt certificate for `socal.xlightsled.com` (HTTP-01 validation).
2. Force HTTPS at the host.
3. **Settings → General**: WordPress Address and Site Address = `https://socal.xlightsled.com`.
4. If moving from a staging URL, search-replace the old URL (host tool or `wp search-replace 'https://staging-url' 'https://socal.xlightsled.com' --all-tables`), then **Elementor → Tools → Regenerate CSS & Data**.
5. Verify: `curl -I https://socal.xlightsled.com` → `200`, valid cert, no mixed-content warnings in the browser console.

## 6. Email delivery

Lead notifications are the business-critical path. `wp_mail()` from a web server with no authentication usually lands in spam or is rejected by DMARC, especially when the From address is `@xlightsled.com`.

**Recommended:** a transactional email provider (Postmark, Amazon SES, Mailgun, Brevo) or the domain's own mailbox provider (Google Workspace / Microsoft 365 SMTP relay).

| Setting | Value |
|---|---|
| From address | An address the provider is authorized to send for, e.g. `no-reply@socal.xlightsled.com` (subdomain keeps the corporate domain's reputation separate) |
| Reply-To | Set automatically: the customer's email on lead notifications; `info@xlightsled.com` on the customer's confirmation email |
| Recipients | `info@xlightsled.com` (Settings → Leads & Form). The plugin keeps it in the list: it is the default, an empty setting falls back to it, and updates add it back if it was removed (other addresses are kept) |
| DNS | The provider's DKIM `TXT`/`CNAME` and SPF `include:` records — **another DNS change that needs approval** |

Configure SMTP with an SMTP plugin (WP Mail SMTP, FluentSMTP) **or** constants in `wp-config.php` (secrets stay out of the database):

```php
define( 'XLIGHTS_SMTP_HOST', 'smtp.postmarkapp.com' );
define( 'XLIGHTS_SMTP_PORT', 587 );
define( 'XLIGHTS_SMTP_SECURE', 'tls' );   // tls | ssl | ''
define( 'XLIGHTS_SMTP_USER', '…' );
define( 'XLIGHTS_SMTP_PASS', '…' );
```

Then **X LIGHTS → Settings → Leads & Form → Send test email** and confirm the message arrives at `info@xlightsled.com` (inbox, not spam). Mail is never reported as delivered unless the transport accepted it; failed sends show as **Failed** in the Leads list *Email* column (with the error and a resend button on the lead), and the lead is always saved.

## 7. Server hardening specific to this plugin

### Lead photo privacy

Lead photos are saved to `wp-content/uploads/xlights-leads/` with `.htaccess` / `web.config` deny rules. **Nginx ignores `.htaccess`.** On Nginx, do one of:

```nginx
# Option A — block the folder
location ^~ /wp-content/uploads/xlights-leads/ { deny all; return 404; }
```

```php
// Option B (preferred) — store outside the web root, in wp-config.php
define( 'XLIGHTS_LEADS_DIR', '/home/site/private/xlights-leads' );
```

Verify: `wp xlights status` reports *Lead photos: … protected* (it makes a loopback request with a throw-away probe file). The plugin runs the same check daily on its admin screens and shows a red **Customer photos are publicly accessible** notice until it's fixed. Photos are otherwise viewable only through the login-protected links in the lead email and admin.

### Reverse proxy / CDN and rate limiting

The quote form rate-limits by IP. Behind Cloudflare or a load balancer every visitor can appear to share one IP. Turn on **Settings → Leads & Form → Trust proxy headers (CF-Connecting-IP / X-Forwarded-For)** only when the site is actually behind a proxy that sets `X-Forwarded-For`/`CF-Connecting-IP`; otherwise leave it off (the headers are spoofable).

### Spam

Honeypot + time trap + rate limit + duplicate guard are on by default. If spam still gets through, add Cloudflare Turnstile keys in *Leads & Form*.

### Cron

Lead retention runs on the daily WP-Cron event `xlights_daily`. On low-traffic sites, use a real cron:

```php
define( 'DISABLE_WP_CRON', true );
```
```cron
*/15 * * * * curl -s https://socal.xlightsled.com/wp-cron.php?doing_wp_cron >/dev/null
```

## 8. Caching & performance

- Full-page caching is safe: if a cached page's form nonce has expired, the form fetches a fresh one over AJAX and retries once, invisibly. Only visitors with JavaScript disabled depend on the cached nonce, so keep page-cache lifetime under ~10 hours (WordPress nonces last 12–24 h) or exclude `/contact-us/`.
- Don't cache `wp-admin/admin-ajax.php`, `wp-admin/admin-post.php` (hosts exclude these by default).
- `GET /wp-json/xlights/v1/projects` is public and cacheable.
- Elementor → Settings → Performance: enable *Improved CSS loading* / *Optimized DOM output* where available.
- Upload project photos at ≥ 2000 px on the long edge; WordPress generates the responsive sizes.

## 9. Launch checklist

- [ ] Client approvals reviewed ([CLIENT-APPROVALS.md](CLIENT-APPROVALS.md)); no gated content enabled without written confirmation
- [ ] Settings verified: phone, lead recipients, service areas, CTA labels
- [ ] Test email received at `info@xlightsled.com`
- [ ] Real end-to-end quote submission with a photo → lead in admin, email received, photo link opens only when logged in
- [ ] Lead photo folder returns 403/404 over the web
- [ ] Privacy policy page published and linked in the form
- [ ] All 12 pages reviewed on mobile and desktop, then published
- [ ] Home set as front page; header menu correct
- [ ] **Settings → Reading → Discourage search engines OFF**
- [ ] Permalinks saved; Elementor CSS regenerated
- [ ] HTTPS forced, valid certificate, no mixed content
- [ ] Google Search Console property for `https://socal.xlightsled.com` + sitemap (`/wp-sitemap.xml` or SEO plugin's sitemap)
- [ ] Backups confirmed at host
- [ ] Wix site checked — `www.xlightsled.com` loads and behaves exactly as before

## 10. Rollback

Delete the `socal` DNS record (or point it back). Wix was never changed, so there is nothing else to undo.
