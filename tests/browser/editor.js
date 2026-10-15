/*
 * Real Elementor editor round-trip on the Home page: editor loads, X LIGHTS
 * widgets registered, Page Composer panel scoped to the current template,
 * live preview updates, pending badges editor-only, save publishes.
 *
 * MODIFIES the Home page heading, then restores it.
 *
 *   BASE_URL=http://localhost:8080 ADMIN_USER=admin ADMIN_PASS=… HOME_ID=31 \
 *     node tests/browser/editor.js
 */
const { BASE_URL, OUT_DIR, ok, launch, done } = require('./lib');

const USER = process.env.ADMIN_USER || 'admin';
const PASS = process.env.ADMIN_PASS || 'admin';
const HOME_ID = process.env.HOME_ID;
if (!HOME_ID) {
	console.error('Set HOME_ID to the Home page ID (wp xlights status).');
	process.exit(2);
}
const EDITED = 'Edited in the <em>Elementor editor</em>';

(async () => {
	const browser = await launch();
	const ctx = await browser.newContext({ viewport: { width: 1600, height: 1000 } });
	const p = await ctx.newPage();
	const errs = [];
	p.on('pageerror', (e) => errs.push(e.message.slice(0, 160)));

	await p.goto(BASE_URL + '/wp-login.php');
	await p.fill('#user_login', USER);
	await p.fill('#user_pass', PASS);
	await p.click('#wp-submit');
	await p.waitForLoadState('networkidle');

	const t0 = Date.now();
	await p.goto(`${BASE_URL}/wp-admin/post.php?post=${HOME_ID}&action=elementor`, { waitUntil: 'domcontentloaded' });
	await p.waitForFunction(() => window.elementor && window.$e && elementor.documents && elementor.documents.getCurrent() && elementor.documents.getCurrent().container, null, { timeout: 120000 });
	await p.waitForTimeout(3000);
	console.log(`INFO editor ready in ${Date.now() - t0} ms`);

	const widgets = await p.evaluate(() => Object.keys(elementor.widgetsCache || {}).filter((k) => k.startsWith('xlights')).length);
	ok(widgets >= 21, `editor knows the X LIGHTS widgets (${widgets})`);

	const id = await p.evaluate(() => {
		const w = elementor.documents.getCurrent().container.children[0].children[0];
		$e.run('document/elements/select', { container: w });
		return w.id;
	});
	await p.waitForTimeout(3000);
	const panel = await p.innerText('#elementor-panel'); // innerText: conditionally hidden controls stay in the DOM.
	ok(panel.includes('Page Configuration'), 'Page Composer panel opens');
	ok(!panel.includes('About Hero'), 'only the current template’s sections are listed');
	await p.screenshot({ path: OUT_DIR + '/editor-panel.png' });

	// Switching a section off in "Sections — Visibility & Order" also removes its settings panel.
	const shown = (sec) => p.evaluate((sec) => {
		const el = document.querySelector('#elementor-panel .elementor-control-home__' + sec);
		return !!el && getComputedStyle(el).display !== 'none';
	}, sec);
	const setEnabled = (sec, on) => p.evaluate(({ id, sec, on }) => {
		const row = elementor.getContainer(id).settings.get('home__order').find((m) => m.get('section') === sec);
		row.set('enabled', on ? 'yes' : '');
	}, { id, sec, on });
	ok(await shown('connected'), 'section panel listed while the section is on');
	await setEnabled('connected', false);
	await p.waitForTimeout(1000);
	ok(!(await shown('connected')), 'switching a section off hides its settings panel');
	await setEnabled('connected', true);
	await p.waitForTimeout(1000);
	ok(await shown('connected'), 'switching it back on brings the panel back');

	const original = await p.evaluate((id) => elementor.getContainer(id).settings.get('home__hero__heading'), id);
	const save = () => p.evaluate(() => $e.run('document/save/update', { force: true }));
	await p.evaluate(
		({ id, text }) => $e.run('document/elements/settings', { container: elementor.getContainer(id), settings: { home__hero__heading: text } }),
		{ id, text: EDITED }
	);
	await p.waitForTimeout(4000);
	const frame = p.frames().find((f) => f.url().includes('elementor-preview'));
	ok(!!frame && (await frame.content()).includes('Edited in the'), 'live preview re-renders with the edit');
	ok(!!frame && (await frame.locator('.xl-pending-badge').count()) > 0, 'approval-gated content shows "pending" badges in the editor');

	await save();
	const front = await (await ctx.request.get(BASE_URL + '/')).text();
	ok(front.includes(EDITED), 'saved edit appears on the live page');
	ok(!front.includes('xl-pending-badge'), 'live page has no pending badges');

	// Restore.
	await p.evaluate(({ id, text }) => $e.run('document/elements/settings', { container: elementor.getContainer(id), settings: { home__hero__heading: text } }), { id, text: original });
	await save();
	const restored = await (await ctx.request.get(BASE_URL + '/')).text();
	ok(!restored.includes(EDITED), 'test edit reverted');

	ok(errs.length === 0, 'no JS errors in the editor ' + errs.slice(0, 3).join(' | '));
	await browser.close();
	done();
})();
