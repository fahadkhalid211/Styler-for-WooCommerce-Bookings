<?php
/**
 * Booking Form Closing Wrapper Template.
 *
 * @package StylerForWooCommerceBookings
 *
 * Available variables:
 * @var string        $layout   Active layout ('split', 'wizard', 'drawer', 'modal', 'bottom-sheet', 'standard').
 * @var string        $theme    Active theme ('clean', 'dark', 'luxury', 'vibrant', 'custom').
 * @var WCBS_Settings $settings Settings instance.
 * @var WC_Product    $product  Current product.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$enable_summary = 'yes' === $settings->get( 'enable_live_summary', 'yes' );
?>

	<?php if ( $enable_summary ) : ?>
		<div class="wcbs-summary-wrapper">
			<?php include WCBS_TEMPLATES_PATH . 'parts/summary-card.php'; ?>
		</div>
	<?php endif; ?>

	<?php if ( 'wizard' === $layout ) : ?>
		<!-- Wizard Bottom Action Navigation Controls -->
		<div class="wcbs-wizard-controls">
			<button type="button" class="wcbs-wizard-btn wcbs-wizard-prev-btn button" style="display:none;">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<polyline points="15 18 9 12 15 6"></polyline>
				</svg>
				<span><?php esc_html_e( 'Back', 'styler-for-woocommerce-bookings' ); ?></span>
			</button>

			<button type="button" class="wcbs-wizard-btn wcbs-wizard-next-btn button alt">
				<span><?php esc_html_e( 'Continue', 'styler-for-woocommerce-bookings' ); ?></span>
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<polyline points="9 18 15 12 9 6"></polyline>
				</svg>
			</button>
		</div>
	<?php endif; ?>

	<?php
	// Add to Calendar preview block.
	if ( 'yes' === $settings->get( 'enable_calendar_sync', 'yes' ) ) {
		include WCBS_TEMPLATES_PATH . 'parts/add-to-calendar.php';
	}
	?>

	<?php if ( 'drawer' === $layout || 'modal' === $layout ) : ?>
			</div><!-- /.wcbs-popup-content -->
		</div><!-- /.wcbs-popup-container -->
	<?php endif; ?>

</div><!-- /.wcbs-root-container -->
<!-- END: Styler for WooCommerce Bookings Container -->
