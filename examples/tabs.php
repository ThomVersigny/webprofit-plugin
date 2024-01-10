<?php
class TestTabs implements ITabs
{
    private $pages;
    public function __construct()
    {
        $this->pages = new PagesBuilder;
        add_action('admin_menu', array( $this, 'admin_menu' ));
    }
    public function admin_menu()
    {
        $this->pages->add_top_page('Tabs', 'example-tabs', array( $this, 'render_page' ));
    }
    public function render_page()
    {
        $tab = new TabsBuilder;
        $tab->add_tab('Tab 1', 'tab-1', array( $this, 'tab_1' ));
        $tab->add_tab('Tab 2', 'tab-2', array( $this, 'tab_2' ));
        $tab->render();
    }
    public function tab_1()
    {
        echo 'tab 1';
    }
    public function tab_2()
    {
        echo 'tab 2';
    }
}
new TestTabs;
