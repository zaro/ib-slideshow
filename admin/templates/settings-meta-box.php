<?php
	$meta->output( '_ib_slideshow[', ']' );
?>

<input type="hidden" id="current_slideshow_type" value="<?php if ( isset( $meta->values['type'] ) ) echo esc_attr( $meta->values['type'] ); ?>">

<div id="ib-slideshow-change-type-notice" class="ib-slideshow-error" style="display: none;">
	<p>
	<?php
		_e( 'Warning: The slides may lose values of the fields that are not defined in the new slideshow type. Please update the post to continue', 'ib-slideshow' );
	?>
	</p>
</div>

<script>
(function($) {
	function belongsToSlideshow(field, slideshowType) {
		var slideshowTypes = field.attr('data-slideshow_type');
		var i;

		if (slideshowTypes) {
			slideshowTypes = slideshowTypes.split(',');

			for (i = 0; i < slideshowTypes.length; ++i) {
				if ( slideshowType !== slideshowTypes[i] ) {
					field.css('display', 'none');
				} else {
					field.css('display', 'block');
				}
			}
		}
	}

	var fields = $('#ib_slideshow_settings .ib-slideshow-field');
	var selectSlideshowType = $('#ib-slideshow-type');
	var currentSlideshowType = document.getElementById('current_slideshow_type').value;

	for (var i = 0; i < fields.length; ++i) {
		belongsToSlideshow(fields.eq(i), selectSlideshowType.val());
	}

	selectSlideshowType.on('change', function() {
		var notice = $('#ib-slideshow-change-type-notice');
		
		if (this.value !== currentSlideshowType) {
			notice.insertAfter(this).show();
		} else {
			if (notice.is(':visible')) {
				notice.hide();
			}
		}

		for (var i = 0; i < fields.length; ++i) {
			belongsToSlideshow(fields.eq(i), this.value);
		}
	});
})(jQuery);
</script>
