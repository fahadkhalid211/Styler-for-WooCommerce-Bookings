<?php
/**
 * Time Slot Morning / Afternoon / Evening Filter Tabs.
 *
 * @package StylerForWooCommerceBookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wcbs-slot-tabs-nav" role="tablist">
	<button type="button" class="wcbs-slot-tab-btn active" data-period="all" role="tab" aria-selected="true">
		<span><?php esc_html_e( 'All Times', 'styler-for-woocommerce-bookings' ); ?></span>
	</button>
	<button type="button" class="wcbs-slot-tab-btn" data-period="morning" role="tab" aria-selected="false">
		<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<circle cx="12" cy="12" r="5"></circle>
			<line x1="12" y1="1" x2="12" y2="3"></line>
			<line x1="12" y1="21" x2="12" y2="23"></line>
			<line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
			<line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
		</svg>
		<span><?php esc_html_e( 'Morning', 'styler-for-woocommerce-bookings' ); ?></span>
		<small class="wcbs-tab-count">(0)</small>
	</button>
	<button type="button" class="wcbs-slot-tab-btn" data-period="afternoon" role="tab" aria-selected="false">
		<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<circle cx="12" cy="12" r="4"></circle>
			<path d="M12 2v2"></path>
			<path d="M12 20v2"></path>
			<path d="m4.93 4.93 1.41 1.41"></path>
			<path d="m17.66 17.66 1.41 1.41"></path>
			<path d="M2 12h2"></path>
			<path d="M20 12h2"></path>
		</svg>
		<span><?php esc_html_e( 'Afternoon', 'styler-for-woocommerce-bookings' ); ?></span>
		<small class="wcbs-tab-count">(0)</small>
	</button>
	<button type="button" class="wcbs-slot-tab-btn" data-period="evening" role="tab" aria-selected="false">
		<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
		</svg>
		<span><?php esc_html_e( 'Evening', 'styler-for-woocommerce-bookings' ); ?></span>
		<small class="wcbs-tab-count">(0)</small>
	</button>
</div>
