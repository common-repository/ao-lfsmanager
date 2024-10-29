<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://hana-paena.de/
 * @since      1.0.0
 *
 * @package    pss-lfsmanager
 * @subpackage pss-lfsmanager/includes
 * @author     Jan Wagner (DUDEMICH Consulting) <info@hana-paena.de>
 * 
 * This class defines all code necessary to run during the plugin's deactivation.
 */

namespace hanapaena\pss_lsfmanager;

class Deactivator {
	/**	
	 * Delete the necessary options for this plugin and all post type data from db
	 *
	 * @since    1.0.0
	 * @access 	public
	 * @return void
	 */
	public static function deactivate() {
		$sRemovedLinksJson = PSS_LFSMANAGER_BD.'admin/json/removed-links.json';
		if( file_exists( $sRemovedLinksJson ) ) {
            $aRemovedLinkFiles = json_decode( file_get_contents( $sRemovedLinksJson ) );
            if( !empty( $aRemovedLinkFiles ) ) {
                foreach ($aRemovedLinkFiles as $sFile => $aChanges) {
                    $sContent = file_get_contents( $sFile );
                    unlink( $sFile );
                    foreach ($aChanges as $aChange) {
                        $sContent = str_replace( $aChange[1], $aChange[0], $sContent );
                    }
                    file_put_contents( $sFile, $sContent );
                }
            }
			unlink( $sRemovedLinksJson );
		}
		$sCleanStylesJson = PSS_LFSMANAGER_BD.'public/json/clean-styles.json';
		if( file_exists($sCleanStylesJson) ) {
			$aFiles = json_decode( file_get_contents( $sCleanStylesJson ) );
			foreach ( $aFiles as $sFile ) {
				unlink( $sFile );
			}
			unlink( $sCleanStylesJson );
		}
		delete_option( 'pss_lfsmanager_current_version' );
		delete_option( 'pss_lfsmanager_setting_remove_google_apis' );		
	}
}
