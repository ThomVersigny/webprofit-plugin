<?php

class WebProfitFeatures
{
    private function get_option($option)
    {
        $option = get_option($option);
        if (!empty($option) || $option == 1) {
            return true;
        }
        return false;
    }
    public function register_features()
    {
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getMethods() as $method) {
            if (strpos($method->name, 'feature_') === 0) {
                $reflectionMethod = new ReflectionMethod($method->class, $method->name);
                $attributes = CustomReflection::getAttributes($reflectionMethod);

                $name = isset($attributes['name']) ? $attributes['name'] : null;
                $description = isset($attributes['description']) ? $attributes['description'] : null;
                $options = isset($attributes['option']) ? $attributes['option'] : null;
                $url = isset($attributes['url']) ? $attributes['url'] : null;
                $check = false;

                if (is_array($options)) {
                    foreach ($options as $option) {
                        if ($this->get_option($option)) {
                            $check = true;
                        }
                    }
                } elseif (is_string($options)) {
                    if ($this->get_option($options)) {
                        $check = true;
                    }
                }
                if (!isset($options)) {
                    $check = true;
                }

                if ($check) {
                    call_user_func(array($this, $method->name));
                }
            }
        }
    }

    public function feature_maintenance_mode()
    {
        if (get_option('webprofit_enable_maintenance', false) == true) {
            add_action('template_redirect', function () {
                global $pagenow;
                if ($pagenow !== 'wp-login.php' && !is_user_logged_in()) {
                    $redirect = get_option('webprofit_maintenance_redirect', false);
                    if (!empty($redirect) && filter_var($redirect, FILTER_VALIDATE_URL) == true) {
                        if ( $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1' ) {
                            header( "HTTP/1.1 503 Service Temporarily Unavailable" );
                        } else {
                            header( "HTTP/1.0 503 Service Temporarily Unavailable" );
                        }
                        header( "Status: 503 Service Temporarily Unavailable" );
                        header( "Retry-After: 86400" ); // 86400 seconds een dag later
                        header( "Location: {$redirect}" );
                        exit();
                    }
                    require_once(WEBPROFIT_PLUGIN_DIR . 'framework/views/maintenance.php');
                    exit();
                }
            });
        }
    }

    /**
    * @name("Debug notification")
    * @description("Convert settings into shortcodes.")
    */
    public function feature_wordpress_debug_notification() {
        if (defined('WP_DEBUG') && true === WP_DEBUG) {
            WebProfit::admin_warning(' <a href="https://codex.wordpress.org/Debugging_in_WordPress" target="_blank">Debugging</a> is geactiveerd.');
        }
    }

    /**
    * @name("Settings Shortcodes")
    * @description("Convert settings into shortcodes.")
    */
    public function feature_settings_shortcodes()
    {
        $sections = include WEBPROFIT_PLUGIN_DIR . 'data/fields.php';
        foreach ($sections as $section=>$fields) {
            foreach ($fields as $field) {
                // Check if shortcode is registered
                if (!isset($field['shortcode'])) {
                    continue;
                }
                $slug = $field['slug'];
                if (isset($field['default'])) {
                    $default = $field['default'];
                } else {
                    $default = null;
                }
                WebProfit::add_shortcode($field['shortcode'], function() use ( $slug, $default ) {
                    $option = get_option( WEBPROFIT_SLUG . '_'. $slug, $default);
                    if (empty($option)) {
                        return;
                    }
                    return $option;
                });
            }
        }
    }

    public function feature_plugin_action_links()
    {
        add_filter('plugin_action_links_' . plugin_basename(WEBPROFIT_PLUGIN_FILE), function( $links ) {
            array_unshift($links, '<a href="' . admin_url( 'admin.php?page=webprofit-settings' ) . '">Settings</a>');
            $links[] = '<a href="https://www.webprofit.nl" target="_blank">WebProfit</a>';
            return $links;
        });
    }

    public function load_font_awesome()
    {
        ?><script src="https://use.fontawesome.com/b0aa9589bb.js"></script><?php
    }
    /**
    * @name("Font Awsome")
    * @description("Load Font Awsome.")
    * @option("webprofit_enable_font_awesome")
    * @url("https://fontawesome.com/")
    */
    public function feature_load_custom_font_awesome()
    {
        add_action('wp_head', array($this, 'load_font_awesome'));
        add_action('admin_head', array($this, 'load_font_awesome'));
    }

    /**
    * @name("Dashboard Icons")
    * @description("Load dashboard icons front-end.")
    * @option("webprofit_enable_dashicons")
    */
    public function feature_load_dashicons()
    {
        add_action('wp_head', function () {
            wp_enqueue_style('dashicons');
        });
    }

    private function multiexplode($delimiters, $string)
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    }
    public function remove_query_string_version($src)
    {
        $url = $this->multiexplode(array( '&ver', '?ver' ), $src);
        return $url[0];
    }
    /**
    * @name("Remove Query Strings From Static Resources")
    * @description("Remove Query Strings From Static Resources.")
    * @url("https://nl.wordpress.org/plugins/remove-query-strings-from-static-resources/")
    * @option("webprofit_enable_remove_query_string")
    */
    public function feature_remove_query_strings_from_static_resources()
    {
        if (!is_admin()) {
            return;
        }
        add_filter('script_loader_src', array($this, 'remove_query_string_version'), 15, 1);
        add_filter('style_loader_src', array($this, 'remove_query_string_version'), 15, 1);
    }

    /**
    * @name("Themeco - Global Colors")
    * @description("Convert theme colors into global css colors.")
    * @option("webprofit_cornerstone_global_colors")
    * @url("https://xthemetips.com/use-themeco-pro-template-colors-anywhere/1035/")
    * @example(".class { color: var(--xtt-black); }")
    */
    public function feature_global_colors()
    {
        add_action('wp_head', function () {
            $themecolors = get_option('cornerstone_color_items');
            $css = '<style type="text/css" id="theme-colors">';
            $colors = json_decode(stripslashes($themecolors), true);
            $count = count($colors);

            for ($i = 0; $i <= $count-1; $i++) {
                $name = 'xtt-' . preg_replace('/[^a-z]/', "", strtolower($colors[$i]['title']));
                $css .= ':root { --' . $name . ': ' . $colors[$i]['value'] . '; } ';
                $css .= '.' . $name . ' { color: ' . $colors[$i]['value'] . '; } ';
            }

            $css .= '</style>';
            echo $css;
        });
    }
    /**
    * @name("Themeco - Element")
    * @description("Toolset Views Element.")
    * @option("webprofit_cornerstone_views_integration")
    */
    public function feature_themeco_views_element()
    {
        add_filter('cornerstone_icon_map', function ($icon_map) {
            $icon_map['views-element'] = WEBPROFIT_PLUGIN_URL . 'assets/img/svg/views-element.svg';
            return $icon_map;
        });
        add_action('cornerstone_register_elements', function () {
            cornerstone_register_element('Toolset_Views_Element', 'views-element', WEBPROFIT_PLUGIN_DIR . 'framework/views/views-element');
        });
    }
    /**
    * @name("Themeco - Disable Portfolio")
    * @description("Disable default portfolio post type.")
    * @option("webprofit_themeco_disable_portfolio")
    * @url("https://theme.co/apex/forum/t/completely-disable-portfolio-cpt/29371/4")
    */
    public function feature_themeco_disable_portfolio()
    {
        add_action('init', function () {
            if ( post_type_exists('x-portfolio') ) {
                unregister_post_type('x-portfolio');
            }
            if ( taxonomy_exists('portfolio-category')) {
                unregister_taxonomy('portfolio-category');
            }
            if ( taxonomy_exists('portfolio-tag')) {
                unregister_taxonomy('portfolio-tag');
            }
        }, 20);
    }

    /**
    * @name("Gravity Forms - Form Labels")
    * @description("Option to hide form lables.")
    * @option("webprofit_gravity_forms_hide_label")
    * @url("https://gravitywiz.com/how-to-hide-gravity-form-field-labels-when-using-placeholders/")
    */
    public function feature_gravity_forms_hide_form_labels()
    {
        add_filter('gform_enable_field_label_visibility_settings', '__return_true');
    }

    public function load_custom_stylesheet()
    {
        wp_enqueue_style(WEBPROFIT_SLUG . '-style', WEBPROFIT_PLUGIN_URL . '/assets/css/webprofit.css');
    }
    /**
    * @name("WebProfit - Style")
    * @description("Load WebProfit Style.")
    * @section("WebProfit")
    * @option("webprofit_enable_style")
    */
    public function feature_load_custom_stylesheet()
    {
        add_action('admin_enqueue_scripts', array($this, 'load_custom_stylesheet'));
        add_action('wp_enqueue_scripts', array($this, 'load_custom_stylesheet'));
    }

    public function load_custom_scripts()
    {
        wp_register_script(WEBPROFIT_SLUG . '-script', WEBPROFIT_PLUGIN_URL . '/assets/js/webprofit.js', array('jquery'));
        wp_enqueue_script(WEBPROFIT_SLUG . '-script');
    }
    /**
    * @name("WebProfit - Scripts")
    * @section("WebProfit")
    * @description("Load WebProfit Scripts.")
    * @option("webprofit_enable_script")
    */
    public function feature_load_custom_scripts()
    {
        add_action('admin_enqueue_scripts', array($this, 'load_custom_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'load_custom_scripts'));
    }

    /**
    * @name("WordPress - Dashboard Widgets")
    * @description("Remove dashboard widgets.")
    * @section("WordPress")
    * @option("webprofit_default_settings", "webprofit_remove_dashboard_widgets", "webprofit_add_dashboard_widget", "webprofit_remove_dashboard_panel")
    * @url("https://codex.wordpress.org/Dashboard_Widgets_API")
    */
    public function feature_wordpress_customize_dashboard_widgets()
    {
        // Add webprofit support widget
        add_action('wp_dashboard_setup', function () {
            wp_add_dashboard_widget('wep_dashboard_widget', 'WebProfit', function () {
                echo '<img src="' . WEBPROFIT_PLUGIN_URL . 'assets/img/webprofit-logo.png' . '" style="width:60%;margin-top:10px;" align="right"><a href="https://www.webprofit.nl">Webprofit</a><br>Edisonweg 30<br>4207 HG Gorinchem<br><a href="tel:+31183201020">+31 (0)183-201020</a></br><a href="mailto:support@webprofit.nl">support@webprofit.nl</a>';
            });
        });
        // Remove default widgets
        add_action('admin_init', function () {
            remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
            remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
            remove_meta_box('dashboard_primary', 'dashboard', 'side');
            remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
            remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
            remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
            remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
            remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
            remove_meta_box('dashboard_activity', 'dashboard', 'normal');
        });
        // Remove welcom panel
        remove_action('welcome_panel', 'wp_welcome_panel');
    }

    /**
    * @name("WordPress - Thickbox")
    * @description("Enable Thickbox.")
    * @section("WordPress")
    * @option("webprofit_gravity_forms_hide_label")
    * @url("https://codex.wordpress.org/Javascript_Reference/ThickBox")
    */
    public function feature_wordpress_enable_thickbox()
    {
        add_action('wp_enqueue_scripts', 'add_thickbox');
    }

    /**
    * @name("WordPress - Zoeken")
    * @description("WordPress Backend Zoeken in Custom Velden.")
    * @section("WordPress")
    * @option("webprofit_default_settings")
    */
    public function feature_wordpress_backend_search_custom_fields()
    {
        add_filter('posts_join', function ($join) {
            global $pagenow, $wpdb;

            if (is_admin() && $pagenow == 'edit.php' && ! empty($_GET['s'])) {
                $join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
            }
            return $join;
        });
        add_filter('posts_where', function ($where) {
            global $pagenow, $wpdb;

            if (is_admin() && $pagenow == 'edit.php' && ! empty($_GET['s'])) {
                $where = preg_replace(
                    "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
                    "(" . $wpdb->posts . ".post_title LIKE $1) OR (" . $wpdb->postmeta . ".meta_value LIKE $1)",
                    $where
                );
            }
            return $where;
        });
        add_filter('posts_groupby', function ($groupby) {
            global $pagenow, $wpdb;
            if (is_admin() && $pagenow == 'edit.php' && ! empty($_GET['s'])) {
                $groupby = "$wpdb->posts.ID";
            }
            return $groupby;
        });
    }

    /**
    * @name("WordPress - Update Notices")
    * @description("Hide WordPress Update Messages.")
    * @section("WordPress")
    * @option("webprofit_default_settings", "webprofit_remove_wordpress_update")
    * @url("https://codex.wordpress.org/Updating_WordPress")
    */
    public function feature_wordpress_hide_update_notices()
    {
        add_action('admin_head', function () {
            if (!current_user_can('update_core')) {
                remove_action('admin_notices', 'update_nag', 3);
            }
        }, 1);
    }

    /**
    * @name("WordPress - Backend Styles Fix")
    * @description("Make wordpress backend more responsive.")
    * @section("WordPress")
    * @option("webprofit_default_settings")
    */
    public function feature_backend_styles()
    {
        add_action('admin_head', function () {
            ?><style>
            @media screen and (max-width: 782px) {
            form.form-table table {
                    width: 100%;
                }
            }
            form hr {
                border-top: 1px solid #ccc;
                border-bottom: 0px;
            }
        </style><?php
        });
    }

    /**
    * @name("WordPress - Login Form")
    * @description("Replace login logo.")
    * @section("WordPress")
    * @option("webprofit_replace_login_logo")
    * @url("https://codex.wordpress.org/Customizing_the_Login_Form")
    */
    public function feature_replace_login_logo()
    {
        add_action('login_enqueue_scripts', function () {
            ?><style type="text/css">
            #login h1 a, .login h1 a {
                width: 300px;
                height: 72px;
                background-image: url( <?php echo WEBPROFIT_PLUGIN_URL . 'assets/img/webprofit-logo-transparent.png'; ?> );
                background-size: contain;
                background-position: bottom;
            }
        </style><?php
        });
    }

    /**
    * @name("WordPress - Login Form")
    * @description("Custom login logo.")
    * @section("WordPress")
    * @option("webprofit_custom_login_logo")
    * @url("https://codex.wordpress.org/Customizing_the_Login_Form")
    */
    public function feature_replace_custom_login_logo()
    {
        add_action('login_enqueue_scripts', function () {
            ?><style type="text/css">
            #login h1 a, .login h1 a {
                width: 300px;
                height: 72px;
                background-image: url( <?php echo get_option('webprofit_custom_login_logo'); ?> );
                background-size: contain;
                background-position: bottom;
            }
        </style><?php
        });
    }

    /**
    * @name("WordPress - Login Form")
    * @description("Custom login css.")
    * @section("WordPress")
    * @option("webprofit_custom_login_css")
    * @url("https://codex.wordpress.org/Customizing_the_Login_Form")
    */
    public function feature_custom_login_css()
    {
        add_action('login_enqueue_scripts', function () {
            ?><style type="text/css"><?php echo get_option('webprofit_custom_login_css'); ?></style><?php
        });
    }

    /**
    * @name("WordPress - Admin bar")
    * @description("Remove admin bar new content.")
    * @section("WordPress")
    * @option("webprofit_default_settings")
    * @url("https://codex.wordpress.org/Function_Reference/remove_menu_page")
    */
    public function feature_remove_admin_bar()
    {
        add_action('wp_before_admin_bar_render', function () {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('new-content');
        });
    }

    /**
    * @name("WordPress - Admin footer text")
    * @description("Replace admin footer text.")
    * @section("WordPress")
    * @option("webprofit_default_settings", "webprofit_replace_admin_footer")
    * @url("https://codex.wordpress.org/Plugin_API/Action_Reference/admin_footer")
    */
    public function feature_replace_admin_footer_text()
    {
        add_filter('admin_footer_text', function () {
            return '<span id="footer-thankyou">Gerealiseerd door <a href="https://www.webprofit.nl/" target="_blank">WebProfit</a></span>';
        });
    }

    /**
    * @name("Google - Maps")
    * @description("Register Google Maps API.")
    * @section("Google")
    * @option("webprofit_google_maps_api")
    */
    public function feature_register_google_maps_api()
    {
        add_action('wp_enqueue_scripts', function () {
            wp_register_script('webprofit-google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . get_option('webprofit_google_maps_api'));
            wp_enqueue_script('webprofit-google-maps');
        });
    }

    /**
    * @name("Google - Analytics")
    * @description("Header Script.")
    * @section("Google")
    * @option("webprofit_google_analytics")
    */
    public function feature_google_analytics()
    {
        add_action('wp_head', function () {
            echo get_option('webprofit_google_analytics');
        });
    }

    /**
    * @name("Google - Tag Manager")
    * @description("Header Script.")
    * @section("Google")
    * @option("webprofit_google_tag_manager_header")
    */
    public function feature_google_tag_manager_header_script()
    {
        add_action('wp_head', function () {
            echo get_option('webprofit_google_tag_manager_header');
        });
    }

    /**
    * @name("Google - Tag Manager")
    * @description("Body Script.")
    * @section("Google")
    * @option("webprofit_google_tag_manager_body")
    */
    public function feature_google_tag_manager_body_script()
    {
        add_filter('body_class', function ($classes) {
            $classes[] = '">' . get_option('webprofit_google_tag_manager_body') . '<noscript></noscript novar="';
            return $classes;
        });
    }

    /**
    * @name("Google - Schema.org")
    * @description("Add schema.org json.")
    * @option("webprofit_enable_schema_org")
    */
    public function feature_google_schema_org_json()
    {
        add_action('wp_footer', function () {
            $url = get_site_url();
            $site_name = get_bloginfo('name');
            $description = get_bloginfo('description');

            $company_name = get_option('webprofit_company_name');
            $street = get_option('webprofit_company_street');
            $place = get_option('webprofit_company_place');
            $zipcode = get_option('webprofit_company_zipcode');
            $lat = get_option('webprofit_company_latitude');
            $long = get_option('webprofit_company_longitude');
            $logo = get_option('webprofit_company_logo');
            $image = get_option('webprofit_company_image');
            $phone = get_option('webprofit_company_phone');
            $email = get_option('webprofit_company_email');

            $facebook = get_option('webprofit_social_facebook');
            $twitter = get_option('webprofit_social_twitter');
            $googleplus = get_option('webprofit_social_google_plus');
            $linkedin = get_option('webprofit_social_linkedin');
            $youtube = get_option('webprofit_social_youtube');

            $website = array(
                "@content" => "http://schema.org/",
                "@type" => "WebSite",
                "url" => $url,
                "name" => $site_name,
                "alternateName" => $description,
                "potentialAction" => array(
                    "@type" => "SearchAction",
                    "target" => $url . '?s={query}',
                    "query-input" => "required name=query",
                ),
            );
            $organization = array(
                "@context" => "http://schema.org/",
                "@type" => "Organization",
                "name" => $company_name,
                "description" => $description,
                "url" => $url,
            );

            if (!empty($street) && !empty($place) && !empty($zipcode)) {
                $organization['location']['@type'] = 'Place';
                $organization['location']['address']['@type'] = 'PostalAddress';
                $organization['location']['address']['addressCountry'] = 'NL';
                $organization['location']['address']['addressRegion'] = 'Netherlands';
                $organization['location']['address']['streetAddress'] = $street;
                $organization['location']['address']['postalCode'] = $zipcode;
                $organization['location']['address']['addressLocality'] = $place;

                $organization['address']['@type'] = 'PostalAddress';
                $organization['address']['addressCountry'] = 'NL';
                $organization['address']['addressRegion'] = 'Netherlands';
                $organization['address']['streetAddress'] = $street;
                $organization['address']['postalCode'] = $zipcode;
                $organization['address']['addressLocality'] = $place;
                if (!empty($long) && !empty($lat)) {
                    $organization['location']['geo']['@type'] = 'GeoCoordinates';
                    $organization['location']['geo']['longitude'] = $long;
                    $organization['location']['geo']['latitude'] = $lat;
                }
            }

            if (!empty($phone)) {
                $organization['telephone'] = $phone;
                $organization['location']['telephone'] = $phone;
            }

            if (!empty($phone)) {
                $organization['email'] = $email;
            }

            if (!empty($logo)) {
                list($width, $height) = getimagesize($logo);
                $organization['logo'] = array(
                    "@type" => "ImageObject",
                    "url" => $logo,
                    "width" => $width,
                    "height" => $height,
                );
            }

            if (!empty($image)) {
                list($width, $height) = getimagesize($image);
                $organization['image'] = array(
                    "@type" => "ImageObject",
                    "url" => $image,
                    "width" => $width,
                    "height" => $height,
                );
            }

            if (!empty($facebook)) {
                $organization['sameAs'][] = $facebook;
            }
            if (!empty($twitter)) {
                $organization['sameAs'][] = $twitter;
            }
            if (!empty($googleplus)) {
                $organization['sameAs'][] = $googleplus;
            }
            if (!empty($linkedin)) {
                $organization['sameAs'][] = $linkedin;
            }
            if (!empty($youtube)) {
                $organization['sameAs'][] = $youtube;
            } ?><script id="webprofit-json-website" type="application/ld+json"><?php
            echo json_encode($website, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?></script><?php
            ?><script id="webprofit-json-organization" type="application/ld+json"><?php
            echo json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?></script><?php
        });
    }

    /**
    * @name("Security - Register Accounts")
    * @description("Whitelist Domains for new users accounts.")
    * @section("Security")
    * @option("webprofit_security_register_whitelist_domains")
    * @url("https://codex.wordpress.org/Plugin_API/Filter_Reference/registration_errors")
    */
    public function feature_security_whitelist_domains()
    {
        add_filter('registration_errors', function ($errors, $sanitized_user_login, $user_email) {
            // Convert $email to $username and $domain (apullen | webprofit.nl)
            $user_email = urldecode($user_email);
            list($username, $domain) = explode('@', $user_email);

            // Get all whitelisted domains and convert to array
            $domains = get_option('webprofit_security_register_whitelist_domains');
            $domains = explode("\n", $domains);
            $domains = array_filter(array_map('trim', $domains));

            // Check if domain is not allowed
            if (!in_array($domain, $domains)) {
                $errors->add('webprofit_error', __('<strong>ERROR</strong>: This domain is not allowed.', WEBPROFIT_SLUG));
            }

            return $errors;
        }, 10, 3);
    }
    /**
    * @name("Security - Mime Types")
    * @description("Whitelist Domains for new users accounts.")
    * @option("webprofit_allowed_mime_types")
    * @url("https://codex.wordpress.org/Function_Reference/get_allowed_mime_types")
    */
    public function feature_security_allowed_mime_types()
    {
        add_filter('upload_mimes', function ($existing_mime_types) {
            $new_mime_types = get_option('webprofit_allowed_mime_types');
            $new_mime_types = explode("\n", $new_mime_types);
            foreach ($new_mime_types as $mime_type) {
                list($extension, $type) = explode(" ", $mime_type);
                $existing_mime_types[$extension] = $type;
            }
            return $existing_mime_types;
        });
    }
}
$webProfitFeatures = new WebProfitFeatures;
$webProfitFeatures->register_features();
