<?php
return array(
    'wordpress' => array(
        array(
            'name' => 'Standaard instellingen',
            // 'description' => '<ol>
            // <li>Vervangen Login Logo</li>
            // <li>Verbergen Update meldingen voor standaard gebruikers</li>
            // <li>Toevoegen Support widget</li>
            // <li>Verbergen Dashboard widgets / panel</li>
            // <li>Vervang admin footer</li>
            // </ol>',
            'slug' => 'default_settings',
            'type' => 'checkbox',
            'default' => '1',
        ),
        array(
            'name' => 'Inschakelen Thickbox',
            'description' => '<a href="https://codex.wordpress.org/Javascript-Reference/ThickBox" target="-blank">Thickbox</a>',
            'slug' => 'wordpress_enable_thickbox',
            'type' => 'checkbox',
        ),
        array(
            'name' => 'Remove Query Strings From Static Resources',
            'slug' => 'enable_remove_query_string',
            'type' => 'checkbox',
        ),
        array(
            'name' => 'Font Awsome',
            'slug' => 'enable_font_awesome',
            'type' => 'checkbox',
        ),
        array(
            'name' => 'Dashicons',
            'slug' => 'enable_dashicons',
            'description' => 'Laad dashboard icons in frontend',
            'type' => 'checkbox',
        ),
    ),
    'webprofit' => array(
        array(
            'name' => 'Shortcodes',
            'slug' => 'enable_shortcodes',
            'type' => 'checkbox',
            'default' => '1',
        ),
        array(
            'name' => 'Styles',
            'slug' => 'enable_style',
            'type' => 'checkbox',
        ),
        array(
            'name' => 'Scripts',
            'slug' => 'enable_script',
            'type' => 'checkbox',
        ),
    ),
    'plugins' => array(
        array(
            'name' => 'Gravity Forms',
            'description' => 'Inschakelen optie om Labels te verbergen <a href="https://gravitywiz.com/how-to-hide-gravity-form-field-labels-when-using-placeholders/" target="-blank">Gravity Forms</a>',
            'slug' => 'gravity_forms_hide_label',
            'type' => 'checkbox',
        ),
    ),
    'themeco' => array(
        array(
            'name' => 'Globale Kleuren',
            'slug' => 'cornerstone_global_colors',
            'type' => 'checkbox',
        ),
        array(
            'name' => 'Views Integratie',
            'slug' => 'cornerstone_views_integration',
            'type' => 'checkbox',
        ),
        array(
            'name' => 'Uitschakelen Portfolio',
            'slug' => 'themeco_disable_portfolio',
            'type' => 'checkbox',
        ),
    ),
    'social' => array(
        array(
            'name' => 'Facebook',
            'slug' => 'social_facebook',
            'type' => 'url',
            'shortcode' => 'w-social-facebook'
        ),
        array(
            'name' => 'Twitter',
            'slug' => 'social_twitter',
            'type' => 'url',
            'shortcode' => 'w-social-twitter'
        ),
        array(
            'name' => 'Google Plus',
            'slug' => 'social_google_plus',
            'type' => 'url',
            'shortcode' => 'w-social-google-plus'
        ),
        array(
            'name' => 'LinkedIn',
            'slug' => 'social_linkedin',
            'type' => 'url',
            'shortcode' => 'w-social-linkedin'
        ),
        array(
            'name' => 'Youtube',
            'slug' => 'social_youtube',
            'type' => 'url',
            'shortcode' => 'w-social-youtube'
        ),
    ),
    'google' => array(
        array(
            'name' => 'Schema.org',
            'description' => 'Aan de hand van <a href="'.get_admin_url(null, 'admin.php?page=webprofit&tab=contact').'">instelling</a> wordt schema.org opmaak toegevoegd aan elke pagina.',
            'slug' => 'enable_schema_org',
            'type' => 'checkbox',
        ),
        array(
            'name' => 'Google Maps API key',
            'description' => '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="-blank">Handleiding</a>',
            'slug' => 'google_maps_api',
            'type' => 'text',
            'placeholder' => 'AIzaSyArcOYvGuS7PeAbHjg1QH-d2jpDUIN6GXE',
        ),
        array(
            'name' => 'Google Tag Manager ID',
            'description' => '<a href="https://developers.google.com/tag-manager/quickstart" target="-blank">Handleiding</a>',
            'slug' => 'google_tag_manager_id',
            'type' => 'text',
            'placeholder' => 'GTM-XXXXXXX',
        ),
        array(
            'name' => 'Google Tag Manager Header Script',
            'slug' => 'google_tag_manager_header',
            'type' => 'textarea',
            'placeholder' => '<script></script>'
        ),
        array(
            'name' => 'Google Tag Manager Body Script',
            'slug' => 'google_tag_manager_body',
            'type' => 'textarea',
            'placeholder' => '<script></script>'
        ),
        array(
            'name' => 'Google Analytics Script',
            'description' => '<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/" target="-blank">Handleiding</a>',
            'slug' => 'google_analytics',
            'type' => 'textarea',
            'placeholder' => '<script></script>'
        ),
    ),
    'maintenance' => array(
        array(
            'name' => 'Inschakelen',
            'slug' => 'enable_maintenance',
            'type' => 'checkbox',
        ),
        array(
            'name' => 'Titel',
            'slug' => 'maintenance_title',
            'type' => 'text',
            'placeholder' => 'Nog even geduld...',
        ),
        array(
            'name' => 'Sub Titel',
            'slug' => 'maintenance_subtitle',
            'type' => 'text',
            'placeholder' => 'Deze website wordt ontwikkeld door WebProfit',
        ),
        array(
            'name' => 'Redirect',
            'slug' => 'maintenance_redirect',
            'type' => 'url',
        ),
        array(
            'name' => 'Achtergrondafbeelding',
            'slug' => 'maintenance_image',
            'type' => 'file',
        ),
        array(
            'name' => 'Custom CSS',
            'slug' => 'maintenance_custom_css',
            'type' => 'textarea',
        ),
        array(
            'name' => 'Login link',
            'slug' => 'maintenance_login',
            'type' => 'checkbox',
        ),
    ),
    'beveiliging' => array(
        array(
            'name' => 'Whitelist Domeinen',
            'description' => 'Alleen de bovenstaande domeinen mogen gebruikt worden om een account aan te maken',
            'slug' => 'security_register_whitelist_domains',
            'type' => 'textarea',
            'placeholder' => 'domain.com&#10;domain.nl',
        ),
        array(
            'name' => 'Mime Types',
            'description' => 'Bovenstaande <a href="https://codex.wordpress.org/Function-Reference/get-allowed-mime-types" target="_blank">Mime Types</a> zijn toegestaan om up te loaden in <a href="'.get_admin_url(null, 'upload.php').'">mediabibliotheek</a>',
            'slug' => 'allowed_mime_types',
            'type' => 'textarea',
            'placeholder' => 'xlsl application/spreadsheetml.sheet&#10;webm video/webm',
            'placeholder' => 'svg image/svg+xml&#10;psd image/vdn.adobe.photoshop',
        ),
    ),
    'updater' => array(
        array(
            'name' => 'Inschakelen',
            'slug' => 'enable_updater',
            'type' => 'checkbox',
            'default' => '1',
        ),
        array(
            'name' => 'Platform',
            'slug' => 'updater_platform',
            'type' => 'select',
            'options' => array(
                'github' => 'Github',
                'gitlab' => 'Gitlab',
            ),
        ),
        array(
            'name' => 'Username',
            'slug' => 'updater_username',
            'type' => 'text',
        ),
        array(
            'name' => 'Repository',
            'slug' => 'updater_repository',
            'type' => 'text',
            'required' => 'required',
        ),
        array(
            'name' => 'API',
            'slug' => 'updater_api',
            'type' => 'text',
        ),
    ),
    'contact' => array(
        array(
            'name' => 'Bedrijfsnaam',
            'slug' => 'company_name',
            'type' => 'text',
            'shortcode' => 'w-company-name',
        ),
        array(
            'name' => 'Logo',
            'slug' => 'company_logo',
            'type' => 'file',
            'shortcode' => 'w-company-logo',
        ),
        array(
            'name' => 'Afbeelding',
            'slug' => 'company_image',
            'type' => 'file',
            'shortcode' => 'w-company-image',
        ),
        array(
            'name' => 'BTW',
            'slug' => 'company_btw',
            'type' => 'text',
            'shortcode' => 'w-company-btw',
        ),
        array(
            'name' => 'KVK',
            'slug' => 'company_kvk',
            'type' => 'text',
            'shortcode' => 'w-company-kvk',
        ),
        array(
            'name' => 'Straatnaam en huisnummer',
            'slug' => 'company_street',
            'type' => 'text',
            'shortcode' => 'w-company-street',
        ),
        array(
            'name' => 'Postcode',
            'slug' => 'company_zipcode',
            'type' => 'text',
            'shortcode' => 'w-company-zipcode',
        ),
        array(
            'name' => 'Plaatsnaam',
            'slug' => 'company_place',
            'type' => 'text',
            'shortcode' => 'w-company-place',
        ),
        array(
            'name' => 'Longitude',
            'slug' => 'company_longitude',
            'type' => 'text',
            'placeholder' => '51.842269',
            'step' => '0.01',
            'shortcode' => 'w-company-longitude',
        ),
        array(
            'name' => 'Latitude',
            'slug' => 'company_latitude',
            'type' => 'text',
            'placeholder' => '4.994569',
            'step' => '0.01',
            'shortcode' => 'w-company-latitude',
        ),
        array(
            'name' => 'Telefoonnummer',
            'slug' => 'company_phone',
            'type' => 'phone',
            'shortcode' => 'w-company-phone',
        ),
        array(
            'name' => 'Email',
            'slug' => 'company_email',
            'type' => 'email',
            'shortcode' => 'w-company-email',
        ),
        array(
            'name' => 'Maandag',
            'slug' => 'company_monday',
            'type' => 'time',
            'shortcode' => 'w-company-monday',
        ),
        array(
            'name' => 'Dinsdag',
            'slug' => 'company_tuesday',
            'type' => 'time',
            'shortcode' => 'w-company-tuesday',
        ),
        array(
            'name' => 'Woensdag',
            'slug' => 'company_wednesday',
            'type' => 'time',
            'shortcode' => 'w-company-wednesday',
        ),
        array(
            'name' => 'Donderdag',
            'slug' => 'company_thursday',
            'type' => 'time',
            'shortcode' => 'w-company-thursday',
        ),
        array(
            'name' => 'Vrijdag',
            'slug' => 'company_friday',
            'type' => 'time',
            'shortcode' => 'w-company-friday',
        ),
        array(
            'name' => 'Zaterdag',
            'slug' => 'company_saturday',
            'type' => 'time',
            'shortcode' => 'w-company-saturday',
        ),
        array(
            'name' => 'Zondag',
            'slug' => 'company_sunday',
            'type' => 'time',
            'shortcode' => 'w-company-sunday',
        ),
    ),
    'openingstijden' => array(
        array(
            'name' => 'Maandag Van',
            'slug' => 'company_monday_start',
            'type' => 'time',
            'shortcode' => 'w-company-monday-start',
        ),
        array(
            'name' => 'Maandag Tot',
            'slug' => 'company_monday_end',
            'type' => 'time',
            'shortcode' => 'w-company-monday-end',
        ),
        array(
            'name' => 'Dinsdag Van',
            'slug' => 'company_tuesday_start',
            'type' => 'time',
            'shortcode' => 'w-company-tuesday-start',
        ),
        array(
            'name' => 'Dinsdag Tot',
            'slug' => 'company_tuesday_end',
            'type' => 'time',
            'shortcode' => 'w-company-tuesday-end',
        ),
        array(
            'name' => 'Woensdag Van',
            'slug' => 'company_wednesday_start',
            'type' => 'time',
            'shortcode' => 'w-company-wednesday-start',
        ),
        array(
            'name' => 'Woensdag Tot',
            'slug' => 'company_wednesday_end',
            'type' => 'time',
            'shortcode' => 'w-company-wednesday-end',
        ),
        array(
            'name' => 'Donderdag Van',
            'slug' => 'company_thursday_start',
            'type' => 'time',
            'shortcode' => 'w-company-thursday-start',
        ),
        array(
            'name' => 'Donderdag Tot',
            'slug' => 'company_thursday_end',
            'type' => 'time',
            'shortcode' => 'w-company-thursday-end',
        ),
        array(
            'name' => 'Vrijdag Van',
            'slug' => 'company_friday_start',
            'type' => 'time',
            'shortcode' => 'w-company-friday-start',
        ),
        array(
            'name' => 'Vrijdag Tot',
            'slug' => 'company_friday_end',
            'type' => 'time',
            'shortcode' => 'w-company-friday-end',
        ),
        array(
            'name' => 'Zaterdag Van',
            'slug' => 'company_saturday_start',
            'type' => 'time',
            'shortcode' => 'w-company-saturday-start',
        ),
        array(
            'name' => 'Zaterdag Tot',
            'slug' => 'company_saturday_end',
            'type' => 'time',
            'shortcode' => 'w-company-saturday-end',
        ),
        array(
            'name' => 'Zondag Van',
            'slug' => 'company_sunday_start',
            'type' => 'time',
            'shortcode' => 'w-company-sunday-start',
        ),
        array(
            'name' => 'Zondag Tot',
            'slug' => 'company_sunday_end',
            'type' => 'time',
            'shortcode' => 'w-company-sunday-end',
        ),
    ),
    'woocommerce-snippets' => array(
        array(
            'name' => 'Verberg verzendmethodes',
            'description' => 'Verzendmethodes verbergen als gratis verzenden beschikbaar is, klik <a href="https://businessbloomer.com/woocommerce-hide-shipping-options-free-shipping-available/" target="-blank">hier</a> voor meer informatie.',
            'slug' => 'woocommerce-hide-shipping-methodes-when_free_is_available',
            'type' => 'checkbox',
        ),
    ),
);
