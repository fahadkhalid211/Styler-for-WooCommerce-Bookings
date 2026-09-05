<?php
/**
 * Booking Form Opening Wrapper Template.
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

$product_id = $product ? $product->get_id() : 0;
$btn_text   = get_post_meta( $product_id, '_wcbs_drawer_btn_text', true ) ?: $settings->get( 'drawer_button_text', __( 'Book Appointment', 'styler-for-woo' ) );
$pos        = $settings->get( 'drawer_position', 'right' );
?>

<!-- START: Styler for WooCommerce Bookings Container -->
<div class="wcbs-root-container wcbs-layout-<?php echo esc_attr( $layout ); ?> wcbs-theme-<?php echo esc_attr( $theme ); ?>"
     data-wcbs-layout="<?php echo esc_attr( $layout ); ?>"
     data-wcbs-theme="<?php echo esc_attr( $theme ); ?>"
     data-product-id="<?php echo esc_attr( $product_id ); ?>">

	<?php if ( 'drawer' === $layout || 'modal' === $layout ) : ?>
		<!-- Trigger button for Drawer/Modal -->
		<div class="wcbs-trigger-wrapper">
			<button type="button" class="wcbs-open-trigger-btn button alt" data-wcbs-target="<?php echo esc_attr( $layout ); ?>">
				<svg class="wcbs-icon-cal" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
					<line x1="16" y1="2" x2="16" y2="6"></line>
					<line x1="8" y1="2" x2="8" y2="6"></line>
					<line x1="3" y1="10" x2="21" y2="10"></line>
				</svg>
				<span><?php echo esc_html( $btn_text ); ?></span>
			</button>
		</div>

		<!-- Backdrop overlay -->
		<div class="wcbs-overlay-backdrop" style="display:none;" aria-hidden="true"></div>

		<!-- Container (Drawer Panel or Modal Dialog) -->
		<div class="wcbs-popup-container wcbs-popup-<?php echo esc_attr( $layout ); ?> wcbs-drawer-<?php echo esc_attr( $pos ); ?>" style="display:none;" role="dialog" aria-modal="true">
			<div class="wcbs-popup-header">
				<h3 class="wcbs-popup-title"><?php echo esc_html( $product ? $product->get_name() : __( 'Book Appointment', 'styler-for-woo' ) ); ?></h3>
				<button type="button" class="wcbs-popup-close-btn" aria-label="<?php esc_attr_e( 'Close', 'styler-for-woo' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<line x1="18" y1="6" x2="6" y2="18"></line>
						<line x1="6" y1="6" x2="18" y2="18"></line>
					</svg>
				</button>
			</div>
			<div class="wcbs-popup-content">
	<?php endif; ?>

	<?php if ( 'wizard' === $layout ) : ?>
		<!-- Wizard Progress Tracker Header -->
		<div class="wcbs-wizard-nav" role="tablist">
			<div class="wcbs-wizard-step active" data-step="1">
				<div class="wcbs-step-badge">1</div>
				<div class="wcbs-step-meta">
					<span class="wcbs-step-label"><?php esc_html_e( 'Step 1', 'styler-for-woo' ); ?></span>
					<span class="wcbs-step-title"><?php esc_html_e( 'Service & Staff', 'styler-for-woo' ); ?></span>
				</div>
			</div>
			<div class="wcbs-wizard-divider"></div>
			<div class="wcbs-wizard-step" data-step="2">
				<div class="wcbs-step-badge">2</div>
				<div class="wcbs-step-meta">
					<span class="wcbs-step-label"><?php esc_html_e( 'Step 2', 'styler-for-woo' ); ?></span>
					<span class="wcbs-step-title"><?php esc_html_e( 'Date & Time', 'styler-for-woo' ); ?></span>
				</div>
			</div>
			<div class="wcbs-wizard-divider"></div>
			<div class="wcbs-wizard-step" data-step="3">
				<div class="wcbs-step-badge">3</div>
				<div class="wcbs-step-meta">
					<span class="wcbs-step-label"><?php esc_html_e( 'Step 3', 'styler-for-woo' ); ?></span>
					<span class="wcbs-step-title"><?php esc_html_e( 'Guests & Details', 'styler-for-woo' ); ?></span>
				</div>
			</div>
			<div class="wcbs-wizard-divider"></div>
			<div class="wcbs-wizard-step" data-step="4">
				<div class="wcbs-step-badge">4</div>
				<div class="wcbs-step-meta">
					<span class="wcbs-step-label"><?php esc_html_e( 'Step 4', 'styler-for-woo' ); ?></span>
					<span class="wcbs-step-title"><?php esc_html_e( 'Confirm & Pay', 'styler-for-woo' ); ?></span>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php
	// Timezone detection bar.
	if ( 'yes' === $settings->get( 'enable_timezone', 'yes' ) ) {
		include WCBS_TEMPLATES_PATH . 'parts/timezone-bar.php';
	}

	// Staff / Resource Cards.
	if ( 'yes' === $settings->get( 'enable_staff_cards', 'yes' ) ) {
		include WCBS_TEMPLATES_PATH . 'parts/resource-cards.php';
	}
	?>

