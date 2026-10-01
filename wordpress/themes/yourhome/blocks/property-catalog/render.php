<?php
if ( ! function_exists( 'yourhome_platform_normalize_property_filters' ) ) {
	echo '<p class="yourhome-catalog__notice">' . esc_html__( 'The property catalog is unavailable until YourHome Platform is active.', 'yourhome' ) . '</p>';
	return;
}

/** @var array<string, mixed> $attributes */
$eyebrow            = sanitize_text_field( (string) ( $attributes['eyebrow'] ?? '' ) );
$heading            = sanitize_text_field( (string) ( $attributes['heading'] ?? '' ) );
$intro              = sanitize_text_field( (string) ( $attributes['intro'] ?? '' ) );
$submit_label       = sanitize_text_field( (string) ( $attributes['submitLabel'] ?? '' ) );
$clear_label        = sanitize_text_field( (string) ( $attributes['clearLabel'] ?? '' ) );
$empty_heading      = sanitize_text_field( (string) ( $attributes['emptyHeading'] ?? '' ) );
$empty_text         = sanitize_text_field( (string) ( $attributes['emptyText'] ?? '' ) );
$empty_button_label = sanitize_text_field( (string) ( $attributes['emptyButtonLabel'] ?? '' ) );

$request = array();
foreach ( array( 'intent', 'type', 'city', 'min_price', 'max_price', 'bedrooms', 'bathrooms', 'sort', 'paged' ) as $key ) {
	if ( isset( $_GET[ $key ] ) && is_scalar( $_GET[ $key ] ) ) {
		$request[ $key ] = wp_unslash( $_GET[ $key ] );
	}
}
$filters = yourhome_platform_normalize_property_filters( $request );
$query   = new WP_Query( yourhome_platform_property_query_args( $filters ) );
$post_id = isset( $block->context['postId'] ) ? absint( $block->context['postId'] ) : 0;
$catalog_url = $post_id > 0 && 'page' === get_post_type( $post_id ) ? get_permalink( $post_id ) : false;
$catalog_url = is_string( $catalog_url ) ? $catalog_url : home_url( '/' );
$action_query = wp_parse_url( $catalog_url, PHP_URL_QUERY );
$route_fields = array();
if ( is_string( $action_query ) ) {
	parse_str( $action_query, $route_fields );
}
$types   = array( 'apartment' => __( 'Apartment', 'yourhome' ), 'house' => __( 'House', 'yourhome' ), 'townhouse' => __( 'Townhouse', 'yourhome' ), 'land' => __( 'Land', 'yourhome' ), 'commercial' => __( 'Commercial', 'yourhome' ) );
$pagination_base = str_replace( 999999999, '%#%', esc_url_raw( get_pagenum_link( 999999999 ) ) );
?>
<section <?php echo get_block_wrapper_attributes( array( 'id' => 'property-catalog', 'class' => 'yourhome-catalog' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> >
	<div class="yourhome-catalog__inner">
	<?php if ( '' !== $eyebrow || '' !== $heading || '' !== $intro ) : ?><header class="yourhome-catalog__header"><?php if ( '' !== $eyebrow ) : ?><p class="yourhome-eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?><?php if ( '' !== $heading ) : ?><h2><?php echo esc_html( $heading ); ?></h2><?php endif; ?><?php if ( '' !== $intro ) : ?><p><?php echo esc_html( $intro ); ?></p><?php endif; ?></header><?php endif; ?>
	<form class="yourhome-catalog__filters" action="<?php echo esc_url( $catalog_url ); ?>" method="get">
		<?php foreach ( $route_fields as $route_name => $route_value ) : if ( ! is_scalar( $route_value ) ) { continue; } ?><input type="hidden" name="<?php echo esc_attr( sanitize_key( (string) $route_name ) ); ?>" value="<?php echo esc_attr( (string) $route_value ); ?>"><?php endforeach; ?>
		<label><span><?php esc_html_e( 'Intent', 'yourhome' ); ?></span><select name="intent"><option value=""><?php esc_html_e( 'Sale or rent', 'yourhome' ); ?></option><option value="sale" <?php selected( $filters['intent'], 'sale' ); ?>><?php esc_html_e( 'For sale', 'yourhome' ); ?></option><option value="rent" <?php selected( $filters['intent'], 'rent' ); ?>><?php esc_html_e( 'For rent', 'yourhome' ); ?></option></select></label>
		<label><span><?php esc_html_e( 'Property type', 'yourhome' ); ?></span><select name="type"><option value=""><?php esc_html_e( 'All types', 'yourhome' ); ?></option><?php foreach ( $types as $slug => $label ) : ?><option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $filters['type'], $slug ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></label>
		<label><span><?php esc_html_e( 'City', 'yourhome' ); ?></span><input type="search" name="city" value="<?php echo esc_attr( (string) $filters['city'] ); ?>" placeholder="<?php esc_attr_e( 'Any city', 'yourhome' ); ?>"></label>
		<label><span><?php esc_html_e( 'Minimum price', 'yourhome' ); ?></span><input type="number" min="0" step="100" name="min_price" value="<?php echo 0 < $filters['min_price'] ? esc_attr( (string) $filters['min_price'] ) : ''; ?>"></label>
		<label><span><?php esc_html_e( 'Maximum price', 'yourhome' ); ?></span><input type="number" min="0" step="100" name="max_price" value="<?php echo 0 < $filters['max_price'] ? esc_attr( (string) $filters['max_price'] ) : ''; ?>"></label>
		<label><span><?php esc_html_e( 'Bedrooms', 'yourhome' ); ?></span><select name="bedrooms"><option value="0"><?php esc_html_e( 'Any', 'yourhome' ); ?></option><?php for ( $number = 1; $number <= 5; $number++ ) : ?><option value="<?php echo esc_attr( (string) $number ); ?>" <?php selected( $filters['bedrooms'], $number ); ?>><?php echo esc_html( (string) $number ); ?>+</option><?php endfor; ?></select></label>
		<label><span><?php esc_html_e( 'Bathrooms', 'yourhome' ); ?></span><select name="bathrooms"><option value="0"><?php esc_html_e( 'Any', 'yourhome' ); ?></option><?php for ( $number = 1; $number <= 4; $number++ ) : ?><option value="<?php echo esc_attr( (string) $number ); ?>" <?php selected( $filters['bathrooms'], $number ); ?>><?php echo esc_html( (string) $number ); ?>+</option><?php endfor; ?></select></label>
		<label><span><?php esc_html_e( 'Sort', 'yourhome' ); ?></span><select name="sort"><option value="newest" <?php selected( $filters['sort'], 'newest' ); ?>><?php esc_html_e( 'Newest first', 'yourhome' ); ?></option><option value="price_asc" <?php selected( $filters['sort'], 'price_asc' ); ?>><?php esc_html_e( 'Price: low to high', 'yourhome' ); ?></option><option value="price_desc" <?php selected( $filters['sort'], 'price_desc' ); ?>><?php esc_html_e( 'Price: high to low', 'yourhome' ); ?></option></select></label>
		<div class="yourhome-catalog__filter-actions"><?php if ( '' !== $submit_label ) : ?><button class="wp-element-button" type="submit"><?php echo esc_html( $submit_label ); ?></button><?php endif; ?><?php if ( '' !== $clear_label ) : ?><a href="<?php echo esc_url( $catalog_url ); ?>"><?php echo esc_html( $clear_label ); ?></a><?php endif; ?></div>
	</form>
	<div class="yourhome-catalog__summary" aria-live="polite"><p><?php echo esc_html( sprintf( _n( '%d property found', '%d properties found', (int) $query->found_posts, 'yourhome' ), (int) $query->found_posts ) ); ?></p></div>
	<?php if ( $query->have_posts() ) : ?><div class="yourhome-property-grid"><?php while ( $query->have_posts() ) : $query->the_post(); echo yourhome_render_property_card( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	endwhile; ?></div>
		<?php $links = paginate_links( array( 'base' => $pagination_base, 'format' => '?paged=%#%', 'current' => (int) $filters['paged'], 'total' => (int) $query->max_num_pages, 'type' => 'list', 'add_args' => array_filter( array_diff_key( $filters, array( 'paged' => true, 'per_page' => true ) ) ) ) ); if ( is_string( $links ) ) : ?><nav class="yourhome-catalog__pagination" aria-label="<?php esc_attr_e( 'Catalog pages', 'yourhome' ); ?>"><?php echo wp_kses_post( $links ); ?></nav><?php endif; ?>
	<?php else : ?><div class="yourhome-empty-state"><?php if ( '' !== $empty_heading ) : ?><h2><?php echo esc_html( $empty_heading ); ?></h2><?php endif; ?><?php if ( '' !== $empty_text ) : ?><p><?php echo esc_html( $empty_text ); ?></p><?php endif; ?><?php if ( '' !== $empty_button_label ) : ?><a class="wp-element-button" href="<?php echo esc_url( $catalog_url ); ?>"><?php echo esc_html( $empty_button_label ); ?></a><?php endif; ?></div><?php endif; wp_reset_postdata(); ?>
	</div>
</section>
