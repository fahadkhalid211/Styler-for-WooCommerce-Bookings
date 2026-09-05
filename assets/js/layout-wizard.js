/**
 * Multi-Step Booking Wizard Controller.
 */
(function($) {
	'use strict';

	window.WCBSWizard = {
		currentStep: 1,
		totalSteps: 4,

		init: function() {
			var self = this;
			var $wizard = $('.wcbs-layout-wizard');
			if (!$wizard.length) return;

			self.setupSteps($wizard);

			// Next button
			$wizard.find('.wcbs-wizard-next-btn').on('click', function(e) {
				e.preventDefault();
				if (self.validateStep(self.currentStep, $wizard)) {
					var nextStep = self.currentStep + 1;
					// Skip step 3 if no persons or extra fields exist
					if (nextStep === 3) {
						var $personsSec = $wizard.find('.wc-bookings-booking-form fieldset:not(.wc-bookings-date-picker):not(.time-picker-fieldset)');
						if (!$personsSec.length || !$personsSec.text().trim()) {
							nextStep = 4;
						}
					}
					self.goToStep(nextStep, $wizard);
				}
			});

			// Previous button
			$wizard.find('.wcbs-wizard-prev-btn').on('click', function(e) {
				e.preventDefault();
				if (self.currentStep > 1) {
					var prevStep = self.currentStep - 1;
					// Skip step 3 on back if no persons or extra fields exist
					if (prevStep === 3) {
						var $personsSec = $wizard.find('.wc-bookings-booking-form fieldset:not(.wc-bookings-date-picker):not(.time-picker-fieldset)');
						if (!$personsSec.length || !$personsSec.text().trim()) {
							prevStep = 2;
						}
					}
					// Skip step 1 on back if no staff exist
					if (prevStep === 1) {
						var $staffSec = $wizard.find('.wcbs-resource-cards-section');
						if (!$staffSec.length) {
							return;
						}
					}
					self.goToStep(prevStep, $wizard);
				}
			});
		},

		setupSteps: function($wizard) {
			var self = this;
			self.currentStep = 1;
			var $staffSec = $wizard.find('.wcbs-resource-cards-section');
			if (!$staffSec.length) {
				self.currentStep = 2;
			}
			self.updateStepVisibility($wizard);
		},

		validateStep: function(step, $wizard) {
			if (step === 2) {
				// Check if date is chosen
				var dateVal = $wizard.find('.wcbs-val-date').text();
				if (!dateVal || dateVal.indexOf('Select a date') !== -1) {
					alert('Please select a booking date to continue.');
					return false;
				}
				// If time-based product, check if time is chosen
				if (window.WCBSFrontend && window.WCBSFrontend.isTimeBasedProduct && window.WCBSFrontend.isTimeBasedProduct($wizard)) {
					var timeVal = $wizard.find('.wcbs-val-time').text();
					if (!timeVal || timeVal.indexOf('Select a time') !== -1) {
						alert('Please select a time slot to continue.');
						return false;
					}
				}
			}
			return true;
		},

		goToStep: function(targetStep, $wizard) {
			var self = this;
			if (targetStep < 1 || targetStep > self.totalSteps) return;

			self.currentStep = targetStep;

			// Update header steps
			$wizard.find('.wcbs-wizard-step').each(function() {
				var stepNum = parseInt($(this).data('step'), 10);
				$(this).removeClass('active completed');
				if (stepNum === targetStep) {
					$(this).addClass('active');
				} else if (stepNum < targetStep) {
					$(this).addClass('completed');
				}
			});

			self.updateStepVisibility($wizard);
		},

		updateStepVisibility: function($wizard) {
			var self = this;
			var step = self.currentStep;

			// Step 1: Staff / Resource section
			var $staffSec = $wizard.find('.wcbs-resource-cards-section');
			// Step 2: Calendar & Slots
			var $calSec = $wizard.find('.wc-bookings-date-picker, .wcbs-calendar-mockup, .wcbs-slot-tabs-nav, .block-picker, .wcbs-slots-container, .wcbs-time-dropdowns-wrapper, .time-picker-fieldset, .wc-bookings-time-block-picker');
			// Step 3: Persons / Quantities
			var $personsSec = $wizard.find('.wc-bookings-booking-form fieldset:not(.wc-bookings-date-picker):not(.time-picker-fieldset)');
			// Step 4: Summary & Submit button
			var $summarySec = $wizard.find('.wcbs-summary-card, .wcbs-inline-summary-wrapper, .wc-bookings-booking-cost, .single_add_to_cart_button');

			// If no staff/resource exists, skip step 1 to 2
			if (!$staffSec.length && step === 1) {
				self.goToStep(2, $wizard);
				return;
			}

			// Show/hide sections based on step
			$staffSec.toggle(step === 1).addClass('wcbs-anim-enter');
			$calSec.toggle(step === 2).addClass('wcbs-anim-enter');
			$personsSec.toggle(step === 3).addClass('wcbs-anim-enter');
			$summarySec.toggle(step === 4).addClass('wcbs-anim-enter');

			if (step === 2 && window.WCBSFrontend) {
				window.WCBSFrontend.syncTimeSlots($wizard);
				window.WCBSFrontend.unblockCalendar($wizard);
			}

			// Navigation buttons state
			var $prevBtn = $wizard.find('.wcbs-wizard-prev-btn');
			var $nextBtn = $wizard.find('.wcbs-wizard-next-btn');
			var $submitBtn = $wizard.find('.single_add_to_cart_button');

			if (step === 1 || (!step === 2 && !$staffSec.length)) {
				$prevBtn.hide();
			} else if (step === 2 && !$staffSec.length) {
				$prevBtn.hide();
			} else {
				$prevBtn.show();
			}

			if (step === self.totalSteps) {
				$nextBtn.hide();
				$submitBtn.show();
			} else {
				$nextBtn.show();
				$submitBtn.hide();
			}
		}
	};

	$(document).ready(function() {
		window.WCBSWizard.init();
	});

})(jQuery);
