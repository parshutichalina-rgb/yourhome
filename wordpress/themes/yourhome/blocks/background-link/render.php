<?php
/** @var array<string, mixed> $attributes */
$eyebrow = sanitize_text_field( (string) ( $attributes['eyebrow'] ?? '' ) );
$heading = sanitize_text_field( (string) ( $attributes['heading'] ?? '' ) );
$label   = sanitize_text_field( (string) ( $attributes['label'] ?? '' ) );
$url     = esc_url( (string) ( $attributes['url'] ?? '' ) );
$image   = esc_url( (string) ( $attributes['imageUrl'] ?? '' ) );
$style   = '' !== $image ? '--yourhome-background-link-image:url(' . esc_url( $image ) . ')' : '';
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'yourhome-background-link', 'style' => $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> >
	<<?php echo '' !== $url ? 'a' : 'div'; ?> class="yourhome-background-link__anchor"<?php if ( '' !== $url ) : ?> href="<?php echo esc_url( $url ); ?>"<?php endif; ?>>
		<span class="yourhome-background-link__content">
			<?php if ( '' !== $eyebrow ) : ?><span class="yourhome-background-link__eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
			<strong class="yourhome-background-link__heading"><?php echo esc_html( $heading ); ?></strong>
			<span class="yourhome-background-link__label"><?php echo esc_html( $label ); ?> <span aria-hidden="true">→</span></span>
		</span>
	</<?php echo '' !== $url ? 'a' : 'div'; ?>>
</section>
