<?php

if (class_exists('WebProfitUpdater')) {
    return;
}

class WebProfitUpdater implements ISettingsAPI
{
    private $settings_api;
    public function __construct()
    {
        $this->settings_api = new SettingsAPI;
        add_action('admin_init', array( $this, 'admin_init' ));
        if (isset($_GET['tab']) && $_GET['tab'] == 'updater') {
            add_action('wep_form_after_submit', array($this, 'render_after_submit'));
        }
    }
    public function admin_init()
    {
        $this->settings_api->init();
    }
    function render_after_submit() {
        ?><div class="wep-test"><a id="wep-test-auth" class="button">Testen</a><span id="wep-message"></span></div><?php
        ?><script type="text/javascript">
        jQuery(document).ready(function($){
            $("#wep-test-auth").click(function(e){
                e.preventDefault();
                $("#wep-message").html('Testen...');
                $("#wep-message").removeClass();
                var id = $("#webprofit_updater_repository").val();
                var token = $("#webprofit_updater_api").val();
                var url = 'https://gitlab.com/api/v4/projects/' + id + '/?private_token=' + token;
                $.ajax({
                    type: 'GET',
                    url: url,
                    statusCode: {
                        401: function(data) {
                            console.log(data);
                            $("#wep-message").addClass('wep-error');
                            $("#wep-message").html('Test mislukt');
                        },
                        404: function(data) {
                            console.log(data);
                            $("#wep-message").addClass('wep-error');
                            $("#wep-message").html('Test mislukt');
                        },
                        200: function(data) {
                            $("#wep-message").addClass('wep-success');
                            $("#wep-message").html('Test geslaagd');
                        }
                    }
                });
            });
        });
        </script>
        <style>
        .wep-test {
            display: flex;
            flex-direction: row;
        }
        #wep-message {
            padding: 5px;
            margin-left: 10px;
        }
        .wep-success {
            background-color: #bfb;
        }
        .wep-error {
            background-color: #f99;
        }
        </style><?php
    }
    public function render_page()
    {
        $this->settings_api->render_page();
    }
}
new WebProfitUpdater;
