<?php

/* -----------------------------------------------------------------
 * 	$Id: address_book.php 873 2014-03-25 16:42:10Z akausch $
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

require_once (DIR_FS_INC . 'xtc_address_label.inc.php');
require_once (DIR_FS_INC . 'xtc_get_country_name.inc.php');
require_once (DIR_FS_INC . 'xtc_count_customer_address_book_entries.inc.php');

if (!isset($_SESSION['customer_id'])) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

$breadcrumb->add(NAVBAR_TITLE_1_ADDRESS_BOOK, xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_ADDRESS_BOOK, xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));

require_once (DIR_WS_INCLUDES . 'header.php');

if ($messageStack->size('addressbook') > 0) {
    $smarty->assign('error', $messageStack->output('addressbook'));
}

$smarty->assign('ADDRESS_DEFAULT', xtc_address_label($_SESSION['customer_id'], $_SESSION['customer_default_address_id'], true, ' ', '<br />'));
$smarty->assign('BUTTON_BACK', '<a href="' . xtc_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
if (xtc_count_customer_address_book_entries() < MAX_ADDRESS_BOOK_ENTRIES) {
    $smarty->assign('BUTTON_NEW', '<a href="' . xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, '', 'SSL') . '">' . xtc_image_button('button_add_address.gif', IMAGE_BUTTON_ADD_ADDRESS) . '</a>');
}
$smarty->assign('ADDRESS_COUNT', sprintf(TEXT_MAXIMUM_ENTRIES, MAX_ADDRESS_BOOK_ENTRIES));
$address_book = new account;
$smarty->assign('addresses_data', $address_book->adress_book());

$cseo_account = cseohookfactory::create_object('AddressBookExtender');
$cseo_account->proceed();
$cseo_extender_result_array = $cseo_account->get_response();
if (is_array($cseo_extender_result_array)) {
    foreach ($cseo_extender_result_array AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->caching = false;

if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/address_book.html')) {
	$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/address_book.html', USE_TEMPLATE_DEVMODE));
}else{
	$main_content = $smarty->fetch(cseo_get_usermod('base/module/address_book.html', USE_TEMPLATE_DEVMODE));
}
$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
