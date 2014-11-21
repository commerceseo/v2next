<?php

/* -----------------------------------------------------------------
 * 	$Id: logoff.php 637 2013-09-26 18:25:55Z akausch $
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
// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

$breadcrumb->add(NAVBAR_TITLE_LOGOFF);

//delete Guests from Database   

if (($_SESSION['account_type'] == 1) && (DELETE_GUEST_ACCOUNT == 'true')) {
	$c_customer_id = (int)$_SESSION['customer_id'];
	xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $c_customer_id . "'");
	xtc_db_query("DELETE FROM " . TABLE_ADDRESS_BOOK . " WHERE customers_id = '" . $c_customer_id . "'");
	xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_INFO . " WHERE customers_info_id = '" . $c_customer_id . "'");
	xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id = '" . $c_customer_id . "'");
	xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " WHERE customers_id = '" . $c_customer_id . "'");
	xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_IP . " WHERE customers_id = '" . $c_customer_id . "'");
	xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_WISHLIST . " WHERE customers_id = '" . $c_customer_id . "'");
	xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES . " WHERE customers_id = '" . $c_customer_id . "'");
	xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_STATUS_HISTORY . " WHERE customers_id = '" . $c_customer_id . "'");
	xtc_db_query("DELETE FROM " . TABLE_COUPON_GV_CUSTOMER . " WHERE customer_id = '" . $c_customer_id . "'");
	xtc_db_query("DELETE FROM " . TABLE_COUPON_GV_QUEUE . " WHERE customer_id = '" . $c_customer_id . "'");				
	xtc_db_query("DELETE FROM " . TABLE_WHOS_ONLINE . " WHERE customer_id = '" . $c_customer_id . "'");			
	xtc_db_query("UPDATE " . TABLE_ORDERS . " SET customers_id = 0 WHERE customers_id = '" . $c_customer_id . "'");
	xtc_db_query("UPDATE " . TABLE_NEWSLETTER_RECIPIENTS . " SET customers_id = 0 WHERE customers_id = '" . $c_customer_id . "'");
	xtc_db_query("UPDATE " . TABLE_COUPON_REDEEM_TRACK . "  SET customer_id = 0 WHERE customer_id = '" . $c_customer_id . "'");
	xtc_db_query("UPDATE withdrawals SET customer_id = 0 WHERE customer_id = '" . $c_customer_id . "'");
}

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
// write customers status guest in session again
require (DIR_WS_INCLUDES . 'write_customers_status.php');
require (DIR_WS_INCLUDES . 'header.php');

xtc_redirect(xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL'));

$smarty->assign('BUTTON_CONTINUE', '<a href="' . xtc_href_link(FILENAME_DEFAULT) . '">' . xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>');
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);

$smarty->caching = false;
$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/logoff.html', USE_TEMPLATE_DEVMODE));

$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
