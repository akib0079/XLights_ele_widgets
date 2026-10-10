# Hover & UX research — what we changed in 1.4.0 and why

**TL;DR:** premium-feeling interaction here means restraint. Motion runs only for a mouse or trackpad, animates only `transform`/`opacity`, gives keyboard users the same feedback, and never delays the hero. The signature moments are **photos that "switch on"** as they enter or on hover, an **Everyday ↔ Holiday slider** on the Soffit page, and a **mobile Call / Get a Quote bar**.

**Method & limits.** Desk research on Oct 10 2026 (web.dev, NN/g, Baymard, W3C/WCAG, MDN, Inclusive Components, Shopify performance docs, competitor sites via search results). The research environment could not open competitor sites directly, so the competitor notes below come from search summaries, not first-hand inspection of their hover behaviour. Vendor conversion claims were treated as unverified.

## What peers do (from search results)

- **Gemstone Lights:** ZIP-first quote form, free "AI preview" of your own home with lights, city "Visual Inspiration" galleries.
- **Jellyfish Lighting:** quote form asks city/state/ZIP on the homepage; persistent "Get Quote" in the header.
- **Oelo:** ballpark pricing page; quotes from submitted photos.
- **X LIGHTS corporate:** "Request a free quote", app, smart-home integrations. (A warranty is stated on the .ca site; the .com terms page contained placeholder text at the time, so warranty claims stay gated here — see `CLIENT-APPROVALS.md`.)
- Common thread: they sell the **day-to-night / everyday-to-holiday change** and start the quote with a **low-effort first step**. This site already has the ZIP + lighting type quick-estimate card and a three-step form; 1.4.0 adds the comparison slider.

## Implemented

| # | Pattern | Implementation | Why |
|---|---|---|---|
| 1 | **Hover only where hover exists** | Card lift, photo zoom and glow are wrapped in `@media (hover: hover) and (pointer: fine)`; the button sheen also requires `prefers-reduced-motion: no-preference` | On touch screens a tap leaves hover styles stuck. WCAG technique C39 (reduced motion) |
| 2 | **Compositor-only motion** | Only `transform` / `opacity` (plus a one-off `filter` on reveal); durations 0.25–0.8 s on the existing ease-out curve; no `transition: all` | web.dev: transform/opacity skip layout and paint; NN/g: UI feedback ~100–500 ms |
| 3 | **Keyboard parity** | `:has(:focus-visible)` gives cards the same lift, zoom, glow and underline as hover; whole-card focus ring drawn *inside* the card edge (it was clipped before) | WCAG 2.4.7 focus visible |
| 4 | **Whole card is the link** | The title link's stretched `::after` now covers overlay/bento cards fully (the photo area wasn't clickable) | Inclusive Components / Kitty Giraudel "stretched link" pattern; one clear link name for screen readers |
| 5 | **"Lights on" photos** | Linked card photos: a warm glow (screen-blended radial gradient) fades in on hover/focus. Section photos: the existing wipe-in now also brightens from dim to full ("switching on"). First section untouched | Brand-specific delight that reinforces the product; hero untouched so LCP is not delayed (Shopify/DebugBear: fading the LCP element costs 650–900 ms) |
| 6 | **Readable overlay cards** | Stronger scrim where the text sits (≈80–94 % navy under text, light at the top) | Text over bright photos (e.g. the bar shot) was below comfortable contrast |
| 7 | **Buttons** | Soft sheen sweep on primary/light buttons; `:active` press scales to 0.97 | Tactile feedback without moving layout |
| 8 | **Dropdown intent** | Opens after 60 ms, stays 220 ms after the pointer leaves; still opens on click/Enter and closes on Esc | Baymard: hover menus that close instantly or flicker are a common failure |
| 9 | **Everyday ↔ Holiday slider** | Soffit page comparison section now uses two aligned client photos of the same home; real `<input type="range">` with a label | The core selling point peers lean on; accessible by keyboard and screen reader |
| 10 | **Mobile Call / Get a Quote bar** | Phones only; appears after the first screen; hides while a quote form or the footer is on screen, while typing, and while the menu is open; 48 px targets; safe-area aware; `scroll-padding-bottom` so focused elements are not hidden (WCAG 2.4.11). Toggle: Settings → Header & Footer | Click-to-call matters for local services (Google/Ipsos 2013, self-reported). **Evidence for sticky bars specifically is weak** (no controlled home-services test found) — measure calls/quotes by source and keep it only if it helps |
| 11 | **Theme isolation** | Generated "theme-style shield" so Hello Elementor / Elementor Theme Style colors can't recolor widget links and buttons on hover | Fixes the invisible About CTA hover, pink dropdown toggles and dark-blue overlay links seen on staging |

## Recommended next (not built)

- **Trust next to the submit button:** Google rating, warranty, licensed & insured — once confirmed (all gated today). Baymard: reassurance beside the action raises perceived security.
- **A/B test the mobile bar** and the quick-estimate card with call tracking per source.
- **Photo-based quote** ("send a photo of your roofline") is already supported by the form's uploads; promote it in the hero copy if the dealer quotes from photos.

## Deliberately avoided

Custom cursors, cursor-follow glows and magnetic buttons; scroll-jacking and heavy parallax; auto-rotating hero sliders; outline-only primary buttons; fading in the hero; information shown only on hover; hover-only menus; a translucent sticky header; stacking a sticky bar, chat bubble and cookie banner at the bottom of the screen.

## Sources

- web.dev — [Animations guide](https://web.dev/articles/animations-guide), [Non-composited animations](https://web.dev/non-composited-animations), [INP](https://web.dev/articles/inp)
- MDN — [will-change](https://developer.mozilla.org/docs/Web/CSS/will-change)
- NN/g — [Animation duration](https://www.nngroup.com/articles/animation-duration/), [Timing exposing content](https://www.nngroup.com/articles/timing-exposing-content), [Sticky headers](https://www.nngroup.com/articles/sticky-headers/)
- Baymard — [Dropdown menu flickering](https://baymard.com/blog/dropdown-menu-flickering-issue), [Checkout form fields](https://baymard.com/blog/checkout-form-fields), [Perceived security](https://baymard.com/blog/perceived-security-of-payment-form)
- W3C — [1.4.13 Content on hover or focus](https://www.w3.org/WAI/WCAG22/Understanding/content-on-hover-or-focus), [2.3.3 Animation from interactions](https://w3c.github.io/wcag/understanding/animation-from-interactions.html)
- [Inclusive Components — Cards](https://inclusive-components.design/cards/), [Kitty Giraudel — Accessible cards](https://kittygiraudel.com/2022/04/02/accessible-cards/)
- [Shopify — don't hide the LCP image behind animations](https://shopify.dev/docs/storefronts/themes/best-practices/performance/dont-hide-lcp-image-behind-animations), [DebugBear — opacity animation and LCP](https://www.debugbear.com/blog/opacity-animation-poor-lcp)
- [CSS-IRL — detecting hover-capable devices](https://css-irl.info/detecting-hover-capable-devices/), [WebKit bug 209292](https://bugs.webkit.org/show_bug.cgi?id=209292)
- [Think with Google — click-to-call research (2013)](https://www.thinkwithgoogle.com/_qs/documents/1454/click-to-call_research-studies_hz2ytgv.pdf), [BrightLocal Local Consumer Review Survey 2025](https://www.brightlocal.com/research/local-consumer-review-survey-2025/)
- Peers: [Gemstone Lights](https://www.gemstonelights.com/), [Jellyfish Lighting](https://www.jellyfishlighting.com/), [Oelo pricing](https://www.oelo.com/oelo-lights-cost/), [Trimlight](https://trimlight.com/), [X LIGHTS](https://www.xlightsled.com/)
