<?php
$iVersionNum = str_replace('.', '', get_option('pss_lfsmanager_current_version'));
if($iVersionNum < 200) {
    add_option( 'pss_lfsmanager_current_version', PSS_LFSMANAGER_VERSION );
	add_option( 'pss_lfsmanager_setting_remove_google_apis', get_option('ao_lfsmanager_setting_remove_google_apis') );
    delete_option( 'ao_lfsmanager_current_version' );
	delete_option( 'ao_lfsmanager_setting_remove_google_apis' );
}
/**
 * Update the version number after update
 */
update_option('pss_lfsmanager_current_version', PSS_LFSMANAGER_VERSION);