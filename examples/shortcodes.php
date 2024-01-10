<?php

if (class_exists('Shortcodes')) {
    return;
}

class Shortcodes implements IPage
{
    private $pages;
    public function __construct()
    {
        $this->pages = new PagesBuilder;
        add_action('admin_menu', array( $this, 'admin_menu' ));
    }
    public function admin_menu()
    {
        $this->pages->add_top_page('Shortcodes', 'example-shortcodes', array( $this, 'render' ));
    }
    public function render()
    {
        $this->pages->start();

        echo do_shortcode('[shortcodes]');

        $this->pages->end();
    }
}
new Shortcodes;
