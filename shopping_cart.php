<?php

/* -----------------------------------------------------------------
 * 	$Id: shopping_cart.php 1472 2015-07-22 20:35:15Z akausch $
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
require ("includes/application_top.php");

$attributes = '';
$access_to_vars = get_object_vars($_SESSION['cart']);

if ($_GET['delete'] != '' && $_SESSION['cart']->in_cart($_GET['delete'])) {
    $_SESSION['cart']->remove($_GET['delete']);
    xtc_redirect((($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . FILENAME_SHOPPING_CART);
}
if ($_GET['del'] != '' && $_SESSION['cart']->in_cart($_GET['del'])) {
    $_SESSION['cart']->remove($_GET['del']);
    xtc_redirect((($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . FILENAME_SHOPPING_CART);
}
if ($_GET['plus'] != '' && $_SESSION['cart']->in_cart($_GET['plus'])) {
    $attributes = $access_to_vars['contents'][$_GET['plus']]['attributes']; // ASSIGN ATTRIBUTES
    $_SESSION['cart']->update_quantity($_GET['plus'], $_SESSION['cart']->get_quantity($_GET['plus']) + 1, $attributes);
    xtc_redirect((($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . FILENAME_SHOPPING_CART);
}
if ($_GET['minus'] != '' && $_SESSION['cart']->in_cart($_GET['minus'])) {
    $attributes = $access_to_vars['contents'][$_GET['minus']]['attributes']; // ASSIGN ATTRIBUTES
    $_SESSION['cart']->update_quantity($_GET['minus'], $_SESSION['cart']->get_quantity($_GET['minus']) - 1, $attributes);
    xtc_redirect((($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . FILENAME_SHOPPING_CART);
}

$smarty = new Smarty;

require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

// include needed functions
require_once (DIR_FS_INC . 'xtc_array_to_string.inc.php');
require_once (DIR_FS_INC . 'xtc_image_submit.inc.php');
require_once (DIR_FS_INC . 'xtc_recalculate_price.inc.php');

$breadcrumb->add(NAVBAR_TITLE_SHOPPING_CART, xtc_href_link(FILENAME_SHOPPING_CART));

require_once (DIR_WS_INCLUDES . 'header.php');
if (!isset($_SESSION['paypal_warten'])) {
    include (DIR_WS_MODULES . 'gift_cart.php');
}

// PayPal Express abgelehnt und erneut aufrufen!
if (isset($_SESSION['reshash']['ACK']) && strtoupper($_SESSION['reshash']['ACK']) != "SUCCESS" && strtoupper($_SESSION['reshash']['ACK']) != "SUCCESSWITHWARNING") {
    if (isset($_SESSION['reshash']['REDIRECTREQUIRED']) && strtoupper($_SESSION['reshash']['REDIRECTREQUIRED']) == "TRUE") {
        require_once (DIR_WS_CLASSES . 'class.payment.php');
        $payment_modules = new payment($_SESSION['payment']);
        $_SESSION['paypal_fehler'] = ((PAYPAL_FEHLER) ? PAYPAL_FEHLER : 'PayPal Fehler...<br />');
        $_SESSION['paypal_warten'] = ((PAYPAL_WARTEN) ? PAYPAL_WARTEN : 'Sie m&uuml;ssen noch einmal zu PayPal. <br />');
        $payment_modules->giropay_process();
    }
}
unset($_SESSION['paypal_express_checkout']);

// Paypal Error Messages:
if (isset($_SESSION['paypal_fehler']) && !isset($_SESSION['paypal_warten'])) {
    if (!isset($_SESSION['reshash']['ACK']) && strtoupper($_SESSION['reshash']['ACK']) != "SUCCESS" && strtoupper($_SESSION['reshash']['ACK']) != "SUCCESSWITHWARNING") {
        $o_paypal->paypal_second_auth_call($_SESSION['tmp_oID']);
        xtc_redirect($o_paypal->payPalURL);
    }
    if (isset($_SESSION['reshash']['ACK']) && (strtoupper($_SESSION['reshash']['ACK']) == "SUCCESS" || strtoupper($_SESSION['reshash']['ACK']) == "SUCCESSWITHWARNING")) {
        $o_paypal->paypal_get_customer_data();
        if ($data['PayerID'] || $_SESSION['reshash']['PAYERID']) {
            require_once (DIR_WS_CLASSES . 'class.order.php');
            $data = array_merge($_SESSION['nvpReqArray'], $_SESSION['reshash']);
            $data = array_merge($data, $GET);
            $o_paypal->complete_ceckout($_SESSION['tmp_oID'], $data);
            $o_paypal->write_status_history($_SESSION['tmp_oID']);
            $o_paypal->logging_status($_SESSION['tmp_oID']);
        }
    }
    $_SESSION['cart']->reset(true);
    // unregister session variables used during checkout
    $last_order = $_SESSION['tmp_oID'];
    unset($_SESSION['sendto']);
    unset($_SESSION['billto']);
    unset($_SESSION['shipping']);
    //  unset ($_SESSION['payment']);
    unset($_SESSION['comments']);
    unset($_SESSION['last_order']);
    unset($_SESSION['tmp_oID']);
    unset($_SESSION['cc']);
    //GV Code Start
    if (isset($_SESSION['credit_covers'])) {
        unset($_SESSION['credit_covers']);
    }
    require_once (DIR_WS_CLASSES . 'class.order_total.php');
    $order_total_modules = new order_total();
    $order_total_modules->clear_posts(); //ICW ADDED FOR CREDIT CLASS SYSTEM
    // GV Code End
    if (isset($_SESSION['reshash']['ACK']) && (strtoupper($_SESSION['reshash']['ACK']) == "SUCCESS" || strtoupper($_SESSION['reshash']['ACK']) == "SUCCESSWITHWARNING")) {
        $redirect = ((isset($_SESSION['reshash']['REDIRECTREQUIRED']) && strtoupper($_SESSION['reshash']['REDIRECTREQUIRED']) == "TRUE") ? true : false);
        $o_paypal->paypal_get_customer_data();
        if ($data['PayerID'] || $_SESSION['reshash']['PAYERID']) {
            if ($redirect) {
                unset($_SESSION['paypal_fehler']);
                require_once (DIR_WS_CLASSES . 'class.payment.php');
                $payment_modules = new payment('paypalexpress');
                $payment_modules->giropay_process();
            }
            $weiter = true;
        }
        unset($_SESSION['payment']);
        unset($_SESSION['nvpReqArray']);
        unset($_SESSION['reshash']);
        if ($weiter) {
            unset($_SESSION['paypal_fehler']);
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL'));
        }
    } else {
        unset($_SESSION['payment']);
        unset($_SESSION['nvpReqArray']);
        unset($_SESSION['reshash']);
    }
    $smarty->assign('error', $_SESSION['paypal_fehler']);
    unset($_SESSION['paypal_fehler']);
}

if ($_SESSION['cart']->count_contents() > 0) {
    if (!isset($_SESSION['paypal_warten'])) {
        // Normaler Warenkorb
        $smarty->assign('FORM_ACTION', xtc_draw_form('cart_quantity', xtc_href_link(FILENAME_SHOPPING_CART, 'action=update_product', 'SSL')));
        $smarty->assign('FORM_END', '</form>');
        $hidden_options = '';
        $_SESSION['any_out_of_stock'] = 0;

        //Mindestbestellmenge
        $_SESSION['any_out_of_minorder_products'] = array();

        $products = $_SESSION['cart']->get_products();
        for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
            // Push all attributes information in an array
            if (isset($products[$i]['attributes'])) {
                while (list ($option, $value) = each($products[$i]['attributes'])) {
                    $hidden_options .= xtc_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
                    $attributes_values = xtc_db_fetch_array(xtc_db_query("SELECT 
													*
												FROM 
													" . TABLE_PRODUCTS_OPTIONS . " popt 
												INNER JOIN
													" . TABLE_PRODUCTS_OPTIONS_VALUES . " poval ON(poval.language_id = '" . (int) $_SESSION['languages_id'] . "')
												INNER JOIN
													" . TABLE_PRODUCTS_ATTRIBUTES . " pa ON(pa.options_id = popt.products_options_id AND pa.products_id = '" . $products[$i]['id'] . "' AND pa.options_id = '" . $option . "' AND pa.options_values_id = '" . $value . "')
												WHERE 
													pa.options_values_id = poval.products_options_values_id
												AND 
													popt.language_id = '" . (int) $_SESSION['languages_id'] . "';"));
                    $products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
                    $products[$i][$option]['options_values_scale_price'] = $attributes_values['options_values_scale_price'];
                    $products[$i][$option]['options_values_id'] = $value;
                    $products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];
                    $products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
                    $products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
                    $products[$i][$option]['weight_prefix'] = $attributes_values['weight_prefix'];
                    $products[$i][$option]['options_values_weight'] = $attributes_values['options_values_weight'];
                    $products[$i][$option]['attributes_stock'] = $attributes_values['attributes_stock'];
                    $products[$i][$option]['products_attributes_id'] = $attributes_values['products_attributes_id'];
                    $products[$i][$option]['attributes_ean'] = $attributes_values['attributes_ean'];
                    $products[$i][$option]['attributes_vpe_status'] = $attributes_values['attributes_vpe_status'];
                    $products[$i][$option]['attributes_vpe'] = $attributes_values['attributes_vpe'];
                    $products[$i][$option]['attributes_vpe_value'] = $attributes_values['attributes_vpe_value'];
                    $products[$i][$option]['attributes_shippingtime'] = $attributes_values['attributes_shippingtime'];
                    $products[$i][$option]['attributes_model'] = $attributes_values['attributes_model'];
                }
            }
            if (isset($products[$i]['freitext'])) {
                while (list ($option, $value) = each($products[$i]['freitext'])) {
                    while (list ($foption, $fvalue) = each($value)) {
                        $hidden_options .= xtc_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
                        $attributes_values = xtc_db_fetch_array(xtc_db_query("SELECT 
													*
												FROM 
													" . TABLE_PRODUCTS_OPTIONS . " popt 
												INNER JOIN
													" . TABLE_PRODUCTS_OPTIONS_VALUES . " poval ON(poval.language_id = '" . (int) $_SESSION['languages_id'] . "')
												INNER JOIN
													" . TABLE_PRODUCTS_ATTRIBUTES . " pa ON(pa.options_id = popt.products_options_id AND pa.products_id = '" . $products[$i]['id'] . "' AND pa.options_id = '" . $option . "' )
												WHERE 
													pa.options_values_id = poval.products_options_values_id
												AND 
													popt.language_id = '" . (int) $_SESSION['languages_id'] . "';"));
                        $products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
                        $products[$i][$option]['options_values_scale_price'] = $attributes_values['options_values_scale_price'];
                        $products[$i][$option]['options_values_id'] = $fvalue;
                        $products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];
                        $products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
                        $products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
                        $products[$i][$option]['weight_prefix'] = $attributes_values['weight_prefix'];
                        $products[$i][$option]['options_values_weight'] = $attributes_values['options_values_weight'];
                        $products[$i][$option]['attributes_stock'] = $attributes_values['attributes_stock'];
                        $products[$i][$option]['products_attributes_id'] = $attributes_values['products_attributes_id'];
                        $products[$i][$option]['attributes_ean'] = $attributes_values['attributes_ean'];
                        $products[$i][$option]['attributes_vpe_status'] = $attributes_values['attributes_vpe_status'];
                        $products[$i][$option]['attributes_vpe'] = $attributes_values['attributes_vpe'];
                        $products[$i][$option]['attributes_vpe_value'] = $attributes_values['attributes_vpe_value'];
                        $products[$i][$option]['attributes_shippingtime'] = $attributes_values['attributes_shippingtime'];
                        $products[$i][$option]['attributes_model'] = $attributes_values['attributes_model'];
                    }
                }
            }
        }
        $smarty->assign('HIDDEN_OPTIONS', $hidden_options);
        require_once (DIR_WS_MODULES . 'order_details_cart.php');
        $_SESSION['allow_checkout'] = 'true';
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
        //Mindestbestellmenge
        if (sizeof($_SESSION['any_out_of_minorder_products']) > 0) {
            $_SESSION['allow_checkout'] = 'false';
            $message = TEXT_MINORDER . '<br />';
            foreach ($_SESSION['any_out_of_minorder_products'] as $minorder_data) {
                $message .= '<br />' . $minorder_data['name'] . ' (' . TEXT_MINORDER_TITLE . ': ' . $minorder_data['minorder'] . ')';
            }
            $smarty->assign('error', $message);
        }
        //Maxbestellmenge
        if (sizeof($_SESSION['any_out_of_maxorder_products']) > 0) {
            $_SESSION['allow_checkout'] = 'false';
            $message = TEXT_MAXORDER . '<br />';
            foreach ($_SESSION['any_out_of_maxorder_products'] as $maxorder_data) {
                $message .= '<br />' . $maxorder_data['name'] . ' (' . TEXT_MAXORDER_TITLE . ': ' . $maxorder_data['maxorder'] . ')';
            }
            $smarty->assign('error', $message);
        }
    } else {
        // 2. PayPal Aufruf - nur anzeigen
        require_once (DIR_WS_CLASSES . 'class.order.php');
        $order = new order((int) $_SESSION['tmp_oID']);
        $smarty->assign('language', $_SESSION['language']);
        if ($order->delivery != false) {
            $smarty->assign('DELIVERY_LABEL', xtc_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'));
            if ($order->info['shipping_method']) {
                $smarty->assign('SHIPPING_METHOD', $order->info['shipping_method']);
            }
        }
        $order_total = $order->getTotalData((int) $_SESSION['tmp_oID']);
        $smarty->assign('order_data', $order->getOrderData((int) $_SESSION['tmp_oID']));
        $smarty->assign('order_total', $order_total['data']);
        $smarty->assign('BILLING_LABEL', xtc_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />'));
        $smarty->assign('ORDER_NUMBER', $_SESSION['tmp_oID']);
        $smarty->assign('ORDER_DATE', xtc_date_long($order->info['date_purchased']));
        $smarty->assign('ORDER_STATUS', $order->info['orders_status']);
        $history_block = '<table summary="order history">';
        $order_content = $smarty->fetch(CURRENT_TEMPLATE . '/module/account_history_info.html');

        $smarty->assign('info_message_1', $order_content);
        $smarty->assign('FORM_ACTION', '<br />' . $o_paypal->build_express_fehler_button() . '<br />' . PAYPAL_NEUBUTTON);
    }
    if (isset($_SESSION['reshash']['FORMATED_ERRORS'])) {
        $smarty->assign('error', $_SESSION['reshash']['FORMATED_ERRORS']);
    }
    // minimum/maximum order value
    $checkout = true;
    if ($_SESSION['cart']->show_total() > 0) {
        if ($_SESSION['cart']->show_total() < $_SESSION['customers_status']['customers_status_min_order']) {
            $_SESSION['allow_checkout'] = 'false';
            $more_to_buy = $_SESSION['customers_status']['customers_status_min_order'] - $_SESSION['cart']->show_total();
            $order_amount = $xtPrice->xtcFormat($more_to_buy, true);
            $min_order = $xtPrice->xtcFormat($_SESSION['customers_status']['customers_status_min_order'], true);
            $smarty->assign('info_message_1', MINIMUM_ORDER_VALUE_NOT_REACHED_1);
            $smarty->assign('info_message_2', MINIMUM_ORDER_VALUE_NOT_REACHED_2);
            $smarty->assign('order_amount', $order_amount);
            $smarty->assign('min_order', $min_order);
        }
        if ($_SESSION['customers_status']['customers_status_max_order'] != 0) {
            if ($_SESSION['cart']->show_total() > $_SESSION['customers_status']['customers_status_max_order']) {
                $_SESSION['allow_checkout'] = 'false';
                $less_to_buy = $_SESSION['cart']->show_total() - $_SESSION['customers_status']['customers_status_max_order'];
                $max_order = $xtPrice->xtcFormat($_SESSION['customers_status']['customers_status_max_order'], true);
                $order_amount = $xtPrice->xtcFormat($less_to_buy, true);
                $smarty->assign('info_message_1', MAXIMUM_ORDER_VALUE_REACHED_1);
                $smarty->assign('info_message_2', MAXIMUM_ORDER_VALUE_REACHED_2);
                $smarty->assign('order_amount', $order_amount);
                $smarty->assign('min_order', $max_order);
            }
        }
    }
    if (isset($_SESSION['paypal_warten'])) {
        $smarty->assign('error', $_SESSION['paypal_warten']);
    } else {
        if ($_GET['info_message']) {
            $smarty->assign('info_message', str_replace('+', ' ', htmlspecialchars($_GET['info_message'])));
        }
        $paypal_express = true;
        for ($i = 0, $n = count($products); $i < $n; $i++) {
            $id = $products[$i]['id'];
            $query = xtc_db_query("SELECT products_forbidden_payment FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $id . "';");
            if ($i == '0') {
                $data = xtc_db_fetch_array($query);
            } else {
                $puffer = xtc_db_fetch_array($query);
                if ($puffer['products_forbidden_payment'] != '') {
                    $data['products_forbidden_payment'] .= "|";
                    $data['products_forbidden_payment'] .= $puffer['products_forbidden_payment'];
                }
            }
        }
        $payment_data = explode("|", $data['products_forbidden_payment']);
        $n = sizeof($selection);
        foreach ($payment_data AS $paymentmodule) {
            for ($i = 0; $i <= $n; $i++) {
                $name = explode('.', $paymentmodule);
                if ($name[0] == 'paypalexpress')
                    $paypal_express = false;
            }
        }

        if ($paypal_express) {
            $smarty->assign('BUTTON_PAYPAL', $o_paypal->build_express_checkout_button((int) $order_amount, $_SESSION['currency']));
            $smarty->assign('BUTTON_PAYPAL_TEXT', '<a rel="nofollow" class="shipping" href="' . xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . PAYPAL_EXPRESS_INFOID) . '"> ' . BUTTON_PAYPAL_TEXT . '</a>');
        }
//Bezahlsperre END
        $smarty->assign('BUTTON_RELOAD', xtc_image_submit('button_update_cart.gif', IMAGE_BUTTON_UPDATE_CART));
        $smarty->assign('BUTTON_BACK', '<a href="javascript:history.back();">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
        if (CHECKOUT_AJAX_STAT == 'true' && file_exists('checkout.php')) {
            $file = FILENAME_CHECKOUT;
        } else {
            $file = FILENAME_CHECKOUT_SHIPPING;
        }
        $smarty->assign('BUTTON_CHECKOUT', '<a title="' . IMAGE_BUTTON_CHECKOUT . '" href="' . xtc_href_link($file, '', 'SSL') . '">' . cseo_wk_image_button('button_checkout.gif', IMAGE_BUTTON_CHECKOUT) . '</a>');
        $smarty->assign('BUTTON_PRINT_CART', '<a class="shipping" title="' . IMAGE_BUTTON_PRINT_CART . '" href="' . xtc_href_link('print_cart.php', '', 'SSL') . '">' . xtc_image_button('button_checkout.gif', IMAGE_BUTTON_PRINT_CART) . '</a>');
    }
} else {
    // empty cart
    $cart_empty = true;
    if ($_GET['info_message']) {
        $smarty->assign('info_message', str_replace('+', ' ', htmlspecialchars($_GET['info_message'])));
    }
    $smarty->assign('cart_empty', $cart_empty);
    $smarty->assign('BUTTON_CONTINUE', '<a href="' . xtc_href_link(FILENAME_DEFAULT) . '">' . xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>');
}
// free shipping start - new code
if ($_SESSION['MAXIMUM_CART_AMOUNT'] == true) {
    $smarty->assign('info_message', PRODUCT_MAXIMUM_AMOUNT_EXCEEDED);
    unset($_SESSION['MAXIMUM_CART_AMOUNT']);
}
// free shipping - end of code

include (DIR_WS_MODULES . 'cart_specials.php');

global $breadcrumb, $cPath_array, $actual_products_id;

// echo $actual_products_id;
if (!empty($actual_products_id)) {
    $smarty->assign('SECOND_NAME', $breadcrumb->_trail[count($breadcrumb->_trail) - 2]['title']);
    $smarty->assign('SECOND_LINK', $breadcrumb->_trail[count($breadcrumb->_trail) - 2]['link']);
}
if (!empty($cPath_array)) {
    $smarty->assign('FIRST_NAME', $breadcrumb->_trail[(count($breadcrumb->_trail) >= 4) ? count($breadcrumb->_trail) - 3 : count($breadcrumb->_trail) - 2]['title']);
    $smarty->assign('FIRST_LINK', $breadcrumb->_trail[(count($breadcrumb->_trail) >= 4) ? count($breadcrumb->_trail) - 3 : count($breadcrumb->_trail) - 2]['link']);
}

if (file_exists(DIR_WS_INCLUDES . 'addons/shopping_cart_addon.php')) {
    include (DIR_WS_INCLUDES . 'addons/shopping_cart_addon.php');
}

if (file_exists(DIR_WS_MODULES . 'bonus_cart.php')) {
	include (DIR_WS_MODULES . 'bonus_cart.php');
}

$smarty->assign('HOME', xtc_href_link(FILENAME_DEFAULT));
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;

$cseo_shopping_cart = cseohookfactory::create_object('ShoppingCartExtender');
$cseo_shopping_cart->set_data('GET', $_GET);
$cseo_shopping_cart->set_data('POST', $_POST);
$cseo_shopping_cart->proceed();
$cseo_extender_result_array = $cseo_shopping_cart->get_response();
if (is_array($cseo_extender_result_array)) {
    foreach ($cseo_extender_result_array AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}

if (file_exists('templates/' . CURRENT_TEMPLATE . '/module/shopping_cart.html')) {
    $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/shopping_cart.html', USE_TEMPLATE_DEVMODE));
} else {
    $main_content = $smarty->fetch(cseo_get_usermod('base/module/shopping_cart.html', USE_TEMPLATE_DEVMODE));
}
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('checkout_site', 'true');
$smarty->assign('main_content', $main_content);

$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));

if (!isset($_SESSION['paypal_warten'])) {
    unset($_SESSION['nvpReqArray']);
    unset($_SESSION['reshash']['FORMATED_ERRORS']);
    unset($_SESSION['reshash']);
    unset($_SESSION['tmp_oID']);
} else {
    unset($_SESSION['paypal_warten']);
}

include ('includes/application_bottom.php');
