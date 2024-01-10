<?php

// Hide shipping methodes if free shipping is available
// https://businessbloomer.com/woocommerce-hide-shipping-options-free-shipping-available/
function wep_woocommerce_unset_shipping_when_free_is_available_all_zones($rates, $package)
{
    $all_free_rates = array();
     
    foreach ($rates as $rate_id => $rate) {
        if ('free_shipping' === $rate->method_id) {
            $all_free_rates[ $rate_id ] = $rate;
            break;
        }
    }

    if (empty($all_free_rates)) {
        return $rates;
    } else {
        return $all_free_rates;
    }
}
if (get_option('webprofit_woocommerce_hide_shipping_methodes_when_free_is_available') == 1) {
    add_filter('woocommerce_package_rates', 'wep_woocommerce_unset_shipping_when_free_is_available_all_zones', 10, 2);
}
