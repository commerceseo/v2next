<?php

/* -----------------------------------------------------------------
 * 	$Id: checkout_confirmation.php 857 2014-03-06 20:03:55Z akausch $
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

include ('includes/application_top.php');

$smarty = new Smarty;
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

require_once (DIR_FS_INC . 'xtc_calculate_tax.inc.php');
require_once (DIR_FS_INC . 'xtc_check_stock.inc.php');
require_once (DIR_FS_INC . 'xtc_display_tax_value.inc.php');

// if the customer is not logged on, redirect them to the login page

if (!isset($_SESSION['customer_id']))
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() < 1)
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID'])) {
    if ($_SESSION['cart']->cartID != $_SESSION['cartID'])
        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}

// if no shipping method has been selected, redirect the customer to the shipping method selection page
if (!isset($_SESSION['shipping']))
    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));

if (isset($_SESSION['tmp_oID']))
    unset($_SESSION['tmp_oID']);

//check if display conditions on checkout page is true

if (isset($_POST['payment']))
    $_SESSION['payment'] = xtc_db_prepare_input($_POST['payment']);

if ($_POST['comments_added'] != '')
    $_SESSION['comments'] = xtc_db_prepare_input($_POST['comments']);

//-- TheMedia Begin check if display conditions on checkout page is true
if (isset($_POST['cot_gv']))
    $_SESSION['cot_gv'] = true;
// if conditions are not accepted, redirect the customer to the payment method selection page

unset($_SESSION['error_message']);
if(defined('VRRL_ACTIVE') && VRRL_ACTIVE == 'true') {
	$show_abandonment_download = false;
	$show_abandonment_service = false;
	$products = $_SESSION['cart']->get_products();
	for($i = 0, $n = sizeof($products); $i < $n; $i++) {
		if($products[$i]['product_type'] == '2') {
			$show_abandonment_download = true;
		}
		if($products[$i]['product_type'] == '3') {
			$show_abandonment_service = true;
		}
	}
	unset($_SESSION['abandonment_download']);
	unset($_SESSION['abandonment_service']);
}
if (!isset($_SESSION['all_checkout_checks_ok'])) {
    if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true' && CHECKOUT_CHECKBOX_AGB == 'true') {
        if ($_REQUEST['conditions'] == false) {
            $error = str_replace('\n', '<br />', ERROR_CONDITIONS_NOT_ACCEPTED);
            $_SESSION['error_message'] = urlencode($error);
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }
    }
    if (DISPLAY_DATENSCHUTZ_ON_CHECKOUT == 'true' && CHECKOUT_CHECKBOX_DSG == 'true') {
        if ($_REQUEST['datenschutz'] == false) {
            $error = str_replace('\n', '<br />', ERROR_DATENSCHUTZ_NOT_ACCEPTED);
            $_SESSION['error_message'] = urlencode($error);
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }
    }
    if (DISPLAY_REVOCATION_ON_CHECKOUT == 'true' && CHECKOUT_CHECKBOX_REVOCATION == 'true') {
        if ($_POST['widerrufsrecht'] == false) {
            $error = str_replace('\n', '<br />', CHECKOUT_ERROR_REVOCATION);
            $_SESSION['error_message'] = urlencode($error);
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }
    }
	
    if (DISPLAY_REVOCATION_ON_CHECKOUT == 'true' && CHECKOUT_CHECKBOX_REVOCATION == 'true' && WITHDRAWAL_DOWNLOAD == 'true') {
        if ($show_abandonment_download) {
		if ($_POST['revocationdownload'] == false) {
            $error = str_replace('\n', '<br />', CHECKOUT_ERROR_REVOCATION_DOWNLOAD);
            $_SESSION['error_message'] = urlencode($error);
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }
        }
    }
    if (DISPLAY_REVOCATION_ON_CHECKOUT == 'true' && CHECKOUT_CHECKBOX_REVOCATION == 'true' && WITHDRAWAL_SERVICE == 'true') {
        if ($show_abandonment_service) {
		if ($_POST['revocationservice'] == false) {
            $error = str_replace('\n', '<br />', CHECKOUT_ERROR_REVOCATION_SERVICE);
            $_SESSION['error_message'] = urlencode($error);
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }
        }
    }

    $_SESSION['all_checkout_checks_ok'] = 'true';
}
// recover carts
require_once (DIR_FS_INC . 'xtc_checkout_site.inc.php');
xtc_checkout_site('confirm');

if (isset($_SESSION['credit_covers'])) {
    $_SESSION['payment'] = 'no_payment';
}
$payment_modules = new payment($_SESSION['payment']);
$order = new order();
$payment_modules->update_status();

$order_total_modules = new order_total();
$order_total_modules->collect_posts();
$order_total_modules->pre_confirmation_check();

if ((is_array($payment_modules->modules) && (sizeof($payment_modules->modules) > 1) && (!is_object($$_SESSION['payment'])) && (!isset($_SESSION['credit_covers']))) || (is_object($$_SESSION['payment']) && ($$_SESSION['payment']->enabled == false))) {
    $_SESSION['error_message'] = urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED);
    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

if (is_array($payment_modules->modules)) {
    $payment_modules->pre_confirmation_check();
}

$shipping_modules = new shipping($_SESSION['shipping']);

// Stock Check
$any_out_of_stock = false;
if (STOCK_CHECK == 'true') {
    for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
        if (xtc_check_stock($order->products[$i]['id'], $order->products[$i]['qty']))
            $any_out_of_stock = true;
    }
    // Out of Stock
    if ((STOCK_ALLOW_CHECKOUT != 'true') && ($any_out_of_stock == true))
        xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_CONFIRMATION, xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_CONFIRMATION);

require_once (DIR_WS_INCLUDES . 'header.php');
if (isset($_GET['payment_error']) && is_object(${ $_GET['payment_error'] }) && ($error = ${$_GET['payment_error']}->get_error())) {
    $smarty->assign('error', htmlspecialchars($error['error']));
}

if (SHOW_IP_LOG == 'true') {
    $smarty->assign('IP_LOG', 'true');
    if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
        $customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else {
        $customers_ip = $_SERVER["REMOTE_ADDR"];
    }
    $smarty->assign('CUSTOMERS_IP', $customers_ip);
}
$smarty->assign('DELIVERY_LABEL', xtc_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'));
if ($_SESSION['credit_covers'] != '1') {
    $smarty->assign('BILLING_LABEL', xtc_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />'));
}
$smarty->assign('PRODUCTS_EDIT', xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
$smarty->assign('SHIPPING_ADDRESS_EDIT', xtc_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL'));
$smarty->assign('BILLING_ADDRESS_EDIT', xtc_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'));

if ($_SESSION['sendto'] != false) {

    if ($order->info['shipping_method']) {
        $smarty->assign('SHIPPING_METHOD', $order->info['shipping_method']);
        $smarty->assign('SHIPPING_EDIT', xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
}

require_once(DIR_WS_MODULES . 'checkout_confirmation_details.php');

if ($order->info['payment_method'] != 'no_payment' && $order->info['payment_method'] != '') {
    include (DIR_WS_LANGUAGES . '/' . $_SESSION['language'] . '/modules/payment/' . $order->info['payment_method'] . '.php');
    $smarty->assign('PAYMENT_METHOD', constant(MODULE_PAYMENT_ . strtoupper($order->info['payment_method']) . _TEXT_TITLE));
    if (isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error()))
        $smarty->assign('error', $error['title'] . '<br />' . htmlspecialchars($error['error']));
}
$smarty->assign('PAYMENT_EDIT', xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));

$total_block = '';
if (MODULE_ORDER_TOTAL_INSTALLED) {
    $aryNNTotal = $order_total_modules->process();
    if (count($aryNNTotal) != 0) {
        foreach ($aryNNTotal as $nnkey => $nnval) {
            if ($nnval['code'] == 'ot_total') {
                $_SESSION['nn_total'] = strtolower($nnval['text']);
                $_SESSION['nn_total'] = str_replace('<b>', '', $_SESSION['nn_total']);
                $_SESSION['nn_total'] = str_replace('</b>', '', $_SESSION['nn_total']);
                $_SESSION['nn_total'] = trim($_SESSION['nn_total']);
                list($_SESSION['nn_total'], $extra) = explode(' ', trim($_SESSION['nn_total']));
            }
        }
    }
    $total_block .= $order_total_modules->output();
}
$total_block .= '';
$smarty->assign('TOTAL_BLOCK', $total_block);

if (is_array($payment_modules->modules)) {
    if ($confirmation = $payment_modules->confirmation()) {

        $payment_info = $confirmation['title'];
        for ($i = 0, $n = sizeof($confirmation['fields']); $i < $n; $i++) {

            $payment_info .= '<div>' . $confirmation['fields'][$i]['title'] . stripslashes($confirmation['fields'][$i]['field']) . '</div>';
        }
        $smarty->assign('PAYMENT_INFORMATION', $payment_info);
    }
}

$payment_modules->refresh();

if (xtc_not_null($order->info['comments'])) {
    $smarty->assign('ORDER_COMMENTS', nl2br(htmlspecialchars($order->info['comments'])) . xtc_draw_hidden_field('comments', $order->info['comments']));
}

if (strpos($_SESSION['payment'], 'easydebit')) {
    $url_string = $payment_modules->process_button();
    $smarty->assign('CHECKOUT_FORM', $url_string);
    $smarty->assign('MODULE_BUTTONS', '');
    $smarty->assign('CHECKOUT_BUTTON', '');
} else {

    if (isset($$_SESSION['payment']->form_action_url) && !$$_SESSION['payment']->tmpOrders)
        $form_action_url = $$_SESSION['payment']->form_action_url;
    else
        $form_action_url = xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');

    $smarty->assign('CHECKOUT_FORM', xtc_draw_form('checkout_confirmation', $form_action_url, 'post'));
    $payment_button = '';
    if (is_array($payment_modules->modules)) {
        $payment_button .= $payment_modules->process_button();
    }
    $smarty->assign('MODULE_BUTTONS', $payment_button);
    $smarty->assign('CHECKOUT_BUTTON', xtc_image_submit('button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER) . '</form>' . "\n");
}
$smarty->assign('BUTTON_EDIT', xtc_image_button('button_edit.gif', 'bearbeiten'));
//check if display conditions on checkout page is true
if (DISPLAY_REVOCATION_ON_CHECKOUT == 'true') {
    if (GROUP_CHECK == 'true') {
        $group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
    }
    $shop_content_data = xtc_db_fetch_array(xtc_db_query("SELECT
							content_title,
							content_heading,
							content_text,
							content_file
							FROM " . TABLE_CONTENT_MANAGER . "
							WHERE content_group='" . REVOCATION_ID . "' " . $group_check . "
							AND languages_id='" . (int)$_SESSION['languages_id'] . "'"));

    if ($shop_content_data['content_file'] != '') {
        ob_start();
        if (strpos($shop_content_data['content_file'], '.txt'))
            echo '<pre>';
        include (DIR_FS_CATALOG . 'media/content/' . $shop_content_data['content_file']);
        if (strpos($shop_content_data['content_file'], '.txt'))
            echo '</pre>';
        $revocation = ob_get_contents();
        ob_end_clean();
    } else {
        $revocation = $shop_content_data['content_text'];
    }

    $smarty->assign('REVOCATION', $revocation);
    $smarty->assign('REVOCATION_TITLE', $shop_content_data['content_heading']);
    $smarty->assign('REVOCATION_LINK', $main->getContentLink(REVOCATION_ID, MORE_INFO));

    $shop_content_data = xtc_db_fetch_array(xtc_db_query("SELECT
								content_heading
							FROM 
								" . TABLE_CONTENT_MANAGER . "
							WHERE 
								content_group = '3' 
								" . $group_check . "
							AND 
								languages_id='" . (int)$_SESSION['languages_id'] . "'"));


    $smarty->assign('AGB_TITLE', $shop_content_data['content_heading']);
    $smarty->assign('AGB_LINK', $main->getContentLink(3, MORE_INFO));
}

if (CHECKOUT_SHOW_SHIPPING == 'true') {
    if (GROUP_CHECK == 'true') {
        $group_check = "AND group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
    }

    $shop_content_query = xtc_db_fetch_array(xtc_db_query("SELECT
							content_text
							FROM 
								" . TABLE_CONTENT_MANAGER . "
							WHERE 
								content_group='" . CHECKOUT_SHOW_SHIPPING_ID . "' 
								" . $group_check . "
							AND 
								languages_id='" . (int)$_SESSION['languages_id'] . "'"));

    $zolltext = $shop_content_query['content_text'];
    $smarty->assign('SZI', $zolltext);
}

$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('PAYMENT_BLOCK', $payment_block);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;

$cseo_checkout = cseohookfactory::create_object('CheckoutConfirmationExtender');
$cseo_checkout->set_data('GET', $_GET);
$cseo_checkout->set_data('POST', $_POST);
$cseo_checkout->proceed();
$cseo_extender_result_array = $cseo_checkout->get_response();
if (is_array($cseo_extender_result_array)) {
	foreach ($cseo_extender_result_array AS $t_key => $t_value) {
		$smarty->assign($t_key, $t_value);
	}
}

$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/checkout_confirmation.html', USE_TEMPLATE_DEVMODE));
$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
