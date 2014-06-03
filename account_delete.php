<?php

/* -----------------------------------------------------------------
 * 	$Id: account_delete.php 873 2014-03-25 16:42:10Z akausch $
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

if (!isset($_SESSION['customer_id'])) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

if (isset($_POST['action']) && ($_POST['action'] == 'process_account_delete')) {
    $delete_customer_query = xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_SIK . " SELECT * FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "';");
    $delete_customer_query = xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "';");
    $db = xtc_db_query("DELETE FROM " . TABLE_NEWSLETTER_RECIPIENTS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "';");
    xtc_session_destroy();
    unset($_SESSION['customer_id']);
    unset($_SESSION['customer_default_address_id']);
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
    $smarty->assign('BUTTON_CONTINUE', '<a href="' . xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '">' . xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>');
}

$breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_DELETE, xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_DELETE, xtc_href_link(FILENAME_ACCOUNT_DELETE, '', 'SSL'));

require_once (DIR_WS_INCLUDES . 'header.php');

$account_smarty = $account->account_delete_smarty('account_delete');
if (is_array($account_smarty)) {
    foreach ($account_smarty AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}

$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;

$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/account_delete.html', USE_TEMPLATE_DEVMODE));
$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
