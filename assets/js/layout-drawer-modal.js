/**
 * Drawer and Modal Layout Controller.
 */
(function($) {
	'use strict';

	window.WCBSDrawerModal = {
		init: function() {
			var self = this;

			// Open trigger
			$(document).on('click', '.wcbs-open-trigger-btn', function(e) {
				e.preventDefault();
				var $container = $(this).closest('.wcbs-root-container');
				self.open($container);
			});

			// Close button
			$(document).on('click', '.wcbs-popup-close-btn, .wcbs-overlay-backdrop', function(e) {
				e.preventDefault();
				var $container = $(this).closest('.wcbs-root-container');
				self.close($container);
			});

			// Escape key closes open popup
			$(document).on('keydown', function(e) {
				if (e.key === 'Escape' || e.keyCode === 27) {
					var $openContainer = $('.wcbs-root-container').has('.wcbs-popup-container:visible');
					if ($openContainer.length) {
						self.close($openContainer);
					}
				}
			});
		},

		open: function($container) {
			$container.find('.wcbs-overlay-backdrop').fadeIn(200);
			$container.find('.wcbs-popup-container').show();
			$('body').addClass('wcbs-popup-open');

			// Trigger datepicker resize/refresh if needed
			if ($.datepicker) {
				$container.find('.ui-datepicker').datepicker('refresh');
			}
		},

		close: function($container) {
			$container.find('.wcbs-overlay-backdrop').fadeOut(150);
			$container.find('.wcbs-popup-container').hide();
			$('body').removeClass('wcbs-popup-open');
		}
	};

	$(document).ready(function() {
		window.WCBSDrawerModal.init();
	});

})(jQuery);
