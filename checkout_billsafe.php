<?php
/* --------------------------------------------------------------
   checkout_billsafe.php 2012-12 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
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

/*

Array
(
    [ack] => OK
    [status] => DECLINED
    [declineReason] => Array
        (
            [code] => 101
            [message] => BillSAFE does not secure this transaction
            [buyerMessage] => Für diese Transaktion steht die Zahlart BillSAFE nicht zur Verfügung. Bitte wählen Sie eine andere Zahlart.
        )

)
 
* this file get called in these cases:
* 1. as return_url in normal mode (with $_GET['token'] set) => check transaction result, forward to checkout_process or checkout_payment
* 2. as trigger page in layer mode (without $_GET['token']) => output pseudo-form to open payment layer
* 3. as return_url in layer mode (with $_GET['token'] and without $_GET['process']) => close layer, redirect onto itself for further processing as in 1.

*/

require_once 'includes/application_top.php';
require_once DIR_WS_CLASSES . 'payment/class_billsafe3.php';
$text = new LanguageTextManager('billsafe', $_SESSION['languages_id']);

if(strpos($_SESSION['payment'], 'billsafe_3') === false) {
	die('invalid payment module, aborting.');
}

$main_content = '<!-- no content -->';

$bs = new GMBillSafe($_SESSION['payment']);

if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_REQUEST['layeredPaymentGateway'])) {
	echo '<html><div id="BillSAFE_Token">'.$_SESSION['billsafe_token'].'</div></html>';
	exit;
}

if(isset($_GET['mode']) && $_GET['mode'] == 'layer') {

} elseif(isset($_GET['token']) && $_GET['token'] == $_SESSION['billsafe_token']) {
	if(strtolower(constant('MODULE_PAYMENT_'.strtoupper($_SESSION['payment']).'_LAYER')) == 'true' && !isset($_GET['process'])) {
		$process_url = DIR_WS_CATALOG.basename(__FILE__).'?token='.$_GET['token'].'&process=1';
		echo "<html>\n<body>\n<script>\nif(top.lpg) {\n ".$jslog." top.lpg.close('".$process_url."');\n }\n</script>\n</body>\n</html>\n";
		flush();
		exit;
	}

	$tres = $bs->getTransactionResult($_GET['token']);
	
	if(isset($tres['ack']) && $tres['ack'] == 'OK') {
		if(isset($tres['status']) && $tres['status'] == 'ACCEPTED') {
			// OK, finalize order
			$bs->saveTransactionId($_SESSION['tmp_oID'], $tres['transactionId']);
			
			xtc_redirect(DIR_WS_CATALOG.'checkout_process.php');
		} elseif(isset($tres['status']) && $tres['status'] == 'DECLINED') {
			$bs->_log("Transaction declined: ".$tres['declineReason']['code'].' '.$tres['declineReason']['message'] .' | token '.$_SESSION['billsafe_token']. ' orders_id '. $_SESSION['tmp_oID']);
			$_SESSION['billsafe_3_error'] = $tres['declineReason']['buyerMessage'];
			if (CHECKOUT_AJAX_STAT == 'true') {
				$_SESSION['checkout_payment_error'] = 'payment_error=' . $_SESSION['billsafe_3_error'];
			} else {
				xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $_SESSION['billsafe_3_error']));
			}
			// xtc_redirect(DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$_SESSION['payment']);
		} else {
			$bs->_log("ERROR: Unhandled transaction status", GMBillSafe::LOGLEVEL_ERROR);
			$main_content .= '<p class="error">'.$text->get_text('general_error').'</p>';
		}
	} else {
		$bs->_log("ERROR: Invalid transaction status response or protocol error", GMBillSafe::LOGLEVEL_ERROR);
		$main_content .= '<p class="error">'.$text->get_text('general_error').'</p>';
	}
}

/* output */
$breadcrumb->add("BillSAFE", xtc_href_link(basename(__FILE__)));
$smarty = new Smarty;
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');
require_once (DIR_WS_INCLUDES.'header.php');
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('bill_main_content', $main_content);
if(isset($_SESSION['billsafe_token'])) {
	$smarty->assign('billsafe_token', $_SESSION['billsafe_token']);
}
$smarty->assign('layerform_action', DIR_WS_CATALOG.basename(__FILE__));
$smarty->assign('lpg_close_url', DIR_WS_CATALOG.basename(__FILE__));
$smarty->assign('layerform_button', $bs->get_text('layerform_button'));
$smarty->assign('sandbox_mode', strtolower(constant('MODULE_PAYMENT_'.strtoupper($_SESSION['payment']).'_SANDBOX')) == 'true' ? 'true' : 'false');
$smarty->assign('product', $bs->getSubmodule()); // invoice|installment
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->caching = false;

$main_content = $smarty->fetch(cseo_get_usermod('base/module/checkout_billsafe.html', USE_TEMPLATE_DEVMODE));

$smarty->assign('main_content', $main_content);

$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');
