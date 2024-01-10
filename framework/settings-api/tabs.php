<?php

class TabsBuilder
{
    private $tabs;

    public function add_tab($tab)
    {
        if (! is_array($tab)) {
            return;
        }

        $defaults = array(
            'name'  => '',
            'slug' => '',
            'page'  => '',
            'callback'  => '',
        );
        $tab = wp_parse_args($tab, $defaults);

        if (isset($_GET['page']) && $_GET['page'] != $tab['page']) {
            return;
        }
        if (isset($_GET['tab']) && $_GET['tab'] != $tab['tab']) {
            return;
        }

        array_push($this->tabs, $tab);
    }

    public function add_tabs($tabs)
    {
        // Check if tabs is array with tabs
        if (! is_array($tabs)) {
            return;
        }
        foreach ($tabs as $tab) {
            $this->add_tab($tab);
        }
    }

    public function render($tabs = '')
    {
        // Check if tabs already set or given with parameter
        if (empty($tabs)) {
            if (empty($this->tabs)) {
                return;
            }
            $tabs = $this->tabs;
        }
        // Check if tabs is array
        if (! is_array($tabs)) {
            return;
        }
        // Check if no tab is selected then redirect to first tab
        if (! isset($_GET['tab'])) {
            foreach ($tabs as $tab) {
                if ($_GET['page'] != $tab['page']) {
                    continue;
                }
                $slug = $tab['slug'];
                break;
            }
            if (! isset($slug)) {
                return;
            }
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            echo '<meta http-equiv="refresh" content="0; URL=' . $url . '&tab=' . $slug . '">';
        }
        // Render tabs ?><h2 class="nav-tab-wrapper"><?php
        foreach ($tabs as $tab) {
            if (! is_array($tab)) {
                continue;
            }
            // Check if tab belongs to current page
            if ($_GET['page'] != $tab['page']) {
                continue;
            }
            if (isset($_GET['tab']) && $_GET['tab'] == $tab['slug'] || count($tabs) == 1) {
                $tab['class'] = 'nav-tab-active';
            } else {
                $tab['class'] = '';
            }
            
            echo sprintf('<a href="?page=%4$s&tab=%1$s" class="nav-tab %3$s" id="%1$s-tab">%2$s</a>', $tab['slug'], $tab['name'], $tab['class'], $_GET['page']);
        } ?></h2><?php
        // Check if callback is registered
        if (isset($tab['callback']) && is_callable($tab['callback'])) {
            ?><div class="wrap"><?php
            call_user_func($tab['callback']); ?></div><?php
        }
    }
}
