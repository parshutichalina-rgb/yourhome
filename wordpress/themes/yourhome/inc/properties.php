<?php
/**
 * Property presentation helpers owned by the theme.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format a stored minor-unit property price.
 */
function yourhome_format_property_price( int $post_id ): string {
	$minor    = (int) get_post_meta( $post_id, 'yourhome_price_minor', true );
	$currency = (string) get_post_meta( $post_id, 'yourhome_currency', true );
	$currency = '' !== $currency ? $currency : 'EUR';

	$intents = wp_get_post_terms( $post_id, 'yourhome_intent', array( 'fields' => 'slugs' ) );
	$suffix  = is_array( $intents ) && in_array( 'rent', $intents, true ) ? __( ' / month', 'yourhome' ) : '';

	return number_format_i18n( $minor / 100, 0 ) . ' ' . $currency . $suffix;
}

/**
 * Render a single property card.
 */
function yourhome_render_property_card( int $post_id ): string {
	$city      = (string) get_post_meta( $post_id, 'yourhome_city', true );
	$area      = (float) get_post_meta( $post_id, 'yourhome_floor_area_sqm', true );
	$bedrooms  = (int) get_post_meta( $post_id, 'yourhome_bedrooms', true );
	$bathrooms = (int) get_post_meta( $post_id, 'yourhome_bathrooms', true );
	$type      = get_the_terms( $post_id, 'yourhome_property_type' );
	$intent    = get_the_terms( $post_id, 'yourhome_intent' );
	$type_name = is_array( $type ) && isset( $type[0] ) ? $type[0]->name : __( 'Property', 'yourhome' );
	$intent_name = is_array( $intent ) && isset( $intent[0] ) ? $intent[0]->name : '';
	$image     = get_the_post_thumbnail_url( $post_id, 'large' );
	$image     = false !== $image ? $image : get_theme_file_uri( '/assets/images/property-placeholder.svg' );

	ob_start();
	?>
	<article class="yourhome-property-card">
		<a class="yourhome-property-card__image-link" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'yourhome' ), get_the_title( $post_id ) ) ); ?>">
			<img class="yourhome-property-card__image" src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy" decoding="async">
			<?php if ( '' !== $intent_name ) : ?>
				<span class="yourhome-property-card__badge"><?php echo esc_html( $intent_name ); ?></span>
			<?php endif; ?>
		</a>
		<div class="yourhome-property-card__body">
			<p class="yourhome-property-card__type"><?php echo esc_html( $type_name ); ?></p>
			<h3 class="yourhome-property-card__title"><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
			<?php if ( '' !== $city ) : ?><p class="yourhome-property-card__location"><?php echo esc_html( $city ); ?></p><?php endif; ?>
			<ul class="yourhome-property-card__facts" aria-label="<?php esc_attr_e( 'Property facts', 'yourhome' ); ?>">
				<?php if ( 0 < $bedrooms ) : ?><li><?php echo esc_html( sprintf( _n( '%d bedroom', '%d bedrooms', $bedrooms, 'yourhome' ), $bedrooms ) ); ?></li><?php endif; ?>
				<?php if ( 0 < $bathrooms ) : ?><li><?php echo esc_html( sprintf( _n( '%d bathroom', '%d bathrooms', $bathrooms, 'yourhome' ), $bathrooms ) ); ?></li><?php endif; ?>
				<?php if ( 0 < $area ) : ?><li><?php echo esc_html( number_format_i18n( $area, 0 ) . ' m²' ); ?></li><?php endif; ?>
			</ul>
			<p class="yourhome-property-card__price"><?php echo esc_html( yourhome_format_property_price( $post_id ) ); ?></p>
		</div>
	</article>
	<?php
	return (string) ob_get_clean();
}
