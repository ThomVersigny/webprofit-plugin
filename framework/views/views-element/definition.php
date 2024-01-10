<?php

class Toolset_Views_Element
{
    public function ui()
    {
        return array(
            'title' => __('Views', 'webprofit'),
            'autofocus' => array(
                'heading' => 'h4.views-element-heading',
                'content' => '.views-element'
            ),
            'icon_group' => 'views-element',
            'icon_id' => 'views-element',
        );
    }
}
