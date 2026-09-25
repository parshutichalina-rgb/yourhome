<?php
/**
 * Header and Footer page selection in the native WordPress Customizer.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register one compact Customizer section for site chrome.
 */
function yourhome_register_customizer( WP_Customize_Manager $manager ): void {
	$manager->add_section(
		'yourhome_header_footer',
		array(
			'title'       => __( 'Header and Footer', 'yourhome' ),
			'description' => __( 'Choose any existing page. Only each page’s Gutenberg content is displayed.', 'yourhome' ),
			'priority'    => 35,
		)
	);

	foreach ( array( 'header', 'footer' ) as $slot ) {
		$label = 'header' === $slot ? __( 'Header page', 'yourhome' ) : __( 'Footer page', 'yourhome' );

		$manager->add_setting(
			'yourhome_' . $slot . '_page_id',
			array(
				'default'           => 0,
				'sanitize_callback' => 'yourhome_sanitize_layout_page_id',
			)
		);

		$manager->add_control(
			'yourhome_' . $slot . '_page_id',
			array(
				'label'          => $label,
				'description'    => __( 'Draft, private, pending, and scheduled content becomes public when used here. Leave empty for the theme default.', 'yourhome' ),
				'section'        => 'yourhome_header_footer',
				'type'           => 'select',
				'choices'        => yourhome_get_layout_page_choices(),
			)
		);
	}
}

/**
 * Return all usable pages with their status visible in the label.
 *
 * @return array<int, string>
 */
function yourhome_get_layout_page_choices(): array {
	$choices = array( 0 => __( 'Use theme default', 'yourhome' ) );
	$pages   = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => yourhome_get_layout_page_statuses(),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	foreach ( $pages as $page ) {
		$title         = '' !== trim( $page->post_title ) ? $page->post_title : __( '(no title)', 'yourhome' );
		$status_object = get_post_status_object( $page->post_status );
		$status_label  = $status_object instanceof stdClass ? $status_object->label : $page->post_status;
		$choices[ $page->ID ] = 'publish' === $page->post_status
			? $title
			: sprintf( '%1$s — %2$s', $title, $status_label );
	}

	return $choices;
}
