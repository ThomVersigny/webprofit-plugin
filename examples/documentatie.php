<?php

class Documentation implements IPage
{
    private $pages;
    public function __construct($t = null)
    {
        $this->pages = new PagesBuilder;
        add_action('admin_menu', array( $this, 'admin_menu' ));
    }
    public function admin_menu()
    {
        $this->pages->add_top_page('Docs', 'example-docs', array( $this, 'render' ));
    }
    public function render()
    {
        $this->pages->start();

        $class = "WebProfit_Shortcodes";
        $reflectionClass = new ReflectionClass($class); ?><div class="accordions"><?php

        foreach ($reflectionClass->getMethods() as $method) {
            $reflectionMethod = new ReflectionMethod($method->class, $method->name);
            $attributes = CustomReflection::getAttributes($reflectionMethod);
            extract($attributes); ?><div class="accordion"><?php

            ?><div class="accordion-header"><div class="accordion-title"><?php

            if (isset($name)) {
                echo $name;
            } else {
                echo 'Shortcode';
            } ?></div></div><?php

            ?><div class="accordion-body"><?php

            if (isset($tag)) {
                echo $tag;
            }

            if (isset($example)) {
                echo '<code>'.$example.'</code>';
            }

            if (isset($tag)) {
                echo do_shortcode($tag);
            } ?></div><?php

            ?></div><?php
        } ?></div><?php

        $this->pages->end();
    }
}
new Documentation;
