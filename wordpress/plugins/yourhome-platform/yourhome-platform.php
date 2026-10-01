<?php
/**
 * Plugin Name: YourHome Platform
 * Description: Portable property, search, authorization, lead, and favorites behavior for YourHome.
 * Version: 0.3.0
 * Requires at least: 7.1
 * Requires PHP: 8.3
 * Text Domain: yourhome-platform
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'YOURHOME_PLATFORM_VERSION', '0.3.0' );
define( 'YOURHOME_PLATFORM_FILE', __FILE__ );
define( 'YOURHOME_PLATFORM_DIRECTORY', __DIR__ );

require_once YOURHOME_PLATFORM_DIRECTORY . '/includes/property.php';
require_once YOURHOME_PLATFORM_DIRECTORY . '/includes/property-address.php';
require_once YOURHOME_PLATFORM_DIRECTORY . '/includes/search.php';
require_once YOURHOME_PLATFORM_DIRECTORY . '/includes/lifecycle.php';
require_once YOURHOME_PLATFORM_DIRECTORY . '/includes/seller-leads.php';
require_once YOURHOME_PLATFORM_DIRECTORY . '/includes/contact-messages.php';

register_activation_hook( YOURHOME_PLATFORM_FILE, 'yourhome_platform_activate' );

add_action( 'plugins_loaded', 'yourhome_platform_maybe_upgrade' );
add_action( 'init', 'yourhome_platform_register_property_model' );
add_action( 'init', 'yourhome_platform_register_seller_lead_model' );
add_action( 'admin_post_yourhome_submit_seller_application', 'yourhome_platform_handle_seller_application' );
add_action( 'admin_post_nopriv_yourhome_submit_seller_application', 'yourhome_platform_handle_seller_application' );
add_action( 'init', 'yourhome_platform_register_contact_message_model' );
add_action( 'admin_post_yourhome_submit_contact_message', 'yourhome_platform_handle_contact_message' );
add_action( 'admin_post_nopriv_yourhome_submit_contact_message', 'yourhome_platform_handle_contact_message' );
