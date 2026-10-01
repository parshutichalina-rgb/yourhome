<?php
/** @var array<string, mixed> $attributes */
$eyebrow = sanitize_text_field( (string) ( $attributes['eyebrow'] ?? '' ) );
$heading = sanitize_text_field( (string) ( $attributes['heading'] ?? '' ) );
$text    = sanitize_textarea_field( (string) ( $attributes['text'] ?? '' ) );
$email   = sanitize_email( (string) ( $attributes['email'] ?? '' ) );
$phone   = sanitize_text_field( (string) ( $attributes['phone'] ?? '' ) );
$button_label = sanitize_text_field( (string) ( $attributes['buttonLabel'] ?? '' ) );
$button_url   = esc_url( (string) ( $attributes['buttonUrl'] ?? '' ) );
$image_url    = esc_url( (string) ( $attributes['imageUrl'] ?? '' ) );
$phone_href = preg_replace( '/[^0-9+]/', '', $phone );
$wrapper_attributes = array(
	'id'    => 'contact-us',
	'class' => 'yourhome-contact-us',
);
if ( '' !== $image_url ) {
	$wrapper_attributes['style'] = '--yourhome-contact-image:url("' . $image_url . '")';
}
?>
<section <?php echo get_block_wrapper_attributes( $wrapper_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> >
	<div class="yourhome-contact-us__inner"><div class="yourhome-contact-us__content"><?php if ( '' !== $eyebrow ) : ?><p class="yourhome-eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?><?php if ( '' !== $heading ) : ?><h2><?php echo esc_html( $heading ); ?></h2><?php endif; ?><?php if ( '' !== $text ) : ?><p><?php echo esc_html( $text ); ?></p><?php endif; ?><div class="yourhome-contact-us__actions">
		<?php if ( '' !== $button_url && '' !== $button_label ) : ?><a class="wp-element-button" href="<?php echo esc_url( $button_url ); ?>"><?php echo esc_html( $button_label ); ?></a><?php elseif ( '' !== $email && '' !== $button_label ) : ?><a class="wp-element-button" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $button_label ); ?></a><?php endif; ?>
		<?php if ( '' === $button_url && '' !== $email ) : ?><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a><?php endif; ?>
		<?php if ( '' === $button_url && '' !== $phone && is_string( $phone_href ) ) : ?><a href="tel:<?php echo esc_attr( $phone_href ); ?>"><?php echo esc_html( $phone ); ?></a><?php endif; ?>
	</div></div></div>
</section>
