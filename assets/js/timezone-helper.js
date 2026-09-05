/**
 * Timezone Detection and Display Helper.
 */
(function($) {
	'use strict';

	window.WCBSTimezone = {
		init: function() {
			var self = this;
			var $bar = $('.wcbs-timezone-bar');
			if (!$bar.length) return;

			var detectedZone = self.detectUserTimezone();
			$bar.find('.wcbs-current-timezone').text(detectedZone);

			// Toggle dropdown
			$bar.find('.wcbs-timezone-change-btn').on('click', function(e) {
				e.preventDefault();
				$bar.find('.wcbs-timezone-dropdown').slideToggle(150);
			});

			// Select timezone change
			$bar.find('.wcbs-timezone-select').on('change', function() {
				var val = $(this).val();
				var label = val === 'auto' ? detectedZone : val;
				$bar.find('.wcbs-current-timezone').text(label);
				$bar.find('.wcbs-timezone-dropdown').slideUp(150);
			});
		},

		detectUserTimezone: function() {
			try {
				if (window.Intl && Intl.DateTimeFormat) {
					return Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
				}
			} catch (e) {}
			return 'UTC';
		}
	};

	$(document).ready(function() {
		window.WCBSTimezone.init();
	});

})(jQuery);
