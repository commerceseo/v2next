<?php
include ('includes/application_top.php');
if(file_exists(DIR_WS_CLASSES . 'payment/class.secupay_api.php')) {
    require_once(DIR_WS_CLASSES . 'payment/class.secupay_api.php');
}else{
    require_once("../".DIR_WS_CLASSES . 'payment/class.secupay_api.php');
}

$error = $_REQUEST['error'];
$payment_error_module = $_REQUEST['payment_error'];

if (isset($error)) {
    $payment_error_return = 'payment_error='.$payment_error_module.'&error=' . urlencode($error);
} elseif (isset($payment_error_module)) {
    $payment_error_return = 'payment_error='.$payment_error_module.'&error=' . urlencode('Transaktion abgebrochen oder fehlgeschlagen.');
}
$_SESSION['sp_success'] = false;

unset($_SESSION['iframe_link']);
unset($_SESSION['sp_tag']);
if (CHECKOUT_AJAX_STAT == 'true') {
	$_SESSION['checkout_payment_error'] = $payment_error_return;
} else {
	xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
}
echo("Sollten Sie nicht automatisch weitergeleitet werden, klicken sie bitte auf <a href='" . xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, "SSL") . "'>Weiterleiten</a>");
die();
