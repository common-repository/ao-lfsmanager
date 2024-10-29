<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * 
 * @link       https://hana-paena.de/
 * @since      1.0.0
 *
 * @package    pss-lfsmanager
 * @subpackage pss-lfsmanager/Main
 * @author     Jan Wagner (DUDEMICH Consulting) <info@hana-paena.de>
 */

namespace hanapaena\pss_lsfmanager;

use WP_Error;

class Main
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $sPluginName    The ID of this plugin.
	 */
	private $sPluginName;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $sVersion    The current version of this plugin.
	 */
	private $sVersion;

	/**
	 * The instance of the class AoCombineStyles.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $oCombineStyle    The current instance of the class AoCombineStyles.
	 */
	private $oCombineStyle;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $sPluginName       The name of the plugin.
	 * @param      string    $sVersion    The version of this plugin.
	 */
	public function __construct(string $sPluginName, string $sVersion)
	{
		$this->sPluginName = $sPluginName;
		$this->sVersion = $sVersion;
	}
	/**
	 * Register the Styles for the public area.
	 *
	 * @since    1.0.0
	 * @access 	public
	 * @return VOID
	 */
	public function registerStyles()
	{
		// if (is_multisite()) {
		// 	wp_register_style('pss-lfsmanager-style', PSS_LFSMANAGER_URL . 'public/style/pss-generated.min.' . get_current_blog_id() . '.css', [], $this->sVersion, 'all');
		// } else {
		// 	wp_register_style('pss-lfsmanager-style', PSS_LFSMANAGER_URL . 'public/style/pss-generated.min.css', [], $this->sVersion, 'all');
		// }
		wp_register_style('pss-lfsmanager-style', PSS_LFSMANAGER_URL . 'public/style/pss-generated.min.css', [], $this->sVersion, 'all');
	}
	/**
	 * Combine styles
	 *
	 * @since    1.0.0
	 * @access 	public
	 * @return 	VOID
	 */
	public function aoUniteCss()
	{
		if (is_user_logged_in() && current_user_can('manage_options')) {
			return;
		}
		require(PSS_LFSMANAGER_BD . 'includes/class-pss-lfsmanager-combine-styles.php');
		$this->oCombineStyle = new \hanapaena\pss_lsfmanager\AoCombineStyles();
		$sFile = PSS_LFSMANAGER_BD . 'public/style/pss-generated.min.css';
		$sExistingStylesJson = PSS_LFSMANAGER_BD . 'public/json/existing-styles.json';
		$aExcludes = is_array(get_option('pss_lfsmanager_setting_exclude_list')) ? get_option('pss_lfsmanager_setting_exclude_list') : [];
		if (get_option('pss_lfsmanager_setting_enable_cache_styles') && file_exists($sFile) && file_exists($sExistingStylesJson)) {
			if (isset($_GET['regen_css']) || isset($_POST['regen_css'])) {
				$this->oCombineStyle->aoGenerateCss($sExistingStylesJson, $sFile, dirname(dirname(dirname(__DIR__))), $aExcludes, get_option('pss_lfsmanager_setting_remove_google_apis'));
			} else {
				$this->oCombineStyle->checkForNotExistingStyles($sExistingStylesJson, $sFile, dirname(dirname(dirname(__DIR__))), $aExcludes, get_option('pss_lfsmanager_setting_remove_google_apis'));
			}
		} else {
			$this->oCombineStyle->aoGenerateCss($sExistingStylesJson, $sFile, dirname(dirname(dirname(__DIR__))), $aExcludes, get_option('pss_lfsmanager_setting_remove_google_apis'));
		}
		$oCombineStyle = $this->oCombineStyle;
		$oCombineStyle->aoDeregisterStyles();
		wp_enqueue_style('pss-lfsmanager-style');
	}
	/**
	 * Remove Google Fonts
	 *
	 * @since    1.0.0
	 * @access 	public
	 * @return 	VOID
	 */
	public function aoRemoveGoogleFont()
	{
		if (is_user_logged_in() && current_user_can('manage_options')) {
			return;
		}
		/* Revolution Slider */
		remove_action('wp_footer', array('RevSliderFront', 'load_google_fonts'));
		/* Jupiter theme */
		wp_dequeue_script('mk-webfontloader');
		/* Divi theme */
		remove_action('wp_footer', 'et_builder_print_font');
		/* aThemes */
		remove_action('wp_head', 'sydney_preconnect_google_fonts');
		remove_action('wp_head', 'botiga_preconnect_google_fonts');
		/* Appointment Green */
		remove_action('wp_head', 'appointment_load_google_font');
		/* Hueman Theme */
		remove_action('wp_head', 'hu_print_gfont_head_link', 2);
		/* Filter Styles and dequeue external google fonts */
		global $wp_styles;
		$aCleanStyles = [];
		foreach ($wp_styles->registered as $oStyle) {
			$sHandle = $oStyle->handle;
			$sUrl    = $oStyle->src;
			foreach ($oStyle->deps as $sDep) {
				if ((strpos($sDep, 'google-fonts') !== false) || (strpos($sDep, 'google_fonts') !== false) || (strpos($sDep, 'googlefonts') !== false)) {
					$wp_styles->remove($sDep);
					$wp_styles->add($sDep, '');
				}
			}
			if (
				strpos($sUrl, 'fonts.googleapis') !== false
				|| strpos($sUrl, 'fonts.gstatic.com') !== false
				|| strpos($sHandle, 'google-fonts') !== false
				|| strpos($sHandle, 'fonts-google') !== false
				|| strpos($sHandle, 'google_fonts') !== false
				|| strpos($sHandle, 'googlefonts') !== false
			) {
				wp_deregister_style($sHandle);
				wp_dequeue_style($sHandle);
			} elseif (!get_option('pss_lfsmanager_setting_enable_unite_styles')) {
				if (stripos($oStyle->src, get_site_url()) !== false) {
					$sFile = substr(str_replace(get_site_url(), '', $sUrl), 1);
					$aPathParts = explode(DIRECTORY_SEPARATOR, $sFile);
					$aPathParts[count($aPathParts) - 1] = 'clean.' . $sHandle . '.css';
					$sNewFile = implode(DIRECTORY_SEPARATOR, $aPathParts);
					if (!file_exists($sFile)) {
						continue;
					}
					if ($sStyleContent = file_get_contents($sFile)) {
						$sPattern = '~@.*(?=\bfonts\.gstatic\.com\b).*?;|@.*(?=\bfonts\.googleapis\.com\b).*?;~m';
						preg_match_all($sPattern, $sStyleContent, $aMatches, PREG_SET_ORDER, 0);
						if (!empty($aMatches)) {
							if ((!is_writable($sFile) && !touch($sFile))) {
								continue;
							}
							if (file_exists($sNewFile)) {
								unlink($sNewFile);
							}
							$sCleanedString = preg_replace($sPattern, '', $sStyleContent);
							file_put_contents($sNewFile, $sCleanedString);
							wp_deregister_style($sHandle);
							wp_dequeue_style($sHandle);
							wp_enqueue_style($sHandle, get_site_url() . '/' . $sNewFile);
							$aCleanStyles[] = ABSPATH . DIRECTORY_SEPARATOR . $sNewFile;
						}
					}
				}
			}
		}
		if (!empty($aCleanStyles)) {
			$sCleanStylesJson = PSS_LFSMANAGER_BD . 'public/json/clean-styles.json';
			if (file_exists($sCleanStylesJson)) {
				unlink($sCleanStylesJson);
			}
			file_put_contents($sCleanStylesJson, json_encode($aCleanStyles));
		}
		/* Filter Scripts and dequeue external google fonts */
		global $wp_scripts;
		foreach ($wp_scripts->queue as $oScript) {
			$sUrl = $wp_scripts->registered[$oScript]->src;
			$sHandle = $wp_scripts->registered[$oScript]->handle;
			if (strpos($sUrl, 'fonts.googleapis') !== false || strpos($sUrl, 'fonts.gstatic') !== false) {
				wp_deregister_script($sHandle);
				wp_dequeue_script($sHandle);
			}
		}
	}
}
