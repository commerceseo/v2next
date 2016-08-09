<?php

/* -----------------------------------------------------------------
 * 	$Id: account_history.php 881 2014-03-27 09:34:01Z akausch $
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
$account = new account;
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

require_once (DIR_FS_INC . 'xtc_count_customer_orders.inc.php');
require_once (DIR_FS_INC . 'xtc_date_short.inc.php');
require_once (DIR_FS_INC . 'xtc_image_button.inc.php');
require_once (DIR_FS_INC . 'xtc_get_all_get_params.inc.php');

if (!isset($_SESSION['customer_id'])) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

$breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_HISTORY, xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_HISTORY, xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));

require_once (DIR_WS_INCLUDES . 'header.php');

$module_content = array();
if (($orders_total = xtc_count_customer_orders()) > 0) {
    $history_query_raw = "SELECT o.orders_id, o.date_purchased, o.delivery_name, o.billing_name, ot.text as order_total, s.orders_status_name FROM " . TABLE_ORDERS . " o, " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS_STATUS . " s WHERE o.customers_id = '" . (int) $_SESSION['customer_id'] . "' AND o.orders_id = ot.orders_id AND ot.class = 'ot_total' AND o.orders_status = s.orders_status_id AND s.language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY orders_id DESC";
    $history_split = new splitPageResults($history_query_raw, $_GET['page'], MAX_DISPLAY_ORDER_HISTORY);
    $history_query = xtc_db_query($history_split->sql_query);
    $module_content = $account->account_history($history_query);
}
$smarty->assign('order_content', $module_content);
if ($orders_total > 0) {
    $smarty->assign('SPLIT_BAR', '
	          <div class="smallText" style="clear:both;"><div style="float:left;">' . $history_split->display_count(TEXT_DISPLAY_NUMBER_OF_ORDERS) . '</div>
              <div align="right">' . TEXT_RESULT_PAGE . ' ' . $history_split->display_links(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array('page', 'info', 'x', 'y'))) . '</div>
              </div>');
}

$smarty->assign('BUTTON_BACK', '<a href="' . xtc_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');

$cseo_account = cseohookfactory::create_object('AccountHistoryExtender');
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

if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/account_history.html')) {
	$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/account_history.html', USE_TEMPLATE_DEVMODE));
}else{
	$main_content = $smarty->fetch(cseo_get_usermod('base/module/account_history.html', USE_TEMPLATE_DEVMODE));
}
$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
