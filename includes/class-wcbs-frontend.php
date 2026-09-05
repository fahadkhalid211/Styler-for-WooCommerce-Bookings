<?php
/**
 * Frontend Controller for Styler for WooCommerce Bookings.
 *
 * @package StylerForWooCommerceBookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCBS_Frontend {

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

		// Enqueue scripts & styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 25 );

		// WooCommerce Booking Form Wrappers.
		add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'render_wrapper_start' ), 5 );
		add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'render_wrapper_end' ), 25 );

		// Body class.
		add_filter( 'body_class', array( $this, 'add_body_classes' ) );
	}

	/**
	 * Determine if current page has a bookable product or shortcode.
	 *
	 * @return bool
	 */
	public function is_booking_page() {
		if ( is_product() ) {
			global $post;
			if ( $post && function_exists( 'wc_get_product' ) ) {
				$product = wc_get_product( $post->ID );
				if ( $product && ( is_a( $product, 'WC_Product_Booking' ) || ( method_exists( $product, 'is_type' ) && $product->is_type( 'booking' ) ) ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Get the effective layout for the current product (checking per-product override).
	 *
	 * @param int|null $product_id Optional product ID.
	 * @return string
	 */
	public function get_effective_layout( $product_id = null ) {
		if ( ! $product_id && is_product() ) {
			global $post;
			$product_id = $post ? $post->ID : null;
		}

		if ( $product_id ) {
			$override = get_post_meta( $product_id, '_wcbs_product_layout', true );
			if ( ! empty( $override ) && 'default' !== $override ) {
				return sanitize_text_field( $override );
			}
		}

		return $this->settings->get( 'default_layout', 'split' );
	}

	/**
	 * Get the effective theme for the current product (checking per-product override).
	 *
	 * @param int|null $product_id Optional product ID.
	 * @return string
	 */
	public function get_effective_theme( $product_id = null ) {
		if ( ! $product_id && is_product() ) {
			global $post;
			$product_id = $post ? $post->ID : null;
		}

		if ( $product_id ) {
			$override = get_post_meta( $product_id, '_wcbs_product_theme', true );
			if ( ! empty( $override ) && 'default' !== $override ) {
				return sanitize_text_field( $override );
			}
		}

		return $this->settings->get( 'active_theme', 'clean' );
	}

	/**
	 * Enqueue frontend CSS and JS assets.
	 */
	public function enqueue_assets() {
		$ver = defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : WCBS_VERSION;

		// Enqueue styles.
		wp_enqueue_style( 'wcbs-animations', WCBS_ASSETS_URL . 'css/animations.css', array(), $ver );
		wp_enqueue_style( 'wcbs-themes', WCBS_ASSETS_URL . 'css/themes.css', array( 'wcbs-animations' ), $ver );
		wp_enqueue_style( 'wcbs-layouts', WCBS_ASSETS_URL . 'css/layouts.css', array( 'wcbs-themes' ), $ver );
		wp_enqueue_style( 'wcbs-frontend', WCBS_ASSETS_URL . 'css/frontend-main.css', array( 'wcbs-layouts' ), $ver );

		// Dynamic CSS variables.
		$inline_css = $this->settings->generate_css_variables();
		wp_add_inline_style( 'wcbs-frontend', $inline_css );

		// Enqueue scripts.
		wp_enqueue_script( 'wcbs-timezone', WCBS_ASSETS_URL . 'js/timezone-helper.js', array( 'jquery' ), $ver, true );
		wp_enqueue_script( 'wcbs-add-to-cal', WCBS_ASSETS_URL . 'js/add-to-calendar.js', array( 'jquery' ), $ver, true );
		wp_enqueue_script( 'wcbs-drawer-modal', WCBS_ASSETS_URL . 'js/layout-drawer-modal.js', array( 'jquery' ), $ver, true );
		wp_enqueue_script( 'wcbs-wizard', WCBS_ASSETS_URL . 'js/layout-wizard.js', array( 'jquery' ), $ver, true );
		wp_enqueue_script( 'wcbs-main', WCBS_ASSETS_URL . 'js/frontend-main.js', array( 'jquery', 'wcbs-wizard', 'wcbs-drawer-modal' ), $ver, true );

		// Localize parameters for scripts.
		$current_layout = $this->get_effective_layout();
		$current_theme  = $this->get_effective_theme();

		wp_localize_script(
			'wcbs-main',
			'wcbs_params',
			array(
				'ajax_url'          => admin_url( 'admin-ajax.php' ),
				'nonce'             => wp_create_nonce( 'wcbs_frontend_nonce' ),
				'layout'            => $current_layout,
				'theme'             => $current_theme,
				'enable_staff'      => $this->settings->get( 'enable_staff_cards', 'yes' ),
				'enable_tabs'       => $this->settings->get( 'enable_slot_tabs', 'yes' ),
				'slot_style'        => $this->settings->get( 'slot_style', 'chips' ),
				'enable_urgency'    => $this->settings->get( 'enable_urgency_badge', 'yes' ),
				'urgency_threshold' => absint( $this->settings->get( 'urgency_threshold', 2 ) ),
				'enable_summary'    => $this->settings->get( 'enable_live_summary', 'yes' ),
				'enable_timezone'   => $this->settings->get( 'enable_timezone', 'yes' ),
				'enable_calendar'   => $this->settings->get( 'enable_calendar_sync', 'yes' ),
				'enable_skeleton'   => $this->settings->get( 'enable_skeleton', 'yes' ),
				'i18n'              => array(
					'step1'        => __( 'Service & Staff', 'styler-for-woocommerce-bookings' ),
					'step2'        => __( 'Date & Time', 'styler-for-woocommerce-bookings' ),
					'step3'        => __( 'Guests & Extras', 'styler-for-woocommerce-bookings' ),
					'step4'        => __( 'Confirm & Book', 'styler-for-woocommerce-bookings' ),
					'next'         => __( 'Continue', 'styler-for-woocommerce-bookings' ),
					'prev'         => __( 'Back', 'styler-for-woocommerce-bookings' ),
					'morning'      => __( 'Morning', 'styler-for-woocommerce-bookings' ),
					'afternoon'    => __( 'Afternoon', 'styler-for-woocommerce-bookings' ),
					'evening'      => __( 'Evening', 'styler-for-woocommerce-bookings' ),
					'urgency_text' => __( 'Only %d spot left!', 'styler-for-woocommerce-bookings' ),
					'spots_left'   => __( 'Only %d spots left!', 'styler-for-woocommerce-bookings' ),
					'book_now'     => $this->settings->get( 'drawer_button_text', __( 'Book Appointment', 'styler-for-woocommerce-bookings' ) ),
					'close'        => __( 'Close', 'styler-for-woocommerce-bookings' ),
				),
			)
		);
	}

	/**
	 * Render the opening HTML wrapper for the booking form.
	 */
	public function render_wrapper_start() {
		global $product;
		if ( ! $product && function_exists( 'wc_get_product' ) ) {
			global $post;
			if ( $post ) {
				$product = wc_get_product( $post->ID );
			}
		}

		if ( ! $product || ! ( is_a( $product, 'WC_Product_Booking' ) || ( method_exists( $product, 'is_type' ) && $product->is_type( 'booking' ) ) ) ) {
			return;
		}

		$product_id = $product->get_id();
		$layout     = $this->get_effective_layout( $product_id );
		$theme      = $this->get_effective_theme( $product_id );
		$settings   = $this->settings;

		include WCBS_TEMPLATES_PATH . 'wrapper-start.php';
	}

	/**
	 * Render the closing HTML wrapper for the booking form.
	 */
	public function render_wrapper_end() {
		global $product;
		if ( ! $product && function_exists( 'wc_get_product' ) ) {
			global $post;
			if ( $post ) {
				$product = wc_get_product( $post->ID );
			}
		}

		if ( ! $product || ! ( is_a( $product, 'WC_Product_Booking' ) || ( method_exists( $product, 'is_type' ) && $product->is_type( 'booking' ) ) ) ) {
			return;
		}

		$product_id = $product->get_id();
		$layout     = $this->get_effective_layout( $product_id );
		$theme      = $this->get_effective_theme( $product_id );
		$settings   = $this->settings;

		include WCBS_TEMPLATES_PATH . 'wrapper-end.php';
	}

	/**
	 * Add body classes for active layout & theme.
	 *
	 * @param array $classes Existing classes.
	 * @return array
	 */
	public function add_body_classes( $classes ) {
		if ( $this->is_booking_page() ) {
			$layout    = $this->get_effective_layout();
			$theme     = $this->get_effective_theme();
			$classes[] = 'wcbs-active';
			$classes[] = 'wcbs-layout-' . sanitize_html_class( $layout );
			$classes[] = 'wcbs-theme-' . sanitize_html_class( $theme );
		}
		return $classes;
	}
}
