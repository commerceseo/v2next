<?php

/* -----------------------------------------------------------------
 * 	$Id: login.php 1425 2015-02-03 15:51:57Z akausch $
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
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

$smarty = new Smarty;
$login = new login();
//Login checken und ebenfalls Smary holen wegen Antispam
if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
		// filtern findet nun in der klasse statt marco fix
        $check_login = $login->check_login('login', $_POST['email_address'], $_POST['password']);
    }
}
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

if ($session_started == false) {
    xtc_redirect(xtc_href_link(FILENAME_COOKIE_USAGE));
}
if (is_array($check_login)) {
	foreach ($check_login AS $t_key => $t_value) {
		$smarty->assign($t_key, $t_value);
	}
}
$breadcrumb->add(NAVBAR_TITLE_LOGIN, xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
require_once (DIR_WS_INCLUDES . 'header.php');
$login = new login();
//Login Smarty holen
$login_smarty = $login->login_smarty('login');
if (is_array($login_smarty)) {
    foreach ($login_smarty AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}

//Extender
$cseo_login = cseohookfactory::create_object('LoginExtender');
$cseo_login->set_data('GET', $_GET);
$cseo_login->set_data('POST', $_POST);
$cseo_login->proceed();
$cseo_extender_result_array = $cseo_login->get_response();
if (is_array($cseo_extender_result_array)) {
    foreach ($cseo_extender_result_array AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}
//Login end
//Create Account
$create_account_info = new Smarty;
$create_account = new create_account();
//Create Account Smarty holen
$create_account_smarty = $create_account->create_account_smarty('login');
if (is_array($create_account_smarty)) {
    foreach ($create_account_smarty AS $t_key => $t_value) {
        $create_account_info->assign($t_key, $t_value);
    }
}
//Extender
$cseo_create_account = cseohookfactory::create_object('CreateAccountExtender');
$cseo_create_account->set_data('GET', $_GET);
$cseo_create_account->set_data('POST', $_POST);
$cseo_create_account->proceed();
$cseo_extender_result_array = $cseo_create_account->get_response();
if (is_array($cseo_extender_result_array)) {
    foreach ($cseo_extender_result_array AS $t_key => $t_value) {
        $create_account_info->assign($t_key, $t_value);
    }
}
$create_account_info->assign('tpl_path', 'templates/base/');
$create_account_info->assign('language', $_SESSION['language']);
$create_account_info = $create_account_info->fetch(cseo_get_usermod('base/module/create_account.html', USE_TEMPLATE_DEVMODE));
$smarty->assign('create_account_content', $create_account_info);
//Create Account end
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->caching = false;

if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/login.html')) {
	$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/login.html', USE_TEMPLATE_DEVMODE));
}else{
	$main_content = $smarty->fetch(cseo_get_usermod('base/module/login.html', USE_TEMPLATE_DEVMODE));
}

$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
