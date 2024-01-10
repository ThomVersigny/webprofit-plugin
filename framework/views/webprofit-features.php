<?php

class WebProfitViewFeatures implements ISettingsAPI
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
    }
    public function admin_menu()
    {
        $this->settings_api->add_sub_page('webprofit', 'Instellingen', 'webprofit-settings', array( $this, 'render_page' ));
    }
    public function render_page()
    {
        $this->settings_api->render_page();
    }
}
new WebProfitViewFeatures;
