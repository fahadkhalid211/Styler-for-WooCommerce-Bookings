/**
 * Frontend Main Controller for Styler for WooCommerce Bookings.
 */
(function($) {
	'use strict';

	window.WCBSFrontend = {
		isSplitReady: false,
		isCalculatingCosts: false,
		isInternalSync: false,
		calcTimer: null,
		dispatchTimer: null,

		init: function() {
			var self = this;
			var $container = $('.wcbs-root-container');
			if (!$container.length) return;

			var layout = $container.data('wcbs-layout') || (typeof wcbs_params !== 'undefined' ? wcbs_params.layout : 'split');

			if (layout === 'split') {
				self.setupSplitLayout($container);
			}

			self.initStaffCards($container);
			self.initDatepickerListeners($container);
			self.initPersonsAndDurationListeners($container);
			self.initAjaxWatchdogs($container);
			self.observeCostCalculations($container);

			if (window.console && console.log) {
				console.log('[WCBS Diagnostics] Initialized.', {
					layout: layout,
					isTimeBased: self.isTimeBasedProduct($container),
					bookingForm: $('#wc-bookings-booking-form').length,
					fieldsets: $('fieldset').map(function() { return this.className; }).get()
				});
			}
		},

		// Check whether current product involves hours/times or is date-only (days/months)
		isTimeBasedProduct: function($container) {
			if (!$container || !$container.length) $container = $('.wcbs-root-container');
			var $form = $container.find('form.cart, #wc-bookings-booking-form');
			if (!$form.length) $form = $('form.cart, #wc-bookings-booking-form');
			return $form.find('.wc-bookings-time-block-picker, .time-picker-fieldset, select[name*="time"], input[name*="time"], ul.block-picker, ul.blocks').length > 0;
		},

		// Centralized safe & debounced cost calculation
		triggerCostCalculation: function($container) {
			var self = this;
			if (!$container || !$container.length) $container = $('.wcbs-root-container');

			clearTimeout(self.calcTimer);
			self.calcTimer = setTimeout(function() {
				var $f = $('form.cart, form.wc-bookings-booking-form, #wc-bookings-booking-form').first();
				var ajaxUrl = (window.booking_form_params && booking_form_params.ajax_url) ? booking_form_params.ajax_url : ((typeof wcbs_params !== 'undefined') ? wcbs_params.ajax_url : '');
				if (!ajaxUrl || !$f.length || self.isCalculatingCosts) return;

				// Verify date fields are populated before calculating
				var hasDate = false;
				var y = $f.find('input[name*="start_date_year"]').val();
				var m = $f.find('input[name*="start_date_month"]').val();
				var d = $f.find('input[name*="start_date_day"]').val();
				if (y && m && d) hasDate = true;
				if (!hasDate) return;

				// Trigger change directly on inputs/selects so native WooCommerce Bookings also calculates
				$f.find('input.required_for_calculation, select').each(function() {
					if ($(this).val()) {
						$(this).trigger('change');
					}
				});

				self.isCalculatingCosts = true;
				$.ajax({
					type: 'POST',
					url: ajaxUrl,
					data: {
						action: 'wc_bookings_calculate_costs',
						form: $f.serialize()
					},
					success: function(resp) {
						self.isCalculatingCosts = false;
						self.handleCostResponse(resp, $container);
					},
					error: function(xhr) {
						self.isCalculatingCosts = false;
						if (xhr && xhr.responseText) {
							self.handleCostResponse(xhr.responseText, $container);
						}
					}
				});
			}, 200);
		},

		// Robust parser for WooCommerce Bookings cost responses (handles JSON objects, JSON strings, and HTML)
		handleCostResponse: function(resp, $container) {
			if (!resp) return;
			if (!$container || !$container.length) $container = $('.wcbs-root-container');

			var costHtml = '';
			var isSuccess = true;

			if (typeof resp === 'object') {
				if (resp.result === 'ERROR') {
					isSuccess = false;
				}
				costHtml = resp.html || resp.message || '';
			} else if (typeof resp === 'string') {
				try {
					var parsed = JSON.parse(resp);
					if (parsed && typeof parsed === 'object') {
						if (parsed.result === 'ERROR') isSuccess = false;
						costHtml = parsed.html || parsed.message || '';
					} else {
						costHtml = resp;
					}
				} catch (e) {
					costHtml = resp;
				}
			}

			if (!costHtml || typeof costHtml !== 'string') return;
			var clean = costHtml.trim();
			var lower = clean.toLowerCase();

			// Strictly reject fatal errors, database errors, or full HTML dumps
			if (lower.indexOf('fatal error') !== -1 || lower.indexOf('database') !== -1 || lower.indexOf('wp-die') !== -1 || lower.indexOf('operation not permitted') !== -1 || lower.indexOf('<html') !== -1) {
				return;
			}

			var $costBox = $('.wc-bookings-booking-cost');
			var $priceDisplay = $container.find('.wcbs-price-display');
			var $bookBtn = $('button.single_add_to_cart_button, .single_add_to_cart_button, button.wc-bookings-booking-form-button');

			if (isSuccess && (clean.indexOf('amount') !== -1 || clean.indexOf('$') !== -1 || clean.indexOf('£') !== -1 || clean.indexOf('€') !== -1 || /\d/.test(clean))) {
				$costBox.html(clean).show();
				// Strip leading "Total:" or "Booking Cost:" for the live summary card display
				var displayPrice = clean.replace(/^Total:?\s*/i, '').replace(/^Booking Cost:?\s*/i, '');
				$priceDisplay.html(displayPrice);
				$bookBtn.prop('disabled', false).removeClass('disabled').removeAttr('disabled');
			} else if (!isSuccess && clean.length > 0) {
				$costBox.html(clean).show();
				$bookBtn.prop('disabled', true).addClass('disabled');
			}
		},

		// 1. Two-Column Split Layout Architecture
		setupSplitLayout: function($container) {
			var self = this;
			var $form = $container.find('form.cart');
			if (!$form.length) {
				$form = $('form.cart');
			}
			if (!$form.length || self.isSplitReady || $form.hasClass('wcbs-split-ready')) return;

			var $bookingForm = $form.find('#wc-bookings-booking-form');
			var $gridTarget  = $bookingForm.length ? $bookingForm : $form;

			self.isSplitReady = true;
			$form.addClass('wcbs-split-form');
			$gridTarget.addClass('wcbs-split-grid wcbs-split-ready');

			// Extract timezone string
			var detectedTz = 'UTC';
			var $tzEl = $container.find('.wc-bookings-date-picker-timezone, .wcbs-current-timezone').first();
			if ($tzEl.length && $tzEl.text().trim() && $tzEl.text().trim() !== 'Detecting timezone...') {
				detectedTz = $tzEl.text().trim();
			} else {
				try {
					var resolved = Intl.DateTimeFormat().resolvedOptions().timeZone;
					if (resolved) detectedTz = resolved;
				} catch (e) {}
			}

			var isTimeBased = self.isTimeBasedProduct($container);

			// Create Left & Right column containers
			var $leftCol = $('<div class="wcbs-split-col-left"></div>');
			var $rightCol = $('<div class="wcbs-split-col-right"></div>');

			// Left Column Header with inline timezone badge (takes ZERO vertical rows/space)
			$leftCol.append(
				'<div class="wcbs-col-header">' +
					'<div class="wcbs-col-header-left">' +
						'<span class="wcbs-col-step-tag">Step 1</span>' +
						'<h3 class="wcbs-col-title">Select Date</h3>' +
					'</div>' +
					'<span class="wcbs-header-tz-badge" title="Times are displayed in ' + detectedTz + '">' +
						'<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>' +
						'<span class="wcbs-header-tz-name">' + detectedTz + '</span>' +
					'</span>' +
				'</div>'
			);

			// Move Staff/Resource Cards if present into Left Column
			var $staffCards = $container.find('.wcbs-resource-cards-section');
			if ($staffCards.length) {
				$leftCol.append($staffCards);
			}

			// Move Datepicker into Left Column (remains inside bookingForm context)
			var $datePicker = $form.find('.wc-bookings-date-picker');
			if ($datePicker.length) {
				$leftCol.append($datePicker);
			}

			// Right Column Header (Context-aware: Time vs Date-only)
			var step2Title = isTimeBased ? 'Select Time & Confirm' : 'Booking Details & Confirm';
			$rightCol.append(
				'<div class="wcbs-col-header">' +
					'<span class="wcbs-col-step-tag">Step 2</span>' +
					'<h3 class="wcbs-col-title">' + step2Title + '</h3>' +
				'</div>'
			);

			// Slots & Time Dropdowns Section in Right Column
			var $slotsSection = $(
				'<div class="wcbs-right-slots-section">' +
					'<div class="wcbs-time-dropdowns-wrapper" style="display:none;"></div>' +
					'<div class="wcbs-slots-prompt">' +
						'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' +
						'<span>Choose an available date on the calendar to view available times.</span>' +
					'</div>' +
					'<div class="wcbs-slots-holder">' +
						'<ul class="block-picker wcbs-slots-display wcbs-hidden"></ul>' +
						'<div class="wcbs-slots-empty-notice" style="display:none;"></div>' +
					'</div>' +
				'</div>'
			);

			if (isTimeBased) {
				$rightCol.append($slotsSection);
			}

			// Move time picker fieldset / block-picker container into $slotsSection inside $rightCol
			var $timePickerBlock = $form.find('.time-picker-fieldset, .form-field:has(.wc-bookings-time-block-picker), .wc-bookings-time-block-picker');
			if ($timePickerBlock.length) {
				$slotsSection.find('.wcbs-time-dropdowns-wrapper').append($timePickerBlock);
			}

			// Move persons / add-on fieldsets if any into Right Column (excluding date and time pickers)
			var $otherFieldsets = $form.find('#wc-bookings-booking-form fieldset:not(.wc-bookings-date-picker):not(.time-picker-fieldset)');
			if ($otherFieldsets.length) {
				$rightCol.append($otherFieldsets);
			}

			// Move Summary Card into Right Column
			var $summaryCard = $container.find('.wcbs-summary-card');
			if ($summaryCard.length) {
				$rightCol.append($summaryCard);
			}

			// Move booking cost into Right Column so it doesn't disrupt the two-column grid
			var $costBox = $form.find('.wc-bookings-booking-cost');
			if ($costBox.length) {
				$rightCol.append($costBox);
			}

			// Move Submit Button into Right Column
			var $submitBtn = $form.find('.single_add_to_cart_button');
			if ($submitBtn.length) {
				$rightCol.append($submitBtn);
			}

			// Move Add to Calendar block into Right Column
			var $calSync = $container.find('.wcbs-add-to-calendar-block');
			if ($calSync.length) {
				$rightCol.append($calSync);
			}

			// Prepend Left and append Right columns to grid target
			$gridTarget.prepend($leftCol);
			$gridTarget.append($rightCol);

			// CRITICAL: Ensure $gridTarget only has 2 direct children ($leftCol and $rightCol)
			$gridTarget.children().not($leftCol).not($rightCol).each(function() {
				var $child = $(this);
				if ($child.is('input[type="hidden"]')) {
					$leftCol.append($child);
				} else if ($child.find('.time-picker-fieldset, .wc-bookings-time-block-picker, select[name*="time"], select[name*="duration"]').length) {
					$rightCol.find('.wcbs-time-dropdowns-wrapper').append($child);
				} else if ($child.is(':empty')) {
					$child.remove();
				} else {
					$rightCol.append($child);
				}
			});

			// If not time-based, hide time row in summary card
			if (!isTimeBased) {
				$container.find('.wcbs-summary-time').hide();
			}

			// Check for existing slots or dropdowns on initial load
			self.syncTimeSlots($container);
		},

		// 2. Staff / Resource Visual Cards
		initStaffCards: function($container) {
			$container.on('click', '.wcbs-resource-card', function(e) {
				e.preventDefault();
				var $card = $(this);
				var resId = $card.data('resource-id');
				var resName = $card.find('.wcbs-resource-name').text();

				// Update active card state
				$container.find('.wcbs-resource-card').removeClass('active').attr('aria-checked', 'false');
				$card.addClass('active').attr('aria-checked', 'true');

				// Update core hidden resource select input
				var $resSelect = $('select[name="wc_bookings_field_resource"]');
				if ($resSelect.length) {
					$resSelect.val(resId).trigger('change');
				}

				// Update Summary Card
				var $row = $container.find('.wcbs-summary-resource');
				$row.show();
				$row.find('.wcbs-val-resource').text(resName);
			});
		},

		// 3. User-Defined Hours (Start & End Time Dropdowns)
		syncDropdowns: function($container) {
			var self = this;
			if (!$container || !$container.length) {
				$container = $('.wcbs-root-container');
			}

			// Look for all selects related to time or duration in the booking form
			// EXCLUDE resource, persons, and addon selects
			var $allSelects = $container.find('select').filter(function() {
				var name = (this.name || '').toLowerCase();
				var id = (this.id || '').toLowerCase();
				if (name.indexOf('resource') !== -1 || id.indexOf('resource') !== -1) return false;
				if (name.indexOf('person') !== -1 || id.indexOf('person') !== -1) return false;
				if (name.indexOf('addon') !== -1 || id.indexOf('addon') !== -1) return false;

				return name.indexOf('time') !== -1 || name.indexOf('duration') !== -1 ||
				       id.indexOf('time') !== -1 || id.indexOf('duration') !== -1 ||
				       $(this).closest('.wc-bookings-time-block-picker, .time-picker-fieldset').length > 0;
			});

			if (!$allSelects.length) {
				return false;
			}

			var $wrapper = $container.find('.wcbs-time-dropdowns-wrapper');
			if (!$wrapper.length) {
				$wrapper = $('<div class="wcbs-time-dropdowns-wrapper"></div>');
				var $slotsSec = $container.find('.wcbs-right-slots-section');
				if ($slotsSec.length) {
					$slotsSec.prepend($wrapper);
				} else {
					$container.find('.wcbs-split-col-right').prepend($wrapper);
				}
			}

			// STRICT PURGE: Clear any duplicate selects accidentally injected into ul.wcbs-slots-display
			var $disp = $container.find('ul.wcbs-slots-display');
			if ($disp.length && $disp.find('select').length) {
				$disp.empty().addClass('wcbs-hidden').hide();
			}

			// Suppress regular block-picker slots UI when dropdowns are in use
			$container.find('.wcbs-slots-prompt').hide();
			$container.find('.wcbs-slots-empty-notice').hide();
			$container.find('.wcbs-slots-holder').hide();
			$wrapper.show().css('display', 'grid');

			// Separate Start and End/Duration selects
			var $startSelect = $allSelects.filter(function() {
				var n = (this.name || '').toLowerCase();
				var id = (this.id || '').toLowerCase();
				return n.indexOf('start') !== -1 || n.indexOf('time') !== -1 || id.indexOf('start') !== -1 || id.indexOf('time') !== -1;
			}).first();

			var $endSelect = $allSelects.filter(function() {
				var n = (this.name || '').toLowerCase();
				var id = (this.id || '').toLowerCase();
				return (n.indexOf('duration') !== -1 || n.indexOf('end') !== -1 || id.indexOf('duration') !== -1 || id.indexOf('end') !== -1) && this !== $startSelect[0];
			}).first();

			if (!$startSelect.length && $allSelects.length >= 1) {
				$startSelect = $allSelects.eq(0);
			}
			if (!$endSelect.length && $allSelects.length >= 2) {
				$endSelect = $allSelects.eq(1);
			}

			// PURGE only extra duplicate selects in the DOM
			$allSelects.each(function() {
				if (this !== $startSelect[0] && (!$endSelect.length || this !== $endSelect[0])) {
					var $dup = $(this).closest('.wcbs-time-dropdown-field');
					if ($dup.length) {
						$dup.remove();
					} else {
						$(this).remove();
					}
				}
			});

			// Helper to wrap each select into its own sleek field container with SVG icon and custom dropdown
			var wrapDropdown = function($sel, fieldClass, labelText, defaultId) {
				if (!$sel || !$sel.length) return null;

				var currentId = $sel.attr('id') || defaultId;
				$sel.attr('id', currentId);

				var $field = $sel.closest('.wcbs-time-dropdown-field');
				if (!$field.length) {
					$field = $('<div class="wcbs-time-dropdown-field ' + fieldClass + '"></div>');
					var $lbl = $sel.prev('label');
					if (!$lbl.length) {
						$lbl = $sel.parent().find('label[for="' + currentId + '"]');
					}
					if (!$lbl.length) {
						$lbl = $('<label for="' + currentId + '">' + labelText + '</label>');
					}
					$sel.before($field);
					$field.append($lbl).append($sel);
				} else {
					$field.addClass(fieldClass);
				}

				var $label = $field.find('label');
				if (!$label.find('svg').length) {
					var cleanText = labelText || $label.text().trim() || 'Time';
					$label.html(
						'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
							'<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>' +
						'</svg> ' +
						'<span>' + cleanText + '</span>'
					);
				}

				if (!$wrapper.has($field).length) {
					$wrapper.append($field);
				}

				$field.show().removeAttr('style').css({ display: 'flex', visibility: 'visible' });

				// Build sleek custom dropdown menu for this select
				self.buildCustomSelect($sel, labelText, currentId);

				return $field;
			};

			wrapDropdown($startSelect, 'wcbs-field-start-time', 'Starts', 'wc_bookings_field_start_date_time');
			if ($endSelect.length) {
				wrapDropdown($endSelect, 'wcbs-field-end-time', 'Ends', 'wc_bookings_field_duration');
			}

			// Central function to process selection and update calculations (debounced & non-recursive)
			var onDropdownChanged = function() {
				var $s = $startSelect.length ? $startSelect : $container.find('.wcbs-field-start-time select').first();
				var $e = $endSelect.length ? $endSelect : $container.find('.wcbs-field-end-time select').first();

				var sVal = $s.length ? ($s.val() || '') : '';
				var eVal = $e.length ? ($e.val() || '') : '';

				var sText = ($s.length && $s.find('option:selected').length) ? $s.find('option:selected').text().trim() : '';
				var eText = ($e.length && $e.find('option:selected').length) ? $e.find('option:selected').text().trim() : '';

				// Update live summary
				var summaryTime = '';
				var isValidStart = sVal && sText.toLowerCase().indexOf('time') === -1 && sText.toLowerCase().indexOf('choose') === -1 && sText.toLowerCase().indexOf('select') === -1;
				var isValidEnd   = eVal && eText.toLowerCase().indexOf('time') === -1 && eText.toLowerCase().indexOf('choose') === -1 && eText.toLowerCase().indexOf('select') === -1;

				if (isValidStart && isValidEnd) {
					summaryTime = sText + ' – ' + eText;
				} else if (isValidStart) {
					summaryTime = sText;
				}

				if (summaryTime) {
					$container.find('.wcbs-val-time').text(summaryTime);
					$container.find('.wcbs-summary-time').show();
				}

				// Calculate numeric duration in units (e.g. hours) if eVal is a time string
				var numericDuration = 1;
				if (eVal) {
					if (eVal.indexOf(':') !== -1 && sVal && sVal.indexOf(':') !== -1) {
						var sP = sVal.split(':');
						var eP = eVal.split(':');
						var sMin = parseInt(sP[0], 10) * 60 + parseInt(sP[1], 10);
						var eMin = parseInt(eP[0], 10) * 60 + parseInt(eP[1], 10);
						var diffMin = eMin - sMin;
						if (diffMin > 0) {
							numericDuration = Math.round(diffMin / 60);
							if (numericDuration < 1) numericDuration = 1;
						}
					} else {
						var parsed = parseInt(eVal, 10);
						if (!isNaN(parsed) && parsed > 0) {
							numericDuration = parsed;
						}
					}
				}

				// Synchronize values into core hidden inputs
				if (sVal) {
					$container.find('input[name="wc_bookings_field_start_date_time"], input#wc_bookings_field_start_date, input.booking_date_time').val(sVal);
				}
				$container.find('input[name="wc_bookings_field_duration"], input.wc_bookings_field_duration').val(numericDuration);
				$container.find('input[name="wc_bookings_field_end_date_time"], input#wc_bookings_field_end_date_time').val(eVal);

				// Trigger native form calculation directly on input/select elements
				if (!self.isInternalSync) {
					self.isInternalSync = true;
					$container.find('input.required_for_calculation, select').trigger('change');
					$('#wc-bookings-booking-form').trigger('change').trigger('wc_booking_form_changed');
					setTimeout(function() {
						self.isInternalSync = false;
					}, 150);
				}

				// Activate Book Now and calculate costs when requirements are met
				var needsEnd = $e.length > 0;
				var isComplete = isValidStart && (!needsEnd || isValidEnd);

				if (isComplete) {
					var $bookBtn = $('button.single_add_to_cart_button, .single_add_to_cart_button, button.wc-bookings-booking-form-button');
					$bookBtn.prop('disabled', false).removeClass('disabled').removeAttr('disabled');
					self.triggerCostCalculation($container);

					if (window.WCBSCalendarSync && window.WCBSCalendarSync.update) {
						var dateText = $container.find('.wcbs-val-date').text();
						var prodTitle = $container.find('.wcbs-summary-title').text() || document.title;
						window.WCBSCalendarSync.update({
							title: prodTitle,
							date: dateText,
							time: summaryTime
						});
					}
				}
			};

			// Bind change listener once
			if (!$wrapper.data('wcbs-bound')) {
				$wrapper.data('wcbs-bound', true);
				$container.on('change input', '.wcbs-time-dropdowns-wrapper select, .wc-bookings-time-block-picker select', onDropdownChanged);
			}

			return true;
		},

		// 3b. Custom Sleek Dropdown Builder (accessible, beautiful floating menu)
		buildCustomSelect: function($select, labelText, defaultId) {
			var self = this;
			if (!$select || !$select.length) return;

			var $existing = $select.siblings('.wcbs-custom-select-container');
			if ($existing.length) {
				self.syncCustomSelectOptions($select, $existing);
				return;
			}

			var selId = $select.attr('id') || defaultId;
			$select.attr('id', selId);

			// Hide native select visually while keeping it fully functional in DOM
			$select.addClass('wcbs-native-select-hidden');

			var $customContainer = $('<div class="wcbs-custom-select-container"></div>');
			var $trigger = $(
				'<button type="button" class="wcbs-custom-select-trigger" aria-haspopup="listbox" aria-expanded="false">' +
					'<span class="wcbs-custom-select-value"></span>' +
					'<svg class="wcbs-custom-select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>' +
				'</button>'
			);
			var $menu = $('<div class="wcbs-custom-select-menu" role="listbox" style="display:none;"></div>');

			$customContainer.append($trigger).append($menu);
			$select.after($customContainer);

			self.syncCustomSelectOptions($select, $customContainer);

			// Toggle dropdown open/close
			$trigger.on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				if ($trigger.prop('disabled') || $customContainer.hasClass('wcbs-disabled')) return;

				var isOpen = $customContainer.hasClass('wcbs-open');
				$('.wcbs-custom-select-container.wcbs-open').not($customContainer).removeClass('wcbs-open').find('.wcbs-custom-select-menu').hide();
				$('.wcbs-custom-select-trigger').attr('aria-expanded', 'false');

				if (isOpen) {
					$customContainer.removeClass('wcbs-open wcbs-dropup');
					$trigger.attr('aria-expanded', 'false');
					$menu.hide();
				} else {
					// Check viewport boundary to flip upward if near screen bottom
					var triggerOffset = $trigger.offset();
					if (triggerOffset) {
						var spaceBelow = $(window).height() - (triggerOffset.top - $(window).scrollTop() + $trigger.outerHeight());
						if (spaceBelow < 230 && (triggerOffset.top - $(window).scrollTop()) > 230) {
							$customContainer.addClass('wcbs-dropup');
						} else {
							$customContainer.removeClass('wcbs-dropup');
						}
					}

					$customContainer.addClass('wcbs-open');
					$trigger.attr('aria-expanded', 'true');
					$menu.show();
				}
			});

			// Keyboard navigation
			$trigger.on('keydown', function(e) {
				if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
					e.preventDefault();
					if (!$customContainer.hasClass('wcbs-open')) {
						$trigger.trigger('click');
					} else {
						var $items = $menu.find('.wcbs-custom-select-item:not(.wcbs-placeholder-item)');
						var $cur = $items.filter('.wcbs-selected');
						var idx = $items.index($cur);
						if (e.key === 'ArrowDown') {
							var next = (idx + 1 < $items.length) ? idx + 1 : 0;
							$items.eq(next).trigger('click');
						} else if (e.key === 'ArrowUp') {
							var prev = (idx - 1 >= 0) ? idx - 1 : $items.length - 1;
							$items.eq(prev).trigger('click');
						}
					}
				}
			});

			// Option selection
			$menu.on('click', '.wcbs-custom-select-item', function(e) {
				e.preventDefault();
				e.stopPropagation();
				var val = $(this).attr('data-value') !== undefined ? $(this).attr('data-value') : $(this).data('value');
				$select.val(val).trigger('change').trigger('input');
				self.syncCustomSelectOptions($select, $customContainer);
				$customContainer.removeClass('wcbs-open wcbs-dropup');
				$trigger.attr('aria-expanded', 'false');
				$menu.hide();
			});

			// Re-sync when native select changes
			$select.on('change', function() {
				self.syncCustomSelectOptions($select, $customContainer);
			});

			// Observe dynamic option mutations (e.g. WooCommerce Bookings updates options or disabled state)
			if (window.MutationObserver && $select[0]) {
				var observer = new MutationObserver(function() {
					self.syncCustomSelectOptions($select, $customContainer);
				});
				observer.observe($select[0], { childList: true, subtree: true, attributes: true, attributeFilter: ['disabled'] });
			}

			// Document click-outside listener bound once
			if (!window.WCBS_SELECT_OUTSIDE_BOUND) {
				window.WCBS_SELECT_OUTSIDE_BOUND = true;
				$(document).on('click', function(e) {
					if (!$(e.target).closest('.wcbs-custom-select-container').length) {
						$('.wcbs-custom-select-container.wcbs-open').removeClass('wcbs-open').find('.wcbs-custom-select-menu').hide();
						$('.wcbs-custom-select-trigger').attr('aria-expanded', 'false');
					}
				});
				$(document).on('keydown', function(e) {
					if (e.key === 'Escape') {
						$('.wcbs-custom-select-container.wcbs-open').removeClass('wcbs-open').find('.wcbs-custom-select-menu').hide();
						$('.wcbs-custom-select-trigger').attr('aria-expanded', 'false');
					}
				});
			}
		},

		syncCustomSelectOptions: function($select, $container) {
			var $trigger = $container.find('.wcbs-custom-select-trigger');
			var $valSpan = $trigger.find('.wcbs-custom-select-value');
			var $menu = $container.find('.wcbs-custom-select-menu');

			// Disabled state sync
			var isDisabled = $select.prop('disabled') || $select.is(':disabled');
			if (isDisabled) {
				$trigger.prop('disabled', true);
				$container.addClass('wcbs-disabled');
			} else {
				$trigger.prop('disabled', false);
				$container.removeClass('wcbs-disabled');
			}

			var currentVal = $select.val() || '';
			var selectedText = '';
			var itemsHtml = '';

			$select.find('option').each(function() {
				var optVal = $(this).attr('value');
				if (typeof optVal === 'undefined') optVal = $(this).val() || '';
				var optText = $(this).text().trim();
				var isSelected = (String(optVal) === String(currentVal)) || (!currentVal && $(this).is(':selected'));
				if (isSelected) {
					selectedText = optText;
				}

				var isPlaceholder = !optVal || optText.toLowerCase().indexOf('time') !== -1 || optText.toLowerCase().indexOf('choose') !== -1 || optText.toLowerCase().indexOf('select') !== -1;
				var itemClass = 'wcbs-custom-select-item' + (isSelected ? ' wcbs-selected' : '') + (isPlaceholder ? ' wcbs-placeholder-item' : '');

				itemsHtml += '<div class="' + itemClass + '" data-value="' + optVal + '" role="option" aria-selected="' + (isSelected ? 'true' : 'false') + '">' +
					'<span>' + optText + '</span>' +
					(isSelected && !isPlaceholder ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' : '') +
				'</div>';
			});

			if (!selectedText) {
				selectedText = $select.find('option:selected').text().trim() || $select.find('option').first().text().trim() || 'Select';
			}

			$valSpan.text(selectedText);
			$menu.html(itemsHtml);
		},

		// 4. Mirror and enhance Time Slots (100% event-driven, NEVER detaches native elements)
		syncTimeSlots: function($container) {
			var self = this;
			if (!$container || !$container.length) {
				$container = $('.wcbs-root-container');
			}

			// Update header timezone name if detected
			var $tzEl = $container.find('.wc-bookings-date-picker-timezone, .wcbs-current-timezone').first();
			if ($tzEl.length && $tzEl.text().trim() && $tzEl.text().trim() !== 'Detecting timezone...') {
				$container.find('.wcbs-header-tz-name').text($tzEl.text().trim());
			}

			// First check for customer-defined duration / select-based time pickers
			if (self.syncDropdowns($container)) {
				return;
			}

			var $holder = $container.find('.wcbs-slots-holder');
			if (!$holder.length) return;

			var $prompt = $container.find('.wcbs-slots-prompt');
			var $displayList = $holder.find('ul.wcbs-slots-display');
			if (!$displayList.length) {
				$displayList = $('<ul class="block-picker wcbs-slots-display wcbs-hidden"></ul>');
				$holder.prepend($displayList);
			}
			var $emptyNotice = $holder.find('.wcbs-slots-empty-notice');
			if (!$emptyNotice.length) {
				$emptyNotice = $('<div class="wcbs-slots-empty-notice" style="display:none;"></div>');
				$holder.append($emptyNotice);
			}

			// Find native time picker element (MUST remain in DOM inside fieldset so WC Bookings AJAX updates work)
			var $nativePicker = $('fieldset.wc-bookings-date-picker ul.block-picker, .wcbs-split-col-left ul.block-picker, fieldset.wc-bookings-date-picker .wc-bookings-time-block-picker ul, fieldset.wc-bookings-date-picker ul.blocks, form.cart ul.block-picker:not(.wcbs-slots-display), ul.block-picker:not(.wcbs-slots-display)').first();

			var $realSlotsInDisplay = $displayList.find('li:has(a), li a');
			var $realSlotsInNative = $nativePicker.length ? $nativePicker.find('li:has(a), li a') : $();
			var $anySlots = $('ul.block-picker').not('.wcbs-slots-display').find('li:has(a), li a');

			if ($realSlotsInNative.length) {
				var nativeHtml = $nativePicker.html().trim();
				if ($displayList.data('native-html') !== nativeHtml || !$realSlotsInDisplay.length) {
					$displayList.data('native-html', nativeHtml);
					$displayList.html(nativeHtml);
					$realSlotsInDisplay = $displayList.find('li:has(a), li a');
				}
			} else if (!$realSlotsInDisplay.length && $anySlots.length) {
				var anyHtml = $anySlots.first().closest('ul').html().trim();
				$displayList.data('native-html', anyHtml);
				$displayList.html(anyHtml);
				$realSlotsInDisplay = $displayList.find('li:has(a), li a');
			}

			if ($realSlotsInDisplay.length) {
				// Real slots exist! Mirror them into display list and reveal
				$prompt.hide();
				$emptyNotice.hide();
				$displayList.removeClass('wcbs-hidden').show().removeAttr('style').css('display', 'grid');
				self.enhanceSlots($container, $displayList, $nativePicker);
			} else {
				// If no real slot anchors yet, check if there is an error/empty notice text
				var checkText = ($nativePicker.length ? $nativePicker.text() : '') + ' ' + $displayList.text();
				var lower = checkText.toLowerCase();
				if (lower.indexOf('no blocks') !== -1 || lower.indexOf('no available') !== -1 || lower.indexOf('fully booked') !== -1) {
					// Fully booked or no slots for this date
					$prompt.hide();
					$displayList.addClass('wcbs-hidden').hide().empty();
					$displayList.removeData('native-html');
					$emptyNotice.html(
						'<div class="wcbs-slots-prompt" style="border-color:#fca5a5;background:#fef2f2;color:#991b1b;">' +
							'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' +
							'<span>No available time slots for this date. Please pick another date.</span>' +
						'</div>'
					).show();
				} else if (lower.indexOf('choose a date') !== -1) {
					// Default placeholder before a date has been selected
					$prompt.show();
					$displayList.addClass('wcbs-hidden').hide().empty();
					$displayList.removeData('native-html');
					$emptyNotice.hide();
				}
			}

			// Enforce theme color on active date cell (ensure round circle, transparent td)
			$('.ui-datepicker-current-day, td.bookable.ui-datepicker-current-day').each(function() {
				this.style.setProperty('background', 'transparent', 'important');
				this.style.setProperty('background-color', 'transparent', 'important');
				this.style.setProperty('border', 'none', 'important');
				this.style.setProperty('box-shadow', 'none', 'important');
			});
			$('.ui-datepicker-current-day a, td.bookable.ui-datepicker-current-day a, td a.ui-state-active').each(function() {
				this.style.setProperty('background', 'var(--wcbs-day-sel-bg, #4f46e5)', 'important');
				this.style.setProperty('background-color', 'var(--wcbs-day-sel-bg, #4f46e5)', 'important');
				this.style.setProperty('color', 'var(--wcbs-day-sel-text, #ffffff)', 'important');
				this.style.setProperty('border-color', 'var(--wcbs-day-sel-bg, #4f46e5)', 'important');
				this.style.setProperty('border-radius', 'var(--wcbs-day-radius, 50%)', 'important');
			});
		},

		enhanceSlots: function($container, $displayList, $nativePicker) {
			var threshold = (typeof wcbs_params !== 'undefined') ? parseInt(wcbs_params.urgency_threshold, 10) : 2;
			var $blocks = $displayList.find('li');

			$blocks.each(function() {
				var $li = $(this);
				var $a = $li.find('a');
				if (!$a.length) {
					$a = $li;
				}

				// Urgency badge
				var spotsLeft = $li.data('slots-left') || $a.data('slots-left');
				if (spotsLeft && parseInt(spotsLeft, 10) <= threshold && !$a.find('.wcbs-urgency-badge').length) {
					$a.append('<span class="wcbs-urgency-badge">Only ' + spotsLeft + ' left!</span>');
				}
			});

			// Slot selection click handler with proxy to native picker
			$displayList.off('click.wcbs', 'a, li').on('click.wcbs', 'a, li', function(e) {
				var $el = $(this);
				var $a = $el.is('a') ? $el : $el.find('a');
				if (!$a.length) return;
				e.preventDefault();

				$displayList.find('li, a').removeClass('selected');
				$a.addClass('selected').closest('li').addClass('selected');

				var chosenVal = $a.data('value') || $el.data('value') || $el.data('block') || $a.data('block') || $a.text().trim();
				var chosenText = $a.text().trim();

				// Update live summary card
				$container.find('.wcbs-val-time').text(chosenText);
				$container.find('.wcbs-summary-time').show();

				// Proxy click to native anchor in $nativePicker so WC Bookings internal handlers fire
				if ($nativePicker && $nativePicker.length) {
					var $targetNative = $nativePicker.find('a').filter(function() {
						var nv = $(this).data('value') || $(this).data('block') || $(this).text().trim();
						return nv === chosenVal || $(this).text().trim() === chosenText;
					}).first();

					if ($targetNative.length) {
						$targetNative.trigger('click');
					} else {
						// Fallback: set hidden input directly
						$('input[name="wc_bookings_field_start_date_time"], input#wc_bookings_field_start_date_time').val(chosenVal).trigger('change');
						$('#wc-bookings-booking-form').trigger('change').trigger('wc_booking_form_changed');
					}
				} else {
					$('input[name="wc_bookings_field_start_date_time"], input#wc_bookings_field_start_date_time').val(chosenVal).trigger('change');
					$('#wc-bookings-booking-form').trigger('change').trigger('wc_booking_form_changed');
				}

				// Enable Book Now button
				$('button.single_add_to_cart_button, .single_add_to_cart_button').prop('disabled', false).removeClass('disabled');

				if (window.WCBSCalendarSync && window.WCBSCalendarSync.update) {
					var dText = $container.find('.wcbs-val-date').text();
					var pTitle = $container.find('.wcbs-summary-title').text() || document.title;
					window.WCBSCalendarSync.update({
						title: pTitle,
						date: dText,
						time: chosenText
					});
				}
			});
		},

		// Date details extractor from calendar cell
		extractDateDetails: function($cell) {
			var dayVal = parseInt($cell.text().trim(), 10);
			if (!dayVal || isNaN(dayVal)) return null;

			var monthVal = null;
			var yearVal = null;

			// Method A: Check td attributes
			var mAttr = $cell.attr('data-month');
			if (mAttr !== undefined && mAttr !== null && mAttr !== '') {
				monthVal = parseInt(mAttr, 10) + 1; // 0-indexed in jQuery UI
			}
			var yAttr = $cell.attr('data-year');
			if (yAttr !== undefined && yAttr !== null && yAttr !== '') {
				yearVal = parseInt(yAttr, 10);
			}

			// Method B: Check anchor attributes
			var $a = $cell.find('a');
			if ($a.length) {
				if (!monthVal && $a.attr('data-month') !== undefined) {
					monthVal = parseInt($a.attr('data-month'), 10) + 1;
				}
				if (!yearVal && $a.attr('data-year') !== undefined) {
					yearVal = parseInt($a.attr('data-year'), 10);
				}
			}

			// Method C: Check Datepicker header
			if (!monthVal || !yearVal) {
				var $header = $cell.closest('.ui-datepicker').find('.ui-datepicker-title');
				if ($header.length) {
					var $ySpan = $header.find('.ui-datepicker-year');
					if ($ySpan.length) {
						var yText = $ySpan.text().trim();
						if (yText && !isNaN(parseInt(yText, 10))) yearVal = parseInt(yText, 10);
					}
					var $mSpan = $header.find('.ui-datepicker-month');
					var titleText = $mSpan.length ? $mSpan.text().trim() : $header.text().trim();
					var monthNames = {
						jan: 1, january: 1,
						feb: 2, february: 2,
						mar: 3, march: 3,
						apr: 4, april: 4,
						may: 5,
						jun: 6, june: 6,
						jul: 7, july: 7,
						aug: 8, august: 8,
						sep: 9, september: 9,
						oct: 10, october: 10,
						nov: 11, november: 11,
						dec: 12, december: 12
					};
					var lowerTitle = titleText.toLowerCase();
					for (var mName in monthNames) {
						if (lowerTitle.indexOf(mName) !== -1) {
							monthVal = monthNames[mName];
							break;
						}
					}
				}
			}

			// Method D: Fallback to existing inputs if filled
			if (!yearVal) {
				var existingY = $('input[name="wc_bookings_field_start_date_year"]').val();
				if (existingY && existingY.length === 4) yearVal = parseInt(existingY, 10);
			}
			if (!monthVal) {
				var existingM = $('input[name="wc_bookings_field_start_date_month"]').val();
				if (existingM && parseInt(existingM, 10) >= 1 && parseInt(existingM, 10) <= 12) monthVal = parseInt(existingM, 10);
			}

			// Method E: Fallback to current date
			var today = new Date();
			if (!yearVal) yearVal = today.getFullYear();
			if (!monthVal) monthVal = today.getMonth() + 1;

			var dayStr = (dayVal < 10 ? '0' : '') + dayVal;
			var monthStr = (monthVal < 10 ? '0' : '') + monthVal;
			var yearStr = String(yearVal);
			var fdate = yearStr + '-' + monthStr + '-' + dayStr;

			return {
				year: yearStr,
				month: monthStr,
				day: dayStr,
				fdate: fdate,
				dayVal: dayVal,
				monthVal: monthVal,
				yearVal: yearVal
			};
		},

		// 5. Listen to Datepicker selections & force event triggers
		initDatepickerListeners: function($container) {
			var self = this;
			if (!$container || !$container.length) $container = $('.wcbs-root-container');

			// Central handler when date inputs change (works for both single-date and range pickers)
			var onDateInputsChanged = function() {
				var sy = parseInt($('input[name="wc_bookings_field_start_date_year"]').val(), 10);
				var sm = parseInt($('input[name="wc_bookings_field_start_date_month"]').val(), 10);
				var sd = parseInt($('input[name="wc_bookings_field_start_date_day"]').val(), 10);

				var ey = parseInt($('input[name*="to_year"], input[name*="end_date_year"]').val(), 10);
				var em = parseInt($('input[name*="to_month"], input[name*="end_date_month"]').val(), 10);
				var ed = parseInt($('input[name*="to_day"], input[name*="end_date_day"]').val(), 10);

				if (sy && sm && sd) {
					var sObj = new Date(sy, sm - 1, sd);
					var sFmt = sObj.toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
					var finalDate = sFmt;

					if (ey && em && ed) {
						var eObj = new Date(ey, em - 1, ed);
						var eFmt = eObj.toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
						var diffDays = Math.round((eObj - sObj) / (1000 * 60 * 60 * 24));
						finalDate = sFmt + ' – ' + eFmt + (diffDays > 0 ? ' (' + diffDays + ' ' + (diffDays === 1 ? 'night' : 'nights') + ')' : '');
					}

					$container.find('.wcbs-val-date').text(finalDate);
					$container.find('.wcbs-summary-date').show();
					$container.find('.wcbs-slots-prompt').hide();

					// If date-only product, enable Book Now immediately and calculate cost
					if (!self.isTimeBasedProduct($container)) {
						var $bookBtn = $('button.single_add_to_cart_button, .single_add_to_cart_button, button.wc-bookings-booking-form-button');
						$bookBtn.prop('disabled', false).removeClass('disabled').removeAttr('disabled');
						self.triggerCostCalculation($container);
					}
				}
				self.syncTimeSlots($container);
			};

			$(document).on('change', 'input[name*="start_date_day"], input[name*="start_date_month"], input[name*="start_date_year"], input[name*="to_day"], input[name*="to_month"], input[name*="to_year"], input[name*="end_date_day"]', onDateInputsChanged);

			// Native DOM Capture Listener for Datepicker cell clicks
			document.addEventListener('click', function(e) {
				var cell = e.target.closest ? e.target.closest('.ui-datepicker-calendar td') : null;
				if (!cell) return;

				var $cell = $(cell);
				if ($cell.hasClass('ui-state-disabled') || $cell.hasClass('not_bookable')) return;

				var dt = self.extractDateDetails($cell);
				if (!dt) return;

				var isTimeBased = self.isTimeBasedProduct($container);

				// Immediately update hidden input values with full valid year, month, day
				$('input[name="wc_bookings_field_start_date_year"], input.booking_date_year').val(dt.yearVal || dt.year);
				$('input[name="wc_bookings_field_start_date_month"], input.booking_date_month').val(dt.monthVal || dt.month);
				$('input[name="wc_bookings_field_start_date_day"], input.booking_date_day').val(dt.dayVal || dt.day);
				$('input[name="wc_bookings_field_start_date"]').val(dt.fdate);

				// Update live summary card
				var dateObj = new Date(dt.yearVal, dt.monthVal - 1, dt.dayVal);
				var dateFormatted = dateObj.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' });
				$('.wcbs-val-date').text(dateFormatted);
				$('.wcbs-summary-date').show();
				$('.wcbs-slots-prompt').hide();

				// Show loading skeleton in slots list if time-based and not dropdowns
				if (isTimeBased && !$('.wcbs-time-dropdowns-wrapper select').length && !$('select[name*="time"]').length) {
					var $disp = $('.wcbs-slots-holder ul.wcbs-slots-display');
					if ($disp.length) {
						$disp.removeClass('wcbs-hidden').show().removeAttr('style').css('display', 'grid');
						$disp.html(
							'<li class="wcbs-skeleton" style="height:44px;list-style:none;"></li>' +
							'<li class="wcbs-skeleton" style="height:44px;list-style:none;"></li>' +
							'<li class="wcbs-skeleton" style="height:44px;list-style:none;"></li>' +
							'<li class="wcbs-skeleton" style="height:44px;list-style:none;"></li>'
						);
					}
				}
				$('.wcbs-slots-empty-notice').hide();

				// Enforce transparent td and round circle anchor
				$cell.each(function() {
					this.style.setProperty('background', 'transparent', 'important');
					this.style.setProperty('background-color', 'transparent', 'important');
					this.style.setProperty('border', 'none', 'important');
					this.style.setProperty('box-shadow', 'none', 'important');
				});
				$cell.find('a').each(function() {
					this.style.setProperty('background', 'var(--wcbs-day-sel-bg, #4f46e5)', 'important');
					this.style.setProperty('background-color', 'var(--wcbs-day-sel-bg, #4f46e5)', 'important');
					this.style.setProperty('color', 'var(--wcbs-day-sel-text, #ffffff)', 'important');
					this.style.setProperty('border-color', 'var(--wcbs-day-sel-bg, #4f46e5)', 'important');
					this.style.setProperty('border-radius', 'var(--wcbs-day-radius, 50%)', 'important');
				});

				// Dispatch triggers to WooCommerce Bookings
				var dispatchTriggers = function() {
					$('input[name="wc_bookings_field_start_date_day"], input.booking_date_day').val(dt.dayVal || dt.day).trigger('change').trigger('input');
					$('input[name="wc_bookings_field_start_date_month"], input.booking_date_month').val(dt.monthVal || dt.month).trigger('change').trigger('input');
					$('input[name="wc_bookings_field_start_date_year"], input.booking_date_year').val(dt.yearVal || dt.year).trigger('change').trigger('input');
					$('input[name="wc_bookings_field_start_date"]').val(dt.fdate).trigger('change').trigger('input');

					var $pickerWrapper = $('.wc-bookings-date-picker, fieldset.wc-bookings-date-picker');
					$pickerWrapper.triggerHandler('date-selected', [ dt.fdate ]);
					$pickerWrapper.trigger('date-selected', [ dt.fdate ]);

					var $targets = $('fieldset.wc-bookings-date-picker, .wc-bookings-date-picker, #wc-bookings-booking-form fieldset, #wc-bookings-booking-form, .picker, form.cart');
					$targets.trigger('date-selected', [ dt.fdate ]).trigger('change');
					$('#wc-bookings-booking-form').trigger('change').trigger('wc_booking_form_changed');
					$('form.cart').trigger('change');

					// If date-only product, trigger cost calculation directly
					if (!isTimeBased) {
						self.triggerCostCalculation($container);
						return;
					}

					// DIRECT AJAX FETCH FALLBACK: Fetch slots via AJAX if block-picker is used
					var ajaxUrl = (window.booking_form_params && booking_form_params.ajax_url) ? booking_form_params.ajax_url : ((typeof wcbs_params !== 'undefined') ? wcbs_params.ajax_url : '');
					if (ajaxUrl) {
						var $f = $('form.cart, form.wc-bookings-booking-form, #wc-bookings-booking-form').first();
						if ($f.length) {
							var fData = $f.serialize();
							$.ajax({
								type: 'POST',
								url: ajaxUrl,
								data: {
									action: 'wc_bookings_get_blocks',
									form: fData
								},
								success: function(code) {
									if (code && code.trim() !== '') {
										if (code.indexOf('<select') !== -1 || code.indexOf('wc_bookings_field_start_date') !== -1 || code.indexOf('wc_bookings_field_duration') !== -1) {
											$('.wcbs-slots-holder ul.wcbs-slots-display').empty().addClass('wcbs-hidden').hide();
											var $targetTime = $container.find('.wcbs-time-dropdowns-wrapper');
											if ($targetTime.length && (!$targetTime.find('select').length || code.indexOf('<select') !== -1)) {
												var $temp = $('<div>' + code + '</div>');
												if ($temp.find('select').length && !$container.find('.wcbs-time-dropdown-field select').length) {
													$targetTime.html(code);
												}
											}
											self.syncDropdowns($container);
										} else {
											var $disp = $('.wcbs-slots-holder ul.wcbs-slots-display');
											if ($disp.length) {
												$disp.html(code);
												self.syncTimeSlots($container);
											}
											var $nat = $('ul.block-picker:not(.wcbs-slots-display)').first();
											if ($nat.length) {
												$nat.html(code);
											}
										}
									}
								}
							});
						}
					}
				};

				// Debounce to 1 single trigger at 40ms
				clearTimeout(self.dispatchTimer);
				self.dispatchTimer = setTimeout(dispatchTriggers, 40);

				// Active watchdog checking for slots or dropdowns
				var pollDelays = [150, 350, 650, 1050, 1600, 2500];
				pollDelays.forEach(function(delay) {
					setTimeout(function() {
						self.syncTimeSlots($container);
						self.unblockCalendar($container);
					}, delay);
				});
			}, true);
		},

		// 6. Persons, Duration, and Add-ons Live Sync
		initPersonsAndDurationListeners: function($container) {
			var self = this;
			if (!$container || !$container.length) $container = $('.wcbs-root-container');

			// Sync Persons / Guests
			var syncPersons = function() {
				var personParts = [];
				var totalCount = 0;
				$container.find('input[name*="persons"]').each(function() {
					var count = parseInt($(this).val(), 10) || 0;
					if (count > 0) {
						totalCount += count;
						var $lbl = $(this).closest('.wc-bookings-booking-form-person-type, .form-field, fieldset').find('label[for="' + this.id + '"], label').first();
						var labelText = $lbl.text().replace(/[:\(].*$/, '').trim() || 'Person';
						personParts.push(count + ' ' + labelText);
					}
				});

				if (totalCount > 0) {
					$container.find('.wcbs-val-persons').text(personParts.join(', '));
					$container.find('.wcbs-summary-persons').show();
				} else {
					$container.find('.wcbs-summary-persons').hide();
				}
			};

			$container.on('change input', 'input[name*="persons"], select[name*="persons"]', function() {
				syncPersons();
				self.triggerCostCalculation($container);
			});
			syncPersons();

			// Sync customer-defined duration in days/months (input type=number)
			$container.on('change input', 'input[name="wc_bookings_field_duration"], input.wc_bookings_field_duration', function() {
				var durVal = parseInt($(this).val(), 10) || 1;
				var durUnit = $(this).closest('.form-field, fieldset').find('label').text().toLowerCase();
				var unitText = durUnit.indexOf('day') !== -1 ? (durVal === 1 ? 'day' : 'days') : (durVal === 1 ? 'unit' : 'units');
				var curDate = $container.find('.wcbs-val-date').text();
				if (curDate && curDate.indexOf('Select') === -1) {
					var baseDate = curDate.replace(/\s*\(\d+\s+.*?\)/, '');
					$container.find('.wcbs-val-date').text(baseDate + ' (' + durVal + ' ' + unitText + ')');
				}
				self.triggerCostCalculation($container);
			});

			// If native resource select exists (and visual staff cards are not enabled)
			var $resSelect = $container.find('select[name="wc_bookings_field_resource"]');
			if ($resSelect.length && !$container.find('.wcbs-resource-cards-section').length) {
				self.buildCustomSelect($resSelect, 'Specialist / Resource', 'wc_bookings_field_resource');
				$resSelect.on('change', function() {
					var text = $(this).find('option:selected').text().trim();
					if (text && text.toLowerCase().indexOf('choose') === -1 && text.toLowerCase().indexOf('select') === -1) {
						$container.find('.wcbs-val-resource').text(text);
						$container.find('.wcbs-summary-resource').show();
					}
					self.triggerCostCalculation($container);
				});
			}
		},

		// 7. AJAX event hooks & unblock watchdog (100% event-based)
		initAjaxWatchdogs: function($container) {
			var self = this;

			// Global jQuery AJAX completion hook
			$(document).ajaxComplete(function(event, xhr, settings) {
				if (settings && settings.data && typeof settings.data === 'string') {
					if (settings.data.indexOf('wc_bookings_calculate_costs') !== -1) {
						if (xhr.responseText) {
							self.handleCostResponse(xhr.responseText, $container);
						}
					}
					if (settings.data.indexOf('wc_bookings_get_blocks') !== -1 || settings.data.indexOf('get_blocks') !== -1) {
						if (xhr.responseText && xhr.responseText.trim() !== '') {
							var resp = xhr.responseText.trim();
							if (resp.indexOf('<select') !== -1 || resp.indexOf('wc_bookings_field_start_date') !== -1 || resp.indexOf('wc_bookings_field_duration') !== -1) {
								$('.wcbs-slots-holder ul.wcbs-slots-display').empty().addClass('wcbs-hidden').hide();
								var $targetTime = $container.find('.wcbs-time-dropdowns-wrapper');
								if ($targetTime.length && (!$targetTime.find('select').length || resp.indexOf('<select') !== -1)) {
									var $temp = $('<div>' + resp + '</div>');
									if ($temp.find('select').length && !$container.find('.wcbs-time-dropdown-field select').length) {
										$targetTime.html(resp);
									}
								}
								self.syncDropdowns($container);
							} else {
								var $disp = $('.wcbs-slots-holder ul.wcbs-slots-display');
								if ($disp.length) {
									$disp.html(resp);
									$disp.removeClass('wcbs-hidden').show().removeAttr('style').css('display', 'grid');
									$('.wcbs-slots-prompt').hide();
									$('.wcbs-slots-empty-notice').hide();
									var $nat = $('ul.block-picker:not(.wcbs-slots-display)').first();
									self.enhanceSlots($container, $disp, $nat);
								}
							}
						}
					}
				}

				setTimeout(function() {
					self.syncTimeSlots($container);
					self.unblockCalendar($container);
				}, 40);
				setTimeout(function() {
					self.syncTimeSlots($container);
					self.unblockCalendar($container);
				}, 200);
			});

			// Hook native XMLHttpRequest to catch non-jQuery AJAX calls
			if (window.XMLHttpRequest && !window.WCBS_XHR_HOOKED) {
				window.WCBS_XHR_HOOKED = true;
				var origOpen = XMLHttpRequest.prototype.open;
				XMLHttpRequest.prototype.open = function() {
					this.addEventListener('load', function() {
						try {
							if (this.responseText && (this.responseText.indexOf('block-picker') !== -1 || this.responseText.indexOf('data-value') !== -1 || this.responseText.indexOf('data-block') !== -1)) {
								if (this.responseText.indexOf('<select') !== -1 || this.responseText.indexOf('wc_bookings_field_start_date') !== -1) {
									$('.wcbs-slots-holder ul.wcbs-slots-display').empty().addClass('wcbs-hidden').hide();
									self.syncDropdowns($container);
								} else {
									var $disp = $('.wcbs-slots-holder ul.wcbs-slots-display');
									if ($disp.length) {
										$disp.html(this.responseText);
										self.syncTimeSlots($container);
									}
								}
							}
						} catch (e) {}
						setTimeout(function() {
							self.syncTimeSlots($container);
							self.unblockCalendar($container);
						}, 50);
					});
					return origOpen.apply(this, arguments);
				};
			}

			// Also listen on WooCommerce custom booking events
			$(document).on('date-selected wc_booking_form_changed change', function() {
				setTimeout(function() {
					self.syncTimeSlots($container);
					self.unblockCalendar($container);
				}, 50);
				setTimeout(function() {
					self.syncTimeSlots($container);
					self.unblockCalendar($container);
				}, 250);
			});

			// Safety timeout on page load: ensure calendar is unblocked after 1 second
			setTimeout(function() {
				self.unblockCalendar($container);
				self.syncTimeSlots($container);
			}, 1000);
		},

		unblockCalendar: function($container) {
			$container.find('.blockUI, .blockOverlay').remove();
			if ($.fn.unblock) {
				$container.find('.picker, .wc-bookings-date-picker, #wc-bookings-booking-form, form.cart').unblock();
			}
		},

		// 8. Synchronize Cost Calculations with Live Summary Card
		observeCostCalculations: function($container) {
			var self = this;
			var safeUpdatePrice = function() {
				var costEl = $('.wc-bookings-booking-cost')[0];
				if (costEl) {
					var raw = $(costEl).html();
					if (raw && typeof raw === 'string') {
						var clean = raw.trim();
						var lower = clean.toLowerCase();
						// Strictly reject fatal errors, database errors, or full HTML dumps
						if (lower.indexOf('fatal error') === -1 && lower.indexOf('database') === -1 && lower.indexOf('wp-die') === -1 && lower.indexOf('operation not permitted') === -1 && lower.indexOf('<html') === -1) {
							if (clean !== '' && clean !== '—') {
								var displayPrice = clean.replace(/^Total:?\s*/i, '').replace(/^Booking Cost:?\s*/i, '');
								$container.find('.wcbs-price-display').html(displayPrice);
								if (clean.indexOf('amount') !== -1 || clean.indexOf('$') !== -1 || clean.indexOf('£') !== -1 || clean.indexOf('€') !== -1) {
									$('button.single_add_to_cart_button, .single_add_to_cart_button, button.wc-bookings-booking-form-button').prop('disabled', false).removeClass('disabled').removeAttr('disabled');
								}
							}
						}
					}
				}
			};

			// Observe DOM mutations on .wc-bookings-booking-cost
			var costNode = $('.wc-bookings-booking-cost')[0];
			if (window.MutationObserver && costNode) {
				var observer = new MutationObserver(function() {
					safeUpdatePrice();
				});
				observer.observe(costNode, { childList: true, subtree: true, characterData: true });
			}

			$(document).on('change ajaxComplete wc_booking_form_changed', safeUpdatePrice);
			safeUpdatePrice();
		}
	};

	$(document).ready(function() {
		window.WCBSFrontend.init();
	});

})(jQuery);
