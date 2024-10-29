<?php
wp_enqueue_style('pss-lfsmanager-admin-global-styles');
?>
<div id="pss-plugin-admin-area" class="wrap">
    <div class="pss-plugin-slogan-wrapper">
        <div class="pss-plugin-slogan">
            <a href="https://hana-paena.de">
                <img src="<?php echo esc_url(PSS_LFSMANAGER_URL); ?>admin/icons/main-icon.svg" alt="Plugin Logo" title="Plugin Logo" />
            </a>
            <span>Secure your website with hanapaena<br>Vertraue den Profis.</span>
        </div>
    </div>
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form action="options.php" method="post">
        <?php
            settings_errors();
            settings_fields($this->sPluginName);
            do_settings_sections($this->sPluginName);
            submit_button(__('Save all changes', 'pss-lfsmanager'),'pss-plugin-btn');
        ?>
    </form>
    <div class="pss-hanapaena-banner-wrapper">
        <div class="pss-hanapaena-banner">
            <a href="https://hana-paena.de">
                <img src="<?php echo esc_url(PSS_LFSMANAGER_URL); ?>admin/icons/hanapaena-cut.png" alt="hanapaena Logo" title="hanapaena Logo" />
            </a>
        </div>
        <div>Unleash Your Reach with hanapaena - Mehr als nur eine Webseite.</div>
    </div>
</div> 