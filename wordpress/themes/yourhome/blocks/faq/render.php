<?php
/** @var array<string, mixed> $attributes */
$eyebrow = sanitize_text_field( (string) ( $attributes['eyebrow'] ?? '' ) );
$heading = sanitize_text_field( (string) ( $attributes['heading'] ?? '' ) );
$link_label = sanitize_text_field( (string) ( $attributes['linkLabel'] ?? '' ) );
$link_url   = esc_url( (string) ( $attributes['linkUrl'] ?? '' ) );
$items   = is_array( $attributes['items'] ?? null ) ? $attributes['items'] : array();
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'yourhome-faq' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> >
	<div class="yourhome-faq__inner"><div class="yourhome-faq__content"><?php if ( '' !== $eyebrow ) : ?><p class="yourhome-eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?><?php if ( '' !== $heading ) : ?><h2><?php echo esc_html( $heading ); ?></h2><?php endif; ?><?php if ( '' !== $link_label && '' !== $link_url ) : ?><a class="yourhome-faq__link" href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $link_label ); ?> <span aria-hidden="true">→</span></a><?php endif; ?><div class="yourhome-faq__items">
		<?php foreach ( array_slice( $items, 0, 12 ) as $item ) : $question = sanitize_text_field( (string) ( $item['question'] ?? '' ) ); $answer = sanitize_textarea_field( (string) ( $item['answer'] ?? '' ) ); if ( '' === $question || '' === $answer ) { continue; } ?>
			<details><summary><?php echo esc_html( $question ); ?></summary><p><?php echo esc_html( $answer ); ?></p></details>
		<?php endforeach; ?>
	</div></div></div>
</section>
