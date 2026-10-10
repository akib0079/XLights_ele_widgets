/*
 * Global design settings (X LIGHTS → Settings → Colors & Fonts) end to end.
 *
 *  1. Static: no default brand color is hard-coded outside the token block.
 *  2. A completely different palette + fonts applied through the settings:
 *     every widget, the header and the footer follow it (computed-style scan
 *     for any leftover default color), headings follow case/size, contrast
 *     still passes axe, nothing overflows with a wide font.
 *  3. Fonts: device stacks, Elementor Global Fonts and a Google font loaded
 *     through Elementor.
 *  4. Settings screen: live preview, readability warnings, custom family field.
 *
 * Changes settings temporarily and restores them at the end.
 *
 *   BASE_URL=http://localhost:8080 WP="wp --path=/srv/www" ADMIN_USER=admin ADMIN_PASS=… \
 *   AXE_PATH=/path/to/axe.min.js node tests/browser/design.js
 */
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');
const { BASE_URL, OUT_DIR, PAGES, ok, launch, trackErrors, settle, done } = require('./lib');

const WP = process.env.WP || 'wp';
const USER = process.env.ADMIN_USER || 'admin';
const PASS = process.env.ADMIN_PASS || 'admin';
const KEYS = ['color_accent', 'color_ink', 'color_text', 'color_dark', 'color_surface', 'font_heading', 'font_body', 'heading_case', 'heading_scale'];

const wp = (args) => execSync(`${WP} ${args}`, { encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'] }).trim();
const get = (key) => wp(`xlights settings get ${key}`);
const set = (values) => Object.entries(values).forEach(([k, v]) => wp(`xlights settings set ${k} '${String(v).replace(/'/g, '')}'`));

// Default palette, as RGB triples, that must not survive a palette change.
const DEFAULT_RGB = {
	accent: [51, 103, 254], accentText: [41, 82, 203], onDark: [200, 215, 254], glow: [124, 152, 255],
	navy: [19, 28, 45], navy2: [26, 36, 54], navy3: [35, 45, 64], deep: [11, 17, 28],
	ink: [5, 7, 11], text: [27, 33, 48], muted: [86, 93, 107], surface: [233, 234, 237],
};

(async () => {
	// 1. Static check of the stylesheet ------------------------------------------------
	const css = fs.readFileSync(path.join(__dirname, '..', '..', 'xlights-elementor-widgets', 'assets', 'css', 'xlights.css'), 'utf8');
	const body = css.slice(css.indexOf(' * Scoped reset'));
	const literals = ['#3367fe', '#2952cb', '#c8d7fe', '#131c2d', '#05070b', '#e9eaed', '#1b2130', '#565d6b', '#1a2436', '#232d40', '#0b111c',
		'rgba(51, 103, 254', 'rgba(124, 152, 255', 'rgba(200, 215, 254', 'rgba(19, 28, 45', 'rgba(11, 17, 28', 'rgba(9, 13, 22', 'rgba(5, 7, 11'];
	const found = literals.filter((l) => body.toLowerCase().includes(l));
	ok(found.length === 0, 'stylesheet has no hard-coded brand colors outside the token block ' + found.join(' '));

	const original = {};
	KEYS.forEach((k) => { original[k] = get(k); });

	const browser = await launch();
	try {
		// 2. Custom palette + fonts -----------------------------------------------------
		const palette = { color_accent: '#e4002b', color_ink: '#14110f', color_text: '#2b2522', color_dark: '#0f3d2e', color_surface: '#efe9df' };
		set(Object.assign({}, palette, { font_heading: '@system-serif', font_body: '@system-sans', heading_case: 'none', heading_scale: 85 }));

		const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
		const page = await ctx.newPage();
		const errs = trackErrors(page);
		const scanPages = ['/', '/lighting-solutions/', '/lighting-solutions/permanent-soffit-lighting/', '/x-lights-app/', '/contact-us/', '/reviews/', '/projects/'];
		let leftovers = [];
		for (const url of scanPages) {
			await page.goto(BASE_URL + url, { waitUntil: 'networkidle' });
			await settle(page);
			if (url === '/') {
				const inline = await page.evaluate(() => (document.getElementById('xlights-inline-css') || {}).textContent || '');
				ok(inline.includes('--xl-accent:#e4002b') && inline.includes(':root,.xl-root{'), 'settings are output as site-wide tokens');
				ok(/--xl-accent-text:#[0-9a-f]{6}/.test(inline) && /--xl-on-accent:#[0-9a-f]{6}/.test(inline), 'derived accent shades are output');
			}
			// Open hover-only/JS states that the scan should see too.
			await page.evaluate(() => {
				document.querySelectorAll('details').forEach((d) => { d.open = true; });
			});
			const hits = await page.evaluate((DEFAULTS) => {
				const props = ['color', 'backgroundColor', 'borderTopColor', 'borderBottomColor', 'borderLeftColor', 'outlineColor', 'fill', 'stroke', 'backgroundImage', 'boxShadow', 'textDecorationColor'];
				const triples = Object.entries(DEFAULTS);
				const out = [];
				const roots = document.querySelectorAll('.xl-root');
				roots.forEach((root) => {
					[root, ...root.querySelectorAll('*')].forEach((el) => {
						if (el.closest('.xl-illus, .xl-phone, .xl-photo-needed, .xl-editor-hint, svg defs')) return;
						if (el.offsetParent === null && getComputedStyle(el).position !== 'fixed') return;
						const cs = getComputedStyle(el);
						props.forEach((p) => {
							const v = cs[p];
							if (!v || v === 'none') return;
							(v.match(/rgba?\([^)]*\)/g) || []).forEach((c) => {
								const n = c.match(/[\d.]+/g).map(Number);
								if (n.length > 3 && n[3] === 0) return;
								triples.forEach(([name, t]) => {
									if (n[0] === t[0] && n[1] === t[1] && n[2] === t[2]) {
										out.push(`${name} in ${p} on ${el.tagName.toLowerCase()}.${String(el.className.baseVal !== undefined ? el.className.baseVal : el.className).trim().split(/\s+/).slice(0, 2).join('.')}`);
									}
								});
							});
						});
					});
				});
				return Array.from(new Set(out));
			}, DEFAULT_RGB);
			leftovers = leftovers.concat(hits.map((h) => url + ': ' + h));
		}
		ok(leftovers.length === 0, `every widget, header and footer follow the new palette (${leftovers.length} leftovers)${leftovers.length ? '\n  ' + leftovers.slice(0, 25).join('\n  ') : ''}`);

		await page.goto(BASE_URL + '/', { waitUntil: 'networkidle' });
		await settle(page);
		const type = await page.evaluate(() => {
			const t = document.querySelector('.xl-hero__title');
			const p = document.querySelector('.xl-hero__text');
			const h2 = document.querySelector('.xl-section:not(.xl-section--lead) .xl-head .xl-title');
			return {
				heading: getComputedStyle(t).fontFamily, body: getComputedStyle(p).fontFamily,
				transform: getComputedStyle(h2).textTransform, size: parseFloat(getComputedStyle(h2).fontSize),
				footer: getComputedStyle(document.querySelector('.xl-footer')).backgroundColor,
				btn: getComputedStyle(document.querySelector('.xl-header .xl-btn--primary')).backgroundColor,
			};
		});
		ok(/ui-serif|Georgia/.test(type.heading), `heading font follows the setting (${type.heading.slice(0, 40)}…)`);
		ok(/ui-sans-serif|system-ui/.test(type.body) && !/Barlow/.test(type.body), `body font follows the setting (${type.body.slice(0, 40)}…)`);
		ok(type.transform === 'none', 'heading letter case follows the setting');
		ok(type.footer === 'rgb(15, 61, 46)', `footer uses the dark sections color (${type.footer})`);
		ok(type.btn === 'rgb(20, 17, 15)', `header button uses the headings & buttons color (${type.btn})`);
		await page.hover('.xl-header .xl-btn--primary');
		await page.waitForTimeout(400);
		const hover = await page.evaluate(() => {
			const cs = getComputedStyle(document.querySelector('.xl-header .xl-btn--primary'));
			return [cs.backgroundColor, cs.color];
		});
		ok(hover[0] === 'rgb(228, 0, 43)' && hover[1] === 'rgb(255, 255, 255)', `button hover uses the accent with readable text (${hover.join(' / ')})`);
		await page.screenshot({ path: OUT_DIR + '/design-custom-home.png' });

		// Heading size: 85% of the default.
		set({ heading_scale: 100 });
		await page.goto(BASE_URL + '/', { waitUntil: 'networkidle' });
		const full = await page.evaluate(() => parseFloat(getComputedStyle(document.querySelector('.xl-section:not(.xl-section--lead) .xl-head .xl-title')).fontSize));
		ok(Math.abs(type.size / full - 0.85) < 0.02, `heading size scales (${type.size.toFixed(1)}px vs ${full.toFixed(1)}px)`);

		// Contrast with the custom palette.
		if (process.env.AXE_PATH && fs.existsSync(process.env.AXE_PATH)) {
			const axe = fs.readFileSync(process.env.AXE_PATH, 'utf8');
			for (const url of ['/', '/contact-us/', '/lighting-solutions/']) {
				await page.goto(BASE_URL + url, { waitUntil: 'networkidle' });
				await settle(page);
				await page.addScriptTag({ content: axe });
				const res = await page.evaluate(() => window.axe.run(document, { runOnly: ['color-contrast'] }));
				ok(res.violations.length === 0, `custom palette keeps WCAG AA contrast on ${url} ${res.violations.map((v) => v.nodes.map((n) => n.target.join(' ')).join(', ')).join(' ')}`);
			}
		} else {
			console.log('SKIP contrast (set AXE_PATH)');
		}

		// A wide font at full size, uppercase: no horizontal overflow on phones.
		set({ font_heading: '@system-sans', heading_case: 'uppercase', heading_scale: 100 });
		const m = await browser.newContext({ viewport: { width: 375, height: 800 }, isMobile: true });
		const mp = await m.newPage();
		const wide = [];
		for (const url of PAGES) {
			await mp.goto(BASE_URL + url, { waitUntil: 'networkidle' });
			const over = await mp.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
			if (over > 0) wide.push(`${url} (${over}px)`);
		}
		ok(wide.length === 0, 'a wide heading font never causes horizontal scrolling at 375px ' + wide.join(' '));
		await m.close();

		// 3. Font sources ---------------------------------------------------------------
		set({ font_heading: '@elementor-primary', font_body: 'Montserrat' });
		await page.goto(BASE_URL + '/', { waitUntil: 'networkidle' });
		const f = await page.evaluate(() => ({
			links: Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map((l) => decodeURIComponent(l.href)).filter((h) => /google-fonts|fonts\.googleapis/.test(h)),
			heading: getComputedStyle(document.querySelector('.xl-hero__title')).fontFamily,
			body: getComputedStyle(document.querySelector('.xl-hero__text')).fontFamily,
		}));
		const kitFamily = wp('eval \'echo \\XLights\\Frontend\\Typography::elementor_global("primary");\'');
		ok(kitFamily !== '' && f.heading.includes(kitFamily), `Elementor global font (Primary: ${kitFamily}) is used for headings`);
		ok(f.body.startsWith('Montserrat') || f.body.startsWith('"Montserrat"'), `Google font family applied to body text (${f.body.slice(0, 30)}…)`);
		ok(f.links.some((h) => h.includes('Montserrat')), 'Google font requested through Elementor (honours its local-fonts option)');

		set({ font_body: 'My Brand Font' });
		await page.goto(BASE_URL + '/', { waitUntil: 'networkidle' });
		const custom = await page.evaluate(() => getComputedStyle(document.querySelector('.xl-hero__text')).fontFamily);
		ok(custom.startsWith('"My Brand Font"'), 'a custom family name (loaded by the theme) is applied as-is');

		set({ font_body: 'x;}body{display:none' });
		ok(get('font_body') === '', 'unsafe font values are rejected');

		// 4. Settings screen --------------------------------------------------------------
		Object.entries(original).forEach(([k, v]) => set({ [k]: v }));
		const a = await browser.newContext({ viewport: { width: 1400, height: 1000 } });
		const ap = await a.newPage();
		const aerrs = trackErrors(ap);
		await ap.goto(BASE_URL + '/wp-login.php');
		await ap.fill('#user_login', USER);
		await ap.fill('#user_pass', PASS);
		await Promise.all([ap.waitForNavigation(), ap.click('#wp-submit')]);
		await ap.goto(BASE_URL + '/wp-admin/admin.php?page=xlights-settings&tab=design', { waitUntil: 'networkidle' });
		ok((await ap.locator('[data-xl-design-preview]').count()) === 1, 'Colors & Fonts tab shows a live preview');
		ok((await ap.locator('select#xl-font_heading optgroup').count()) >= 4, 'font picker lists bundled, device, Elementor global and Elementor/Google fonts');
		await ap.evaluate(() => jQuery('#xl-color_accent').wpColorPicker('color', '#e4002b'));
		await ap.waitForFunction(() => getComputedStyle(document.querySelector('[data-xl-design-preview]')).getPropertyValue('--xl-accent').trim() === '#e4002b', null, { timeout: 5000 }).catch(() => {});
		ok((await ap.evaluate(() => getComputedStyle(document.querySelector('[data-xl-design-preview]')).getPropertyValue('--xl-accent').trim())) === '#e4002b', 'preview updates live when a color changes');
		await ap.evaluate(() => jQuery('#xl-color_dark').wpColorPicker('color', '#8fa3c8'));
		await ap.waitForTimeout(1200);
		ok((await ap.locator('[data-xl-design-checks] p:not(.is-ok)').count()) >= 1, 'preview warns about unreadable combinations');
		await ap.selectOption('select#xl-font_heading', '__custom');
		ok(await ap.locator('#xl-font_heading-custom').isVisible(), '"Another font family…" reveals the name field');
		await ap.fill('#xl-font_heading-custom', 'Brand Display');
		await ap.selectOption('#xl-heading_case', 'none');
		await ap.screenshot({ path: OUT_DIR + '/design-settings.png', fullPage: true });
		await Promise.all([ap.waitForNavigation(), ap.click('#submit')]);
		ok((await ap.locator('.notice-warning, .notice.notice-warning, div.updated, .settings-error').filter({ hasText: 'Readability check' }).count()) >= 1, 'saving an unreadable palette shows a readability warning');
		ok(get('font_heading') === 'Brand Display' && get('heading_case') === 'none' && get('color_accent').toLowerCase() === '#e4002b', 'settings form saves colors, custom font and case');
		await ap.click('[data-xl-design-reset]');
		ok((await ap.inputValue('#xl-color_accent')).toLowerCase() === '#3367fe' && (await ap.inputValue('select#xl-font_heading')) === '', 'reset fills in the X LIGHTS defaults');
		ok(errs.length + aerrs.length === 0, 'no JS errors ' + errs.concat(aerrs).join(' | '));
		await a.close();
	} finally {
		Object.entries(original).forEach(([k, v]) => set({ [k]: v }));
		ok(KEYS.every((k) => get(k) === original[k]), 'original settings restored');
		await browser.close();
	}
	done();
})();
