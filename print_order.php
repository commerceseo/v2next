<?php

/* -----------------------------------------------------------------
 * 	$Id: print_order.php 928 2014-03-31 13:56:47Z akausch $
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

// include needed functions
require_once (DIR_FS_INC . 'xtc_get_order_data.inc.php');
require_once (DIR_FS_INC . 'xtc_get_attributes_model.inc.php');


$smarty = new Smarty;

// check if custmer is allowed to see this order!
$oID = (int) $_GET['oID'];
$order_check = xtc_db_fetch_array(xtc_db_query("SELECT customers_id FROM " . TABLE_ORDERS . " WHERE orders_id='" . $oID . "';"));

if ($_SESSION['customer_id'] == $order_check['customers_id']) {
    $order = new order($oID);
    $smarty->assign('address_label_customer', xtc_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));
    $smarty->assign('address_label_shipping', xtc_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'));
    $smarty->assign('address_label_payment', xtc_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'));
    $smarty->assign('csID', $order->customer['csID']);
    // get products data
    $order_total = $order->getTotalData($oID);
    $smarty->assign('order_data', $order->getOrderData($oID));
    $smarty->assign('order_total', $order_total['data']);
    $smarty->assign('oID', (int) $_GET['oID']);
    if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') {
        include (DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $order->info['payment_method'] . '.php');
        $payment_method = constant(strtoupper('MODULE_PAYMENT_' . $order->info['payment_method'] . '_TEXT_TITLE'));
    }
    $smarty->assign('PAYMENT_METHOD', $payment_method);
    $smarty->assign('COMMENT', $order->info['comments']);
    $smarty->assign('DATE', xtc_date_long($order->info['date_purchased']));
	if (CHECKOUT_SHOW_SHIPPING == 'true') {
		if (GROUP_CHECK == 'true') {
			$group_check = "AND group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
		}

		$shop_content_query = xtc_db_fetch_array(xtc_db_query("SELECT
								content_title,
								content_heading,
								content_text,
								content_file
								FROM " . TABLE_CONTENT_MANAGER . "
								WHERE content_group='" . CHECKOUT_SHOW_SHIPPING_ID . "' " . $group_check . "
								AND languages_id='" . $_SESSION['languages_id'] . "'"));

		$zolltext = $shop_content_query['content_text'];
		$smarty->assign('SZI', $zolltext);
	}
    $path = DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/';
    $smarty->assign('tpl_path', $path);

    // PAYMENT MODUL TEXTS
    // EU Bank Transfer
    if ($order->info['payment_method'] == 'eustandardtransfer') {
        $smarty->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION);
        $smarty->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION));
    }

    // MONEYORDER
    if ($order->info['payment_method'] == 'moneyorder') {
        $smarty->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION);
        $smarty->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION));
    }
    $header = '<!DOCTYPE html>
	<html lang ="' . HTML_PARAMS . '">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=' . $_SESSION['language_charset'] . '" />
	';

    $smarty->assign('HEADER', $header);
    $smarty->assign('language', $_SESSION['language']);
    $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $smarty->caching = false;
    $smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/module/print_order.html', USE_TEMPLATE_DEVMODE));
} else {
    $smarty->assign('ERROR', 'You are not allowed to view this order!');
    $smarty->assign('language', $_SESSION['language']);
    $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $smarty->caching = false;
    $smarty->display(cseo_get_usermod('base/module/error_message.html', USE_TEMPLATE_DEVMODE));
}
