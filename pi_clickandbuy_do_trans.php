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
include(DIR_WS_LANGUAGES . $language . '/modules/payment/pi_clickandbuy_recurring.php');

require (DIR_WS_CLASSES . 'order.php');

$cabApi     = new pi_clickandbuy_functions();
$cabXtcApi  = new pi_clickandbuy_xtc_functions();
$order = unserialize($_SESSION['pi']['order']);
$inc = true;
$paymentType = $_POST['paymentType'];

$checkExternalID = $_POST['externalID'];
$sHash = $_POST['sHash'];
$XTCsid = xtc_session_id();

$shipping_tax_rate = $cabXtcApi->get_shipping_tax_rate($order);

if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
    $basketAmount = $order->info['total'] + $order->info['tax'];
} else {
    $basketAmount = $order->info['total'];
}

$basketAmount = round($xtPrice->xtcCalculateCurrEx($basketAmount, $_SESSION['currency']), $xtPrice->get_decimal_places($_SESSION['currency']));

//Shop has tax class in shipping module add shipping tax to order total 
if ($shipping_tax_rate > 0) {
    $shipping_tax = round(($order->info ['shipping_cost'] / 100) * $shipping_tax_rate, 2);
    $basketAmount = ($basketAmount + $shipping_tax);
}

$basketAmount = number_format($basketAmount, 2, '.', '');
$currency = $_SESSION['currency'];
$customer_id = $_SESSION['customer_id'];

$authentication = $cabXtcApi->getCabSettings($paymentType);
$secretKey = $authentication['secretKey'];

//Generate sHash for double check
$checkSHash = $cabXtcApi->generateSHash($basketAmount, $currency, $checkExternalID, $secretKey, $paymentType);

//SHash check#1 
if ($sHash != $checkSHash) {
    $errors[] = utf8_encode(CLICKANDBUY_ERROR_MESSAGE_1);
}

if (!empty($errors)) {
    $error_reason = implode(";", $errors);
    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'XTCsid=' . $XTCsid . '&error_message=' . $error_reason, 'SSL'));
} else {
    $details = array();
    $shippingAddress = array();
    $billingAddress = array();
    $items = array();
    $recurring = array();

    $debited = $basketAmount;
    $authorization = 0;

    //Text for shipping costs
    $shippingCostsText = utf8_encode(MODULE_PAYMENT_PI_CLICKANDBUY_SHIPPING_COSTS_TEXT);

    //Create a new externalID avoid reload problem or double call 
    $externalID = substr(md5(uniqid(rand())), 0, 12);
    $sHash = $cabXtcApi->generateSHash($basketAmount, $currency, $externalID, $secretKey, $paymentType);

    //successURL and failureURL
    $shopURL = (ENABLE_SSL ? HTTPS_SERVER . DIR_WS_CATALOG : HTTP_SERVER . DIR_WS_CATALOG);
    $successURL = $shopURL . "pi_clickandbuy_trans.php?externalID=" . $externalID . "&sHash=" . $sHash . "&paymentType=" . $paymentType;
    $failureURL = $shopURL . FILENAME_CHECKOUT_PAYMENT . "?XTCsid=" . $XTCsid;

    $consumerIPAddress = $_SERVER['REMOTE_ADDR'];
    $consumerLanguage = $cabXtcApi->getConsumerLanguage();
    $consumerCountry = strtolower($order->customer['country']['iso_code_2']);

    $details['amount'] = $basketAmount;
    $details['currency'] = $currency;
    $details['successURL'] = $successURL;
    $details['failureURL'] = $failureURL;
    $details['consumerIPAddress'] = $consumerIPAddress;
    $details['externalID'] = $externalID;
    $details['consumerLanguage'] = $consumerLanguage;
    $details['consumerCountry'] = $consumerCountry;
    $details['orderDescription'] = utf8_encode(CLICKANDBUY_CHECKOUT_ORDER_DESCRIPTION . STORE_NAME);

    //Shipping details	
    if (empty($_POST['shippingCompany'])) {
        $shippingType = 'consumer';
        $shippingAddress['firstName'] = utf8_encode($order->delivery['firstname']);
        $shippingAddress['lastName'] = utf8_encode($order->delivery['lastname']);
    } else {
        $shippingType = 'company';
        $shippingAddress['name'] = utf8_encode($order->delivery['company']);
    }

    $shippingAddress['address']['street'] = utf8_encode($order->delivery['street_address']);
    $shippingAddress['address']['zip'] = $order->delivery['postcode'];
    $shippingAddress['address']['city'] = utf8_encode($order->delivery['city']);
    $shippingAddress['address']['country'] = strtolower($order->delivery['country']['iso_code_2']);

    //Billing details	
    if (empty($order->billing['company'])) {
        $billingType = 'consumer';
        $billingAddress['firstName'] = utf8_encode($order->billing['firstname']);
        $billingAddress['lastName'] = utf8_encode($order->billing['lastname']);
    } else {
        $billingType = 'company';
        $billingAddress['name'] = $order->billing['company'];
    }

    $billingAddress['address']['street'] = utf8_encode($order->billing['street_address']);
    $billingAddress['address']['zip'] = $order->billing['postcode'];
    $billingAddress['address']['city'] = utf8_encode($order->billing['city']);
    $billingAddress['address']['country'] = strtolower($order->billing['country']['iso_code_2']);

    $items[1]['itemType'] = 'item1Text';
    $items[1]['textItemDescription'] = utf8_encode(CLICKANDBUY_CHECKOUT_ORDER_DESCRIPTION . STORE_NAME);

    $items = $cabXtcApi->getItems($currency);

    //ClickandBuy payRequestRecurring Process
    if (($authentication['recUseCase'] == 'Fast Checkout') AND ($paymentType == 'clickandbuyfastcheckout')) {
        $authorization = 1;

        $value_query = xtc_db_query("SELECT authorizationID FROM picab_orders WHERE shopUserID = '" . $customer_id . "' AND  authorization = '1' AND  authorizationID > 0 AND handshake = 1 ORDER BY created  DESC ");
        $value = xtc_db_fetch_array($value_query);
        $authorizationID = $value['authorizationID'];

        if (!empty($authorizationID)) {
            $details['recurringAuthorizationID'] = $authorizationID;
            $requestResult = $cabApi->payRequestRecurring($authentication, $details, $shippingType, $shippingAddress, $billingType, $billingAddress, $items);

            if ($requestResult['values']['transaction']['transactionStatus'] == 'SUCCESS') {
                $transactionID = $requestResult['values']['transaction']['transactionID'];
                $transactionStatus = $requestResult['values']['transaction']['transactionStatus'];

                xtc_db_query("INSERT INTO picab_orders (id,shopOrderID,shopUserID,transactionID,externalID,paymentType,authorization,authorizationID,handshake,closed,recAmount,amount,debited,refunded,cancelled,currency,created,modified) VALUES (NULL,'" . $shopOrderID . "', '" . $customer_id . "', '" . $transactionID . "','" . $externalID . "','" . $paymentType . "','" . $authorization . "','" . $authorizationID . "',1,0,'" . $authentication['recAmount'] . "','" . $basketAmount . "','0.00',0.00,0.00,'" . $currency . "','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "')");

                //Redirect checkout process finalize order
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PROCESS, 'XTCsid=' . $XTCsid . '&amount=' . $basketAmount . '&currency=' . $currency . '&externalID=' . $externalID . '&paymentType=' . $paymentType . '&sHash=' . $sHash, 'SSL'));
            } else {
                $inc = false;

                //Create a new externalID avoid reload problem or double call 
                $externalID = substr(md5(uniqid(rand())), 0, 12);
                $sHash = $cabXtcApi->generateSHash($basketAmount, $currency, $externalID, $secretKey, $paymentType);
                $successURL = $shopURL . "pi_clickandbuy_trans.php?externalID=" . $externalID . "&sHash=" . $sHash . "&paymentType=" . $paymentType;

                $details['successURL'] = $successURL;
                $details['externalID'] = $externalID;
            }
        }
    }

    //ClickandBuy payRequest Process
    if (($paymentType == 'clickandbuyrecurring') || ($paymentType == 'clickandbuypartialdelivery') || ($paymentType == 'clickandbuyfastcheckout')) {
        $authorization = 1;
        empty($authentication['recDescription']) ? $recurring['description'] = utf8_encode('Billing Agreement') : $recurring['description'] = utf8_encode($authentication['recDescription']);

        if ($authentication['initialAmount'] == 'True') {
            $debited = $details['amount'] = '0.00';
        } else {
            $details['amount'] = $basketAmount;
        }

        $authentication['recNumberOfBillings'] == 0 ? $recurring['numberLimit'] = $authentication['partialDeliveryBillings'] : $recurring['numberLimit'] = $authentication['recNumberOfBillings'];

        (empty($authentication['recAmount']) || $authentication['recAmount'] == 0) ? $recAmount = $basketAmount : $recAmount = $authentication['recAmount'];
        $recurring['amountLimit']['amount'] = $recAmount;
        empty($authentication['recCurrency']) ? $recurring['amountLimit']['currency'] = $currency : $recurring['amountLimit']['currency'] = $authentication['recCurrency'];

        empty($authentication['recDateLimit']) ? $recurring['expireDate'] = '' : $recurring['expireDate'] = $authentication['recDateLimit'];
        $authentication['recRevokableByConsumer'] == 'True' ? $recurring['revokableByConsumer'] = 'true' : $recurring['revokableByConsumer'] = 'false';
    }

    if ($paymentType == 'clickandbuypartialdelivery') {
        $partialDeliveryAmount = 0;
        $partialDeliveryBillings = 0;
        $initialAmountZero = false;

        $items = $cabXtcApi->getItemsPartialDelivery();

        if ($initialAmountZero == true) {
            $debited = '0.00';
            $partialDeliveryAmount = $basketAmount;
        } else {
            $debited = number_format(($basketAmount - $partialDeliveryAmount), 2, '.', '');
        }

        $details['amount'] = $debited;
        $recAmount = $partialDeliveryAmount;

        if ($partialDeliveryAmount > 0) {
            $recurring['numberLimit'] = $partialDeliveryBillings;
            $recurring['amountLimit']['amount'] =  number_format($recAmount, 2, '.', '');
            $recurring['amountLimit']['currency'] = $currency;
        } else {
            $recurring = array();
        }
    }

    if ($paymentType == 'clickandbuyfastcheckout') {
        $details['amount'] = $basketAmount;
        $debited = $basketAmount;
        $recAmount = 0;

        $recurring['numberLimit'] = '';
        $recurring['amountLimit']['amount'] = '';
        $recurring['amountLimit']['currency'] = '';
    }
    $requestResult = $cabApi->payRequest($authentication, $details, $shippingType, $shippingAddress, $billingType, $billingAddress, $items, $recurring, $inc);

    if ($requestResult['success'] != 1) {
        $cabXtcApi->redirectError($requestResult);
    }

    $payRequest = $requestResult['values'];
    $transactionID = $payRequest['transaction']['transactionID'];
    $transactionStatus = $payRequest['transaction']['transactionStatus'];
    $redirectURL = $payRequest['transaction']['redirectURL'];
    empty($payRequest['transaction']['createdRecurringPaymentAuthorization']['recurringPaymentAuthorizationID']) ? $authorizationID = 'NULL' : $authorizationID = $payRequest['transaction']['createdRecurringPaymentAuthorization']['recurringPaymentAuthorizationID'];

    /**
     * @todo authorizationId partial deliver?!?!?!?
     */
    
    xtc_db_query("INSERT INTO picab_orders (id,shopOrderID,shopUserID,transactionID,externalID,paymentType,authorization,authorizationID,handshake,closed,recAmount,amount,debited,refunded,cancelled,currency,created,modified) VALUES (NULL,'', '" . $customer_id . "', '" . $transactionID . "','" . $externalID . "','" . $paymentType . "','" . $authorization . "','" . $authorizationID . "',0,0,'" . $recAmount . "','" . $basketAmount . "','" . $debited . "',0.00,0.00,'" . $currency . "','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "')");

    //Redirect to ClickandBuy Payment Page
    header("Location: " . $redirectURL);
    exit();
}

require('includes/application_bottom.php');
?>