<?php
/**
 * YourHome block theme setup and custom block registration.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	static function (): void {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/presentation.css' );
	}
);

add_action(
	'init',
	static function (): void {
		$block_directories = glob( get_stylesheet_directory() . '/blocks/*', GLOB_ONLYDIR );

		if ( false === $block_directories ) {
			return;
		}

		foreach ( $block_directories as $block_directory ) {
			if ( file_exists( $block_directory . '/block.json' ) ) {
				register_block_type( $block_directory );
			}
		}
	}
);

add_action(
	'enqueue_block_editor_assets',
	static function (): void {
		$path = get_stylesheet_directory() . '/assets/js/blocks.js';

		wp_enqueue_script(
			'yourhome-theme-blocks',
			get_stylesheet_directory_uri() . '/assets/js/blocks.js',
			array( 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n' ),
			file_exists( $path ) ? (string) filemtime( $path ) : null,
			true
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		$path = get_stylesheet_directory() . '/assets/presentation.css';

		wp_enqueue_style(
			'yourhome-theme-presentation',
			get_stylesheet_directory_uri() . '/assets/presentation.css',
			array( 'wp-block-library' ),
			file_exists( $path ) ? (string) filemtime( $path ) : null
		);
	}
);
