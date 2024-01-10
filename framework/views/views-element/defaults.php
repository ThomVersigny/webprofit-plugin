<?php

global $wpdb;
$query = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'view'";
$results = $wpdb->get_results($query, OBJECT);

if ($results != null) {
    $view = $results[0]->ID;
} else {
    $view = '';
}

return array(
    'id' => '',
    'class' => '',
    'style' => '',
    'color' => '',
    'view' => $view,
    'display' => 'all',
);
