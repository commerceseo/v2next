<?php

/* -----------------------------------------------------------------
 * 	$Id: account.php 1099 2014-06-12 14:51:40Z akausch $
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

$breadcrumb->add(NAVBAR_TITLE_ACCOUNT, xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));

require_once (DIR_WS_INCLUDES . 'header.php');

if ($messageStack->size('account') > 0) {
    $smarty->assign('error_message', $messageStack->output('account'));
}

if (isset($_SESSION['tracking']['products_history'])) {
    require_once (DIR_FS_INC . 'xtc_get_product_path.inc.php');
    $i = 0;
    $max = count($_SESSION['tracking']['products_history']);
    $row = 0;
    $module_content = array();
    while ($i < $max) {
        $row++;
        $history_product = xtc_db_fetch_array(xtDBquery("SELECT * FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd WHERE p.products_id=pd.products_id AND pd.language_id='" . (int) $_SESSION['languages_id'] . "' AND p.products_status = '1' AND p.products_id = '" . (int) $_SESSION['tracking']['products_history'][$i] . "';"));
        $cpath = xtc_get_product_path($_SESSION['tracking']['products_history'][$i]);
        if ($history_product['products_status'] != 0) {
            $cpath = xtc_get_product_path($_SESSION['tracking']['products_history'][$i]);
            $history_product = array_merge($history_product, array('cat_url' => xtc_href_link(FILENAME_DEFAULT, 'cPath=' . $cpath)));
            $module_content[] = $product->buildDataArray($history_product, 'thumbnail', 'history_product', $row);
        }
        $i++;
    }
    $smarty->assign('language', $_SESSION['language']);
    $smarty->assign('module_content', $module_content);
    $smarty->assign('TITLE', HISTORY_PRODUCT);
    $module = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE));
    $smarty->assign('MODULE_products_history', $module);
}

if (xtc_count_customer_orders() > 0) {
    $smarty->assign('order_content', $account->accountorderhistory((int) $_SESSION['customer_id']));
}

$account_smarty = $account->account_smarty('account');
if (is_array($account_smarty)) {
    foreach ($account_smarty AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}

// GV Code Start
if (isset($_SESSION['customer_id'])) {
    $gv_result = xtc_db_fetch_array(xtc_db_query("SELECT amount FROM " . TABLE_COUPON_GV_CUSTOMER . " WHERE customer_id = '" . (int) $_SESSION['customer_id'] . "';"));
    if ($gv_result['amount'] > 0) {
        $smarty->assign('GV_AMOUNT', $xtPrice->xtcFormat($gv_result['amount'], true, 0, true));
        $smarty->assign('GV_SEND_TO_FRIEND_LINK', '<a href="' . xtc_href_link(FILENAME_GV_SEND) . '">');
    }
}

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->caching = false;

$cseo_account = cseohookfactory::create_object('AccountExtender');
$cseo_account->proceed();
$cseo_extender_result_array = $cseo_account->get_response();
if (is_array($cseo_extender_result_array)) {
    foreach ($cseo_extender_result_array AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}
if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/account.html')) {
	$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/account.html', USE_TEMPLATE_DEVMODE));
}else{
	$main_content = $smarty->fetch(cseo_get_usermod('base/module/account.html', USE_TEMPLATE_DEVMODE));
}

$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
