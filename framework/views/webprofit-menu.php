<?php

if (class_exists('WebProfitMenu')) {
    return;
}

class WebProfitMenu implements ISettingsAPI
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
        $this->settings_api->set_sections($this->get_sections());
        $this->settings_api->set_fields($this->get_fields());
        $this->settings_api->init();
        $this->remove_menu_pages();
    }

    public function admin_menu()
    {
        $this->settings_api->add_sub_page('webprofit', 'Menu', 'webprofit-menu', array( $this, 'render_page' ));
    }

    public function get_sections()
    {
        $sections = array(
            array(
                'name' => 'Menu',
                'description' => 'Selecteer per menu-item voor wie deze zichtbaar is',
                'slug' => 'menu',
                'page' => 'webprofit-menu',
            ),
        );
        return $sections;
    }
    
    public function get_fields()
    {
        $fields = array('menu' => array());

        if (isset($GLOBALS['menu'])) {
            $menus = $GLOBALS[ 'menu' ];
        }

        if (empty($menus)) {
            return;
        }

        foreach ($menus as $menu) {
            $title = isset($menu[0]) ? $menu[0] : '';
            $capability = isset($menu[1]) ? $menu[1] : '';
            $url = isset($menu[2]) ? $menu[2] : '';
            $idk = isset($menu[3]) ? $menu[3] : '';
            $classes = isset($menu[4]) ? $menu[4] : '';
            $slug = isset($menu[5]) ? $menu[5] : '';
            $icon = isset($menu[6]) ? $menu[6] : '';

            // $post, $get replaces . with _ so replace . with | and later convert back to .
            $url = str_replace('.', '|', $url);

            if (empty($title)) {
                continue;
            }

            // Remove html tags
            $title = strip_tags($title);
            // Check if name ends with number
            if (is_numeric(substr($title, -1))) {
                // Remove number from title
                $title = strtok($title, " ");
            }
            array_push($fields['menu'], array(
                'name' => $title,
                'slug' => 'menu_' . $url,
                'type' => 'select',
                'options' => array(
                    'everyone' => 'Iedereen',
                    'none' => 'Niemand',
                    'webprofit' => 'WebProfit',
                    'admin' => 'Admin',
                )
            ));
        }
        return $fields;
    }

    public function render_page()
    {
        $this->settings_api->render_page(false);
    }

    private function endsWith($haystack, $needle)
    {
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    public function remove_menu_pages()
    {
        global $wpdb;
        $user = wp_get_current_user();
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->options} WHERE option_name LIKE 'webprofit_menu_%'");

        if (empty($results)) {
            return;
        }

        foreach ($results as $row) {
            // Remove prefix from name to get menu slug
            $name = str_replace("webprofit_menu_", "", $row->option_name);
            // Convert | back to .
            $name = str_replace('|', '.', $name);
            $value = $row->option_value;

            if (empty($name) || empty($value)) {
                continue;
            }

            if ($value == 'none') {
                remove_menu_page($name);
            } elseif ($value == 'webprofit' && !($user->user_login == 'webprofit' || $user->user_login == 'webprofitteam' || $this->endsWith($user->user_email, '@webprofit.nl'))) {
                remove_menu_page($name);
            } elseif ($value == 'admin' && !current_user_can('administrator')) {
                remove_menu_page($name);
            }
        }
    }
}
new WebProfitMenu;
