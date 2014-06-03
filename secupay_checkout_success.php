<?php

include ('includes/application_top.php');

if (file_exists(DIR_WS_CLASSES . 'payment/class.secupay_api.php')) {
    require_once(DIR_WS_CLASSES . 'payment/class.secupay_api.php');
} else {
    require_once("../" . DIR_WS_CLASSES . 'payment/class.secupay_api.php');
}

$logme = MODULE_PAYMENT_SPKK_LOGGING == "Ja";
unset($_SESSION['iframe_link']);

if ($_SESSION['uid'] == $_REQUEST['uid']) {

    // when uid matches, we check transaction status on secupay, if accepted, then everything is ok
    $data = array();
    $data['hash'] = $_SESSION['sp_hash'];
    $data['apikey'] = MODULE_PAYMENT_SECUPAY_APIKEY;
    $data['amount'] = $_SESSION['sp_amount'];
    $request = array();
    $request['data'] = $data;
    $sp_api = new secupay_api($request, 'status/' . $data['hash'], 'application/json', $logme);
    $response = $sp_api->request();

    //save transaction_id
    if (isset($response->data->trans_id) && !empty($response->data->trans_id)) {
        try {
            @xtc_db_query("UPDATE secupay_transaction_order SET transaction_id=" . intval($response->data->trans_id) . " WHERE hash ='" . xtc_db_input($data['hash']) . "'");

        } catch (Exception $e) {
            secupay_log($logme, ' secupay_checkout_success - update trans_id - EXCEPTION: ' . $e->getMessage());
        }
    }
    
    // check API response
    if (isset($response->data->status) && ($response->data->status == 'accepted' || $response->data->status == 'authorized')) {
        $_SESSION['sp_success'] = true;
        header("Location: " . xtc_href_link(FILENAME_CHECKOUT_PROCESS, "", "SSL"));
        echo("Sollten Sie nicht automatisch weitergeleitet werden, klicken sie bitte auf <a href='" . xtc_href_link(FILENAME_CHECKOUT_PROCESS, "", "SSL") . "'>Weiterleiten</a>");
        die();
    }
} else {
    secupay_log($logme, "Request array:");
    secupay_log($logme, $_REQUEST);
}
// attempt to display user friendly error message
if (isset($response->data->status)) {
    $_REQUEST['ERRORCODE'] = 1;
    $_REQUEST['ERROR'] = 'Failed,' . urlencode('Transaktion fehlgeschlagen: ') . $response->data->status;
}

$error = "(" . $_REQUEST['ERRORCODE'] . ") " . $_REQUEST['ERROR'];
if (!isset($_REQUEST['ERROR'])) {
    $error = urlencode('Transaktion fehlgeschlagen.');
}
$payment_error_module = $_REQUEST['payment_error'];
$payment_error_return = 'payment_error=' . $payment_error_module . '&error=' . urlencode($error);
$_SESSION['sp_success'] = false;
if (CHECKOUT_AJAX_STAT == 'true') {
	$_SESSION['checkout_payment_error'] = $payment_error_return;
} else {
	xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
}
echo("Sollten Sie nicht automatisch weitergeleitet werden, klicken sie bitte auf <a href='" . xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, "SSL") . "'>Weiterleiten</a>");
die();