/*!
 * X LIGHTS Elementor Widgets — front-end interactions (no dependencies).
 * Header navigation, mobile drawer, tabs, carousel, lightbox, before/after,
 * gallery filters, map facade, hero video and entrance animations.
 */
(function () {
	'use strict';

	var cfg = window.XLightsConfig || { i18n: {} };
	var t = function (key, fallback) {
		return (cfg.i18n && cfg.i18n[key]) || fallback;
	};
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var isEditor = document.body && document.body.classList.contains('elementor-editor-active');

	function once(el, key) {
		var flag = 'xl' + key;
		if (el.dataset[flag]) {
			return false;
		}
		el.dataset[flag] = '1';
		return true;
	}

	function $all(root, selector) {
		return Array.prototype.slice.call(root.querySelectorAll(selector));
	}

	/* ------------------------------------------------------------------
	 * Entrance animations
	 * ---------------------------------------------------------------- */
	function initReveal(root) {
		var roots = root.classList && root.classList.contains('xl-root') ? [root] : $all(root, '.xl-root');
		roots.forEach(function (r) {
			var items = $all(r, '[data-xl-reveal]:not(.is-visible)');
			if (!r.classList.contains('xl-anim') || reduceMotion || isEditor || !('IntersectionObserver' in window)) {
				items.forEach(function (el) {
					el.classList.add('is-visible');
				});
				return;
			}
			var vh = window.innerHeight || 800;
			// Elements already on screen are shown immediately (no flash of hidden content).
			items.forEach(function (el) {
				var rect = el.getBoundingClientRect();
				if (rect.top < vh * 0.92 && rect.bottom > 0) {
					el.classList.add('is-visible');
				}
			});
			r.classList.add('xl-js');
			var io = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						io.unobserve(entry.target);
					}
				});
			}, { rootMargin: '0px 0px -6% 0px', threshold: 0.06 });
			items.forEach(function (el) {
				if (!el.classList.contains('is-visible')) {
					io.observe(el);
				}
			});
		});
	}

	/* ------------------------------------------------------------------
	 * Tabs (WAI-ARIA tabs pattern, automatic activation)
	 * ---------------------------------------------------------------- */
	function initTabs(root) {
		$all(root, '[data-xl-tabs]').forEach(function (wrap) {
			if (!once(wrap, 'Tabs')) {
				return;
			}
			var tabs = $all(wrap, '[role="tab"]');
			var views = $all(wrap, '[data-xl-screen-view]');
			function select(index, focus) {
				tabs.forEach(function (tab, i) {
					var on = i === index;
					tab.setAttribute('aria-selected', on ? 'true' : 'false');
					tab.tabIndex = on ? 0 : -1;
					var panel = document.getElementById(tab.getAttribute('aria-controls'));
					if (panel) {
						panel.hidden = !on;
					}
				});
				views.forEach(function (view) {
					var on = parseInt(view.getAttribute('data-xl-screen-view'), 10) === index;
					view.classList.toggle('is-active', on);
					view.setAttribute('aria-hidden', on ? 'false' : 'true');
				});
				if (focus) {
					tabs[index].focus();
				}
			}
			tabs.forEach(function (tab, i) {
				tab.addEventListener('click', function () {
					select(i, false);
				});
				tab.addEventListener('keydown', function (e) {
					var next = null;
					if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
						next = (i + 1) % tabs.length;
					} else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
						next = (i - 1 + tabs.length) % tabs.length;
					} else if (e.key === 'Home') {
						next = 0;
					} else if (e.key === 'End') {
						next = tabs.length - 1;
					}
					if (next !== null) {
						e.preventDefault();
						select(next, true);
					}
				});
			});
		});
	}

	/* ------------------------------------------------------------------
	 * FAQ: one item open at a time (native <details> keeps working without JS)
	 * ---------------------------------------------------------------- */
	function initAccordion(root) {
		$all(root, '[data-xl-accordion]').forEach(function (acc) {
			if (!once(acc, 'Acc')) {
				return;
			}
			var items = $all(acc, 'details');
			items.forEach(function (item) {
				item.addEventListener('toggle', function () {
					if (item.open) {
						items.forEach(function (other) {
							if (other !== item && other.open) {
								other.open = false;
							}
						});
					}
				});
			});
		});
	}

	/* ------------------------------------------------------------------
	 * Carousel (CSS scroll-snap + buttons)
	 * ---------------------------------------------------------------- */
	function initCarousels(root) {
		$all(root, '[data-xl-carousel]').forEach(function (car) {
			if (!once(car, 'Car')) {
				return;
			}
			var track = car.querySelector('.xl-carousel__track');
			var prev = car.querySelector('[data-xl-prev]');
			var next = car.querySelector('[data-xl-next]');
			if (!track) {
				return;
			}
			function update() {
				var max = track.scrollWidth - track.clientWidth - 2;
				if (prev) {
					prev.disabled = track.scrollLeft <= 2;
				}
				if (next) {
					next.disabled = track.scrollLeft >= max;
				}
			}
			function go(dir) {
				track.scrollBy({ left: dir * track.clientWidth * 0.9, behavior: reduceMotion ? 'auto' : 'smooth' });
			}
			if (prev) {
				prev.addEventListener('click', function () {
					go(-1);
				});
			}
			if (next) {
				next.addEventListener('click', function () {
					go(1);
				});
			}
			track.addEventListener('scroll', function () {
				window.requestAnimationFrame(update);
			}, { passive: true });
			track.addEventListener('keydown', function (e) {
				if (e.key === 'ArrowRight') {
					e.preventDefault();
					go(1);
				} else if (e.key === 'ArrowLeft') {
					e.preventDefault();
					go(-1);
				}
			});
			window.addEventListener('resize', update);
			update();
		});
	}

	/* ------------------------------------------------------------------
	 * Before / after slider
	 * ---------------------------------------------------------------- */
	function initCompare(root) {
		$all(root, '[data-xl-ba]').forEach(function (ba) {
			if (!once(ba, 'Ba')) {
				return;
			}
			var range = ba.querySelector('.xl-ba__range');
			if (!range) {
				return;
			}
			var set = function () {
				ba.style.setProperty('--xl-pos', range.value + '%');
			};
			range.addEventListener('input', set);
			range.addEventListener('change', set);
			set();
		});
	}

	/* ------------------------------------------------------------------
	 * Lightbox (native <dialog>: focus trap, Escape and inert background)
	 * ---------------------------------------------------------------- */
	var lightbox = null;
	function svg(path) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' + path + '</svg>';
	}
	function buildLightbox() {
		var dialog = document.createElement('dialog');
		dialog.className = 'xl-lightbox';
		dialog.setAttribute('aria-label', t('dialog', 'Project photos'));
		dialog.innerHTML =
			'<div class="xl-lightbox__stage"><img class="xl-lightbox__img" alt=""></div>' +
			'<p class="xl-lightbox__caption" aria-live="polite"></p>' +
			'<button type="button" class="xl-lightbox__btn xl-lightbox__close" aria-label="' + t('close', 'Close') + '">' + svg('<path d="M6 6l12 12M18 6 6 18"/>') + '</button>' +
			'<button type="button" class="xl-lightbox__btn xl-lightbox__prev" aria-label="' + t('previous', 'Previous photo') + '">' + svg('<path d="m15 18-6-6 6-6"/>') + '</button>' +
			'<button type="button" class="xl-lightbox__btn xl-lightbox__next" aria-label="' + t('next', 'Next photo') + '">' + svg('<path d="m9 18 6-6-6-6"/>') + '</button>';
		document.body.appendChild(dialog);
		var state = { items: [], index: 0, trigger: null };
		var img = dialog.querySelector('.xl-lightbox__img');
		var caption = dialog.querySelector('.xl-lightbox__caption');
		var prev = dialog.querySelector('.xl-lightbox__prev');
		var next = dialog.querySelector('.xl-lightbox__next');

		function show(i) {
			var n = state.items.length;
			state.index = (i + n) % n;
			var item = state.items[state.index];
			img.src = item.src;
			if (item.srcset) {
				img.srcset = item.srcset;
				img.sizes = '100vw';
			} else {
				img.removeAttribute('srcset');
			}
			img.alt = item.alt || '';
			var counter = t('counter', 'Photo %1$s of %2$s').replace('%1$s', state.index + 1).replace('%2$s', n);
			caption.textContent = (item.caption ? item.caption + ' — ' : '') + counter;
			prev.hidden = next.hidden = n < 2;
		}
		dialog.querySelector('.xl-lightbox__close').addEventListener('click', function () {
			dialog.close();
		});
		prev.addEventListener('click', function () {
			show(state.index - 1);
		});
		next.addEventListener('click', function () {
			show(state.index + 1);
		});
		dialog.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') {
				show(state.index - 1);
			} else if (e.key === 'ArrowRight') {
				show(state.index + 1);
			}
		});
		dialog.addEventListener('click', function (e) {
			if (e.target === dialog || e.target.classList.contains('xl-lightbox__stage')) {
				dialog.close();
			}
		});
		dialog.addEventListener('close', function () {
			document.documentElement.classList.remove('xl-scroll-lock');
			img.removeAttribute('src');
			if (state.trigger) {
				state.trigger.focus();
			}
		});
		var touchX = null;
		dialog.addEventListener('touchstart', function (e) {
			touchX = e.touches[0].clientX;
		}, { passive: true });
		dialog.addEventListener('touchend', function (e) {
			if (touchX === null) {
				return;
			}
			var dx = e.changedTouches[0].clientX - touchX;
			if (Math.abs(dx) > 50) {
				show(state.index + (dx < 0 ? 1 : -1));
			}
			touchX = null;
		});
		return {
			open: function (items, index, trigger) {
				state.items = items;
				state.trigger = trigger;
				show(index || 0);
				document.documentElement.classList.add('xl-scroll-lock');
				dialog.showModal();
				dialog.querySelector('.xl-lightbox__close').focus();
			}
		};
	}
	function onLightboxClick(e) {
		var trigger = e.target.closest ? e.target.closest('[data-xl-lightbox]') : null;
		if (!trigger) {
			return;
		}
		var items;
		try {
			items = JSON.parse(trigger.getAttribute('data-xl-lightbox'));
		} catch (err) {
			return;
		}
		if (!items || !items.length) {
			return;
		}
		e.preventDefault();
		if (typeof HTMLDialogElement === 'undefined') {
			window.open(items[0].src, '_blank', 'noopener');
			return;
		}
		lightbox = lightbox || buildLightbox();
		lightbox.open(items, parseInt(trigger.getAttribute('data-xl-index') || '0', 10), trigger);
	}

	/* ------------------------------------------------------------------
	 * Project gallery: AJAX filters + load more (links work without JS)
	 * ---------------------------------------------------------------- */
	function initProjects(root) {
		$all(root, '[data-xl-projects]').forEach(function (box) {
			if (!once(box, 'Proj')) {
				return;
			}
			var grid = box.querySelector('.xl-projects__grid');
			var more = box.querySelector('[data-xl-more]');
			var empty = box.querySelector('.xl-projects__empty');
			var endpoint = box.getAttribute('data-endpoint');
			var state = {
				filter: box.getAttribute('data-filter') || 'all',
				page: 1,
				count: parseInt(box.getAttribute('data-count') || '9', 10),
				featured: box.getAttribute('data-featured') === '1'
			};
			var busy = false;
			function load(reset) {
				if (busy || !endpoint || !window.fetch) {
					return;
				}
				busy = true;
				grid.setAttribute('aria-busy', 'true');
				var url = new URL(endpoint, window.location.href);
				url.searchParams.set('filter', state.filter);
				url.searchParams.set('page', String(state.page));
				url.searchParams.set('count', String(state.count));
				if (state.featured) {
					url.searchParams.set('featured', '1');
				}
				fetch(url.toString(), { credentials: 'same-origin', headers: { Accept: 'application/json' } })
					.then(function (r) {
						if (!r.ok) {
							throw new Error('HTTP ' + r.status);
						}
						return r.json();
					})
					.then(function (data) {
						if (reset) {
							grid.innerHTML = data.html || '';
						} else {
							grid.insertAdjacentHTML('beforeend', data.html || '');
						}
						$all(grid, '[data-xl-reveal]').forEach(function (el) {
							el.classList.add('is-visible');
						});
						if (more) {
							more.hidden = !data.has_more;
						}
						if (empty) {
							empty.hidden = grid.children.length > 0;
						}
					})
					.catch(function () {
						if (empty) {
							empty.hidden = false;
							empty.textContent = t('error', 'Projects could not be loaded. Please try again.');
						}
					})
					.then(function () {
						busy = false;
						grid.setAttribute('aria-busy', 'false');
					});
			}
			$all(box, '[data-xl-filter]').forEach(function (link) {
				link.addEventListener('click', function (e) {
					e.preventDefault();
					state.filter = link.getAttribute('data-xl-filter');
					state.page = 1;
					state.featured = false;
					$all(box, '[data-xl-filter]').forEach(function (other) {
						other.removeAttribute('aria-current');
					});
					link.setAttribute('aria-current', 'true');
					if (window.history && window.history.replaceState) {
						var u = new URL(window.location.href);
						u.searchParams.set('xl_filter', state.filter);
						window.history.replaceState(null, '', u.pathname + u.search);
					}
					load(true);
				});
			});
			if (more) {
				more.addEventListener('click', function () {
					state.page += 1;
					load(false);
				});
			}
		});
	}

	/* ------------------------------------------------------------------
	 * Map facade: no Google requests until the visitor asks for the map
	 * ---------------------------------------------------------------- */
	function initMaps(root) {
		$all(root, '[data-xl-map]').forEach(function (map) {
			if (!once(map, 'Map')) {
				return;
			}
			var btn = map.querySelector('[data-xl-map-load]');
			if (!btn) {
				return;
			}
			btn.addEventListener('click', function () {
				var iframe = document.createElement('iframe');
				iframe.src = map.getAttribute('data-xl-map');
				iframe.title = btn.textContent.trim();
				iframe.loading = 'lazy';
				iframe.referrerPolicy = 'no-referrer-when-downgrade';
				iframe.setAttribute('allowfullscreen', '');
				var ph = map.querySelector('.xl-map__placeholder');
				if (ph) {
					ph.remove();
				}
				map.appendChild(iframe);
				iframe.focus();
			});
		});
	}

	/* ------------------------------------------------------------------
	 * Hero background video (skipped for reduced motion / data saver)
	 * ---------------------------------------------------------------- */
	function initVideo(root) {
		$all(root, '[data-xl-video]').forEach(function (video) {
			if (!once(video, 'Vid')) {
				return;
			}
			var saveData = navigator.connection && navigator.connection.saveData;
			if (reduceMotion || saveData) {
				return;
			}
			var start = function () {
				video.preload = 'auto';
				video.muted = true;
				var p = video.play();
				if (p && p.then) {
					p.then(function () {
						video.classList.add('is-playing');
					}).catch(function () {});
				}
			};
			if (document.readyState === 'complete') {
				start();
			} else {
				window.addEventListener('load', start, { once: true });
			}
		});
	}

	/* ------------------------------------------------------------------
	 * Header: sticky state, transparent-over-hero, dropdowns, mobile drawer
	 * ---------------------------------------------------------------- */
	function initHeader(header) {
		if (!once(header, 'Hdr')) {
			return;
		}
		var docEl = document.documentElement;

		// Transparent over a dark first section.
		if (header.classList.contains('xl-header--overlay-capable') && document.body.classList.contains('xl-header-overlay')) {
			var first = document.querySelector('.xl-main .xl-section, .xl-root--page .xl-section');
			if (first && first.classList.contains('xl-bg--dark')) {
				var gap = first.getBoundingClientRect().top - header.getBoundingClientRect().bottom;
				if (gap < 60) {
					header.classList.add('is-over-dark');
					var setOffset = function () {
						docEl.style.setProperty('--xl-header-offset', header.offsetHeight + 'px');
					};
					setOffset();
					window.addEventListener('resize', setOffset);
				}
			}
		}

		var ticking = false;
		var onScroll = function () {
			if (!ticking) {
				window.requestAnimationFrame(function () {
					header.classList.toggle('is-scrolled', window.scrollY > 12);
					ticking = false;
				});
				ticking = true;
			}
		};
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();

		// Dropdown disclosures.
		function setOpen(item, open) {
			var btn = item.querySelector(':scope > .xl-nav__toggle');
			item.classList.toggle('is-open', open);
			if (btn) {
				btn.setAttribute('aria-expanded', open ? 'true' : 'false');
			}
		}
		function closeAll(scope, except) {
			$all(scope, '.xl-nav__item.is-open').forEach(function (item) {
				if (item !== except) {
					setOpen(item, false);
				}
			});
		}
		$all(header, '.xl-nav__toggle').forEach(function (btn) {
			var item = btn.closest('.xl-nav__item');
			btn.addEventListener('click', function (e) {
				e.stopPropagation();
				var open = btn.getAttribute('aria-expanded') !== 'true';
				closeAll(item.parentElement, item);
				setOpen(item, open);
			});
			item.addEventListener('focusout', function (e) {
				if (!item.closest('.xl-drawer') && !item.contains(e.relatedTarget)) {
					setOpen(item, false);
				}
			});
			if (!item.closest('.xl-drawer')) {
				item.addEventListener('mouseenter', function () {
					if (window.matchMedia('(hover: hover) and (min-width: 1101px)').matches) {
						btn.setAttribute('aria-expanded', 'true');
					}
				});
				item.addEventListener('mouseleave', function () {
					if (!item.classList.contains('is-open')) {
						btn.setAttribute('aria-expanded', 'false');
					}
				});
			}
		});
		header.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape') {
				return;
			}
			var open = header.querySelector('.xl-nav .xl-nav__item.is-open');
			if (open) {
				setOpen(open, false);
				var btn = open.querySelector('.xl-nav__toggle');
				if (btn) {
					btn.focus();
				}
			}
		});
		document.addEventListener('click', function (e) {
			if (!header.contains(e.target)) {
				closeAll(header.querySelector('.xl-nav') || header, null);
			}
		});

		// Mobile drawer.
		var openBtn = header.querySelector('[data-xl-menu-open]');
		var drawer = header.querySelector('[data-xl-drawer]');
		if (!openBtn || !drawer) {
			return;
		}
		var panel = drawer.querySelector('.xl-drawer__panel');
		var closeTimer = null;
		function focusables() {
			return $all(panel, 'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])').filter(function (el) {
				return el.offsetParent !== null;
			});
		}
		function openDrawer() {
			window.clearTimeout(closeTimer);
			drawer.hidden = false;
			header.classList.add('is-menu-open');
			docEl.classList.add('xl-scroll-lock');
			openBtn.setAttribute('aria-expanded', 'true');
			window.requestAnimationFrame(function () {
				drawer.classList.add('is-open');
				var close = drawer.querySelector('.xl-drawer__head [data-xl-menu-close]');
				if (close) {
					close.focus();
				}
			});
		}
		function closeDrawer(returnFocus) {
			drawer.classList.remove('is-open');
			header.classList.remove('is-menu-open');
			docEl.classList.remove('xl-scroll-lock');
			openBtn.setAttribute('aria-expanded', 'false');
			closeTimer = window.setTimeout(function () {
				drawer.hidden = true;
			}, reduceMotion ? 0 : 320);
			if (returnFocus !== false) {
				openBtn.focus();
			}
		}
		openBtn.addEventListener('click', openDrawer);
		$all(drawer, '[data-xl-menu-close]').forEach(function (el) {
			el.addEventListener('click', function () {
				closeDrawer();
			});
		});
		drawer.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				e.preventDefault();
				closeDrawer();
				return;
			}
			if (e.key !== 'Tab') {
				return;
			}
			var items = focusables();
			if (!items.length) {
				return;
			}
			var firstEl = items[0];
			var lastEl = items[items.length - 1];
			if (e.shiftKey && document.activeElement === firstEl) {
				e.preventDefault();
				lastEl.focus();
			} else if (!e.shiftKey && document.activeElement === lastEl) {
				e.preventDefault();
				firstEl.focus();
			}
		});
		$all(drawer, 'a[href]').forEach(function (a) {
			a.addEventListener('click', function () {
				closeDrawer(false);
			});
		});
		window.addEventListener('resize', function () {
			if (window.innerWidth > 1100 && !drawer.hidden) {
				closeDrawer(false);
			}
		});
	}

	/* ------------------------------------------------------------------
	 * Skip link target fallback (global header mode without <main id>)
	 * ---------------------------------------------------------------- */
	function initSkipTarget() {
		if (document.getElementById('xl-main')) {
			return;
		}
		var target = document.querySelector('main') || document.querySelector('.xl-root--page');
		if (target) {
			target.id = target.id || 'xl-main';
			if (!target.hasAttribute('tabindex')) {
				target.setAttribute('tabindex', '-1');
			}
		}
	}

	/* ------------------------------------------------------------------
	 * Boot
	 * ---------------------------------------------------------------- */
	function init(root) {
		root = root || document;
		initTabs(root);
		initAccordion(root);
		initCarousels(root);
		initCompare(root);
		initProjects(root);
		initMaps(root);
		initVideo(root);
		initReveal(root);
	}

	function boot() {
		$all(document, '[data-xl-header]').forEach(initHeader);
		initSkipTarget();
		init(document);
		document.addEventListener('click', onLightboxClick);
	}

	window.XLights = { init: init };

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	// Elementor editor: re-initialize widgets after each (re)render.
	window.addEventListener('elementor/frontend/init', function () {
		if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
			return;
		}
		window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
			var el = $scope && $scope[0];
			if (!el || !el.getAttribute) {
				return;
			}
			var type = el.getAttribute('data-widget_type') || '';
			if (type.indexOf('xlights') === 0) {
				var header = el.querySelector('[data-xl-header]');
				if (header) {
					initHeader(header);
				}
				init(el);
			}
		});
	});
})();
