<?php
/**
 * Add to Calendar Links Template Part.
 *
 * @package StylerForWooCommerceBookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wcbs-add-to-calendar-block" style="display:none;">
	<div class="wcbs-cal-sync-title">
		<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
			<polyline points="17 21 17 13 7 13 7 21"></polyline>
			<polyline points="7 3 7 8 15 8"></polyline>
		</svg>
		<span><?php esc_html_e( 'Sync to Personal Calendar:', 'styler-for-woo' ); ?></span>
	</div>
	<div class="wcbs-cal-buttons-group">
		<a href="#" class="wcbs-cal-btn wcbs-cal-google" target="_blank" rel="noopener noreferrer">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
				<path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm3.8 14.2H8.2V8.8h7.6v7.4z"/>
			</svg>
			<span>Google Calendar</span>
		</a>
		<a href="#" class="wcbs-cal-btn wcbs-cal-apple">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
				<path d="M18.7 19.5c-.8 1.2-1.7 2.4-3 2.5-1.4.1-1.8-.8-3.3-.8-1.5 0-2 .8-3.3.8-1.3 0-2.3-1.3-3.2-2.5-1.8-2.6-3.1-7.3-1.3-10.5 1-1.6 2.7-2.6 4.5-2.6 1.4 0 2.6.9 3.4.9.8 0 2.3-1.1 3.9-.9 1.5.2 2.8.9 3.6 2.1-3.2 1.9-2.7 6.1.4 7.4-.7 1.4-1.5 2.6-2.4 3.6zM15.5 5.5c.7-.9 1.1-2 1-3.2-1 .1-2.2.7-2.9 1.5-.6.7-1.1 1.9-1 3.1 1.1.1 2.2-.6 2.9-1.4z"/>
			</svg>
			<span>Apple / iCal</span>
		</a>
		<a href="#" class="wcbs-cal-btn wcbs-cal-outlook" target="_blank" rel="noopener noreferrer">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
				<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-2h2v2zm0-4h-2V7h2v5z"/>
			</svg>
			<span>Outlook</span>
		</a>
	</div>
</div>
