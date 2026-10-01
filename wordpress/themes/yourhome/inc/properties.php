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

/**
 * Prepare public detail values, preserving legitimate zeroes.
 *
 * @return array<string, mixed> Formatted property details.
 */
function yourhome_get_property_detail_data( int $post_id ): array {
	$get_value = static function ( string $key ) use ( $post_id ): string {
		$value = get_post_meta( $post_id, 'yourhome_' . $key, true );
		return is_scalar( $value ) ? trim( (string) $value ) : '';
	};
	$get_terms = static function ( string $taxonomy ) use ( $post_id ): string {
		$terms = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'names' ) );
		return is_array( $terms ) ? implode( ', ', $terms ) : '';
	};
	$type = $get_terms( 'yourhome_property_type' );
	$intent = $get_terms( 'yourhome_intent' );
	$currency = $get_value( 'currency' );
	$minor = $get_value( 'price_minor' );
	$price = '';
	if ( '' !== $minor && is_numeric( $minor ) && 0 <= (int) $minor ) {
		$price = number_format_i18n( (int) $minor / 100, 0 === (int) $minor % 100 ? 0 : 2 );
		$price .= '' !== $currency ? ' ' . $currency : '';
		$intents = wp_get_post_terms( $post_id, 'yourhome_intent', array( 'fields' => 'slugs' ) );
		if ( is_array( $intents ) && in_array( 'rent', $intents, true ) ) {
			$price .= __( ' / month', 'yourhome' );
		}
	}
	$facts = array(
		'type' => array( 'label' => __( 'Property type', 'yourhome' ), 'value' => $type ),
		'intent' => array( 'label' => __( 'Listing intent', 'yourhome' ), 'value' => $intent ),
		'price' => array( 'label' => __( 'Price', 'yourhome' ), 'value' => $price ),
		'currency' => array( 'label' => __( 'Currency', 'yourhome' ), 'value' => $currency ),
	);
	foreach ( array(
		'floor_area_sqm' => __( 'Floor area', 'yourhome' ),
		'lot_area_sqm' => __( 'Lot area', 'yourhome' ),
		'bedrooms' => __( 'Bedrooms', 'yourhome' ),
		'bathrooms' => __( 'Bathrooms', 'yourhome' ),
		'floor' => __( 'Floor', 'yourhome' ),
		'year_built' => __( 'Year built', 'yourhome' ),
		'availability' => __( 'Availability', 'yourhome' ),
	) as $key => $label ) {
		$value = $get_value( $key );
		if ( 'availability' === $key ) {
			$value = ucwords( str_replace( array( '-', '_' ), ' ', $value ) );
		} elseif ( '' !== $value && is_numeric( $value ) ) {
			if ( in_array( $key, array( 'floor_area_sqm', 'lot_area_sqm' ), true ) ) {
				$area = (float) $value;
				$value = 0 < $area ? number_format_i18n( $area, floor( $area ) === $area ? 0 : 2 ) . ' m²' : '';
			} elseif ( 'floor' === $key && 0 === (int) $value ) {
				$value = __( 'Ground floor', 'yourhome' );
			} elseif ( 'year_built' === $key ) {
				$value = 0 < (int) $value ? (string) (int) $value : '';
			} else {
				$value = number_format_i18n( (int) $value );
			}
		} else {
			$value = '';
		}
		$facts[ $key ] = array( 'label' => $label, 'value' => $value );
	}
	$location = array();
	foreach ( array(
		'street' => __( 'Street / district', 'yourhome' ),
		'city' => __( 'City', 'yourhome' ),
		'postcode' => __( 'Postcode', 'yourhome' ),
		'country' => __( 'Country code', 'yourhome' ),
		'latitude' => __( 'Latitude', 'yourhome' ),
		'longitude' => __( 'Longitude', 'yourhome' ),
	) as $key => $label ) {
		$location[ $key ] = array( 'label' => $label, 'value' => $get_value( $key ) );
	}
	$address = implode( ', ', array_filter( array(
		$location['street']['value'],
		trim( $location['postcode']['value'] . ' ' . $location['city']['value'] ),
		$location['country']['value'],
	) ) );
	return compact( 'type', 'intent', 'price', 'facts', 'location', 'address' );
}

/**
 * Build map URLs from validated stored coordinates and the configured provider.
 *
 * @return array<string, string> Empty if coordinates or provider are unavailable.
 */
function yourhome_get_property_map( int $post_id ): array {
	$latitude = get_post_meta( $post_id, 'yourhome_latitude', true );
	$longitude = get_post_meta( $post_id, 'yourhome_longitude', true );
	$embed_base = yourhome_sanitize_map_url( (string) get_theme_mod( 'yourhome_map_embed_url', '' ) );
	$view_base = yourhome_sanitize_map_url( (string) get_theme_mod( 'yourhome_map_view_url', '' ) );
	// Slippy maps cannot display the polar regions beyond Web Mercator's limit.
	$mercator_limit = 85.05112878;
	if ( ! is_numeric( $latitude ) || ! is_numeric( $longitude ) || abs( (float) $latitude ) >= $mercator_limit || abs( (float) $longitude ) > 180 || '' === $embed_base ) {
		return array();
	}
	$lat = (float) $latitude;
	$lon = (float) $longitude;
	// Show the surrounding neighbourhood, approximately one kilometre each way.
	$neighbourhood_span = 0.01;
	$bbox = implode( ',', array( max( -180, $lon - $neighbourhood_span ), max( -$mercator_limit, $lat - $neighbourhood_span ), min( 180, $lon + $neighbourhood_span ), min( $mercator_limit, $lat + $neighbourhood_span ) ) );
	return array(
		'embed' => add_query_arg( array( 'bbox' => $bbox, 'layer' => 'mapnik', 'marker' => $lat . ',' . $lon ), $embed_base ),
		'view' => '' !== $view_base ? add_query_arg( array( 'mlat' => $lat, 'mlon' => $lon ), $view_base ) . '#map=16/' . $lat . '/' . $lon : '',
	);
}

/**
 * Collect listing photos from the featured image and editable image/gallery blocks.
 *
 * @return int[] Existing image attachment IDs in editorial order.
 */
function yourhome_get_property_image_ids( int $post_id ): array {
	$image_ids = array( get_post_thumbnail_id( $post_id ) );
	$collect = static function ( array $blocks ) use ( &$collect, &$image_ids ): void {
		foreach ( $blocks as $block ) {
			if ( 'core/image' === $block['blockName'] && ! empty( $block['attrs']['id'] ) ) {
				$image_ids[] = absint( $block['attrs']['id'] );
			}
			if ( 'core/gallery' === $block['blockName'] && ! empty( $block['attrs']['ids'] ) && is_array( $block['attrs']['ids'] ) ) {
				$image_ids = array_merge( $image_ids, array_map( 'absint', $block['attrs']['ids'] ) );
			}
			if ( ! empty( $block['innerBlocks'] ) ) {
				$collect( $block['innerBlocks'] );
			}
		}
	};
	$collect( parse_blocks( (string) get_post_field( 'post_content', $post_id ) ) );
	return array_values( array_filter( array_unique( $image_ids ), 'wp_attachment_is_image' ) );
}
