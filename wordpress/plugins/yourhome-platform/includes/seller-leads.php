<?php
/**
 * Seller application persistence, submission handling, and admin visibility.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const YOURHOME_PLATFORM_SELLER_LEAD_POST_TYPE = 'yourhome_seller_lead';
const YOURHOME_PLATFORM_SELLER_ACTION = 'yourhome_submit_seller_application';
const YOURHOME_PLATFORM_SELLER_NONCE_ACTION = 'yourhome_submit_seller_application';
const YOURHOME_PLATFORM_SELLER_NONCE_NAME = 'yourhome_seller_nonce';

/**
 * Register private seller applications for administrator review.
 */
function yourhome_platform_register_seller_lead_model(): void {
	$admin_capability = 'manage_options';
	register_post_type(
		YOURHOME_PLATFORM_SELLER_LEAD_POST_TYPE,
		array(
			'labels'       => array(
				'name'          => __( 'Seller applications', 'yourhome-platform' ),
				'singular_name' => __( 'Seller application', 'yourhome-platform' ),
				'all_items'     => __( 'All applications', 'yourhome-platform' ),
				'edit_item'     => __( 'Review application', 'yourhome-platform' ),
				'not_found'     => __( 'No seller applications found.', 'yourhome-platform' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => false,
			'menu_icon'    => 'dashicons-clipboard',
			'menu_position' => 21,
			'supports'     => array( 'title' ),
			'capabilities' => array(
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
			'map_meta_cap' => false,
		)
	);
}

/**
 * Redirect back to the public form with a bounded status.
 */
function yourhome_platform_redirect_seller_application( string $status ): never {
	$allowed_statuses = array( 'success', 'invalid', 'rate_limited', 'error' );
	$status = in_array( $status, $allowed_statuses, true ) ? $status : 'error';
	$raw_redirect = isset( $_POST['redirect_to'] ) && is_scalar( $_POST['redirect_to'] ) ? wp_unslash( (string) $_POST['redirect_to'] ) : '';
	$redirect = wp_validate_redirect( $raw_redirect, home_url( '/' ) );
	wp_safe_redirect( add_query_arg( 'seller_application', $status, $redirect ) );
	exit;
}

/**
 * Process an anonymous or authenticated seller application.
 */
function yourhome_platform_handle_seller_application(): never {
	if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
		yourhome_platform_redirect_seller_application( 'invalid' );
	}

	if ( ! isset( $_POST[ YOURHOME_PLATFORM_SELLER_NONCE_NAME ] ) || ! is_scalar( $_POST[ YOURHOME_PLATFORM_SELLER_NONCE_NAME ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( (string) $_POST[ YOURHOME_PLATFORM_SELLER_NONCE_NAME ] ) ), YOURHOME_PLATFORM_SELLER_NONCE_ACTION ) ) {
		yourhome_platform_redirect_seller_application( 'invalid' );
	}

	$honeypot = isset( $_POST['yourhome_seller_website_check'] ) && is_scalar( $_POST['yourhome_seller_website_check'] ) ? trim( wp_unslash( (string) $_POST['yourhome_seller_website_check'] ) ) : '';
	if ( '' !== $honeypot ) {
		yourhome_platform_redirect_seller_application( 'invalid' );
	}

	$client_address = sanitize_text_field( (string) ( $_SERVER['REMOTE_ADDR'] ?? 'unknown' ) );
	$rate_key = 'yourhome_seller_rate_' . hash( 'sha256', wp_salt( 'nonce' ) . $client_address );
	$attempts = absint( get_transient( $rate_key ) );
	if ( 5 <= $attempts ) {
		yourhome_platform_redirect_seller_application( 'rate_limited' );
	}
	set_transient( $rate_key, $attempts + 1, HOUR_IN_SECONDS );

	$fields = array(
		'name'           => isset( $_POST['seller_name'] ) && is_scalar( $_POST['seller_name'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['seller_name'] ) ) : '',
		'email'          => isset( $_POST['seller_email'] ) && is_scalar( $_POST['seller_email'] ) ? sanitize_email( wp_unslash( (string) $_POST['seller_email'] ) ) : '',
		'phone'          => isset( $_POST['seller_phone'] ) && is_scalar( $_POST['seller_phone'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['seller_phone'] ) ) : '',
		'property_type'  => isset( $_POST['property_type'] ) && is_scalar( $_POST['property_type'] ) ? sanitize_key( wp_unslash( (string) $_POST['property_type'] ) ) : '',
		'city'           => isset( $_POST['property_city'] ) && is_scalar( $_POST['property_city'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['property_city'] ) ) : '',
		'address'        => isset( $_POST['property_address'] ) && is_scalar( $_POST['property_address'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['property_address'] ) ) : '',
		'expected_price' => isset( $_POST['expected_price'] ) && is_scalar( $_POST['expected_price'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['expected_price'] ) ) : '',
		'details'        => isset( $_POST['property_details'] ) && is_scalar( $_POST['property_details'] ) ? sanitize_textarea_field( wp_unslash( (string) $_POST['property_details'] ) ) : '',
	);
	$allowed_types = array( 'apartment', 'house', 'townhouse', 'land', 'commercial' );
	$consent = isset( $_POST['privacy_consent'] ) && '1' === (string) $_POST['privacy_consent'];

	if ( '' === $fields['name'] || 120 < mb_strlen( $fields['name'] ) || ! is_email( $fields['email'] ) || ! in_array( $fields['property_type'], $allowed_types, true ) || '' === $fields['city'] || 120 < mb_strlen( $fields['city'] ) || '' === $fields['address'] || 240 < mb_strlen( $fields['address'] ) || '' === $fields['details'] || 4000 < mb_strlen( $fields['details'] ) || ! $consent ) {
		yourhome_platform_redirect_seller_application( 'invalid' );
	}

	$fingerprint = hash( 'sha256', strtolower( $fields['email'] ) . '|' . strtolower( $fields['city'] ) . '|' . strtolower( $fields['details'] ) );
	$duplicate_key = 'yourhome_seller_duplicate_' . $fingerprint;
	$duplicate_id = absint( get_transient( $duplicate_key ) );
	if ( 0 < $duplicate_id && YOURHOME_PLATFORM_SELLER_LEAD_POST_TYPE === get_post_type( $duplicate_id ) && 'private' === get_post_status( $duplicate_id ) ) {
		yourhome_platform_redirect_seller_application( 'success' );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => YOURHOME_PLATFORM_SELLER_LEAD_POST_TYPE,
			'post_status' => 'private',
			'post_title'  => sprintf( '%s — %s', $fields['name'], $fields['city'] ),
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		yourhome_platform_redirect_seller_application( 'error' );
	}

	foreach ( $fields as $key => $value ) {
		update_post_meta( $post_id, '_yourhome_seller_' . $key, $value );
	}
	update_post_meta( $post_id, '_yourhome_seller_consent_at', current_time( 'mysql', true ) );
	update_post_meta( $post_id, '_yourhome_seller_submitted_at', current_time( 'mysql', true ) );
	$notification_sent = yourhome_platform_notify_seller_application( (int) $post_id, $fields );
	update_post_meta( $post_id, '_yourhome_seller_notification', $notification_sent ? 'sent' : 'failed' );
	set_transient( $duplicate_key, (int) $post_id, 10 * MINUTE_IN_SECONDS );

	yourhome_platform_redirect_seller_application( 'success' );
}

/**
 * Notify the configured site administrator after persistence succeeds.
 *
 * The private admin record remains authoritative when email transport is absent.
 *
 * @param array<string, string> $fields Sanitized application fields.
 */
function yourhome_platform_notify_seller_application( int $post_id, array $fields ): bool {
	$recipient = sanitize_email( (string) get_option( 'admin_email', '' ) );
	if ( ! is_email( $recipient ) ) {
		return false;
	}

	$subject = sprintf(
		/* translators: %s: applicant name. */
		__( 'New seller application from %s', 'yourhome-platform' ),
		$fields['name']
	);
	$message = implode(
		"\n",
		array(
			__( 'A new seller application was saved in WordPress.', 'yourhome-platform' ),
			sprintf( __( 'Applicant: %s', 'yourhome-platform' ), $fields['name'] ),
			sprintf( __( 'City: %s', 'yourhome-platform' ), $fields['city'] ),
			sprintf( __( 'Review: %s', 'yourhome-platform' ), admin_url( 'post.php?post=' . $post_id . '&action=edit' ) ),
		)
	);

	return wp_mail( $recipient, $subject, $message );
}

/**
 * Add a protected, read-only application details panel.
 */
function yourhome_platform_add_seller_lead_meta_box(): void {
	add_meta_box( 'yourhome-seller-details', __( 'Application details', 'yourhome-platform' ), 'yourhome_platform_render_seller_lead_meta_box', YOURHOME_PLATFORM_SELLER_LEAD_POST_TYPE, 'normal', 'high' );
}
add_action( 'add_meta_boxes_' . YOURHOME_PLATFORM_SELLER_LEAD_POST_TYPE, 'yourhome_platform_add_seller_lead_meta_box' );

/**
 * Render escaped seller application details in wp-admin.
 */
function yourhome_platform_render_seller_lead_meta_box( WP_Post $post ): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$labels = array(
		'name'           => __( 'Name', 'yourhome-platform' ),
		'email'          => __( 'Email', 'yourhome-platform' ),
		'phone'          => __( 'Phone', 'yourhome-platform' ),
		'property_type'  => __( 'Property type', 'yourhome-platform' ),
		'city'           => __( 'City', 'yourhome-platform' ),
		'address'        => __( 'Address', 'yourhome-platform' ),
		'expected_price' => __( 'Expected price', 'yourhome-platform' ),
		'details'        => __( 'Property details', 'yourhome-platform' ),
		'consent_at'     => __( 'Consent recorded at', 'yourhome-platform' ),
		'submitted_at'   => __( 'Submitted at', 'yourhome-platform' ),
		'notification'   => __( 'Admin email notification', 'yourhome-platform' ),
	);

	echo '<table class="widefat striped"><tbody>';
	foreach ( $labels as $key => $label ) {
		$value = (string) get_post_meta( $post->ID, '_yourhome_seller_' . $key, true );
		echo '<tr><th scope="row" style="width:180px">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( $value ) ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

/**
 * Show useful fields in the application list.
 *
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string>
 */
function yourhome_platform_seller_lead_columns( array $columns ): array {
	return array(
		'cb'       => $columns['cb'] ?? '',
		'title'    => __( 'Applicant', 'yourhome-platform' ),
		'email'    => __( 'Email', 'yourhome-platform' ),
		'city'     => __( 'City', 'yourhome-platform' ),
		'type'     => __( 'Property type', 'yourhome-platform' ),
		'date'     => $columns['date'] ?? __( 'Date', 'yourhome-platform' ),
	);
}
add_filter( 'manage_' . YOURHOME_PLATFORM_SELLER_LEAD_POST_TYPE . '_posts_columns', 'yourhome_platform_seller_lead_columns' );

/**
 * Render escaped seller application list values.
 */
function yourhome_platform_render_seller_lead_column( string $column, int $post_id ): void {
	$meta_key = array(
		'email' => 'email',
		'city'  => 'city',
		'type'  => 'property_type',
	)[ $column ] ?? '';
	if ( '' !== $meta_key ) {
		echo esc_html( (string) get_post_meta( $post_id, '_yourhome_seller_' . $meta_key, true ) );
	}
}
add_action( 'manage_' . YOURHOME_PLATFORM_SELLER_LEAD_POST_TYPE . '_posts_custom_column', 'yourhome_platform_render_seller_lead_column', 10, 2 );

/**
 * Add recent seller applications to the main WordPress dashboard.
 */
function yourhome_platform_register_seller_dashboard_widget(): void {
	if ( current_user_can( 'manage_options' ) ) {
		wp_add_dashboard_widget( 'yourhome-seller-applications', __( 'Recent seller applications', 'yourhome-platform' ), 'yourhome_platform_render_seller_dashboard_widget' );
	}
}
add_action( 'wp_dashboard_setup', 'yourhome_platform_register_seller_dashboard_widget' );

/**
 * Render a bounded list of recent private applications.
 */
function yourhome_platform_render_seller_dashboard_widget(): void {
	$applications = get_posts(
		array(
			'post_type'      => YOURHOME_PLATFORM_SELLER_LEAD_POST_TYPE,
			'post_status'    => 'private',
			'posts_per_page' => 5,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	if ( array() === $applications ) {
		echo '<p>' . esc_html__( 'No seller applications have been received yet.', 'yourhome-platform' ) . '</p>';
		return;
	}

	echo '<ul>';
	foreach ( $applications as $application ) {
		$city = (string) get_post_meta( $application->ID, '_yourhome_seller_city', true );
		echo '<li><a href="' . esc_url( get_edit_post_link( $application->ID, 'raw' ) ) . '"><strong>' . esc_html( get_the_title( $application ) ) . '</strong></a>';
		if ( '' !== $city ) {
			echo '<br><small>' . esc_html( $city ) . '</small>';
		}
		echo '</li>';
	}
	echo '</ul><p><a href="' . esc_url( admin_url( 'edit.php?post_type=' . YOURHOME_PLATFORM_SELLER_LEAD_POST_TYPE ) ) . '">' . esc_html__( 'View all applications', 'yourhome-platform' ) . ' &rarr;</a></p>';
}
