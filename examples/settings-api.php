<?php

if (class_exists('TestSettingsApi')) {
    return;
}

class TestSettingsApi implements ISettingsAPI
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
        $this->settings_api->set_tabs($this->get_tabs());
        $this->settings_api->set_sections($this->get_sections());
        $this->settings_api->set_fields($this->get_fields());
        $this->settings_api->init();
    }
    public function admin_menu()
    {
        $this->settings_api->add_top_page('Settings API', 'example-settingsapi', array( $this, 'render_page' ));
    }
    public function get_tabs()
    {
        $tabs = include WEBPROFIT_PLUGIN_DIR . 'data/tabs.php';
        return $tabs;
    }
    public function get_sections()
    {
        $sections = include WEBPROFIT_PLUGIN_DIR . 'data/sections.php';
        return $sections;
    }
    public function get_fields()
    {
        $fields = include WEBPROFIT_PLUGIN_DIR . 'data/fields.php';
        return $fields;
    }
    public function render_page()
    {
        $this->settings_api->render_page();
    }
}
new TestSettingsApi;
