<?php
/** @var array<string, mixed> $attributes */

$eyebrow = sanitize_text_field( (string) ( $attributes['eyebrow'] ?? '' ) );
$heading = sanitize_text_field( (string) ( $attributes['heading'] ?? '' ) );
$intro   = sanitize_text_field( (string) ( $attributes['intro'] ?? '' ) );
$link_label = sanitize_text_field( (string) ( $attributes['linkLabel'] ?? '' ) );
$link_url = esc_url_raw( (string) ( $attributes['linkUrl'] ?? '' ) );
$empty_message = sanitize_text_field( (string) ( $attributes['emptyMessage'] ?? '' ) );
$count   = min( 12, max( 1, absint( $attributes['count'] ?? 6 ) ) );
$query   = new WP_Query(
	array(
		'post_type'           => 'property',
		'post_status'         => 'publish',
		'posts_per_page'      => $count,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'ignore_sticky_posts' => true,
	)
);
$wrapper = get_block_wrapper_attributes( array( 'class' => 'yourhome-new-properties' ) );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> >
	<div class="yourhome-new-properties__inner">
		<div class="yourhome-section-heading">
			<div><?php if ( '' !== $eyebrow ) : ?><p class="yourhome-eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?><?php if ( '' !== $heading ) : ?><h2><?php echo esc_html( $heading ); ?></h2><?php endif; ?><?php if ( '' !== $intro ) : ?><p><?php echo esc_html( $intro ); ?></p><?php endif; ?></div>
			<?php if ( '' !== $link_label && '' !== $link_url ) : ?><a class="yourhome-text-link" href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $link_label ); ?> <span aria-hidden="true">→</span></a><?php endif; ?>
		</div>
		<?php if ( $query->have_posts() ) : ?>
			<div class="yourhome-property-grid">
				<?php while ( $query->have_posts() ) : $query->the_post(); echo yourhome_render_property_card( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				endwhile; ?>
			</div>
		<?php elseif ( '' !== $empty_message ) : ?>
			<p class="yourhome-empty-state"><?php echo esc_html( $empty_message ); ?></p>
		<?php endif; wp_reset_postdata(); ?>
	</div>
</section>
