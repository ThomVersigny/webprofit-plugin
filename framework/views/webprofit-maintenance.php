<?php

if (class_exists('WebProfitMaintenance')) {
    return;
}

class WebProfitMaintenance implements ISettingsAPI
{
    private $settings_api;
    public function __construct()
    {
        $this->settings_api = new SettingsAPI;
        add_action('admin_init', array( $this, 'admin_init' ));
        add_action('admin_menu', array( $this, 'admin_menu' ));
    }
    public function admin_init()
    {
        $this->settings_api->init();

        if (get_option('webprofit_enable_maintenance') == 1) {
            WebProfit::admin_warning('<a href="'.get_admin_url(null, 'admin.php?page=webprofit-maintenance').'">Maintenance mode</a> is geactiveerd.');
        }
    }
    public function admin_menu()
    {
        $this->settings_api->add_sub_page('webprofit', 'Maintenance', 'webprofit-maintenance', array( $this, 'render_page' ));
    }
    public function render_page()
    {
        $this->settings_api->render_page();
    }
}
new WebProfitMaintenance;
