/**
 * Admin Visual Customizer Controller.
 */
(function($) {
	'use strict';

	$(document).ready(function() {
		var $previewWrapper = $('.wcbs-preview-wrapper');
		var $previewContent = $('#wcbs-live-preview-content');
		var $form           = $('#wcbs-customizer-form');
		var presets         = wcbs_admin_data.presets || {};

		// 1. Navigation Tabs
		$('.wcbs-tab-link').on('click', function(e) {
			e.preventDefault();
			var targetTab = $(this).data('tab');
			$('.wcbs-tab-link').removeClass('active');
			$(this).addClass('active');
			$('.wcbs-tab-pane').removeClass('active');
			$('#' + targetTab).addClass('active');
		});

		// 2. Device Preview Switcher
		$('.wcbs-device-btn').on('click', function(e) {
			e.preventDefault();
			var device = $(this).data('device');
			$('.wcbs-device-btn').removeClass('active');
			$(this).addClass('active');
			$previewWrapper.attr('data-device', device);
		});

		// 3. Initialize WordPress Color Pickers with live change hooks
		$('.wcbs-color-picker').each(function() {
			var $input = $(this);
			$input.wpColorPicker({
				change: function(event, ui) {
					var color = ui.color.toString();
					var id    = $input.attr('id');
					updateCssVariableFromColor(id, color);
				}
			});
		});

		// Helper to update CSS variable from color picker ID
		function updateCssVariableFromColor(id, color) {
			var varMap = {
				'primary_color': '--wcbs-primary',
				'primary_hover': '--wcbs-primary-hover',
				'accent_color': '--wcbs-accent',
				'calendar_bg': '--wcbs-cal-bg',
				'calendar_header_bg': '--wcbs-cal-header-bg',
				'day_available_bg': '--wcbs-day-avail-bg',
				'day_selected_bg': '--wcbs-day-sel-bg',
				'day_booked_bg': '--wcbs-day-booked-bg',
				'slot_bg': '--wcbs-slot-bg',
				'slot_selected_bg': '--wcbs-slot-selected-bg'
			};

			if (varMap[id]) {
				document.documentElement.style.setProperty(varMap[id], color);
				$previewContent[0].style.setProperty(varMap[id], color);
			}
		}

		// 4. Layout Switcher
		$('input[name="default_layout"]').on('change', function() {
			var layout = $(this).val();
			$('.wcbs-layout-card').removeClass('active');
			$(this).closest('.wcbs-layout-card').addClass('active');

			// Update preview container classes
			$previewContent.attr('data-wcbs-layout', layout);
			$previewContent.removeClass('wcbs-layout-split wcbs-layout-wizard wcbs-layout-drawer wcbs-layout-modal wcbs-layout-bottom-sheet wcbs-layout-standard')
			               .addClass('wcbs-layout-' + layout);

			// Toggle layout-specific elements in preview
			if (layout === 'split') {
				$previewContent.find('.wcbs-layout-grid').addClass('wcbs-split-grid');
				$previewContent.find('.wcbs-split-only').show();
				$previewContent.find('.wcbs-wizard-nav').hide();
			} else if (layout === 'wizard') {
				$previewContent.find('.wcbs-layout-grid').removeClass('wcbs-split-grid');
				$previewContent.find('.wcbs-split-only').hide();
				$previewContent.find('.wcbs-wizard-nav').show();
			} else {
				$previewContent.find('.wcbs-layout-grid').removeClass('wcbs-split-grid');
				$previewContent.find('.wcbs-split-only').hide();
				$previewContent.find('.wcbs-wizard-nav').hide();
			}
		});

		// 5. Theme Presets Switcher
		$('input[name="active_theme"]').on('change', function() {
			var themeKey = $(this).val();
			$('.wcbs-preset-card').removeClass('active');
			$(this).closest('.wcbs-preset-card').addClass('active');

			$previewContent.attr('data-wcbs-theme', themeKey);
			$previewContent.removeClass('wcbs-theme-clean wcbs-theme-dark wcbs-theme-luxury wcbs-theme-vibrant wcbs-theme-custom')
			               .addClass('wcbs-theme-' + themeKey);

			if (themeKey !== 'custom' && presets[themeKey]) {
				var p = presets[themeKey];
				// Update form color pickers
				$.each(p, function(k, v) {
					var $picker = $('#' + k);
					if ($picker.length && $picker.hasClass('wcbs-color-picker')) {
						$picker.wpColorPicker('color', v);
					}
				});

				// Update live preview style
				requestPreviewCss();
			}
		});

		// 6. Day Shape Selector
		$('input[name="day_shape"]').on('change', function() {
			var shape = $(this).val();
			$('.wcbs-shape-opt').removeClass('active');
			$(this).closest('.wcbs-shape-opt').addClass('active');

			var radius = '50%';
			if (shape === 'rounded') radius = '8px';
			else if (shape === 'square') radius = '0px';
			else if (shape === 'pill') radius = '24px';

			$previewContent[0].style.setProperty('--wcbs-day-radius', radius);
		});

		// 7. Border Radius Slider Sync
		$('#border_radius_base_range').on('input', function() {
			var val = $(this).val();
			$('#border_radius_base').val(val);
			$previewContent[0].style.setProperty('--wcbs-radius', val + 'px');
		});
		$('#border_radius_base').on('input', function() {
			var val = $(this).val();
			$('#border_radius_base_range').val(val);
			$previewContent[0].style.setProperty('--wcbs-radius', val + 'px');
		});

		// 8. Slot Style Selector
		$('#slot_style').on('change', function() {
			var style = $(this).val();
			$previewContent.find('.wcbs-slots-container')
			               .removeClass('wcbs-style-chips wcbs-style-grid wcbs-style-list')
			               .addClass('wcbs-style-' + style);
		});

		// 9. Feature Toggles Real-time display
		$('input[name="enable_staff_cards"]').on('change', function() {
			$previewContent.find('.wcbs-resource-cards-section').toggle($(this).is(':checked'));
		});

		$('input[name="enable_slot_tabs"]').on('change', function() {
			$previewContent.find('.wcbs-slot-tabs-nav').toggle($(this).is(':checked'));
		});

		$('input[name="enable_timezone"]').on('change', function() {
			$previewContent.find('.wcbs-timezone-bar').toggle($(this).is(':checked'));
		});

		$('input[name="enable_urgency_badge"]').on('change', function() {
			$previewContent.find('.wcbs-urgency-badge').toggle($(this).is(':checked'));
		});

		$('input[name="enable_calendar_sync"]').on('change', function() {
			$previewContent.find('.wcbs-add-to-calendar-block').toggle($(this).is(':checked'));
		});

		// 10. Request dynamic CSS from server for complex updates
		function requestPreviewCss() {
			var formData = $form.serializeArray();
			var settingsObj = {};
			$.each(formData, function(i, field) {
				settingsObj[field.name] = field.value;
			});

			$.ajax({
				url: wcbs_admin_data.ajax_url,
				type: 'POST',
				data: {
					action: 'wcbs_get_preview_styles',
					nonce: wcbs_admin_data.nonce,
					settings: settingsObj
				},
				success: function(res) {
					if (res.success && res.data.css) {
						var $styleTag = $('#wcbs-dynamic-preview-styles');
						if (!$styleTag.length) {
							$styleTag = $('<style id="wcbs-dynamic-preview-styles"></style>').appendTo('head');
						}
						$styleTag.html(res.data.css);
					}
				}
			});
		}

		// 11. Save Changes AJAX
		$('#wcbs-save-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var $spinner = $btn.find('.wcbs-btn-spinner');
			var $text = $btn.find('.wcbs-btn-text');

			$btn.prop('disabled', true);
			$spinner.show();
			$text.text(wcbs_admin_data.i18n.saving);

			var formData = $form.serializeArray();
			var settingsObj = {};
			$.each(formData, function(i, field) {
				settingsObj[field.name] = field.value;
			});

			// Handle unchecked checkboxes
			$form.find('input[type="checkbox"]').each(function() {
				if (!$(this).is(':checked')) {
					settingsObj[$(this).attr('name')] = 'no';
				}
			});

			$.ajax({
				url: wcbs_admin_data.ajax_url,
				type: 'POST',
				data: {
					action: 'wcbs_save_customizer_settings',
					nonce: wcbs_admin_data.nonce,
					settings: settingsObj
				},
				success: function(res) {
					$spinner.hide();
					$btn.prop('disabled', false);
					$text.text(wcbs_admin_data.i18n.saved);
					setTimeout(function() {
						$text.text('Save Changes');
					}, 2500);
				},
				error: function() {
					$spinner.hide();
					$btn.prop('disabled', false);
					$text.text(wcbs_admin_data.i18n.error);
				}
			});
		});

		// 12. Reset Defaults AJAX
		$('#wcbs-reset-btn').on('click', function(e) {
			e.preventDefault();
			if (!confirm(wcbs_admin_data.i18n.confirm_reset)) {
				return;
			}

			var $btn = $(this);
			$btn.prop('disabled', true).text(wcbs_admin_data.i18n.resetting);

			$.ajax({
				url: wcbs_admin_data.ajax_url,
				type: 'POST',
				data: {
					action: 'wcbs_reset_customizer_settings',
					nonce: wcbs_admin_data.nonce
				},
				success: function() {
					location.reload();
				}
			});
		});
	});

})(jQuery);
