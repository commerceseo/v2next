<?php

/* -----------------------------------------------------------------
 * 	$Id: checkout_success.php 934 2014-04-02 15:40:06Z akausch $
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

// Google Analytics
if (GOOGLE_ANAL_ON == 'true' && GOOGLE_ANAL_CODE != '') {
    require_once (DIR_FS_INC . 'xtc_get_order_data.inc.php');
    require_once (DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
}
// Google Analytics End
// if the customer is not logged on, redirect them to the shopping cart page
if (!isset($_SESSION['customer_id'])) {
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

if (isset($_GET['action']) && ($_GET['action'] == 'update')) {
    if ($_SESSION['account_type'] != 1) {
        xtc_redirect(xtc_href_link(FILENAME_DEFAULT));
    } else {
        xtc_redirect(xtc_href_link(FILENAME_LOGOFF));
    }
}
$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_SUCCESS);
$breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_SUCCESS);

require_once (DIR_WS_INCLUDES . 'header.php');

$orders = xtc_db_fetch_array(xtc_db_query("SELECT orders_id, orders_status FROM " . TABLE_ORDERS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' ORDER BY orders_id desc LIMIT 1;"));
$last_order = $orders['orders_id'];
$order_status = $orders['orders_status'];

//BOF - Barzahlen - 2013-01-28: Barzahlen Checkout-Page
$payment = xtc_db_fetch_array(xtc_db_query("SELECT payment_method FROM " . TABLE_ORDERS . " WHERE orders_id = '" . $last_order . "' LIMIT 1;"));
if ($payment['payment_method'] === 'barzahlen') {
    if (isset($_SESSION['infotext-1'])) {
        $smarty->assign('INFOTEXT_1', $_SESSION['infotext-1']);
        unset($_SESSION['infotext-1']);
    } else {
        xtc_redirect(xtc_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $last_order, 'SSL'));
    }
}
//EOF - Barzahlen - 2013-01-28: Barzahlen Checkout-Page
if (file_exists('CheckoutByAmazon/amazon_checkout_success.php')) {
    include_once('CheckoutByAmazon/amazon_checkout_success.php');
}
//Trusted Shops im Checkout
if (TRUSTED_SHOP_STATUS == 'true') {
    include(DIR_WS_MODULES . 'module_trusted_shops.php');
}

$smarty->assign('FORM_ACTION', xtc_draw_form('order', xtc_href_link(FILENAME_CHECKOUT_SUCCESS, 'action=update', 'SSL')));
$smarty->assign('BUTTON_CONTINUE', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));
$smarty->assign('BUTTON_PRINT', '<a href="javascript:void(0)" onclick="javascript:window.open(\'' . xtc_href_link(FILENAME_PRINT_ORDER, 'oID=' . $orders['orders_id']) . '\', \'popup\', \'toolbar=0, width=640, height=600\')">' . xtc_image_button('print.gif', IMAGE_BUTTON_PRINT) . '</a>');
$smarty->assign('FORM_END', '</form>');

// GV Code Start
$gv_query = xtc_db_query("SELECT amount FROM " . TABLE_COUPON_GV_CUSTOMER . " WHERE customer_id='" . (int) $_SESSION['customer_id'] . "';");
if ($gv_result = xtc_db_fetch_array($gv_query)) {
    if ($gv_result['amount'] > 0) {
        $smarty->assign('GV_SEND_LINK', xtc_href_link(FILENAME_GV_SEND));
    }
}
// GV Code End

if (DOWNLOAD_ENABLED == 'true') {
    include(DIR_WS_MODULES . 'downloads.php');
}

$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('PAYMENT_BLOCK', $payment_block);
$smarty->caching = false;

$cseo_checkout = cseohookfactory::create_object('CheckoutSuccessExtender');
$cseo_checkout->set_data('GET', $_GET);
$cseo_checkout->set_data('POST', $_POST);
$cseo_checkout->proceed();
$cseo_extender_result_array = $cseo_checkout->get_response();
if (is_array($cseo_extender_result_array)) {
	foreach ($cseo_extender_result_array AS $t_key => $t_value) {
		$smarty->assign($t_key, $t_value);
	}
}
if (file_exists(DIR_WS_INCLUDES . 'addons/checkout_success_addon.php')) {
	include(DIR_WS_INCLUDES . 'addons/checkout_success_addon.php');
}

$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/checkout_success.html', USE_TEMPLATE_DEVMODE));

//Beginn Partnerseller Anbindung
if (PARTNER_SELLER_ACTIVE == 'true') {
	function xtc_psl($psl_url, $customer, $order_id, $passwort, $pruefwort){
		$order=new order($order_id);
		$psl_price=$order->info['pp_total']-$order->info['pp_tax'];
		$psl_price-=$order->info['pp_shipping'];
		$psl_cur=$order->info['currency'];
		$psl_kd=$order->customer;
		$parameter="preis=".$psl_price;
		$parameter.="&kundenkennung=".$customer;
		$parameter.="&order_id=".$order_id;
		$parameter.="&remark_admin=".$psl_kd['firstname'].",".$psl_kd['lastname'].",".$psl_kd['email_address'];
		$parameter.="&cur=".$psl_cur;
		$verschluesselt = psl_crypt($parameter, $passwort, $pruefwort);
		$return_value="<img src=\"".$psl_url."verkauft.php?code=".$verschluesselt."\" height=\"1\" width=\"1\" border=\"0\">";
		return $return_value;
	}

	function psl_crypt($parameter, $passwort, $pruefwort) {
		$neuer_string = $parameter . "&pruefwort=" . $pruefwort;
		for($y = 0; $y <= ord(md5($passwort)); $y++) {
			$out = "";
			$x = 0;
			for($i = 0; $i < strlen($neuer_string); $i++) {
				if($x >= strlen($passwort)) {
					$x = 0;
				}
				$out .= chr((ord($neuer_string[$i]) + ord(md5($pruefwort))) -
				ord($passwort[$x]));
				$x++;
			}
			$neuer_string = $out;
		}
		return rawurlencode(base64_encode($neuer_string));
	}

	$main_content.=xtc_psl(PARTNER_SELLER_PATH, $_SESSION['customer_id'], $last_order, PARTNER_SELLER_PWD, PARTNER_SELLER_CHK);
}


//Ende Partnerseller Anbindung

if (($_SESSION['account_type'] == 1) && (DELETE_GUEST_ACCOUNT == 'true')) {
	// xtc_session_destroy();
	unset($_SESSION['customer_default_address_id']);
	unset($_SESSION['customers_status']['customers_status_id']);
	unset($_SESSION['customer_first_name']);
	unset($_SESSION['customer_country_id']);
	unset($_SESSION['customer_zone_id']);
	unset($_SESSION['comments']);
	unset($_SESSION['user_info']);
	unset($_SESSION['customers_status']);
	unset($_SESSION['selected_box']);
	unset($_SESSION['navigation']);
	unset($_SESSION['shipping']);
	unset($_SESSION['payment']);
	unset($_SESSION['ccard']);
	unset($_SESSION['gv_id']);
	unset($_SESSION['cc_id']);
	$_SESSION['cart']->reset();
}

$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
