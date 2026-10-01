<?php
/**
 * Contact message persistence, submission handling, and admin visibility.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const YOURHOME_PLATFORM_CONTACT_MESSAGE_POST_TYPE = 'yourhome_contact';
const YOURHOME_PLATFORM_CONTACT_ACTION = 'yourhome_submit_contact_message';
const YOURHOME_PLATFORM_CONTACT_NONCE_ACTION = 'yourhome_submit_contact_message';
const YOURHOME_PLATFORM_CONTACT_NONCE_NAME = 'yourhome_contact_nonce';

/**
 * Register private contact messages for administrator review.
 */
function yourhome_platform_register_contact_message_model(): void {
	$admin_capability = 'manage_options';

	register_post_type(
		YOURHOME_PLATFORM_CONTACT_MESSAGE_POST_TYPE,
		array(
			'labels'        => array(
				'name'          => __( 'Contact messages', 'yourhome-platform' ),
				'singular_name' => __( 'Contact message', 'yourhome-platform' ),
				'all_items'     => __( 'All messages', 'yourhome-platform' ),
				'edit_item'     => __( 'Review message', 'yourhome-platform' ),
				'not_found'     => __( 'No contact messages found.', 'yourhome-platform' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => false,
			'menu_icon'     => 'dashicons-email-alt',
			'menu_position' => 22,
			'supports'      => array( 'title' ),
			'capabilities'  => array(
				'edit_post'              => $admin_capability,
				'read_post'              => $admin_capability,
				'delete_post'            => $admin_capability,
				'edit_posts'             => $admin_capability,
				'edit_others_posts'      => $admin_capability,
				'publish_posts'          => $admin_capability,
				'read_private_posts'     => $admin_capability,
				'delete_posts'           => $admin_capability,
				'delete_private_posts'   => $admin_capability,
				'delete_published_posts' => $admin_capability,
				'delete_others_posts'    => $admin_capability,
				'edit_private_posts'     => $admin_capability,
				'edit_published_posts'   => $admin_capability,
				'create_posts'           => 'do_not_allow',
			),
			'map_meta_cap'  => false,
		)
	);
}

/**
 * Redirect back to the public form with a bounded status.
 */
function yourhome_platform_redirect_contact_message( string $status ): never {
	$allowed_statuses = array( 'success', 'invalid', 'rate_limited', 'error' );
	$status           = in_array( $status, $allowed_statuses, true ) ? $status : 'error';
	$raw_redirect     = isset( $_POST['redirect_to'] ) && is_scalar( $_POST['redirect_to'] ) ? wp_unslash( (string) $_POST['redirect_to'] ) : '';
	$redirect         = wp_validate_redirect( $raw_redirect, home_url( '/' ) );

	wp_safe_redirect( add_query_arg( 'contact_message', $status, $redirect ) );
	exit;
}

/**
 * Process an anonymous or authenticated contact message.
 */
function yourhome_platform_handle_contact_message(): never {
	if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
		yourhome_platform_redirect_contact_message( 'invalid' );
	}

	if ( ! isset( $_POST[ YOURHOME_PLATFORM_CONTACT_NONCE_NAME ] ) || ! is_scalar( $_POST[ YOURHOME_PLATFORM_CONTACT_NONCE_NAME ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( (string) $_POST[ YOURHOME_PLATFORM_CONTACT_NONCE_NAME ] ) ), YOURHOME_PLATFORM_CONTACT_NONCE_ACTION ) ) {
		yourhome_platform_redirect_contact_message( 'invalid' );
	}

	$honeypot = isset( $_POST['yourhome_contact_website_check'] ) && is_scalar( $_POST['yourhome_contact_website_check'] ) ? trim( wp_unslash( (string) $_POST['yourhome_contact_website_check'] ) ) : '';
	if ( '' !== $honeypot ) {
		yourhome_platform_redirect_contact_message( 'invalid' );
	}

	$client_address = sanitize_text_field( (string) ( $_SERVER['REMOTE_ADDR'] ?? 'unknown' ) );
	$rate_key       = 'yourhome_contact_rate_' . hash( 'sha256', wp_salt( 'nonce' ) . $client_address );
	$attempts       = absint( get_transient( $rate_key ) );
	if ( 5 <= $attempts ) {
		yourhome_platform_redirect_contact_message( 'rate_limited' );
	}
	set_transient( $rate_key, $attempts + 1, HOUR_IN_SECONDS );

	$fields = array(
		'name'    => isset( $_POST['contact_name'] ) && is_scalar( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['contact_name'] ) ) : '',
		'email'   => isset( $_POST['contact_email'] ) && is_scalar( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( (string) $_POST['contact_email'] ) ) : '',
		'phone'   => isset( $_POST['contact_phone'] ) && is_scalar( $_POST['contact_phone'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['contact_phone'] ) ) : '',
		'topic'   => isset( $_POST['contact_topic'] ) && is_scalar( $_POST['contact_topic'] ) ? sanitize_key( wp_unslash( (string) $_POST['contact_topic'] ) ) : '',
		'message' => isset( $_POST['contact_message_body'] ) && is_scalar( $_POST['contact_message_body'] ) ? sanitize_textarea_field( wp_unslash( (string) $_POST['contact_message_body'] ) ) : '',
	);
	$allowed_topics = array( 'buying', 'renting', 'selling', 'listing', 'other' );
	$consent        = isset( $_POST['privacy_consent'] ) && '1' === (string) $_POST['privacy_consent'];

	if ( '' === $fields['name'] || 120 < mb_strlen( $fields['name'] ) || ! is_email( $fields['email'] ) || 40 < mb_strlen( $fields['phone'] ) || ! in_array( $fields['topic'], $allowed_topics, true ) || '' === $fields['message'] || 4000 < mb_strlen( $fields['message'] ) || ! $consent ) {
		yourhome_platform_redirect_contact_message( 'invalid' );
	}

	$fingerprint   = hash( 'sha256', strtolower( $fields['email'] ) . '|' . $fields['topic'] . '|' . strtolower( $fields['message'] ) );
	$duplicate_key = 'yourhome_contact_duplicate_' . $fingerprint;
	$duplicate_id = absint( get_transient( $duplicate_key ) );
	if ( 0 < $duplicate_id && YOURHOME_PLATFORM_CONTACT_MESSAGE_POST_TYPE === get_post_type( $duplicate_id ) && 'private' === get_post_status( $duplicate_id ) ) {
		yourhome_platform_redirect_contact_message( 'success' );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => YOURHOME_PLATFORM_CONTACT_MESSAGE_POST_TYPE,
			'post_status' => 'private',
			'post_title'  => sprintf( '%s — %s', $fields['name'], yourhome_platform_contact_topic_label( $fields['topic'] ) ),
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		yourhome_platform_redirect_contact_message( 'error' );
	}

	foreach ( $fields as $key => $value ) {
		update_post_meta( $post_id, '_yourhome_contact_' . $key, $value );
	}
	update_post_meta( $post_id, '_yourhome_contact_consent_at', current_time( 'mysql', true ) );
	update_post_meta( $post_id, '_yourhome_contact_submitted_at', current_time( 'mysql', true ) );
	set_transient( $duplicate_key, (int) $post_id, 10 * MINUTE_IN_SECONDS );

	yourhome_platform_redirect_contact_message( 'success' );
}

/**
 * Resolve a stored contact topic to an administrator-facing label.
 */
function yourhome_platform_contact_topic_label( string $topic ): string {
	$labels = array(
		'buying'  => __( 'Buying a property', 'yourhome-platform' ),
		'renting' => __( 'Renting a property', 'yourhome-platform' ),
		'selling' => __( 'Selling a property', 'yourhome-platform' ),
		'listing' => __( 'Existing listing', 'yourhome-platform' ),
		'other'   => __( 'Other enquiry', 'yourhome-platform' ),
	);

	return $labels[ $topic ] ?? $labels['other'];
}

/**
 * Add a protected, read-only message details panel.
 */
function yourhome_platform_add_contact_message_meta_box(): void {
	add_meta_box( 'yourhome-contact-details', __( 'Message details', 'yourhome-platform' ), 'yourhome_platform_render_contact_message_meta_box', YOURHOME_PLATFORM_CONTACT_MESSAGE_POST_TYPE, 'normal', 'high' );
}
add_action( 'add_meta_boxes_' . YOURHOME_PLATFORM_CONTACT_MESSAGE_POST_TYPE, 'yourhome_platform_add_contact_message_meta_box' );

/**
 * Render escaped contact message details in wp-admin.
 */
function yourhome_platform_render_contact_message_meta_box( WP_Post $post ): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$labels = array(
		'name'         => __( 'Name', 'yourhome-platform' ),
		'email'        => __( 'Email', 'yourhome-platform' ),
		'phone'        => __( 'Phone', 'yourhome-platform' ),
		'topic'        => __( 'Topic', 'yourhome-platform' ),
		'message'      => __( 'Message', 'yourhome-platform' ),
		'consent_at'   => __( 'Consent recorded at', 'yourhome-platform' ),
		'submitted_at' => __( 'Submitted at', 'yourhome-platform' ),
	);

	echo '<table class="widefat striped"><tbody>';
	foreach ( $labels as $key => $label ) {
		$value = (string) get_post_meta( $post->ID, '_yourhome_contact_' . $key, true );
		if ( 'topic' === $key ) {
			$value = yourhome_platform_contact_topic_label( $value );
		}
		echo '<tr><th scope="row" style="width:180px">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( $value ) ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

/**
 * Show useful fields in the contact message list.
 *
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string>
 */
function yourhome_platform_contact_message_columns( array $columns ): array {
	return array(
		'cb'    => $columns['cb'] ?? '',
		'title' => __( 'Sender', 'yourhome-platform' ),
		'email' => __( 'Email', 'yourhome-platform' ),
		'topic' => __( 'Topic', 'yourhome-platform' ),
		'date'  => $columns['date'] ?? __( 'Date', 'yourhome-platform' ),
	);
}
add_filter( 'manage_' . YOURHOME_PLATFORM_CONTACT_MESSAGE_POST_TYPE . '_posts_columns', 'yourhome_platform_contact_message_columns' );

/**
 * Render escaped contact message list values.
 */
function yourhome_platform_render_contact_message_column( string $column, int $post_id ): void {
	if ( 'email' === $column ) {
		echo esc_html( (string) get_post_meta( $post_id, '_yourhome_contact_email', true ) );
	}
	if ( 'topic' === $column ) {
		echo esc_html( yourhome_platform_contact_topic_label( (string) get_post_meta( $post_id, '_yourhome_contact_topic', true ) ) );
	}
}
add_action( 'manage_' . YOURHOME_PLATFORM_CONTACT_MESSAGE_POST_TYPE . '_posts_custom_column', 'yourhome_platform_render_contact_message_column', 10, 2 );
