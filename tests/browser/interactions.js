/*
 * Interactive components: gallery filters + URL state + no-JS fallback,
 * lightbox (keyboard, focus return), before/after slider, ARIA tabs,
 * dropdown keyboard support, floating header, hero quick-estimate card,
 * FAQ accordion, stacked cards, reduced motion, mobile drawer + scroll lock.
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
	// 4 QA projects (tests/php/seed-qa-data.php) + the Cottonmouth Dispensary project the plugin adds (1.7.1).
	ok((await cards()) === 5, 'gallery shows the 4 QA projects and Cottonmouth Dispensary');
	await page.click('#xl-projects-gallery [data-xl-filter="soffit"]');
	await page.waitForTimeout(600);
	ok((await cards()) === 3, 'soffit filter → 3 projects');
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
	ok(await page.locator('.xl-section--hero.xl-hero--inset').count() === 1, 'home hero uses the inset frame');
	const float = await page.evaluate(() => {
		const h = document.querySelector('[data-xl-header]');
		const bar = h.querySelector('.xl-header__bar').getBoundingClientRect();
		const hero = document.querySelector('.xl-section--lead').getBoundingClientRect();
		return { over: hero.top < bar.top && bar.bottom > hero.top, bg: getComputedStyle(h.querySelector('.xl-header__bar')).backgroundColor, dark: h.classList.contains('is-over-dark') };
	});
	ok(float.over && !float.dark, 'header card floats inside the framed hero');
	ok(float.bg === 'rgb(255, 255, 255)', `floating header card is solid white (${float.bg})`);
	const clear = await page.evaluate(() => {
		window.scrollTo({ top: 0, behavior: 'instant' });
		const t = document.querySelector('.xl-section--lead .xl-hero__content').getBoundingClientRect().top;
		return t - document.querySelector('.xl-header__bar').getBoundingClientRect().bottom;
	});
	ok(clear > 0, `hero copy clears the floating header (${Math.round(clear)}px)`);
	ok((await page.locator('.xl-header .xl-lockup__market').first().textContent()).trim().length > 0, 'header shows the dealer lockup (logo + market)');

	// Hero quick-estimate card → step 1 of the quote form, prefilled.
	await page.selectOption('.xl-estimate select[name="service"]', 'bistro');
	await page.fill('.xl-estimate input[name="zip"]', '92672');
	await Promise.all([page.waitForNavigation({ waitUntil: 'networkidle' }), page.click('.xl-estimate button[type="submit"]')]);
	ok(page.url().includes('service=bistro') && page.url().includes('zip=92672'), 'estimate card opens the quote page with its answers');
	ok(await page.locator('input[name="interests[]"][value="bistro"]').isChecked(), 'chosen lighting type is preselected in step 1');
	ok((await page.inputValue('input[name="zip"]')) === '92672', 'ZIP code carried into the form');
	await page.goto(BASE_URL + '/', { waitUntil: 'networkidle' });

	// Dropdown keyboard.
	const toggle = page.locator('.xl-nav .xl-nav__toggle').first();
	await toggle.focus();
	await page.keyboard.press('Enter');
	ok((await toggle.getAttribute('aria-expanded')) === 'true', 'dropdown opens with Enter');
	const ratio = await page.evaluate(() => {
		const link = document.querySelector('.xl-header .xl-nav__item.is-open .xl-nav__sublink');
		const lum = (c) => {
			const [r, g, b] = c.match(/[\d.]+/g).slice(0, 3).map(Number).map((v) => {
				v /= 255;
				return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
			});
			return 0.2126 * r + 0.7152 * g + 0.0722 * b;
		};
		const fg = lum(getComputedStyle(link).color);
		const bg = lum(getComputedStyle(link.closest('.xl-nav__sub')).backgroundColor);
		return (Math.max(fg, bg) + 0.05) / (Math.min(fg, bg) + 0.05);
	});
	ok(ratio >= 4.5, `open dropdown text contrast ${ratio.toFixed(1)}:1 (needs 4.5:1)`);
	await page.keyboard.press('Escape');
	ok((await toggle.getAttribute('aria-expanded')) === 'false', 'Escape closes the dropdown');

	// FAQ: one row open at a time, keyboard operable, animated close.
	await page.goto(BASE_URL + '/lighting-solutions/permanent-soffit-lighting/', { waitUntil: 'networkidle' });
	const faq = page.locator('#xl-soffit-faq details');
	await faq.nth(0).locator('summary').click();
	await page.waitForTimeout(500);
	ok(await faq.nth(0).evaluate((d) => d.open), 'FAQ row opens on click');
	await faq.nth(1).locator('summary').focus();
	await page.keyboard.press('Enter');
	await page.waitForTimeout(600);
	ok(await faq.nth(1).evaluate((d) => d.open), 'FAQ row opens with Enter');
	ok(!(await faq.nth(0).evaluate((d) => d.open)), 'opening a row closes the previous one');
	await page.keyboard.press('Enter');
	await page.waitForTimeout(600);
	ok(!(await faq.nth(1).evaluate((d) => d.open)), 'Enter closes the open row again');

	// Stacked numbered cards.
	await page.goto(BASE_URL + '/lighting-solutions/', { waitUntil: 'networkidle' });
	const stack = page.locator('.xl-cards--stack > li');
	ok((await stack.count()) === 4, 'solutions overview uses stacked numbered cards');
	ok((await stack.first().evaluate((li) => getComputedStyle(li).position)) === 'sticky', 'stacked cards stick on desktop');
	ok((await page.locator('.xl-cards--stack .xl-card__index').first().textContent()).trim().startsWith('01'), 'cards are numbered 01…');

	// Image reveal settles fully (no lingering clip).
	const img = page.locator('.xl-section:not(.xl-section--lead) :is(.xl-media__frame, .xl-cine__media) > img').first();
	await img.scrollIntoViewIfNeeded();
	await page.waitForTimeout(2200);
	ok((await img.evaluate((i) => getComputedStyle(i).clipPath)) === 'none', 'revealed image is not left clipped');

	// Reduced motion: nothing hidden, nothing animated.
	const rm = await browser.newContext({ viewport: { width: 1440, height: 900 }, reducedMotion: 'reduce' });
	const rp = await rm.newPage();
	await rp.goto(BASE_URL + '/lighting-solutions/', { waitUntil: 'networkidle' });
	const hidden = await rp.evaluate(() => Array.from(document.querySelectorAll('[data-xl-reveal]')).filter((e) => getComputedStyle(e).opacity !== '1').length);
	ok(hidden === 0, 'reduced motion: every block visible without scrolling');
	ok((await rp.evaluate(() => getComputedStyle(document.documentElement).scrollBehavior)) === 'auto', 'reduced motion: no smooth scrolling');
	await rm.close();

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
