<?php

class IB_Slideshow_Install {
	/**
	 * @access public
	 * @var IB_Slideshow_Install
	 */
	protected static $instance;

	/**
	 * Private constructor; it's singleton class.
	 */
	private function __construct() {}

	/**
	 * Get instance of this class.
	 *
	 * @return IB_Slideshow_Install
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup capabilities.
	 */
	public function set_caps() {
		global $wp_roles;

		if ( isset( $wp_roles ) && is_object( $wp_roles ) ) {
			$admin_caps = array(
				'edit_ib_slideshow',
				'read_ib_slideshow',
				'delete_ib_slideshow',
				'edit_ib_slideshows',
				'edit_others_ib_slideshows',
				'publish_ib_slideshows',
				'read_private_ib_slideshows',
				'delete_ib_slideshows',
				'delete_private_ib_slideshows',
				'delete_published_ib_slideshows',
				'delete_others_ib_slideshows',
				'edit_private_ib_slideshows',
				'edit_published_ib_slideshows',
			);

			foreach ( $admin_caps as $cap ) {
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}
}
