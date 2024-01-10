<?php

class FormsBuilder
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ));
    }
    public function admin_enqueue_scripts()
    {
        wp_enqueue_media();
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('jquery');
    }
    public function form_start()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        ?><form method="post" action="options.php"><?php
    }
    public function form_submit()
    {
        submit_button();
        do_action('wep_form_after_submit');
        $this->scripts();
    }
    public function form_end()
    {
        $this->form_submit(); ?></form><?php
    }

    // Render
    public function render_default($args)
    {
        $slug = isset($args['slug']) ? $args['slug'] : '';
        $class = isset($args['class']) ? $args['class'] : '';
        $option = $this->get_option($slug);

        $html = $this->get_description($args);
        echo $html;
    }

    public function render_text($args)
    {
        $slug = isset($args['slug']) ? $args['slug'] : '';
        $class = isset($args['class']) ? $args['class'] : '';
        $option = $this->get_option($slug);

        $html = '<input type="text" ';
        $html .= $this->get_option_attributes($args);
        $html .= '>';

        $html .= $this->get_description($args);
        echo $html;
    }

    public function render_number($args)
    {
        extract($args);
        $option = $this->get_option($slug, $args['default']);

        $html = '<input type="number" ';
        $html .= ! empty($slug) ? "id='{$slug}' name='{$slug}' " : '';
        $html .= ! empty($class) ? "class='regular-text {$class}' " : "class='regular-text' ";
        $html .= ! empty($option) ? "value='{$option}' " : '';
        $html .= ! empty($placeholder) ? "placeholder='{$placeholder}'" : '';
        $html .= ! empty($required) ? 'required ' : '';
        $html .= ! empty($disabled) ? 'disabled ' : '';
        $html .= isset($min) ? "min='{$min}' " : '';
        $html .= isset($max) ? "max='{$max}' " : '';
        $html .= isset($step) ? "step='{$step}' " : '';
        $html .= '>';
        $html .= $this->get_description($args);
        echo $html;
    }

    public function render_date($args)
    {
        $html = '<input type="date" ';
        $html .= $this->get_option_attributes($args);
        $html .= '>';

        $html .= $this->get_description($args);
        echo $html;
    }
    public function render_time($args)
    {
        $html = '<input type="time" ';
        $html .= $this->get_option_attributes($args);
        $html .= '>';

        $html .= $this->get_description($args);
        echo $html;
    }

    public function render_textarea($args)
    {
        $slug = isset($args['slug']) ? $args['slug'] : '';
        $class = isset($args['class']) ? $args['class'] : '';
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        $option = $this->get_option($slug);

        $html = "<textarea rows='5' cols='55' class='{$class}' id='{$slug}' name='{$slug}' placeholder='{$placeholder}'>{$option}</textarea>";
        $html .= $this->get_description($args);
        echo $html;
    }
    public function render_wysiwyg($args)
    {
        $slug = isset($args['slug']) ? $args['slug'] : '';
        $class = isset($args['class']) ? $args['class'] : '';
        $option = $this->get_option($slug);

        $html = wp_editor($option, $slug, isset($args['options']) ? $args['options'] : '');
        $html .= $this->get_description($args);
        echo $html;
    }

    public function render_checkbox($args)
    {
        $slug = isset($args['slug']) ? $args['slug'] : '';
        $class = isset($args['class']) ? $args['class'] : '';
        $option = $this->get_option($slug);

        $checked = $option == 1 || $args['default'] == 1 ? 'checked' : '';

        $html = '<label>';
        $html .= "<input type='checkbox' class='checkbox {$class}' id='{$slug}' name='{$slug}' {$checked} value='1'/>";
        $html .= '&nbsp;'. $this->get_description($args, false);
        $html .= '</label>';
        echo $html;
    }
    public function render_radio($args)
    {
        $slug = isset($args['slug']) ? $args['slug'] : '';
        $class = isset($args['class']) ? $args['class'] : '';
        $option = $this->get_option($slug);

        $html = '';
        foreach ($args['options'] as $key=>$value) {
            $checked = $option == $key ? 'checked' : '';
            $html .= "<input type='radio' class='checkbox {$class}' name='{$slug}' value='{$key}' {$checked}/>{$value}<br>";
        }
        $html .= $this->get_description($args);
        echo $html;
    }
    public function render_select($args)
    {
        $slug = isset($args['slug']) ? $args['slug'] : '';
        $class = isset($args['class']) ? $args['class'] : '';
        $option = $this->get_option($slug);
        
        $html = "<select id='{$slug}' name='{$slug}'>";
        foreach ($args['options'] as $key=>$value) {
            $checked = $option == $key ? 'selected' : '';
            $html .= "<option value='{$key}' $checked>{$value}</option>";
        }
        $html .= '</select>';
        $html .= $this->get_description($args);
        echo $html;
    }
    public function render_list($args)
    {
        $slug = isset($args['slug']) ? $args['slug'] : '';
        $class = isset($args['class']) ? $args['class'] : '';
        $option = $this->get_option($slug);

        $html = "<input list='{$slug}' name='{$slug}' value='{$option}' class='regular-text {$class}'>";
        $html .= "<datalist id='{$slug}'>";
        foreach ($args['options'] as $value) {
            $html .= "<option value='{$value}'>{$value}</option>";
        }
        $html .= "</datalist>";
        $html .= $this->get_description($args);
        echo $html;
    }

    public function render_file($args)
    {
        $slug = isset($args['slug']) ? $args['slug'] : '';
        $class = isset($args['class']) ? $args['class'] : '';
        $default = isset($args['default']) ? $args['default'] : '';
        $option = $this->get_option($slug);

        $html = "<div class='wp-file'>";
        $html .= "<div><img class='wp-image' src='{$option}'></div>";
        $html .= "<input type='text' class='wp-url regular-text {$class}' id='{$slug}' name='{$slug}' value='{$option}'/>";
        $html .= "<input type='button' class='button wp-browse' value='Select'/>";
        $html .= "</div>";
        $html .= $this->get_description($args);
        echo $html;
    }
    public function render_color($args)
    {
        $slug = isset($args['slug']) ? $args['slug'] : '';
        $class = isset($args['class']) ? $args['class'] : '';
        $default = isset($args['default']) ? $args['default'] : '';
        $option = $this->get_option($slug);

        $html = "<input type='text' class='wp-color-picker-field {$class}' id='{$slug}' name='{$slug}' value='{$option}' data-default-color='{$default}'/>";
        $html .= $this->get_description($args);
        echo $html;
    }
    public function render_password($args)
    {
        $slug = isset($args['slug']) ? $args['slug'] : '';
        $class = isset($args['class']) ? $args['class'] : '';
        $option = $this->get_option($slug);

        $html = "<input type='password' id='{$slug}' name='{$slug}' value='{$option}' class='regular-text {$class}'>";
        $html .= $this->get_description($args);
        echo $html;
    }
    public function render_url($args)
    {
        $html = '<input type="url" ';
        $html .= $this->get_option_attributes($args);
        $html .= '>';

        $html .= $this->get_description($args);
        echo $html;
    }
    
    public function render_page($args)
    {
        $slug = isset($args['slug']) ? $args['slug'] : '';
        $class = isset($args['class']) ? $args['class'] : '';
        $option = $this->get_option($slug);
        $pages = get_all_page_ids();

        $html = "<select id='{$slug}' name='{$slug}'>";
        foreach ($pages as $key) {
            $checked = $option == $key ? 'selected' : '';
            $value = get_the_title($key);
            $html .= "<option value='{$key}' $checked>{$value}</option>";
        }
        $html .= '</select>';
        $html .= $this->get_description($args);
        echo $html;
    }

    // Getters
    private function get_description($args, $bool = true)
    {
        if (isset($args['description']) && $bool) {
            $description = '<p class="description">' . $args['description'] . '</p>';
        } elseif ($bool == false) {
            $description = $args['description'];
        } else {
            $description = '';
        }
        return $description;
    }
    private function get_option($id, $default = '')
    {
        $option = get_option($id);
        if (isset($option)) {
            return $option;
        }
        return $default;
    }
    private function get_option_attributes($args)
    {
        extract($args);
        $option = $this->get_option($slug);
        $html = ! empty($slug) ? "id='{$slug}' name='{$slug}' " : '';
        $html .= ! empty($class) ? "class='regular-text {$class}' " : "class='regular-text' ";
        $html .= ! empty($placeholder) ? "placeholder='{$placeholder}'" : '';
        $html .= ! empty($required) ? 'required ' : '';
        $html .= ! empty($disabled) ? 'disabled ' : '';
        $html .= ! empty($option) ? "value='{$option}' " : '';
        $html .= isset($min) ? "min='{$min}' " : '';
        $html .= isset($max) ? "max='{$max}' " : '';
        $html .= isset($step) ? "step='{$step}' " : '';
        return $html;
    }

    private function scripts()
    {
        ?><script>
            jQuery(document).ready(function($) {
                // Color picker
                $('.wp-color-picker-field').wpColorPicker();
                // Media
                $('.wp-browse').on('click', function(event) {
                    event.preventDefault();
                    var self = $(this);
                    // Create the media frame.
                    var file_frame = wp.media.frames.file_frame = wp.media({
                        title: self.data('uploader_title'),
                        button: {
                            text: self.data('uploader_button_text'),
                        },
                        multiple: false
                    });
                    file_frame.on('select', function() {
                        attachment = file_frame.state().get('selection').first().toJSON();
                        self.prev('.wp-url').val(attachment.url).change();
                        self.closest('.wp-image').attr( 'src', attachment.url );
                    });
                    // Open the media frame
                    file_frame.open();
                });
                // Show select all button only if 3 or more select boxes
                if ($(".wep-options :checkbox").length > 2) {
                    $("#submit").after('&nbsp;&nbsp;<div class="button select-all" data-select="1">Selecteer alles</div>');
                }
                // Toggle all checkboxes
                $(".select-all").click(function() {
                    if ($(this).data('select') == 1) {
                        $(this).data('select', 0);
                        $(".wep-options input:checkbox").each(function() {
                            $(this).prop('checked', true);
                        });
                    } else {
                        $(this).data('select', 1);
                        $(".wep-options input:checkbox").each(function() {
                            $(this).prop('checked', false);
                        });
                    }
                });
            });
        </script><style type="text/css">
            .wp-image {
                width: 100px;
            }
            input.regular-text {
                border: 1px solid #ddd;
                box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
                background-color: #fff;
                color: #32373c;
                outline: 0;
                transition: 50ms border-color ease-in-out;
            }
        </style><?php
    }
}
