<?php
/**
 * Existing-page selection and rendering for the global Header and Footer.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return page statuses that can supply site chrome.
 *
 * @return array<int, string>
 */
function yourhome_get_layout_page_statuses(): array {
	return array( 'publish', 'draft', 'private', 'pending', 'future' );
}

/**
 * Validate that a selected ID belongs to a usable page.
 */
function yourhome_is_valid_layout_page( int $page_id ): bool {
	$page = get_post( $page_id );

	return $page instanceof WP_Post
		&& 'page' === $page->post_type
		&& in_array( $page->post_status, yourhome_get_layout_page_statuses(), true );
}

/**
 * Sanitize an optional Header or Footer page ID.
 */
function yourhome_sanitize_layout_page_id( mixed $value ): int {
	$page_id = absint( $value );

	return 0 === $page_id || yourhome_is_valid_layout_page( $page_id ) ? $page_id : 0;
}

/**
 * Render a selected page's Gutenberg content or the theme fallback.
 */
function yourhome_render_layout_slot( string $slot ): string {
	if ( ! in_array( $slot, array( 'header', 'footer' ), true ) ) {
		return '';
	}

	$page_id = absint( get_theme_mod( 'yourhome_' . $slot . '_page_id', 0 ) );

	if ( 0 < $page_id && yourhome_is_valid_layout_page( $page_id ) ) {
		$page = get_post( $page_id );

		return $page instanceof WP_Post ? do_blocks( $page->post_content ) : '';
	}

	if ( 'header' === $slot ) {
		return do_blocks( '<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} --><div class="wp-block-group"><!-- wp:site-title {"level":0} /--><!-- wp:navigation {"overlayMenu":"mobile"} /--></div><!-- /wp:group -->' );
	}

	return do_blocks( '<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} --><div class="wp-block-group"><!-- wp:site-title {"level":0,"isLink":true} /--><!-- wp:paragraph --><p>Built with WordPress and Gutenberg.</p><!-- /wp:paragraph --></div><!-- /wp:group -->' );
}

/**
 * Check whether a selected page uses the dedicated block for its slot.
 */
function yourhome_layout_slot_uses_custom_block( string $slot ): bool {
	if ( ! in_array( $slot, array( 'header', 'footer' ), true ) ) {
		return false;
	}

	$page_id = absint( get_theme_mod( 'yourhome_' . $slot . '_page_id', 0 ) );
	$page    = get_post( $page_id );

	return $page instanceof WP_Post
		&& yourhome_is_valid_layout_page( $page_id )
		&& has_block( 'yourhome/' . $slot, $page );
}
