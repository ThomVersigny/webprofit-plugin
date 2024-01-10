<?php

if (class_exists('Database')) {
    return;
}

class Database implements IPage
{
    private $pages;
    public function __construct()
    {
        $this->pages = new PagesBuilder;
        add_action('admin_menu', array( $this, 'admin_menu' ));
    }
    public function admin_menu()
    {
        $this->pages->add_top_page('Database', 'example-database', array( $this, 'render' ));
    }
    public function render()
    {
        $this->pages->start();

        $db = new DatabaseConnection;
        $db->prepare(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $result = $db->query("SELECT * FROM wp_posts");

        echo 'results<br>';

        foreach ($result as $record) {
            echo $record['post_title'].'<br>';
        }
        $db->close();

        $this->pages->end();
    }
}
new Database;
