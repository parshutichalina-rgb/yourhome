<?php
/**
 * Render the globally selected Header page.
 */

$content    = yourhome_render_layout_slot( 'header' );
$slot_class = 'yourhome-layout-slot yourhome-layout-slot--header';
if ( yourhome_layout_slot_uses_custom_block( 'header' ) ) {
	$slot_class .= ' has-custom-layout-block';
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $slot_class ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="yourhome-layout-slot__content"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</div>
