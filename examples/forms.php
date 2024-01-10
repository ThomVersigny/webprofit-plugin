<?php

if (class_exists('TestForm')) {
    return;
}

class TestForm
{
    private $pages;
    public function __construct()
    {
        $this->pages = new PagesBuilder;
        add_action('admin_menu', array( $this, 'admin_menu' ));
    }
    public function admin_menu()
    {
        $this->pages->add_top_page('Form', 'example-form', array( $this, 'render_page' ));
    }
    public function render_page()
    {
        $this->pages->start();
        $form = new FormsBuilder;
        $form->form_start();
        $form->render_number(
            array(
                'name' => 'Number',
                'slug' => 'webprofit_WEBPROFIT_number',
                'type' => 'number',
                'description' => 'Number Description',
                'disabled' => false,
                'required' => true,
                'default' => 0,
                'min' => 0,
                'step' => 10,
                'max' => 100,
            )
        );
        $form->render_text(
            array(
                'name' => 'Text',
                'slug' => 'webprofit_WEBPROFIT_text',
                'type' => 'text',
                'description' => 'Text Description',
            )
        );
        $form->render_password(
            array(
                'name' => 'Password',
                'slug' => 'webprofit_WEBPROFIT_password',
                'type' => 'password',
                'description' => 'Password Description',
            )
        );
        $form->render_textarea(
            array(
                'name' => 'Textarea',
                'slug' => 'webprofit_WEBPROFIT_textarea',
                'type' => 'textarea',
                'description' => 'Textarea Description',
            )
        );
        $form->render_checkbox(
            array(
                'name' => 'Checkbox',
                'slug' => 'webprofit_WEBPROFIT_checkbox',
                'type' => 'checkbox',
                'description' => 'Checkbox Description',
            )
        );
        $form->render_radio(
            array(
                'name' => 'Radio',
                'slug' => 'webprofit_WEBPROFIT_radio',
                'type' => 'radio',
                'description' => 'Radio Description',
                'options' => array(
                    'option-1' => 'Option 1',
                    'option-2' => 'Option 2',
                    'option-3' => 'Option 3',
                ),
            )
        );
        $form->render_list(
            array(
                'name' => 'List',
                'slug' => 'webprofit_WEBPROFIT_list',
                'type' => 'list',
                'description' => 'List Description',
                'options' => array(
                    'option-1' => 'Option 1',
                    'option-2' => 'Option 2',
                    'option-3' => 'Option 3',
                ),
            )
        );
        $form->render_select(
            array(
                'name' => 'Select',
                'slug' => 'webprofit_WEBPROFIT_select',
                'type' => 'select',
                'description' => 'Select Description',
                'options' => array(
                    'option-1' => 'Option 1',
                    'option-2' => 'Option 2',
                    'option-3' => 'Option 3',
                ),
            )
        );
        $form->render_file(
            array(
                'name' => 'File',
                'slug' => 'webprofit_WEBPROFIT_file',
                'type' => 'file',
                'description' => 'File Description',
            )
        );
        $form->render_wysiwyg(
            array(
                'name' => 'WYSIWYG',
                'slug' => 'webprofit_WEBPROFIT_wysiwyg',
                'type' => 'wysiwyg',
                'description' => 'WYSIWYG Description <a href="https://codex.wordpress.org/Function_Reference/wp_editor" target="_blank">Lees meer</a>',
                'options' => array(
                    'media_buttons' => 'false',
                ),
            )
        );
        $form->render_color(
            array(
                'name' => 'Color',
                'slug' => 'webprofit_WEBPROFIT_color',
                'type' => 'color',
                'description' => 'Color Description',
            )
        );
        $form->form_end();
        $this->pages->end();
    }
}
new TestForm;
