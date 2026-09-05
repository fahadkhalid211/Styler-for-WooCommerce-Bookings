<?php
/**
 * Admin Panel Controller for Styler for WooCommerce Bookings.
 *
 * @package StylerForWooCommerceBookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCBS_Admin {

	/**
	 * Settings instance.
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

		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 60 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Register Admin Submenu under Bookings or WooCommerce.
	 */
	public function register_admin_menu() {
		// Prefer 'edit.php?post_type=wc_booking' (WooCommerce Bookings menu) if available.
		$parent_slug = 'edit.php?post_type=wc_booking';

		// Fallback to WooCommerce top menu if Bookings menu isn't top-level.
		global $menu;
		$has_booking_menu = false;
		if ( is_array( $menu ) ) {
			foreach ( $menu as $item ) {
				if ( isset( $item[2] ) && $item[2] === $parent_slug ) {
					$has_booking_menu = true;
					break;
				}
			}
		}

		if ( ! $has_booking_menu ) {
			$parent_slug = 'woocommerce';
		}

		add_submenu_page(
			$parent_slug,
			__( 'Booking Styler & Layouts', 'styler-for-woo' ),
			__( 'Booking Styler', 'styler-for-woo' ),
			'manage_options',
			'wcbs-booking-styler',
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Enqueue Admin Scripts & Styles for the customizer page.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( strpos( $hook, 'wcbs-booking-styler' ) === false ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'wcbs-admin-css', WCBS_URL . 'admin/css/admin-customizer.css', array( 'wp-color-picker' ), WCBS_VERSION );

		// Frontend styles for the live preview inside the admin customizer.
		wp_enqueue_style( 'wcbs-animations', WCBS_ASSETS_URL . 'css/animations.css', array(), WCBS_VERSION );
		wp_enqueue_style( 'wcbs-themes', WCBS_ASSETS_URL . 'css/themes.css', array( 'wcbs-animations' ), WCBS_VERSION );
		wp_enqueue_style( 'wcbs-layouts', WCBS_ASSETS_URL . 'css/layouts.css', array( 'wcbs-themes' ), WCBS_VERSION );
		wp_enqueue_style( 'wcbs-frontend', WCBS_ASSETS_URL . 'css/frontend-main.css', array( 'wcbs-layouts' ), WCBS_VERSION );

		// Dynamic styles.
		$inline_css = $this->settings->generate_css_variables();
		wp_add_inline_style( 'wcbs-frontend', $inline_css );

		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'wcbs-admin-js', WCBS_URL . 'admin/js/admin-customizer.js', array( 'jquery', 'wp-color-picker' ), WCBS_VERSION, true );

		wp_localize_script(
			'wcbs-admin-js',
			'wcbs_admin_data',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'wcbs_admin_nonce' ),
				'settings' => $this->settings->get_all(),
				'presets'  => WCBS_Settings::get_presets(),
				'i18n'     => array(
					'saved'      => __( 'Changes Saved Successfully!', 'styler-for-woo' ),
					'saving'     => __( 'Saving Changes...', 'styler-for-woo' ),
					'resetting'  => __( 'Resetting to Defaults...', 'styler-for-woo' ),
					'confirm_reset' => __( 'Are you sure you want to reset all customizer settings to factory defaults?', 'styler-for-woo' ),
					'error'      => __( 'Error saving settings. Please try again.', 'styler-for-woo' ),
				),
			)
		);
	}

	/**
	 * Render the Customizer Admin View.
	 */
	public function render_admin_page() {
		$settings = $this->settings->get_all();
		$presets  = WCBS_Settings::get_presets();
		include WCBS_PATH . 'admin/views/customizer-view.php';
	}
}
