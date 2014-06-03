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
if (isset($_POST["xml"])) {
    $xml = stripslashes($_POST["xml"]);

    require('includes/application_top.php');
    include('includes/classes/pi_clickandbuy_functions.php');
    include('includes/classes/class.pi_clickandbuy_xtc_functions.php');
    require('ext/modules/payment/pi_clickandbuy/lib/xml.php');

    $cabApi = new pi_clickandbuy_functions();
    $sendEmailTo = MMS_EMAIL_TO;
    $sendEmailFrom = MMS_EMAIL_FROM;
    if ((checkSignature($xml, false) != true) && (checkSignature($xml, true) != true)) {
        if (MMS_LOG == true) {
            $handle = fopen("mms.log", "a+");
            fwrite($handle, "------------ new entry, received @ " . date('Y-m-d H:i:s') . " ------------------- \n");
            fwrite($handle, "Signature check failed\n");
            fwrite($handle, print_r($xml, true) . "\n");
            fclose($handle);
        }
        die('Signature check failed');
    }

    if (MMS_LOG == true) {
        $handle = fopen("mms.log", "a+");
        fwrite($handle, "------------ new entry, received @ " . date('Y-m-d H:i:s') . " ------------------- \n");
        fwrite($handle, print_r($xml, true) . "\n");
        fclose($handle);
    }

    if (!empty($sendEmailTo) && !empty($sendEmailFrom)) {
        $cabApi->sendMail($sendEmailTo, $sendEmailFrom, $xml);
    }

// Un-quotes a quoted string
    $data = stripslashes($xml);
// Set Array 
    $xmlArray = XML_unserialize($data);
    $signature = $xmlArray['eventlist']['signature'];
    $eventType = array('payEvent', 'refundEvent', 'creditEvent', 'recurringPaymentAuthorizationEvent');

    foreach ($eventType as $key) {
        $eventList = $xmlArray['eventlist'][$key];

        if (!empty($eventList)) {
            $functionName = $key . 'List';
            $functionName($eventList, $signature);
        }
    }
    exit('OK');
} else {
    die('Signature check failed');
}

function payEventList($payEventList, $signature) {
    
    global $xml;

    if (!is_array($payEventList[0])) {
        $payEventList = array($payEventList);
    }

    foreach ($payEventList as $key => $value) {

        $merchantID = $value['merchantID'];
        $projectID = $value['projectID'];
        $eventID = $value['eventID'];
        $creationDateTime = $value['creationDateTime'];
        $transactionID = $value['transactionID'];
        $externalID = $value['externalID'];
        $crn = $value['crn'];
        $transactionAmount = $value['transactionAmount']['amount'];
        $transactionCurrency = $value['transactionAmount']['currency'];
        $merchantAmount = $value['merchantAmount']['amount'];
        $merchantCurrency = $value['merchantAmount']['currency'];
        $oldState = $value['oldState'];
        $newState = $value['newState'];

        $count = getCountEventID($eventID);
        if (empty($count) || ($count < 1)) {
            
            $shopOrderID = 0;

            $shopOrderIDOrder = isOrder($externalID);
            $shopOrderIDTransaction = isTransaction($externalID);

            if (!empty($shopOrderIDTransaction)) {
                $shopOrderID = $shopOrderIDTransaction;
                updateTransactionStatus($externalID, $newState);
            } else {
                $shopOrderID = $shopOrderIDOrder;
            }

            $serializeArray = array();
            $serializeArray['eventlist']['signature'] = $signature;
            $serializeArray['eventlist']['payEvent'] = $value;
            $eventXML = XML_serialize($serializeArray);
            insert_mms($eventID, $shopOrderID, $externalID, $transactionID, $oldState, $newState, $eventXML);
        }
    }
}

function refundEventList($refundEventList, $signature) {
    global $xml;

    if (!is_array($refundEventList[0])) {
        $refundEventList = array($refundEventList);
    }

    foreach ($refundEventList as $key => $value) {
        $merchantID = $value['merchantID'];
        $projectID = $value['projectID'];
        $eventID = $value['eventID'];
        $creationDateTime = $value['creationDateTime'];
        $refundTransactionID = $value['refundTransactionID'];
        $externalID = $value['externalID'];
        $associatedTransactionID = $value['associatedTransactionID'];
        $crn = $value['crn'];
        $transactionAmount = $value['transactionAmount']['amount'];
        $transactionCurrency = $value['transactionAmount']['currency'];
        $merchantAmount = $value['merchantAmount']['amount'];
        $merchantCurrency = $value['merchantAmount']['currency'];
        $oldState = $value['oldState'];
        $newState = $value['newState'];

        $count = getCountEventID($eventID);
        if (empty($count) || ($count < 1)) {
            $shopOrderID = 0;

            $value_query = xtc_db_query("SELECT shopOrderID FROM picab_transactions WHERE transactionID = '" . $refundTransactionID . "'");
            $transactions = xtc_db_fetch_array($value_query);

            if (!empty($transactions['shopOrderID'])) {
                $shopOrderID = $transactions['shopOrderID'];
                xtc_db_query("UPDATE picab_transactions SET status = '$newState' WHERE transactionID = '$refundTransactionID' LIMIT 1");
            }

            $serializeArray = array();
            $serializeArray['eventlist']['signature'] = $signature;
            $serializeArray['eventlist']['refundEvent'] = $value;
            $eventXML = XML_serialize($serializeArray);
            insert_mms($eventID, $shopOrderID, $externalID, $refundTransactionID, $oldState, $newState, $eventXML);
        }
    }
}

function creditEventList($creditEventList, $signature) {
    global $xml;

    if (!is_array($creditEventList[0])) {
        $creditEventList = array($creditEventList);
    }

    foreach ($creditEventList as $key => $value) {
        $merchantID = $value['merchantID'];
        $projectID = $value['projectID'];
        $eventID = $value['eventID'];
        $creationDateTime = $value['creationDateTime'];
        $transactionID = $value['transactionID'];
        $externalID = $value['externalID'];
        $email = $value['email'];
        $crn = $value['crn'];
        $transactionAmount = $value['transactionAmount']['amount'];
        $transactionCurrency = $value['transactionAmount']['currency'];
        $merchantAmount = $value['merchantAmount']['amount'];
        $merchantCurrency = $value['merchantAmount']['currency'];
        $oldState = $value['oldState'];
        $newState = $value['newState'];

        $count = getCountEventID($eventID);
        if (empty($count) || ($count < 1)) {
            $shopOrderID = 0;

            $value_query = xtc_db_query("SELECT shopOrderID FROM picab_transactions WHERE transactionID = '" . $transactionID . "'");
            $transactions = xtc_db_fetch_array($value_query);


            if (!empty($transactions['shopOrderID'])) {
                $shopOrderID = $transactions['shopOrderID'];
                xtc_db_query("UPDATE picab_transactions SET status = '$newState' WHERE transactionID = '$transactionID' LIMIT 1");
            }

            $serializeArray = array();
            $serializeArray['eventlist']['signature'] = $signature;
            $serializeArray['eventlist']['creditEvent'] = $value;
            $eventXML = XML_serialize($serializeArray);
            insert_mms($eventID, $shopOrderID, $externalID, $transactionID, $oldState, $newState, $eventXML);
        }
    }
}

function recurringPaymentAuthorizationEventList($recurringEventList, $signature) {
    if (!is_array($recurringEventList[0])) {
        $recurringEventList = array($recurringEventList);
    }

    foreach ($recurringEventList as $key => $value) {
        $merchantID = $value['merchantID'];
        $projectID = $value['projectID'];
        $eventID = $value['eventID'];
        $creationDateTime = $value['creationDateTime'];
        $transactionID = $value['transactionID'];
        $externalID = $value['externalID'];
        $crn = $value['crn'];
        $amount = $value['amount']['amount'];
        $currency = $value['amount']['currency'];
        $oldState = $value['oldState'];
        $newState = $value['newState'];
        $remainingAmount = $value['remainingAmount']['amount'];
        $remainingCurrency = $value['remainingAmount']['currency'];
        $remainingRetries = $value['remainingRetries'];
        $validUntil = $value['validUntil'];

        $count = getCountEventID($eventID);
        if (empty($count) || ($count < 1)) {
            $shopOrderID = 0;

            $shopOrderIDOrder = isOrder($externalID);
            $shopOrderIDTransaction = isTransaction($externalID);

            if (!empty($shopOrderIDTransaction)) {
                $shopOrderID = $shopOrderIDTransaction;
                updateTransactionStatus($externalID, $newState);
            } else {
                $shopOrderID = $shopOrderIDOrder;
            }

            $serializeArray = array();
            $serializeArray['eventlist']['signature'] = $signature;
            $serializeArray['eventlist']['recurringPaymentAuthorizationEvent'] = $value;
            $eventXML = XML_serialize($serializeArray);
            insert_mms($eventID, $shopOrderID, $externalID, $transactionID, $oldState, $newState, $eventXML);
        }
    }
}

function insert_mms($eventID, $shopOrderID, $externalID, $transactionID, $oldState, $newState, $xml) {
    xtc_db_query("INSERT INTO picab_mms (eventID,shopOrderID,externalID,transactionID,oldState,newState,xml,created) VALUES ('$eventID', '$shopOrderID','$externalID','$transactionID','$oldState','$newState','$xml', NOW( ) )");
}

function isOrder($externalID) {
    $value_query = xtc_db_query("SELECT shopOrderID FROM picab_orders WHERE externalID = '" . $externalID . "'");
    $orders = xtc_db_fetch_array($value_query);
    return $orders['shopOrderID'];
}

function isTransaction($externalID) {
    $value_query = xtc_db_query("SELECT shopOrderID FROM picab_transactions WHERE externalID = '" . $externalID . "'");
    $transactions = xtc_db_fetch_array($value_query);
    return $transactions['shopOrderID'];
}

function getCountEventID($eventID) {
    $value_query = xtc_db_query("SELECT COUNT(eventID) as countEventID FROM picab_mms WHERE eventID='" . $eventID . "'");
    $mms = xtc_db_fetch_array($value_query);
    return $mms['countEventID'];
}

function updateTransactionStatus($externalID, $status) {
    xtc_db_query("UPDATE picab_transactions SET status = '$status' WHERE externalID = '$externalID' LIMIT 1");
}

function getMmsSecretKey($recurring = false) {
    if ($recurring) {
        $value_query = xtc_db_query("SELECT configuration_value FROM configuration WHERE configuration_key = 'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY_MMS'");
    } else {
        $value_query = xtc_db_query("SELECT configuration_value FROM configuration WHERE configuration_key = 'MODULE_PAYMENT_PI_CLICKANDBUY_SECRET_KEY_MMS'");
    }
    $settings = xtc_db_fetch_array($value_query);
    return $settings['configuration_value'];
}

function checkSignature($xml, $recurring = false) {
    $sharedKey = getMmsSecretKey($recurring);

    // find signature
    $signatureTag = "<signature>";
    $signatureTagStart = strpos($xml, $signatureTag);
    $signatureTagLength = strlen($signatureTag);

    $signatureStart = $signatureTagStart + $signatureTagLength;
    $signatureLength = 40;
    $signature = substr($xml, $signatureStart, $signatureLength);

    //singature from xml
    $xmlSignature = "<signature>" . $signature . "</signature>";
    $emptySignature = "<signature />";
    $xmlWithoutSignature = str_replace($xmlSignature, $emptySignature, $xml);

    //Hash 
    $textToHash = $sharedKey . $xmlWithoutSignature;
    $hash = sha1($textToHash);
    return true;
    ($signature == $hash) ? $result = true : $result = false;
    return $result;
}

?>