<?php
	$slide_settings = array(
		'name_prefix'   => '',
		'name_suffix'   => ']',
		'attachment_id' => 0,
		'slide_num'     => 0,
	);
?>

<table id="ib-slideshow-slides">
	<tbody>
	<?php
		if ( ! empty( $slides ) ) {
			$i = 0;

			foreach ( $slides as $slide ) {
				$meta->set_values( $slide );
				$slide_settings['name_prefix'] = 'ib_slide_' . $i . '[';
				$slide_settings['attachment_id'] = $slide['attachment_id'];
				$slide_settings['slide_num'] = $i;

				echo '<tr class="ib-slideshow-slide" data-slide_num="' . $i . '">';
				self::slide_template( $meta, $slide_settings );
				echo '</tr>';
				++$i;
			}
		}
	?>
	</tbody>
</table>

<p>
	<button id="ib-slideshow-add-slide" class="button-secondary"><?php _e( 'Add Slide', 'ib-slideshow' ); ?></button>
</p>

<script type="text/html" id="tpl-ib-slideshow-slide">
<?php
	$meta->set_values( array() );
	$slide_settings['name_prefix'] = 'ib_slide_%slide_num%[';
	$slide_settings['attachment_id'] = 0;
	$slide_settings['slide_num'] = 0;

	self::slide_template( $meta, $slide_settings );
?>
</script>
