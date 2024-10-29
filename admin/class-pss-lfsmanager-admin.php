<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://hana-paena.de/
 * @since      1.0.0
 *
 * @package    pss-lfsmanager
 * @subpackage pss-lfsmanager/admin
 * @author     Dennis Piepiorra / Jan Wagner (DUDEMICH Consulting) <info@hana-paena.de>
 * 
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */

namespace hanapaena\pss_lsfmanager;

class Admin
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
     * The name of the option for the settings page of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $sOptionName    The settings page name of this plugin.
     */
    private $sOptionName;

    /**
     * List of all header files in active theme.
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $aHeaderFiles    List of all header files
     */
    private $aHeaderFiles;

    /**
     * List of all footer files in active theme.
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $aFooterFiles    List of all footer files
     */
    private $aFooterFiles;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @access 	public
     * @param      string    $sPluginName       The name of this plugin.
     * @param      string    $sVersion    The version of this plugin.
     * 
     * @return VOID
     */
    public function __construct($sPluginName, $sVersion)
    {

        $this->sPluginName = $sPluginName;
        $this->sVersion = $sVersion;
        $this->sOptionName = 'pss_lfsmanager_setting';
        $this->aHeaderFiles = [];
        $this->aFooterFiles = [];
        if (get_stylesheet_directory() !== get_template_directory()) {
            $sBaseDir = get_stylesheet_directory() . DIRECTORY_SEPARATOR;
            $this->findFiles($sBaseDir, 'all');
            if (empty($this->aHeaderFiles)) {
                $sBaseDir = get_template_directory() . DIRECTORY_SEPARATOR;
                $this->findFiles($sBaseDir, 'header');
                if (empty($this->aHeaderFiles)) {
                    $sBaseFile = ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'theme-compat' . DIRECTORY_SEPARATOR  . 'header.php';
                    $sThemeFile = get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'header.php';
                    $this->copyFile($sBaseFile, $sThemeFile);
                }
            }
            if (empty($this->aFooterFiles)) {
                $sBaseDir = get_template_directory() . DIRECTORY_SEPARATOR;
                $this->findFiles($sBaseDir, 'footer');
                if (empty($this->aFooterFiles)) {
                    $sBaseFile = ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'theme-compat' . DIRECTORY_SEPARATOR  . 'footer.php';
                    $sThemeFile = get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'footer.php';
                    $this->copyFile($sBaseFile, $sThemeFile);
                }
            }
        } else {
            $sBaseDir = get_template_directory() . DIRECTORY_SEPARATOR;
            $this->findFiles($sBaseDir, 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     * @access 	public
     * @return VOID
     */
    public function registerScripts()
    {
        wp_enqueue_script($this->sPluginName, plugin_dir_url(__FILE__) . 'js/pss-lfsmanager-admin.js', ['jquery'], $this->sVersion, true);
    }
    /**
     * Register the Styles for the admin area.
     *
     * @since    1.0.0
     * @access 	public
     * @return VOID
     */
    public function registerStyles()
    {
        /* Load global aohipa plugin styles once */
        wp_register_style('pss-lfsmanager-admin-global-styles', PSS_LFSMANAGER_URL . 'admin/style/pss-plugin-admin.css', [], $this->sVersion, 'all');
    }
    /**
     * Register the setting parameters
     *
     * @since  	1.0.0
     * @access 	public
     * @return VOID
     */
    public function registerPssLFSManagerSettings()
    {
        // Add a General section
        add_settings_section(
            $this->sOptionName . '_general',
            '',
            [$this, 'settingGeneralText'],
            $this->sPluginName
        );
        // Add a activate activate remove google apis
        add_settings_field(
            $this->sOptionName . '_remove_google_apis',
            __('Remove all the font imports from google.', 'pss-lfsmanager'),
            [$this, 'settingActivateRemoveGoogle'],
            $this->sPluginName,
            $this->sOptionName . '_general'
        );
        // Add a description section
        add_settings_section(
            $this->sOptionName . '_description',
            __('Description', 'pss-lfsmanager'),
            [$this, 'settingDescriptionText'],
            $this->sPluginName
        );        
        register_setting($this->sPluginName, $this->sOptionName . '_remove_google_apis');
        // Register the activate remove google apis strict section
    }
    /**
     * Render the text for the general section
     *
     * @since  	1.0.0
     * @access 	public
     * @return VOID
     */
    public function settingGeneralText()
    {
        echo '<p>' . __('In a secure future!.', 'pss-lfsmanager') . '</p>';
        echo '<p>' . __('Make your website DSGVO compliant by preventing external google fonts from being loaded.', 'pss-lfsmanager') . '</p>';
        echo '<p>' . __('As long as you are logged in, all fonts will be loaded as usual.', 'pss-lfsmanager') . '</p>';
        if (get_option($this->sOptionName . '_remove_google_apis')) {
            $aFontsInFiles = $this->findFontsInFiles();
            if (!empty($aFontsInFiles)) {
                echo '<p class="pss-plugin-message pss-plugin-error">' . __('You need to remove some lines from your header files:', 'pss-lfsmanager') . '</p>';
                foreach ($aFontsInFiles as $sFile => $aLines) {
                    echo '<h4>' . __('File', 'pss-lfsmanager') . ': ' .  esc_attr($sFile) . '</h4><div class="pss-links-to-remove">';
                    foreach ($aLines as $aLine) {
                        echo '<p>' . esc_html(htmlentities($aLine)) . '</p>';
                    }
                    echo '</div>';
                }
            }
        }
        echo '<p>' . __('If Google Fonts are still loading, please send a support mail and add the name of your current theme and the plugin version.', 'pss-lfsmanager') . '</p>';
        echo '<a class="pss-plugin-btn" href="mailto:web-support@hana-paena.de">' . __('Send Email', 'pss-lfsmanager') . '</a>';
    }
    /**
     * Render the switch to activate the google fonts removing
     *
     * @since  1.0.0
     * @access public
     * @return VOID
     */
    public function settingActivateRemoveGoogle()
    {
        $bOption = get_option($this->sOptionName . '_remove_google_apis');
        $sChecked = $bOption ? 'checked' : '';
        echo '<label class="pss-plugin-setting-switch pss-plugin-switch-strict-mode">';
        echo '<input type="checkbox" name="' . esc_attr($this->sOptionName) . '_remove_google_apis" id="' . esc_attr($this->sOptionName) . '_remove_google_apis" ' . esc_attr($sChecked) . ' />';
        echo '<span class="pss-plugin-setting-slider"></span>';
        echo '</label>';
    }
    /**
     * Render the text for the description section
     *
     * @since  	1.0.0
     * @access 	public
     * @return VOID
     */
    public function settingDescriptionText()
    {
        echo '<p>' . __('The Lite - Font & Style Manager plugin is the perfect solution for anyone who wants to escape the current wave of warnings regarding Google fonts!', 'pss-lfsmanager') . '</p>';
        echo '<p>' . __('The plugin automatically removes the Google Fonts and replaces them with fonts that are integrated in the theme or alternatively locally.', 'pss-lfsmanager') . '</p>';
        echo '<p>' . __('Would you also like to improve your SEO and the performance of your website?', 'pss-lfsmanager') . '</p>';
        echo '<p>' . __('Then take a look at the full version of the Font & Style Manager.', 'pss-lfsmanager') . '</p>';
        echo '<a class="pss-plugin-btn" href="https://hana-paena.de/font-style-manager/">hanapaena\'s Font & Style Manager</a>';
    }
    /**
     * Include the jobs setting page
     *
     * @since  1.0.0
     * @access private
     * @return VOID/FALSE
     */
    public function pssLFSManagerInit()
    {
        // Just implement if user grant access
        if (!current_user_can('manage_options')) {
            return;
        }
        include PSS_LFSMANAGER_BD . 'admin/partials/pss-lfsmanager-admin-display.php';
    }
    /**
     * Include the jobs menu link
     *
     * @since  1.0.0
     * @access public
     * @return VOID
     */
    public function pssLFSManagerPluginSetupMenu()
    {
        if(defined('STD_PREFIX')) {
            add_submenu_page(STD_PREFIX . '.edit', __('Lite - Font & Style Manager - DSGVO/GDPR', 'pss-lfsmanager'), __('Font & Style', 'pss-lfsmanager'), 'manage_options', 'lite-font-style-manager-settings', array($this, 'pssLFSManagerInit'));
        }
        else {
            $sIconBas64 = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(PSS_LFSMANAGER_BD . 'admin/icons/main-icon.svg'));
            add_menu_page(__('Lite - Font & Style Manager - DSGVO/GDPR', 'pss-lfsmanager'), __('Font & Style', 'pss-lfsmanager'), 'manage_options', 'lite-font-style-manager-settings', array($this, 'pssLFSManagerInit'), $sIconBas64);
        }
    }

    /**
     * Include the jobs setting link to plugin page
     *
     * @since  1.0.0
     * @access public
     * @param array     $aLinks     Array of the plugin action links
     * @param string    $sSlug      Slug of the plugin
     * @return VOID
     */
    public function pssLFSManagerAddSettingLink($aLinks, $sSlug)
    {
        if ($sSlug === PSS_LFSMANAGER_SLUG && current_user_can('manage_options')) {
            // Build and escape the URL.
            $sUrl = admin_url('admin.php?page=lite-font-style-manager-settings');
            // Create the link.
            $aLinks[] = sprintf('<a href="%s">%s</a>', $sUrl, __('Settings', 'pss-lfsmanager'));
        }
        return $aLinks;
    }
    /**
     * Find google fonts in header files
     *
     * @since  1.0.0
     * @access public
     * @return array    List of files including all found lines to remove
     */
    private function findFontsInFiles(): array
    {
        $sRemovedLinksJson = PSS_LFSMANAGER_BD . 'admin/json/removed-links.json';
        if (!is_dir(PSS_LFSMANAGER_BD . 'admin/json')) {
            if (!mkdir(PSS_LFSMANAGER_BD . 'admin/json')) {
                return [];
            }
        }
        if (is_file($sRemovedLinksJson)) {
            $aBackUp = json_decode(file_get_contents($sRemovedLinksJson), true);
        } else {
            $aBackUp = [];
        }
        if (!is_writable($sRemovedLinksJson) && !touch($sRemovedLinksJson)) {
            return [];
        }
        unlink($sRemovedLinksJson);
        $aAllFiles = array_merge($this->aHeaderFiles, $this->aFooterFiles);
        $aResult = [];
        foreach ($aAllFiles as $sFile) {
            if ($sStyleContent = file_get_contents($sFile)) {
                $sPattern = '~<link.*((?=\bfonts\.gstatic\b)|(?=\bfonts\.googleapis\b)).*?>~m';
                preg_match_all($sPattern, $sStyleContent, $aMatches, PREG_SET_ORDER, 0);
                if (!empty($aMatches)) {
                    if ((is_writable($sFile))) {
                        unlink($sFile);
                        if (!array_key_exists($sFile, $aBackUp)) {
                            $aBackUp[$sFile] = [];
                        }
                        $iCount = count($aBackUp[$sFile]);
                        foreach ($aMatches as $iKey => $aMatch) {
                            $sComment = '<!-- This is link no. ' . ($iKey + $iCount) . ' and its removed by aohipa Font & Style Manager Plugin. please do not remove this comment -->';
                            $aBackUp[$sFile][] = [
                                $aMatch[0],
                                $sComment
                            ];
                            $sStyleContent = str_replace($aMatch[0], $sComment, $sStyleContent);
                        }
                        file_put_contents($sFile, $sStyleContent);
                    } else {
                        foreach ($aMatches as $iKey => $aMatch) {
                            $aResult[$sFile] = $aMatch[0];
                        }
                    }
                }
            }
        }
        file_put_contents($sRemovedLinksJson, json_encode($aBackUp));
        return $aResult;
    }
    /**
     * Find header files
     *
     * @since   1.0.0
     * @access  private
     * @param 	string $sBaseDir
     * @param 	string $sType   all | header | footer
     * @return 	VOID
     */
    private function findFiles(string $sBaseDir, string $sType)
    {
        $sPatternHeader = '~.*(?=\bheader\b).*?\.php~';
        $sPatternFooter = '~.*(?=\bfooter\b).*?\.php~';
        if (is_dir($sBaseDir)) {
            foreach (scandir($sBaseDir) as $sFile) {
                if ($sFile != '.' && $sFile != '..') {
                    if (is_dir($sBaseDir . $sFile)) {
                        $this->findFiles($sBaseDir . $sFile . DIRECTORY_SEPARATOR, $sType);
                    } elseif ((preg_match($sPatternHeader, $sFile)) && ($sType === 'all' || $sType === 'header')) {
                        $this->aHeaderFiles[] = $sBaseDir . $sFile;
                    } elseif ((preg_match($sPatternFooter, $sFile)) && ($sType === 'all' || $sType === 'footer')) {
                        $this->aFooterFiles[] = $sBaseDir . $sFile;
                    }
                }
            }
        }
    }
    /**
     * Copy file from wp-includes to theme by name 
     *
     * @since   1.0.0
     * @access  private
     * @param 	string $sFile
     * @param 	string $sDestination
     * @return 	bool
     */
    private function copyFile(string $sFile, string $sDestination): bool
    {
        if (copy($sFile, $sDestination)) {
            return true;
        }
        return false;
    }
    /**
     * Undo all changes in files
     *
     * @since   1.0.0
     * @access 	public
     * @return 	VOID
     */
    public function restoreHeaderFiles()
    {
        $sRemovedLinksJson = PSS_LFSMANAGER_BD . 'admin/json/removed-links.json';
        if (file_exists($sRemovedLinksJson)) {
            $aRemovedLinkFiles = json_decode(file_get_contents($sRemovedLinksJson), true);
            if (!empty($aRemovedLinkFiles)) {
                foreach ($aRemovedLinkFiles as $sFile => $aChanges) {
                    $sContent = file_get_contents($sFile);
                    unlink($sFile);
                    foreach ($aChanges as $aChange) {
                        $sContent = str_replace($aChange[1], $aChange[0], $sContent);
                    }
                    file_put_contents($sFile, $sContent);
                }
            }
            unlink($sRemovedLinksJson);
        }
    }
}
