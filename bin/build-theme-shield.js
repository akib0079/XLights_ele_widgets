#!/usr/bin/env node
/*
 * Generates the "theme-style shield" at the end of assets/css/xlights.css.
 *
 * Elementor Site Settings → Theme Style can color every link and button on the
 * site, including :hover/:focus (`.elementor-kit-N a:hover`, `button:focus`,
 * specificity 0,2,1), which outranks most of the widgets' own state rules.
 * This script copies the color declarations (color, background, border-color)
 * of every rule that targets one of the plugin's links or buttons, prefixed
 * with `html ` (one element of specificity higher), in the original order, so
 * the widgets keep their own colors and every relationship between their rules
 * is preserved. Elementor per-widget Style controls only set CSS variables, so
 * they keep working.
 *
 *   node bin/build-theme-shield.js          # rewrite the block
 *   node bin/build-theme-shield.js --check  # exit 1 if the block is out of date
 */
'use strict';
const fs = require('fs');
const path = require('path');

const FILE = path.join(__dirname, '..', 'xlights-elementor-widgets', 'assets', 'css', 'xlights.css');
const BEGIN = '/* BEGIN generated theme-style shield (bin/build-theme-shield.js) */';
const END = '/* END generated theme-style shield */';
// Classes that sit on <a>, <button> or <summary> elements rendered by the plugin.
const CLASSES = [
	'xl-accordion__q', 'xl-btn', 'xl-card__link', 'xl-filter', 'xl-header__brand', 'xl-header__cta', 'xl-header__phone',
	'xl-header__toggle', 'xl-icon-btn', 'xl-lightbox__btn', 'xl-nav__link', 'xl-nav__sublink', 'xl-nav__toggle',
	'xl-phone-link', 'xl-project__open', 'xl-quote__contact-link', 'xl-reach', 'xl-skip', 'xl-stepper__btn', 'xl-store',
	'xl-tabs__tab', 'xl-sticky-cta__btn', 'xl-showcase__tab', 'xl-case__open', 'xl-footer__dealer',
];
const PROPS = ['color', 'background', 'background-color', 'border-color'];

function splitTop(str, sep) {
	const out = [];
	let depth = 0;
	let cur = '';
	for (const ch of str) {
		if (ch === '(' || ch === '[') depth++;
		else if (ch === ')' || ch === ']') depth--;
		if (ch === sep && depth === 0) {
			out.push(cur);
			cur = '';
		} else {
			cur += ch;
		}
	}
	out.push(cur);
	return out;
}

function subject(sel) {
	// Last compound selector (outside parentheses).
	let depth = 0;
	let last = 0;
	for (let i = 0; i < sel.length; i++) {
		const ch = sel[i];
		if (ch === '(' || ch === '[') depth++;
		else if (ch === ')' || ch === ']') depth--;
		else if (depth === 0 && /[\s>+~]/.test(ch)) last = i + 1;
	}
	return sel.slice(last);
}

function targetsControl(sel) {
	if (sel.includes('::') || /^\s*(:root|html|@)/.test(sel)) return false;
	const subj = subject(sel.trim());
	if (/^(a|button|summary)\b/.test(subj) || /:is\(button/.test(subj)) return true;
	return CLASSES.some((c) => new RegExp('\\.' + c + '(?![\\w-])|\\.' + c + '--').test(subj));
}

/** Walk the stylesheet: yields { media, selectors, decls } for style rules. */
function rules(css) {
	const out = [];
	const stack = [];
	let i = 0;
	while (i < css.length) {
		if (css.startsWith('/*', i)) {
			i = css.indexOf('*/', i) + 2;
			continue;
		}
		if (css[i] === '}') {
			stack.pop();
			i++;
			continue;
		}
		let j = i;
		while (j < css.length && !'{};'.includes(css[j]) && !css.startsWith('/*', j)) j++;
		if (css.startsWith('/*', j)) {
			i = j;
			continue;
		}
		if (css[j] !== '{') {
			i = css[j] === '}' ? j : j + 1;
			continue;
		}
		const prelude = css.slice(i, j).trim();
		if (prelude.startsWith('@')) {
			stack.push(prelude);
			i = j + 1;
			continue;
		}
		const close = css.indexOf('}', j);
		const body = css.slice(j + 1, close);
		const inKeyframes = stack.some((s) => s.startsWith('@keyframes'));
		if (!inKeyframes && !stack.some((s) => s.startsWith('@font-face'))) {
			out.push({ media: stack.filter((s) => s.startsWith('@media') || s.startsWith('@supports')), selectors: splitTop(prelude, ',').map((s) => s.trim()), body });
		}
		i = close + 1;
	}
	return out;
}

function build(css) {
	const lines = [
		BEGIN + '\n/* Do not edit by hand: run `node bin/build-theme-shield.js` after changing link or button styles.\n * Elementor Theme Style (.elementor-kit-N a:hover, button:focus) must not recolor the widgets.\n * Plain links inside widgets keep their color on hover/focus unless a rule below says otherwise. */',
		':where(html) .xl-root a:is(:hover, :focus, :active) {\n\tcolor: inherit;\n}',
	];
	for (const r of rules(css)) {
		const sels = r.selectors.filter(targetsControl);
		if (!sels.length) continue;
		const decls = r.body.split(';').map((d) => d.trim()).filter((d) => {
			const prop = d.split(':')[0].trim().toLowerCase();
			return PROPS.includes(prop);
		});
		if (!decls.length) continue;
		let block = sels.map((s) => 'html ' + s).join(',\n') + ' {\n' + decls.map((d) => '\t' + d + ';').join('\n') + '\n}';
		for (const m of r.media.slice().reverse()) {
			block = m + ' {\n' + block.split('\n').map((l) => '\t' + l).join('\n') + '\n}';
		}
		lines.push(block);
	}
	lines.push(END);
	return lines.join('\n\n') + '\n';
}

const raw = fs.readFileSync(FILE, 'utf8');
const start = raw.indexOf(BEGIN);
const base = start >= 0 ? raw.slice(0, start).replace(/\s+$/, '\n') : raw.replace(/\s+$/, '\n');
const next = base + '\n' + build(base);
if (process.argv.includes('--check')) {
	if (next !== raw) {
		console.error('theme-style shield is out of date: run node bin/build-theme-shield.js');
		process.exit(1);
	}
	console.log('theme-style shield is up to date');
	process.exit(0);
}
fs.writeFileSync(FILE, next);
console.log('theme-style shield: ' + (next.split('\n').length - base.split('\n').length) + ' lines');
