<td class="slide-handle"><span class="dashicons dashicons-sort"></span></td>

<td class="slide-image">
	<?php
		$image = wp_get_attachment_image_src( $slide_settings['attachment_id'], 'thumbnail' );

		if ( $image ) {
			echo '<img src="' . esc_url( $image[0] ) . '">';
		}
	?>
	<input type="hidden" name="<?php echo $slide_settings['name_prefix'] . 'attachment_id' . $slide_settings['name_suffix'] ?>" value="<?php echo intval( $slide_settings['attachment_id'] ); ?>">
	<input type="hidden" name="ib_slide_order[]" value="<?php echo intval( $slide_settings['slide_num'] ); ?>">
</td>

<td class="slide-settings">
	<div class="slide-options">
		<a class="ib-slideshow-add-image" href="#"><?php _e( 'Add Image', 'ib-slideshow' ); ?></a>
		<a class="open-slide" href="#"><?php _e( 'Settings', 'ib-slideshow' ); ?></a>
		<a class="close-slide" href="#"><?php _e( 'Close', 'ib-slideshow' ); ?></a>
		<a class="delete" href="#"><?php _e( 'Delete', 'ib-slideshow' ); ?></a>
	</div>

	<div class="slide-meta">
		<?php $meta->output( $slide_settings['name_prefix'], $slide_settings['name_suffix'] ); ?>
	</div>
</td>
