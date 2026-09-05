<?php
/**
 * Resource / Staff Member Visual Cards Part.
 *
 * @package StylerForWooCommerceBookings
 *
 * Available variables:
 * @var WC_Product_Booking $product Bookable product.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $product || ! method_exists( $product, 'has_resources' ) || ! $product->has_resources() ) {
	return;
}

$resources = $product->get_resources();
if ( empty( $resources ) ) {
	return;
}
?>
<div class="wcbs-resource-cards-section">
	<label class="wcbs-section-label">
		<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
			<circle cx="12" cy="7" r="4"></circle>
		</svg>
		<span><?php esc_html_e( 'Choose Professional / Resource:', 'styler-for-woo' ); ?></span>
	</label>

	<div class="wcbs-resource-grid" role="radiogroup">
		<?php
		$first = true;
		foreach ( $resources as $resource ) :
			$res_id    = $resource->get_id();
			$res_name  = $resource->get_name();
			$thumb_id  = get_post_thumbnail_id( $res_id );
			$thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : false;
			?>
			<div class="wcbs-resource-card <?php echo $first ? 'active' : ''; ?>"
			     data-resource-id="<?php echo esc_attr( $res_id ); ?>"
			     role="radio"
			     aria-checked="<?php echo $first ? 'true' : 'false'; ?>"
			     tabindex="0">
				<div class="wcbs-resource-avatar">
					<?php if ( $thumb_url ) : ?>
						<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $res_name ); ?>" />
					<?php else : ?>
						<div class="wcbs-avatar-initials">
							<?php
							$words    = explode( ' ', $res_name );
							$initials = '';
							foreach ( array_slice( $words, 0, 2 ) as $w ) {
								$initials .= strtoupper( substr( $w, 0, 1 ) );
							}
							echo esc_html( $initials ?: 'P' );
							?>
						</div>
					<?php endif; ?>
					<div class="wcbs-resource-check">
						<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
					</div>
				</div>
				<div class="wcbs-resource-info">
					<div class="wcbs-resource-name"><?php echo esc_html( $res_name ); ?></div>
					<div class="wcbs-resource-badge"><?php esc_html_e( 'Available', 'styler-for-woo' ); ?></div>
				</div>
			</div>
			<?php
			$first = false;
		endforeach;
		?>
	</div>
</div>
