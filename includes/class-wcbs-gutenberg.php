<?php
/**
 * Gutenberg Block Integration for Styler for WooCommerce Bookings.
 *
 * @package StylerForWooCommerceBookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCBS_Gutenberg {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	/**
	 * Register Gutenberg Server-Side Rendered Block.
	 */
	public function register_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type(
			'wcbs/booking-form',
			array(
				'attributes'      => array(
					'productId' => array(
						'type'    => 'number',
						'default' => 0,
					),
					'layout'    => array(
						'type'    => 'string',
						'default' => 'default',
					),
					'theme'     => array(
						'type'    => 'string',
						'default' => 'default',
					),
				),
				'render_callback' => array( $this, 'render_block' ),
			)
		);
	}

	/**
	 * Render callback for block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string HTML output.
	 */
	public function render_block( $attributes ) {
		$product_id = ! empty( $attributes['productId'] ) ? absint( $attributes['productId'] ) : 0;
		$layout     = ! empty( $attributes['layout'] ) ? sanitize_text_field( $attributes['layout'] ) : '';
		$theme      = ! empty( $attributes['theme'] ) ? sanitize_text_field( $attributes['theme'] ) : '';

		if ( ! $product_id && is_singular( 'product' ) ) {
			global $post;
			$product_id = $post ? $post->ID : 0;
		}

		if ( ! $product_id ) {
			return '<div class="wcbs-block-notice">' . esc_html__( 'Please select a bookable product in block settings.', 'styler-for-woocommerce-bookings' ) . '</div>';
		}

		return do_shortcode( sprintf( '[wc_booking_form product_id="%d" layout="%s" theme="%s"]', $product_id, esc_attr( $layout ), esc_attr( $theme ) ) );
	}
}

new WCBS_Gutenberg();
