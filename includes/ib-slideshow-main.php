<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class IB_Slideshow_Main {
	/**
	 * Initialize.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	}

	/**
	 * Plugin activation hook.
	 */
	public static function plugin_activation() {
		require_once 'ib-slideshow-install.php';
		$si = IB_Slideshow_Install::get_instance();
		$si->set_caps();
	}

	/**
	 * Register slideshow post type.
	 */
	public static function register_post_type() {
		register_post_type( 'ib_slideshow', array(
			'labels' => array(
				'name' => __( 'Slideshows', 'ib-slideshow' ),
				'singular_name' => __( 'Slideshow', 'ib-slideshow' ),
			),
			'public' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'show_in_menu' => true,
			'menu_icon' => 'dashicons-format-gallery',
			'show_in_admin_bar' => true,
			'capability_type' => 'ib_slideshow',
			'map_meta_cap' => true,
			'hierarchical' => false,
			'supports' => array( 'title' ),
			'has_archive' => false,
			'query_var' => false,
			'can_export' => true,
		) );
	}

	/**
	 * Slideshow shortcode.
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public static function slideshow_shortcode( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'slug' => '',
		), $atts );

		if ( empty( $atts['slug'] ) ) {
			return '';
		}

		return apply_filters( 'alter_ib_slideshow_shortcode', '', $atts, $content );
	}
}

add_shortcode( 'ib_slideshow', array( 'IB_Slideshow_Main', 'slideshow_shortcode' ) );
