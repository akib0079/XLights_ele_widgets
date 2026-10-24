/*
 * 1.4.0/1.5.0 photos, layouts & interaction: bundled photos load with srcset and carry
 * no metadata, theme-style shield is current, hover motion only for fine
 * pointers, keyboard focus mirrors hover, whole overlay cards are clickable,
 * focus rings are not clipped, reduced motion disables the button sheen,
 * Everyday/Holiday comparison, mobile call & quote bar behaviour; 1.6.0: no
 * drawn mock-ups, real app screens and icon, store links, photo behind every CTA.
 *
 *   BASE_URL=http://localhost:8080 node tests/browser/ux.js
 */
const fs = require('fs');
const path = require('path');
const { execFileSync } = require('child_process');
const { BASE_URL, ok, launch, trackErrors, settle, done } = require('./lib');

const ROOT = path.join(__dirname, '..', '..');
const PHOTOS = path.join(ROOT, 'xlights-elementor-widgets', 'assets', 'images', 'photos');

/** RIFF chunk ids in a WebP file. */
function webpChunks(file) {
	const b = fs.readFileSync(file);
	const ids = [];
	for (let i = 12; i + 8 <= b.length; ) {
		const id = b.toString('ascii', i, i + 4);
		const size = b.readUInt32LE(i + 4);
		ids.push(id);
		i += 8 + size + (size % 2);
	}
	return ids;
}

(async () => {
	// Files: privacy and generated CSS.
	const files = fs.readdirSync(PHOTOS).filter((f) => f.endsWith('.webp'));
	ok(files.length >= 21, `bundled photos present (${files.length} files)`);
	const meta = files.filter((f) => webpChunks(path.join(PHOTOS, f)).some((id) => /^(EXIF|XMP |ICCP)$/.test(id)));
	ok(meta.length === 0, 'no EXIF/XMP/ICC metadata in bundled photos' + (meta.length ? ': ' + meta.join(', ') : ''));
	let shield = true;
	try {
		execFileSync(process.execPath, [path.join(ROOT, 'bin', 'build-theme-shield.js'), '--check'], { stdio: 'pipe' });
	} catch (e) {
		shield = false;
	}
	ok(shield, 'theme-style shield is up to date (node bin/build-theme-shield.js --check)');

	const browser = await launch();

	// Desktop, mouse.
	const desk = await browser.newContext({ viewport: { width: 1440, height: 900 } });
	const d = await desk.newPage();
	const errs = trackErrors(d);
	for (const p of ['/', '/lighting-solutions/', '/lighting-solutions/landscape-lighting/']) {
		await d.goto(BASE_URL + p, { waitUntil: 'networkidle' });
		const imgs = await d.evaluate(async () => {
			const list = [...document.querySelectorAll('.xl-root img')].filter((i) => i.src.includes('/assets/images/photos/'));
			for (const i of list) {
				i.loading = 'eager';
			}
			await Promise.all(list.map((i) => (i.complete ? null : new Promise((r) => { i.onload = i.onerror = r; setTimeout(r, 5000); }))));
			return list.map((i) => ({ src: i.currentSrc.split('/').pop(), w: i.naturalWidth, srcset: !!i.srcset, wh: !!(i.getAttribute('width') && i.getAttribute('height')) }));
		});
		ok(imgs.length > 0 && imgs.every((i) => i.w > 0), `${p}: ${imgs.length} bundled photos load`);
		ok(imgs.every((i) => i.wh), `${p}: bundled photos have width/height (no layout shift)`);
		ok(imgs.some((i) => i.srcset), `${p}: larger photos ship a srcset`);
	}

	await d.goto(BASE_URL + '/', { waitUntil: 'networkidle' });
	await settle(d);
	const card = d.locator('.xl-cards--bento .xl-card--linked, .xl-cards--overlay .xl-card--linked').first();
	await card.scrollIntoViewIfNeeded();
	const box = await card.boundingBox();
	const hit = await d.evaluate(([x, y]) => {
		const el = document.elementFromPoint(x, y);
		return el && el.closest('a') ? el.closest('a').className : '';
	}, [box.x + box.width / 2, box.y + 24]);
	ok(/xl-card__link/.test(hit), 'photo tile: the photo area is part of the card link');
	await d.mouse.move(box.x + box.width / 2, box.y + box.height / 2);
	await d.waitForTimeout(100);
	const hov = await card.evaluate((c) => ({
		glow: getComputedStyle(c.querySelector('.xl-card__media'), '::before').opacity,
		zoom: getComputedStyle(c.querySelector('.xl-card__media img')).transform,
	}));
	ok(hov.glow === '1' && hov.zoom !== 'none', 'mouse hover: lights-on glow + photo zoom');
	await d.mouse.move(2, 2);
	await d.waitForTimeout(50);
	await d.keyboard.press('Tab');
	await card.locator('.xl-card__link').focus();
	await d.keyboard.press('Shift+Tab');
	await d.keyboard.press('Tab');
	const foc = await card.evaluate((c) => {
		const a = c.querySelector('.xl-card__link');
		const after = getComputedStyle(a, '::after');
		return { fv: a.matches(':focus-visible'), glow: getComputedStyle(c.querySelector('.xl-card__media'), '::before').opacity, ring: after.outlineStyle, off: parseFloat(after.outlineOffset) };
	});
	ok(foc.fv && foc.glow === '1', 'keyboard focus mirrors hover (glow)');
	ok(foc.ring === 'solid' && foc.off < 0, 'photo tile focus ring is drawn inside the clipped card');

	// Image cards: focus ring not clipped either.
	await d.goto(BASE_URL + '/', { waitUntil: 'networkidle' });
	await settle(d);
	const imgCardRing = await d.evaluate(() => {
		const a = document.querySelector('.xl-cards--image .xl-card__link');
		if (!a) {
			return null;
		}
		a.focus();
		return parseFloat(getComputedStyle(a, '::after').outlineOffset);
	});
	ok(imgCardRing === null || imgCardRing <= 0, 'image card focus ring is drawn inside the card');

	// Hover underline hugs the "Explore" label (it used to span the whole card).
	await d.goto(BASE_URL + '/', { waitUntil: 'networkidle' });
	await settle(d);
	const more = d.locator('.xl-cards--bento .xl-card--linked').nth(1);
	await more.scrollIntoViewIfNeeded();
	const mb = await more.boundingBox();
	await d.mouse.move(mb.x + mb.width / 2, mb.y + mb.height / 2);
	const ul = await more.evaluate((c) => {
		const box = c.querySelector('.xl-card__more').getBoundingClientRect();
		const label = c.querySelector('.xl-card__more-label');
		const lb = label.getBoundingClientRect();
		return { more: box.width, card: c.getBoundingClientRect().width, label: lb.width, size: getComputedStyle(label).backgroundSize, arrowOutside: c.querySelector('.xl-card__more .xl-icon').getBoundingClientRect().left >= lb.right - 1 };
	});
	ok(ul.more < ul.card * 0.8 && ul.size.startsWith(ul.label > 0 ? '100%' : 'x') && ul.arrowOutside, `hover underline hugs the label, not the card or arrow (${Math.round(ul.more)} of ${Math.round(ul.card)} px)`);
	const textLink = await d.evaluate(() => {
		const a = document.querySelector('.xl-btn--text');
		const st = getComputedStyle(a);
		return { anchorBg: st.backgroundImage, spanBg: getComputedStyle(a.querySelector('span')).backgroundImage };
	});
	ok(textLink.anchorBg === 'none' && textLink.spanBg !== 'none', 'text links underline the label span only');

	// Layout rhythm: never two side-by-side splits in a row.
	for (const p of ['/', '/about-us/', '/x-lights-app/', '/lighting-solutions/', '/lighting-solutions/permanent-soffit-lighting/', '/lighting-solutions/bistro-lighting/', '/lighting-solutions/deck-lighting/', '/lighting-solutions/landscape-lighting/', '/commercial-lighting/']) {
		await d.goto(BASE_URL + p, { waitUntil: 'domcontentloaded' });
		const run = await d.evaluate(() => {
			let max = 0;
			let cur = 0;
			document.querySelectorAll('.xl-root--page > section').forEach((sec) => {
				cur = sec.classList.contains('xl-layout--standard') ? cur + 1 : 0;
				max = Math.max(max, cur);
			});
			return { max, layouts: [...document.querySelectorAll('.xl-root--page > section')].map((x) => (x.className.match(/xl-layout--(\w+)|xl-cards--(\w+)|xl-showcase-section/) || [''])[0]).filter(Boolean) };
		});
		ok(run.max <= 1, `${p}: no back-to-back side-by-side splits (${run.layouts.join(' ')})`);
	}

	// Cinematic + editorial layouts render with readable panels.
	await d.goto(BASE_URL + '/lighting-solutions/permanent-soffit-lighting/', { waitUntil: 'networkidle' });
	await settle(d);
	const lay = await d.evaluate(() => {
		const panel = document.querySelector('.xl-cine__panel');
		const li = panel && panel.querySelector('.xl-checklist li');
		return { cine: document.querySelectorAll('.xl-cine').length, offset: document.querySelectorAll('.xl-offset').length, li: li ? getComputedStyle(li).color : '', panelBg: panel ? getComputedStyle(panel).backgroundColor : '' };
	});
	ok(lay.cine >= 1 && lay.offset >= 1, `soffit: cinematic (${lay.cine}) and editorial (${lay.offset}) layouts`);
	ok(/^rgb\((2[3-5]\d), (2[3-5]\d), (2[3-5]\d)\)/.test(lay.li), 'cinematic panel checklist text is light on the dark panel (' + lay.li + ')');

	// Feature explorer: ARIA tabs, keyboard, cross-fade panels.
	await d.goto(BASE_URL + '/lighting-solutions/deck-lighting/', { waitUntil: 'networkidle' });
	await settle(d);
	const ex = await d.evaluate(() => {
		const w = document.querySelector('[data-xl-showcase]');
		const list = w.querySelector('[role="tablist"]');
		const tabs = [...w.querySelectorAll('[role="tab"]')];
		const panels = [...w.querySelectorAll('[role="tabpanel"]')];
		return { list: !!list && !list.hidden, label: list && list.getAttribute('aria-label'), tabs: tabs.length, panels: panels.length, sel: tabs.map((t) => t.getAttribute('aria-selected')).join(','), controls: tabs.every((t) => document.getElementById(t.getAttribute('aria-controls'))), vis: panels.map((p) => getComputedStyle(p).visibility).join(',') };
	});
	ok(ex.list && ex.label && ex.tabs === 3 && ex.panels === 3 && ex.controls, 'explorer: labelled tablist with 3 tabs controlling 3 panels');
	ok(ex.sel === 'true,false,false' && ex.vis === 'visible,hidden,hidden', 'explorer: first feature shown, others hidden');
	await d.focus('[data-xl-showcase] [role="tab"][aria-selected="true"]');
	await d.keyboard.press('ArrowDown');
	const ex2 = await d.evaluate(() => {
		const w = document.querySelector('[data-xl-showcase]');
		const tabs = [...w.querySelectorAll('[role="tab"]')];
		const panels = [...w.querySelectorAll('[role="tabpanel"]')];
		return { sel: tabs.map((t) => t.getAttribute('aria-selected')).join(','), focus: document.activeElement === tabs[1], vis: panels.map((p) => getComputedStyle(p).visibility).join(','), inert: panels.map((p) => p.inert).join(',') };
	});
	ok(ex2.sel === 'false,true,false' && ex2.focus && ex2.vis === 'hidden,visible,hidden' && ex2.inert === 'true,false,true', 'explorer: ArrowDown moves to the next feature (focus, panel, inert)');
	await d.keyboard.press('End');
	ok((await d.evaluate(() => [...document.querySelectorAll('[data-xl-showcase] [role="tab"]')].map((t) => t.getAttribute('aria-selected')).join(','))) === 'false,false,true', 'explorer: End jumps to the last feature');
	await d.click('[data-xl-showcase] [role="tab"] >> nth=0');
	ok((await d.evaluate(() => document.querySelector('[data-xl-showcase] [role="tabpanel"]').classList.contains('is-active'))), 'explorer: click selects a feature');

	// Without JavaScript every feature is listed (nothing hidden).
	const nojs = await browser.newContext({ viewport: { width: 1440, height: 900 }, javaScriptEnabled: false });
	const nj = await nojs.newPage();
	await nj.goto(BASE_URL + '/lighting-solutions/landscape-lighting/', { waitUntil: 'domcontentloaded' });
	const njs = await nj.evaluate(() => {
		const w = document.querySelector('[data-xl-showcase]');
		return { tabsHidden: w.querySelector('[data-xl-showcase-tabs]').hidden, visible: [...w.querySelectorAll('[data-xl-showcase-panel]')].filter((p) => getComputedStyle(p).visibility === 'visible' && getComputedStyle(p).opacity === '1').length };
	});
	ok(njs.tabsHidden && njs.visible === 3, 'explorer without JS: all 3 features listed, tab buttons hidden');
	await nojs.close();

	// Everyday / Holiday comparison on the soffit page.
	await d.goto(BASE_URL + '/lighting-solutions/permanent-soffit-lighting/', { waitUntil: 'networkidle' });
	const cmp = await d.evaluate(() => {
		const ba = document.querySelector('.xl-ba');
		if (!ba) {
			return null;
		}
		const imgs = [...ba.querySelectorAll('img')].map((i) => i.getAttribute('src').split('/').pop());
		const range = ba.querySelector('input[type="range"]');
		const label = range && document.querySelector('label[for="' + range.id + '"]');
		return { imgs, labelled: !!(label && label.textContent.trim()) };
	});
	ok(cmp && cmp.imgs.length === 2 && cmp.imgs.some((s) => /holiday/.test(s)) && cmp.imgs.some((s) => /warm-white/.test(s)), 'soffit: Everyday / Holiday comparison shows both photos');
	ok(cmp && cmp.labelled, 'soffit: comparison slider is a labelled range input');

	// Desktop never shows the mobile bar.
	await d.evaluate(() => window.scrollTo(0, 2400));
	await d.waitForTimeout(300);
	ok((await d.evaluate(() => getComputedStyle(document.querySelector('[data-xl-sticky-cta]')).display)) === 'none', 'desktop: no mobile call & quote bar');
	ok(errs.length === 0, 'desktop: no JS errors ' + errs.join(' | '));

	// Reduced motion: no button sheen.
	const rm = await browser.newContext({ viewport: { width: 1440, height: 900 }, reducedMotion: 'reduce' });
	const r = await rm.newPage();
	await r.goto(BASE_URL + '/', { waitUntil: 'networkidle' });
	const btn = r.locator('.xl-section--lead .xl-btn--primary').first();
	await btn.hover();
	await r.waitForTimeout(300);
	const sheen = await btn.evaluate((b) => getComputedStyle(b, '::after').transform);
	ok(/-\d/.test(sheen), 'reduced motion: button sheen stays parked');
	await rm.close();

	// Phone, touch.
	const mob = await browser.newContext({ viewport: { width: 390, height: 844 }, hasTouch: true, isMobile: true });
	const m = await mob.newPage();
	const merrs = trackErrors(m);
	const bar = () => m.evaluate(() => {
		const b = document.querySelector('[data-xl-sticky-cta]');
		return { shown: b.classList.contains('is-shown'), vis: getComputedStyle(b).visibility };
	});
	await m.goto(BASE_URL + '/lighting-solutions/', { waitUntil: 'networkidle' });
	let s = await bar();
	ok(!s.shown && s.vis === 'hidden', 'phone: bar hidden on the first screen');
	await m.evaluate(() => window.scrollTo(0, 2400));
	await m.waitForTimeout(700);
	s = await bar();
	ok(s.shown && s.vis === 'visible', 'phone: bar slides up after the first screen');
	const links = await m.evaluate(() => [...document.querySelectorAll('.xl-sticky-cta__btn')].map((a) => ({ href: a.getAttribute('href'), h: a.getBoundingClientRect().height })));
	ok(links.some((l) => /^tel:/.test(l.href)) && links.every((l) => l.h >= 44), 'phone: Call + Quote, targets ≥ 44 px');
	ok((await m.evaluate(() => document.documentElement.classList.contains('xl-sticky-cta-on'))), 'phone: scroll padding keeps focus clear of the bar');
	await m.evaluate(() => document.querySelector('.xl-footer').scrollIntoView());
	await m.waitForTimeout(700);
	ok(!(await bar()).shown, 'phone: bar steps aside at the footer');

	// Touch never gets stuck hover motion.
	await m.goto(BASE_URL + '/', { waitUntil: 'networkidle' });
	await settle(m);
	const media = m.locator('.xl-split .xl-media__frame, .xl-offset .xl-media__frame').first();
	await media.scrollIntoViewIfNeeded();
	await media.tap();
	await m.waitForTimeout(100);
	const tz = await media.evaluate((f) => getComputedStyle(f.querySelector('img')).transform);
	ok(tz === 'none', 'touch: tapping a photo does not leave a hover zoom');

	await m.goto(BASE_URL + '/lighting-solutions/deck-lighting/', { waitUntil: 'networkidle' });
	const order = await m.evaluate(() => {
		const w = document.querySelector('[data-xl-showcase]');
		const y = (el) => el.getBoundingClientRect().top;
		return y(w.querySelector('[data-xl-showcase-tabs]')) < y(w.querySelector('.xl-showcase__stage')) && y(w.querySelector('.xl-showcase__stage')) < y(w.querySelector('.xl-showcase__aside > .xl-actions'));
	});
	ok(order, 'phone: explorer chips, then the panel they control, then the button');

	await m.goto(BASE_URL + '/contact-us/', { waitUntil: 'networkidle' });
	await m.evaluate(() => window.scrollTo(0, 1000));
	await m.waitForTimeout(700);
	const formOnScreen = await m.evaluate(() => {
		const f = document.querySelector('.xl-root form');
		const rc = f.getBoundingClientRect();
		return rc.bottom > 0 && rc.top < innerHeight;
	});
	ok(formOnScreen && !(await bar()).shown, 'phone: bar hidden while the quote form is on screen');
	ok(merrs.length === 0, 'phone: no JS errors ' + merrs.join(' | '));
	await mob.close();

	// 1.6.0 content: real photos and app screens only, store badges with the app icon,
	// a photo behind every call to action.
	const pages = ['/', '/about-us/', '/x-lights-app/', '/reviews/', '/lighting-solutions/', '/commercial-lighting/', '/projects/', '/contact-us/',
		'/lighting-solutions/permanent-soffit-lighting/', '/lighting-solutions/bistro-lighting/', '/lighting-solutions/deck-lighting/', '/lighting-solutions/landscape-lighting/'];
	const cp = await desk.newPage();
	const drawn = [];
	const plainCta = [];
	for (const u of pages) {
		await cp.goto(BASE_URL + u, { waitUntil: 'domcontentloaded' });
		const r = await cp.evaluate(() => ({
			drawn: document.querySelectorAll('.xl-root .xl-illus, .xl-root .xl-phone--mock').length,
			plain: [...document.querySelectorAll('.xl-root .xl-cta')].filter((c) => !c.querySelector('.xl-cta__media img')).length,
		}));
		if (r.drawn) drawn.push(u + ' (' + r.drawn + ')');
		if (r.plain) plainCta.push(u + ' (' + r.plain + ')');
	}
	ok(drawn.length === 0, 'no drawn mock-ups or illustrations on any page' + (drawn.length ? ': ' + drawn.join(', ') : ''));
	ok(plainCta.length === 0, 'every call to action has a background photo' + (plainCta.length ? ': ' + plainCta.join(', ') : ''));

	await cp.goto(BASE_URL + '/', { waitUntil: 'networkidle' });
	const app = await cp.evaluate(() => {
		const hub = document.querySelector('.xl-connected__hub img');
		const stores = document.querySelector('.xl-stores');
		return {
			hub: hub ? /app-icon/.test(hub.currentSrc || hub.src) : false,
			icon: !!(stores && stores.querySelector('.xl-stores__app img')),
			links: stores ? [...stores.querySelectorAll('a.xl-store')].map((a) => a.href) : [],
		};
	});
	ok(app.hub, 'home: connected hub shows the X LIGHTS app icon');
	ok(app.icon, 'home: app icon next to the store badges');
	ok(app.links.some((h) => /apps\.apple\.com\/.*id6462840352/.test(h)) && app.links.some((h) => /play\.google\.com\/.*com\.goulyled\.xlights/.test(h)), 'home: App Store and Google Play badges link to the X LIGHTS LED app');
	const screens = await cp.evaluate(() => [...document.querySelectorAll('.xl-app-showcase img.xl-phone__device')].filter((i) => /\/images\/app\//.test(i.src)).length);
	ok(screens >= 3, `home: app showcase uses real app screenshots (${screens})`);

	// Commercial case study: facts, story, three photos that open in the lightbox.
	await cp.goto(BASE_URL + '/commercial-lighting/', { waitUntil: 'networkidle' });
	const cs = await cp.evaluate(() => {
		const s = document.querySelector('.xl-case');
		return s ? { facts: s.querySelectorAll('.xl-case__fact').length, photos: s.querySelectorAll('.xl-case__open img').length, alts: [...s.querySelectorAll('.xl-case__open img')].every((i) => i.alt.length > 10), text: /Cottonmouth/.test(s.textContent) } : null;
	});
	ok(cs && cs.text && cs.facts === 4 && cs.photos === 2 && cs.alts, 'commercial: Cottonmouth case study with facts and 2 described photos');
	await cp.locator('.xl-case__open').nth(1).scrollIntoViewIfNeeded();
	await cp.locator('.xl-case__open').nth(1).click();
	await cp.waitForTimeout(600);
	const lb = await cp.evaluate(() => { const d = document.querySelector('.xl-lightbox'); return d && d.open ? d.querySelector('.xl-lightbox__caption').textContent : ''; });
	ok(/2 of 2/.test(lb), 'commercial: case study photo opens in the lightbox at the right photo');
	await cp.keyboard.press('Escape');
	const comm = await cp.evaluate(() => {
		const imgs = [...document.querySelectorAll('.xl-root img')].map((i) => i.getAttribute('src') || '').filter((s) => /\/assets\/images\/photos\//.test(s));
		const hero = document.querySelector('.xl-section--hero img');
		return { hero: hero ? hero.getAttribute('src') : '', other: imgs.filter((s) => !/cottonmouth|\/brand\//.test(s)) };
	});
	ok(/cottonmouth-roofline/.test(comm.hero), 'commercial: the Cottonmouth sign is the main (hero) image');
	ok(comm.other.length === 0, 'commercial: no residential photos on the commercial page' + (comm.other.length ? ': ' + comm.other.join(', ') : ''));

	// Round 3 feedback: no snow or old-packaging installer photos anywhere; dealer link; Home slider,
	// automation and Google reviews; Solutions app screens gallery.
	const banned = [];
	for (const u of pages) {
		await cp.goto(BASE_URL + u, { waitUntil: 'domcontentloaded' });
		const hits = await cp.evaluate(() => [...document.querySelectorAll('img')].map((i) => (i.getAttribute('src') || '') + ' ' + (i.getAttribute('srcset') || '')).filter((s) => /soffit-winter|holiday-color-family|installer-controller|installer-roofline|controller-box/.test(s)).length);
		if (hits) banned.push(u + ' (' + hits + ')');
	}
	ok(banned.length === 0, 'no snow or old-packaging installer photos on any page' + (banned.length ? ': ' + banned.join(', ') : ''));
	await cp.goto(BASE_URL + '/', { waitUntil: 'networkidle' });
	const home = await cp.evaluate(() => {
		const dealer = document.querySelector('.xl-footer__dealer');
		const txt = document.body.textContent;
		const brand = [...document.querySelectorAll('.xl-review')].filter((r) => /Google review/.test(r.textContent));
		return {
			dealer: dealer ? dealer.href : '',
			blank: dealer ? dealer.target : '',
			slider: !!document.querySelector('.xl-ba input[type=range]'),
			automation: /Control4/.test(txt) && /Lutron/.test(txt) && /Crestron/.test(txt) && /Bond/.test(txt),
			google: brand.length,
			labelled: brand.every((r) => /another X LIGHTS location/.test(r.textContent) && /Canada/.test(r.textContent)),
		};
	});
	ok(home.dealer === 'https://www.xlightsled.com/dealer' && home.blank === '_blank', 'footer: dealer program link to xlightsled.com/dealer (new tab)');
	ok(home.slider, 'home: Everyday / Holiday comparison slider');
	ok(home.automation, 'home: luxury home automation (Control4, Lutron, Crestron, Bond)');
	ok(home.google >= 1 && home.labelled, `home: Google reviews shown and labelled as brand-level from Canada (${home.google})`);
	await cp.goto(BASE_URL + '/lighting-solutions/', { waitUntil: 'networkidle' });
	const gal = await cp.evaluate(() => {
		const g = document.querySelector('.xl-app-gallery');
		return g ? { phones: g.querySelectorAll('img.xl-phone__device').length, nav: !!g.querySelector('[data-xl-next]'), heading: /Control it all/i.test(g.textContent) } : null;
	});
	ok(gal && gal.heading && gal.phones >= 6 && gal.nav, 'solutions: "Control it all from your phone" is a scrolling gallery of the real app screens');
	await cp.goto(BASE_URL + '/projects/?xl_filter=commercial', { waitUntil: 'networkidle' });
	const cm = await cp.evaluate(() => [...document.querySelectorAll('#xl-projects-gallery .xl-projects__grid > li')].map((li) => li.textContent.replace(/\s+/g, ' ').trim()));
	ok(cm.some((t) => /Cottonmouth Dispensary/.test(t)), 'projects gallery: Cottonmouth Dispensary listed under Commercial');
	await cp.close();

	await browser.close();
	done();
})();
