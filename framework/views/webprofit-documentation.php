<?php

if (class_exists('WebProfitDocumentation')) {
    return;
}

class WebProfitDocumentation implements IPage
{
    private $pages;
    public function __construct()
    {
        $this->pages = new PagesBuilder;
        add_action('admin_menu', array( $this, 'admin_menu' ));
        add_action('init', array($this, 'init'));
    }
    public function init()
    {
        $webProfitFeatures = new WebProfitFeatures;
        if (get_option('webprofit_enable_style', 0) != 1){
            $webProfitFeatures->feature_load_custom_stylesheet();
        }
        if (get_option('webprofit_enable_scripts', 0) != 1){
            $webProfitFeatures->feature_load_custom_scripts();
        }
        if (get_option('webprofit_enable_font_awesome', 0) != 1){
            $webProfitFeatures->feature_load_custom_font_awesome();
        }
    }
    public function admin_menu()
    {
        $this->pages->add_sub_page('webprofit', 'Documentatie', 'webprofit-docs', array( $this, 'render_page' ));
    }
    public function render_section_shortcodes()
    {
        if (get_option('webprofit_enable_shortcodes', 0) != 1) {
            WebProfit::admin_error('Shortcodes not activated', false);
            return;
        }

        ?><h2 id="shortcodes">Shortcodes</h2><?php

        $class = "WebProfitShortcodes";
        $reflectionClass = new ReflectionClass($class); ?><div class="accordions"><?php

        foreach ($reflectionClass->getMethods() as $method) {
            $reflectionMethod = new ReflectionMethod($method->class, $method->name);
            $attributes = CustomReflection::getAttributes($reflectionMethod);

            if (empty($attributes)) {
                continue;
            }

            if (! isset($attributes['name']) || $method->name == '__construct' || $method->name  == 'run') {
                continue;
            }

            $this->render_card($attributes);
        } ?></div><?php
    }
    public function render_section_features()
    {
        ?><h2 id="features">Features</h2><?php

        $class = "WebProfitFeatures";
        $reflectionClass = new ReflectionClass($class); ?><div class="accordions"><?php

        foreach ($reflectionClass->getMethods() as $method) {
            $reflectionMethod = new ReflectionMethod($method->class, $method->name);
            $attributes = CustomReflection::getAttributes($reflectionMethod);

            if (empty($attributes)) {
                continue;
            }

            if (! isset($attributes['name']) || $method->name == '__construct' || $method->name  == 'run') {
                continue;
            }

            $this->render_card($attributes);
        } ?></div><?php
    }
    private function render_card($attributes)
    {
        $name = isset($attributes['name']) ? $attributes['name'] : null;
        $description = isset($attributes['description']) ? $attributes['description'] : null;
        $tags = isset($attributes['tags']) ? $attributes['tags'] : null;
        $result = isset($attributes['result']) ? $attributes['result'] : null;
        $params = isset($attributes['attributes']) ? $attributes['attributes'] : null;
        $url = isset($attributes['url']) ? $attributes['url'] : null;
        $example = isset($attributes['example']) ? $attributes['example'] : null; ?><div class="accordion"><?php

            ?><div class="accordion-header"><div class="accordion-title"><?php

            if (isset($name)) {
                echo $name;
            } ?></div></div><?php

            ?><div class="accordion-body"><?php

            if (isset($description)) {
                ?><p><?php echo $description; ?></p><?php
            }

        if (isset($tags)) {
            if (is_string($tags)) {
                $tags = array($tags);
            }
            if (empty($tags) || !is_array($tags)) {
                return;
            }
            $tags = array_map(function ($element) {
                return "[{$element}]";
            }, $tags); ?><h2>Shortcodes</h2><?php
                ?><code><?php 
                echo implode(" ", $tags); ?></code><?php
                $result = do_shortcode($tags[0]);
            if (!empty($result)) {
                ?><h2>Resultaat</h2><?php
                    echo $result;
            }
            if (isset($params)) {
                ?><h2>Attributen</h2><?php
                    if (is_array($params)) {
                        echo implode(", ", $params);
                    } else {
                        echo $params;
                    }
            }
        }

        if (isset($example)) {
            ?><h2>Example</h2><?php
            ?><code><?php echo $example; ?></code><br><br><?php
            $example = do_shortcode($example);
            if (!empty($example)) {
                echo $example;
            }
        }

        if (isset($url)) {
            echo "<br><br><hr><br><a href='{$url}' class='button' target='_blank'>Meer informatie</a>";
        } ?></div></div><?php
    }
    public function render_page()
    {
        $this->pages->start();
        
        $this->render_section_shortcodes();
        $this->render_section_features();

        $this->pages->end();
    }
}
new WebProfitDocumentation;
