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
$language = $_SESSION['language'];

include('../lang/' . $language . '/admin/modules/payment/pi_clickandbuy.php');
include('../includes/classes/pi_clickandbuy_functions.php');
include('../includes/classes/class.pi_clickandbuy_xtc_functions.php');

$cabApi         = new pi_clickandbuy_functions();
$cabXtcApi      = new pi_clickandbuy_xtc_functions();

$shopOrderID = $_POST['oID'];

!empty($_POST['actionSave']) ? $actionSave = true : $actionSave = false;

$value_query = xtc_db_query("SELECT * FROM picab_orders WHERE shopOrderID ='" . $shopOrderID . "'");
$cabOrderDetails = xtc_db_fetch_array($value_query);

$authentication = $cabXtcApi->getCabSettings($cabOrderDetails['paymentType']);

$externalID = 'NULL';

if ($actionSave) {
    $transactionID = $cabOrderDetails['transactionID'];
    $authorizationId = $cabOrderDetails['authorizationID'];
    $requestResult = $cabApi->cancelRequest($authentication, $transactionID, $authorizationId, CANCEL_MODE_RPA);
    $requestValues = $requestResult['values'];
    if ($requestValues['transaction']['createdRecurringPaymentAuthorization']['recurringPaymentAuthorizationStatus'] == 'CANCELLED') {
        $messageBox = 'SUCCESS';
        $transactionID = $requestValues['transaction']['transactionID'];
        $transactionStatus = $requestValues['transaction']['transactionStatus'];
        xtc_db_query("INSERT INTO picab_transactions
                             (id,shopOrderID,transactionID,externalID,transactionType,description,amount,currency,paid,status,created,modified)
                      VALUES (NULL,'" . $shopOrderID . "','" . $transactionID . "','" . $externalID . "','cancel_authorize','" . $itemDesc1 . "'," . 0 . ",'" . $cabOrderDetails['currency'] . "',1,'" . $transactionStatus . "',NOW(),NOW())");

        xtc_redirect(xtc_href_link('pi_clickandbuy_details.php', 'oID=' . $shopOrderID . '&picabstatus=SUCCESS', 'SSL'));
    } else {
        $messageBox = 'ERROR';
        $errorDescription = $requestResult['values']['detail']['errorDetails']['description'];
        xtc_redirect(xtc_href_link('pi_clickandbuy_details.php', 'oID=' . $shopOrderID . '&picabstatus=ERROR&picabmessage=' . $errorDescription, 'SSL'));
    }
} elseif ($actionSave && $cancelled > 0.00) {
    $messageBox = 'ERROR';
    $errorDescription = CLICKANDBUY_ORDER_CANCEL_IS_CANCELLED;
    xtc_redirect(xtc_href_link('pi_clickandbuy_details.php', 'oID=' . $shopOrderID . '&picabstatus=ERROR&picabmessage=' . $errorDescription, 'SSL'));
}

?>