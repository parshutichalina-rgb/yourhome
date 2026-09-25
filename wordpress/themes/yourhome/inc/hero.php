<?php
/**
 * Dynamic Hero slider rendering.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render Hero Interactivity API directives and controls.
 *
 * @param array<string, mixed> $attributes Block attributes.
 */
function yourhome_render_hero( array $attributes, string $content, WP_Block $block ): string {
	$slide_count = 0;
	foreach ( $block->parsed_block['innerBlocks'] ?? array() as $inner_block ) {
		if ( 'yourhome/hero-slide' === ( $inner_block['blockName'] ?? '' ) ) {
			++$slide_count;
		}
	}

	if ( 0 === $slide_count ) {
		return $content;
	}

	$context = array(
		'activeIndex'  => 0,
		'count'        => $slide_count,
		'autoplay'     => (bool) ( $attributes['autoplay'] ?? true ),
		'interval'     => max( 3000, min( 15000, absint( $attributes['interval'] ?? 6000 ) ) ),
		'pauseOnHover' => (bool) ( $attributes['pauseOnHover'] ?? true ),
		'paused'       => false,
		'hoverPaused'  => false,
		'reducedMotion' => false,
		'liveMode'     => 'off',
	);

	$processor = new WP_HTML_Tag_Processor( $content );
	if ( $processor->next_tag( array( 'class_name' => 'wp-block-yourhome-hero' ) ) ) {
		$processor->set_attribute( 'data-wp-interactive', 'yourhome/hero' );
		$processor->set_attribute( 'data-wp-context', wp_json_encode( $context ) );
		$processor->set_attribute( 'data-wp-init', 'callbacks.init' );
		$processor->set_attribute( 'data-wp-on--mouseenter', 'actions.handlePause' );
		$processor->set_attribute( 'data-wp-on--mouseleave', 'actions.handleResume' );
		$processor->set_attribute( 'data-wp-on--keydown', 'actions.handleKeydown' );
		$processor->set_attribute( 'role', 'region' );
		$processor->set_attribute( 'aria-roledescription', __( 'carousel', 'yourhome' ) );
		$processor->set_attribute( 'aria-label', __( 'Featured properties', 'yourhome' ) );
	}

	$slide_index = 0;
	while ( $processor->next_tag( array( 'class_name' => 'wp-block-yourhome-hero-slide' ) ) ) {
		$processor->set_attribute( 'data-wp-context', wp_json_encode( array( 'slideIndex' => $slide_index ) ) );
		$processor->set_attribute( 'data-wp-class--is-active', 'state.isActiveSlide' );
		$processor->set_attribute( 'data-wp-bind--aria-hidden', 'state.isSlideHidden' );
		$processor->set_attribute( 'data-wp-bind--inert', 'state.isSlideHidden' );
		$processor->set_attribute( 'role', 'group' );
		$processor->set_attribute( 'aria-roledescription', __( 'slide', 'yourhome' ) );
		$processor->set_attribute( 'aria-label', sprintf( __( '%1$d of %2$d', 'yourhome' ), $slide_index + 1, $slide_count ) );
		if ( 0 === $slide_index ) {
			$processor->add_class( 'is-active' );
		}
		++$slide_index;
	}

	$content  = $processor->get_updated_html();
	$controls = yourhome_get_hero_controls( $attributes, $slide_count );
	$position = strrpos( $content, '</section>' );

	return false === $position ? $content . $controls : substr_replace( $content, $controls, $position, 0 );
}

/**
 * Build controls for a rendered Hero.
 *
 * @param array<string, mixed> $attributes Block attributes.
 */
function yourhome_get_hero_controls( array $attributes, int $slide_count ): string {
	if ( 2 > $slide_count ) {
		return '';
	}

	$output = '<div class="yourhome-hero__controls" aria-label="' . esc_attr__( 'Slider controls', 'yourhome' ) . '">';
	if ( (bool) ( $attributes['showArrows'] ?? true ) ) {
		$output .= '<button type="button" class="yourhome-hero__arrow" data-wp-on--click="actions.handlePrevious" aria-label="' . esc_attr__( 'Previous slide', 'yourhome' ) . '">←</button>';
	}

	if ( (bool) ( $attributes['showPagination'] ?? true ) ) {
		$output .= '<div class="yourhome-hero__pagination">';
		for ( $index = 0; $index < $slide_count; ++$index ) {
			$output .= sprintf(
				'<button type="button" class="yourhome-hero__dot" data-wp-context="%1$s" data-wp-on--click="actions.goTo" data-wp-bind--aria-current="state.isActiveDot" aria-label="%2$s"></button>',
				esc_attr( wp_json_encode( array( 'targetIndex' => $index ) ) ),
				esc_attr( sprintf( __( 'Show slide %d', 'yourhome' ), $index + 1 ) )
			);
		}
		$output .= '</div>';
	}

	$output .= '<span class="yourhome-hero__status" data-wp-bind--aria-live="state.liveMode" data-wp-text="state.statusText">1 / ' . esc_html( (string) $slide_count ) . '</span>';
	$output .= '<button type="button" class="yourhome-hero__pause" data-wp-on--click="actions.togglePause" data-wp-bind--disabled="state.isPauseDisabled" data-wp-text="state.pauseLabel">' . esc_html__( 'Pause slides', 'yourhome' ) . '</button>';

	if ( (bool) ( $attributes['showArrows'] ?? true ) ) {
		$output .= '<button type="button" class="yourhome-hero__arrow" data-wp-on--click="actions.handleNext" aria-label="' . esc_attr__( 'Next slide', 'yourhome' ) . '">→</button>';
	}

	return $output . '</div>';
}
