(function($) {
	$(document).ready(function() {
		var imageUpload = null;

		/**
		 * Callback for user's selection.
		 *
		 * @param {jQuery} button
		 */
		function imageUploadSelect(button) {
			var attachment = imageUpload.state().get('selection').first().toJSON();

			// Update attachment id.
			button.siblings('input[name="attachment_id"]').val(attachment.id || 0);

			// Update image.
			var imgContainer = button.closest('.ib-slideshow-slide').find('> .slide-image');
			var img = imgContainer.find('> img');
			var img_src = (attachment.sizes && attachment.sizes.thumbnail) ? attachment.sizes.thumbnail.url : attachment.url;

			if ( ! img.length ) {
				img = $('<img>');
				img.attr('src', img_src);
				img.appendTo(imgContainer);
			} else {
				img.attr('src', img_src);
			}

			// Update attachment id.
			imgContainer.find('> input[name$="[attachment_id]"]').val(attachment.id);
			
			imageUpload.off('select', arguments.callee);
		}

		// Process image upload buttons.
		$('body').on('click', '.ib-slideshow-add-image', function(e) {
			e.preventDefault();
			var button = $(this);

			if (!imageUpload) {
				imageUpload = wp.media.frames.dm3SettingsImageUpload = wp.media({
					frame: 'select'
				});
			}

			imageUpload.off('select');
			imageUpload.on('select', function() {
				imageUploadSelect(button);
			});
			imageUpload.open();
		});

		// Manage slides.
		var slides = $('#ib-slideshow-slides');

		/**
		 * Add slide.
		 */
		function addSlide() {
			var slideTpl = $('#tpl-ib-slideshow-slide').html();
			var slide = $('<tr>');

			// Get next slide number.
			var slideNum = 0;

			slides.find('> tbody > tr').each(function() {
				var num = parseInt(this.getAttribute('data-slide_num'), 10);

				if (slideNum <= num) {
					slideNum = num + 1;
				}
			});

			slide.attr('data-slide_num', slideNum);
			slide.addClass('ib-slideshow-slide');
			slide.html(slideTpl.replace(/%slide_num%/g, slideNum));
			slide.find('input[name="ib_slide_order[]"]').val(slideNum);
			slides.find('> tbody').append(slide);
		}

		/**
		 * Open slide and display its settings.
		 *
		 * @param {jQuery} slide
		 */
		function openSlide(slide) {
			slide.addClass('open');
		}

		/**
		 * Close slide and hide its settings.
		 *
		 * @param {jQuery} slide
		 */
		function closeSlide(slide) {
			slide.removeClass('open');
		}

		/**
		 * Delete slide.
		 *
		 * @param {jQuery} slide
		 */
		function deleteSlide(slide) {
			slide.remove();
		}

		// Add slide action.
		$('#ib-slideshow-add-slide').on('click', function(e) {
			e.preventDefault();
			addSlide();
		});

		// Open slide action.
		slides.on('click', 'a.open-slide', function(e) {
			e.preventDefault();
			openSlide($(this).closest('.ib-slideshow-slide'));
		});

		// Close slide action.
		slides.on('click', 'a.close-slide', function(e) {
			e.preventDefault();
			closeSlide($(this).closest('.ib-slideshow-slide'));
		});

		// Delete slide action.
		slides.on('click', 'a.delete', function(e) {
			e.preventDefault();
			deleteSlide($(this).closest('.ib-slideshow-slide'));
		});

		// Make slides sortable.
		slides.find('> tbody').sortable({
			placeholder: 'slide-placeholder',
			items: 'tr',
			handle: '.slide-handle',
			start: function(e, ui) {
				ui.placeholder.css({
					width: ui.item.width() + 'px',
					height: ui.item.height() + 'px'
				});
			},
			helper: function(e, tr) {
				var originals = tr.children();
				var helper = tr.clone();
				helper.children().each(function(i) {
					var border = 1;

					if (i === originals.length - 1) {
						border = 2;
					}

					$(this).width(originals.eq(i).width() + border);
				});
				return helper;
			}
		});
	});
})(jQuery);
