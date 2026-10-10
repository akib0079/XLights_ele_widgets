# Bundled photos — sources, edits and what to confirm

**TL;DR:** 21 photos from the client's image ZIP are bundled with the plugin (1.4.0) and used as page defaults instead of illustrations. All were stripped of metadata (incl. GPS), visible house numbers were removed, and each ships as compressed WebP with smaller copies for phones (2.2 MB total, ~70 KB per image on a phone). Before launch the client should confirm usage rights, and ideally send full-resolution originals for the six images marked *low-res*.

## How they were processed

| Step | Detail |
|---|---|
| Privacy | All EXIF/XMP/ICC metadata removed (12 originals carried GPS coordinates). House numbers on four photos replaced with neighbouring wall texture (not blurred, so no smudge). Photos showing licence plates, garage interiors or wiring were not used. |
| Format | WebP, quality 80, longest side ≤ 1600 px (2400 px for the one large original). Copies at 640 px and 1024 px wide where the original is larger, served via `srcset`, so phones download the small file. |
| Files | `xlights-elementor-widgets/assets/images/photos/<name>.webp` (+ `-640` / `-1024`). Templates reference them with `Defaults::photo( '<name>' )`. Originals are **not** in the repository. |
| Alt text | Describes what is visible only; no locations, customer names or "our project" claims. |

## The photos

| File | Client original | Size | Used on | Notes |
|---|---|---|---|---|
| roofline-warm-white-modern | 434142D9-….png | 1515×1038 | Home hero, Soffit comparison, App | House number removed |
| roofline-holiday-color-modern | 8271B1E2-….png | 1515×1038 | Soffit (seasonal, cards, comparison), App | Same home and angle as above, so it powers the Everyday ↔ Holiday slider; house number removed |
| roofline-two-story-modern | IMG_2459.PNG | 875×598 | Home, Soffit, Commercial | |
| steps-color-celebration | IMG_1199.PNG | 840×623 | Soffit card, App card | House number removed |
| entry-steps-night | IMG_7436.heic | 2400×1800 | Landscape hero, Home card, Solutions | Real field photo; GPS removed; faint house number removed |
| deck-steps-desert | IMG_7473.PNG | 908×577 | Deck hero, Solutions | |
| floating-steps | IMG_1198.PNG | 567×425 | Deck, Solutions card | **Low-res** (cropped from a portrait screenshot) |
| deck-stairs-dot-lights | IMG_1976.PNG | 573×417 | Deck, Home card | **Low-res** |
| patio-pool-firepit | IMG_2187.PNG | 783×669 | Deck | |
| pergola-lounge | IMG_3282.PNG | 1170×766 | Commercial hero | |
| pool-patio-dusk | IMG_3283.PNG | 1170×763 | Solutions hero | |
| pergola-bar | IMG_3284.PNG | 1170×762 | Commercial, Bistro, Home card | |
| firepit-lounge-string-lights | IMG_3425.PNG | 1169×833 | Bistro hero, Home | |
| pergola-string-lights | IMG_7574.HEIC | 836×627 | Bistro | Real field photo; GPS removed (IMG_9981 is a duplicate) |
| backyard-string-lights | IMG_7475.PNG | 731×548 | Bistro, Solutions card | **Low-res** |
| deck-string-lights | IMG_4207.HEIC | 836×627 | Bistro | Real field photo; GPS removed |
| backyard-overview | IMG_1809.PNG | 800×655 | Solutions | |
| garden-steps-path-lights | IMG_1674.PNG | 516×411 | Landscape, Solutions card | **Low-res** |
| path-lights-walkway | IMG_9945.PNG | 734×550 | Landscape, Commercial | **Low-res** |
| tree-spotlight | dsc02129-14.jpg | 591×443 | Landscape | **Low-res** (cropped from portrait) |
| installer-roofline | A51F6C44-….png | 886×591 | About | Person in X LIGHTS shirt — confirm the image may be used |

The three images that shipped in earlier versions (`home-two-story-night.jpg`, `home-single-story-dusk.jpg`, `soffit-track-daytime.jpg`) are unchanged and still used where they fit.

**Not used:** the five flyers (text-heavy, not photos), wiring/detail close-ups (IMG_2060, IMG_3467, IMG_3472, IMG_1091), IMG_2061 (licence plates and house number), IMG_8508 (garage interior), IMG_0128 / IMG_0305 / IMG_3372–3374 / IMG_8509 / remaining dsc… product close-ups (daylight or detail shots that read poorly at section size). They can still be uploaded to the Media Library and picked in Elementor.

## Confirm with the client

1. **Usage rights.** Several images look like professional marketing renderings rather than photos of this dealer's installs (very clean, staged scenes). Confirm they come from X LIGHTS corporate marketing or a source the dealer may use. The site never captions them as local projects; real installs belong in **X LIGHTS → Projects**.
2. **People.** The installer photo on About shows a person; confirm consent/rights.
3. **Higher-resolution originals** for the six *low-res* images (they came as phone screenshots). They look fine at 1× but soft on high-density screens and when shown large.
4. **More holiday / color roofline photos.** There is one holiday photo, so it appears in three places on the Soffit page. Two or three more would remove the repetition.

## Replacing a photo

Elementor → the page → **Page Composer** → section → **Image** → choose from the Media Library. A Media Library image always wins over the bundled default and gets WordPress's own responsive sizes. Card rows saved before 1.4.0 that still carry their original title pick up the new bundled photo automatically; a picked image is never replaced.
