/**
 * Frontend Main Controller for Styler for WooCommerce Bookings.
 */
(function($) {
	'use strict';

	window.WCBSFrontend = {
		isSplitReady: false,

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
			self.initAjaxWatchdogs($container);
			self.observeCostCalculations($container);

			if (window.console && console.log) {
				console.log('[WCBS Diagnostics] Initialized.', {
					layout: layout,
					bookingForm: $('#wc-bookings-booking-form').length,
					fieldsets: $('fieldset').map(function() { return this.className; }).get(),
					timeBlockPicker: $('.wc-bookings-time-block-picker').length,
					blockPicker: $('.block-picker').length,
					timeSelect: $('select[name="wc_bookings_field_start_date_time"]').length
				});
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

			// Right Column Header
			$rightCol.append(
				'<div class="wcbs-col-header">' +
					'<span class="wcbs-col-step-tag">Step 2</span>' +
					'<h3 class="wcbs-col-title">Select Time & Confirm</h3>' +
				'</div>'
			);

			// Slots & Time Dropdowns Section in Right Column (Slot Tabs completely removed as requested)
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
			$rightCol.append($slotsSection);

			// Move persons / add-on fieldsets if any into Right Column
			var $otherFieldsets = $form.find('#wc-bookings-booking-form fieldset:not(.wc-bookings-date-picker)');
			if ($otherFieldsets.length) {
				$rightCol.append($otherFieldsets);
			}

			// Move Summary Card into Right Column
			var $summaryCard = $container.find('.wcbs-summary-card');
			if ($summaryCard.length) {
				$rightCol.append($summaryCard);
			}

			// Move booking cost into Right Column so it doesn\'t disrupt the two-column grid
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
			// Route select elements or custom time pickers to $rightCol; hidden inputs/native block-pickers to $leftCol
			$gridTarget.children().not($leftCol).not($rightCol).each(function() {
				var $child = $(this);
				if ($child.is('input[type="hidden"]')) {
					$leftCol.append($child);
				} else if ($child.find('select').length || $child.is('select') || $child.hasClass('form-field-wide')) {
					$rightCol.find('.wcbs-time-dropdowns-wrapper').append($child);
				} else if ($child.find('ul.block-picker').length || $child.is('ul.block-picker') || $child.hasClass('block-picker')) {
					$leftCol.append($child);
				} else {
					$rightCol.append($child);
				}
			});

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
			var $startSelect = $('select[name="wc_bookings_field_start_date_time"], select#wc_bookings_field_start_date_time');
			var $endSelect   = $('select[name="wc_bookings_field_duration"], select#wc_bookings_field_duration, select[name="wc_bookings_field_end_date_time"], select#wc_bookings_field_end_date_time');

			if (!$startSelect.length && !$endSelect.length) {
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

			// Suppress regular block-picker slots UI when dropdowns are in use
			$container.find('.wcbs-slots-prompt').hide();
			$container.find('.wcbs-slots-empty-notice').hide();
			$container.find('.wcbs-slots-holder').hide();
			$container.find('.wcbs-slots-display').hide();
			$container.find('.wcbs-slot-tabs-nav').hide();
			$wrapper.show().css('display', 'grid');

			// Process Start Time Dropdown
			if ($startSelect.length) {
				var $startField = $startSelect.closest('.form-field, p.form-field, div.form-field, .wc-bookings-time-block-picker');
				if (!$startField.length) {
					$startField = $startSelect.parent();
				}
				$startField.addClass('wcbs-time-dropdown-field wcbs-field-start-time');

				var $startLabel = $startField.find('label');
				if (!$startLabel.length) {
					$startLabel = $('<label for="' + ($startSelect.attr('id') || 'wc_bookings_field_start_date_time') + '">Start Time</label>');
					$startSelect.before($startLabel);
				}
				if (!$startLabel.find('svg').length) {
					$startLabel.prepend('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> ');
				}

				if (!$wrapper.has($startField).length) {
					$wrapper.append($startField);
				}
				$startField.show().removeAttr('style').css({ display: 'flex', visibility: 'visible', height: 'auto', position: 'relative' });
				$startSelect.show().removeAttr('style').css({ display: 'block', visibility: 'visible' });
			}

			// Process End Time / Duration Dropdown
			if ($endSelect.length) {
				var $endField = $endSelect.closest('.form-field, p.form-field, div.form-field, .wc-bookings-time-block-picker');
				if (!$endField.length) {
					$endField = $endSelect.parent();
				}
				$endField.addClass('wcbs-time-dropdown-field wcbs-field-end-time');

				var $endLabel = $endField.find('label');
				if (!$endLabel.length) {
					$endLabel = $('<label for="' + ($endSelect.attr('id') || 'wc_bookings_field_duration') + '">End Time</label>');
					$endSelect.before($endLabel);
				}
				if (!$endLabel.find('svg').length) {
					$endLabel.prepend('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> ');
				}

				if (!$wrapper.has($endField).length) {
					$wrapper.append($endField);
				}
				$endField.show().removeAttr('style').css({ display: 'flex', visibility: 'visible', height: 'auto', position: 'relative' });
				$endSelect.show().removeAttr('style').css({ display: 'block', visibility: 'visible' });
			}

			// Bind change listener once
			if (!$wrapper.data('wcbs-bound')) {
				$wrapper.data('wcbs-bound', true);

				$container.on('change', 'select[name="wc_bookings_field_start_date_time"], select#wc_bookings_field_start_date_time, select[name="wc_bookings_field_duration"], select#wc_bookings_field_duration, select[name="wc_bookings_field_end_date_time"], select#wc_bookings_field_end_date_time', function() {
					var $sSel = $('select[name="wc_bookings_field_start_date_time"], select#wc_bookings_field_start_date_time');
					var $eSel = $('select[name="wc_bookings_field_duration"], select#wc_bookings_field_duration, select[name="wc_bookings_field_end_date_time"], select#wc_bookings_field_end_date_time');

					var sVal = $sSel.val();
					var eVal = $eSel.length ? $eSel.val() : '';

					var sText = $sSel.find('option:selected').text().trim();
					var eText = $eSel.length ? $eSel.find('option:selected').text().trim() : '';

					// Format live summary text
					var summaryTime = '';
					if (sVal && eVal && eText) {
						summaryTime = sText + ' – ' + eText;
					} else if (sVal) {
						summaryTime = sText;
					}

					if (summaryTime && summaryTime.toLowerCase().indexOf('choose') === -1) {
						$container.find('.wcbs-val-time').text(summaryTime);
						$container.find('.wcbs-summary-time').show();
					}

					// Trigger booking form change event
					$('#wc-bookings-booking-form, form.cart').trigger('change').trigger('wc_booking_form_changed');

					// Enable Book Now if user has selected required times
					var hasEndRequirement = $eSel.length > 0;
					var isReady = sVal && (!hasEndRequirement || eVal);

					if (isReady) {
						var $bookBtn = $('button.single_add_to_cart_button, .single_add_to_cart_button');
						$bookBtn.prop('disabled', false).removeClass('disabled');

						// Direct cost calculation fallback to populate price display
						var $f = $('form.cart, form.wc-bookings-booking-form, #wc-bookings-booking-form').first();
						var ajaxUrl = (window.booking_form_params && booking_form_params.ajax_url) ? booking_form_params.ajax_url : ((typeof wcbs_params !== 'undefined') ? wcbs_params.ajax_url : '');
						if (ajaxUrl && $f.length) {
							$.ajax({
								type: 'POST',
								url: ajaxUrl,
								data: {
									action: 'wc_bookings_calculate_costs',
									form: $f.serialize()
								},
								success: function(resp) {
									if (resp && resp.trim() !== '') {
										$('.wc-bookings-booking-cost').html(resp).show();
										$('.wcbs-price-display').html(resp);
										$bookBtn.prop('disabled', false).removeClass('disabled');
									}
								}
							});
						}
					}
				});
			}

			// Update summary and check initial state
			var curStart = $startSelect.val();
			var curEnd   = $endSelect.length ? $endSelect.val() : '';
			if (curStart) {
				var stText = $startSelect.find('option:selected').text().trim();
				var enText = $endSelect.length ? $endSelect.find('option:selected').text().trim() : '';
				var smTime = (curEnd && enText) ? (stText + ' – ' + enText) : stText;
				if (smTime && smTime.toLowerCase().indexOf('choose') === -1) {
					$container.find('.wcbs-val-time').text(smTime);
					$container.find('.wcbs-summary-time').show();
				}
				if (curStart && (!$endSelect.length || curEnd)) {
					$('button.single_add_to_cart_button, .single_add_to_cart_button').prop('disabled', false).removeClass('disabled');
				}
			}

			return true;
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

			// Check where real slots exist:
			// 1) Already in $displayList (injected by WC Bookings or AJAX hook)
			// 2) In $nativePicker
			// 3) In any other .block-picker
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
				// If no real slot anchors yet, check if there\'s an error/empty notice text
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

			if (window.console && console.log) {
				var nativeSnippet = ($nativePicker.length ? $nativePicker.html().trim().substring(0, 100) : '');
				console.log('[WCBS Diagnostics] syncTimeSlots:', {
					nativePickerFound: $nativePicker.length,
					realSlotsCount: $realSlotsInDisplay.length,
					displaySlotsCount: $displayList.find('li').length,
					nativeSnippet: nativeSnippet
				});
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
					var $nativeA = $nativePicker.find('a[data-value="' + chosenVal + '"]');
					if (!$nativeA.length) {
						$nativeA = $nativePicker.find('a').filter(function() {
							return $(this).text().trim() === chosenText;
						});
					}
					if ($nativeA.length) {
						$nativeA.trigger('click');
					}
				}

				// Update core time input directly
				var $timeInput = $('input[name="wc_bookings_field_start_date_time"], input.booking_date_time');
				if ($timeInput.length) {
					$timeInput.val(chosenVal).trigger('change').trigger('input');
				}

				// Trigger form change for price calculations & enabling Add to Cart button
				$('fieldset.wc-bookings-date-picker, #wc-bookings-booking-form, form.cart').trigger('change').trigger('wc_booking_form_changed');

				// Enable Add to Cart / Book Now button
				$('button.single_add_to_cart_button, .single_add_to_cart_button').prop('disabled', false).removeClass('disabled');

				// Sync calendar links
				var chosenDate = $container.find('.wcbs-val-date').text();
				if (window.WCBSCalendarSync) {
					window.WCBSCalendarSync.update({
						title: $('h1.product_title').text().trim() || 'Appointment',
						date: chosenDate,
						time: chosenText,
						startIso: new Date().toISOString()
					});
				}
			});
		},

		// Helper: Extract complete year, month, day reliably
		extractDateDetails: function($cell) {
			if (!$cell || !$cell.length) return null;

			// 1. Day of the month
			var dayText = $cell.find('a').text().trim() || $cell.text().trim();
			var dayVal = parseInt(dayText, 10);
			if (isNaN(dayVal) || dayVal < 1 || dayVal > 31) return null;

			// 2. Year & Month
			var yearVal = null;
			var monthVal = null;

			// Method A: Check td attributes (jQuery UI attaches data-month [0-11] and data-year [e.g. 2026])
			var attrYear = $cell.attr('data-year') || $cell.data('year');
			if (attrYear) {
				var yParsed = parseInt(attrYear, 10);
				if (!isNaN(yParsed) && yParsed >= 2020) yearVal = yParsed;
			}

			var attrMonth = $cell.attr('data-month');
			if (attrMonth === undefined || attrMonth === null || attrMonth === '') {
				attrMonth = $cell.data('month');
			}
			if (attrMonth !== undefined && attrMonth !== null && attrMonth !== '') {
				var mParsed = parseInt(attrMonth, 10);
				if (!isNaN(mParsed) && mParsed >= 0 && mParsed <= 11) {
					monthVal = mParsed + 1; // Convert 0-indexed to 1-indexed (1-12)
				}
			}

			// Method B: Check jQuery UI Datepicker instance on .picker
			if (!yearVal || !monthVal) {
				var $pickerEl = $cell.closest('.hasDatepicker, .picker');
				if (!$pickerEl.length) $pickerEl = $('.hasDatepicker, .picker').first();
				if ($pickerEl.length && window.jQuery && jQuery.datepicker) {
					try {
						var inst = jQuery.datepicker._getInst($pickerEl[0]);
						if (inst) {
							if (!yearVal) yearVal = inst.drawYear || inst.selectedYear || inst.currentYear;
							if (!monthVal) {
								var m = (inst.drawMonth !== undefined) ? inst.drawMonth : ((inst.selectedMonth !== undefined) ? inst.selectedMonth : inst.currentMonth);
								if (m !== undefined && m !== null) monthVal = parseInt(m, 10) + 1;
							}
						}
					} catch (err) {}
				}
			}

			// Method C: Parse the datepicker header title (.ui-datepicker-title)
			if (!yearVal || !monthVal) {
				var $pickerContainer = $cell.closest('.wc-bookings-date-picker, .picker, .wcbs-root-container, #wc-bookings-booking-form');
				var titleText = $pickerContainer.find('.ui-datepicker-title').text().trim() || $('.ui-datepicker-title').first().text().trim();

				if (!yearVal) {
					var yMatch = titleText.match(/\b(20\d\d)\b/);
					if (yMatch) yearVal = parseInt(yMatch[1], 10);
				}

				if (!monthVal) {
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

			// Hook into standard jQuery UI datepicker hidden inputs if changed
			$(document).on('change', 'input[name="wc_bookings_field_start_date_year"], input[name="wc_bookings_field_start_date_month"], input[name="wc_bookings_field_start_date_day"]', function() {
				var y = parseInt($('input[name="wc_bookings_field_start_date_year"]').val(), 10);
				var m = parseInt($('input[name="wc_bookings_field_start_date_month"]').val(), 10);
				var d = parseInt($('input[name="wc_bookings_field_start_date_day"]').val(), 10);
				if (y && m && d) {
					var dateObj = new Date(y, m - 1, d);
					var dateFormatted = dateObj.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' });
					$container.find('.wcbs-val-date').text(dateFormatted);
					$container.find('.wcbs-summary-date').show();
					$container.find('.wcbs-slots-prompt').hide();
				}
				self.syncTimeSlots($container);
			});

			// Native DOM Capture Listener for Datepicker cell clicks
			document.addEventListener('click', function(e) {
				var cell = e.target.closest ? e.target.closest('.ui-datepicker-calendar td') : null;
				if (!cell) return;

				var $cell = $(cell);
				if ($cell.hasClass('ui-state-disabled') || $cell.hasClass('not_bookable')) return;

				var dt = self.extractDateDetails($cell);
				if (!dt) return;

				// Immediately update hidden input values with full valid year, month, day
				$('input[name="wc_bookings_field_start_date_year"], input.booking_date_year').val(dt.year);
				$('input[name="wc_bookings_field_start_date_month"], input.booking_date_month').val(dt.month);
				$('input[name="wc_bookings_field_start_date_day"], input.booking_date_day').val(dt.day);
				$('input[name="wc_bookings_field_start_date"]').val(dt.fdate);

				// Update live summary card and hide initial empty prompt
				var dateObj = new Date(dt.yearVal, dt.monthVal - 1, dt.dayVal);
				var dateFormatted = dateObj.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' });
				$('.wcbs-val-date').text(dateFormatted);
				$('.wcbs-summary-date').show();
				$('.wcbs-slots-prompt').hide();

				// Show loading skeleton in slots list if no dropdowns
				if (!$('select[name="wc_bookings_field_start_date_time"]').length) {
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

				// Dispatch triggers to WooCommerce Bookings (debounced to 1 single call)
				var dispatchTriggers = function() {
					$('input[name="wc_bookings_field_start_date_day"], input.booking_date_day').val(dt.day).trigger('change').trigger('input');
					$('input[name="wc_bookings_field_start_date_month"], input.booking_date_month').val(dt.month).trigger('change').trigger('input');
					$('input[name="wc_bookings_field_start_date_year"], input.booking_date_year').val(dt.year).trigger('change').trigger('input');
					$('input[name="wc_bookings_field_start_date"]').val(dt.fdate).trigger('change').trigger('input');

					var $pickerWrapper = $('.wc-bookings-date-picker, fieldset.wc-bookings-date-picker');
					$pickerWrapper.triggerHandler('date-selected', [ dt.fdate ]);
					$pickerWrapper.trigger('date-selected', [ dt.fdate ]);

					var $targets = $('fieldset.wc-bookings-date-picker, .wc-bookings-date-picker, #wc-bookings-booking-form fieldset, #wc-bookings-booking-form, .picker, form.cart');
					$targets.trigger('date-selected', [ dt.fdate ]).trigger('change');
					$('#wc-bookings-booking-form').trigger('change').trigger('wc_booking_form_changed');
					$('form.cart').trigger('change');

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
							});
						}
					}

					if (window.console && console.log) {
						console.log('[WCBS Diagnostics] Date clicked & dispatched:', dt.fdate);
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

		// 6. AJAX event hooks & unblock watchdog (100% event-based)
		initAjaxWatchdogs: function($container) {
			var self = this;

			// Global jQuery AJAX completion hook
			$(document).ajaxComplete(function(event, xhr, settings) {
				if (window.console && console.log) {
					console.log('[WCBS Diagnostics] AJAX Completed:', settings ? settings.url : '', 'Status:', xhr ? xhr.status : '');
				}

				// If this was a wc_bookings_get_blocks request, populate display picker
				if (settings && settings.data && typeof settings.data === 'string') {
					if (settings.data.indexOf('wc_bookings_get_blocks') !== -1 || settings.data.indexOf('get_blocks') !== -1) {
						if (xhr.responseText && xhr.responseText.trim() !== '') {
							var resp = xhr.responseText.trim();
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
								var $disp = $('.wcbs-slots-holder ul.wcbs-slots-display');
								if ($disp.length) {
									$disp.html(this.responseText);
									self.syncTimeSlots($container);
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

		// 7. Synchronize Cost Calculations with Live Summary Card
		observeCostCalculations: function($container) {
			$(document).on('change ajaxComplete wc_booking_form_changed', function() {
				var costEl = $('.wc-bookings-booking-cost')[0];
				if (costEl) {
					var html = $(costEl).html();
					if (html && html.trim() !== '') {
						$container.find('.wcbs-price-display').html(html);
						if (html.indexOf('amount') !== -1 || html.indexOf('$') !== -1) {
							$('button.single_add_to_cart_button, .single_add_to_cart_button').prop('disabled', false).removeClass('disabled');
						}
					}
				}
			});

			var costEl = $('.wc-bookings-booking-cost')[0];
			if (costEl) {
				var initialCost = $(costEl).html();
				if (initialCost && initialCost.trim() !== '') {
					$container.find('.wcbs-price-display').html(initialCost);
				}
			}
		}
	};

	$(document).ready(function() {
		window.WCBSFrontend.init();
	});

})(jQuery);
