<?php

class WebProfit
{
    public static $shortcodes = [];

    public static function error($message, $dismissible = true)
    {
        if (! is_admin()) {
            return;
        }
        if ($dismissible === true) {
            $dismissible = 'is-dismissible';
        } else {
            $dismissible = false;
        }
        add_action('admin_notices', function() use($dismissible, $message) {
            echo "<div class='notice notice-error {$dismissible}'><p>WebProfit - {$message}</p></div>";
        });
    }

    public static function admin_error($message, $dismissible = true)
    {
        if (!function_exists('current_user_can')) {
            return;
        }
        if (current_user_can('administrator')) {
            self::error($message, $dismissible);
        }
    }

    public static function warning($message, $dismissible = true)
    {
        if (! is_admin()) {
            return;
        }
        if ($dismissible === true) {
            $dismissible = 'is-dismissible';
        } else {
            $dismissible = false;
        }
        add_action('admin_notices', function() use($dismissible, $message) {
            echo "<div class='notice notice-warning {$dismissible}'><p>WebProfit - {$message}</p></div>";
        });
    }

    public static function admin_warning($message, $dismissible = true)
    {
        if (!function_exists('current_user_can')) {
            return;
        }
        if (current_user_can('administrator')) {
            self::warning($message, $dismissible);
        }
    }

    public static function success($message, $dismissible = true)
    {
        if (! is_admin()) {
            return;
        }
        if ($dismissible === true) {
            $dismissible = 'is-dismissible';
        } else {
            $dismissible = false;
        }
        add_action('admin_notices', function() use($dismissible, $message) {
            echo "<div class='notice notice-success {$dismissible}'><p>WebProfit - {$message}</p></div>";
        });
    }

    public static function admin_success($message, $dismissible = true)
    {
        if (current_user_can('administrator')) {
            self::success($message, $dismissible);
        }
    }

    public static function get_stack()
    {
        if (function_exists('x_get_stack')) {
            $stack = x_get_stack();
            return $stack;
        }
        return;
    }

    public static function is_x()
    {
        $theme = wp_get_theme();
        if ($theme == 'X') {
            return true;
        }
        return false;
    }

    public static function is_pro()
    {
        $theme = wp_get_theme();
        if ($theme == 'Pro') {
            return true;
        }
        return false;
    }

    public static function add_shortcode($tag, $callback)
    {
        if (shortcode_exists($tag)) {
            WebProfit::admin_warning('Shortcode [' . $tag . '] bestaat al.');
        } else {
            add_shortcode($tag, $callback);
            array_push(self::$shortcodes, $tag);
        }
    }

    public static function add_shortcodes($tags, $callback)
    {
        if (!is_array($tags)) {
            return;
        }
        foreach ($tags as $tag) {
            WebProfit::add_shortcode($tag, $callback);
        }
    }

    public static function get_shortcodes()
    {
        return self::$shortcodes;
    }
}
