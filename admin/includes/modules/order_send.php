<?php

/* -----------------------------------------------------------------
 * 	$Id: order_send.php 1002 2014-05-05 15:14:06Z akausch $
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
defined("_VALID_XTC") or die("Direct access to this location isn't allowed.");

$send_to_customer = 0;
$send_to_admin = 0;

if (isset($_GET['stc'])) {
    $send_to_customer = $_GET['stc'];
}
if (isset($_GET['sta'])) {
    $send_to_admin = $_GET['sta'];
}

$oID = xtc_db_prepare_input($_GET['oID']);
$order = new order($oID);
$xtPrice = new xtcPrice($order->info['currency'], $order->info['status']);
// set dirs manual
$smarty->template_dir = DIR_FS_CATALOG . 'templates';
$smarty->compile_dir = DIR_FS_CATALOG . 'templates_c';
$smarty->config_dir = DIR_FS_CATALOG . 'lang';

$smarty->assign('address_label_customer', xtc_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));
$smarty->assign('address_label_shipping', xtc_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'));
if ($_SESSION['credit_covers'] != '1') {
    $smarty->assign('address_label_payment', xtc_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'));
}
$smarty->assign('csID', $order->customer['csID']);

$order_query = xtc_db_query("SELECT
		        				products_id,
		        				orders_products_id,
		        				products_model,
		        				products_name,
		        				final_price,
		        				products_quantity
		        			FROM
		        				" . TABLE_ORDERS_PRODUCTS . "
		        			WHERE
		        				orders_id='" . $oID . "'");
$order_data = array();
while ($order_data_values = xtc_db_fetch_array($order_query)) {
    $attributes_query = xtc_db_query("SELECT
				        				products_options,
				        				products_options_values,
				        				price_prefix,
				        				options_values_price
				        			FROM
				        				" . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
				        			WHERE
				        				orders_products_id='" . $order_data_values['orders_products_id'] . "'
									ORDER BY orders_products_attributes_id ASC
									");

    $getTaxClass = xtc_db_fetch_array(xtc_db_query("SELECT products_tax_class_id FROM products WHERE products_id = '" . $order_data_values['products_id'] . "' "));

    $attributes_data = '';
    $attributes_model = '';
    $attributes_single_price = '';
    $attributes_final_price = '';
    $attributes_qty = '';
    while ($attributes_data_values = xtc_db_fetch_array($attributes_query)) {
        $attributes_data .= $attributes_data_values['products_options'] . ': ' . $attributes_data_values['products_options_values'] . '<br />';
        $attributes_model .= xtc_get_attributes_model($order_data_values['products_id'], $attributes_data_values['products_options_values']);

        $attributes_single_price .= $xtPrice->xtcFormat($attributes_data_values['options_values_price'], true, $getTaxClass['products_tax_class_id']) . '<br />';
        $attributes_final_price .= $xtPrice->xtcFormat($attributes_data_values['options_values_price'] * $order_data_values['products_quantity'], true, $getTaxClass['products_tax_class_id']) . '<br />';
        $attributes_qty .= ($order_data_values['products_options'] != 'Downloads' ? $order_data_values['products_quantity'] . 'x' : '') . '<br />';
    }
    $order_data[] = array('PRODUCTS_MODEL' => $order_data_values['products_model'],
        'PRODUCTS_NAME' => $order_data_values['products_name'],
        'PRODUCTS_ATTRIBUTES' => $attributes_data,
        'PRODUCTS_ATTRIBUTES_MODEL' => $attributes_model,
        'PRODUCTS_ATTRIBUTES_SINGLE_PRICE' => $attributes_data_values['price_prefix'] . $attributes_single_price,
        'PRODUCTS_ATTRIBUTES_FINAL_PRICE' => $attributes_data_values['price_prefix'] . $attributes_final_price,
        'PRODUCTS_ATTRIBUTES_QTY' => $attributes_qty,
        'PRODUCTS_PRICE' => $xtPrice->xtcFormat($order_data_values['final_price'], true),
        'PRODUCTS_SINGLE_PRICE' => $xtPrice->xtcFormat($order_data_values['final_price'] / $order_data_values['products_quantity'], true),
        'PRODUCTS_QTY' => $order_data_values['products_quantity']);
}

$oder_total_query = xtc_db_query("SELECT
					title,
					text,
					sort_order
					FROM " . TABLE_ORDERS_TOTAL . "
					WHERE orders_id='" . $oID . "'
					ORDER BY sort_order ASC");

$order_total = array();
while ($oder_total_values = xtc_db_fetch_array($oder_total_query)) {
    $order_total[] = array('TITLE' => $oder_total_values['title'], 'TEXT' => $oder_total_values['text']);
}

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
$smarty->assign('oID', $oID);
if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') {
    include ('' . DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $order->info['payment_method'] . '.php');
    $payment_method = constant(strtoupper('MODULE_PAYMENT_' . $order->info['payment_method'] . '_TEXT_TITLE'));
}
$smarty->assign('PAYMENT_METHOD', $payment_method);
$smarty->assign('DATE', utf8_encode(xtc_date_long($order->info['date_purchased'])));
$smarty->assign('order_data', $order_data);
$smarty->assign('order_total', $order_total);
$smarty->assign('NAME', $order->customer['name']);
$smarty->assign('COMMENTS', $order->info['comments']);
$smarty->assign('EMAIL', $order->customer['email_address']);
$smarty->assign('PHONE', $order->customer['telephone']);
$smarty->assign('GENDER', $customers_gender['customers_gender']);
$smarty->assign('VNAME', $order->customer['firstname']);
$smarty->assign('NNAME', $order->customer['lastname']);
$smarty->assign('UID', $order->customer['customers_vat_id']);

/** BEGIN BILLPAY CHANGED **/
require_once(DIR_FS_CATALOG . 'includes/billpay/utils/billpay_mail.php');
/** EOF BILLPAY CHANGED **/
if ($order->info['payment_method'] == 'eustandardtransfer') {
    $smarty->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION);
    $smarty->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION));
}

if ($order->info['payment_method'] == 'moneyorder') {
    $smarty->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION);
    $smarty->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION));
}

if ($order->info['payment_method'] == 'cash') {
    $smarty->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_CASH_TEXT_INFO);
    $smarty->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_CASH_TEXT_INFO));
}

$widerruf = xtc_db_fetch_array(xtc_db_query("SELECT content_heading, content_text, content_file FROM " . TABLE_CONTENT_MANAGER . " WHERE content_group = '".REVOCATION_ID."' AND languages_id = '" . (int) $_SESSION['languages_id'] . "' "));

$smarty->assign('WIDERRUF_HEAD', $widerruf['content_heading']);

if ($widerruf['content_file'] != '') {
	ob_start();
	include (DIR_FS_CATALOG . 'media/content/' . $widerruf['content_file']);
	$text = stripslashes(ob_get_contents());
	ob_end_clean();
	$smarty->assign('WIDERRUF_TEXT', $text);
} else {
	$smarty->assign('WIDERRUF_TEXT', $widerruf['content_text']);
}

$smarty->caching = false;
require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
$html_mail = $smarty->fetch('html:order_mail');
$html_mail .= $signatur_html;
$txt_mail = $smarty->fetch('txt:order_mail');
$txt_mail .= $signatur_text;
require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
$mail_data = cseo_get_mail_data('order_mail');

$order_subject = str_replace('{$nr}', $oID, $mail_data['EMAIL_SUBJECT']);
$order_subject = str_replace('{$date}', utf8_encode(strftime(DATE_FORMAT_LONG)), $order_subject);
$order_subject = str_replace('{$lastname}', $order->customer['lastname'], $order_subject);
$order_subject = str_replace('{$firstname}', $order->customer['firstname'], $order_subject);
$order_subject = str_replace('{$shop_besitzer}', STORE_OWNER, $order_subject);
$order_subject = str_replace('{$shop_name}', STORE_NAME, $order_subject);

if ($send_to_admin == 1) {
    xtc_php_mail($order->customer['email_address'], 
				$order->customer['firstname'] . ' ' . $order->customer['lastname'], 
				$mail_data['EMAIL_ADDRESS'], 
				$mail_data['EMAIL_ADDRESS_NAME'], 
				$mail_data['EMAIL_FORWARD'], 
				$order->customer['email_address'], 
				$order->customer['firstname'] . ' ' . $order->customer['lastname'], 
				'', 
				'', 
				$order_subject, 
				$html_mail, 
				$txt_mail, 
				true);
}

if ($send_to_customer == 1) {
    xtc_php_mail($mail_data['EMAIL_ADDRESS'], 
				$mail_data['EMAIL_ADDRESS_NAME'], 
				$order->customer['email_address'], 
				$order->customer['firstname'] . ' ' . $order->customer['lastname'], 
				'', 
				$mail_data['EMAIL_REPLAY_ADDRESS'], 
				$from_name, 
				'', 
				'', 
				$order_subject, 
				$html_mail, 
				$txt_mail, 
				true);
}
if (AFTERBUY_ACTIVATED == 'true') {
    require_once (DIR_WS_CLASSES . 'class.afterbuy.php');
    $aBUY = new xtc_afterbuy_functions($oID);
    if ($aBUY->order_send())
        $aBUY->process_order();
}
$messageStack->add_session(SUCCESS_ORDER_SEND, 'success');
