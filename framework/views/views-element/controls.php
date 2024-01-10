<?php

global $wpdb;
$query = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'view'";
$results = $wpdb->get_results($query, OBJECT);

if ($results != null) {
    foreach ($results as $result) {
        $views[] = array( 'value' => $result->ID, 'label' => __($result->post_title, 'webprofit') );
    }
} else {
    $views[] = array( 'value' => '', 'label' => __('Create a view first', 'webprofit') );
}

return array(
    'view' => array(
        'type' => 'select',
        'ui' => array(
            'title' => __('Select a view', 'webprofit'),
            'tooltip' => __('Select a view', 'webprofit')
        ),
        'options' => array(
            'choices' => $views
        ),
    ),
    'display' => array(
        'type' => 'select',
        'ui'   => array(
          'title'   => __('Select what to display', 'webprofit'),
          'tooltip' => __('Select what to display of the view.', 'webprofit'),
        ),
        'options' => array(
          'choices' => array(
            array( 'value' => 'all',  'label' => __('All', 'webprofit')  ),
            array( 'value' => 'filter', 'label' => __('Filter', 'webprofit') ),
            array( 'value' => 'results',  'label' => __('Results', 'webprofit')  ),
          ),
        ),
    ),
);
