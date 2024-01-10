<?php $classes = array( $class ); ?>
<div <?php cs_atts(array( 'id' => $id, 'class' => implode(' ', $classes), 'style' => $style ), true); ?>>
<?php
$html = null;
if ($display == 'all') {
    $html = '[wpv-view ';
} elseif ($display == 'filter') {
    $html = '[wpv-form-view target_id="self"';
} elseif ($display == 'results') {
    $html = '[wpv-view view_display="layout"';
}
if ($view != null) {
    $html .= ' id="'.$view.'"';
}
$html .= ']';
echo $html;
?></div>
