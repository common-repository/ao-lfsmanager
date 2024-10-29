<?php

/**
 * @link              https://hana-paena.de/
 * @since             2.0.0
 * @package           PssLFSManager
 *
 * @wordpress-plugin
 * Plugin Name:       hanapaena's Lite - Font & Style Manager - DSGVO/GDPR
 * Plugin URI:        https://hana-paena.de/plugins/
 * Description:       Remove external google fonts
 * Version:           2.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Jan Wagner (hanapaena) <info@hana-paena.de>
 * Author URI:        https://hana-paena.de/ 						
 * License: 		  GPLv2 or later
 * License URI: 	  http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pss-lfsmanager
 * Domain Path:       /languages
 *
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
/**
 * Currently plugin version.
 */
define('PSS_LFSMANAGER_VERSION', '2.1');

/**
 * Baseurl
 */
define('PSS_LFSMANAGER_URL', plugin_dir_url(__FILE__));

/**
 * Basedir
 */
define('PSS_LFSMANAGER_BD', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
 * Slug
 */
define('PSS_LFSMANAGER_SLUG', plugin_basename(__FILE__));


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pss-lfsmanager-activator.php
 */
function activatePssLFSManager()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-pss-lfsmanager-activator.php';
	\hanapaena\pss_lsfmanager\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pss-lfsmanager-deactivator.php
 */
function deactivatePssLFSManager()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-pss-lfsmanager-deactivator.php';
	\hanapaena\pss_lsfmanager\Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activatePssLFSManager');
register_deactivation_hook(__FILE__, 'deactivatePssLFSManager');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-pss-lfsmanager.php';
/**
 * 
 * Some Hooks for apollo13 and hemmingway
 * 
 */
if (get_option('pss_lfsmanager_setting_remove_google_apis')) {
	if ( ! function_exists( 'apollo13framework_get_web_fonts_dynamic' ) ) {
		/**
		 * Dequeue Google Fonts loaded by the Apollo13 Themes Framework.
		 */
		function apollo13framework_get_web_fonts_dynamic() {
			return;
		}
	}
	if ( ! function_exists( 'apollo13framework_get_web_fonts_static' ) ) {
		/**
		 * Dequeue Google Fonts loaded by the Apollo13 Themes Framework.
		 */
		function apollo13framework_get_web_fonts_static() {
			return;
		}
	}
	if ( ! function_exists( 'hemingway_get_google_fonts_url' ) ) {
		/**
		 * Dequeue Google Fonts loaded by the Hemingway theme.
		 */
		function hemingway_get_google_fonts_url() {
			return false;
		}
	}
}
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
\hanapaena\pss_lsfmanager\PssLFSManager::init()->run();
