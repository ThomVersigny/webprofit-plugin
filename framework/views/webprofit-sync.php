<?php

class WebProfitSync implements IPage
{
    private $pages;

    public function __construct()
    {
        $this->pages = new PagesBuilder;
        add_action('admin_menu', array( $this, 'admin_menu' ));
        add_action('admin_footer', array( $this, 'admin_footer' ));
        add_action('wp_ajax_wep_settings_sync', array( $this, 'wp_ajax_wep_settings_sync' ));
    }

    public function admin_menu()
    {
        $this->pages->add_sub_page('webprofit', 'Sync', 'webprofit-sync', array( $this, 'render_page' ));
    }

    public function admin_footer() {
        ?><script type="text/javascript" class="xyz">
	    jQuery(document).ready(function($) {
            var data = {
                'action': 'wep_settings_sync',
                'data': []
            };

            jQuery('.button.section-button').click(function(){

                var section = jQuery(this).data('section');

                jQuery(`input[data-section='${section}']`).each(function(index, element){
                    var name = jQuery(element).attr("name");
                    var value = jQuery(element).val();

                    data['data'][index] = {
                        'option': name,
                        'value': value
                    };
                });
                // console.log(data);
        
                jQuery("#wep-message." + section).html('Instellingen opslaan...');
                jQuery("#wep-message." + section).removeClass('wep-success wep-error');

                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                jQuery.post(ajaxurl, data, function(response) {
                    if (response) {
                        jQuery("#wep-message." + section).addClass('wep-success');
                        jQuery("#wep-message." + section).html('Instellingen opgeslagen');
                        jQuery("table." + section + " tr.wep-option-ne").each(function(index, element){
                            jQuery(element).removeClass('wep-option-ne');
                            var defaultValue = jQuery(".wep-default-value", element).html();
                            jQuery(element).children(".wep-current-value").html(defaultValue);
                        });
                        jQuery(`button[data-section='${section}']`).prop('disabled', true);
                    } else {
                        console.log(response);
                        jQuery("#wep-message." + section).addClass('wep-error');
                        jQuery("#wep-message." + section).html('Instellingen niet opgeslagen');
                    }
                });
            });
        });
	    </script>
        <style>
            table.widefat.fixed {
                margin-bottom: 1em;
            }
            button.button.section-button {
                margin-bottom: 2em;
            }
            tr.wep-option-ne td {
                color: red;
            }
            .wep-row {
                display: flex;
                flex-direction: row;
            }
            #wep-message {
                padding: 5px;
                margin-left: 10px;
                max-height: 18px;
            }
            .wep-success {
                background-color: #bfb;
            }
            .wep-error {
                background-color: #f99;
            }
        </style><?php
    }

    public function wp_ajax_wep_settings_sync() {

        if ( !current_user_can("manage_options") ) {
            echo false;
        }

        if ( $_POST['data'] ) {
            $options = $_POST['data'];
            if (!empty($options)) {
                foreach($options as $option) {
                    update_option($option['option'], $option['value']);
                }
                echo true;
            }
        }

        echo false;

        wp_die();
    }

    public function render_page()
    {
        $this->pages->start();
        $settings = $this->get_settings();
        foreach ($settings as $title=>$options) {
            $check_section = false;
            $slug = sanitize_title( $title );
            ?><table class="widefat fixed <?php echo $slug; ?>" cellspacing="0"><?php
            ?><thead><tr><?php
            ?><th class="manage-column column-columnname" scope="col" colspan="3"><strong><?php echo $title; ?></strong></th><?php
            ?></tr></thead><?php
            ?><tbody><?php
                foreach($options as $option) {
                    $check_option = false;
                    $label = $option['title'];
                    $name = $option['option'];
                    $default_value = $option['default_value'];
                    $current_value = get_option($option['option']);
                    if (is_numeric($default_value)) {
                        $default_value = intval($default_value);
                    }
                    if (is_numeric($current_value)) {
                        $current_value = intval($current_value);
                    }
                    if (isset($option['default_option']) && empty($current_value)) {
                        $current_value = get_option($option['default_option']);
                    }
                    if ($current_value != $default_value) {
                        $check_section = true;
                        $check_option = true;
                    }
                    if ( $check_option ) {
                        ?><tr class="wep-option-ne"><?php
                    } else {
                        ?><tr><?php
                    }
                    ?><td class="column-columnname wep-label"><?php
                    echo "<label>{$label}</label>";
                    echo "<input type='hidden' name='{$name}' value='{$default_value}' data-section='{$slug}'>";
                    ?></td><?php
                    if (isset($option['options'])) {
                        foreach($option['options'] as $key=>$value) {
                            if ($current_value == $key) {
                                $current_value = $value;
                            }
                            if ($default_value == $key) {
                                $default_value = $value;
                            }
                        }
                    }
                    if (empty($default_value)) {
                        $default_value = '<span class="dashicons dashicons-no-alt"></span>';
                    }
                    if (empty($current_value)) {
                        $current_value = '<span class="dashicons dashicons-no-alt"></span>';
                    }
                    if ($current_value === 1 || $current_value == 'open' ) {
                        $current_value = '<span class="dashicons dashicons-yes"></span>';
                    }
                    if ($default_value === 1 || $default_value == 'open' ) {
                        $default_value = '<span class="dashicons dashicons-yes"></span>';
                    }
                    ?><td class="column-columnname wep-current-value"><?php echo $current_value; ?></td><?php
                    ?><td class="column-columnname wep-default-value"><?php echo $default_value; ?></td><?php
                    ?></tr><?php
                    $check_option = false;
                }
            ?></tbody><?php
            ?></table><?php
            ?><div class="wep-row"><button class="button section-button" data-section="<?php echo $slug; ?>" <?php if (!$check_section): echo 'disabled'; endif; ?>>Synchroniseer</button><span id="wep-message" class="<?php echo $slug; ?>"></span></div><?php
            $check_section = false;
        }
        $this->pages->end();
    }
    
    private function get_settings() {
        $settings = array( 
            'Algemene instellingen' => array(
                array(
                    'title' => 'E-mailadres',
                    'default_option' => 'admin_email',
                    'option' => 'new_admin_email',
                    'default_value' => 'wordpress@webprofit.nl'
                ),
                array(
                    'title' => 'Standaard rol voor nieuwe gebruikers',
                    'option' => 'default_role',
                    'default_value' => 'subscriber'
                ),
                array(
                    'title' => 'Lidmaatschap',
                    'option' => 'users_can_register',
                    'default_value' => 1
                ),
                array(
                    'title' => 'Websitetaal',
                    'option' => 'WPLANG',
                    'default_value' => 'nl_NL'
                ),
                array(
                    'title' => 'Tijdzone',
                    'option' => 'timezone_string',
                    'default_value' => 'Europe/Amsterdam'
                ),
                array(
                    'title' => 'Datumnotatie',
                    'option' => 'date_format',
                    'default_value' => 'j F Y'
                ),
                array(
                    'title' => 'Tijdnotatie',
                    'option' => 'time_format',
                    'default_value' => 'H:i'
                ),
                array(
                    'title' => 'Week begint op',
                    'option' => 'start_of_week',
                    'default_value' => 1,
                    'options' => array(
                        0 => 'zondag',
                        1 => 'maandag',
                        2 => 'dinsdag',
                        3 => 'woensdag',
                        4 => 'donderdag',
                        5 => 'vrijdag',
                        6 => 'zaterdag',
                    )
                ),
            ),
            'Reacties' => array(
                array(
                    'title' => 'Probeer elk ander blog gelinkt in dit artikel te benaderen ',
                    'option' => 'default_pingback_flag',
                    'default_value' => 0
                ),
                array(
                    'title' => 'Sta linkmeldingen van andere blogs (pingbacks en trackbacks) op nieuwe artikelen toe',
                    'option' => 'default_ping_status',
                    'default_value' => 'open'
                ),
                array(
                    'title' => 'Sta toe dat bezoekers kunnen reageren op nieuwe artikelen',
                    'option' => 'default_comment_status',
                    'default_value' => 'open'
                ),
                array(
                    'title' => 'Schrijvers van reacties moeten naam en e-mailadres opgeven',
                    'option' => 'require_name_email',
                    'default_value' => 1
                ),
                array(
                    'title' => 'Gebruikers moeten ingelogd zijn om te kunnen reageren',
                    'option' => 'comment_registration',
                    'default_value' => 1
                ),
                array(
                    'title' => 'De reactiemogelijkheid automatisch uitschakelen bij berichten',
                    'option' => 'close_comments_for_old_posts',
                    'default_value' => 1
                ),
                array(
                    'title' => 'De reactiemogelijkheid automatisch uitschakelen bij berichten ouder dan .. dag(en)',
                    'option' => 'close_comments_days_old',
                    'default_value' => 0
                ),
                array(
                    'title' => 'Opt-in selectievakje voor reactie cookies tonen.',
                    'option' => 'show_comments_cookies_opt_in',
                    'default_value' => 0
                ),
                array(
                    'title' => 'Geneste reacties toestaan tot X niveaus diep',
                    'option' => 'thread_comments',
                    'default_value' => 0
                ),
                array(
                    'title' => 'Reacties over meerdere pagina\'s verdelen met',
                    'option' => 'page_comments',
                    'default_value' => 0
                ),
                array(
                    'title' => 'Stuur mij een e-mail wanneer: Iemand een reactie plaatst',
                    'option' => 'comments_notify',
                    'default_value' => 0
                ),
                array(
                    'title' => 'Stuur mij een e-mail wanneer: Een reactie wacht op moderatie',
                    'option' => 'moderation_notify',
                    'default_value' => 0
                ),
                array(
                    'title' => 'Reactie moet handmatig worden goedgekeurd',
                    'option' => 'comment_moderation',
                    'default_value' => 1
                ),
                array(
                    'title' => 'De afzender moet een eerder toegelaten reactie geplaatst hebben',
                    'option' => 'comment_whitelist',
                    'default_value' => 1
                ),
                array(
                    'title' => 'Avatars tonen',
                    'option' => 'show_avatars',
                    'default_value' => 0
                ),
            ),
            'Media' => array(
                array(
                    'title' => 'Bestanden uploaden (Uploads bewaren in mappen op basis van maand en jaar)',
                    'option' => 'uploads_use_yearmonth_folders',
                    'default_value' => 1
                ),
                array(
                    'title' => 'Thumbnails Croppen',
                    'option' => 'thumbnail_crop',
                    'default_value' => 1
                ),
                array(
                    'title' => 'Thumbnailgrootte Breedte',
                    'option' => 'thumbnail_size_w',
                    'default_value' => '150'
                ),
                array(
                    'title' => 'Thumbnailgrootte Hoogte',
                    'option' => 'thumbnail_size_h',
                    'default_value' => '150'
                ),
                array(
                    'title' => 'Gemiddelde afmeting Breedte',
                    'option' => 'medium_size_w',
                    'default_value' => '300'
                ),
                array(
                    'title' => 'Gemiddelde afmeting Hoogte',
                    'option' => 'medium_size_h',
                    'default_value' => '300'
                ),
                array(
                    'title' => 'Grote afmeting Breedte',
                    'option' => 'large_size_w',
                    'default_value' => '600'
                ),
                array(
                    'title' => 'Grote afmeting Hoogte',
                    'option' => 'large_size_h',
                    'default_value' => '600'
                ),
            ),
            'Permalinks' => array(
                array(
                    'title' => 'Aangepaste structuur',
                    'option' => 'custom_selection',
                    'default_value' => 'custom'
                ),
                array(
                    'title' => 'Format',
                    'option' => 'permalink_structure',
                    'default_value' => '/%category%/%postname%/'
                ),
            )
        );
        return $settings;
    }
}
new WebProfitSync;
