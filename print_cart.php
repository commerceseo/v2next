<?php

/* -----------------------------------------------------------------
 * 	$Id: print_cart.php 1200 2014-09-17 06:30:45Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

$cart_empty = false;
require("includes/application_top.php");
// $_SESSION['cart']->restore_contents();
// create smarty elements
$smarty = new Smarty;
// include needed functions
require_once(DIR_FS_INC . 'xtc_array_to_string.inc.php');
require_once(DIR_FS_INC . 'xtc_image_button.inc.php');
require_once(DIR_FS_INC . 'xtc_image_submit.inc.php');
require_once(DIR_FS_INC . 'xtc_recalculate_price.inc.php');
$smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE);
include(DIR_WS_MODULES . 'gift_cart.php');

if ($_SESSION['cart']->count_contents() > 0) {
    $hidden_options = '';
    $_SESSION['any_out_of_stock'] = 0;
    $products = $_SESSION['cart']->get_products();
    for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
        // Push all attributes information in an array
        if (isset($products[$i]['attributes'])) {
            while (list($option, $value) = each($products[$i]['attributes'])) {
                $hidden_options.= xtc_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
                $attributes_values = xtc_db_fetch_array(xtc_db_query("SELECT *
                                      FROM " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                      WHERE pa.products_id = '" . $products[$i]['id'] . "'
                                       AND pa.options_id = '" . $option . "'
                                       AND pa.options_id = popt.products_options_id
                                       AND pa.options_values_id = '" . $value . "'
                                       AND pa.options_values_id = poval.products_options_values_id
                                       AND popt.language_id = '" . (int) $_SESSION['languages_id'] . "'
                                       AND poval.language_id = '" . (int) $_SESSION['languages_id'] . "';"));

                $products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
                $products[$i][$option]['options_values_id'] = $value;
                $products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];
                $products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
                $products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
                $products[$i][$option]['weight_prefix'] = $attributes_values['weight_prefix'];
                $products[$i][$option]['options_values_weight'] = $attributes_values['options_values_weight'];
                $products[$i][$option]['attributes_stock'] = $attributes_values['attributes_stock'];
                $products[$i][$option]['products_attributes_id'] = $attributes_values['products_attributes_id'];
                $products[$i][$option]['attributes_model'] = $attributes_values['attributes_model'];
            }
        }
    }

    $smarty->assign('HIDDEN_OPTIONS', $hidden_options);
    require(DIR_WS_MODULES . 'order_details_print_cart.php');
    $_SESSION['allow_checkout'] = 'true';
    $smarty->assign('SHIPPING_INFO', SHIPPING_COSTS);
    $header = '<!DOCTYPE html>
	<html lang ="' . HTML_PARAMS . '">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=' . $_SESSION['language_charset'] . '" />
	';
    $smarty->assign('HEADER', $header);
    $smarty->assign('STORE_OWNER', nl2br(STORE_NAME_ADDRESS));
    if ($_GET['info_message']) {
        $smarty->assign('info_message', str_replace('+', ' ', $_GET['info_message']));
	}
} else {
    // empty cart
    $cart_empty = true;
    if ($_GET['info_message'])
        $smarty->assign('info_message', str_replace('+', ' ', $_GET['info_message']));
    $smarty->assign('cart_empty', $cart_empty);
    $smarty->assign('BUTTON_CONTINUE', '<a href="' . xtc_href_link(FILENAME_DEFAULT) . '">' . xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>');
}

$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;
if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/print_cart.html')) {
	$smarty->display(CURRENT_TEMPLATE.'/module/print_cart.html', USE_TEMPLATE_DEVMODE);
}else{
	$smarty->display('base/module/print_cart.html');
}