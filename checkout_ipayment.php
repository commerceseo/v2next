<?php
/* --------------------------------------------------------------
   checkout_ipayment.php 2013-02-28 mabr
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once 'includes/application_top.php';
require_once 'includes/classes/class.ipayment.php';

if(isset($_GET['back_button'])) {
	xtc_redirect(FILENAME_CHECKOUT_CONFIRMATION);
}

if(empty($_SESSION['tmp_oID'])) {
   die('not in checkout or session expired');
}

$order = new order($_SESSION['tmp_oID']);
try {
	$ipayment = new GMIPayment($order->info['payment_method']);
}
catch(GMIPaymentCodeInvalidException $e) {
	die('payment method is not ipayment, aborting');
}

if(isset($_REQUEST['ret_status']) && isset($_REQUEST['ret_param_checksum'])) {
	// returning from payment
	$checksum_correct = $ipayment->checkReturnHash($_REQUEST);
	if($checksum_correct == true) {
		$_SESSION['ipayment_response'][$_SESSION['tmp_oID']] = $_REQUEST;
		xtc_redirect('checkout_process.php');
	}
	else {
		$ipayment->log("Response handler called with incorrect hash, request follows:\n".print_r($_REQUEST));
		unset($_SESSION['tmp_oID']);
		die('violation of security parameters');
	}
}

if(isset($_REQUEST['ret_status']) && isset($_REQUEST['ret_url_checksum'])) {
	$url_checksum_correct = $ipayment->checkURLHash($_SERVER['REQUEST_URI'], $_REQUEST['ret_url_checksum']);
	if(!$url_checksum_correct == true) {
		die('violation of security parameters (G)');
	}
}

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'back') {
	// payment in prepaid mode aborted - not much we can do, go back to payment selection.
	xtc_redirect(FILENAME_CHECKOUT_PAYMENT);
}

// countries_list
$countries_result = xtc_db_query("SELECT countries_id, countries_name, countries_iso_code_2, countries_iso_code_3 FROM countries WHERE status = 1");
$countries_list = array();
$cid_usa = false;
$cid_canada = false;
while($countries_row = xtc_db_fetch_array($countries_result)) {
	$countries_list[] = $countries_row;
	$cid_usa = ($cid_usa == false && $countries_row['countries_iso_code_2'] == 'US') ? $countries_row['countries_id'] : $cid_usa;
	$cid_canada = ($cid_canada == false && $countries_row['countries_iso_code_2'] == 'CA') ? $countries_row['countries_id'] : $cid_canada;
}

$states_usa = array();
if($cid_usa !== false) {
	$states_usa_result = xtc_db_query("SELECT zone_code, zone_name FROM zones WHERE zone_country_id = ".(int)$cid_usa);
	while($states_usa_row = xtc_db_fetch_array($states_usa_result)) {
		$states_usa[] = $states_usa_row;
	}
}

$states_canada = array();
if($cid_canada !== false) {
	$states_canada_result = xtc_db_query("SELECT zone_code, zone_name FROM zones WHERE zone_country_id = ".(int)$cid_canada);
	while($states_canada_row = xtc_db_fetch_array($states_canada_result)) {
		$states_canada[] = $states_canada_row;
	}
}

if($_SERVER['REQUEST_METHOD'] == 'GET') {
	$smarty = new Smarty;
	require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');
	require_once (DIR_WS_INCLUDES.'header.php');

	$formfields = $ipayment->getFormData($_SESSION['tmp_oID'], $order);

	$smarty->assign('nonsilent_message', 'Wenn Sie nicht automatisch weitergeleitet werden, klicken Sie bitte auf den Button, um zum Zahlungsdienstleister weitergeleitet zu werden');
	if(isset($_REQUEST['ret_errormsg'])) {
		$smarty->assign('silent_error', strip_tags($_REQUEST['ret_errormsg']));
	}
	$smarty->assign('language', $_SESSION['language']);
	$smarty->assign('formfields', $formfields);
	$smarty->assign('returned_fields', $_REQUEST);
	$smarty->assign('countries_list', $countries_list);
	$smarty->assign('states_usa', $states_usa);
	$smarty->assign('states_canada', $states_canada);
	$smarty->assign('months', array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'));
	$years = array();
	$current_year = date('y');
	for($i = 0; $i <= 20; $i++) {
		$years[] = sprintf('%02d', ($current_year + $i));
	}
	$smarty->assign('years', $years);
	$smarty->assign('action_url', $formfields['action']);
	$smarty->assign('orders_id', $_SESSION['tmp_oID']);
	$smarty->assign('back_url', basename(__FILE__).'?back_button=go');
	$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/checkout_ipayment.html');

	$smarty->assign('main_content', $main_content);
	$smarty->caching = false;
	$smarty->display(CURRENT_TEMPLATE.'/index.html');
	include ('includes/application_bottom.php');
}

