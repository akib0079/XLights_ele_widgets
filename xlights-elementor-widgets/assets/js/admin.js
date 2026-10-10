/*!
 * X LIGHTS admin: media pickers, project gallery manager, color pickers.
 */
(function ($) {
	'use strict';
	var L = window.XLightsAdmin || {};

	$(function () {
		var $preview = $('[data-xl-design-preview]');
		var preview = $preview.length ? designPreview($preview) : null;

		if ($.fn.wpColorPicker) {
			$('.xl-color').wpColorPicker({
				change: function (e, ui) {
					$(e.target).val(ui.color.toString());
					if (preview) {
						preview.colors();
					}
				},
				clear: function () {
					if (preview) {
						preview.colors();
					}
				}
			});
		}

		// Font pickers: "Another font family…" reveals the name field.
		$('[data-xl-font]').each(function () {
			var $wrap = $(this);
			var $select = $wrap.find('select');
			var $custom = $wrap.find('.xl-font-field__custom');
			$select.on('change', function () {
				$custom.prop('hidden', $select.val() !== '__custom');
				if ($select.val() === '__custom') {
					$custom.find('input').trigger('focus');
				}
				if (preview) {
					preview.fonts();
				}
			});
			$custom.find('input').on('input', function () {
				if (preview) {
					preview.fonts();
				}
			});
		});
		$('#xl-heading_case, #xl-heading_scale').on('change input', function () {
			if (preview) {
				preview.fonts();
			}
		});

		// Reset to the X LIGHTS defaults (still needs Save).
		$('[data-xl-design-reset]').on('click', function () {
			$('[data-xl-design]').each(function () {
				var $input = $(this);
				if ($.fn.wpColorPicker && $input.hasClass('wp-color-picker')) {
					$input.wpColorPicker('color', $input.data('default-color'));
				} else {
					$input.val($input.data('default-color'));
				}
			});
			$('[data-xl-font] select').val('').trigger('change');
			$('[data-xl-font] .xl-font-field__custom input').val('');
			$('#xl-heading_case').val('uppercase');
			$('#xl-heading_scale').val(100);
			if (preview) {
				preview.colors();
				preview.fonts();
			}
		});

		if (preview) {
			preview.colors();
			preview.fonts();
		}

		// Single image fields.
		$(document).on('click', '[data-xl-media-pick]', function (e) {
			e.preventDefault();
			var $wrap = $(this).closest('[data-xl-media]');
			var frame = wp.media({
				title: L.choose || 'Choose image',
				button: { text: L.use || 'Use image' },
				library: { type: 'image' },
				multiple: false
			});
			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				var url = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
				$wrap.find('input[type=hidden]').val(att.id).trigger('change');
				$wrap.find('.xl-media-field__preview').html($('<img alt="">').attr('src', url));
				$wrap.find('[data-xl-media-clear]').prop('hidden', false);
			});
			frame.open();
		});
		$(document).on('click', '[data-xl-media-clear]', function (e) {
			e.preventDefault();
			var $wrap = $(this).closest('[data-xl-media]');
			$wrap.find('input[type=hidden]').val('0').trigger('change');
			$wrap.find('.xl-media-field__preview').empty();
			$(this).prop('hidden', true);
		});

		// Gallery manager.
		$('[data-xl-gallery]').each(function () {
			var $box = $(this);
			var $input = $box.find('[data-xl-gallery-input]');
			var $list = $box.find('[data-xl-gallery-list]');
			function sync() {
				var ids = $list.children('li').map(function () {
					return $(this).data('id');
				}).get();
				$input.val(ids.join(','));
			}
			if ($.fn.sortable) {
				$list.sortable({ update: sync });
			}
			$list.on('click', '.xl-gallery-field__remove', function () {
				$(this).closest('li').remove();
				sync();
			});
			$box.find('[data-xl-gallery-add]').on('click', function (e) {
				e.preventDefault();
				var frame = wp.media({
					title: L.chooseMany || 'Choose gallery images',
					button: { text: L.useMany || 'Add to gallery' },
					library: { type: 'image' },
					multiple: 'add'
				});
				frame.on('select', function () {
					frame.state().get('selection').each(function (att) {
						var a = att.toJSON();
						if ($list.children('li[data-id="' + a.id + '"]').length) {
							return;
						}
						var thumb = a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;
						var $li = $('<li>').attr('data-id', a.id);
						$li.append($('<img alt="">').attr('src', thumb));
						$li.append($('<button type="button" class="xl-gallery-field__remove">&times;</button>').attr('aria-label', L.remove || 'Remove'));
						$list.append($li);
					});
					sync();
				});
				frame.open();
			});
		});

		// Setup page: select all.
		$('[data-xl-check-all]').on('change', function () {
			$(this).closest('table').find('input[name="pages[]"]').prop('checked', this.checked);
		});
	});

	/**
	 * Live preview for Settings → Colors & Fonts. Colors are derived on the server
	 * (the same code that styles the site); fonts are applied directly.
	 */
	function designPreview($preview) {
		var el = $preview[0];
		var cfg = $preview.data('config') || {};
		var $checks = $preview.find('[data-xl-design-checks]');
		var set = [];
		var timer = null;
		var seq = 0;

		function stack(role) {
			var $wrap = $('[data-xl-font][data-role="' + role + '"]');
			var $select = $wrap.find('select');
			if ($select.val() === '__custom') {
				var name = $.trim($wrap.find('.xl-font-field__custom input').val()).replace(/["'\\;{}<>]/g, '');
				return name ? '"' + name + '", sans-serif' : '';
			}
			return $select.val() ? String($select.find('option:selected').data('stack') || '') : '';
		}

		function prop(name, value) {
			if (value) {
				el.style.setProperty(name, value);
			} else {
				el.style.removeProperty(name);
			}
		}

		return {
			colors: function () {
				clearTimeout(timer);
				timer = setTimeout(function () {
					var mine = ++seq;
					var colors = {};
					$('[data-xl-design]').each(function () {
						colors[$(this).data('xl-design')] = $(this).val();
					});
					$.post(cfg.ajax, { action: 'xlights_design_tokens', _ajax_nonce: cfg.nonce, colors: colors }).done(function (res) {
						if (mine !== seq || !res || !res.success) {
							return;
						}
						set.forEach(function (name) {
							el.style.removeProperty(name);
						});
						set = [];
						$.each(res.data.tokens || {}, function (name, value) {
							el.style.setProperty(name, value);
							set.push(name);
						});
						$checks.empty();
						if (res.data.warnings && res.data.warnings.length) {
							res.data.warnings.forEach(function (msg) {
								$checks.append($('<p>').text(msg));
							});
						} else {
							$checks.append($('<p class="is-ok">').text(L.contrastOk || 'Text contrast passes WCAG AA for this palette.'));
						}
					});
				}, 200);
			},
			fonts: function () {
				prop('--xl-font-heading', stack('heading'));
				prop('--xl-font-body', stack('body'));
				prop('--xl-heading-case', $('#xl-heading_case').val() === 'none' ? 'none' : '');
				var scale = parseInt($('#xl-heading_scale').val(), 10);
				prop('--xl-heading-scale', scale && scale !== 100 ? String(Math.max(0.6, Math.min(1.4, scale / 100))) : '');
			}
		};
	}
})(jQuery);
