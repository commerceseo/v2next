<?php

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category  PayIntelligent
 * @package   PayIntelligent_ClickandBuy
 * @copyright (C) 2010 PayIntelligent GmbH  <http://www.payintelligent.de/>
 * @license   GPLv2
 */
require('includes/application_top.php');

// if the customer is not logged on, redirect them to the login page
if (!isset($_SESSION['customer_id'])) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

if ($_SESSION['customers_status']['customers_status_show_price'] != '1') {
    xtc_redirect(xtc_href_link(FILENAME_DEFAULT, '', ''));
}

if (!isset($_SESSION['sendto'])) {
    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

if ((xtc_not_null(MODULE_PAYMENT_INSTALLED)) && (!isset($_SESSION['payment']))) {
    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID'])) {
    if ($_SESSION['cart']->cartID != $_SESSION['cartID']) {
        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
}

$language = $_SESSION['language'];

include('includes/classes/pi_clickandbuy_functions.php');
include('includes/classes/class.pi_clickandbuy_xtc_functions.php');
include(DIR_WS_LANGUAGES . $language . '/modules/payment/pi_clickandbuy.php');
include(DIR_WS_CLASSES . 'order.php');

$cabApi         = new pi_clickandbuy_functions();
$cabXtcApi      = new pi_clickandbuy_xtc_functions();
$order = unserialize($_SESSION['pi']['order']);

$XTCsid = xtc_session_id();
$externalID = $_GET["externalID"];
$sHash = $_GET["sHash"];
$paymentType = $_GET['paymentType'];
$partialDeliveryAmount = $_GET['partialDeliveryAmount'];

$orders_query = xtc_db_query("SELECT * FROM picab_orders WHERE externalID = '" . $externalID . "'");
$orders = xtc_db_fetch_array($orders_query);

$amount = $orders['amount'];
$currency = $orders['currency'];
$transactionID = $orders['transactionID'];
$handshake = $orders['handshake'];

$authentication = $cabXtcApi->getCabSettings($paymentType);
$secretKey = $authentication['secretKey'];

$shipping_tax_rate = $cabXtcApi->get_shipping_tax_rate($order);

if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
    $shopAmount = $order->info['total'] + $order->info['tax'];
} else {
    $shopAmount = $order->info['total'];
}

$shopAmount = round($xtPrice->xtcCalculateCurrEx($shopAmount, $_SESSION['currency']), $xtPrice->get_decimal_places($_SESSION['currency']));

//Shop has tax class in shipping module add shipping tax to order total
if ($shipping_tax_rate > 0) {
    $shipping_tax = round(($order->info ['shipping_cost'] / 100) * $shipping_tax_rate, 2);
    $shopAmount = ($shopAmount + $shipping_tax);
}

$shopAmount = number_format($shopAmount, 2, '.', '');
$shopCurrency = $_SESSION['currency'];

//Handshake check
if ($handshake != 0 || $handshake == "") {
    $errors[] = CLICKANDBUY_ERROR_MESSAGE_7;
}

//Generate sHash for double check#1
$checkSHash = $cabXtcApi->generateSHash($shopAmount, $shopCurrency, $externalID, $secretKey, $paymentType, $partialDeliveryAmount);
if ($sHash != $checkSHash) {
    $errors[] = CLICKANDBUY_ERROR_MESSAGE_8;
}
unset($_SESSION['pi']['order']);
if (empty($errors)) {
    $statusType = 'transactionID';
    $idList['transactionID1'] = $transactionID;
    $requestResult = $cabApi->statusRequest($authentication, $statusType, $idList);

    // ClickandBuy Check
    if ($requestResult['success'] != 1) {
        redirectError($requestResult);
    } else {
        $requestValues = $requestResult['values'];
        $transactionStatus = $requestValues['transactionList']['transaction']['transactionStatus'];

        //Redirect checkout process finalize order
        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PROCESS, 'XTCsid=' . $XTCsid . '&amount=' . $amount . '&currency=' . $currency . '&externalID=' . $externalID . '&paymentType=' . $paymentType . '&sHash=' . $sHash, 'SSL'));
    }
} else {
    $errDescription = implode(";", $errors);
    $errorMessage = CLICKANDBUY_ERROR_MESSAGE_2 . $errDescription;

    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '&error_message=' . $errorMessage, 'SSL'));
}
?>