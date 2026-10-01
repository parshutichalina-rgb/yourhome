<?php
/**
 * Render the seller application form.
 *
 * @var array<string, mixed> $attributes
 */

$eyebrow        = sanitize_text_field( (string) ( $attributes['eyebrow'] ?? '' ) );
$heading        = sanitize_text_field( (string) ( $attributes['heading'] ?? '' ) );
$intro          = sanitize_textarea_field( (string) ( $attributes['intro'] ?? '' ) );
$submit_label   = sanitize_text_field( (string) ( $attributes['submitLabel'] ?? '' ) );
$consent_text   = sanitize_text_field( (string) ( $attributes['consentText'] ?? '' ) );
$success_heading = sanitize_text_field( (string) ( $attributes['successHeading'] ?? '' ) );
$success_text   = sanitize_textarea_field( (string) ( $attributes['successText'] ?? '' ) );
$background_image_url = esc_url( (string) ( $attributes['backgroundImageUrl'] ?? '' ) );
$status         = isset( $_GET['seller_application'] ) && is_scalar( $_GET['seller_application'] ) ? sanitize_key( wp_unslash( (string) $_GET['seller_application'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$redirect_url   = get_permalink();
$privacy_url    = get_privacy_policy_url();

if ( ! is_string( $redirect_url ) || '' === $redirect_url ) {
	$redirect_url = home_url( '/' );
}

$messages = array(
	'invalid'      => __( 'Please check the required fields and try again.', 'yourhome' ),
	'rate_limited' => __( 'Too many attempts were received. Please try again later.', 'yourhome' ),
	'error'        => __( 'We could not save your application. Please try again.', 'yourhome' ),
);
$wrapper_attributes = array( 'class' => 'yourhome-seller-application' );
if ( '' !== $background_image_url ) {
	$wrapper_attributes['style'] = '--yourhome-seller-background:url("' . $background_image_url . '")';
}
?>
<section <?php echo get_block_wrapper_attributes( $wrapper_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="yourhome-seller-application__inner">
		<div class="yourhome-seller-application__intro">
			<?php if ( '' !== $eyebrow ) : ?><p class="yourhome-eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
			<?php if ( '' !== $heading ) : ?><h1><?php echo esc_html( $heading ); ?></h1><?php endif; ?>
			<?php if ( '' !== $intro ) : ?><p><?php echo esc_html( $intro ); ?></p><?php endif; ?>
			<ul class="yourhome-seller-application__steps" aria-label="<?php esc_attr_e( 'What happens next', 'yourhome' ); ?>">
				<li><span>01</span><?php esc_html_e( 'Tell us about your property', 'yourhome' ); ?></li>
				<li><span>02</span><?php esc_html_e( 'A local specialist reviews it', 'yourhome' ); ?></li>
				<li><span>03</span><?php esc_html_e( 'We contact you with the next steps', 'yourhome' ); ?></li>
			</ul>
		</div>
		<div class="yourhome-seller-application__panel">
			<?php if ( 'success' === $status ) : ?>
				<div class="yourhome-seller-application__success" role="status">
					<span aria-hidden="true">&#10003;</span>
					<?php if ( '' !== $success_heading ) : ?><h2><?php echo esc_html( $success_heading ); ?></h2><?php endif; ?>
					<?php if ( '' !== $success_text ) : ?><p><?php echo esc_html( $success_text ); ?></p><?php endif; ?>
				</div>
			<?php elseif ( ! function_exists( 'yourhome_platform_handle_seller_application' ) ) : ?>
				<p class="yourhome-seller-application__notice" role="alert"><?php esc_html_e( 'The application form is temporarily unavailable.', 'yourhome' ); ?></p>
			<?php else : ?>
				<?php if ( isset( $messages[ $status ] ) ) : ?><p class="yourhome-seller-application__notice" role="alert"><?php echo esc_html( $messages[ $status ] ); ?></p><?php endif; ?>
				<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="yourhome-seller-application__form">
					<input type="hidden" name="action" value="yourhome_submit_seller_application">
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_url ); ?>">
					<?php wp_nonce_field( YOURHOME_PLATFORM_SELLER_NONCE_ACTION, YOURHOME_PLATFORM_SELLER_NONCE_NAME ); ?>
					<div class="yourhome-seller-application__trap" hidden aria-hidden="true"><label for="company-website"><?php esc_html_e( 'Company website', 'yourhome' ); ?></label><input id="company-website" name="yourhome_seller_website_check" type="text" tabindex="-1" autocomplete="off"></div>
					<div class="yourhome-seller-application__grid">
						<label><?php esc_html_e( 'Your name', 'yourhome' ); ?> <span aria-hidden="true">*</span><input name="seller_name" type="text" maxlength="120" autocomplete="name" required></label>
						<label><?php esc_html_e( 'Email address', 'yourhome' ); ?> <span aria-hidden="true">*</span><input name="seller_email" type="email" maxlength="254" autocomplete="email" required></label>
						<label><?php esc_html_e( 'Phone number', 'yourhome' ); ?><input name="seller_phone" type="tel" maxlength="40" autocomplete="tel"></label>
						<label><?php esc_html_e( 'Property type', 'yourhome' ); ?> <span aria-hidden="true">*</span><select name="property_type" required><option value=""><?php esc_html_e( 'Select a type', 'yourhome' ); ?></option><option value="apartment"><?php esc_html_e( 'Apartment', 'yourhome' ); ?></option><option value="house"><?php esc_html_e( 'House', 'yourhome' ); ?></option><option value="townhouse"><?php esc_html_e( 'Townhouse', 'yourhome' ); ?></option><option value="land"><?php esc_html_e( 'Land', 'yourhome' ); ?></option><option value="commercial"><?php esc_html_e( 'Commercial property', 'yourhome' ); ?></option></select></label>
						<label><?php esc_html_e( 'City', 'yourhome' ); ?> <span aria-hidden="true">*</span><input name="property_city" type="text" maxlength="120" autocomplete="address-level2" required></label>
						<label><?php esc_html_e( 'Property address', 'yourhome' ); ?> <span aria-hidden="true">*</span><input name="property_address" type="text" maxlength="240" autocomplete="street-address" required></label>
						<label class="yourhome-seller-application__wide"><?php esc_html_e( 'Expected price', 'yourhome' ); ?><input name="expected_price" type="text" maxlength="80" inputmode="decimal" placeholder="<?php esc_attr_e( 'For example, 450,000 PLN', 'yourhome' ); ?>"></label>
						<label class="yourhome-seller-application__wide"><?php esc_html_e( 'Tell us about the property', 'yourhome' ); ?> <span aria-hidden="true">*</span><textarea name="property_details" rows="6" maxlength="4000" required></textarea></label>
					</div>
					<label class="yourhome-seller-application__consent"><input name="privacy_consent" type="checkbox" value="1" required><span><?php echo esc_html( $consent_text ); ?><?php if ( '' !== $privacy_url ) : ?> <a href="<?php echo esc_url( $privacy_url ); ?>"><?php esc_html_e( 'Privacy Policy', 'yourhome' ); ?></a><?php endif; ?></span></label>
					<button class="wp-element-button" type="submit"><?php echo esc_html( $submit_label ); ?> <span aria-hidden="true">&#8594;</span></button>
				</form>
			<?php endif; ?>
		</div>
	</div>
</section>
