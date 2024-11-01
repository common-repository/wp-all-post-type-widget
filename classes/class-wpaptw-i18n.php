<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 */

class WPAPTW_i18n{

	public function wpaptw_load_plugin_textdomain() {
		load_plugin_textdomain(WPAPTW_NAME,false,WPAPTW_DIR . 'languages/');
	}
	
}