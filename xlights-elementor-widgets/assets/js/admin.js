/*!
 * X LIGHTS admin: media pickers, project gallery manager, color pickers.
 */
(function ($) {
	'use strict';
	var L = window.XLightsAdmin || {};

	$(function () {
		if ($.fn.wpColorPicker) {
			$('.xl-color').wpColorPicker();
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
})(jQuery);
