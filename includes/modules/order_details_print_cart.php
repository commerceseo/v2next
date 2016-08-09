<?php

/* -----------------------------------------------------------------
 * 	$Id: order_details_print_cart.php 1200 2014-09-17 06:30:45Z akausch $
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

require_once(DIR_FS_INC . 'xtc_check_stock.inc.php');
require_once(DIR_FS_INC . 'xtc_get_products_stock.inc.php');
require_once(DIR_FS_INC . 'xtc_remove_non_numeric.inc.php');
require_once(DIR_FS_INC . 'xtc_get_short_description.inc.php');
require_once(DIR_FS_INC . 'xtc_format_price.inc.php');

$module_content = array();
$any_out_of_stock = '';
$mark_stock = '';

for ($i = 0, $n = sizeof($products); $i < $n; $i++) {

    if (STOCK_CHECK == 'true') {
        $mark_stock = xtc_check_stock($products[$i]['id'], $products[$i]['quantity']);
        if ($mark_stock)
            $_SESSION['any_out_of_stock'] = 1;
    }

    $image = '';
    if ($products[$i]['image'] != '') {
        $image = DIR_WS_THUMBNAIL_IMAGES . $products[$i]['image'];
    } else {
        $image = DIR_WS_THUMBNAIL_IMAGES . 'no_img.jpg';
    }
    if ($mark_stock) {
        $products_shipping_time = '';
    } else {
        $products_shipping_time = $products[$i]['shipping_time'];
    }

    $module_content[$i] = array(
        'PRODUCTS_NAME' => $products[$i]['name'],
        'PRODUCTS_QTY' => $products[$i]['quantity'],
        'PRODUCTS_MODEL' => $products[$i]['model'],
        'PRODUCTS_SHIPPING_TIME' => $products[$i]['shipping_time'],
        'PRODUCTS_TAX' => number_format($products[$i]['tax'], TAX_DECIMAL_PLACES),
        'PRODUCTS_IMAGE' => $image,
        'PRODUCTS_VPE' => $products[$i]['vpe'],
        'PRODUCTS_WEIGHT' => $products[$i]['weight'],
        'IMAGE_ALT' => $products[$i]['name'],
        'PRODUCTS_PRICE' => $xtPrice->xtcFormat($products[$i]['price'] * $products[$i]['quantity'], true),
        'PRODUCTS_SINGLE_PRICE' => $xtPrice->xtcFormat($products[$i]['price'], true),
        'PRODUCTS_SHORT_DESCRIPTION' => strip_tags(xtc_get_short_description($products[$i]['id'])),
        'ATTRIBUTES' => '',
        'BUY_NOW' => xtc_image_submit('button_buy_now.gif', TEXT_NOW));

    // Product options names
    $attributes_exist = ((isset($products[$i]['attributes'])) ? 1 : 0);

    if ($attributes_exist == 1) {
        reset($products[$i]['attributes']);

        while (list($option, $value) = each($products[$i]['attributes'])) {

            if (ATTRIBUTE_STOCK_CHECK == 'true' && STOCK_CHECK == 'true') {
                $attribute_stock_check = xtc_check_stock_attributes($products[$i][$option]['products_attributes_id'], $products[$i]['quantity']);
                if ($attribute_stock_check)
                    $_SESSION['any_out_of_stock'] = 0;
            }
            $module_content[$i]['ATTRIBUTES'][] = array(
                'ID' => $products[$i][$option]['products_attributes_id'],
                'MODEL' => $products[$i][$option]['attributes_model'],
                'NAME' => $products[$i][$option]['products_options_name'],
                'PRICE' => ($products[$i][$option]['products_options_name'] != 'Downloads') ? $xtPrice->xtcFormat($products[$i][$option]['options_values_price'] * $products[$i]['quantity'], true) : '',
                'PREFIX' => ($products[$i][$option]['products_options_name'] != 'Downloads') ? $products[$i][$option]['price_prefix'] : '',
                'VALUE_NAME' => $products[$i][$option]['products_options_values_name'] . $attribute_stock_check
            );
        }
    }
}

$total_content = '';
if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1 && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00') {
    $discount = xtc_recalculate_price($_SESSION['wishList']->show_total(), $_SESSION['customers_status']['customers_status_ot_discount']);
    $total_content = $_SESSION['customers_status']['customers_status_ot_discount'] . ' % ' . SUB_TITLE_OT_DISCOUNT . ' -' . xtc_format_price($discount, $price_special = 1, $calculate_currencies = false) . '<br />';
}

if ($_SESSION['customers_status']['customers_status_show_price'] == '1') {
    $total_content.= $xtPrice->xtcFormat($_SESSION['wishList']->show_total(), true);
} else {
    $total_content.= TEXT_INFO_SHOW_PRICE_NO;
}

if ($customer_status_value['customers_status_ot_discount'] != 0) {
    $total_content.= TEXT_CART_OT_DISCOUNT . $customer_status_value['customers_status_ot_discount'] . '%';
}

if ($_SESSION['customers_status']['customers_status_show_price'] == '1') {
    if (sizeof($products) == 1)
        $info_text = sprintf(HEAD_INFO_TXT, sizeof($products)) . $total_content;
    else
        $info_text = sprintf(HEAD_INFO_TXT_MORE, sizeof($products)) . $total_content;
}
$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
$module_smarty->assign('HEAD_INFO', $info_text);
$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$module_smarty->assign('module_content', $module_content);
$module_smarty->caching = false;
if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/print_cart_details.html')) {
	$module = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/print_cart_details.html', USE_TEMPLATE_DEVMODE);
}else{
	$module = $module_smarty->fetch('base/module/print_cart_details.html');
}
$smarty->assign('MODULE_order_details', $module);
