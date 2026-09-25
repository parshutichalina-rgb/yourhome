<?php
/**
 * Plugin Name: YourHome Platform
 * Description: Portable property, search, authorization, lead, and favorites behavior for YourHome.
 * Version: 0.1.0
 * Requires at least: 7.1
 * Requires PHP: 8.3
 * Text Domain: yourhome-platform
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'YOURHOME_PLATFORM_VERSION', '0.1.0' );
define( 'YOURHOME_PLATFORM_FILE', __FILE__ );
define( 'YOURHOME_PLATFORM_DIRECTORY', __DIR__ );

require_once YOURHOME_PLATFORM_DIRECTORY . '/includes/property.php';
require_once YOURHOME_PLATFORM_DIRECTORY . '/includes/lifecycle.php';

register_activation_hook( YOURHOME_PLATFORM_FILE, 'yourhome_platform_activate' );

add_action( 'plugins_loaded', 'yourhome_platform_maybe_upgrade' );
add_action( 'init', 'yourhome_platform_register_property_model' );
