<?php

/* -----------------------------------------------------------------
 * 	$Id: account_edit.php 873 2014-03-25 16:42:10Z akausch $
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
require_once (DIR_FS_INC . 'xtc_image_button.inc.php');
require_once (DIR_FS_INC . 'xtc_validate_email.inc.php');
require_once (DIR_FS_INC . 'xtc_get_geo_zone_code.inc.php');
require_once (DIR_FS_INC . 'xtc_get_customers_country.inc.php');

if (!isset($_SESSION['customer_id'])) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

if ($_SESSION['customers_status']['customers_status_id'] == 0) {
    xtc_redirect(xtc_href_link_admin(FILENAME_CUSTOMERS, 'cID=' . (int) $_SESSION['customer_id'] . '&action=edit', 'SSL'));
}

$breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_EDIT, xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_EDIT, xtc_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));

require_once (DIR_WS_INCLUDES . 'header.php');

$account_edit = new account;
$account_edit_smarty = $account_edit->account_edit_smarty('account_edit');
if (is_array($account_edit_smarty)) {
    foreach ($account_edit_smarty AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}

$cseo_account = cseohookfactory::create_object('AccountEditExtender');
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

$main_content = $smarty->fetch(cseo_get_usermod('base/module/account_edit.html', USE_TEMPLATE_DEVMODE));
$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
