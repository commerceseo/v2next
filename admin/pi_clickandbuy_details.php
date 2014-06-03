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

!empty($_GET['oID']) ? $shopOrderID = $_GET['oID'] : $shopOrderID = $_POST['oID'];

$value_query = xtc_db_query("SELECT * FROM picab_orders WHERE shopOrderID ='" . $shopOrderID . "'");
$cabOrderDetails = xtc_db_fetch_array($value_query);

$authentication = $cabXtcApi->getCabSettings($cabOrderDetails['paymentType']);

$statusType = 'transactionID';
$idList['transactionID1'] = $cabOrderDetails['transactionID'];

$requestResult = $cabApi->statusRequest($authentication, $statusType, $idList);
$requestValues = $requestResult['values'];

if ($requestResult['success'] != 1) {
    $statusRequestResult['result'] = 'ERROR';
    $errorDescription = $requestResult['values']['detail']['errorDetails']['description'];
} else {
    $statusRequestResult['result'] = 'SUCCESS';
    $statusRequestResult['transactionID'] = $requestValues['transactionList']['transaction']['transactionID'];
    $statusRequestResult['externalID'] = $externalID;
    $statusRequestResult['transactionStatus'] = $requestValues['transactionList']['transaction']['transactionStatus'];
    $statusRequestResult['transactionType'] = $requestValues['transactionList']['transaction']['transactionType'];
    $statusRequestResult['recurringPaymentAuthorizationID'] = $requestValues['transactionList']['transaction']['createdRecurringPaymentAuthorization']['recurringPaymentAuthorizationID'];
    $statusRequestResult['recurringPaymentAuthorizationStatus'] = $requestValues['transactionList']['transaction']['createdRecurringPaymentAuthorization']['recurringPaymentAuthorizationStatus'];
    $statusRequestResult['errorDetailCode'] = $requestValues['transactionList']['transaction']['errorDetails']['detailCode'];
    $statusRequestResult['errorDescription'] = $requestValues['transactionList']['transaction']['errorDetails']['description'];
}

$transactionList = getTransactionList();
$latestRecurringList = getLatestRecurringList();
$recurringTotalAmount = getrecurringTotalAmount();
if (empty($recurringTotalAmount))
    $recurringTotalAmount = '0.00';

function getTransactionList()
{
    global $shopOrderID;
    $transactionList = array();

    $value_query = xtc_db_query("SELECT * FROM picab_transactions WHERE shopOrderID = '" . $shopOrderID . "' ORDER BY created ASC ");

    while ($row = xtc_db_fetch_array($value_query)) {
        $transactionList[] = $row;
    }
    return $transactionList;
}

function getLatestRecurringList()
{
    global $shopOrderID;
    $latestRecurringList = array();

    $value_query = xtc_db_query("SELECT * FROM picab_transactions WHERE shopOrderID = '" . $shopOrderID . "' AND transactionType = 'recurring' ORDER BY created ASC LIMIT 0,5");

    while ($row = xtc_db_fetch_array($value_query)) {
        $latestRecurringList[] = $row;
    }
    return $latestRecurringList;
}

function getrecurringTotalAmount()
{
    global $shopOrderID;
    $value_query = xtc_db_query("SELECT SUM(amount) as recurringTotalAmount FROM picab_transactions WHERE shopOrderID = '" . $shopOrderID . "' AND  transactionType = 'recurring'");
    $recurringTransactions = xtc_db_fetch_array($value_query);
    $recurringTotalAmount = $recurringTransactions['recurringTotalAmount'];
    if (empty($recurringTotalAmount))
        $recurringTotalAmount = '0.00';

    return $recurringTotalAmount;
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
        <meta name="robots" content="noindex,nofollow">
        <title><?php echo TITLE; ?></title>
        <link rel="stylesheet" type="text/css" href="../ext/modules/payment/pi_clickandbuy/style.css">
        <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
        <script language="javascript" src="includes/general.js"></script>

    </head>
    <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
        <!-- header //-->
        <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
        <!-- header_eof //-->

        <!-- body //-->
        <table border="0" width="100%" cellspacing="2" cellpadding="2">
            <tr>
                <!-- body_text //-->
                <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                            <td>
                                <table border="0" width="100%" cellspacing="0" cellpadding="2" height="40">
                                    <tr>
                                        <td class="pageHeading"><?php echo CLICKANDBUY_ORDER_CLICKANDBUY_DETAILS; ?></td>
                                    </tr>
                                    <tr>
                                        <td><img width="100%" height="1" border="0" alt="" src="images/pixel_black.gif"/></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <?php
                                if ($_GET['picabstatus'] == 'SUCCESS') {
                                    $showMessage = $cabXtcApi->showMessageSuccess(CLICKANDBUY_ORDER_CANCEL_AUTHORIZE_SUCCESSFUL);
                                    echo $showMessage;
                                } elseif ($_GET['picabstatus'] == 'ERROR') {
                                    $showMessage = $cabXtcApi->showMessageError($_GET['picabmessage']);
                                    echo $showMessage;
                                }
                                ?>
                                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                    <tr>
                                        <td valign="top">
                                            <!-- ClickandBuy Details START -->
                                            <table cellspacing="0" cellpadding="0" border="0" width="98%">
                                                <tr>
                                                    <td width="50%" valign="top">
                                                        <table cellspacing="0" cellpadding="5" border="0" width="400">
                                                            <tbody>
                                                                <tr class="infoBoxHeading">
                                                                    <td valign="top" class="infoBoxHeading" colspan="2">
                                                                        <b><?php echo CLICKANDBUY_ORDER_DETAILS_OVERVIEW; ?></b>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="infoBoxContent" height="20" width="100"><?php echo CLICKANDBUY_ORDER_DETAILS_TOTAL_AMOUNT; ?>:</td>
                                                                    <td class="infoBoxContent"><?php echo $cabOrderDetails['amount'] . ' ' . $cabOrderDetails['currency']; ?>  </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="infoBoxContent" height="20" width="100"><?php echo CLICKANDBUY_ORDER_DETAILS_DEBITED; ?>:</td>
                                                                    <td class="infoBoxContent"><?php echo $cabOrderDetails['debited'] . ' ' . $cabOrderDetails['currency']; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="infoBoxContent" height="20"><?php echo CLICKANDBUY_ORDER_DETAILS_REFUNDED; ?>:</td>
                                                                    <td class="infoBoxContent"><?php echo $cabOrderDetails['refunded'] . ' ' . $cabOrderDetails['currency']; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="infoBoxContent" height="20"><?php echo CLICKANDBUY_ORDER_DETAILS_CANCELLED; ?>:</td>
                                                                    <td class="infoBoxContent"><?php echo $cabOrderDetails['cancelled'] . ' ' . $cabOrderDetails['currency']; ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <br />
                                                        <table cellspacing="0" cellpadding="5" border="0" width="400">
                                                            <tbody>
                                                                <tr class="infoBoxHeading">
                                                                    <td valign="top" class="infoBoxHeading"><b><?php echo CLICKANDBUY_ORDER_DETAILS_REFUND; ?></b></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <form name="refund" action="pi_clickandbuy_refund.php" method="POST">
                                                            <input type="hidden" name="oID" value="<?php echo $shopOrderID; ?>">
                                                            <table width="400" border="0" cellspacing="0" cellpadding="5" border="0">
                                                                <tbody>
                                                                    <?php
                                                                    foreach ($transactionList as $item => $value) {
                                                                        if ($value['transactionType'] == 'refund') {
                                                                            $transactionTypeRefund = true;

                                                                            echo '
                                                                                <tr>
                                                                                    <td class="infoBoxContent" width="140">' . $value['created'] . '</td>
                                                                                    <td class="infoBoxContent" width="100">' . $value['amount'] . ' ' . $value['currency'] . '</td>
                                                                                    <td class="infoBoxContent" width="145">' . $value['status'] . '</td>
                                                                                    <td class="infoBoxContent" width="15">
                                                                                        <div id="infoBoxRight">
                                                                                            <a href="#">
                                                                                                <img src="images/icon_info.gif" border="0" alt="" title=""/>
                                                                                                <span>
                                                                                                    <b>' . CLICKANDBUY_ORDER_DETAILS_REFUND . ' ' . CLICKANDBUY_ORDER_DETAILS . '</b><br />
                                                                                                       ' . CLICKANDBUY_ORDER_DETAILS_DATE_TIME . ': ' . $value['created'] . '<br />
                                                                                                       ' . CLICKANDBUY_ORDER_DETAILS_EXTERNALID . ': ' . $value['externalID'] . '<br />
                                                                                                       ' . CLICKANDBUY_ORDER_DETAILS_STATUS . ': ' . $value['status'] . '<br />
                                                                                                       ' . CLICKANDBUY_ORDER_DETAILS_AMOUNT . ': ' . $value['amount'] . ' ' . $value['currency'] . '<br />
                                                                                                       ' . CLICKANDBUY_ORDER_DETAILS_DESCRIPTION . ': ' . $value['description'] . '<br />
                                                                                                </span>
                                                                                            </a>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>';
                                                                        }
                                                                    }
                                                                    if ($transactionTypeRefund) {
                                                                        echo '<tr>
                                                                                <td class="infoBoxContent" colspan="4"><hr /></td>
                                                                              </tr>';
                                                                    }
                                                                    ?>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" colspan="4"><?php echo CLICKANDBUY_ORDER_DETAILS_REFUND_DESC; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" colspan="4"><input class="submit" type="submit" value="<?php echo CLICKANDBUY_ORDER_DETAILS_REFUND; ?>" name="refund"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </form>
                                                        <br />
                                                        <form name="cancel" action="pi_clickandbuy_cancel.php" method="POST">
                                                            <input type="hidden" name="oID" value="<?php echo $shopOrderID; ?>">
                                                            <table cellspacing="0" cellpadding="5" border="0" width="400">
                                                                <tbody>
                                                                    <tr class="infoBoxHeading">
                                                                        <td valign="top" class="infoBoxHeading"><b><?php echo CLICKANDBUY_ORDER_DETAILS_CANCELLATION; ?></b></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table width="400" border="0" cellspacing="0" cellpadding="5" border="0">
                                                                <tbody>
                                                                    <?php
                                                                    foreach ($transactionList as $item => $value) {
                                                                        if ($value['transactionType'] == 'cancel') {
                                                                            $transactionTypeCancel = true;

                                                                            echo '
                                                                                <tr>
                                                                                    <td class="infoBoxContent" width="140">' . $value['created'] . '</td>
                                                                                    <td class="infoBoxContent" width="100">' . $value['amount'] . ' ' . $value['currency'] . '</td>
                                                                                    <td class="infoBoxContent" width="145">' . $value['status'] . '</td>
                                                                                    <td class="infoBoxContent" width="15">
                                                                                        <div id="infoBoxRight">
                                                                                            <a href="#"><img src="images/icon_info.gif" border="0" alt="" title="">
                                                                                                <span>
                                                                                                    <b>' . CLICKANDBUY_ORDER_DETAILS_CANCELLATION . ' ' . CLICKANDBUY_ORDER_DETAILS . '</b><br />
                                                                                                       ' . CLICKANDBUY_ORDER_DETAILS_DATE_TIME . ': ' . $value['created'] . '<br />
                                                                                                       ' . CLICKANDBUY_ORDER_DETAILS_STATUS . ': ' . $value['status'] . '<br />
                                                                                                       ' . CLICKANDBUY_ORDER_DETAILS_AMOUNT . ': ' . $value['amount'] . ' ' . $value['currency'] . '<br />
                                                                                                </span>
                                                                                            </a>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>';
                                                                        }
                                                                    }
                                                                    if ($transactionTypeCancel) {
                                                                        echo '<tr>
                                                                                <td class="infoBoxContent" colspan="4"><hr /></td>
                                                                             </tr>';
                                                                    }
                                                                    ?>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" colspan="4">&nbsp;<?php echo CLICKANDBUY_ORDER_DETAILS_CANCELLATION_DESC; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" colspan="4"><input class="submit" type="submit" value="&nbsp;&nbsp;<?php echo CLICKANDBUY_ORDER_DETAILS_CANCEL; ?>&nbsp;&nbsp;" name="cancel"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </form>
                                                        <br />
                                                        <table cellspacing="0" cellpadding="5" border="0" width="400">
                                                            <tbody>
                                                                <tr class="infoBoxHeading">
                                                                    <td valign="top" class="infoBoxHeading"><b><?php echo CLICKANDBUY_ORDER_DETAILS_CREDIT; ?></b></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <form name="credit" action="pi_clickandbuy_credit.php" method="POST">
                                                            <input type="hidden" name="oID" value="<?php echo $shopOrderID; ?>">
                                                            <table width="400" border="0" cellspacing="0" cellpadding="5" border="0">
                                                                <tbody>
                                                                    <?php
                                                                    foreach ($transactionList as $item => $value) {
                                                                        if ($value['transactionType'] == 'credit') {
                                                                            $transactionTypeCredit = true;

                                                                            echo '
                                                                                <tr>
                                                                                    <td class="infoBoxContent" width="140">' . $value['created'] . '</td>
                                                                                    <td class="infoBoxContent" width="100">' . $value['amount'] . ' ' . $value['currency'] . '</td>
                                                                                    <td class="infoBoxContent" width="145">' . $value['status'] . '</td>
                                                                                    <td class="infoBoxContent" width="15">
                                                                                        <div id="infoBoxRight">
                                                                                            <a href="#">
                                                                                                <img src="images/icon_info.gif" border="0" alt="" title=""/>
                                                                                                <span>
                                                                                                    <b>' . CLICKANDBUY_ORDER_DETAILS_CREDIT . ' ' . CLICKANDBUY_ORDER_DETAILS . '</b><br />
                                                                                                       ' . CLICKANDBUY_ORDER_DETAILS_DATE_TIME . ': ' . $value['created'] . '<br />
                                                                                                       ' . CLICKANDBUY_ORDER_DETAILS_EXTERNALID . ': ' . $value['externalID'] . '<br />
                                                                                                       ' . CLICKANDBUY_ORDER_DETAILS_STATUS . ': ' . $value['status'] . '<br />
                                                                                                       ' . CLICKANDBUY_ORDER_DETAILS_AMOUNT . ': ' . $value['amount'] . ' ' . $value['currency'] . '<br />
                                                                                                       ' . CLICKANDBUY_ORDER_DETAILS_DESCRIPTION . ': ' . $value['description'] . '<br />
                                                                                                </span>
                                                                                            </a>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>';
                                                                        }
                                                                    }
                                                                    if ($transactionTypeCancel) {
                                                                        echo '<tr>
                                                                                    <td class="infoBoxContent" colspan="4"><hr /></td>
                                                                              </tr>';
                                                                    }
                                                                    ?>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" colspan="4"><?php echo CLICKANDBUY_ORDER_DETAILS_CREDIT_DESC; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" colspan="4"><input class="submit" type="submit" value="&nbsp;&nbsp;<?php echo CLICKANDBUY_ORDER_DETAILS_CREDIT; ?>&nbsp;&nbsp;" name="credit"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </form>
                                                    </td>
                                                    <td width="50%" valign="top">
                                                        <table cellspacing="0" cellpadding="5" border="0" width="400">
                                                            <tbody>
                                                                <tr class="infoBoxHeading">
                                                                    <td valign="top" class="infoBoxHeading"><b><?php echo CLICKANDBUY_ORDER_DETAILS_STATUS; ?>: </b></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <table width="400" cellspacing="0" cellpadding="5" border="0">
                                                            <tbody>
                                                                <?php
                                                                if ($statusRequestResult['result'] == 'SUCCESS') {
                                                                    if (!empty($statusRequestResult['transactionStatus'])) {
                                                                        echo '
                                                                            <tr>
                                                                                <td class="infoBoxContent" height="20" width="140">' . CLICKANDBUY_ORDER_DETAILS_TRANSACTIONID . ':</td>
                                                                                <td class="infoBoxContent">' . $statusRequestResult['transactionID'] . '</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="infoBoxContent">' . CLICKANDBUY_ORDER_DETAILS_TRANSACTION_STATUS . ':</td>
                                                                                <td class="infoBoxContent">' . $statusRequestResult['transactionStatus'] . '</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="infoBoxContent">' . CLICKANDBUY_ORDER_DETAILS_TRANSACTION_TYPE . ':</td>
                                                                                <td class="infoBoxContent">' . $statusRequestResult['transactionType'] . '</td>
                                                                            </tr>';
                                                                        if (!empty($statusRequestResult['recurringPaymentAuthorizationID'])) {
                                                                            echo '
                                                                                <tr>
                                                                                    <td class="infoBoxContent">' . CLICKANDBUY_ORDER_DETAILS_AUTHORIZATIONID . ':</td>
                                                                                    <td class="infoBoxContent">' . $statusRequestResult['recurringPaymentAuthorizationID'] . '</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="infoBoxContent">' . CLICKANDBUY_ORDER_DETAILS_AUTHORIZATION_STATUS . ':</td>
                                                                                    <td class="infoBoxContent">' . $statusRequestResult['recurringPaymentAuthorizationStatus'] . '</td>
                                                                                </tr>';
                                                                        }
                                                                    } else {
                                                                        echo '
                                                                            <tr>
                                                                                <td class="infoBoxContent" height="20" width="140">' . CLICKANDBUY_ORDER_DETAILS_TRANSACTIONID . ':</td>
                                                                                <td class="infoBoxContent">' . $statusRequestResult['transactionID'] . '</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="infoBoxContent">' . CLICKANDBUY_ORDER_DETAILS_ERROR_CODE . ':</td>
                                                                                <td class="infoBoxContent">' . $statusRequestResult['errorDetailCode'] . '</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="infoBoxContent">' . CLICKANDBUY_ORDER_DETAILS_ERROR_DESC . ':</td>
                                                                                <td class="infoBoxContent">' . $statusRequestResult['errorDescription'] . '</td>
                                                                            </tr>';
                                                                    }
                                                                } else {
                                                                    echo '<tr>
                                                                            <td class="infoBoxContent" height="20" colspan="2">' . CLICKANDBUY_ORDER_DETAILS_ERROR_REASON . '<br />' . $statusRequestResult['errorDescription'] . '</td>
                                                                          </tr>'
                                                                    ;
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                        <br/>
                                                        <form name="mms" action="pi_clickandbuy_mms.php" method="POST">
                                                            <input type="hidden" name="oID" value="<?php echo $shopOrderID; ?>">
                                                            <table cellspacing="0" cellpadding="5" border="0" width="400" border="0">
                                                                <tbody>
                                                                    <tr class="infoBoxHeading">
                                                                        <td valign="top" class="infoBoxHeading"><b><?php echo CLICKANDBUY_ORDER_DETAILS_MMS; ?>: </b></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table width="400" cellspacing="0" cellpadding="5" border="0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="infoBoxContent"><input class="submit" type="submit" value="&nbsp;&nbsp;<?php echo CLICKANDBUY_ORDER_DETAILS_MMS_SHOW; ?>&nbsp;&nbsp;" name="mms_show"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </form>
                                                        <br/>
                                                        <?php
                                                        if (($cabOrderDetails['paymentType'] == 'clickandbuyrecurring') || ($cabOrderDetails['paymentType'] == 'clickandbuypartialdelivery') || ($cabOrderDetails['paymentType'] == 'clickandbuyfastcheckout')) {
                                                            ?>
                                                            <form name="cancel_authorize" action="pi_clickandbuy_cancel_authorize.php" method="POST">
                                                                <input type="hidden" name="oID" value="<?php echo $shopOrderID; ?>">
                                                                <input type="hidden" name="actionSave" value="true">
                                                                <table cellspacing="0" cellpadding="5" border="0" width="400" border="0">
                                                                    <tbody>
                                                                        <tr class="infoBoxHeading">
                                                                            <td valign="top" class="infoBoxHeading" colspan="3"><b><?php echo CLICKANDBUY_ORDER_DETAILS_CANCELLATION_AUTHORIZATION; ?>: </b></td>
                                                                        </tr>
                                                                        <?php
                                                                        foreach ($transactionList as $item => $value) {
                                                                            if ($value['transactionType'] == 'cancel_authorize') {
                                                                                $transactionTypeCancel = true;

                                                                                echo '
                                                                                    <tr>
                                                                                        <td class="infoBoxContent" width="140">' . $value['created'] . '</td>
                                                                                        <td class="infoBoxContent" width="145">' . $value['status'] . '</td>
                                                                                        <td class="infoBoxContent" width="15">
                                                                                            <div id="infoBoxRight">
                                                                                                <a href="#"><img src="images/icon_info.gif" border="0" alt="" title="">
                                                                                                    <span>
                                                                                                        <b>' . CLICKANDBUY_ORDER_DETAILS_CANCELLATION . ' ' . CLICKANDBUY_ORDER_DETAILS . '</b><br />
                                                                                                           ' . CLICKANDBUY_ORDER_DETAILS_DATE_TIME . ': ' . $value['created'] . '<br />
                                                                                                           ' . CLICKANDBUY_ORDER_DETAILS_STATUS . ': ' . $value['status'] . '<br />
                                                                                                    </span>
                                                                                                </a>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>';
                                                                            }
                                                                        }
                                                                        if ($transactionTypeCancel) {
                                                                            echo '<tr>
                                                                                    <td class="infoBoxContent" colspan="4"><hr /></td>
                                                                                 </tr>';
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                                <table width="400" cellspacing="0" cellpadding="5" border="0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="infoBoxContent"><input class="submit" type="submit" value="&nbsp;&nbsp;<?php echo CLICKANDBUY_ORDER_DETAILS_CANCEL; ?>&nbsp;&nbsp;" name="cancel_authorization"></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </form>
                                                            <br/>

                                                            <table cellspacing="0" cellpadding="5" border="0" width="400">
                                                                <tbody>
                                                                    <tr class="infoBoxHeading">
                                                                        <td valign="top" class="infoBoxHeading"><b><?php echo CLICKANDBUY_ORDER_DETAILS_RECURRING; ?>: </b></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <table width="400" border="0" cellspacing="0" cellpadding="5">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20"><?php echo CLICKANDBUY_ORDER_DETAILS_AUTHORIZATION_AMOUNT; ?>:</td>
                                                                        <td class="infoBoxContent"  colspan="3"><?php echo $cabOrderDetails['recAmount'] . ' ' . $cabOrderDetails['currency']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20"><?php echo CLICKANDBUY_ORDER_DETAILS_TOTAL_AMOUNT; ?>:</td>
                                                                        <td class="infoBoxContent" colspan="3"><?php echo $recurringTotalAmount . ' ' . $cabOrderDetails['currency']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" colspan="4"><hr /></td>
                                                                    </tr>
                                                                    <?php
                                                                    if (!empty($latestRecurringList)) {
                                                                        foreach ($latestRecurringList as $item => $value) {
                                                                            if ($value['transactionType'] == 'recurring') {
                                                                                $transactionTypeRecurring = true;
                                                                                echo '
                                                                                    <tr>
                                                                                        <td class="infoBoxContent" width="140">' . $value['created'] . '</td>
                                                                                        <td class="infoBoxContent" width="100">' . $value['amount'] . ' ' . $value['currency'] . '</td>
                                                                                        <td class="infoBoxContent" width="145">' . $value['status'] . '</td>
                                                                                        <td class="infoBoxContent" width="15">
                                                                                            <div id="infoBoxLeft">
                                                                                                <a href="#">
                                                                                                    <img src="images/icon_info.gif" border="0" alt="" title=""/>
                                                                                                    <span>
                                                                                                        <b>' . CLICKANDBUY_ORDER_DETAILS_RECURRING . ' ' . CLICKANDBUY_ORDER_DETAILS . '</b><br />
                                                                                                           ' . CLICKANDBUY_ORDER_DETAILS_DATE_TIME . ': ' . $value['created'] . '<br />
                                                                                                           ' . CLICKANDBUY_ORDER_DETAILS_EXTERNALID . ': ' . $value['externalID'] . '<br />
                                                                                                           ' . CLICKANDBUY_ORDER_DETAILS_STATUS . ': ' . $value['status'] . '<br />
                                                                                                           ' . CLICKANDBUY_ORDER_DETAILS_AMOUNT . ': ' . $value['amount'] . ' ' . $value['currency'] . '<br />
                                                                                                           ' . CLICKANDBUY_ORDER_DETAILS_DESCRIPTION . ': ' . $value['description'] . '<br />
                                                                                                    </span>
                                                                                                </a>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>';
                                                                            }
                                                                        }
                                                                    }

                                                                    if ($transactionTypeRecurring) {
                                                                        echo '<tr>
                                                                                <td class="infoBoxContent" colspan="4"><hr /></td>
                                                                              </tr>';
                                                                    }
                                                                    ?>
                                                                    <tr>
                                                                        <td class="infoBoxContent" colspan="4"><?php echo CLICKANDBUY_ORDER_DETAILS_RECURRING_DESC; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <form name="recurring_debit" action="pi_clickandbuy_recurring_debit.php" method="POST">
                                                                            <input type="hidden" name="oID" value="<?php echo $shopOrderID; ?>">
                                                                            <td class="infoBoxContent"><input class="submit" type="submit" value="<?php echo CLICKANDBUY_ORDER_DETAILS_DEBIT_TRANSACTION; ?>" name="recDebit"></td>
                                                                        </form>
                                                                        <form name="recurring" action="pi_clickandbuy_recurring.php" method="POST">
                                                                            <input type="hidden" name="oID" value="<?php echo $shopOrderID; ?>">
                                                                            <td class="infoBoxContent" colspan="3"><input class="submit" type="submit" value="<?php echo CLICKANDBUY_ORDER_DETAILS_SHOW_TRANSACTIONS; ?>" name="recShowList"></td>
                                                                        </form>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <br/>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </table>
                                <!-- ClickandBuy Details END -->
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>