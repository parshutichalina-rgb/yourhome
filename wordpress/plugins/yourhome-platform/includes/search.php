<?php
/**
 * Bounded public property search normalization and query construction.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const YOURHOME_PLATFORM_DEFAULT_RESULTS_PER_PAGE = 12;
const YOURHOME_PLATFORM_MAX_RESULTS_PER_PAGE = 100;

/**
 * Normalize public catalog input into a stable query contract.
 *
 * @param array<string, mixed> $input Untrusted request input.
 * @return array<string, int|string>
 */
function yourhome_platform_normalize_property_filters( array $input ): array {
	$allowed_intents = array( 'sale', 'rent' );
	$allowed_types   = array( 'apartment', 'house', 'townhouse', 'land', 'commercial' );
	$allowed_sorts   = array( 'newest', 'price_asc', 'price_desc' );
	$intent          = sanitize_key( (string) ( $input['intent'] ?? '' ) );
	$type            = sanitize_key( (string) ( $input['type'] ?? '' ) );
	$sort            = sanitize_key( (string) ( $input['sort'] ?? 'newest' ) );
	$min_price       = max( 0, absint( $input['min_price'] ?? 0 ) );
	$max_price       = max( 0, absint( $input['max_price'] ?? 0 ) );

	if ( 0 < $max_price && $min_price > $max_price ) {
		$temporary = $min_price;
		$min_price = $max_price;
		$max_price = $temporary;
	}

	return array(
		'intent'      => in_array( $intent, $allowed_intents, true ) ? $intent : '',
		'type'        => in_array( $type, $allowed_types, true ) ? $type : '',
		'city'        => sanitize_text_field( (string) ( $input['city'] ?? '' ) ),
		'min_price'   => $min_price,
		'max_price'   => $max_price,
		'bedrooms'    => min( 20, absint( $input['bedrooms'] ?? 0 ) ),
		'bathrooms'   => min( 20, absint( $input['bathrooms'] ?? 0 ) ),
		'sort'        => in_array( $sort, $allowed_sorts, true ) ? $sort : 'newest',
		'paged'       => max( 1, absint( $input['paged'] ?? 1 ) ),
		'per_page'    => min( YOURHOME_PLATFORM_MAX_RESULTS_PER_PAGE, max( 1, absint( $input['per_page'] ?? YOURHOME_PLATFORM_DEFAULT_RESULTS_PER_PAGE ) ) ),
	);
}

/**
 * Build a public WP_Query argument set from normalized filters.
 *
 * @param array<string, int|string> $filters Normalized filters.
 * @return array<string, mixed>
 */
function yourhome_platform_property_query_args( array $filters ): array {
	$args = array(
		'post_type'           => YOURHOME_PLATFORM_PROPERTY_POST_TYPE,
		'post_status'         => 'publish',
		'posts_per_page'      => (int) $filters['per_page'],
		'paged'               => (int) $filters['paged'],
		'ignore_sticky_posts' => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
	);

	$tax_query = array();
	if ( '' !== $filters['intent'] ) {
		$tax_query[] = array(
			'taxonomy' => YOURHOME_PLATFORM_INTENT_TAXONOMY,
			'field'    => 'slug',
			'terms'    => $filters['intent'],
		);
	}
	if ( '' !== $filters['type'] ) {
		$tax_query[] = array(
			'taxonomy' => YOURHOME_PLATFORM_TYPE_TAXONOMY,
			'field'    => 'slug',
			'terms'    => $filters['type'],
		);
	}
	if ( array() !== $tax_query ) {
		$args['tax_query'] = $tax_query;
	}

	$meta_query = array();
	if ( '' !== $filters['city'] ) {
		$meta_query[] = array(
			'key'     => 'yourhome_city',
			'value'   => $filters['city'],
			'compare' => 'LIKE',
		);
	}
	if ( 0 < $filters['min_price'] ) {
		$meta_query[] = array(
			'key'     => 'yourhome_price_minor',
			'value'   => (int) $filters['min_price'] * 100,
			'compare' => '>=',
			'type'    => 'NUMERIC',
		);
	}
	if ( 0 < $filters['max_price'] ) {
		$meta_query[] = array(
			'key'     => 'yourhome_price_minor',
			'value'   => (int) $filters['max_price'] * 100,
			'compare' => '<=',
			'type'    => 'NUMERIC',
		);
	}
	foreach ( array( 'bedrooms', 'bathrooms' ) as $room_key ) {
		if ( 0 < $filters[ $room_key ] ) {
			$meta_query[] = array(
				'key'     => 'yourhome_' . $room_key,
				'value'   => (int) $filters[ $room_key ],
				'compare' => '>=',
				'type'    => 'NUMERIC',
			);
		}
	}
	if ( array() !== $meta_query ) {
		$args['meta_query'] = $meta_query;
	}

	if ( 'price_asc' === $filters['sort'] || 'price_desc' === $filters['sort'] ) {
		$args['meta_key'] = 'yourhome_price_minor';
		$args['orderby']  = 'meta_value_num';
		$args['order']    = 'price_asc' === $filters['sort'] ? 'ASC' : 'DESC';
	}

	return $args;
}
