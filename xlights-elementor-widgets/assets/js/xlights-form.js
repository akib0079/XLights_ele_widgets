/*!
 * X LIGHTS quote form: progressive enhancement over a standard POST form.
 * Inline validation, residential/commercial switching, photo previews,
 * AJAX submission with duplicate protection and cache-safe nonce refresh.
 */
(function () {
	'use strict';

	var cfg = window.XLightsForm || { i18n: {} };
	var t = function (key, fallback) {
		return (cfg.i18n && cfg.i18n[key]) || fallback;
	};
	var ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];
	var SERVICES = ['soffit', 'bistro', 'deck', 'landscape', 'not_sure'];
	var REDUCED = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

	function $all(root, sel) {
		return Array.prototype.slice.call(root.querySelectorAll(sel));
	}

	function phoneDigits(value) {
		var d = String(value || '').replace(/\D+/g, '');
		if (d.length === 11 && d.charAt(0) === '1') {
			d = d.slice(1);
		}
		return d;
	}

	function icon(name) {
		var paths = {
			check: '<circle cx="12" cy="12" r="9"/><path d="m8 12.3 2.8 2.8L16.3 9.5"/>',
			alert: '<path d="M12 3.5 2.8 19.5h18.4z"/><path d="M12 10v4M12 16.8v.1"/>',
			close: '<path d="M6 6l12 12M18 6 6 18"/>'
		};
		return '<svg class="xl-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' + paths[name] + '</svg>';
	}

	function initForm(form) {
		if (form.dataset.xlFormReady) {
			return;
		}
		form.dataset.xlFormReady = '1';

		var status = form.querySelector('.xl-form__status');
		var submit = form.querySelector('[data-xl-submit]');
		var fileInput = form.querySelector('input[type="file"]');
		var fileList = form.querySelector('[data-xl-file-list]');
		var maxFiles = parseInt(form.getAttribute('data-max-files') || '6', 10);
		var maxMb = parseInt(form.getAttribute('data-max-mb') || '10', 10);
		var files = [];
		var attempted = false;
		var sending = false;
		var retried = false;

		var source = form.querySelector('input[name="source_url"]');
		if (source) {
			source.value = window.location.href.split('#')[0];
		}

		// Preselect from ?service= (links from service pages) when nothing is chosen yet.
		try {
			var params = new URLSearchParams(window.location.search);
			var service = (params.get('service') || '').toLowerCase();
			var chosen = $all(form, 'input[name="interests[]"]:checked').length > 0;
			if (!chosen && SERVICES.indexOf(service) !== -1) {
				var box = form.querySelector('input[name="interests[]"][value="' + service + '"]');
				if (box) {
					box.checked = true;
				}
			}
			var zip = (params.get('zip') || '').trim();
			if (/^\d{5}$/.test(zip) && form.elements.zip && !form.elements.zip.value) {
				form.elements.zip.value = zip;
			}
			if (service === 'commercial' || params.get('type') === 'commercial') {
				var com = form.querySelector('input[name="property_type"][value="commercial"]');
				if (com) {
					com.checked = true;
				}
			}
		} catch (e) {}

		// Residential / commercial switching.
		var commercial = form.querySelector('[data-xl-commercial]');
		function syncType() {
			var checked = form.querySelector('input[name="property_type"]:checked');
			var isCom = checked && checked.value === 'commercial';
			if (commercial) {
				commercial.hidden = !isCom;
				$all(commercial, '[data-xl-commercial-required]').forEach(function (el) {
					el.required = isCom;
					if (!isCom) {
						clearError(el.name);
					}
				});
			}
		}
		$all(form, 'input[name="property_type"]').forEach(function (r) {
			r.addEventListener('change', syncType);
		});
		syncType();

		// Error helpers.
		function fieldWrap(name) {
			return form.querySelector('[data-xl-field="' + name + '"]');
		}
		function setError(name, message) {
			var wrap = fieldWrap(name);
			if (!wrap) {
				return;
			}
			wrap.classList.add('has-error');
			var err = wrap.querySelector('.xl-field__error');
			if (err) {
				err.innerHTML = icon('alert') + '<span></span>';
				err.querySelector('span').textContent = message;
				err.hidden = false;
			}
			$all(wrap, 'input, select, textarea').forEach(function (el) {
				if (el.type !== 'hidden') {
					el.setAttribute('aria-invalid', 'true');
				}
			});
		}
		function clearError(name) {
			var wrap = fieldWrap(name);
			if (!wrap) {
				return;
			}
			wrap.classList.remove('has-error');
			var err = wrap.querySelector('.xl-field__error');
			if (err) {
				err.hidden = true;
				err.textContent = '';
			}
			$all(wrap, '[aria-invalid]').forEach(function (el) {
				el.removeAttribute('aria-invalid');
			});
		}
		function showStatus(type, message) {
			if (!status) {
				return;
			}
			status.hidden = false;
			status.className = 'xl-form__status is-' + type;
			status.innerHTML = icon(type === 'success' ? 'check' : 'alert') + '<p></p>';
			status.querySelector('p').textContent = message;
		}

		// Client-side validation (the server re-validates everything).
		function value(name) {
			var el = form.elements[name];
			return el && el.value ? String(el.value).trim() : '';
		}
		function validate() {
			var errors = {};
			['first_name', 'last_name', 'address', 'city'].forEach(function (name) {
				if (!value(name)) {
					errors[name] = t('required', 'This field is required.');
				}
			});
			if (!EMAIL_RE.test(value('email'))) {
				errors.email = t('email', 'Please enter a valid email address.');
			}
			var digits = phoneDigits(value('phone'));
			if (digits.length !== 10 || digits.charAt(0) === '0' || digits.charAt(0) === '1') {
				errors.phone = t('phone', 'Please enter a valid 10-digit US phone number.');
			}
			if (!/^\d{5}(-\d{4})?$/.test(value('zip'))) {
				errors.zip = t('zip', 'Please enter a valid ZIP code.');
			}
			if (!value('state')) {
				errors.state = t('required', 'This field is required.');
			}
			var type = form.querySelector('input[name="property_type"]:checked');
			if (!type) {
				errors.property_type = t('required', 'This field is required.');
			} else if (type.value === 'commercial') {
				if (!value('company')) {
					errors.company = t('required', 'This field is required.');
				}
				if (!value('business_type')) {
					errors.business_type = t('required', 'This field is required.');
				}
			}
			if (!form.querySelector('input[name="interests[]"]:checked')) {
				errors.interests = t('interests', 'Please choose at least one lighting interest.');
			}
			var privacy = form.querySelector('input[name="privacy"]');
			if (privacy && !privacy.checked) {
				errors.privacy = t('privacy', 'Please confirm the privacy acknowledgment.');
			}
			var fileError = validateFiles(files);
			if (fileError) {
				errors.photos = fileError;
			}
			return errors;
		}
		function render(errors) {
			['first_name', 'last_name', 'phone', 'email', 'address', 'city', 'state', 'zip', 'property_type', 'company', 'business_type', 'interests', 'message', 'photos', 'privacy'].forEach(function (name) {
				if (errors[name]) {
					setError(name, errors[name]);
				} else {
					clearError(name);
				}
			});
		}
		function focusFirstError() {
			var scope = stepped ? steps[current] : form;
			var first = scope.querySelector('.has-error input:not([type="hidden"]), .has-error select, .has-error textarea');
			if (first) {
				first.focus();
			}
		}

		// ---------------------------------------------------------------
		// Steps: Project → Property → Details. Without JS every step shows
		// as one long form; the server validates the full submission either way.
		// ---------------------------------------------------------------
		var steps = $all(form, '[data-xl-step]');
		var stepper = form.querySelector('[data-xl-stepper]');
		var live = form.querySelector('[data-xl-step-live]');
		var stepped = steps.length > 1 && !!stepper;
		var current = 0;
		var reached = 0;

		function stepFields(i) {
			return $all(steps[i], '[data-xl-field]').map(function (el) {
				return el.getAttribute('data-xl-field');
			});
		}
		function stepOf(name) {
			for (var i = 0; i < steps.length; i++) {
				if (stepFields(i).indexOf(name) !== -1) {
					return i;
				}
			}
			return steps.length - 1;
		}
		function firstErrorStep(errors) {
			var best = steps.length;
			Object.keys(errors).forEach(function (name) {
				best = Math.min(best, stepOf(name));
			});
			return best < steps.length ? best : -1;
		}
		function stepTitle(i) {
			var title = steps[i].querySelector('.xl-step__title');
			var count = title ? title.querySelector('.xl-step__count') : null;
			var text = title ? title.textContent : '';
			return count ? text.replace(count.textContent, '').trim() : text.trim();
		}
		function goTo(i, focus) {
			if (!stepped || i < 0 || i >= steps.length) {
				return;
			}
			var dir = i >= current ? 'fwd' : 'back';
			current = i;
			reached = Math.max(reached, i);
			steps.forEach(function (s, n) {
				s.classList.remove('is-active', 'is-enter-fwd', 'is-enter-back');
				if (n === i) {
					s.classList.add('is-active');
					if (!REDUCED) {
						void s.offsetWidth;
						s.classList.add(dir === 'fwd' ? 'is-enter-fwd' : 'is-enter-back');
					}
				}
			});
			$all(stepper, '[data-xl-stepper-item]').forEach(function (li, n) {
				li.classList.toggle('is-current', n === i);
				li.classList.toggle('is-done', n < i);
				if (n === i) {
					li.setAttribute('aria-current', 'step');
				} else {
					li.removeAttribute('aria-current');
				}
				var btn = li.querySelector('[data-xl-goto]');
				if (btn) {
					btn.disabled = n >= i && n > reached;
					if (n === i) {
						btn.disabled = true;
					}
				}
			});
			stepper.style.setProperty('--xl-step-progress', String(i / (steps.length - 1)));
			if (live) {
				live.textContent = t('stepOf', 'Step %1$s of %2$s: %3$s').replace('%1$s', i + 1).replace('%2$s', steps.length).replace('%3$s', stepTitle(i));
			}
			if (focus) {
				var top = form.getBoundingClientRect().top;
				if (top < 0 || top > window.innerHeight * 0.6) {
					form.scrollIntoView({ behavior: REDUCED ? 'auto' : 'smooth', block: 'start' });
				}
				var title = steps[i].querySelector('.xl-step__title');
				if (title) {
					title.focus({ preventScroll: true });
				}
			}
		}
		function validateStep(i) {
			attempted = true;
			var errors = validate();
			var names = stepFields(i);
			var mine = {};
			names.forEach(function (name) {
				if (errors[name]) {
					mine[name] = errors[name];
					setError(name, errors[name]);
				} else {
					clearError(name);
				}
			});
			return Object.keys(mine).length === 0;
		}
		function next() {
			if (validateStep(current)) {
				if (status && status.classList.contains('is-error')) {
					status.hidden = true;
				}
				goTo(current + 1, true);
			} else {
				focusFirstError();
			}
		}
		if (stepped) {
			form.classList.add('is-stepped');
			stepper.hidden = false;
			$all(form, '[data-xl-step-nav], .xl-form__submit [data-xl-prev]').forEach(function (el) {
				el.hidden = false;
			});
			$all(form, '[data-xl-next]').forEach(function (b) {
				b.addEventListener('click', next);
			});
			$all(form, '[data-xl-prev]').forEach(function (b) {
				b.addEventListener('click', function () {
					goTo(current - 1, true);
				});
			});
			$all(stepper, '[data-xl-goto]').forEach(function (b) {
				b.addEventListener('click', function () {
					var n = parseInt(b.getAttribute('data-xl-goto'), 10) - 1;
					if (n < current || n <= reached) {
						goTo(n, true);
					}
				});
			});
			goTo(0, false);
		}

		form.addEventListener('input', function (e) {
			if (attempted && e.target.name) {
				var errors = validate();
				var name = e.target.name.replace('[]', '');
				if (errors[name]) {
					setError(name, errors[name]);
				} else {
					clearError(name);
				}
			}
		});
		form.addEventListener('change', function (e) {
			if (attempted && e.target.name) {
				var name = e.target.name.replace('[]', '');
				var errors = validate();
				if (!errors[name]) {
					clearError(name);
				}
			}
		});

		var phone = form.elements.phone;
		if (phone) {
			phone.addEventListener('blur', function () {
				var d = phoneDigits(phone.value);
				if (d.length === 10) {
					phone.value = '(' + d.slice(0, 3) + ') ' + d.slice(3, 6) + '-' + d.slice(6);
				}
			});
		}

		// Photos: validation, previews, removal.
		function validateFiles(list) {
			if (list.length > maxFiles) {
				return t('tooMany', 'Please upload no more than %d photos.').replace('%d', maxFiles);
			}
			for (var i = 0; i < list.length; i++) {
				if (ALLOWED.indexOf(list[i].type) === -1) {
					return t('badType', 'Photos must be JPG, PNG or WEBP images.');
				}
				if (list[i].size > maxMb * 1024 * 1024) {
					return t('tooLarge', 'Each photo must be smaller than %d MB.').replace('%d', maxMb);
				}
			}
			return '';
		}
		function syncInput() {
			if (!fileInput || typeof DataTransfer === 'undefined') {
				return;
			}
			try {
				var dt = new DataTransfer();
				files.forEach(function (f) {
					dt.items.add(f);
				});
				fileInput.files = dt.files;
			} catch (e) {}
		}
		function renderFiles() {
			if (!fileList) {
				return;
			}
			$all(fileList, 'img').forEach(function (img) {
				URL.revokeObjectURL(img.src);
			});
			fileList.innerHTML = '';
			files.forEach(function (file, index) {
				var li = document.createElement('li');
				li.className = 'xl-file';
				if (ALLOWED.indexOf(file.type) !== -1) {
					var img = document.createElement('img');
					img.alt = '';
					img.src = URL.createObjectURL(file);
					li.appendChild(img);
				}
				var name = document.createElement('span');
				name.textContent = file.name;
				li.appendChild(name);
				if (typeof DataTransfer !== 'undefined') {
					var btn = document.createElement('button');
					btn.type = 'button';
					btn.innerHTML = icon('close');
					btn.setAttribute('aria-label', t('removeFile', 'Remove %s').replace('%s', file.name));
					btn.addEventListener('click', function () {
						files.splice(index, 1);
						syncInput();
						renderFiles();
						var err = validateFiles(files);
						if (err) {
							setError('photos', err);
						} else {
							clearError('photos');
						}
						if (fileInput) {
							fileInput.focus();
						}
					});
					li.appendChild(btn);
				}
				fileList.appendChild(li);
			});
		}
		if (fileInput) {
			var drop = form.querySelector('[data-xl-drop]');
			fileInput.addEventListener('change', function () {
				var incoming = Array.prototype.slice.call(fileInput.files || []);
				// Append to existing selection when the browser can rebuild the FileList.
				files = typeof DataTransfer !== 'undefined' ? files.concat(incoming) : incoming;
				var err = validateFiles(files);
				if (err && files.length > maxFiles && typeof DataTransfer !== 'undefined') {
					files = files.slice(0, maxFiles);
				}
				syncInput();
				renderFiles();
				err = validateFiles(files);
				if (err) {
					setError('photos', err);
				} else {
					clearError('photos');
				}
			});
			if (drop) {
				['dragenter', 'dragover'].forEach(function (ev) {
					drop.addEventListener(ev, function () {
						drop.classList.add('is-dragover');
					});
				});
				['dragleave', 'drop'].forEach(function (ev) {
					drop.addEventListener(ev, function () {
						drop.classList.remove('is-dragover');
					});
				});
			}
		}

		// Token refresh for cached pages (expired nonce).
		function refreshToken() {
			return fetch(cfg.tokenUrl, { credentials: 'same-origin', cache: 'no-store' })
				.then(function (r) {
					return r.json();
				})
				.then(function (json) {
					if (json && json.success && json.data) {
						form.elements._xl_nonce.value = json.data.nonce;
						form.elements._xl_t.value = json.data.token;
						return true;
					}
					return false;
				});
		}

		function send() {
			var data = new FormData(form);
			return fetch(form.getAttribute('data-ajax'), {
				method: 'POST',
				body: data,
				credentials: 'same-origin',
				headers: { 'X-Requested-With': 'XMLHttpRequest' }
			}).then(function (r) {
				return r.json().catch(function () {
					return { success: false, data: { code: 'error', message: t('network', 'Something went wrong. Please try again.') } };
				});
			});
		}

		function finish() {
			sending = false;
			form.classList.remove('is-sending');
			if (submit) {
				submit.disabled = false;
				submit.removeAttribute('aria-disabled');
			}
		}

		form.addEventListener('submit', function (e) {
			if (!window.fetch || !window.FormData) {
				return; // Native POST fallback.
			}
			e.preventDefault();
			if (sending) {
				return;
			}
			if (stepped && current < steps.length - 1) {
				next();
				return;
			}
			attempted = true;
			var errors = validate();
			render(errors);
			if (Object.keys(errors).length) {
				showStatus('error', t('fixErrors', 'Please review the highlighted fields.'));
				if (stepped) {
					goTo(firstErrorStep(errors), false);
				}
				focusFirstError();
				return;
			}
			sending = true;
			form.classList.add('is-sending');
			if (submit) {
				submit.disabled = true;
				submit.setAttribute('aria-disabled', 'true');
			}
			if (status) {
				status.hidden = true;
			}

			var handle = function (json) {
				var payload = (json && json.data) || {};
				if (json && json.success) {
					showStatus('success', payload.message || '');
					form.classList.add('is-sent');
					status.focus();
					try {
						window.dataLayer = window.dataLayer || [];
						window.dataLayer.push({ event: 'xlights_quote_submitted', xlights_variant: form.elements.variant ? form.elements.variant.value : '' });
						form.dispatchEvent(new CustomEvent('xlights:lead', { bubbles: true, detail: { variant: form.elements.variant ? form.elements.variant.value : '' } }));
					} catch (err) {}
					finish();
					return;
				}
				if (payload.code === 'nonce' && !retried) {
					retried = true;
					return refreshToken().then(function (ok) {
						return ok ? send().then(handle) : Promise.reject();
					});
				}
				if (payload.fields) {
					render(payload.fields);
					if (stepped && Object.keys(payload.fields).length) {
						goTo(firstErrorStep(payload.fields), false);
					}
					focusFirstError();
				}
				showStatus('error', payload.message || t('network', 'Something went wrong. Please try again.'));
				if (!payload.fields || !Object.keys(payload.fields).length) {
					status.focus();
				}
				if (window.turnstile && typeof window.turnstile.reset === 'function') {
					window.turnstile.reset();
				}
				finish();
			};

			send().then(handle).catch(function () {
				showStatus('error', t('network', 'We could not reach the server. Please check your connection and try again.'));
				status.focus();
				finish();
			});
		});
	}

	function boot(root) {
		$all(root || document, 'form[data-xl-form]').forEach(initForm);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			boot();
		});
	} else {
		boot();
	}

	window.addEventListener('elementor/frontend/init', function () {
		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
				if ($scope && $scope[0]) {
					boot($scope[0]);
				}
			});
		}
	});
})();
