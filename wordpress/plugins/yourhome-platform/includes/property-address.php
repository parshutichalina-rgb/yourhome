<?php
/** Mandatory property address editing and publication rules. */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return required address fields for presentation and validation. */
function yourhome_platform_required_address_fields(): array {
	return array(
		'yourhome_street' => __( 'Street address', 'yourhome-platform' ),
		'yourhome_city' => __( 'City', 'yourhome-platform' ),
		'yourhome_country' => __( 'Country code', 'yourhome-platform' ),
	);
}

/** Merge supplied address metadata with the stored values. */
function yourhome_platform_has_property_address( int $post_id, array $meta ): bool {
	foreach ( yourhome_platform_required_address_fields() as $key => $label ) {
		$value = array_key_exists( $key, $meta ) ? $meta[ $key ] : get_post_meta( $post_id, $key, true );
		if ( ! is_scalar( $value ) || '' === trim( sanitize_text_field( (string) $value ) ) ) {
			return false;
		}
	}
	return true;
}

/** Reject publication or removal of a published property's required address. */
function yourhome_platform_validate_rest_property_address( stdClass $prepared_post, WP_REST_Request $request ): stdClass|WP_Error {
	$post_id = absint( $request['id'] ?? 0 );
	$status = $prepared_post->post_status ?? get_post_status( $post_id );
	$meta = $request->get_param( 'meta' );
	$meta = is_array( $meta ) ? $meta : array();
	if ( in_array( $status, array( 'publish', 'future', 'private' ), true ) && ! yourhome_platform_has_property_address( $post_id, $meta ) ) {
		return new WP_Error( 'yourhome_address_required', __( 'Add a street address, city and country code before publishing this property.', 'yourhome-platform' ), array( 'status' => 400 ) );
	}
	return $prepared_post;
}
add_filter( 'rest_pre_insert_property', 'yourhome_platform_validate_rest_property_address', 10, 2 );

/** Keep incomplete non-REST inserts as drafts, including imports and classic saves. */
function yourhome_platform_require_property_address( array $data, array $postarr ): array {
	if ( 'property' !== $data['post_type'] || ! in_array( $data['post_status'], array( 'publish', 'future', 'private' ), true ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return $data;
	}
	$meta = isset( $postarr['meta_input'] ) && is_array( $postarr['meta_input'] ) ? wp_unslash( $postarr['meta_input'] ) : array();
	if ( ! yourhome_platform_has_property_address( absint( $postarr['ID'] ?? 0 ), $meta ) ) {
		$data['post_status'] = 'draft';
	}
	return $data;
}
add_filter( 'wp_insert_post_data', 'yourhome_platform_require_property_address', 10, 2 );

/** Load the Gutenberg address panel only for property editing. */
function yourhome_platform_enqueue_property_address_editor(): void {
	$screen = get_current_screen();
	if ( ! $screen || 'property' !== $screen->post_type ) {
		return;
	}
	$relative_path = 'assets/js/property-address.js';
	wp_enqueue_script( 'yourhome-property-address', plugins_url( $relative_path, YOURHOME_PLATFORM_FILE ), array( 'wp-plugins', 'wp-editor', 'wp-element', 'wp-components', 'wp-data', 'wp-i18n' ), (string) filemtime( YOURHOME_PLATFORM_DIRECTORY . '/' . $relative_path ), true );
}
add_action( 'enqueue_block_editor_assets', 'yourhome_platform_enqueue_property_address_editor' );
