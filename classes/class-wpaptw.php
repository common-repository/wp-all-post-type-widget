<?php

/**
 * The file that defines the core plugin class
 */

class wpaptw{
	
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 */	
	protected $loader;

	public function __construct(){

		$this->wpaptw_load_dependencies();
		$this->wpaptw_set_locale();
		$this->wpaptw_load();
		$this->wpaptw_load_ajax_hooks();

	}

	/**
	 *	Load all dependencies for localization, admin and public classes
	 */
	private function wpaptw_load_dependencies(){

		require_once( WPAPTW_CLASSES_DIR . 'class-wpaptw-loader.php' );
		require_once( WPAPTW_CLASSES_DIR . 'class-wpaptw-i18n.php');
		require_once( WPAPTW_CLASSES_DIR . 'class-widget.php');
		$this->loader = new WPAPTW_Loader();
	}

	/**
	 *	Set internationalization for plugin
	 */

	private function wpaptw_set_locale() {
		$plugin_i18n = new WPAPTW_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'wpaptw_load_plugin_textdomain' );
	}


	/**
	 * Load widget hooks
	 */
	public function wpaptw_load(){
		$this->loader->add_action( 'widgets_init',$this,'wpaptw_widget_init',10);
		$wpaptw_widget= new WPAPT_Widget();

	}


	/**
	 * Initialize widget
	 */
	public function wpaptw_widget_init(){
		register_widget( 'WPAPT_Widget' );
	}
	
	/**
	 *	Load ajax hooks
	 */

	public function wpaptw_load_ajax_hooks(){
            
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class the hooks with the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

}