<?php

/**
 * @name("WebProfit Shortcodes")
 * @url("https://codex.wordpress.org/Shortcode_API")
 */
class WebProfitShortcodes
{
    private $shortcodes = array();

    private function add_shortcode($tag, $callback)
    {
        WebProfit::add_shortcode($tag, $callback);
        $this->shortcodes[] = $tag;
    }

    private function add_shortcodes($tags, $callback)
    {
        if (!is_array($tags)) {
            return;
        }
        foreach ($tags as $tag) {
            $this->add_shortcode($tag, $callback);
        }
    }

    private function register_toolset_shortcodes($shortcodes = null)
    {
        $shortcodes = $this->shortcodes;
        add_filter('wpv_custom_inner_shortcodes', function ($toolsetShortcodes) use ($shortcodes) {
            foreach ($shortcodes as $shortcode) {
                $toolset_shortcodes[] = $shortcode;
            }
            return $toolset_shortcodes;
        });
    }

    public function register_shortcodes()
    {
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getMethods() as $method) {
            if (strpos($method->name, 'shortcode_') === 0) {
                $reflectionMethod = new ReflectionMethod($method->class, $method->name);
                $attributes = CustomReflection::getAttributes($reflectionMethod);

                if (!isset($attributes['tags']) || empty($attributes['tags'])) {
                    continue;
                }

                $tags = $attributes['tags'];
                if (is_array($tags)) {
                    foreach ($tags as $tag) {
                        $this->add_shortcode($tag, array($this, $method->name));
                    }
                }

                if (is_string($tags)) {
                    $this->add_shortcode($tags, array($this, $method->name));
                }
            }
        }
        $this->register_toolset_shortcodes();
    }

    /**
    * @name("Year")
    * @description("Returns current year.")
    * @tags("year", "w-year", "w-current-year")
    * @url("http://php.net/manual/en/function.date.php")
    */
    public function shortcode_year()
    {
        return date('Y');
    }
    /**
    * @name("URL")
    * @description("Returns site URL.")
    * @tags("url", w-url")
    * @url("https://developer.wordpress.org/reference/functions/get_site_url/")
    */
    public function shortcode_url()
    {
        return get_site_url();
    }
    /**
    * @name("Copyright")
    * @description("Returns copyright symbol with login link.")
    * @tags("copyright", "w-copyright")
    */
    public function shortcode_copyright()
    {
        return '( <a href="' . wp_login_url() . '" target="_blank">C</a> )';
    }
    /**
    * @name("Breadcrumbs")
    * @description("Show page breadcrums.")
    * @tags("w-breadcrumb")
    * @url("https://kb.yoast.com/kb/implement-wordpress-seo-breadcrumbs/")
    */
    public function shortcode_breadcrumbs()
    {
        if ( function_exists('yoast_breadcrumb') ) {
            return yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
        }
        if (x_get_option('x_breadcrumb_display') == '1' && function_exists('x_breadcrumbs')) {
            return x_breadcrumbs();
        }
    }
    /**
    * @name("Custom Post Type by Post ID")
    * @description("Returns custom post type slug.")
    * @tags("w-cpt")
    * @attributes("id")
    * @example("[w-cpt id='1']]")
    */
    public function shortcode_cpt($attributes)
    {
        $attributes = shortcode_atts(array( 'id' => '' ), $attributes);
        if (empty($attributes['id'])) {
            return;
        }
        return get_post_type($attributes['id']);
    }
    /**
    * @name("Custom Post Type Info")
    * @description("Returns custom post type info.")
    * @tags("w-post-type")
    * @attributes("id", "type")
    * @example("[w-post-type id='1' type='wpcf-name']]")
    */
    public function shortcode_custom_post_type_info($attributes)
    {
        if (! isset($attributes['id'])) {
            return;
        }
        $id = $attributes['id'];
        
        if (isset($attributes['type'])) {
            $type = $attributes['type'];
        } else {
            $type = 'name';
        }
        
        $post_type = get_post_type($id);
        $post_type_object = get_post_type_object($post_type);
        
        if (! property_exists($post_type_object, $type)) {
            return;
        }
        return $post_type_object->$type;
    }
    /**
    * @name("Post")
    * @description("Returns current post information.")
    * @tags("w-post")
    * @attributes("property", "type")
    * @example("[w-post property='status-schip' type='taxonomy']")
    */
    public function shortcode_post($attributes)
    {
        $attributes = shortcode_atts(array(
            'property' => '',
            'type' => '',
        ), $attributes);
        
        global $post;

        if (!isset($post) || empty($post)) {
            return;
        }

        $property = $attributes['property'];
        $type = $attributes['type'];
        
        if ($post->post_type == 'cs_global_block') {
            return $property;
        }
        if ($property == 'featured_image' && $type == 'url') {
            return wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
        }
        if ($property == 'post_content') {
            return wpautop( $post->post_content );
        }
        if ($type == 'meta') {
            return get_post_meta($post->ID, 'wpcf-'.$property, true);
        }
        if ($type == 'taxonomy' || $type == 'terms') {
            $terms = wp_get_object_terms($post->ID, $property, array('orderby' => 'term_id', 'order' => 'ASC') );
            $post_terms = array();
            if (!empty($terms)) {
                foreach($terms as $term) {
                    $post_terms[] = $term->name;
                }
            }
            return implode(" ", $post_terms);
        }
        if (property_exists($post, $property)) {
            return $post->$property;
        }
    }
    /**
    * @name("Title")
    * @description("Returns current page/post/taxonomy/admin title.")
    * @tags("w-title")
    */
    public function shortcode_title()
    {
        global $post;
        if (is_single()) {
            return get_post_type_object($post->post_type)->labels->name;
        } elseif (is_page()) {
            return the_title();
        } elseif (is_category()) {
            echo 'category';
        } elseif (is_tax()) {
            $queried_object = get_queried_object();
            $taxonomy = get_taxonomy($queried_object->taxonomy);
            return $taxonomy->labels->name;
        } elseif (is_admin()) {
            return get_admin_page_title();
        } else {
            $object = get_queried_object();
            if (!empty($object)) {
                if (isset($object->post_title)) {
                    return $object->post_title;
                }
            }
            return get_the_title();
        }
    }
    /**
    * @name("Page Title")
    * @description("Returns current page title.")
    * @tags("w-page-title")
    */
    public function shortcode_page_title()
    {
        return the_title();
    }
    /**
    * @name("Post Title")
    * @description("Returns current post title.")
    * @tags("w-post-title")
    */
    public function shortcode_post_title()
    {
        global $post;
        if (empty($post)) {
            return;
        }
        return get_the_title($post->ID);
    }
    /**
    * @name("Archive Title")
    * @description("Returns current archive title.")
    * @tags("w-archive-title")
    */
    public function shortcode_archive_title()
    {
        return post_type_archive_title('', false);
    }
    /**
    * @name("Post body without p tags")
    * @description("Return raw post body.")
    * @tags("w-post-body")
    */
    public function shortcode_post_body()
    {
        global $post;
        if (empty($post)) {
            return;
        }
        return $post->post_content;
    }
    /**
    * @name("Lorem Ipsum")
    * @description("Returns lorem ipsum sample text.")
    * @tags("lorem", "w-lorem")
    * @attributes("woorden", "zinnen")
    * @example("[w-lorem woorden='5' zinnen='5']")
    */
    public function shortcode_lorem($attributes)
    {
        $attributes = shortcode_atts(array( 'woorden' => '', 'zinnen' => '' ), $attributes);
        $alinea1 = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc placerat, tortor luctus mattis vulputate, arcu ante elementum orci, quis aliquet purus dui eu ante. Maecenas accumsan ornare enim et congue. Proin sollicitudin massa augue, sit amet auctor ex pharetra ac. Etiam hendrerit posuere bibendum. Ut iaculis in lorem id interdum. Vivamus at lobortis est, sit amet lacinia libero. Vivamus pulvinar ante quam, id porttitor lectus varius sed. Fusce a vestibulum magna. Morbi convallis aliquet mollis. Morbi rhoncus ex eget pulvinar euismod. Curabitur in dolor vitae odio viverra mollis. Morbi maximus, ligula vitae rhoncus hendrerit, urna lorem volutpat orci, sed tincidunt velit ex non neque. Maecenas ac iaculis dui. Pellentesque iaculis in tortor in tempus.';
        $alinea2 = 'Duis blandit nulla odio, nec consectetur quam dapibus ac. Donec dui libero, efficitur in elit ac, viverra malesuada odio. Morbi ut erat in turpis convallis aliquet et eget lorem. Ut mattis vestibulum dolor eget facilisis. Nulla facilisi. Duis at ante at diam auctor sodales in at risus. Quisque eget eros tortor. Sed erat sapien, sagittis ac interdum pretium, eleifend vitae urna. Nam a odio ut felis dapibus commodo et a risus. Mauris et tincidunt risus. Nam at dictum libero. Aenean posuere velit ante, ut aliquam ligula euismod sed. Mauris consequat maximus suscipit. Phasellus sem nisl, auctor et justo sed, pretium semper leo. Morbi ut luctus lectus, ac feugiat quam.';
        $alinea3 = 'Suspendisse aliquam ullamcorper nunc sed aliquet. Cras sed dapibus dui. Phasellus nisl lectus, congue ac vehicula eget, semper semper purus. Etiam tincidunt, magna eu tristique maximus, nulla ipsum lacinia nisi, vel faucibus lorem diam sit amet justo. Suspendisse facilisis, lacus in viverra porta, sem nunc mattis ligula, sit amet elementum lacus arcu ac orci. Aenean id ultrices neque. Nulla condimentum auctor sapien, nec aliquet urna pretium eu. Integer auctor ipsum non iaculis pretium. Morbi viverra massa eget nisi posuere, ut sagittis risus porta.';
        $alinea4 = 'Nunc lacinia magna at est mollis, ac fermentum lacus maximus. Proin sollicitudin neque nisl, a fermentum ligula consectetur eget. Sed pellentesque enim quis leo pharetra, quis elementum libero convallis. Praesent in risus enim. Aenean non malesuada odio, vitae feugiat massa. Aliquam erat volutpat. Sed quis blandit erat. Cras ultricies lectus id est luctus, ornare volutpat arcu tempor. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed egestas dolor erat, vitae commodo nulla sollicitudin a. In aliquet semper lacus ac viverra. Cras a sapien quis eros gravida interdum at semper leo.';
        $alinea5 = 'Duis aliquet, neque nec consequat rhoncus, sem felis dictum lacus, vel elementum tortor lectus ac dolor. Phasellus ultricies, risus in ullamcorper dapibus, tellus magna volutpat mi, eu volutpat ante turpis vitae quam. Aenean placerat sit amet nulla id ornare. Praesent porta arcu nisl, tempor vulputate arcu pellentesque quis. Nulla vel ligula quis velit commodo ornare id nec nunc. Aenean molestie, quam vel volutpat bibendum, nulla lorem dignissim turpis, nec porta metus sem et ipsum. Morbi lacinia dapibus metus vel ullamcorper. Nullam rhoncus, sem nec vulputate placerat, odio sem laoreet nibh, at commodo ipsum erat non mi. Pellentesque vel sapien ante. Nam rutrum risus turpis, vitae rutrum velit rhoncus nec. Suspendisse ultrices tincidunt massa, at tincidunt augue efficitur vel. Sed dapibus iaculis neque, eu congue purus fringilla at. Proin accumsan purus sit amet purus auctor pellentesque. Mauris id enim a risus tempus dapibus. Sed ut ligula porttitor, convallis leo a, dignissim metus.';
        $alineas = array( $alinea1, $alinea2, $alinea3, $alinea4, $alinea5 );
        $text = $alineas[array_rand($alineas)];
        if ($attributes['woorden'] != null) {
            $text = explode(' ', $text);
            $text = implode(' ', array_splice($text, '0', $attributes['woorden']));
            return $text;
        }
        if ($attributes['zinnen'] != null) {
            $text = explode('.', $text);
            $text = implode('.', array_splice($text, '0', $attributes['zinnen']));
            $text .= '.';
            return $text;
        }
        return $text;
    }
    /**
    * @name("Rating stars")
    * @description("Display ratings stars with font awsome icons.")
    * @tags("rating-stars", "w-rating-stars")
    * @attributes("rating", "maxrating")
    * @example("[w-rating-stars rating='7' maxrating='10']")
    */
    public function shortcode_rating_stars($attributes, $content = null)
    {
        $attributes = shortcode_atts(array( 'rating' => 0, 'maxrating' => '' ), $attributes);
        if ($content != null) {
            $rating = do_shortcode($content);
        } else {
            $rating = $attributes['rating'];
        }
    
        if ($attributes['maxrating'] != null) {
            $scale = $attributes['maxrating'] / 5;
            $rating = $rating / $scale;
        }
    
        $html = null;
        // Render full star
        for ($i=1; $i <= $rating; $i++) {
            if (shortcode_exists('x_icon')) {
                $html .= do_shortcode( '[x_icon type="star"]');
            } else {
                $html .= '<i class="x-icon x-icon-star" data-x-icon="" aria-hidden="true"></i>';
            }
        }
        // Render half star
        if (strpos($rating, '.')) {
            if (shortcode_exists('x_icon')) {
                $html .= do_shortcode( '[x_icon type="star-half-empty"]');
            } else {
                $html .= '<i class="x-icon x-icon-star-half-empty" data-x-icon="" aria-hidden="true"></i>';
            }
            $i++;
        }
        // Render blank star
        while ($i <= 5) {
            if (shortcode_exists('x_icon')) {
                $html .= do_shortcode( '[x_icon type="star-o"]');
            } else {
                $html .= '<i class="x-icon x-icon-star-o" data-x-icon="" aria-hidden="true"></i>';
            }
            $i++;
        }
        return $html;
    }

    /**
    * @name("User login link")
    * @description("Return user login or logout link.")
    * @tags("w-user")
    */
    public function shortcode_user()
    {
        if ( is_user_logged_in() ) {
            $user = wp_get_current_user();
            return 'Ingelogd als ' . $user->user_firstname . ' ' . $user->user_lastname . ' <a href="' . wp_logout_url( home_url() ) . '">Logout</a>';
        } else {
            return '<a href="' . wp_login_url( home_url() ) . '">Login</a>';
        }
    }

    /**
    * @name("User login or logout link")
    * @description("Return user login or logout link.")
    * @tags("w-user-link")
    */
    public function shortcode_user_link()
    {
        if (is_user_logged_in()) {
            return '<a href="' . wp_logout_url(home_url()) . '">Logout</a>';
        } else {
            return '<a href="' . wp_login_url(home_url()) . '">Login</a>';
        }
    }
    /**
    * @name("Username")
    * @description("Return user display name.")
    * @tags("w-user-name")
    */
    public function shortcode_user_name()
    {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            return $user->display_name;
        }
    }

    /**
    * @name("Protected Content")
    * @description("Return content only for logged in users.")
    * @tags("w-logged-in-users")
    * @example("[w-logged-in-users]Only visible for logged in users[/w-logged-in-users]")
    */
    public function shortcode_logged_in_users($attributes, $content=null)
    {
        if (is_user_logged_in()) {
            return $content;
        }
    }
    /**
    * @name("Anonymous Content")
    * @description("Content for not logged in users.")
    * @tags("w-logged-out-users")
    * @example("[w-logged-out-users]Only visible for logged out uses[/w-logged-out-users]")
    */
    public function shortcode_logged_out_users($attributes, $content=null)
    {
        if (! is_user_logged_in()) {
            return $content;
        }
    }

    private function get_all_meta_values($key, $type = 'recensie', $status = 'publish')
    {
        global $wpdb;

        if (empty($key)) {
            return array(0);
        }
    
        $results = $wpdb->get_results($wpdb->prepare("SELECT pm.meta_value FROM {$wpdb->postmeta} pm LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = %s AND p.post_status = %s AND p.post_type = %s", $key, $status, $type));
    
        if (empty($results)) {
            return array(0);
        }
    
        foreach ($results as $row) {
            $array[] = $row->meta_value;
        }
    
        return $array;
    }
    /**
    * @name("Total custom field value")
    * @description("Return total amount of values custom field value.")
    * @tags("w-total-custom-fields", "w-total-custom-field")
    * @attributes("field")
    * @example("[w-total-custom-fields field='wpcf-recensie-cijfer']")
    */
    public function shortcode_total_custom_field_value($attributes = null)
    {
        $attributes = shortcode_atts(array( 'field' => '' ), $attributes);
        $field = $attributes['field'];
    
        $result = $this->get_all_meta_values($field);

        if (empty($result)) {
            return 0;
        }

        return count($result);
    }
    /**
    * @name("Highest custom field value")
    * @description("Return highest value of custom field value.")
    * @tags("w-highest-custom-fields", "w-highest-custom-field")
    * @attributes("field")
    * @example("[w-highest-custom-fields field='wpcf-recensie-cijfer']")
    */
    public function shortcode_highest_custom_field_value($attributes = null)
    {
        $attributes = shortcode_atts(array( 'field' => '' ), $attributes);
        $field = $attributes['field'];
    
        $result = $this->get_all_meta_values($field);
    
        if (is_array($result)) {
            $result = max($result);
        }
    
        return round($result * 2) / 2;
    }
    /**
    * @name("Average custom field value")
    * @description("Return average value of custom field value.")
    * @tags("w-average-custom-fields", "w-average-custom-field")
    * @attributes("field")
    * @example("[w-average-custom-fields field='wpcf-recensie-cijfer']")
    */
    public function shortcode_average_custom_field_value($attributes = null)
    {
        $attributes = shortcode_atts(array( 'field' => '' ), $attributes);
        $field = $attributes['field'];
    
        $result = $this->get_all_meta_values($field);
    
        if (is_array($result)) {
            $result = array_sum($result) / count($result);
        }
    
        return round($result * 2) / 2;
    }
    /**
    * @name("Lowest custom field value")
    * @description("Return lowest value of custom field value.")
    * @tags("w-lowest-custom-fields", "w-lowest-custom-field")
    * @attributes("field")
    * @example("[w-lowest-custom-fields field='wpcf-recensie-cijfer']")
    */
    public function shortcode_lowest_custom_field_value($attributes = null)
    {
        $attributes = shortcode_atts(array( 'field' => '' ), $attributes);
        $field = $attributes['field'];
    
        $result = $this->get_all_meta_values($field);
    
        if (is_array($result)) {
            $result = min($result);
        }
    
        return round($result * 2) / 2;
    }
}

if (get_option('webprofit_enable_shortcodes') == 1) {
    $webProfitShortcodes = new WebProfitShortcodes;
    $webProfitShortcodes->register_shortcodes();
}
