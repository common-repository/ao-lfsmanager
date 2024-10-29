<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 * 
 * @link       https://hana-paena.de/
 * @since      1.0.0
 *
 * @package    pss-lfsmanager
 * @subpackage pss-lfsmanager/includes
 * @author     Jan Wagner (DUDEMICH Consulting) <info@hana-paena.de>
 */

namespace hanapaena\pss_lsfmanager;
class PssLFSManager {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      PssLFSManagerLoader    $oLoader    Maintains and registers all hooks for the plugin.
	 */
	protected $oLoader;
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $sPluginName    The string used to uniquely identify this plugin.
	 */
	protected $sPluginName;
	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $sVersion    The current version of the plugin.
	 */
	protected $sVersion;
	/**
	 * Factory method
	 * @return \hanapaena\pss_lsfmanager\PssLFSManager
	 */
	public static function init()
	{
		return new \hanapaena\pss_lsfmanager\PssLFSManager();
	}
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PSS_LFSMANAGER_VERSION' ) ) {
			$this->sVersion = PSS_LFSMANAGER_VERSION;
		} else {
			$this->sVersion = '1.0.0';
		}
		$this->sPluginName = 'pss-lfsmanager';

		/**
		 * autoloader
		 */
		spl_autoload_register( [ $this, 'loadDependencies' ] );

		/**
		 * Loader for action hooks and footer
		 * Create an instance of the loader which will be used to register the hooks
	 	 * with WordPress.
		 */
		$this->oLoader = new \hanapaena\pss_lsfmanager\Loader();
		$this->setLocale();
		$this->defineAdminHooks();
		$this->definePublicHooks();				
	}
	/**
	 * Autoloader for the required dependencies of the plugin.	
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @param	string 	Name of the Class
	 * @return 	VOID
	 */
	private function loadDependencies( $sClassName ) {
		$aClassNameParts = explode('\\', $sClassName);
		foreach(['includes', 'admin', 'public'] as $sFolder){
			$sFile = '';
			if(!empty($aClassNameParts[2]) && strtolower($aClassNameParts[2]) != $this->sPluginName){
				$sFile = PSS_LFSMANAGER_BD . $sFolder . '/class-' . $this->sPluginName . '-' . strtolower($aClassNameParts[2]) . '.php';
			}
			if(file_exists($sFile))	{
				require_once($sFile);
			}
		}
	}
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the PssLFSManageri18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @return 	VOID
	 */
	private function setLocale() {
		$oPluginI18n = new \hanapaena\pss_lsfmanager\I18n();
		$this->oLoader->addAction( 'plugins_loaded', $oPluginI18n, 'loadPluginTextdomain' );
	}
	/**
	 * Register all of the hooks related to the admin-facing functionality
	 * of the plugin.
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @return 	VOID
	 */
	private function defineAdminHooks() {
        $oPluginAdmin = new \hanapaena\pss_lsfmanager\Admin( $this->getPluginName(), $this->getVersion() );
        $this->oLoader->addAction( 'admin_enqueue_scripts', $oPluginAdmin, 'registerScripts' );   
        $this->oLoader->addAction( 'admin_enqueue_scripts', $oPluginAdmin, 'registerStyles' );
		$this->oLoader->addAction( 'admin_init', $oPluginAdmin, 'registerPssLFSManagerSettings' );
		$this->oLoader->addAction( 'admin_menu', $oPluginAdmin, 'pssLFSManagerPluginSetupMenu', 200 );
		$this->oLoader->addFilter( 'plugin_action_links', $oPluginAdmin, 'pssLFSManagerAddSettingLink', 10, 2 );
		if( !get_option( 'pss_lfsmanager_setting_remove_google_apis' ) ) {
			$this->oLoader->addFilter( 'admin_init', $oPluginAdmin, 'restoreHeaderFiles' );
		}
    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @return 	VOID
	 */
	private function definePublicHooks() {
		$oPluginPublic = new \hanapaena\pss_lsfmanager\Main( $this->getPluginName(), $this->getVersion() );
		if (get_option('pss_lfsmanager_setting_remove_google_apis')) {
			$this->googleFontHooks( $oPluginPublic );
		} 
		else {
			$sCleanStylesJson = PSS_LFSMANAGER_BD . 'public/json/clean-styles.json';
			if ( file_exists( $sCleanStylesJson ) ) {
				$aFiles = json_decode( file_get_contents( $sCleanStylesJson ) );
				foreach ( $aFiles as $sFile ) {
					unlink( $sFile );
				}
				unlink( $sCleanStylesJson );
			}
		}
	}
	/**
	 * All hooks to remove google fonts
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access 	private
	 * @return 	VOID
	 */
	/* private function googleFontHooks( Main|Admin $oPluginArea ) { */
	private function googleFontHooks( $oPluginPublic ) {
		$this->oLoader->addAction( 'wp_enqueue_scripts', $oPluginPublic, 'aoRemoveGoogleFont', PHP_INT_MAX );
		/* Dequeue google fonts when using Avia */
		if (class_exists('avia_style_generator')) {
			add_action('init', function () {
				global $avia;
				$avia->style->print_extra_output = false;
			}, PHP_INT_MAX );
		}
		/* Dequeue google fonts when using Elementor */
		if( in_array( 'elementor/elementor.php', get_option( 'active_plugins' ) ) ) {
			add_filter( 'elementor/frontend/print_google_fonts', '__return_false', PHP_INT_MAX );
		}
		/* Dequeue google fonts when using JupiterX */
		add_filter( 'jupiterx_register_fonts', function ($fonts) {
			return array();
		}, PHP_INT_MAX );
		/* Dequeue google fonts when using Hustle */
		add_filter( 'hustle_load_google_fonts', '__return_false', PHP_INT_MAX );
		add_filter( 'mailpoet_display_custom_fonts', '__return_false', PHP_INT_MAX );
		/* Dequeue google fonts when using Beaver Builder */
		if ( in_array( 'bb-plugin/fl-builder.php', get_option( 'active_plugins' ) ) ) {
			add_filter( 'fl_builder_google_fonts_pre_enqueue', function ( $fonts ) {
				return array();
			}, PHP_INT_MAX );			
		}
	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since  	1.0.0
	 * @access 	public
	 * @return 	VOID
	 */
	public function run() {
		$this->oLoader->run();
	}
	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since  	1.0.0
	 * @access 	public
	 * @return 	string    The name of the plugin.
	 */
	public function getPluginName() {
		return $this->sPluginName;
	}
	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @return 	PssLFSManagerLoader    Orchestrates the hooks of the plugin.
	 */
	public function getLoader() {
		return $this->oLoader;
	}
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  	1.0.0
	 * @access 	public
	 * @return 	string    The version number of the plugin.
	 */
	public function getVersion() {
		return $this->sVersion;
	}

}
