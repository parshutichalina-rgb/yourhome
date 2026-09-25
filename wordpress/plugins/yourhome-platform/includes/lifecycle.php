<?php
/**
 * Versioned installation and upgrade lifecycle.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const YOURHOME_PLATFORM_VERSION_OPTION = 'yourhome_platform_version';

/**
 * Install or upgrade persistent plugin state without deleting user content.
 */
function yourhome_platform_install(): void {
	$installed_version = get_option( YOURHOME_PLATFORM_VERSION_OPTION );

	if ( YOURHOME_PLATFORM_VERSION === $installed_version ) {
		return;
	}

	yourhome_platform_register_property_model();
	yourhome_platform_seed_property_terms();
	flush_rewrite_rules();

	if ( false === $installed_version ) {
		add_option( YOURHOME_PLATFORM_VERSION_OPTION, YOURHOME_PLATFORM_VERSION, '', false );
		return;
	}

	update_option( YOURHOME_PLATFORM_VERSION_OPTION, YOURHOME_PLATFORM_VERSION, false );
}

/**
 * Run installation when WordPress activates the plugin.
 */
function yourhome_platform_activate(): void {
	yourhome_platform_install();
}

/**
 * Apply future versioned upgrades after a source update.
 */
function yourhome_platform_maybe_upgrade(): void {
	yourhome_platform_install();
}
