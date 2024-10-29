$ = jQuery;
(function ($) {
  "use strict";
  /* Enable or disable checkbox style cache */
  $(document).on('change','#pss_lfsmanager_setting_enable_unite_styles',function() {
    if ( $( '#pss_lfsmanager_setting_enable_unite_styles' ).is( ':checked' ) === true ) {
        $( '#pss_lfsmanager_setting_enable_cache_styles' ).removeAttr( 'disabled' );
    }
    else {
        $( '#pss_lfsmanager_setting_enable_cache_styles' ).removeAttr( 'checked' );
        $( '#pss_lfsmanager_setting_enable_cache_styles' ).attr( 'disabled', true );
    }   
  });
  /* Enable or disable checkbox style cache */
  $(document).on('change','.pss-plugin-setting-switch.pss-plugin-switch-strict-mode',function() {
    if ( $( '#pss_lfsmanager_setting_remove_google_apis' ).is( ':checked' ) === true ) {
      $( '#pss_lfsmanager_setting_remove_google_apis_strict' ).removeAttr( 'disabled' );
    }
    else {
      $( '#pss_lfsmanager_setting_remove_google_apis_strict' ).removeAttr( 'checked' );
      $( '#pss_lfsmanager_setting_remove_google_apis_strict' ).attr( 'disabled', true );
    }
  });
  /* Display error on input field */
  $(document).on('focus','.pss-plugin-input-error',function(e) {
    $(this).removeClass('pss-plugin-input-error');
  });
})(jQuery);