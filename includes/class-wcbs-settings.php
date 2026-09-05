<?php
/**
 * Settings and Option Management for Styler for WooCommerce Bookings.
 *
 * @package StylerForWooCommerceBookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCBS_Settings {

	const OPTION_KEY = 'wcbs_plugin_settings';

	/**
	 * Cached settings.
	 *
	 * @var array|null
	 */
	private $settings = null;

	/**
	 * Get all current settings merged with defaults.
	 *
	 * @return array
	 */
	public function get_all() {
		if ( null === $this->settings ) {
			$saved          = get_option( self::OPTION_KEY, array() );
			$defaults       = self::get_defaults();
			$this->settings = wp_parse_args( $saved, $defaults );
		}
		return $this->settings;
	}

	/**
	 * Get a specific setting value with fallback.
	 *
	 * @param string $key Setting key.
	 * @param mixed  $default Fallback value.
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		$all = $this->get_all();
		return isset( $all[ $key ] ) ? $all[ $key ] : $default;
	}

	/**
	 * Update all settings with sanitization.
	 *
	 * @param array $input Raw input settings.
	 * @return bool
	 */
	public function update( array $input ) {
		$sanitized      = $this->sanitize( $input );
		$updated        = update_option( self::OPTION_KEY, $sanitized );
		$this->settings = $sanitized;
		return $updated;
	}

	/**
	 * Return the default configuration array.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			// Layout & General.
			'default_layout'       => 'split',         // 'standard', 'split', 'wizard', 'drawer', 'modal', 'bottom-sheet'
			'active_theme'         => 'clean',         // 'clean', 'dark', 'luxury', 'vibrant', 'custom'
			'enable_staff_cards'   => 'yes',
			'enable_slot_tabs'     => 'yes',           // Morning / Afternoon / Evening
			'slot_style'           => 'chips',         // 'chips', 'grid', 'list'
			'enable_urgency_badge' => 'yes',
			'urgency_threshold'    => 2,               // Show badge if <= 2 spots left
			'enable_live_summary'  => 'yes',           // Sticky live price card
			'enable_timezone'      => 'yes',           // Detect & convert visitor timezone
			'enable_calendar_sync' => 'yes',           // Google, iCal, Outlook link generator
			'enable_skeleton'      => 'yes',           // Modern shimmer loader

			// Color Palette (Custom Theme).
			'primary_color'        => '#4f46e5',       // Modern Indigo
			'primary_hover'        => '#4338ca',
			'accent_color'         => '#06b6d4',       // Cyan
			'calendar_bg'          => '#ffffff',
			'calendar_header_bg'   => '#f8fafc',
			'calendar_header_text' => '#0f172a',
			'text_primary'         => '#1e293b',
			'text_secondary'       => '#64748b',
			'border_color'         => '#e2e8f0',

			// Calendar Day States.
			'day_shape'            => 'circle',        // 'circle', 'rounded', 'square', 'pill'
			'day_available_bg'     => '#f1f5f9',
			'day_available_text'   => '#1e293b',
			'day_selected_bg'      => '#4f46e5',
			'day_selected_text'    => '#ffffff',
			'day_booked_bg'        => '#fef2f2',
			'day_booked_text'      => '#94a3b8',
			'today_border_color'   => '#4f46e5',

			// Time Slot Styling.
			'slot_bg'              => '#ffffff',
			'slot_text'            => '#1e293b',
			'slot_border'          => '#cbd5e1',
			'slot_selected_bg'     => '#4f46e5',
			'slot_selected_text'   => '#ffffff',

			// Typography & Dimensions.
			'font_family'          => 'inherit',
			'font_size_base'       => 15,              // px
			'border_radius_base'   => 12,              // px
			'box_shadow_intensity' => 'medium',        // 'none', 'subtle', 'medium', 'deep'
			'button_padding_y'     => 14,              // px
			'button_padding_x'     => 24,              // px

			// Drawer / Modal triggers.
			'drawer_button_text'   => __( 'Book Appointment', 'styler-for-woocommerce-bookings' ),
			'drawer_position'      => 'right',         // 'right', 'left'
		);
	}

	/**
	 * Predefined Theme Presets.
	 *
	 * @return array
	 */
	public static function get_presets() {
		return array(
			'clean'   => array(
				'name'                 => __( 'Clean Minimalist (Default)', 'styler-for-woocommerce-bookings' ),
				'primary_color'        => '#4f46e5',
				'primary_hover'        => '#4338ca',
				'accent_color'         => '#06b6d4',
				'calendar_bg'          => '#ffffff',
				'calendar_header_bg'   => '#f8fafc',
				'calendar_header_text' => '#0f172a',
				'text_primary'         => '#1e293b',
				'text_secondary'       => '#64748b',
				'border_color'         => '#e2e8f0',
				'day_shape'            => 'circle',
				'day_available_bg'     => '#f8fafc',
				'day_available_text'   => '#1e293b',
				'day_selected_bg'      => '#4f46e5',
				'day_selected_text'    => '#ffffff',
				'day_booked_bg'        => '#f1f5f9',
				'day_booked_text'      => '#94a3b8',
				'today_border_color'   => '#4f46e5',
				'slot_bg'              => '#ffffff',
				'slot_text'            => '#1e293b',
				'slot_border'          => '#cbd5e1',
				'slot_selected_bg'     => '#4f46e5',
				'slot_selected_text'   => '#ffffff',
				'border_radius_base'   => 12,
			),
			'dark'    => array(
				'name'                 => __( 'Modern Dark', 'styler-for-woocommerce-bookings' ),
				'primary_color'        => '#6366f1',
				'primary_hover'        => '#4f46e5',
				'accent_color'         => '#38bdf8',
				'calendar_bg'          => '#0f172a',
				'calendar_header_bg'   => '#1e293b',
				'calendar_header_text' => '#f8fafc',
				'text_primary'         => '#f1f5f9',
				'text_secondary'       => '#94a3b8',
				'border_color'         => '#334155',
				'day_shape'            => 'rounded',
				'day_available_bg'     => '#1e293b',
				'day_available_text'   => '#f8fafc',
				'day_selected_bg'      => '#6366f1',
				'day_selected_text'    => '#ffffff',
				'day_booked_bg'        => '#0f172a',
				'day_booked_text'      => '#475569',
				'today_border_color'   => '#38bdf8',
				'slot_bg'              => '#1e293b',
				'slot_text'            => '#f1f5f9',
				'slot_border'          => '#334155',
				'slot_selected_bg'     => '#6366f1',
				'slot_selected_text'   => '#ffffff',
				'border_radius_base'   => 10,
			),
			'luxury'  => array(
				'name'                 => __( 'Luxury & Spa', 'styler-for-woocommerce-bookings' ),
				'primary_color'        => '#b45309', // Warm amber / bronze
				'primary_hover'        => '#92400e',
				'accent_color'         => '#d97706',
				'calendar_bg'          => '#fdfbf7',
				'calendar_header_bg'   => '#fef3c7',
				'calendar_header_text' => '#78350f',
				'text_primary'         => '#451a03',
				'text_secondary'       => '#78350f',
				'border_color'         => '#fde68a',
				'day_shape'            => 'circle',
				'day_available_bg'     => '#ffffff',
				'day_available_text'   => '#451a03',
				'day_selected_bg'      => '#b45309',
				'day_selected_text'    => '#ffffff',
				'day_booked_bg'        => '#fef2f2',
				'day_booked_text'      => '#b45309',
				'today_border_color'   => '#d97706',
				'slot_bg'              => '#ffffff',
				'slot_text'            => '#451a03',
				'slot_border'          => '#fde68a',
				'slot_selected_bg'     => '#b45309',
				'slot_selected_text'   => '#ffffff',
				'border_radius_base'   => 8,
			),
			'vibrant' => array(
				'name'                 => __( 'Vibrant Modern', 'styler-for-woocommerce-bookings' ),
				'primary_color'        => '#ec4899', // Bold magenta / rose
				'primary_hover'        => '#db2777',
				'accent_color'         => '#8b5cf6',
				'calendar_bg'          => '#ffffff',
				'calendar_header_bg'   => '#fdf2f8',
				'calendar_header_text' => '#831843',
				'text_primary'         => '#1f2937',
				'text_secondary'       => '#6b7280',
				'border_color'         => '#fce7f3',
				'day_shape'            => 'pill',
				'day_available_bg'     => '#fdf2f8',
				'day_available_text'   => '#831843',
				'day_selected_bg'      => '#ec4899',
				'day_selected_text'    => '#ffffff',
				'day_booked_bg'        => '#f3f4f6',
				'day_booked_text'      => '#9ca3af',
				'today_border_color'   => '#ec4899',
				'slot_bg'              => '#ffffff',
				'slot_text'            => '#1f2937',
				'slot_border'          => '#fbcfe8',
				'slot_selected_bg'     => '#ec4899',
				'slot_selected_text'   => '#ffffff',
				'border_radius_base'   => 16,
			),
		);
	}

	/**
	 * Build CSS custom properties (:root variables) string from current settings.
	 *
	 * @param array|null $custom_settings Optional specific settings array to render.
	 * @return string
	 */
	public function generate_css_variables( $custom_settings = null ) {
		$s = is_array( $custom_settings ) ? wp_parse_args( $custom_settings, $this->get_all() ) : $this->get_all();

		// Apply preset values if a preset is selected and not 'custom'.
		if ( ! empty( $s['active_theme'] ) && 'custom' !== $s['active_theme'] ) {
			$presets = self::get_presets();
			if ( isset( $presets[ $s['active_theme'] ] ) ) {
				$s = wp_parse_args( $presets[ $s['active_theme'] ], $s );
			}
		}

		$radius = absint( $s['border_radius_base'] );
		$font   = esc_attr( $s['font_family'] );
		if ( 'inherit' === $font || empty( $font ) ) {
			$font = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", sans-serif';
		}

		// Box shadow computation based on intensity.
		switch ( $s['box_shadow_intensity'] ) {
			case 'none':
				$shadow = 'none';
				break;
			case 'subtle':
				$shadow = '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03)';
				break;
			case 'deep':
				$shadow = '0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.08)';
				break;
			case 'medium':
			default:
				$shadow = '0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04)';
				break;
		}

		$day_radius = '50%';
		if ( 'rounded' === $s['day_shape'] ) {
			$day_radius = '8px';
		} elseif ( 'square' === $s['day_shape'] ) {
			$day_radius = '0px';
		} elseif ( 'pill' === $s['day_shape'] ) {
			$day_radius = '24px';
		}

		$css  = ':root, .wcbs-root-container {';
		$css .= '--wcbs-primary: ' . esc_attr( $s['primary_color'] ) . ';';
		$css .= '--wcbs-primary-hover: ' . esc_attr( $s['primary_hover'] ) . ';';
		$css .= '--wcbs-accent: ' . esc_attr( $s['accent_color'] ) . ';';
		$css .= '--wcbs-cal-bg: ' . esc_attr( $s['calendar_bg'] ) . ';';
		$css .= '--wcbs-cal-header-bg: ' . esc_attr( $s['calendar_header_bg'] ) . ';';
		$css .= '--wcbs-cal-header-text: ' . esc_attr( $s['calendar_header_text'] ) . ';';
		$css .= '--wcbs-text-main: ' . esc_attr( $s['text_primary'] ) . ';';
		$css .= '--wcbs-text-muted: ' . esc_attr( $s['text_secondary'] ) . ';';
		$css .= '--wcbs-border: ' . esc_attr( $s['border_color'] ) . ';';

		$css .= '--wcbs-day-radius: ' . esc_attr( $day_radius ) . ';';
		$css .= '--wcbs-day-avail-bg: ' . esc_attr( $s['day_available_bg'] ) . ';';
		$css .= '--wcbs-day-avail-text: ' . esc_attr( $s['day_available_text'] ) . ';';
		$css .= '--wcbs-day-sel-bg: ' . esc_attr( $s['day_selected_bg'] ) . ';';
		$css .= '--wcbs-day-sel-text: ' . esc_attr( $s['day_selected_text'] ) . ';';
		$css .= '--wcbs-day-booked-bg: ' . esc_attr( $s['day_booked_bg'] ) . ';';
		$css .= '--wcbs-day-booked-text: ' . esc_attr( $s['day_booked_text'] ) . ';';
		$css .= '--wcbs-today-border: ' . esc_attr( $s['today_border_color'] ) . ';';

		$css .= '--wcbs-slot-bg: ' . esc_attr( $s['slot_bg'] ) . ';';
		$css .= '--wcbs-slot-text: ' . esc_attr( $s['slot_text'] ) . ';';
		$css .= '--wcbs-slot-border: ' . esc_attr( $s['slot_border'] ) . ';';
		$css .= '--wcbs-slot-sel-bg: ' . esc_attr( $s['slot_selected_bg'] ) . ';';
		$css .= '--wcbs-slot-sel-text: ' . esc_attr( $s['slot_selected_text'] ) . ';';

		$css .= '--wcbs-radius: ' . $radius . 'px;';
		$css .= '--wcbs-font-family: ' . $font . ';';
		$css .= '--wcbs-font-size: ' . absint( $s['font_size_base'] ) . 'px;';
		$css .= '--wcbs-shadow: ' . $shadow . ';';
		$css .= '--wcbs-btn-pad-y: ' . absint( $s['button_padding_y'] ) . 'px;';
		$css .= '--wcbs-btn-pad-x: ' . absint( $s['button_padding_x'] ) . 'px;';
		$css .= '}';

		return $css;
	}

	/**
	 * Sanitize raw input array.
	 *
	 * @param array $input Raw input.
	 * @return array
	 */
	public function sanitize( array $input ) {
		$sanitized = array();
		$defaults  = self::get_defaults();

		foreach ( $defaults as $key => $default_val ) {
			if ( ! isset( $input[ $key ] ) ) {
				$sanitized[ $key ] = $default_val;
				continue;
			}

			$val = $input[ $key ];

			if ( is_numeric( $default_val ) ) {
				$sanitized[ $key ] = absint( $val );
			} elseif ( in_array( $key, array( 'primary_color', 'primary_hover', 'accent_color', 'calendar_bg', 'calendar_header_bg', 'calendar_header_text', 'text_primary', 'text_secondary', 'border_color', 'day_available_bg', 'day_available_text', 'day_selected_bg', 'day_selected_text', 'day_booked_bg', 'day_booked_text', 'today_border_color', 'slot_bg', 'slot_text', 'slot_border', 'slot_selected_bg', 'slot_selected_text' ), true ) ) {
				$sanitized[ $key ] = sanitize_hex_color( $val ) ?: $default_val;
			} else {
				$sanitized[ $key ] = sanitize_text_field( $val );
			}
		}

		return $sanitized;
	}
}
