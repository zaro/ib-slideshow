<?php

class IB_Slideshow_Admin {
	/**
	 * Initialize.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'save_post', array( __CLASS__, 'save_slides' ), 10, 3 );
		add_action( 'save_post', array( __CLASS__, 'save_settings' ), 10, 3 );
		add_action( 'edit_form_after_title', array( __CLASS__, 'output_shortcode' ) );
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public static function enqueue_scripts() {
		$screen = get_current_screen();

		if ( $screen && 'post' == $screen->base && 'ib_slideshow' == $screen->id ) {
			wp_enqueue_style( 'ib-slideshow-edit-slideshow', IB_SLIDESHOW_URL . '/admin/css/edit-slideshow.css' );
			wp_enqueue_media();
			wp_enqueue_script( 'ib-slideshow-edit-slideshow', IB_SLIDESHOW_URL . '/admin/js/edit-slideshow.js' );
		}
	}

	/**
	 * Add meta boxes.
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'ib_slideshow_settings',
			__( 'Slideshow Settings', 'ib-slideshow' ),
			array( __CLASS__, 'settings_meta_box' ),
			'ib_slideshow'
		);

		add_meta_box(
			'ib_slideshow_slides',
			__( 'Slides', 'ib-slideshow' ),
			array( __CLASS__, 'slides_meta_box' ),
			'ib_slideshow'
		);
	}

	/**
	 * Output slides meta box.
	 *
	 * @param WP_Post $post
	 */
	public static function slides_meta_box( $post ) {
		wp_nonce_field( 'ib_slideshow_slides_meta_box', 'ib_slideshow_slides_meta_box_nonce' );

		// Get slideshow type.
		$slideshow_settings = get_post_meta( $post->ID, '_ib_slideshow', true );
		$slideshow_type = '';

		if ( $slideshow_settings && isset( $slideshow_settings['type'] ) ) {
			$slideshow_type = $slideshow_settings['type'];
		} else {
			$types = apply_filters( 'ib_slideshow_types', array() );

			if ( count( $types ) ) {
				reset( $types );
				$slideshow_type = key( $types );
			}
		}

		if ( empty( $slideshow_type ) ) {
			return;
		}
		
		// Get slide fields given slideshow type.
		$fields = apply_filters( 'ib_slideshow_slide_fields', array() );
		$meta = new IB_Slideshow_Meta( $fields, $slideshow_type );

		// Get slides.
		$slides = IB_Slideshow::get_instance()->get_slides( $post->ID );

		include 'templates/slides-meta-box.php';
	}

	/**
	 * Save slides.
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 * @param boolean $update
	 */
	public static function save_slides( $post_id, $post, $update ) {
		if ( ! isset( $_POST['ib_slideshow_slides_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['ib_slideshow_slides_meta_box_nonce'], 'ib_slideshow_slides_meta_box' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'ib_slideshow' != get_post_type( $post_id ) || ! current_user_can( 'edit_ib_slideshow', $post_id ) ) {
			return;
		}

		// Get slideshow type.
		$slideshow_settings = get_post_meta( $post->ID, '_ib_slideshow', true );
		
		if ( ! is_array( $slideshow_settings ) || ! isset( $slideshow_settings['type'] ) ) {
			return;
		}

		$slideshow_type = $slideshow_settings['type'];

		// Get slide fields given slideshow type.
		$fields = apply_filters( 'ib_slideshow_slide_fields', array() );

		$meta = new IB_Slideshow_Meta( $fields );

		// Generate slides from user input.
		$slides = array();

		if ( ! isset( $_POST['ib_slide_order'] ) || ! is_array( $_POST['ib_slide_order'] ) ) {
			return;
		}

		foreach ( $_POST['ib_slide_order'] as $slide_order => $slide_num ) {
			$key = sanitize_key( 'ib_slide_' . $slide_num );

			if ( ! isset( $_POST[ $key ] ) || ! is_array( $_POST[ $key ] ) ) {
				continue;
			}

			if ( ! isset( $slides[ $slide_order ] ) ) {
				$slides[ $slide_order ] = array();
			}

			if ( isset( $_POST[ $key ]['attachment_id'] ) ) {
				$slides[ $slide_order ]['attachment_id'] = $_POST[ $key ]['attachment_id'];
			}

			foreach ( $meta->fields as $field ) {
				if ( ! $field->validSlideshowType( $slideshow_type ) ) {
					continue;
				}

				if ( ! isset( $_POST[ $key ][ $field->name ] ) ) {
					continue;
				}

				$slides[ $slide_order ][ $field->name ] = $field->sanitize( $_POST[ $key ][ $field->name ] );
			}
		}

		// Update slides in database.
		update_post_meta( $post_id, '_ib_slideshow_slides', $slides );
	}

	/**
	 * Output slideshow settings meta box.
	 *
	 * @param WP_Post $post
	 */
	public static function settings_meta_box( $post ) {
		// Verify nonce.
		wp_nonce_field( 'ib_slideshow_settings_meta_box', 'ib_slideshow_settings_meta_box_nonce' );

		$meta = new IB_Slideshow_Meta( self::get_slideshow_fields() );
		$meta->set_values( get_post_meta( $post->ID, '_ib_slideshow', true ) );

		include 'templates/settings-meta-box.php';
	}

	/**
	 * Save slideshow settings.
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 * @param boolean $update
	 */
	public static function save_settings( $post_id, $post, $update ) {
		if ( ! isset( $_POST['ib_slideshow_settings_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['ib_slideshow_settings_meta_box_nonce'], 'ib_slideshow_settings_meta_box' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'ib_slideshow' != get_post_type( $post_id ) || ! current_user_can( 'edit_ib_slideshow', $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['_ib_slideshow'] ) || ! is_array( $_POST['_ib_slideshow'] ) ) {
			return;
		}

		// Get slideshow type.
		if ( ! isset( $_POST['_ib_slideshow']['type'] ) ) {
			return;
		}

		$slideshow_type = $_POST['_ib_slideshow']['type'];

		// Get settings from user input.
		$meta = new IB_Slideshow_Meta( self::get_slideshow_fields() );
		$settings = array();

		foreach ( $meta->fields as $field ) {
			if ( ! $field->validSlideshowType( $slideshow_type ) ) {
				continue;
			}

			if ( ! isset( $_POST['_ib_slideshow'][ $field->name ] ) ) {
				continue;
			}

			$settings[ $field->name ] = $field->sanitize( $_POST['_ib_slideshow'][ $field->name ] );
		}

		// Update slides in database.
		update_post_meta( $post_id, '_ib_slideshow', $settings );
	}

	/**
	 * Get slideshow settings fields.
	 *
	 * @return array
	 */
	public static function get_slideshow_fields() {
		return apply_filters(
			'ib_slideshow_fields',
			array(
				array(
					'name'    => 'type',
					'type'    => 'select',
					'id'      => 'ib-slideshow-type',
					'label'   => __( 'Slideshow Type', 'ib-slideshow' ),
					'choices' => apply_filters( 'ib_slideshow_types', array() ),
				),
			)
		);
	}

	public static function output_shortcode() {
		global $post;

		if ( ! $post || 'ib_slideshow' != $post->post_type ) {
			return;
		}

		echo '<p><strong>' . __( 'Shortcode:', 'ib-slideshow' ) . '</strong> [ib_slideshow slug="' . esc_html( $post->post_name ) . '" /]</p>';
	}

	/**
	 * Output slide form HTML.
	 *
	 * @param IB_Slideshow_Meta $meta
	 * @param array $slide_settings
	 */
	public static function slide_template( $meta, $slide_settings ) {
		include 'templates/slide.php';
	}
}
