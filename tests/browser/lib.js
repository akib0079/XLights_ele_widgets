/* Shared helpers for the Playwright browser tests. */
const path = require('path');
const fs = require('fs');

let playwright;
try {
	playwright = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
} catch (e) {
	console.error('Playwright not found. `npm i -D playwright` or set PLAYWRIGHT_MODULE=/path/to/playwright');
	process.exit(2);
}

const BASE_URL = (process.env.BASE_URL || 'http://localhost:8080').replace(/\/$/, '');
const OUT_DIR = process.env.OUT_DIR || path.join(__dirname, '..', '..', 'test-results');
fs.mkdirSync(OUT_DIR, { recursive: true });

const PAGES = [
	'/',
	'/about-us/',
	'/x-lights-app/',
	'/reviews/',
	'/lighting-solutions/',
	'/lighting-solutions/permanent-soffit-lighting/',
	'/lighting-solutions/bistro-lighting/',
	'/lighting-solutions/deck-lighting/',
	'/lighting-solutions/landscape-lighting/',
	'/commercial-lighting/',
	'/projects/',
	'/contact-us/',
];

let failures = 0;
function ok(cond, msg) {
	console.log((cond ? 'PASS ' : 'FAIL ') + msg);
	if (!cond) failures++;
}

async function launch() {
	const opts = {};
	if (process.env.CHROMIUM_PATH) opts.executablePath = process.env.CHROMIUM_PATH;
	return playwright.chromium.launch(opts);
}

/** Collect uncaught page errors and console errors (ignoring network 404 noise). */
function trackErrors(page) {
	const errors = [];
	page.on('pageerror', (e) => errors.push('pageerror: ' + e.message));
	page.on('console', (m) => {
		if (m.type() === 'error' && !/Failed to load resource/i.test(m.text())) errors.push('console: ' + m.text());
	});
	return errors;
}

/** Reveal scroll-animated content and disable transitions (stable checks/screenshots). */
async function settle(page) {
	await page.addStyleTag({ content: '*,*::before,*::after{transition:none!important;animation:none!important}' });
	await page.evaluate(() => document.querySelectorAll('[data-xl-reveal]').forEach((e) => e.classList.add('is-visible')));
	await page.waitForTimeout(200);
}

function done() {
	console.log(failures ? `\n${failures} check(s) failed.` : '\nAll checks passed.');
	process.exit(failures ? 1 : 0);
}

module.exports = { BASE_URL, OUT_DIR, PAGES, ok, launch, trackErrors, settle, done };
