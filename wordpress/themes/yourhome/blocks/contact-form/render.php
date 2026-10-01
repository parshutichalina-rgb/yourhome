<?php
/**
 * Render the Contact Us form.
 *
 * @var array<string, mixed> $attributes
 */

$eyebrow         = sanitize_text_field( (string) ( $attributes['eyebrow'] ?? '' ) );
$heading         = sanitize_text_field( (string) ( $attributes['heading'] ?? '' ) );
$intro           = sanitize_textarea_field( (string) ( $attributes['intro'] ?? '' ) );
$details_heading = sanitize_text_field( (string) ( $attributes['detailsHeading'] ?? '' ) );
$details_text    = sanitize_textarea_field( (string) ( $attributes['detailsText'] ?? '' ) );
$email           = sanitize_email( (string) ( $attributes['email'] ?? '' ) );
$phone           = sanitize_text_field( (string) ( $attributes['phone'] ?? '' ) );
$submit_label    = sanitize_text_field( (string) ( $attributes['submitLabel'] ?? '' ) );
$consent_text    = sanitize_text_field( (string) ( $attributes['consentText'] ?? '' ) );
$success_heading = sanitize_text_field( (string) ( $attributes['successHeading'] ?? '' ) );
$success_text    = sanitize_textarea_field( (string) ( $attributes['successText'] ?? '' ) );
$status          = isset( $_GET['contact_message'] ) && is_scalar( $_GET['contact_message'] ) ? sanitize_key( wp_unslash( (string) $_GET['contact_message'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$redirect_url    = get_permalink();
$privacy_url     = get_privacy_policy_url();
$phone_href      = preg_replace( '/[^0-9+]/', '', $phone );

if ( ! is_string( $redirect_url ) || '' === $redirect_url ) {
	$redirect_url = home_url( '/' );
}

$messages = array(
	'invalid'      => __( 'Please check the required fields and try again.', 'yourhome' ),
	'rate_limited' => __( 'Too many attempts were received. Please try again later.', 'yourhome' ),
	'error'        => __( 'We could not save your message. Please try again.', 'yourhome' ),
);
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'yourhome-contact-form' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="yourhome-contact-form__inner">
		<div class="yourhome-contact-form__intro">
			<?php if ( '' !== $eyebrow ) : ?><p class="yourhome-eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
			<?php if ( '' !== $heading ) : ?><h1><?php echo esc_html( $heading ); ?></h1><?php endif; ?>
			<?php if ( '' !== $intro ) : ?><p class="yourhome-contact-form__lead"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
			<div class="yourhome-contact-form__details">
				<?php if ( '' !== $details_heading ) : ?><h2><?php echo esc_html( $details_heading ); ?></h2><?php endif; ?>
				<?php if ( '' !== $details_text ) : ?><p><?php echo esc_html( $details_text ); ?></p><?php endif; ?>
				<?php if ( '' !== $email ) : ?><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a><?php endif; ?>
				<?php if ( '' !== $phone && is_string( $phone_href ) ) : ?><a href="tel:<?php echo esc_attr( $phone_href ); ?>"><?php echo esc_html( $phone ); ?></a><?php endif; ?>
			</div>
		</div>
		<div class="yourhome-contact-form__panel">
			<?php if ( 'success' === $status ) : ?>
				<div class="yourhome-contact-form__success" role="status">
					<span aria-hidden="true">&#10003;</span>
					<?php if ( '' !== $success_heading ) : ?><h2><?php echo esc_html( $success_heading ); ?></h2><?php endif; ?>
					<?php if ( '' !== $success_text ) : ?><p><?php echo esc_html( $success_text ); ?></p><?php endif; ?>
				</div>
			<?php elseif ( ! function_exists( 'yourhome_platform_handle_contact_message' ) ) : ?>
				<p class="yourhome-contact-form__notice" role="alert"><?php esc_html_e( 'The contact form is temporarily unavailable.', 'yourhome' ); ?></p>
			<?php else : ?>
				<?php if ( isset( $messages[ $status ] ) ) : ?><p class="yourhome-contact-form__notice" role="alert"><?php echo esc_html( $messages[ $status ] ); ?></p><?php endif; ?>
				<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="yourhome-contact-form__form">
					<input type="hidden" name="action" value="yourhome_submit_contact_message">
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_url ); ?>">
					<?php wp_nonce_field( YOURHOME_PLATFORM_CONTACT_NONCE_ACTION, YOURHOME_PLATFORM_CONTACT_NONCE_NAME ); ?>
					<div class="yourhome-contact-form__trap" hidden aria-hidden="true"><label for="contact-company-website"><?php esc_html_e( 'Company website', 'yourhome' ); ?></label><input id="contact-company-website" name="yourhome_contact_website_check" type="text" tabindex="-1" autocomplete="off"></div>
					<div class="yourhome-contact-form__grid">
						<label><?php esc_html_e( 'Your name', 'yourhome' ); ?> <span aria-hidden="true">*</span><input name="contact_name" type="text" maxlength="120" autocomplete="name" required></label>
						<label><?php esc_html_e( 'Email address', 'yourhome' ); ?> <span aria-hidden="true">*</span><input name="contact_email" type="email" maxlength="254" autocomplete="email" required></label>
						<label><?php esc_html_e( 'Phone number', 'yourhome' ); ?><input name="contact_phone" type="tel" maxlength="40" autocomplete="tel"></label>
						<label><?php esc_html_e( 'How can we help?', 'yourhome' ); ?> <span aria-hidden="true">*</span><select name="contact_topic" required><option value=""><?php esc_html_e( 'Select a topic', 'yourhome' ); ?></option><option value="buying"><?php esc_html_e( 'Buying a property', 'yourhome' ); ?></option><option value="renting"><?php esc_html_e( 'Renting a property', 'yourhome' ); ?></option><option value="selling"><?php esc_html_e( 'Selling a property', 'yourhome' ); ?></option><option value="listing"><?php esc_html_e( 'An existing listing', 'yourhome' ); ?></option><option value="other"><?php esc_html_e( 'Something else', 'yourhome' ); ?></option></select></label>
						<label class="yourhome-contact-form__wide"><?php esc_html_e( 'Your message', 'yourhome' ); ?> <span aria-hidden="true">*</span><textarea name="contact_message_body" rows="7" maxlength="4000" required></textarea></label>
					</div>
					<label class="yourhome-contact-form__consent"><input name="privacy_consent" type="checkbox" value="1" required><span><?php echo esc_html( $consent_text ); ?><?php if ( '' !== $privacy_url ) : ?> <a href="<?php echo esc_url( $privacy_url ); ?>"><?php esc_html_e( 'Privacy Policy', 'yourhome' ); ?></a><?php endif; ?></span></label>
					<button class="wp-element-button" type="submit"><?php echo esc_html( $submit_label ); ?> <span aria-hidden="true">&#8594;</span></button>
				</form>
			<?php endif; ?>
		</div>
	</div>
</section>
