/*
 * Interactive components: gallery filters + URL state + no-JS fallback,
 * lightbox (keyboard, focus return), before/after slider, ARIA tabs,
 * dropdown keyboard support, mobile drawer + scroll lock.
 *
 * Expects the data set from tests/php/seed-qa-data.php.
 *
 *   BASE_URL=http://localhost:8080 node tests/browser/interactions.js
 */
const { BASE_URL, OUT_DIR, ok, launch, trackErrors, done } = require('./lib');

(async () => {
	const browser = await launch();
	const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
	const page = await ctx.newPage();
	const errs = trackErrors(page);
	const cards = () => page.locator('#xl-projects-gallery .xl-projects__grid > li').count();

	// Projects gallery.
	await page.goto(BASE_URL + '/projects/', { waitUntil: 'networkidle' });
	ok((await cards()) === 4, 'gallery shows the 4 QA projects');
	await page.click('#xl-projects-gallery [data-xl-filter="soffit"]');
	await page.waitForTimeout(600);
	ok((await cards()) === 2, 'soffit filter → 2 projects');
	ok((await page.getAttribute('#xl-projects-gallery [data-xl-filter="soffit"]', 'aria-current')) === 'true', 'active filter has aria-current');
	ok(page.url().includes('xl_filter=soffit'), 'URL reflects the filter (shareable)');
	await page.click('#xl-projects-gallery [data-xl-filter="bistro"]');
	await page.waitForTimeout(600);
	ok((await cards()) === 1, 'bistro filter → 1 project');
	await page.click('#xl-projects-gallery [data-xl-filter="all"]');
	await page.waitForTimeout(600);

	// Lightbox.
	await page.locator('.xl-project__open').first().click();
	await page.waitForTimeout(300);
	ok((await page.locator('dialog.xl-lightbox[open]').count()) === 1, 'lightbox opens as a modal <dialog>');
	const cap1 = await page.textContent('.xl-lightbox__caption');
	await page.keyboard.press('ArrowRight');
	await page.waitForTimeout(150);
	ok(cap1 !== (await page.textContent('.xl-lightbox__caption')), 'ArrowRight advances the photo');
	await page.keyboard.press('Escape');
	await page.waitForTimeout(200);
	ok((await page.locator('dialog.xl-lightbox[open]').count()) === 0, 'Escape closes the lightbox');
	ok((await page.evaluate(() => document.activeElement.className)).includes('xl-project__open'), 'focus returns to the trigger');

	// Before / after.
	const ba = page.locator('[data-xl-ba]').first();
	ok((await ba.count()) === 1, 'before/after renders from project data');
	await page.locator('.xl-ba__range').first().fill('20');
	ok((await ba.evaluate((e) => e.style.getPropertyValue('--xl-pos'))) === '20%', 'before/after slider responds');

	// No-JS filter fallback.
	const html = await (await ctx.request.get(BASE_URL + '/projects/?xl_filter=deck')).text();
	const gallery = html.split('id="xl-projects-gallery"')[1].split('</section>')[0];
	ok((gallery.match(/class="xl-project"/g) || []).length === 1, 'server-side ?xl_filter=deck → 1 project (works without JS)');

	// Home: tabs, reviews, header.
	await page.goto(BASE_URL + '/', { waitUntil: 'networkidle' });
	const tab = page.locator('[role="tab"]').nth(2);
	await tab.click();
	ok((await tab.getAttribute('aria-selected')) === 'true', 'tab click selects the tab');
	await page.keyboard.press('ArrowRight');
	ok((await page.evaluate(() => document.activeElement.getAttribute('aria-selected'))) === 'true', 'ArrowRight moves tab selection and focus');
	ok((await page.locator('.xl-app-showcase__screen.is-active').count()) === 1, 'exactly one phone screen active');
	ok((await page.locator('.xl-section--reviews').count()) === 1, 'reviews section visible with permitted reviews');
	ok(!(await page.content()).includes('must NOT render'), 'review without permission is not rendered');
	ok((await page.locator('.xl-review__brand').count()) >= 1, 'brand-level review is labeled as such');
	ok(await page.locator('[data-xl-header]').evaluate((h) => h.classList.contains('is-over-dark')), 'header is transparent over the dark hero');

	// Dropdown keyboard.
	const toggle = page.locator('.xl-nav .xl-nav__toggle').first();
	await toggle.focus();
	await page.keyboard.press('Enter');
	ok((await toggle.getAttribute('aria-expanded')) === 'true', 'dropdown opens with Enter');
	await page.keyboard.press('Escape');
	ok((await toggle.getAttribute('aria-expanded')) === 'false', 'Escape closes the dropdown');

	// Mobile drawer.
	const m = await browser.newContext({ viewport: { width: 375, height: 780 }, isMobile: true, hasTouch: true });
	const mp = await m.newPage();
	const merrs = trackErrors(mp);
	await mp.goto(BASE_URL + '/', { waitUntil: 'networkidle' });
	await mp.click('[data-xl-menu-open]');
	await mp.waitForTimeout(450);
	ok(await mp.locator('[data-xl-drawer]').evaluate((d) => !d.hidden && d.classList.contains('is-open')), 'mobile drawer opens');
	ok(await mp.evaluate(() => document.documentElement.classList.contains('xl-scroll-lock')), 'page scroll locked while open');
	await mp.locator('.xl-drawer .xl-nav__toggle').nth(1).click();
	await mp.waitForTimeout(150);
	ok(await mp.locator('.xl-drawer .xl-nav__item.is-open .xl-nav__sub').isVisible(), 'drawer submenu expands');
	await mp.screenshot({ path: OUT_DIR + '/interactions-drawer.png' });
	await mp.keyboard.press('Escape');
	await mp.waitForTimeout(450);
	ok(await mp.locator('[data-xl-drawer]').evaluate((d) => d.hidden), 'Escape closes the drawer');
	ok(!(await mp.evaluate(() => document.documentElement.classList.contains('xl-scroll-lock'))), 'scroll lock released');

	ok(errs.length + merrs.length === 0, 'no JS errors ' + errs.concat(merrs).join(' | '));
	await browser.close();
	done();
})();
