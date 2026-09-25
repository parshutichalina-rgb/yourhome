<?php
/**
 * Render the globally selected Footer page.
 */

$content    = yourhome_render_layout_slot( 'footer' );
$slot_class = 'yourhome-layout-slot yourhome-layout-slot--footer';
if ( yourhome_layout_slot_uses_custom_block( 'footer' ) ) {
	$slot_class .= ' has-custom-layout-block';
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $slot_class ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="yourhome-layout-slot__content"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</div>
