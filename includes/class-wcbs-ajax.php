<?php
/**
 * AJAX Controller for Styler for WooCommerce Bookings.
 *
 * @package StylerForWooCommerceBookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCBS_Ajax {

	/**
	 * Settings manager.
	 *
	 * @var WCBS_Settings
	 */
	protected $settings;

	/**
	 * Constructor.
	 *
	 * @param WCBS_Settings $settings Settings instance.
	 */
	public function __construct( WCBS_Settings $settings ) {
		$this->settings = $settings;

		add_action( 'wp_ajax_wcbs_save_customizer_settings', array( $this, 'save_customizer_settings' ) );
		add_action( 'wp_ajax_wcbs_reset_customizer_settings', array( $this, 'reset_customizer_settings' ) );
		add_action( 'wp_ajax_wcbs_get_preview_styles', array( $this, 'get_preview_styles' ) );
		add_action( 'wp_ajax_nopriv_wcbs_debug_info', array( $this, 'debug_info' ) );
		add_action( 'wp_ajax_wcbs_debug_info', array( $this, 'debug_info' ) );
	}

	/**
	 * Save customizer settings via AJAX.
	 */
	public function save_customizer_settings() {
		check_ajax_referer( 'wcbs_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'styler-for-woo' ) ) );
		}

		$raw_data = isset( $_POST['settings'] ) ? (array) $_POST['settings'] : array();
		$success  = $this->settings->update( $raw_data );

		if ( $success ) {
			wp_send_json_success( array( 'message' => __( 'Settings successfully saved!', 'styler-for-woo' ) ) );
		} else {
			// Even if settings were identical (update_option returns false), consider it success.
			wp_send_json_success( array( 'message' => __( 'Settings up to date.', 'styler-for-woo' ) ) );
		}
	}

	/**
	 * Reset customizer settings to factory defaults.
	 */
	public function reset_customizer_settings() {
		check_ajax_referer( 'wcbs_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'styler-for-woo' ) ) );
		}

		$defaults = WCBS_Settings::get_defaults();
		$this->settings->update( $defaults );

		wp_send_json_success(
			array(
				'message'  => __( 'Settings reset to factory defaults.', 'styler-for-woo' ),
				'defaults' => $defaults,
			)
		);
	}

	/**
	 * Generate real-time CSS variables for the preview frame.
	 */
	public function get_preview_styles() {
		check_ajax_referer( 'wcbs_admin_nonce', 'nonce' );

		$raw_data = isset( $_POST['settings'] ) ? (array) $_POST['settings'] : array();
		$css      = $this->settings->generate_css_variables( $raw_data );

		wp_send_json_success( array( 'css' => $css ) );
	}

	/**
	 * Diagnostic dump for WooCommerce Bookings templates and product form.
	 */
	public function debug_info() {
		$out = array();
		$plugin_dir = WP_PLUGIN_DIR . '/woocommerce-bookings';
		if ( is_dir( $plugin_dir ) ) {
			$files = glob( $plugin_dir . '/templates/booking-form/*.php' );
			if ( ! empty( $files ) ) {
				foreach ( $files as $f ) {
					$out['templates'][ basename( $f ) ] = file_get_contents( $f );
				}
			}
		}
		if ( function_exists( 'wc_get_product' ) ) {
			$prod = wc_get_product( 54 );
			if ( $prod ) {
				$out['product_type']   = $prod->get_type();
				$out['duration_type']  = method_exists( $prod, 'get_duration_type' ) ? $prod->get_duration_type() : '';
				$out['duration_unit']  = method_exists( $prod, 'get_duration_unit' ) ? $prod->get_duration_unit() : '';
				if ( class_exists( 'WC_Booking_Form' ) ) {
					ob_start();
					$bf = new WC_Booking_Form( $prod );
					$bf->output();
					$out['booking_form_html'] = ob_get_clean();
				}
			}
		}
		wp_send_json( $out );
	}
}
