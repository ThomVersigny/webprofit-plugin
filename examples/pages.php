<?php

if (class_exists('TestPage')) {
    return;
}

class TestPage implements IPage
{
    private $pages;
    public function __construct()
    {
        $this->pages = new PagesBuilder;
        add_action('admin_menu', array( $this, 'admin_menu' ));
    }
    public function admin_menu()
    {
        $this->pages->add_top_page('Page', 'example-page', array( $this, 'render_top_page' ));
        $this->pages->add_sub_page('example-page', 'Sub Page', 'example-sub-page', array( $this, 'render_sub_page' ));
    }
    public function render_top_page()
    {
        $this->pages->start();
        echo 'top page';
        $this->pages->end();
    }
    public function render_sub_page()
    {
        $this->pages->start();
        echo 'sub page';
        $this->pages->end();
    }
}
new TestPage;
