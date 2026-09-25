<?php
/**
 * Property content model and registered metadata.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const YOURHOME_PLATFORM_PROPERTY_POST_TYPE = 'property';
const YOURHOME_PLATFORM_INTENT_TAXONOMY = 'yourhome_intent';
const YOURHOME_PLATFORM_TYPE_TAXONOMY = 'yourhome_property_type';

/**
 * Register the property post type, controlled taxonomies, and scalar metadata.
 */
function yourhome_platform_register_property_model(): void {
	register_post_type(
		YOURHOME_PLATFORM_PROPERTY_POST_TYPE,
		array(
			'labels'        => array(
				'name'                     => __( 'Properties', 'yourhome-platform' ),
				'singular_name'            => __( 'Property', 'yourhome-platform' ),
				'add_new_item'             => __( 'Add New Property', 'yourhome-platform' ),
				'edit_item'                => __( 'Edit Property', 'yourhome-platform' ),
				'new_item'                 => __( 'New Property', 'yourhome-platform' ),
				'view_item'                => __( 'View Property', 'yourhome-platform' ),
				'search_items'             => __( 'Search Properties', 'yourhome-platform' ),
				'not_found'                => __( 'No properties found.', 'yourhome-platform' ),
				'not_found_in_trash'       => __( 'No properties found in Trash.', 'yourhome-platform' ),
				'all_items'                => __( 'All Properties', 'yourhome-platform' ),
				'item_published'           => __( 'Property published.', 'yourhome-platform' ),
				'item_updated'             => __( 'Property updated.', 'yourhome-platform' ),
				'item_reverted_to_draft'   => __( 'Property reverted to draft.', 'yourhome-platform' ),
			),
			'public'        => true,
			'has_archive'   => 'properties',
			'rewrite'       => array( 'slug' => 'properties' ),
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-admin-home',
			'menu_position' => 20,
			'supports'      => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'revisions', 'custom-fields' ),
			'template'      => array(
				array( 'core/paragraph', array( 'placeholder' => __( 'Describe the property…', 'yourhome-platform' ) ) ),
			),
		)
	);

	yourhome_platform_register_property_taxonomies();
	yourhome_platform_register_property_meta();
}

/**
 * Register controlled property classification taxonomies.
 */
function yourhome_platform_register_property_taxonomies(): void {
	register_taxonomy(
		YOURHOME_PLATFORM_INTENT_TAXONOMY,
		YOURHOME_PLATFORM_PROPERTY_POST_TYPE,
		array(
			'labels'            => array(
				'name'          => __( 'Listing Intents', 'yourhome-platform' ),
				'singular_name' => __( 'Listing Intent', 'yourhome-platform' ),
			),
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'hierarchical'      => false,
			'rewrite'           => array( 'slug' => 'property-intent' ),
		)
	);

	register_taxonomy(
		YOURHOME_PLATFORM_TYPE_TAXONOMY,
		YOURHOME_PLATFORM_PROPERTY_POST_TYPE,
		array(
			'labels'            => array(
				'name'          => __( 'Property Types', 'yourhome-platform' ),
				'singular_name' => __( 'Property Type', 'yourhome-platform' ),
			),
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'hierarchical'      => false,
			'rewrite'           => array( 'slug' => 'property-type' ),
		)
	);
}

/**
 * Register the approved scalar property fields.
 */
function yourhome_platform_register_property_meta(): void {
	$fields = array(
		'yourhome_price_minor'    => array( 'type' => 'integer', 'sanitize_callback' => 'absint' ),
		'yourhome_currency'       => array( 'type' => 'string', 'sanitize_callback' => 'yourhome_platform_sanitize_uppercase_code' ),
		'yourhome_country'        => array( 'type' => 'string', 'sanitize_callback' => 'yourhome_platform_sanitize_uppercase_code' ),
		'yourhome_city'           => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
		'yourhome_latitude'       => array( 'type' => 'number', 'sanitize_callback' => 'floatval' ),
		'yourhome_longitude'      => array( 'type' => 'number', 'sanitize_callback' => 'floatval' ),
		'yourhome_floor_area_sqm' => array( 'type' => 'number', 'sanitize_callback' => 'floatval' ),
		'yourhome_bedrooms'       => array( 'type' => 'integer', 'sanitize_callback' => 'absint' ),
		'yourhome_bathrooms'      => array( 'type' => 'integer', 'sanitize_callback' => 'absint' ),
		'yourhome_lot_area_sqm'   => array( 'type' => 'number', 'sanitize_callback' => 'floatval' ),
		'yourhome_street'         => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
		'yourhome_postcode'       => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
		'yourhome_floor'          => array( 'type' => 'integer', 'sanitize_callback' => 'intval' ),
		'yourhome_year_built'     => array( 'type' => 'integer', 'sanitize_callback' => 'absint' ),
		'yourhome_availability'   => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_key' ),
	);

	foreach ( $fields as $meta_key => $field ) {
		register_post_meta(
			YOURHOME_PLATFORM_PROPERTY_POST_TYPE,
			$meta_key,
			array(
				'type'              => $field['type'],
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => $field['sanitize_callback'],
				'auth_callback'     => 'yourhome_platform_can_edit_property_meta',
			)
		);
	}
}

/**
 * Restrict property metadata writes to users who can edit the owning property.
 */
function yourhome_platform_can_edit_property_meta( bool $allowed, string $meta_key, int $object_id ): bool {
	unset( $allowed, $meta_key );

	return current_user_can( 'edit_post', $object_id );
}

/**
 * Normalize short country and currency codes without applying validation policy.
 *
 * @param mixed $value Untrusted metadata value.
 */
function yourhome_platform_sanitize_uppercase_code( mixed $value ): string {
	return strtoupper( sanitize_text_field( (string) $value ) );
}

/**
 * Seed the approved controlled vocabulary.
 */
function yourhome_platform_seed_property_terms(): void {
	$terms = array(
		YOURHOME_PLATFORM_INTENT_TAXONOMY => array(
			'sale' => __( 'For Sale', 'yourhome-platform' ),
			'rent' => __( 'For Rent', 'yourhome-platform' ),
		),
		YOURHOME_PLATFORM_TYPE_TAXONOMY   => array(
			'apartment'  => __( 'Apartment', 'yourhome-platform' ),
			'house'      => __( 'House', 'yourhome-platform' ),
			'townhouse'  => __( 'Townhouse', 'yourhome-platform' ),
			'land'       => __( 'Land', 'yourhome-platform' ),
			'commercial' => __( 'Commercial', 'yourhome-platform' ),
		),
	);

	foreach ( $terms as $taxonomy => $taxonomy_terms ) {
		foreach ( $taxonomy_terms as $slug => $name ) {
			if ( ! term_exists( $slug, $taxonomy ) ) {
				wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
			}
		}
	}
}
