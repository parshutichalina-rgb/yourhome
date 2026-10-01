<?php
/**
 * Current property presentation. Metadata remains owned by YourHome Platform.
 *
 * @var array<string, mixed> $attributes
 * @var WP_Block $block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = absint( $block->context['postId'] ?? get_the_ID() );
if ( 'property' !== get_post_type( $post_id ) ) {
	if ( is_admin() ) {
		echo '<p>' . esc_html__( 'Property details appear when viewing a property.', 'yourhome' ) . '</p>';
	}
	return;
}

$data = yourhome_get_property_detail_data( $post_id );
$is_demo_address = (bool) get_post_meta( $post_id, 'yourhome_demo_address', true );
$view = in_array( $attributes['view'] ?? '', array( 'facts', 'map' ), true ) ? $attributes['view'] : 'overview';
$map = 'map' === $view ? yourhome_get_property_map( $post_id ) : array();
if ( 'map' === $view && empty( $map ) ) {
	return;
}
$heading = sanitize_text_field( (string) ( $attributes['heading'] ?? '' ) );
$location_heading = sanitize_text_field( (string) ( $attributes['locationHeading'] ?? '' ) );
$contact_label = sanitize_text_field( (string) ( $attributes['contactLabel'] ?? '' ) );
$contact_url = esc_url( (string) ( $attributes['contactUrl'] ?? '' ) );
$wrapper_attributes = array( 'class' => 'yourhome-property-' . $view );
if ( ! empty( $attributes['anchor'] ) ) {
	$wrapper_attributes['id'] = sanitize_title( (string) $attributes['anchor'] );
}
?>
<div <?php echo get_block_wrapper_attributes( $wrapper_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( 'overview' === $view ) : ?>
		<?php $image_ids = yourhome_get_property_image_ids( $post_id ); ?>
		<div class="yourhome-property-overview__photos<?php echo 1 < count( $image_ids ) ? ' has-gallery' : ''; ?>">
			<?php if ( ! empty( $image_ids ) ) : ?>
				<?php foreach ( array_slice( $image_ids, 0, 5 ) as $index => $image_id ) : ?>
					<figure class="yourhome-property-overview__media">
						<?php echo wp_get_attachment_image( $image_id, 0 === $index ? 'large' : 'medium_large', false, array( 'class' => 'yourhome-property-overview__image', 'loading' => 0 === $index ? 'eager' : 'lazy', 'fetchpriority' => 0 === $index ? 'high' : 'auto' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</figure>
				<?php endforeach; ?>
			<?php else : ?>
				<figure class="yourhome-property-overview__media">
					<img class="yourhome-property-overview__image" src="<?php echo esc_url( get_theme_file_uri( '/assets/images/property-placeholder.svg' ) ); ?>" alt="">
					<figcaption><?php esc_html_e( 'Property photos have not been added yet.', 'yourhome' ); ?></figcaption>
				</figure>
			<?php endif; ?>
		</div>
		<header class="yourhome-property-overview__header">
			<div class="yourhome-property-overview__badges">
				<?php foreach ( array_filter( array( $data['intent'], $data['type'], $data['facts']['availability']['value'] ) ) as $badge ) : ?>
					<span><?php echo esc_html( $badge ); ?></span>
				<?php endforeach; ?>
			</div>
			<h1><?php echo esc_html( get_the_title( $post_id ) ); ?></h1>
			<?php if ( '' !== $data['address'] ) : ?><p class="yourhome-property-overview__address"><?php echo esc_html( $data['address'] ); ?></p><?php endif; ?>
			<?php if ( $is_demo_address ) : ?><p class="yourhome-property-demo-note"><?php esc_html_e( 'Fictional demo address', 'yourhome' ); ?></p><?php endif; ?>
			<?php if ( '' !== $contact_label && '' !== $contact_url ) : ?><a class="wp-element-button yourhome-property-overview__contact" href="<?php echo esc_url( $contact_url ); ?>"><?php echo esc_html( $contact_label ); ?></a><?php endif; ?>
		</header>
	<?php elseif ( 'map' === $view ) : ?>
		<?php if ( '' !== $heading ) : ?><h2><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
		<?php if ( '' !== $data['address'] ) : ?><p class="yourhome-property-facts__address"><?php echo esc_html( $data['address'] ); ?></p><?php endif; ?>
		<iframe class="yourhome-property-map__frame" src="<?php echo esc_url( $map['embed'] ); ?>" title="<?php echo esc_attr( sprintf( __( 'Location map for %s', 'yourhome' ), get_the_title( $post_id ) ) ); ?>" loading="lazy" referrerpolicy="no-referrer"></iframe>
		<?php if ( $is_demo_address ) : ?><p class="yourhome-property-demo-note"><?php esc_html_e( 'Approximate demo location; this pin does not identify a real property.', 'yourhome' ); ?></p><?php endif; ?>
		<?php if ( '' !== $map['view'] ) : ?><p><a href="<?php echo esc_url( $map['view'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open larger map (new tab)', 'yourhome' ); ?></a></p><?php endif; ?>
	<?php else : ?>
		<section class="yourhome-property-facts__panel">
			<?php if ( '' !== $heading ) : ?><h2><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
			<?php if ( '' !== $data['price'] ) : ?><p class="yourhome-property-facts__price"><?php echo esc_html( $data['price'] ); ?></p><?php endif; ?>
			<dl class="yourhome-property-facts__list">
				<?php foreach ( $data['facts'] as $key => $fact ) : ?>
					<?php if ( 'price' === $key || '' === $fact['value'] ) { continue; } ?>
					<div><dt><?php echo esc_html( $fact['label'] ); ?></dt><dd><?php echo esc_html( $fact['value'] ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
		</section>
		<section class="yourhome-property-facts__panel yourhome-property-facts__location">
			<?php if ( '' !== $location_heading ) : ?><h2><?php echo esc_html( $location_heading ); ?></h2><?php endif; ?>
			<?php if ( '' !== $data['address'] ) : ?><p class="yourhome-property-facts__address"><?php echo esc_html( $data['address'] ); ?></p><?php endif; ?>
			<dl class="yourhome-property-facts__list">
				<?php foreach ( $data['location'] as $fact ) : ?>
					<?php if ( '' === $fact['value'] ) { continue; } ?>
					<div><dt><?php echo esc_html( $fact['label'] ); ?></dt><dd><?php echo esc_html( $fact['value'] ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
		</section>
	<?php endif; ?>
</div>
