<?php
/**
 * Timezone Detection and Conversion Bar Part.
 *
 * @package StylerForWooCommerceBookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wcbs-timezone-bar">
	<div class="wcbs-timezone-display">
		<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<circle cx="12" cy="12" r="10"></circle>
			<line x1="2" y1="12" x2="22" y2="12"></line>
			<path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
		</svg>
		<span><?php esc_html_e( 'Displaying times in:', 'styler-for-woo' ); ?></span>
		<strong class="wcbs-current-timezone"><?php esc_html_e( 'Detecting timezone...', 'styler-for-woo' ); ?></strong>
	</div>
	<button type="button" class="wcbs-timezone-change-btn" title="<?php esc_attr_e( 'Change timezone', 'styler-for-woo' ); ?>">
		<?php esc_html_e( 'Change', 'styler-for-woo' ); ?>
	</button>
	<div class="wcbs-timezone-dropdown" style="display:none;">
		<select class="wcbs-timezone-select">
			<option value="auto"><?php esc_html_e( 'Auto-detect local timezone', 'styler-for-woo' ); ?></option>
			<option value="UTC">UTC (GMT+0)</option>
			<option value="America/New_York">Eastern Time (US & Canada)</option>
			<option value="America/Chicago">Central Time (US & Canada)</option>
			<option value="America/Denver">Mountain Time (US & Canada)</option>
			<option value="America/Los_Angeles">Pacific Time (US & Canada)</option>
			<option value="Europe/London">London (GMT / BST)</option>
			<option value="Europe/Paris">Central European Time (Paris, Berlin)</option>
			<option value="Asia/Dubai">Gulf Standard Time (Dubai)</option>
			<option value="Asia/Karachi">Pakistan Standard Time (PKT)</option>
			<option value="Asia/Kolkata">India Standard Time (IST)</option>
			<option value="Asia/Singapore">Singapore / Hong Kong (SGT)</option>
			<option value="Asia/Tokyo">Japan Standard Time (JST)</option>
			<option value="Australia/Sydney">Australian Eastern Time (Sydney)</option>
		</select>
	</div>
</div>
