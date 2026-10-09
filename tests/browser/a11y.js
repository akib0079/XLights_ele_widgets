/*
 * axe-core WCAG 2.0/2.1/2.2 A + AA scan of all 12 pages at desktop and mobile.
 *
 *   npm i -D axe-core playwright
 *   BASE_URL=http://localhost:8080 node tests/browser/a11y.js
 *
 * Set AXE_PATH to axe.min.js if axe-core is not resolvable from here.
 */
const fs = require('fs');
const { BASE_URL, PAGES, ok, launch, settle, done } = require('./lib');

let axePath = process.env.AXE_PATH;
if (!axePath) {
	try {
		axePath = require.resolve('axe-core/axe.min.js');
	} catch (e) {
		console.error('axe-core not found. `npm i -D axe-core` or set AXE_PATH=/path/to/axe.min.js');
		process.exit(2);
	}
}
const axeSource = fs.readFileSync(axePath, 'utf8');

(async () => {
	const browser = await launch();
	for (const w of [1440, 375]) {
		const page = await (await browser.newContext({ viewport: { width: w, height: 900 } })).newPage();
		for (const p of PAGES) {
			await page.goto(BASE_URL + p, { waitUntil: 'networkidle' });
			await settle(page);
			await page.addScriptTag({ content: axeSource });
			const violations = await page.evaluate(async () =>
				(await window.axe.run(document, { runOnly: { type: 'tag', values: ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'] } })).violations.map(
					(v) => `${v.id} (${v.impact}, ${v.nodes.length}): ${v.nodes[0].target.join(' ')}`
				)
			);
			ok(violations.length === 0, `${p} @${w}px — ${violations.length ? violations.join('; ') : 'no violations'}`);
		}
	}
	await browser.close();
	done();
})();
