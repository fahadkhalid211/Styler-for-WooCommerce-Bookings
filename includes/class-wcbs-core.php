<?php
/**
 * Core Orchestrator Class for Styler for WooCommerce Bookings.
 *
 * @package StylerForWooCommerceBookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCBS_Core {

	/**
	 * Single instance of the class.
	 *
	 * @var WCBS_Core|null
	 */
	private static $instance = null;

	/**
	 * Settings manager instance.
	 *
	 * @var WCBS_Settings
	 */
	public $settings;

	/**
	 * Frontend manager instance.
	 *
	 * @var WCBS_Frontend
	 */
	public $frontend;

	/**
	 * Admin manager instance.
	 *
	 * @var WCBS_Admin|null
	 */
	public $admin;

	/**
	 * Shortcode manager instance.
	 *
	 * @var WCBS_Shortcode
	 */
	public $shortcode;

	/**
	 * Product meta manager instance.
	 *
	 * @var WCBS_Product_Meta
	 */
	public $product_meta;

	/**
	 * AJAX handler instance.
	 *
	 * @var WCBS_Ajax
	 */
	public $ajax;

	/**
	 * Get singleton instance.
	 *
	 * @return WCBS_Core
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->init_components();
		$this->init_hooks();
	}

	/**
	 * Load required module classes.
	 */
	private function load_dependencies() {
		require_once WCBS_PATH . 'includes/class-wcbs-settings.php';
		require_once WCBS_PATH . 'includes/class-wcbs-frontend.php';
		require_once WCBS_PATH . 'includes/class-wcbs-product-meta.php';
		require_once WCBS_PATH . 'includes/class-wcbs-shortcode.php';
		require_once WCBS_PATH . 'includes/class-wcbs-ajax.php';

		if ( is_admin() ) {
			require_once WCBS_PATH . 'admin/class-wcbs-admin.php';
		}

		// Elementor Integration.
		add_action( 'elementor/widgets/register', array( $this, 'register_elementor_widget' ) );

		// Gutenberg Block Integration.
		require_once WCBS_PATH . 'includes/class-wcbs-gutenberg.php';
	}

	/**
	 * Initialize main sub-components.
	 */
	private function init_components() {
		$this->settings     = new WCBS_Settings();
		$this->frontend     = new WCBS_Frontend( $this->settings );
		$this->product_meta = new WCBS_Product_Meta();
		$this->shortcode    = new WCBS_Shortcode( $this->frontend );
		$this->ajax         = new WCBS_Ajax( $this->settings );

		if ( is_admin() ) {
			$this->admin = new WCBS_Admin( $this->settings );
		}
	}

	/**
	 * Initialize plugin hooks.
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load plugin textdomain for translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'styler-for-woocommerce-bookings',
			false,
			dirname( WCBS_BASE_NAME ) . '/languages/'
		);
	}

	/**
	 * Register Elementor Widget if Elementor is present.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_elementor_widget( $widgets_manager ) {
		if ( class_exists( '\Elementor\Widget_Base' ) ) {
			require_once WCBS_PATH . 'includes/class-wcbs-elementor.php';
			$widgets_manager->register( new WCBS_Elementor_Widget() );
		}
	}
}
