<?php

interface ISettingsAPI
{
    public function admin_init();
    public function render_page();
}

// https://developer.wordpress.org/plugins/settings/custom-settings-page/
// https://github.com/tareq1988/wordpress-settings-api-class
class SettingsAPI
{
    private $tab;
    private $page;

    private $pages_builder;
    private $tabs_builder;
    private $forms_builder;

    private $tabs = array();
    private $sections = array();
    private $fields = array();

    private $prefix = 'webprofit_';

    public function __construct()
    {
        if (isset($_GET['page'])) {
            $this->page = $_GET['page'];
        }
        if (isset($_GET['tab'])) {
            $this->tab = $_GET['tab'];
        }
        $this->pages_builder = new PagesBuilder;
        $this->tabs_builder = new TabsBuilder;
        $this->forms_builder = new FormsBuilder;
    }

    public function init()
    {
        $defaults = array(
            'name'  => '',
            'slug' => '',
            'type'  => 'text',
        );

        // Check if sections and fields is set
        if (! is_array($this->sections) && ! is_array($this->fields)) {
            return;
        }
        // Check if sections and fields not empty
        if (empty($this->sections) && empty($this->fields)) {
            $this->load_defaults();
        }

        $this->clean_up();

        // Register sections
        foreach ($this->sections as $section) {
            // Check if section exists inside current page
            if (isset($section['page']) && $section['page'] != $this->page) {
                continue;
            }
            // Check if section exists inside current tab
            if (isset($section['tab']) && $section['tab'] != $this->tab) {
                continue;
            }
            // Add section
            add_settings_section($section['slug'], $section['name'], function () use ($section) {
                if (isset($section['description'])) {
                    echo $section['description'];
                }
            }, $section['slug']);
        }

        // Register fields
        foreach ($this->fields as $section=>$fields) {
            foreach ($fields as $field) {
                // Parse field with default arguments
                $field = wp_parse_args($field, $defaults);
                // Callback
                $type = is_callable(array( $this->forms_builder, 'render_' . $field['type'] )) ? 'render_' . $field['type'] : 'render_text';
                // Prepare settings field attributes
                $attributes = array(
                    'slug' => $this->prefix . $field['slug'],
                    'label_for' => $this->prefix . $field['slug'],
                    'class' => ! empty($field['class']) ? $field['class'] : '',
                    'description' => ! empty($field['description']) ? $field['description'] : '',
                    'options' => isset($field['options']) ? $field['options'] : '',
                    'placeholder' => isset($field['placeholder']) ? $field['placeholder'] : '',
                    'required' => ! empty($field['required']) == true ? true : false,
                    'disabled' => ! empty($field['disabled']) ? true : false,
                    'default' => ! empty($field['default']) ? $field['default'] : '',
                );
                if (isset($field['min'])) {
                    $attributes['min'] = $field['min'];
                }
                if (isset($field['max'])) {
                    $attributes['max'] = $field['max'];
                }
                if (isset($field['step'])) {
                    $attributes['step'] = $field['step'];
                }

                // Register settings db
                register_setting($section, $this->prefix . $field['slug']);
                // Add settings
                add_settings_field($section.'['.$this->prefix . $field['slug'].']', $field['name'], array( $this->forms_builder, $type ), $section, $section, $attributes);
            }
        }
    }

    public function add_top_page($title, $slug, $callback, $icon = '', $position = null)
    {
        return $this->pages_builder->add_top_page($title, $slug, $callback, $icon, $position);
    }

    public function add_sub_page($parent, $title, $slug, $callback)
    {
        return $this->pages_builder->add_sub_page($parent, $title, $slug, $callback);
    }

    public function load_defaults()
    {
        if (empty($this->tabs)) {
            $this->tabs = include WEBPROFIT_PLUGIN_DIR . 'data/tabs.php';
        }
        if (empty($this->sections)) {
            $this->sections = include WEBPROFIT_PLUGIN_DIR . 'data/sections.php';
        }
        if (empty($this->fields)) {
            $this->fields = include WEBPROFIT_PLUGIN_DIR . 'data/fields.php';
        }
    }

    public function clean_up()
    {
        // Remove unused tabs from array $this->tabs
        for ($i = 0; $i < count($this->tabs); $i++) {
            if (isset($this->tabs[$i]['page']) && $this->tabs[$i]['page'] != $this->page) {
                unset($this->tabs[$i]);
            }
        }
        // Remove unused sections from array $this->sections
        for ($i = 0; $i < count($this->sections); $i++) {
            if (isset($this->sections[$i]['page']) && $this->sections[$i]['page'] != $this->page) {
                unset($this->sections[$i]);
            }
            if (isset($this->sections[$i]['tab']) && $this->sections[$i]['tab'] != $this->tab) {
                unset($this->sections[$i]);
            }
        }
    }

    public function set_tabs($tabs = array())
    {
        if (empty($tabs)) {
            $tabs = include WEBPROFIT_PLUGIN_DIR . 'data/tabs.php';
        }
        $this->tabs = $tabs;
    }

    public function set_sections($sections = array())
    {
        if (empty($sections)) {
            $sections = include WEBPROFIT_PLUGIN_DIR . 'data/sections.php';
        }
        $this->sections = $sections;
        return $this;
    }

    public function set_fields($fields = array())
    {
        if (empty($fields)) {
            $fields = include WEBPROFIT_PLUGIN_DIR . 'data/fields.php';
        }
        $this->fields = $fields;
        return $this;
    }

    public function render_page($title = true)
    {
        $this->render_page_start($title);
        // Check if tabs is set and not empty
        if (is_array($this->tabs)  && ! empty($this->tabs)) {
            $this->tabs_builder->render($this->tabs);
        }
        $this->render_form();
        $this->render_page_end();
    }

    public function render_page_start($title = true)
    {
        $this->pages_builder->start($title);
    }
    
    public function render_page_end()
    {
        $this->pages_builder->end();
    }

    public function render_form()
    {
        $this->forms_builder->form_start();
        if (is_array($this->sections) && !empty($this->sections)) {
            foreach ($this->sections as $section) {
                // Check if section exists inside current page
                if (isset($section['page']) && $section['page'] != $this->page) {
                    continue;
                }
                // Check if section exists inside current tab
                if (isset($section['tab']) && $section['tab'] != $this->tab) {
                    continue;
                }
                do_settings_sections($section['slug']);
                settings_fields($section['slug']);
            }
        }
        // Render all registered settings
        // settings_fields( $this->group );
        // Render all sections for this page
        // do_settings_sections( $this->group );
        $this->forms_builder->form_end();
    }
}
