<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>WebProfit Maintenance</title>
</head>
<body>
<style>
<?php
if (get_option('webprofit_maintenance_custom_css') != null) {
    echo get_option('webprofit_maintenance_custom_css');
}
?>
html, body {
    overflow: hidden !important;
    height: 100% !important;
    background: none;
}
body {
    background-color: #34495e;
    background-image: url(<?php echo((get_option('webprofit_maintenance_image') != null) ? (get_option('webprofit_maintenance_image')) : WEBPROFIT_PLUGIN_URL . 'assets/img/banner-maintenance.jpg') ?>);
	background-position: 50% 50%;
    background-repeat: no-repeat;
    -webkit-background-size: cover;
    background-size: cover;
}
.wep-overlay {
	position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    overflow-x: hidden;
    overflow-y: auto;
    z-index: 99999;
}
.wep-outer {
	display: table;
    width: 100%;
    height: 100%;
}
.wep-inner {
	display: table-cell;
    vertical-align: middle;
    padding: 55px 35px;
}
.wep-wrapper {
	display: block;
    overflow: auto;
    margin: 0 auto;
    max-width: 600px;
    font-family: Lato, "Helvetica Neue", Helvetica, sans-serif;
    text-align: center;
	background-color: rgb(204,204,204);
    padding: 20px;
}
.wep-wrapper h1 {
    margin: 0 0 25px;
    font-family: Lato, "Helvetica Neue", Helvetica, sans-serif;
    font-size: 48px;
    font-weight: 300;
    line-height: 1;
    color: #ffffff;
}
.wep-wrapper h2 {
    margin: 0;
    font-family: Lato, "Helvetica Neue", Helvetica, sans-serif;
	font-size: 24px;
    font-weight: 300;
    line-height: 1.4;
    color: #ffffff;
}
.wep-wrapper p {
    margin: 20px 0 0 0;
    font-family: Lato, "Helvetica Neue", Helvetica, sans-serif;
    font-size: 16px;
    font-weight: 300;
    line-height: 1.2;
	color: #ffffff;
}
.wep-wrapper a {
	color: #f28a00;
}
@media (max-width: 768px) {
    .wep-wrapper h1 {
    	font-size: 32px;
    }
    .wep-wrapper h2 {
    	font-size: 21px;
    }

    .wep-wrapper p {
        font-size: 16px;
    }
}
</style>
<div class="wep-overlay"><div class="wep-outer"><div class="wep-inner"><div class="wep-wrapper">
<h1><?php echo((get_option('webprofit_maintenance_title') != null) ? (get_option('webprofit_maintenance_title')) : 'Nog even geduld...') ?></h1>
<h2><?php echo((get_option('webprofit_maintenance_subtitle') != null) ? (get_option('webprofit_maintenance_subtitle')) : 'Deze website wordt ontwikkeld door WebProfit') ?></h2>
<?php if (get_option('webprofit_maintenance_login') != null) {
    ?>
<p><a href="<?php echo wp_login_url(); ?>">Login</a></p>
<?php
} ?>
</div></div></div></div>
</body>
</html>