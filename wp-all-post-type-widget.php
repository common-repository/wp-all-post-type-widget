<?php
/*
Plugin Name: WP All Post Type Widget
Description: Display post type and his category base post in your widget
Version: 1.0.4
Author: Nayan Virani
Author URI: https://profiles.wordpress.org/nayanvirani
License: GPLv2 or later
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

define('WPAPTW_URL',plugin_dir_url(plugin_basename(__FILE__)));
define('WPAPTW_DIR', plugin_dir_path(__FILE__));
define('WPAPTW_CLASSES_DIR', plugin_dir_path(__FILE__).'/classes/');
define('WPAPTW_ASSETS_URL', plugin_dir_url(plugin_basename(__FILE__)).'/assets/');
define('WPAPTW_NAME','wpaptwidget');



/**
 * The code that runs during plugin activation.
 */
function activate_wpaptwidget(){
	require_once WPAPTW_CLASSES_DIR . 'class-wpaptw-activator.php';
	WPAPTW_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_wpaptwidget' );

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_wpaptwidget(){
	require_once WPAPTW_CLASSES_DIR . 'class-wpaptw-deactivator.php';
	WPAPTW_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_wpaptwidget' );


/**
 * The core plugin class that is used to define 
 * admin-specific hooks, and public-specific site hooks.
 */
require_once WPAPTW_CLASSES_DIR . 'class-wpaptw.php';

function run_wpaptw() {

	$plugin = new wpaptw();
	$plugin->run();

}
run_wpaptw();