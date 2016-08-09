<?php

/* -----------------------------------------------------------------
 * 	$Id: account_history_info.php 971 2014-04-11 08:37:04Z akausch $
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

require_once (DIR_FS_INC . 'xtc_date_short.inc.php');
require_once (DIR_FS_INC . 'xtc_get_all_get_params.inc.php');
require_once (DIR_FS_INC . 'xtc_image_button.inc.php');
require_once (DIR_FS_INC . 'xtc_display_tax_value.inc.php');
require_once (DIR_FS_INC . 'xtc_format_price_order.inc.php');

//security checks
if (!isset($_SESSION['customer_id'])) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}
if (!isset($_GET['order_id']) || (isset($_GET['order_id']) && !is_numeric($_GET['order_id']))) {
    xtc_redirect(xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
}

if (is_numeric($_GET['order_id'])) {
	$order_id = (int) $_GET['order_id'];
}

$customer_info = xtc_db_fetch_array(xtc_db_query("SELECT customers_id FROM " . TABLE_ORDERS . " WHERE orders_id = '" . (int) $_GET['order_id'] . "';"));
if ($customer_info['customers_id'] != (int)$_SESSION['customer_id']) {
    xtc_redirect(xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
}

$breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_HISTORY_INFO, xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_HISTORY_INFO, xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
$breadcrumb->add(sprintf(NAVBAR_TITLE_3_ACCOUNT_HISTORY_INFO, (int) $_GET['order_id']), xtc_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . (int) $_GET['order_id'], 'SSL'));

require_once (DIR_WS_INCLUDES . 'header.php');

$account_history_info = new account;
$account_history_info_smarty = $account_history_info->account_history_info($order_id);
if (is_array($account_history_info_smarty)) {
    foreach ($account_history_info_smarty AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}

$from_history = preg_match("/page=/i", xtc_get_all_get_params());
$back_to = $from_history ? FILENAME_ACCOUNT_HISTORY : FILENAME_ACCOUNT;
if (DOWNLOAD_ENABLED == 'true') {
    include (DIR_WS_MODULES . 'downloads.php');
}

$smarty->assign('BUTTON_BACK', '<a href="' . xtc_href_link($back_to, xtc_get_all_get_params(array('order_id')), 'SSL') . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
$cseo_account = cseohookfactory::create_object('AccountHistoryInfoExtender');
$cseo_account->proceed();
$cseo_extender_result_array = $cseo_account->get_response();
if (is_array($cseo_extender_result_array)) {
    foreach ($cseo_extender_result_array AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}


$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;

if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/account_history_info.html')) {
	$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/account_history_info.html', USE_TEMPLATE_DEVMODE));
}else{
	$main_content = $smarty->fetch(cseo_get_usermod('base/module/account_history_info.html', USE_TEMPLATE_DEVMODE));
}
$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
