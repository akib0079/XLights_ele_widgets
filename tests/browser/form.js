/*
 * Stepped quote form in a real browser: Project → Property → Details.
 * Prefill from ?service=&zip=, per-step validation and focus, Back keeps
 * data, Enter advances, commercial fields, client file checks, previews,
 * AJAX submission, and the no-JavaScript single-form fallback.
 * Creates one lead (qa-browser-…@example.com).
 *
 *   BASE_URL=http://localhost:8080 node tests/browser/form.js
 */
const path = require('path');
const fs = require('fs');
const os = require('os');
const { execFileSync } = require('child_process');
const { BASE_URL, OUT_DIR, ok, launch, trackErrors, done } = require('./lib');

const fixtures = fs.mkdtempSync(path.join(os.tmpdir(), 'xl-fx-'));
execFileSync('php', [path.join(__dirname, '..', 'fixtures', 'make-fixtures.php'), fixtures]);
const photo = path.join(fixtures, 'photo-exif.jpg');

(async () => {
	const browser = await launch();
	const page = await (await browser.newContext({ viewport: { width: 1280, height: 900 } })).newPage();
	const errs = trackErrors(page);
	const active = () => page.locator('.xl-step.is-active');
	const stepNo = async () => parseInt(await active().getAttribute('data-xl-step'), 10);
	const next = () => active().locator('[data-xl-next]').click();

	await page.goto(BASE_URL + '/contact-us/?service=deck&zip=92672#quote', { waitUntil: 'networkidle' });
	ok((await page.locator('.xl-form.is-stepped').count()) === 1, 'form switches to stepped mode');
	ok(await page.locator('[data-xl-stepper]').isVisible(), 'stepper is visible');
	ok((await stepNo()) === 1, 'starts on step 1 (Project)');
	ok(await page.locator('input[name="interests[]"][value="deck"]').isChecked(), '?service=deck preselects Deck Lighting');
	ok((await page.inputValue('input[name="zip"]')) === '92672', '?zip= prefills the ZIP code');

	// Step 1 validation is scoped to step 1.
	await page.locator('input[name="interests[]"][value="deck"]').uncheck();
	await next();
	ok((await stepNo()) === 1, 'Continue with no interest stays on step 1');
	ok((await page.locator('[data-xl-field="interests"].has-error').count()) === 1, 'interest error shown');
	ok((await page.locator('[data-xl-field="first_name"].has-error').count()) === 0, 'later-step fields are not flagged yet');

	// Commercial fields.
	await page.check('input[name="property_type"][value="commercial"]');
	ok(await page.locator('[data-xl-commercial]').isVisible(), 'commercial fields appear');
	ok(await page.locator('input[name="company"]').evaluate((e) => e.required), 'company becomes required');
	await page.check('input[name="property_type"][value="residential"]');
	ok(!(await page.locator('[data-xl-commercial]').isVisible()), 'commercial fields hide again for residential');

	await page.check('input[name="interests[]"][value="soffit"]');
	await page.check('input[name="interests[]"][value="bistro"]');
	await page.fill('textarea[name="message"]', 'QA browser submission.');
	await next();
	ok((await stepNo()) === 2, 'valid step 1 advances to step 2 (Property)');
	ok((await page.getAttribute('[data-xl-stepper-item="1"]', 'class')).includes('is-done'), 'stepper marks step 1 done');
	ok(await page.evaluate(() => document.activeElement.classList.contains('xl-step__title')), 'focus moves to the step heading');

	// Step 2: errors, focus, files.
	await next();
	ok((await stepNo()) === 2, 'empty address keeps you on step 2');
	ok(await page.evaluate(() => document.activeElement.name === 'address'), 'focus moves to the first invalid field');
	ok((await page.getAttribute('input[name="address"]', 'aria-invalid')) === 'true', 'aria-invalid set on invalid fields');
	await page.setInputFiles('input[type="file"]', { name: 'doc.pdf', mimeType: 'application/pdf', buffer: Buffer.from('%PDF-1.4') });
	ok((await page.locator('[data-xl-field="photos"].has-error').count()) === 1, 'non-image upload rejected in the browser');
	await page.locator('.xl-file button').first().click();
	await page.fill('input[name="address"]', '1 QA Way');
	await page.fill('input[name="city"]', 'Testville');
	await page.setInputFiles('input[type="file"]', [photo, photo]);
	ok((await page.locator('.xl-file').count()) === 2, 'photo previews rendered');
	await page.locator('.xl-file button').first().click();
	ok((await page.locator('.xl-file').count()) === 1, 'a photo can be removed before sending');

	// Back keeps data; Enter advances.
	await active().locator('[data-xl-prev]').click();
	ok((await stepNo()) === 1, 'Back returns to step 1');
	ok((await page.inputValue('textarea[name="message"]')) === 'QA browser submission.', 'entered data is kept');
	await page.click('[data-xl-goto="2"]');
	ok((await stepNo()) === 2, 'stepper jumps forward to an already reached step');
	await page.locator('input[name="city"]').press('Enter');
	ok((await stepNo()) === 3, 'Enter on step 2 advances instead of submitting');

	// Step 3 + submit.
	await page.waitForTimeout(3100); // Minimum fill time guard.
	await page.fill('input[name="first_name"]', 'QA<script>alert(1)</script>');
	await page.fill('input[name="last_name"]', 'Browser');
	await page.fill('input[name="phone"]', '9497952206');
	await page.fill('input[name="email"]', `qa-browser-${Date.now()}@example.com`);
	await page.check('input[name="privacy"]');
	await page.click('[data-xl-submit]');
	await page.waitForSelector('.xl-form.is-sent', { timeout: 20000 }).catch(() => {});
	ok((await page.locator('.xl-form.is-sent').count()) === 1, 'AJAX submission succeeds and shows the success state');
	ok(!(await page.locator('[data-xl-stepper]').isVisible()), 'stepper hides after success');
	const status = ((await page.textContent('.xl-form__status')) || '').trim();
	ok(!/delivered|emailed to/i.test(status), `success message makes no delivery claim ("${status.slice(0, 70)}…")`);
	await page.screenshot({ path: OUT_DIR + '/form-success.png' });
	ok(errs.length === 0, 'no JS errors ' + errs.join(' | '));

	// No JavaScript: one long form, every step visible, no stepper.
	const noJs = await (await browser.newContext({ javaScriptEnabled: false, viewport: { width: 1280, height: 900 } })).newPage();
	await noJs.goto(BASE_URL + '/contact-us/#quote', { waitUntil: 'domcontentloaded' });
	ok((await noJs.locator('.xl-step').evaluateAll((els) => els.filter((e) => e.offsetParent !== null).length)) === 3, 'without JS all three steps are visible');
	ok(!(await noJs.locator('[data-xl-stepper]').isVisible()), 'without JS the stepper stays hidden');
	ok(await noJs.locator('[data-xl-submit]').isVisible(), 'without JS the submit button is available');

	await browser.close();
	fs.rmSync(fixtures, { recursive: true, force: true });
	done();
})();
