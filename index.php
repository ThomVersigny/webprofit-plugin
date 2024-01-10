<?php
/*
Plugin Name: WebProfit
Plugin URI: https://www.webprofit.nl/
Description: For internal usage only
Version:  2.0.4
Author: Kevin Kraaijveld
Author URI: https://www.kevii.nl/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: webprofit
Domain Path: /languages
*/

define('WEBPROFIT_NAME', 'WebProfit');
define('WEBPROFIT_SLUG', 'webprofit');
define('WEBPROFIT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WEBPROFIT_PLUGIN_FILE', plugin_dir_path(__FILE__) . 'index.php');
define('WEBPROFIT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WEBPROFIT_PLUGIN_VERSION', '3.0.4');

// Classes
include_once(WEBPROFIT_PLUGIN_DIR . 'framework/classes/webprofit.php');
include_once(WEBPROFIT_PLUGIN_DIR . 'framework/classes/plugin.php');

add_action('plugins_loaded', function () {
    // Classes
    include_once(WEBPROFIT_PLUGIN_DIR . 'framework/classes/reflection.php');
    include_once(WEBPROFIT_PLUGIN_DIR . 'framework/classes/shortcodes.php');
    include_once(WEBPROFIT_PLUGIN_DIR . 'framework/classes/features.php');
    if (is_admin()) {
        // Settings API
        include_once(WEBPROFIT_PLUGIN_DIR . 'framework/settings-api/pages.php');
        include_once(WEBPROFIT_PLUGIN_DIR . 'framework/settings-api/tabs.php');
        include_once(WEBPROFIT_PLUGIN_DIR . 'framework/settings-api/forms.php');
        include_once(WEBPROFIT_PLUGIN_DIR . 'framework/settings-api/settings-api.php');

        // Updater
        include_once(WEBPROFIT_PLUGIN_DIR . 'framework/updater/index.php');

        // Views
        include_once(WEBPROFIT_PLUGIN_DIR . 'framework/views/webprofit-settings.php');
        include_once(WEBPROFIT_PLUGIN_DIR . 'framework/views/webprofit-menu.php');
        include_once(WEBPROFIT_PLUGIN_DIR . 'framework/views/webprofit-sync.php');
        include_once(WEBPROFIT_PLUGIN_DIR . 'framework/views/webprofit-updater.php');
        include_once(WEBPROFIT_PLUGIN_DIR . 'framework/views/webprofit-maintenance.php');
        include_once(WEBPROFIT_PLUGIN_DIR . 'framework/views/webprofit-documentation.php');
        include_once(WEBPROFIT_PLUGIN_DIR . 'framework/views/webprofit-features.php');

        // WooCommerce
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            include_once(WEBPROFIT_PLUGIN_DIR . 'framework/views/woocommerce/snippets.php');
            include_once(WEBPROFIT_PLUGIN_DIR . 'framework/views/woocommerce/woocommerce-settings.php');
        }
    }
});
