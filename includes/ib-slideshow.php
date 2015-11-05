<?php

class IB_Slideshow {
	/**
	 * @access public
	 * @var IB_Slideshow
	 */
	protected static $instance;

	/**
	 * Private constructor; it's singleton class.
	 */
	private function __construct() {}

	/**
	 * Get instance of this class.
	 *
	 * @return IB_Slideshow
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get slideshows.
	 *
	 * @return false|array
	 */
	public function get_slideshows() {
		return get_posts( array(
			'post_type'      => 'ib_slideshow',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		) );
	}

	/**
	 * Get slides by slideshow id.
	 *
	 * @param int $slideshow_id
	 * @return false|array
	 */
	public function get_slides( $slideshow_id ) {
		return get_post_meta( $slideshow_id, '_ib_slideshow_slides', true );
	}

	/**
	 * Get slideshow.
	 *
	 * @param string $key ID or slug
	 * @param mixed $value
	 * @return false|WP_Post
	 */
	public function get_slideshow_by( $key, $value ) {
		if ( 'ID' == $key ) {
			return get_post( $value );
		}

		$posts = get_posts( array(
			'post_type'      => 'ib_slideshow',
			'post_status'    => 'publish',
			'name'      => $value,
			'posts_per_page' => 1,
		) );

		if ( ! $posts ) {
			return false;
		}

		return $posts[0];
	}

	public function get_slideshow_settings( $post_id ) {
		return get_post_meta( $post_id, '_ib_slideshow', true );
	}
}
