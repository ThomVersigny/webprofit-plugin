<?php

if (!defined('ABSPATH')) {
    exit;
}

if (get_option('webprofit_enable_updater') != null) {
    add_action('init', function () {
        $platform = get_option('webprofit_updater_platform');
        $username = get_option('webprofit_updater_username');
        $repository = get_option('webprofit_updater_repository');
        $api = get_option('webprofit_updater_api');
        if ($platform == 'github') {
            include_once(WEBPROFIT_PLUGIN_DIR . 'framework/updater/github-plugin-updater.php');
            new GithubPluginUpdater(WEBPROFIT_PLUGIN_FILE, $username, $repository, $api);
        } elseif ($platform == 'gitlab') {
            include_once(WEBPROFIT_PLUGIN_DIR . 'framework/updater/gitlab-plugin-updater.php');
            new GitlabPluginUpdater(WEBPROFIT_PLUGIN_FILE, $repository, $api);
        } else {
            WebProfit::admin_error('<a href="'.get_admin_url(null, 'admin.php?page=webprofit-settings&tab=updater').'">Updater</a> platform ongeldig.');
        }
    });
} else {
    WebProfit::admin_warning('<a href="'.get_admin_url(null, 'admin.php?page=webprofit-settings&tab=updater').'">Updater</a> niet geactiveerd.');
}
