<?php
/**
 * Universal Shortcode Handler for Styler for WooCommerce Bookings.
 *
 * @package StylerForWooCommerceBookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCBS_Shortcode {

	/**
	 * Frontend instance.
	 *
	 * @var WCBS_Frontend
	 */
	protected $frontend;

	/**
	 * Constructor.
	 *
	 * @param WCBS_Frontend $frontend Frontend instance.
	 */
	public function __construct( WCBS_Frontend $frontend ) {
		$this->frontend = $frontend;
		add_shortcode( 'wc_booking_form', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Render [wc_booking_form id="123" layout="wizard" theme="clean"] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'         => 0,
				'product_id' => 0,
				'layout'     => '',
				'theme'      => '',
			),
			$atts,
			'wc_booking_form'
		);

		$product_id = absint( $atts['id'] ?: $atts['product_id'] );
		if ( ! $product_id ) {
			global $post;
			$product_id = $post ? $post->ID : 0;
		}

		if ( ! $product_id ) {
			return '<p class="wcbs-error">' . esc_html__( 'Please specify a valid product_id for the booking form.', 'styler-for-woocommerce-bookings' ) . '</p>';
		}

		$product = wc_get_product( $product_id );
		if ( ! $product || ! is_a( $product, 'WC_Product_Booking' ) ) {
			return '<p class="wcbs-error">' . esc_html__( 'Specified product is not a valid WooCommerce Bookable product.', 'styler-for-woocommerce-bookings' ) . '</p>';
		}

		// Save current global product.
		global $product;
		$original_product = $product;
		$product          = wc_get_product( $product_id );

		// Temporarily apply shortcode layout & theme overrides if provided.
		if ( ! empty( $atts['layout'] ) ) {
			add_filter(
				'wcbs_filter_layout',
				function() use ( $atts ) {
					return sanitize_text_field( $atts['layout'] );
				}
			);
		}

		ob_start();

		// Enqueue frontend scripts and styles if not already loaded.
		$this->frontend->enqueue_assets();

		echo '<div class="wcbs-shortcode-container" data-product-id="' . esc_attr( $product_id ) . '">';
		woocommerce_template_single_add_to_cart();
		echo '</div>';

		// Restore original product.
		$product = $original_product;

		return ob_get_clean();
	}
}
