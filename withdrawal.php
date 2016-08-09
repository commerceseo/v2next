<?php

/* -----------------------------------------------------------------
 * 	$Id: withdrawal.php 865 2014-03-16 12:44:08Z akausch $
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

require_once('includes/application_top.php');

if ($_SERVER['HTTPS'] != 'on' && ENABLE_SSL) {
	xtc_redirect(xtc_href_link(FILENAME_WITHDRAWAL, '', 'SSL'));
}
if (WITHDRAWAL_WEBFORM_ACTIVE == 'false') {
	xtc_redirect(xtc_href_link(FILENAME_DEFAULT, '', 'SSL'));
} else {
$smarty = new Smarty;

require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

if (isset($_SESSION['customer_id'])) {
	$cid = (int)($_SESSION['customer_id']);
}

$breadcrumb->add(NAVBAR_TITLE_WITHDRAWAL, xtc_href_link(FILENAME_WITHDRAWAL, '', 'SSL'));
require_once(DIR_WS_INCLUDES . 'header.php');
if (isset($_GET['action']) && ($_GET['action'] == 'success')) {
	$smarty->assign('success', '1');
	$smarty->assign('BUTTON_CONTINUE', '<a href="' . xtc_href_link(FILENAME_DEFAULT) . '">' . xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>');
}
$withdrawal = new withdrawal();
if(isset($_GET['order']) && trim($_GET['order']) != '') {
	$oid = $withdrawal->set_order_hash($_GET['order']);
}

$withdrawal_smarty = $withdrawal->withdrawal_smarty($oid, $cid);

if (is_array($withdrawal_smarty)) {
    foreach ($withdrawal_smarty AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}
//Extender
$cseo_withdrawal = cseohookfactory::create_object('WithdrawalExtender');
$cseo_withdrawal->set_data('GET', $_GET);
$cseo_withdrawal->set_data('POST', $_POST);
$cseo_withdrawal->proceed();
$cseo_extender_result_array = $cseo_withdrawal->get_response();
if (is_array($cseo_extender_result_array)) {
    foreach ($cseo_extender_result_array AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->caching = false;
if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/withdrawal.html')) {
	$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/withdrawal.html', USE_TEMPLATE_DEVMODE));
}else{
	$main_content = $smarty->fetch(cseo_get_usermod('base/module/withdrawal.html', USE_TEMPLATE_DEVMODE));
}
$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
}