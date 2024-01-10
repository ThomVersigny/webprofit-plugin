<?php

class WebProfitPlugin
{
    public function __construct()
    {
        register_activation_hook(WEBPROFIT_PLUGIN_FILE, array($this, 'activation'));
        register_deactivation_hook(WEBPROFIT_PLUGIN_FILE, array($this, 'deactivation'));
    }
    public function activation()
    {
        $default_plugin_options = array(
            'webprofit_default_settings' => 1,
            'webprofit_enable_shortcodes' => 1,
            'webprofit_company_name' => get_bloginfo( 'name' ),
            'webprofit_menu_x-addons-home' => 'webprofit',
            'webprofit_menu_toolset-dashboard' => 'webprofit',
            'webprofit_menu_wpseo_dashboard' => 'webprofit',
            'webprofit_menu_wpmudev' => 'webprofit',
            'webprofit_menu_wphb' => 'webprofit',
            'webprofit_menu_smush' => 'webprofit',
            'webprofit_menu_wp-defender' => 'webprofit',
            'webprofit_menu_options-general|php' => 'admin',
            'webprofit_menu_tools|php' => 'admin',
            'webprofit_menu_themes|php' => 'admin',
            'webprofit_menu_plugins|php' => 'admin',
            'webprofit_social_facebook' => get_option( 'x_social_facebook' )
        );
        if (!empty(get_option( 'x_social_facebook'))) {
            $default_plugin_options['webprofit_social_facebook'] = get_option( 'x_social_facebook' );
        }
        if (!empty(get_option( 'x_social_twitter'))) {
            $default_plugin_options['webprofit_social_twitter'] = get_option( 'x_social_twitter' );
        }
        if (!empty(get_option( 'x_social_googleplus'))) {
            $default_plugin_options['webprofit_social_google_plus'] = get_option( 'x_social_googleplus' );
        }
        if (!empty(get_option( 'x_social_linkedin'))) {
            $default_plugin_options['webprofit_social_linkedin'] = get_option( 'x_social_linkedin' );
        }
        if (!empty(get_option( 'x_social_youtube'))) {
            $default_plugin_options['webprofit_social_youtube'] = get_option( 'x_social_youtube' );
        }
        if (is_plugin_active('gravityforms/gravityforms.php')) {
            $default_plugin_options['webprofit_gravity_forms_hide_label'] = 1;
        }
        if (Webprofit::is_pro() || WebProfit::is_x()) {
            $default_plugin_options['webprofit_cornerstone_global_colors'] = 1;
            $default_plugin_options['webprofit_cornerstone_views_integration'] = 1;
        }
        foreach($default_plugin_options as $option=>$value) {
            // dont overwrite existing options
            if (!get_option($option)) {
                update_option($option, $value);
            }
        }
        $this->send_plugin_activation_mail(true);

        // temporary bugfix for plugins <= 2.0.2 remove later
        $options = wp_load_alloptions();
        foreach ( $options as $option => $value ) {
            $prefix = 'webprofit_webprofit_';
            if (substr($option, 0, strlen($prefix)) === $prefix) {
                add_option( str_replace($prefix, 'webprofit_', $option), $value );
                delete_option( $option );
            }
        }
    }
    public function deactivation()
    {
        $this->send_plugin_activation_mail(false);
    }
    private function send_plugin_activation_mail($activated = true)
    {
        $to = 'wordpress@webprofit.nl';
        if ($activated) {
            $subject = 'WebProfit Plug-in is inschakeled';
            $message = 'WebProfit Plug-in is inschakeled op <a href="' . get_site_url() . '">'.get_bloginfo('name').'</a>';
        } else {
            $subject = 'WebProfit Plug-in uitgeschakeld';
            $message = 'WebProfit Plug-in is uitgeschakeld op <a href="' . get_site_url() . '">'.get_bloginfo('name').'</a>';
        }
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($to, $subject, $message, $headers);
    }
}
new WebProfitPlugin;
