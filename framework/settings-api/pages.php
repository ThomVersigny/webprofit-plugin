<?php

interface IPage
{
    public function admin_menu();
}

class PagesBuilder
{
    public function start($title = true)
    {
        ?><div class="wrap wep-options"><?php 
        if ($title) {
            echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
        } ?><?php
    }
    public function end()
    {
        ?></div><?php
    }
    // https://developer.wordpress.org/reference/functions/add_menu_page/
    public function add_top_page($title, $slug, $callback, $icon = '', $position = null)
    {
        return add_menu_page(
            $title,
            $title,
            'manage_options',
            $slug,
            $callback,
            $icon != null ? $icon : '',
            $position != null ? $position : null
        );
    }
    // https://developer.wordpress.org/reference/functions/add_submenu_page/
    public function add_sub_page($parent, $title, $slug, $callback)
    {
        return add_submenu_page(
            $parent,
            $title,
            $title,
            'manage_options',
            $slug,
            $callback
        );
    }
}
