<?php
/*
Plugin Name: ib-slideshow
Description: Provides a UI to create slideshows
Version: 1.1.0
Author: educatorteam
Author URI: http://educatorplugin.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ib-slideshow
*/

/*
Copyright (C) 2015 http://educatorplugin.com/ - contact@educatorplugin.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

define( 'IB_SLIDESHOW_DIR', plugin_dir_path( __FILE__ ) );
define( 'IB_SLIDESHOW_URL', plugin_dir_url( __FILE__ ) );

register_activation_hook( __FILE__, array( 'IB_Slideshow_Main', 'plugin_activation' ) );
require_once 'includes/ib-slideshow.php';
require_once 'includes/ib-slideshow-main.php';
IB_Slideshow_Main::init();

if ( is_admin() ) {
	require_once 'admin/ib-slideshow-meta.php';
	require_once 'admin/ib-slideshow-admin.php';
	IB_Slideshow_Admin::init();
}
