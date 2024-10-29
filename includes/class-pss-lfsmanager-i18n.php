<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://hana-paena.de/
 * @since      1.0.0
 *
 * @package    pss-lfsmanager
 * @subpackage pss-lfsmanager/includes
 * @author     Jan Wagner (DUDEMICH Consulting) <info@hana-paena.de>
 */

namespace hanapaena\pss_lsfmanager;

class I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 * @access 	 public
	 * @return 	 VOID
	 */
	public function loadPluginTextdomain() {
		load_plugin_textdomain(
			'pss-lfsmanager',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
