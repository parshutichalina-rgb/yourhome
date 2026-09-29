<?php
/** @var array<string, mixed> $attributes */
$eyebrow = sanitize_text_field( (string) ( $attributes['eyebrow'] ?? '' ) );
$heading = sanitize_text_field( (string) ( $attributes['heading'] ?? '' ) );
$text    = sanitize_textarea_field( (string) ( $attributes['text'] ?? '' ) );
$email   = sanitize_email( (string) ( $attributes['email'] ?? '' ) );
$phone   = sanitize_text_field( (string) ( $attributes['phone'] ?? '' ) );
$button_label = sanitize_text_field( (string) ( $attributes['buttonLabel'] ?? '' ) );
$phone_href = preg_replace( '/[^0-9+]/', '', $phone );
?>
<section <?php echo get_block_wrapper_attributes( array( 'id' => 'contact-us', 'class' => 'yourhome-contact-us' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> >
	<div class="yourhome-contact-us__inner"><div><?php if ( '' !== $eyebrow ) : ?><p class="yourhome-eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?><?php if ( '' !== $heading ) : ?><h2><?php echo esc_html( $heading ); ?></h2><?php endif; ?><?php if ( '' !== $text ) : ?><p><?php echo esc_html( $text ); ?></p><?php endif; ?></div><div class="yourhome-contact-us__actions">
		<?php if ( '' !== $email && '' !== $button_label ) : ?><a class="wp-element-button" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $button_label ); ?></a><?php endif; ?><?php if ( '' !== $email ) : ?><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a><?php endif; ?>
		<?php if ( '' !== $phone && is_string( $phone_href ) ) : ?><a href="tel:<?php echo esc_attr( $phone_href ); ?>"><?php echo esc_html( $phone ); ?></a><?php endif; ?>
	</div></div>
</section>
