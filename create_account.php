<?php

/* -----------------------------------------------------------------
 * 	$Id: create_account.php 865 2014-03-16 12:44:08Z akausch $
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
if ($_SERVER['HTTPS'] != 'on' && ENABLE_SSL) {
	xtc_redirect(xtc_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));
}
$smarty = new Smarty;
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

if (isset($_SESSION['customer_id'])) {
    xtc_redirect(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
}

$breadcrumb->add(NAVBAR_TITLE_CREATE_ACCOUNT, xtc_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));
require_once(DIR_WS_INCLUDES . 'header.php');


//Create Account
$create_account = new create_account();
//Create Account Smarty holen
$create_account_smarty = $create_account->create_account_smarty('create_account');
if (is_array($create_account_smarty)) {
    foreach ($create_account_smarty AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}
//Create Account end
//Extender
$cseo_create_account = cseohookfactory::create_object('CreateAccountExtender');
$cseo_create_account->set_data('GET', $_GET);
$cseo_create_account->set_data('POST', $_POST);
$cseo_create_account->proceed();
$cseo_extender_result_array = $cseo_create_account->get_response();
if (is_array($cseo_extender_result_array)) {
    foreach ($cseo_extender_result_array AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->caching = false;
if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/create_account.html')) {
	$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/create_account.html', USE_TEMPLATE_DEVMODE));
}else{
	$main_content = $smarty->fetch(cseo_get_usermod('base/module/create_account.html', USE_TEMPLATE_DEVMODE));
}
$smarty->assign('main_content', $main_content);

$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
