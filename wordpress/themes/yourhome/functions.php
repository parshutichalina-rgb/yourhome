<?php
/**
 * YourHome block theme setup and custom block registration.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_stylesheet_directory() . '/inc/layouts.php';
require_once get_stylesheet_directory() . '/inc/customizer.php';
require_once get_stylesheet_directory() . '/inc/hero.php';

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
		$scripts = array(
			'yourhome-theme-blocks' => 'assets/js/blocks.js',
			'yourhome-layout-blocks' => 'assets/js/layout-blocks.js',
			'yourhome-hero-blocks' => 'assets/js/hero-blocks.js',
		);

		foreach ( $scripts as $handle => $relative_path ) {
			$path = get_stylesheet_directory() . '/' . $relative_path;

			wp_enqueue_script(
				$handle,
				get_stylesheet_directory_uri() . '/' . $relative_path,
				array( 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n' ),
				file_exists( $path ) ? (string) filemtime( $path ) : null,
				true
			);
		}
	}
);

add_action(
	'enqueue_block_assets',
	static function (): void {
		if ( is_admin() ) {
			return;
		}

		$path = get_stylesheet_directory() . '/assets/presentation.css';

		wp_enqueue_style(
			'yourhome-theme-presentation',
			get_stylesheet_directory_uri() . '/assets/presentation.css',
			array( 'wp-block-library' ),
			file_exists( $path ) ? (string) filemtime( $path ) : null
		);
	}
);

add_action( 'customize_register', 'yourhome_register_customizer' );
