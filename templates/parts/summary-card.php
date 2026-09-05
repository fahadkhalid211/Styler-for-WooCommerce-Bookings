<?php
/**
 * Dynamic Live Booking Summary Card Part.
 *
 * @package StylerForWooCommerceBookings
 *
 * Available variables:
 * @var WC_Product $product Current bookable product.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$thumb = $product ? wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'thumbnail' ) : false;
?>
<div class="wcbs-summary-card">
	<div class="wcbs-summary-header">
		<?php if ( $thumb ) : ?>
			<img src="<?php echo esc_url( $thumb[0] ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" class="wcbs-summary-thumb" />
		<?php endif; ?>
		<div class="wcbs-summary-product-info">
			<h4 class="wcbs-summary-title"><?php echo esc_html( $product ? $product->get_name() : __( 'Your Booking', 'styler-for-woocommerce-bookings' ) ); ?></h4>
			<span class="wcbs-summary-badge"><?php esc_html_e( 'Appointment Summary', 'styler-for-woocommerce-bookings' ); ?></span>
		</div>
	</div>

	<div class="wcbs-summary-details">
		<!-- Selected Resource/Staff -->
		<div class="wcbs-summary-row wcbs-summary-resource" style="display:none;">
			<div class="wcbs-summary-label">
				<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
					<circle cx="12" cy="7" r="4"></circle>
				</svg>
				<span><?php esc_html_e( 'Staff / Resource:', 'styler-for-woocommerce-bookings' ); ?></span>
			</div>
			<div class="wcbs-summary-val wcbs-val-resource">—</div>
		</div>

		<!-- Selected Date -->
		<div class="wcbs-summary-row wcbs-summary-date">
			<div class="wcbs-summary-label">
				<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
					<line x1="16" y1="2" x2="16" y2="6"></line>
					<line x1="8" y1="2" x2="8" y2="6"></line>
					<line x1="3" y1="10" x2="21" y2="10"></line>
				</svg>
				<span><?php esc_html_e( 'Date:', 'styler-for-woocommerce-bookings' ); ?></span>
			</div>
			<div class="wcbs-summary-val wcbs-val-date"><?php esc_html_e( 'Select a date', 'styler-for-woocommerce-bookings' ); ?></div>
		</div>

		<!-- Selected Time Slot -->
		<div class="wcbs-summary-row wcbs-summary-time">
			<div class="wcbs-summary-label">
				<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="12" cy="12" r="10"></circle>
					<polyline points="12 6 12 12 16 14"></polyline>
				</svg>
				<span><?php esc_html_e( 'Time Slot:', 'styler-for-woocommerce-bookings' ); ?></span>
			</div>
			<div class="wcbs-summary-val wcbs-val-time"><?php esc_html_e( 'Select a time', 'styler-for-woocommerce-bookings' ); ?></div>
		</div>

		<!-- Persons / Guests -->
		<div class="wcbs-summary-row wcbs-summary-persons" style="display:none;">
			<div class="wcbs-summary-label">
				<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
					<circle cx="9" cy="7" r="4"></circle>
					<path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
					<path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
				</svg>
				<span><?php esc_html_e( 'Guests / Persons:', 'styler-for-woocommerce-bookings' ); ?></span>
			</div>
			<div class="wcbs-summary-val wcbs-val-persons">1</div>
		</div>
	</div>

	<!-- Price Breakdown Section -->
	<div class="wcbs-summary-cost-box">
		<div class="wcbs-summary-cost-label"><?php esc_html_e( 'Calculated Total:', 'styler-for-woocommerce-bookings' ); ?></div>
		<div class="wcbs-summary-cost-amount">
			<span class="wcbs-price-display">—</span>
		</div>
	</div>

	<div class="wcbs-summary-guarantee">
		<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
		</svg>
		<span><?php esc_html_e( 'Instant Booking Confirmation', 'styler-for-woocommerce-bookings' ); ?></span>
	</div>
</div>
