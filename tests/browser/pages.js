/*
 * Every page at 5 widths: no horizontal overflow, no JS errors, one H1,
 * valid JSON-LD, title + meta description. Saves full-page screenshots.
 *
 *   BASE_URL=http://localhost:8080 node tests/browser/pages.js [widths]
 */
const { BASE_URL, OUT_DIR, PAGES, ok, launch, trackErrors, settle, done } = require('./lib');

const widths = (process.argv[2] || '375,768,1024,1440,1920').split(',').map(Number);

(async () => {
	const browser = await launch();
	for (const w of widths) {
		const ctx = await browser.newContext({ viewport: { width: w, height: 900 } });
		const page = await ctx.newPage();
		const errors = trackErrors(page);
		for (const p of PAGES) {
			const res = await page.goto(BASE_URL + p, { waitUntil: 'networkidle' });
			await settle(page);
			const name = (p === '/' ? 'home' : p.replace(/\//g, '_').replace(/^_|_$/g, '')) + '-' + w;
			const info = await page.evaluate(() => ({
				overflow: document.documentElement.scrollWidth - window.innerWidth,
				h1: document.querySelectorAll('h1').length,
				title: document.title,
				desc: (document.querySelector('meta[name="description"]') || {}).content || '',
				ld: [...document.querySelectorAll('script[type="application/ld+json"]')].map((s) => {
					try {
						JSON.parse(s.textContent);
						return true;
					} catch (e) {
						return false;
					}
				}),
			}));
			ok(res.status() === 200, `${name} HTTP 200`);
			ok(info.overflow <= 0, `${name} no horizontal overflow (${info.overflow}px)`);
			ok(info.h1 === 1, `${name} one H1 (${info.h1})`);
			if (w === widths[0]) {
				ok(info.title.length > 10, `${name} has a title`);
				ok(info.desc.length > 50, `${name} has a meta description`);
				ok(info.ld.length > 0 && info.ld.every(Boolean), `${name} JSON-LD present and valid (${info.ld.length})`);
			}
			ok(errors.length === 0, `${name} no JS errors ${errors.join(' | ')}`);
			errors.length = 0;
			await page.screenshot({ path: `${OUT_DIR}/${name}.png`, fullPage: true });
		}
		await ctx.close();
	}
	await browser.close();
	done();
})();
