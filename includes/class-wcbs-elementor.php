<?php
/**
 * Elementor Custom Widget for Styler for WooCommerce Bookings.
 *
 * @package StylerForWooCommerceBookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCBS_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wcbs_booking_form';
	}

	public function get_title() {
		return __( 'WooCommerce Booking Styler', 'styler-for-woocommerce-bookings' );
	}

	public function get_icon() {
		return 'eicon-calendar';
	}

	public function get_categories() {
		return array( 'woocommerce-elements', 'general' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Booking Form Settings', 'styler-for-woocommerce-bookings' ),
			)
		);

		// Bookable products list.
		$options = array( 'current' => __( '— Current Product (Single Product Page) —', 'styler-for-woocommerce-bookings' ) );
		if ( function_exists( 'wc_get_products' ) ) {
			$products = wc_get_products(
				array(
					'type'   => 'booking',
					'limit'  => 50,
					'status' => 'publish',
				)
			);
			foreach ( $products as $p ) {
				$options[ $p->get_id() ] = $p->get_name() . ' (#' . $p->get_id() . ')';
			}
		}

		$this->add_control(
			'product_id',
			array(
				'label'   => __( 'Select Bookable Product', 'styler-for-woocommerce-bookings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $options,
				'default' => 'current',
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Booking Layout', 'styler-for-woocommerce-bookings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'default'      => __( 'Global Plugin Default', 'styler-for-woocommerce-bookings' ),
					'split'        => __( 'Two-Column Split View', 'styler-for-woocommerce-bookings' ),
					'wizard'       => __( 'Multi-Step Wizard Flow', 'styler-for-woocommerce-bookings' ),
					'drawer'       => __( 'Slide-Over Drawer (Off-Canvas)', 'styler-for-woocommerce-bookings' ),
					'modal'        => __( 'Popup Lightbox Modal', 'styler-for-woocommerce-bookings' ),
					'bottom-sheet' => __( 'Mobile Bottom Sheet', 'styler-for-woocommerce-bookings' ),
					'standard'     => __( 'Modern Standard Layout', 'styler-for-woocommerce-bookings' ),
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'theme',
			array(
				'label'   => __( 'Design Theme', 'styler-for-woocommerce-bookings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'default' => __( 'Global Plugin Default', 'styler-for-woocommerce-bookings' ),
					'clean'   => __( 'Clean Minimalist', 'styler-for-woocommerce-bookings' ),
					'dark'    => __( 'Modern Dark', 'styler-for-woocommerce-bookings' ),
					'luxury'  => __( 'Luxury & Spa', 'styler-for-woocommerce-bookings' ),
					'vibrant' => __( 'Vibrant Modern', 'styler-for-woocommerce-bookings' ),
				),
				'default' => 'default',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$product_id = $settings['product_id'];

		if ( 'current' === $product_id || empty( $product_id ) ) {
			global $post;
			$product_id = $post ? $post->ID : 0;
		}

		$product_id = absint( $product_id );
		$product    = wc_get_product( $product_id );

		if ( ! $product || ! is_a( $product, 'WC_Product_Booking' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="elementor-alert elementor-alert-info">';
				esc_html_e( 'Please select a valid Bookable Product to preview the booking form.', 'styler-for-woocommerce-bookings' );
				echo '</div>';
			}
			return;
		}

		// Render via Shortcode engine.
		echo do_shortcode( sprintf( '[wc_booking_form product_id="%d" layout="%s" theme="%s"]', $product_id, esc_attr( $settings['layout'] ), esc_attr( $settings['theme'] ) ) );
	}
}
