/*
 * Quote form in a real browser: service preselect, inline validation and
 * focus management, commercial fields, client-side file checks, previews,
 * AJAX submission. Creates one lead (qa-browser@example.com).
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

	await page.goto(BASE_URL + '/contact-us/?service=deck#quote', { waitUntil: 'networkidle' });
	ok(await page.locator('input[name="interests[]"][value="deck"]').isChecked(), '?service=deck preselects Deck Lighting');

	// Empty submit.
	await page.locator('input[name="interests[]"][value="deck"]').uncheck();
	await page.click('[data-xl-submit]');
	const errCount = await page.locator('.xl-field.has-error, fieldset.has-error').count();
	ok(errCount >= 8, `empty submit shows inline errors (${errCount})`);
	ok(await page.evaluate(() => document.activeElement.name === 'first_name'), 'focus moves to the first invalid field');
	ok((await page.getAttribute('input[name="email"]', 'aria-invalid')) === 'true', 'aria-invalid set on invalid fields');

	// Commercial fields.
	await page.check('input[name="property_type"][value="commercial"]');
	ok(await page.locator('[data-xl-commercial]').isVisible(), 'commercial fields appear');
	ok(await page.locator('input[name="company"]').evaluate((e) => e.required), 'company becomes required');
	await page.check('input[name="property_type"][value="residential"]');
	ok(!(await page.locator('[data-xl-commercial]').isVisible()), 'commercial fields hide again for residential');

	// Client-side file type check.
	await page.setInputFiles('input[type="file"]', { name: 'doc.pdf', mimeType: 'application/pdf', buffer: Buffer.from('%PDF-1.4') });
	ok((await page.locator('[data-xl-field="photos"].has-error').count()) === 1, 'non-image upload rejected in the browser');

	// Valid submission.
	await page.reload({ waitUntil: 'networkidle' });
	await page.waitForTimeout(3100); // Minimum fill time.
	await page.fill('input[name="first_name"]', 'QA<script>alert(1)</script>');
	await page.fill('input[name="last_name"]', 'Browser');
	await page.fill('input[name="phone"]', '9497952206');
	await page.fill('input[name="email"]', `qa-browser-${Date.now()}@example.com`);
	await page.fill('input[name="address"]', '1 QA Way');
	await page.fill('input[name="city"]', 'Testville');
	await page.fill('input[name="zip"]', '92672');
	await page.check('input[name="interests[]"][value="soffit"]');
	await page.check('input[name="interests[]"][value="bistro"]');
	await page.fill('textarea[name="message"]', 'QA browser submission.');
	await page.setInputFiles('input[type="file"]', [photo, photo]);
	ok((await page.locator('.xl-file').count()) === 2, 'photo previews rendered');
	await page.locator('.xl-file button').first().click();
	ok((await page.locator('.xl-file').count()) === 1, 'a photo can be removed before sending');
	await page.check('input[name="privacy"]');
	await page.click('[data-xl-submit]');
	await page.waitForSelector('.xl-form.is-sent', { timeout: 20000 }).catch(() => {});
	ok((await page.locator('.xl-form.is-sent').count()) === 1, 'AJAX submission succeeds and shows the success state');
	const status = ((await page.textContent('.xl-form__status')) || '').trim();
	ok(!/delivered|emailed to/i.test(status), `success message makes no delivery claim ("${status.slice(0, 80)}…")`);
	await page.screenshot({ path: OUT_DIR + '/form-success.png' });

	ok(errs.length === 0, 'no JS errors ' + errs.join(' | '));
	await browser.close();
	fs.rmSync(fixtures, { recursive: true, force: true });
	done();
})();
