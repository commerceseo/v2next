<?php

/* -----------------------------------------------------------------
 * 	$Id: wish_list.php 659 2013-10-08 16:48:08Z akausch $
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
$_SESSION['wishList']->restore_contents();
$attributes = '';
$access_to_vars = get_object_vars($_SESSION['wishList']); //PASS WISHLIST TO ARRAY

if ($_GET['delete'] != '' && $_SESSION['wishList']->in_cart($_GET['delete'])) {
    $_SESSION['wishList']->remove($_GET['delete']);
    xtc_redirect(FILENAME_WISH_LIST);
}
if ($_GET['plus'] != '' && $_SESSION['wishList']->in_cart($_GET['plus'])) {
    $attributes = $access_to_vars['contents'][$_GET['plus']]['attributes']; // ASSIGN ATTRIBUTES
    $_SESSION['wishList']->update_quantity($_GET['plus'], $_SESSION['wishList']->get_quantity($_GET['plus']) + 1, $attributes);
    xtc_redirect(FILENAME_WISH_LIST);
}
if ($_GET['minus'] != '' && $_SESSION['wishList']->in_cart($_GET['minus'])) {
    $attributes = $access_to_vars['contents'][$_GET['minus']]['attributes']; // ASSIGN ATTRIBUTES
    $_SESSION['wishList']->update_quantity($_GET['minus'], $_SESSION['wishList']->get_quantity($_GET['minus']) - 1, $attributes);
    xtc_redirect(FILENAME_WISH_LIST);
}
if ($_GET['all_to_cart'] != '' && $_GET['all_to_cart'] == '1') {
    $_SESSION['wishList']->all_to_cart();
    xtc_redirect(FILENAME_SHOPPING_CART);
}
// SPECIFIC ITEM FROM WISHLIST TO CART
if ($_GET['action'] == 'add_product' && $_POST['products_id'] != '') {
    $_SESSION['wishList']->all_to_cart($_POST['products_id'], $access_to_vars['contents'][$_POST['products_id']]['qty'], $access_to_vars['contents'][$_POST['products_id']]['attributes']); //ADD TO CART
    $_SESSION['wishList']->remove($_POST['products_id']); //REMOVE FROM WISHLIST
    xtc_redirect(FILENAME_SHOPPING_CART);
}

// create smarty elements
$smarty = new Smarty;
require(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');
// include needed functions
require_once(DIR_FS_INC . 'xtc_array_to_string.inc.php');
require_once(DIR_FS_INC . 'xtc_image_button.inc.php');
require_once(DIR_FS_INC . 'xtc_image_submit.inc.php');
require_once(DIR_FS_INC . 'xtc_recalculate_price.inc.php');


$breadcrumb->add(NAVBAR_TITLE_WISH_LIST, xtc_href_link(FILENAME_WISH_LIST));

require(DIR_WS_INCLUDES . 'header.php');
include(DIR_WS_MODULES . 'gift_cart.php');

if ($_SESSION['wishList']->count_contents() > 0) {

    $smarty->assign('FORM_ACTION', xtc_draw_form('cart_quantity', xtc_href_link(FILENAME_WISH_LIST, 'action=update_product'), 'post', 'name="cart_quantity"'));
    $smarty->assign('FORM_END', xtc_draw_hidden_field('submit_target', 'cart') . "\n" . '</form>');
    $hidden_options = '';
    $_SESSION['any_out_of_stock'] = 0;

    $products = $_SESSION['wishList']->get_products();
    for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
        // Push all attributes information in an array
        if (isset($products[$i]['attributes'])) {
            while (list($option, $value) = each($products[$i]['attributes'])) {
                $hidden_options.= xtc_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
                $attributes = xtc_db_query("SELECT popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix,pa.attributes_stock,pa.products_attributes_id,pa.attributes_model
								  FROM " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
								  WHERE pa.products_id = '" . $products[$i]['id'] . "'
								   AND pa.options_id = '" . $option . "'
								   AND pa.options_id = popt.products_options_id
								   AND pa.options_values_id = '" . $value . "'
								   AND pa.options_values_id = poval.products_options_values_id
								   AND popt.language_id = '" . (int) $_SESSION['languages_id'] . "'
								   AND poval.language_id = '" . (int) $_SESSION['languages_id'] . "'");
                $attributes_values = xtc_db_fetch_array($attributes);

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
    require(DIR_WS_MODULES . 'order_details_wishlist.php');

    if (STOCK_CHECK == 'true') {
        if ($_SESSION['any_out_of_stock'] == 1) {
            if (STOCK_ALLOW_CHECKOUT == 'true') {
                // write permission in session
                $_SESSION['allow_checkout'] = 'true';

                $smarty->assign('info_message', OUT_OF_STOCK_CAN_CHECKOUT);
            } else {
                $_SESSION['allow_checkout'] = 'false';
                $smarty->assign('info_message', OUT_OF_STOCK_CANT_CHECKOUT);
            }
        } else {
            $_SESSION['allow_checkout'] = 'true';
        }
    }

    $smarty->assign('SHIPPING_INFO', '<a title="' . SHIPPING_COSTS . '" rel="nofollow" class="shipping" href="' . xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . SHIPPING_INFOS) . '"> ' . SHIPPING_COSTS . '</a>');

    if ($_GET['info_message'])
        $smarty->assign('info_message', str_replace('+', ' ', $_GET['info_message']));
    $smarty->assign('BUTTON_BACK', '<a href="javascript:history.back();">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
    $smarty->assign('BUTTON_CHECKOUT', '<a href="' . xtc_href_link(FILENAME_WISH_LIST, 'all_to_cart=1', 'SSL') . '">' . xtc_image_button('button_checkout.gif', IMAGE_BUTTON_ALL_WISH) . '</a>');
    $smarty->assign('WISH_PRINT', '<a href="javascript:void(0)" onclick="javascript:window.open(\'' . xtc_href_link('print_wish_list.php') . '\', \'popup\', \'toolbar=0, width=640, height=600\')">' . xtc_image_button('print.gif', IMAGE_BUTTON_PRINT) . '</a>');
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
if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/wish_list.html')) {
	$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/wish_list.html', USE_TEMPLATE_DEVMODE));
}else{
	$main_content = $smarty->fetch(cseo_get_usermod('base/module/wish_list.html', USE_TEMPLATE_DEVMODE));
}
$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
