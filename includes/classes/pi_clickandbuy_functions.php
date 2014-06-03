<?php

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category  PayIntelligent
 * @package   PayIntelligent_ClickandBuy
 * @copyright (C) 2010 PayIntelligent GmbH  <http://www.payintelligent.de/>
 * @license   http://www.gnu.org/licenses/  GNU General Public License 3
 */
require_once('pi_clickandbuy_constants.php');
require_once('pi_clickandbuy_functions_shop.php');
require_once(NUSOAP_FOLDER . 'nusoap.php');

class pi_clickandbuy_functions
{
    var $piCabFunctionsShop;
    var $client;

    function pi_clickandbuy_functions()
    {
        $this->piCabFunctionsShop = new pi_clickandbuy_functions_shop();
        $this->client = new nusoap_client($this->piCabFunctionsShop->getSoapEndpoint());

    }

    function doSoapRequest($reqName, $reqParam)
    {
        $this->client->soap_defencoding = "UTF-8";
        $success = false;

        $result = $this->client->call($reqName, $reqParam, SOAP_NAMESPACE, SOAP_ACTION, false, null, "rpc", "literal");

        if ($this->client->fault) {
            $nusoapResult['error_type'] = 'fault';
            $nusoapResult['faultcode'] = $this->client->faultcode;
            $nusoapResult['faultstring'] = $this->client->faultstring;
            $nusoapResult['faultdetail'] = $this->client->fault_detail;
        } elseif ($this->client->getError()) {
            $nusoapResult['error_type'] = 'error';
            $nusoapResult['error'] = $this->client->getError();
        } else {
            $success = true;
        }

        $nusoapResult['success'] = $success;
        $nusoapResult['values'] = $result;
        $nusoapResult['req_name'] = $reqName;
        $nusoapResult['request'] = $this->client->request;
        $nusoapResult['response'] = $this->client->response;
        return $nusoapResult;
    }

    function generateToken($projectID, $secretKey)
    {
        $timestamp = gmdate("YmdHis");
        $hashStr = $projectID . "::" . $secretKey . "::" . $timestamp;
        $toBeHashed = strtoupper(sha1($hashStr));
        $token = $timestamp . '::' . $toBeHashed;

        return $token;
    }

    function removeEmptyTag($arr)
    {
        foreach ($arr as $key => $value) {
            if (is_array($arr[$key])) {
                foreach ($arr[$key] as $key2 => $value2) {
                    if (empty($arr[$key][$key2])) {
                        unset($arr[$key][$key2]);
                    }
                }
            }
            if (empty($arr[$key])) {
                unset($arr[$key]);
            }
        }
        return $arr;
    }

    function createItemList($items)
    {
        // Fill itemListArr
        $itemListArr = array();
        foreach ($items as $key => $value) {
            $item = 'item' . $key;
            if ($value['itemType'] == $item . 'Text') {
                array_push($itemListArr, new soapval('item', false, array('itemType' => 'TEXT', 'description' => $value['textItemDescription'])));
            } elseif ($value['itemType'] == $item . 'Item') {
                array_push($itemListArr, new soapval('item', false,
                                array('itemType' => 'ITEM',
                                    'description' => $value['itemDescription'],
                                    'quantity' => $value['itemQuantity'],
                                    new soapval('unitPrice', false, array('amount' => $value['itemUnitPriceAmount'], 'currency' => $value['itemUnitPriceCurrency'])),
                                    new soapval('totalPrice', false, array('amount' => $value['itemTotalPriceAmount'], 'currency' => $value['itemTotalPriceCurrency']))
                                )
                        )
                );
            } elseif ($value['itemType'] == $item . 'Subtotal') {
                array_push($itemListArr, new soapval('item', false,
                                array('itemType' => 'SUBTOTAL',
                                    'description' => $value['subtotalItemDescription'],
                                    new soapval('totalPrice', false, array('amount' => $value['subtotalItemTotalPriceAmount'], 'currency' => $value['subtotalItemTotalPriceCurrency']))
                                )
                        )
                );
            } elseif ($value['itemType'] == $item . 'Vat') {
                array_push($itemListArr, new soapval('item', false,
                                array('itemType' => 'VAT',
                                    'description' => $value['vatItemDescription'],
                                    new soapval('totalPrice', false, array('amount' => $value['vatItemTotalPriceAmount'], 'currency' => $value['vatItemTotalPriceCurrency']))
                                )
                        )
                );
            } elseif ($value['itemType'] == $item . 'Total') {
                array_push($itemListArr, new soapval('item', false,
                                array('itemType' => 'TOTAL',
                                    'description' => $value['totalItemDescription'],
                                    new soapval('totalPrice', false, array('amount' => $value['totalItemTotalPriceAmount'], 'currency' => $value['totalItemTotalPriceCurrency']))
                                )
                        )
                );
            }
        }
        return $itemListArr;
    }

    function payRequest($authentication, $details, $shippingType, $shippingAddress, $billingType, $billingAddress, $items, $createRecurring, $inc=true)
    {

        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $amountArr = array(
            'amount' => $details['amount'],
            'currency' => $details['currency']
        );

        $shippingAddressArr = array(
            $shippingType => $shippingAddress
        );

        $billingAddressArr = array(
            $billingType => $billingAddress
        );

        $itemListArr = array();
        if (!empty($items)) {
            $itemListArr = $this->createItemList($items);
        }
        $orderDetailsArr = array(
            'text' => $details['orderDescription'],
            'itemList' => $itemListArr
        );

        $orderDetailsArr = $this->removeEmptyTag($orderDetailsArr);
        $createRecurring = $this->removeEmptyTag($createRecurring);

        $detailsArr = array(
            'amount' => $amountArr,
            'basketRisk' => $details['basketRisk'],
            'clientRisk' => $details['clientRisk'],
            'authExpiration' => $details['authExpiration'],
            'confirmExpiration' => $details['confirmExpiration'],
            'successExpiration' => $details['successExpiration'],
            'successURL' => $details['successURL'],
            'failureURL' => $details['failureURL'],
            'consumerIPAddress' => $details['consumerIPAddress'],
            'externalID' => $details['externalID'],
            'consumerLanguage' => $details['consumerLanguage'],
            'consumerCountry' => $details['consumerCountry'],
            'orderDetails' => $orderDetailsArr,
            'shipping' => $shippingAddressArr,
            'billing' => $billingAddressArr,
            'createRecurring' => $createRecurring
        );

        $detailsArr = $this->removeEmptyTag($detailsArr);

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('payRequest_Request', $reqParam);
        return $nusoapResult;
    }

    function payRequestRecurring($authentication, $details, $shippingType, $shippingAddress, $billingType, $billingAddress, $items)
    {
        $this->client = new nusoap_client($this->piCabFunctionsShop->getSoapEndpoint());

        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $amountArr = array(
            'amount' => $details['amount'],
            'currency' => $details['currency']
        );

        $shippingAddressArr = array(
            $shippingType => $shippingAddress
        );

        $billingAddressArr = array(
            $billingType => $billingAddress
        );

        $itemListArr = array();
        if (!empty($items)) {
            $itemListArr = $this->createItemList($items);
        }

        $orderDetailsArr = array(
            'text' => $details['orderDescription'],
            'itemList' => $itemListArr
        );

        $orderDetailsArr = $this->removeEmptyTag($orderDetailsArr);

        $detailsArr = array(
            'amount' => $amountArr,
            'recurringPaymentAuthorizationID' => $details['recurringAuthorizationID'],
            'basketRisk' => $details['basketRisk'],
            'clientRisk' => $details['clientRisk'],
            'externalID' => $details['externalID'],
            'authExpiration' => $details['authExpiration'],
            'successExpiration' => $details['successExpiration'],
            'orderDetails' => $orderDetailsArr,
            'shipping' => $shippingAddressArr,
            'billing' => $billingAddressArr,
        );

        $detailsArr = $this->removeEmptyTag($detailsArr);

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('payRequestRecurring_Request', $reqParam);
        return $nusoapResult;
    }

    function refundRequest($authentication, $details, $items)
    {
        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        $amountArr = array(
            'amount' => $details['amount'],
            'currency' => $details['currency']
        );

        $itemListArr = array();
        if (!empty($items)) {
            $itemListArr = $this->createItemList($items);
        }

        $orderDetailsArr = array(
            'itemList' => $itemListArr
        );

        $orderDetailsArr = $this->removeEmptyTag($orderDetailsArr);

        $detailsArr = array(
            'amount' => $amountArr,
            'transactionID' => $details['transactionID'],
            'externalID' => $details['externalID'],
            'orderDetails' => $orderDetailsArr
        );

        $detailsArr = $this->removeEmptyTag($detailsArr);

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('refundRequest_Request', $reqParam);
        return $nusoapResult;
    }

    function cancelRequest($authentication, $transactionId, $authorizationId = '', $cancelMode = CANCEL_MODE_TX)
    {
        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        if ($cancelMode == CANCEL_MODE_BOTH) {
            $detailsArr = array(
                'cancelMode' => CANCEL_MODE_BOTH,
                'cancelIdentifier' => array('transactionID' => $transactionId,
                    'recurringPaymentAuthorizationID' => $authorizationId)
            );
        } else if ($cancelMode == CANCEL_MODE_RPA) {
            $detailsArr = array(
                'cancelMode' => CANCEL_MODE_RPA,
                'cancelIdentifier' => array('recurringPaymentAuthorizationID' => $authorizationId)
            );
        } else {
            $detailsArr = array(
                'cancelMode' => CANCEL_MODE_TX,
                'cancelIdentifier' => array('transactionID' => $transactionId)
            );
        }

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('cancelRequest_Request', $reqParam);
        return $nusoapResult;
    }

    function creditRequest($authentication, $details, $items)
    {
        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        $amountArr = array(
            'amount' => $details['amount'],
            'currency' => $details['currency']
        );

        $itemListArr = array();
        if (!empty($items)) {
            $itemListArr = $this->createItemList($items);
        }

        $orderDetailsArr = array(
            'itemList' => $itemListArr
        );

        $orderDetailsArr = $this->removeEmptyTag($orderDetailsArr);

        $detailsArr = array(
            'amount' => $amountArr,
            'recipient' => array('emailAddress' => $details['emailAddress']),
            'consumerLanguage' => $details['consumer_language'],
            'orderDetails' => $orderDetailsArr
        );

        $detailsArr = $this->removeEmptyTag($detailsArr);

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('creditRequest_Request', $reqParam);
        return $nusoapResult;
    }

    function statusRequest($authentication, $statusType, $ids)
    {
        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        $idListArr = array();
        if (!empty($ids)) {
            foreach ($ids as $key => $value) {
                array_push($idListArr, new soapval($statusType, false, $value));
            }
        }

        $detailsArr = array(
            $statusType . 'List' => $idListArr
        );

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('statusRequest_Request', $reqParam);
        return $nusoapResult;
    }

    function createBatch($authentication, $externalBatchID)
    {
        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        $detailsArr = array(
            'externalBatchID' => $externalBatchID
        );

        $detailsArr = $this->removeEmptyTag($detailsArr);

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('createBatch_Request', $reqParam);
        return $nusoapResult;
    }

    function addBatchPayRequest($authentication, $batchID, $itemList)
    {

        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $pos = 0;
        $batchItems = array();
        foreach ($itemList as $key => $itemArr) {
            $amountArr = array(
                'amount' => $itemArr['amount'],
                'currency' => $itemArr['currency']
            );

            $shippingAddressArr = array(
                $itemArr['shippingType'] => $itemArr['shippingAddress']
            );

            $billingAddressArr = array(
                $itemArr['billingType'] => $itemArr['billingAddress']
            );

            $amountLimitArr = array(
                'amount' => $itemArr['recurring']['amountLimit']['amount'],
                'currency' => $itemArr['recurring']['amountLimit']['currency']
            );

            $amountLimitArr = $this->removeEmptyTag($amountLimitArr);

            $createRecurringArr = array(
                'description' => $itemArr['recurring']['description'],
                'numberLimit' => $itemArr['recurring']['numberLimit'],
                'amountLimit' => $amountLimitArr,
                'expireDate' => $itemArr['recurring']['expireDate'],
                'revokableByConsumer' => $itemArr['recurring']['revokableByConsumer']
            );

            $createRecurringArr = $this->removeEmptyTag($createRecurringArr);

            $items = $itemArr['itemDetailList'];
            $itemListArr = array();
            if (!empty($items)) {
                $itemListArr = $this->createItemList($items);
            }

            $orderDetailsArr = array(
                'text' => $itemArr['orderDescription'],
                'itemList' => $itemListArr
            );

            $orderDetailsArr = $this->removeEmptyTag($orderDetailsArr);

            $payRequestArr = array(
                'amount' => $amountArr,
                'basketRisk' => $itemArr['basketRisk'],
                'clientRisk' => $itemArr['clientRisk'],
                'authExpiration' => $itemArr['authExpiration'],
                'confirmExpiration' => $itemArr['confirmExpiration'],
                'successExpiration' => $itemArr['successExpiration'],
                'successURL' => $itemArr['successURL'],
                'failureURL' => $itemArr['failureURL'],
                'consumerIPAddress' => $itemArr['consumerIPAddress'],
                'externalID' => $itemArr['externalID'],
                'consumerLanguage' => $itemArr['consumerLanguage'],
                'consumerCountry' => $itemArr['consumerCountry'],
                'orderDetails' => $orderDetailsArr,
                'shipping' => $shippingAddressArr,
                'billing' => $billingAddressArr,
                'createRecurring' => $createRecurringArr
            );

            $payRequestArr = $this->removeEmptyTag($payRequestArr);

            $batchItemDetail = array(new soapval('payRequestDetails', false, $payRequestArr));
            array_push($batchItems, new soapval('batchItemDetails', false, array('externalID' => $itemArr['batchItemExternalID'], 'details' => $batchItemDetail)));
        }

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        $detailsArr = array(
            'batchID' => $batchID,
            'batchItemDetailsList' => $batchItems
        );

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('addBatchItem_Request', $reqParam);
        return $nusoapResult;
    }

    function addBatchRecurring($authentication, $batchID, $itemList)
    {

        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $pos = 0;
        $batchItems = array();
        foreach ($itemList as $key => $itemArr) {
            $amountArr = array(
                'amount' => $itemArr['amount'],
                'currency' => $itemArr['currency']
            );

            $shippingAddressArr = array(
                $itemArr['shippingType'] => $itemArr['shippingAddress']
            );

            $billingAddressArr = array(
                $itemArr['billingType'] => $itemArr['billingAddress']
            );

            $items = $itemArr['itemDetailList'];
            $itemListArr = array();
            if (!empty($items)) {
                $itemListArr = $this->createItemList($items);
            }

            $orderDetailsArr = array(
                'text' => $itemArr['orderDescription'],
                'itemList' => $itemListArr
            );

            $orderDetailsArr = $this->removeEmptyTag($orderDetailsArr);

            $payRequestRecurringArr = array(
                'amount' => $amountArr,
                'recurringPaymentAuthorizationID' => $itemArr['recurringPaymentAuthorizationID'],
                'basketRisk' => $itemArr['basketRisk'],
                'clientRisk' => $itemArr['clientRisk'],
                'externalID' => $itemArr['externalID'],
                'successExpiration' => $itemArr['successExpiration'],
                'orderDetails' => $orderDetailsArr,
                'shipping' => $shippingAddressArr,
                'billing' => $billingAddressArr
            );

            $payRequestRecurringArr = $this->removeEmptyTag($payRequestRecurringArr);

            $batchItemDetail = array(new soapval('payRequestRecurringDetails', false, $payRequestRecurringArr));
            array_push($batchItems, new soapval('batchItemDetails', false, array('externalID' => $itemArr['batchItemExternalID'], 'details' => $batchItemDetail)));
        }

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        $detailsArr = array(
            'batchID' => $batchID,
            'batchItemDetailsList' => $batchItems
        );

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('addBatchItem_Request', $reqParam);
        return $nusoapResult;
    }

    function addBatchCredit($authentication, $batchID, $itemList)
    {

        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $pos = 0;
        $batchItems = array();
        foreach ($itemList as $key => $itemArr) {
            $amountArr = array(
                'amount' => $itemArr['amount'],
                'currency' => $itemArr['currency']
            );

            $items = $itemArr['itemDetailList'];
            $itemListArr = array();
            if (!empty($items)) {
                $itemListArr = $this->createItemList($items);
            }

            $orderDetailsArr = array(
                'text' => $itemArr['orderDescription'],
                'itemList' => $itemListArr
            );

            $orderDetailsArr = $this->removeEmptyTag($orderDetailsArr);

            $requestArr = array(
                'amount' => $amountArr,
                'emailAddress' => $itemArr['emailAddress'],
                'consumerLanguage' => $itemArr['consumerLanguage'],
                'externalID' => $itemArr['externalID'],
                'orderDetails' => $orderDetailsArr
            );

            $creditArr = $this->removeEmptyTag($requestArr);

            $batchItemDetail = array(new soapval('creditRequestDetails', false, $creditArr));
            array_push($batchItems, new soapval('batchItemDetails', false, array('externalID' => $itemArr['batchItemExternalID'], 'details' => $batchItemDetail)));
        }

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        $detailsArr = array(
            'batchID' => $batchID,
            'batchItemDetailsList' => $batchItems
        );

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('addBatchItem_Request', $reqParam);
        return $nusoapResult;
    }

    function addBatchRefund($authentication, $batchID, $itemList)
    {
        $this->client = new nusoap_client($this->piCabFunctionsShop->getSoapEndpoint());
        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $pos = 0;
        $batchItems = array();
        foreach ($itemList as $key => $itemArr) {
            $amountArr = array(
                'amount' => $itemArr['amount'],
                'currency' => $itemArr['currency']
            );

            $items = $itemArr['itemDetailList'];
            $itemListArr = array();
            if (!empty($items)) {
                $itemListArr = $this->createItemList($items);
            }

            $orderDetailsArr = array(
                'text' => $itemArr['orderDescription'],
                'itemList' => $itemListArr
            );

            $orderDetailsArr = $this->removeEmptyTag($orderDetailsArr);

            $requestArr = array(
                'amount' => $amountArr,
                'transactionID' => $itemArr['transactionID'],
                'orderDetails' => $orderDetailsArr,
                'externalID' => $itemArr['externalID']
            );

            $refundArr = $this->removeEmptyTag($requestArr);

            $batchItemDetail = array(new soapval('refundRequestDetails', false, $refundArr));
            array_push($batchItems, new soapval('batchItemDetails', false, array('externalID' => $itemArr['batchItemExternalID'], 'details' => $batchItemDetail)));
        }

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        $detailsArr = array(
            'batchID' => $batchID,
            'batchItemDetailsList' => $batchItems
        );

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('addBatchItem_Request', $reqParam);
        return $nusoapResult;
    }

    function addBatchCancel($authentication, $batchID, $itemList)
    {
        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $pos = 0;
        $batchItems = array();
        foreach ($itemList as $key => $itemArr) {
            $requestArr = array(
                'transactionID' => $itemArr['transactionID']
            );

            $cancelArr = $this->removeEmptyTag($requestArr);

            $batchItemDetail = array(new soapval('cancelRequestDetails', false, $cancelArr));
            array_push($batchItems, new soapval('batchItemDetails', false, array('externalID' => $itemArr['batchItemExternalID'], 'details' => $batchItemDetail)));
        }

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        $detailsArr = array(
            'batchID' => $batchID,
            'batchItemDetailsList' => $batchItems
        );

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('addBatchItem_Request', $reqParam);
        return $nusoapResult;
    }

    function executeBatch($authentication, $batchID)
    {
        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        $detailsArr = array(
            'batchID' => $batchID
        );

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('executeBatch_Request', $reqParam);
        return $nusoapResult;
    }

    function getBatchStatus($authentication, $batchID, $externalBatchID, $batchItemsArr)
    {
        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        if (!empty($batchItemsArr)) {
            // Fill itemListArr
            $batchItemIDListArr = array();
            foreach ($batchItemsArr as $key => $value) {
                array_push($batchItemIDListArr, new soapval('batchItemID', false, $value));
            }
        }

        $detailsArr = array(
            'batchID' => $batchID,
            'externalBatchID' => $externalBatchID,
            'batchItemIDList' => $batchItemIDListArr
        );

        $detailsArr = $this->removeEmptyTag($detailsArr);

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('getBatchStatus_Request', $reqParam);
        return $nusoapResult;
    }

    function cancelBatch($authentication, $batchID)
    {
        $token = $this->generateToken($authentication['projectID'], $authentication['secretKey']);

        $authenticationArr = array(
            'merchantID' => $authentication['merchantID'],
            'projectID' => $authentication['projectID'],
            'token' => $token
        );

        $detailsArr = array(
            'batchID' => $batchID
        );

        $reqParam = array(
            'authentication' => $authenticationArr,
            'details' => $detailsArr
        );

        $nusoapResult = $this->doSoapRequest('cancelBatch_Request', $reqParam);
        return $nusoapResult;
    }

    function logMmsEvent($xml)
    {
        $handle = fopen('mms.log', 'a+');
        fwrite($handle, "------------ new entry, received @ " . date('Y-m-d H:i:s') . " ------------------- \n");
        fwrite($handle, print_r($xml, true) . "\n");
        fclose($handle);
    }

    function sendMail($sendEmailTo, $sendEmailFrom, $text)
    {
        $subject = 'ClickandBuy MMS Event ' . date('Y-m-d H:i:s');
        $body = '
		<html>
			<head>
			  <title>MMS Event</title>
			</head>
			<body>
				------------ new entry, received @ ' . date('Y-m-d H:i:s') . ' -------------------
				<p>' . htmlspecialchars($text, ENT_QUOTES) . '</p>
			</body>
		</html>
	';

        // Set header
        $header = 'MIME-Version: 1.0' . "\r\n";
        $header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $header .= 'To: <' . $sendEmailTo . '>' . "\r\n";
        $header .= 'From: <' . $sendEmailFrom . '>' . "\r\n";

        // Send mail
        mail($sendEmailTo, $subject, $body, $header);
    }

    function payEventList($payEventList)
    {
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

            /*
             * Write the data into the database!
             */
        }
    }

    function refundEventList($refundEventList)
    {
        if (!is_array($refundEventList[0])) {
            $refundEventList = array($refundEventList);
        }

        foreach ($refundEventList as $key => $value) {
            $merchantID = $value['merchantID'];
            $projectID = $value['projectID'];
            $eventID = $value['eventID'];
            $creationDateTime = $value['creationDateTime'];
            $refundTransactionID = $value['refundTransactionID'];
            $associatedTransactionID = $value['associatedTransactionID'];
            $crn = $value['crn'];
            $transactionAmount = $value['transactionAmount']['amount'];
            $transactionCurrency = $value['transactionAmount']['currency'];
            $merchantAmount = $value['merchantAmount']['amount'];
            $merchantCurrency = $value['merchantAmount']['currency'];
            $oldState = $value['oldState'];
            $newState = $value['newState'];

            /*
             * Write the data into the database!
             */
        }
    }

    function creditEventList($creditEventList)
    {
        if (!is_array($creditEventList[0])) {
            $creditEventList = array($creditEventList);
        }

        foreach ($creditEventList as $key => $value) {
            $merchantID = $value['merchantID'];
            $projectID = $value['projectID'];
            $eventID = $value['eventID'];
            $creationDateTime = $value['creationDateTime'];
            $transactionID = $value['transactionID'];
            $email = $value['email'];
            $crn = $value['crn'];
            $transactionAmount = $value['transactionAmount']['amount'];
            $transactionCurrency = $value['transactionAmount']['currency'];
            $merchantAmount = $value['merchantAmount']['amount'];
            $merchantCurrency = $value['merchantAmount']['currency'];
            $oldState = $value['oldState'];
            $newState = $value['newState'];

            /*
             * Write the data into the database!
             */
        }
    }

    function recurringPaymentAuthorizationEventList($recurringEventList)
    {
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

            /*
             * Write the data into the database!
             */
        }
    }

    function batchEventList($batchEventList)
    {
        if (!is_array($batchEventList[0])) {
            $batchEventList = array($batchEventList);
        }

        foreach ($batchEventList as $key => $value) {
            $merchantID = $value['merchantID'];
            $projectID = $value['projectID'];
            $eventID = $value['eventID'];
            $creationDateTime = $value['creationDateTime'];
            $batchID = $value['batchID'];
            $externalBatchID = $value['externalBatchID'];
            $itemCount = $value['itemCount'];
            $newState = $value['newState'];

            if (!is_array($value['batchItemlist']['batchItem'][0])) {
                $value['batchItemlist']['batchItem'] = array($value['batchItemlist']['batchItem']);
            }

            foreach ($value['batchItemlist']['batchItem'] as $itemKey => $itemValue) {
                $batchItemID = $itemValue['batchItemID'];
                $externalID = $itemValue['externalID'];
                $batchItemStatus = $itemValue['batchItemStatus'];
                $resultTransactionID = $itemValue['resultTransactionID'];
                $errorCode = $itemValue['errorDetails']['code'];
                $errorDetailCode = $itemValue['errorDetails']['detailCode'];
                $errorDescription = $itemValue['errorDetails']['description'];
                $errorRetry = $itemValue['errorDetails']['retry'];

                /*
                 * Write the data into the database!
                 */
            }
        }
    }

    function checkSignature($xml, $sharedKey)
    {
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

        ($signature == $hash) ? $result = true : $result = false;
        return $result;
    }

    /**
     * Build a request from the createMerchantRegistration.php form
     * send it to clickandbuy and receive the response
     *
     * @parm array $reqArray
     * @return object $nusoapResult
     */

    function createMerchantRegistration($requestArray)
    {
        $this->client = new nusoap_client($this->piCabFunctionsShop->getMerchantRegistrationEndpoint());
        $nusoapResult = $this->doSoapRequest('createMerchantRegistration_Request', $requestArray);
        return $nusoapResult;
    }

    /**
     * Collect all needed data from the merchant registration call
     * and create a simple json string
     *
     * @param array $nusoapResult
     * @return string $json
     */

    function getMerchantRegistrationResponseData($nusoapResult)
    {
        $responseData = array();
        if ($nusoapResult['success'] == 1) {
            $responseData['success'] = true;
            $responseData['merchantID'] = $nusoapResult['values']['registrationInfo']['merchantID'];
            $responseData['registrationStatus'] = $nusoapResult['values']['registrationInfo']['registrationStatus'];
            $responseData['registrationSharedSecret'] = $nusoapResult['values']['registrationInfo']['registrationSharedSecret'];
            $responseData['registrationURL'] = $nusoapResult['values']['registrationInfo']['registrationURL'];

            $responseData['settlementAccountID'] = $nusoapResult['values']['integrationInfoList']['integrationInfo']['settlementInfo']['settlementAccountID'];
            $responseData['settlementAccountCurrency'] = $nusoapResult['values']['integrationInfoList']['integrationInfo']['settlementInfo']['settlementAccountCurrency'];
            $responseData['settlementAccountName'] = $nusoapResult['values']['integrationInfoList']['integrationInfo']['settlementInfo']['settlementAccountName'];

            $responseData['invoicingCycle'] = $nusoapResult['values']['integrationInfoList']['integrationInfo']['feeCardInfo']['invoicingCycle'];
            $responseData['settlementDelay'] = $nusoapResult['values']['integrationInfoList']['integrationInfo']['feeCardInfo']['settlementDelay'];
            $responseData['amount'] = $nusoapResult['values']['integrationInfoList']['integrationInfo']['feeCardInfo']['averageTicketSize']['amount'];
            $responseData['currency'] = $nusoapResult['values']['integrationInfoList']['integrationInfo']['feeCardInfo']['averageTicketSize']['currency'];

            $responseData['projectID'] = $nusoapResult['values']['integrationInfoList']['integrationInfo']['projectInfo']['projectID'];
            $responseData['projectName'] = $nusoapResult['values']['integrationInfoList']['integrationInfo']['projectInfo']['projectName'];
            $responseData['projectSharedSecret'] = $nusoapResult['values']['integrationInfoList']['integrationInfo']['projectInfo']['projectSharedSecret'];
            $responseData['mmsURL'] = $nusoapResult['values']['integrationInfoList']['integrationInfo']['projectInfo']['mmsURL'];
            $responseData['mmsSharedSecret'] = $nusoapResult['values']['integrationInfoList']['integrationInfo']['projectInfo']['mmsSharedSecret'];

            $this->saveTokenData($responseData['merchantID'], $responseData['projectSharedSecret'], $responseData['mmsSharedSecret'], $responseData['registrationSharedSecret'], $responseData['projectID']);
        } else {
            $responseData['success'] = false;
            $responseData['requestTrackingID'] = $nusoapResult['values']['detail']['errorDetails']['requestTrackingID'];
            $responseData['code'] = $nusoapResult['values']['detail']['errorDetails']['code'];
            $responseData['detailCode'] = $nusoapResult['values']['detail']['errorDetails']['detailCode'];
            $responseData['description'] = $nusoapResult['values']['detail']['errorDetails']['description'];
            $responseData['retry'] = $nusoapResult['values']['detail']['errorDetails']['retry'];
        }
        return $responseData;
    }

    /**
     * Build a request from the getMerchantRegistrationStatus.php form
     * send it to clickandbuy and receive the response
     *
     * @param $reqArray
     * @return object $nusoapResult
     */

    function getMerchantResponse($reqArray, $callName)
    {
        $this->client = new nusoap_client('https://api.clickandbuy.com/webservices/soap/pay_1_1_0');
        $nusoapResult = $this->doSoapRequest($callName, $reqArray);

        return $nusoapResult;
    }

    /**
     * Generates a merchant registration token
     *
     * @param string $businessOriginID
     * @param string $merchantID
     * @param string $sharedSecret
     * @return string $token
     */

    function generateMerchantRegistrationToken($businessOriginID, $merchantID, $sharedSecret)
    {
        $timestamp = gmdate("YmdHis");
        $token = $timestamp . '::' . sha1($businessOriginID . '::' . $merchantID . '::' . $sharedSecret . '::' . $timestamp);

        return $token;
    }

    /**
     * Save the date which needed for token generating in the session
     *
     * @param string $merchantID
     * @param string $sharedSecret
     */

    function saveTokenData($merchantId, $sharedSecretProject, $sharedSecretMms, $sharedSecretRegistration, $projectId)
    {
        $this->piCabFunctionsShop->saveTokenData($merchantId, $sharedSecretProject, $sharedSecretMms, $sharedSecretRegistration, $projectId);

    }

    /**
     * Set the languange
     *
     * @param string $langCode
     */

    function setLanguangeSession($langCode)
    {
        $this->piCabFunctionsShop->setLanguangeSession($langCode);
    }

    function getBuisinessOriginId()
    {
        return $this->piCabFunctionsShop->getBuisinessOriginId();
    }

}

?>