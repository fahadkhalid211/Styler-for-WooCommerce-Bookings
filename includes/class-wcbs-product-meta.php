<?php
/**
 * Per-Product Meta Box Controller for Styler for WooCommerce Bookings.
 *
 * @package StylerForWooCommerceBookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCBS_Product_Meta {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_product_meta_box' ) );
		add_action( 'save_post_product', array( $this, 'save_product_meta' ) );
	}

	/**
	 * Register meta box on WooCommerce product edit screen.
	 */
	public function add_product_meta_box() {
		add_meta_box(
			'wcbs_booking_styler_meta',
			__( 'Booking Styler Options', 'styler-for-woo' ),
			array( $this, 'render_meta_box' ),
			'product',
			'side',
			'default'
		);
	}

	/**
	 * Render the meta box HTML.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'wcbs_save_product_meta', 'wcbs_product_meta_nonce' );

		$current_layout = get_post_meta( $post->ID, '_wcbs_product_layout', true ) ?: 'default';
		$current_theme  = get_post_meta( $post->ID, '_wcbs_product_theme', true ) ?: 'default';
		$btn_text       = get_post_meta( $post->ID, '_wcbs_drawer_btn_text', true ) ?: '';

		?>
		<div class="wcbs-meta-box-wrapper" style="padding-top: 6px;">
			<p>
				<label for="wcbs_product_layout"><strong><?php esc_html_e( 'Layout Override:', 'styler-for-woo' ); ?></strong></label><br>
				<select name="wcbs_product_layout" id="wcbs_product_layout" style="width: 100%; margin-top: 4px;">
					<option value="default" <?php selected( $current_layout, 'default' ); ?>><?php esc_html_e( '— Use Global Default —', 'styler-for-woo' ); ?></option>
					<option value="split" <?php selected( $current_layout, 'split' ); ?>><?php esc_html_e( 'Two-Column Split View', 'styler-for-woo' ); ?></option>
					<option value="wizard" <?php selected( $current_layout, 'wizard' ); ?>><?php esc_html_e( 'Multi-Step Wizard Flow', 'styler-for-woo' ); ?></option>
					<option value="drawer" <?php selected( $current_layout, 'drawer' ); ?>><?php esc_html_e( 'Slide-Over Drawer (Off-Canvas)', 'styler-for-woo' ); ?></option>
					<option value="modal" <?php selected( $current_layout, 'modal' ); ?>><?php esc_html_e( 'Popup Lightbox Modal', 'styler-for-woo' ); ?></option>
					<option value="bottom-sheet" <?php selected( $current_layout, 'bottom-sheet' ); ?>><?php esc_html_e( 'Mobile Bottom Sheet', 'styler-for-woo' ); ?></option>
					<option value="standard" <?php selected( $current_layout, 'standard' ); ?>><?php esc_html_e( 'Modern Standard Layout', 'styler-for-woo' ); ?></option>
				</select>
			</p>

			<p>
				<label for="wcbs_product_theme"><strong><?php esc_html_e( 'Theme Override:', 'styler-for-woo' ); ?></strong></label><br>
				<select name="wcbs_product_theme" id="wcbs_product_theme" style="width: 100%; margin-top: 4px;">
					<option value="default" <?php selected( $current_theme, 'default' ); ?>><?php esc_html_e( '— Use Global Default —', 'styler-for-woo' ); ?></option>
					<option value="clean" <?php selected( $current_theme, 'clean' ); ?>><?php esc_html_e( 'Clean Minimalist', 'styler-for-woo' ); ?></option>
					<option value="dark" <?php selected( $current_theme, 'dark' ); ?>><?php esc_html_e( 'Modern Dark', 'styler-for-woo' ); ?></option>
					<option value="luxury" <?php selected( $current_theme, 'luxury' ); ?>><?php esc_html_e( 'Luxury & Spa', 'styler-for-woo' ); ?></option>
					<option value="vibrant" <?php selected( $current_theme, 'vibrant' ); ?>><?php esc_html_e( 'Vibrant Modern', 'styler-for-woo' ); ?></option>
				</select>
			</p>

			<p>
				<label for="wcbs_drawer_btn_text"><strong><?php esc_html_e( 'Drawer/Modal Button Text:', 'styler-for-woo' ); ?></strong></label><br>
				<input type="text" name="wcbs_drawer_btn_text" id="wcbs_drawer_btn_text" value="<?php echo esc_attr( $btn_text ); ?>" placeholder="<?php esc_attr_e( 'e.g. Book Consultation', 'styler-for-woo' ); ?>" style="width: 100%; margin-top: 4px;">
				<small style="color: #64748b;"><?php esc_html_e( 'Used when Drawer or Modal layout is active.', 'styler-for-woo' ); ?></small>
			</p>
		</div>
		<?php
	}

	/**
	 * Save product meta values.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_product_meta( $post_id ) {
		if ( ! isset( $_POST['wcbs_product_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['wcbs_product_meta_nonce'] ), 'wcbs_save_product_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['wcbs_product_layout'] ) ) {
			$allowed_layouts = array( 'default', 'split', 'wizard', 'drawer', 'modal', 'bottom-sheet', 'standard' );
			$layout          = sanitize_text_field( wp_unslash( $_POST['wcbs_product_layout'] ) );
			if ( in_array( $layout, $allowed_layouts, true ) ) {
				update_post_meta( $post_id, '_wcbs_product_layout', $layout );
			}
		}

		if ( isset( $_POST['wcbs_product_theme'] ) ) {
			$allowed_themes = array( 'default', 'clean', 'dark', 'luxury', 'vibrant' );
			$theme          = sanitize_text_field( wp_unslash( $_POST['wcbs_product_theme'] ) );
			if ( in_array( $theme, $allowed_themes, true ) ) {
				update_post_meta( $post_id, '_wcbs_product_theme', $theme );
			}
		}

		if ( isset( $_POST['wcbs_drawer_btn_text'] ) ) {
			update_post_meta( $post_id, '_wcbs_drawer_btn_text', sanitize_text_field( wp_unslash( $_POST['wcbs_drawer_btn_text'] ) ) );
		}
	}
}
