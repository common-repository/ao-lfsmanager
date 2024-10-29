<?php

/**
 * Fired during plugin activation
 *
 * @link       https://hana-paena.de/
 * @since      1.0.0
 *
 * @package    pss-lfsmanager
 * @subpackage pss-lfsmanager/includes
 * @author     Jan Wagner (DUDEMICH Consulting) <info@hana-paena.de>
 * 
 * This class defines all code necessary to run during the plugin's activation.
 */

namespace hanapaena\pss_lsfmanager;

class Activator {
	/**
	 * Add the necessary options for this plugin and the currencies. Check for constants
	 *
	 * @since    1.0.0
	 * @access 	public
	 * @return void
	 */
	public static function activate() {
		add_option( 'pss_lfsmanager_current_version', PSS_LFSMANAGER_VERSION );
		add_option( 'pss_lfsmanager_setting_remove_google_apis', false );
	}
}
