<?php
/**
 * Plugin Name: Styler for WooCommerce Bookings
 * Plugin URI:  https://github.com/fahadkhalid211/Styler-for-WooCommerce-Bookings
 * Description: Transform WooCommerce Bookings with modern layout flows (Wizard, Split-View, Drawer, Modal, Bottom-Sheet), live visual customizer, designer themes, time slot grouping, staff cards, and live price breakdown.
 * Version:     1.0.8
 * Author:      Fahad Khalid
 * Author URI:  https://github.com/fahadkhalid211
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: styler-for-woo
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * WC requires at least: 6.0
 * WC tested up to:      9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Plugin Constants.
define( 'WCBS_VERSION', '1.0.8' );
define( 'WCBS_FILE', __FILE__ );
define( 'WCBS_BASE_NAME', plugin_basename( __FILE__ ) );
define( 'WCBS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCBS_URL', plugin_dir_url( __FILE__ ) );
define( 'WCBS_ASSETS_URL', WCBS_URL . 'assets/' );
define( 'WCBS_TEMPLATES_PATH', WCBS_PATH . 'templates/' );

/**
 * Check dependencies and bootstrap plugin.
 */
function wcbs_init_plugin() {
	// 1. Check WooCommerce.
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wcbs_missing_woocommerce_notice' );
		return;
	}

	// 2. Check WooCommerce Bookings.
	if ( ! class_exists( 'WC_Bookings' ) ) {
		add_action( 'admin_notices', 'wcbs_missing_bookings_notice' );
		return;
	}

	// 3. Load Plugin Core.
	require_once WCBS_PATH . 'includes/class-wcbs-core.php';
	WCBS_Core::instance();
}
add_action( 'plugins_loaded', 'wcbs_init_plugin', 20 );

/**
 * Notice: Missing WooCommerce.
 */
function wcbs_missing_woocommerce_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	?>
	<div class="notice notice-error is-dismissible">
		<p>
			<strong><?php esc_html_e( 'Styler for WooCommerce Bookings', 'styler-for-woo' ); ?></strong>
			<?php esc_html_e( 'requires WooCommerce to be installed and active.', 'styler-for-woo' ); ?>
		</p>
	</div>
	<?php
}

/**
 * Notice: Missing WooCommerce Bookings.
 */
function wcbs_missing_bookings_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	?>
	<div class="notice notice-warning is-dismissible">
		<p>
			<strong><?php esc_html_e( 'Styler for WooCommerce Bookings', 'styler-for-woo' ); ?></strong>
			<?php
			printf(
				/* translators: %s: WooCommerce Bookings official link */
				esc_html__( 'requires %s to be installed and active to power booking logic and availability calculations.', 'styler-for-woo' ),
				'<a href="https://woocommerce.com/products/woocommerce-bookings/" target="_blank" rel="noopener noreferrer">WooCommerce Bookings</a>'
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Plugin activation hook.
 */
register_activation_hook( __FILE__, 'wcbs_activate_plugin' );
function wcbs_activate_plugin() {
	// Set default settings if not already present.
	require_once WCBS_PATH . 'includes/class-wcbs-settings.php';
	if ( false === get_option( WCBS_Settings::OPTION_KEY, false ) ) {
		update_option( WCBS_Settings::OPTION_KEY, WCBS_Settings::get_defaults() );
	}
}

/**
 * Plugin deactivation hook.
 */
register_deactivation_hook( __FILE__, 'wcbs_deactivate_plugin' );
function wcbs_deactivate_plugin() {
	// Cleanup temporary transients if needed.
	delete_transient( 'wcbs_flush_rewrite_rules' );
}
