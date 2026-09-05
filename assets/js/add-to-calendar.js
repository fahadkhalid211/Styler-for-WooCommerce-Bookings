/**
 * Add to Calendar Generator (Google, Apple iCal, Outlook).
 */
(function($) {
	'use strict';

	window.WCBSCalendarSync = {
		update: function(bookingData) {
			var $block = $('.wcbs-add-to-calendar-block');
			if (!$block.length || !bookingData || !bookingData.date) return;

			var title = bookingData.title || 'Booking Appointment';
			var startIso = bookingData.startIso;
			var endIso   = bookingData.endIso;

			if (!startIso && bookingData.date) {
				try {
					var rawDate = bookingData.date.replace(/^[a-zA-Z]+,\s*/, '');
					var rawTime = (bookingData.time && bookingData.time.indexOf('Select') === -1) ? bookingData.time : '09:00';
					var timeParts = rawTime.split(/[–-]/);
					var startTimeStr = timeParts[0].trim();
					var endTimeStr = timeParts.length > 1 ? timeParts[1].trim() : '';

					var dObj = new Date(rawDate + ' ' + startTimeStr);
					if (!isNaN(dObj.getTime())) {
						startIso = dObj.toISOString();
						if (endTimeStr) {
							var eObj = new Date(rawDate + ' ' + endTimeStr);
							if (!isNaN(eObj.getTime())) {
								endIso = eObj.toISOString();
							}
						}
					}
				} catch (e) {}
			}

			if (!startIso) startIso = new Date().toISOString();
			if (!endIso) endIso = new Date(new Date(startIso).getTime() + 60*60*1000).toISOString();

			// Format for Google / Outlook: YYYYMMDDTHHmmssZ
			function formatCalDate(iso) {
				return iso.replace(/[-:]/g, '').replace(/\.\d{3}/, '');
			}

			var gStart = formatCalDate(startIso);
			var gEnd   = formatCalDate(endIso);

			// Google Calendar URL
			var googleUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE' +
				'&text=' + encodeURIComponent(title) +
				'&dates=' + gStart + '/' + gEnd +
				'&details=' + encodeURIComponent('Confirmed booking at ' + window.location.hostname);
			$block.find('.wcbs-cal-google').attr('href', googleUrl);

			// Outlook URL
			var outlookUrl = 'https://outlook.live.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent' +
				'&subject=' + encodeURIComponent(title) +
				'&startdt=' + encodeURIComponent(startIso) +
				'&enddt=' + encodeURIComponent(endIso) +
				'&body=' + encodeURIComponent('Confirmed booking at ' + window.location.hostname);
			$block.find('.wcbs-cal-outlook').attr('href', outlookUrl);

			// Apple / iCal Download
			$block.find('.wcbs-cal-apple').off('click').on('click', function(e) {
				e.preventDefault();
				var icsContent = [
					'BEGIN:VCALENDAR',
					'VERSION:2.0',
					'PRODID:-//Styler for WooCommerce Bookings//EN',
					'BEGIN:VEVENT',
					'SUMMARY:' + title,
					'DTSTART:' + gStart,
					'DTEND:' + gEnd,
					'DESCRIPTION:Booking confirmed at ' + window.location.hostname,
					'STATUS:CONFIRMED',
					'END:VEVENT',
					'END:VCALENDAR'
				].join('\r\n');

				var blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
				var link = document.createElement('a');
				link.href = window.URL.createObjectURL(blob);
				link.setAttribute('download', 'appointment.ics');
				document.body.appendChild(link);
				link.click();
				document.body.removeChild(link);
			});

			$block.fadeIn(200);
		}
	};

})(jQuery);
