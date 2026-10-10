/*
 * 1.4.0 photos & interaction layer: bundled photos load with srcset and carry
 * no metadata, theme-style shield is current, hover motion only for fine
 * pointers, keyboard focus mirrors hover, whole overlay cards are clickable,
 * focus rings are not clipped, reduced motion disables the button sheen,
 * Everyday/Holiday comparison, mobile call & quote bar behaviour.
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
	const card = d.locator('.xl-cards--overlay .xl-card--linked').first();
	await card.scrollIntoViewIfNeeded();
	const box = await card.boundingBox();
	const hit = await d.evaluate(([x, y]) => {
		const el = document.elementFromPoint(x, y);
		return el && el.closest('a') ? el.closest('a').className : '';
	}, [box.x + box.width / 2, box.y + 24]);
	ok(/xl-card__link/.test(hit), 'overlay card: the photo area is part of the card link');
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
	ok(foc.ring === 'solid' && foc.off < 0, 'overlay card focus ring is drawn inside the clipped card');

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
	const media = m.locator('.xl-split .xl-media__frame').first();
	await media.scrollIntoViewIfNeeded();
	await media.tap();
	await m.waitForTimeout(100);
	const tz = await media.evaluate((f) => getComputedStyle(f.querySelector('img')).transform);
	ok(tz === 'none', 'touch: tapping a photo does not leave a hover zoom');

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

	await browser.close();
	done();
})();
