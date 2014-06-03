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

$amount = (float) $cabOrderDetails['amount'];
$refunded = (float) $cabOrderDetails['refunded'];
$cancelled = (float) $cabOrderDetails['cancelled'];
$openAmount = ($amount - $refunded - $cancelled);
$externalID = 'NULL';

if ($actionSave && $cancelled == 0.00) {
    $transactionID = $cabOrderDetails['transactionID'];

    $requestResult = $cabApi->cancelRequest($authentication, $transactionID);
    $requestValues = $requestResult['values'];

    if ($requestValues['transaction']['transactionStatus'] == 'CANCELLED') {
        $messageBox = 'SUCCESS';
        $cancelled = $openAmount + $cancelled;

        xtc_db_query("UPDATE picab_orders SET cancelled =  '" . $cancelled . "' WHERE shopOrderID ='" . $shopOrderID . "'");

        $transactionID = $requestValues['transaction']['transactionID'];
        $transactionStatus = $requestValues['transaction']['transactionStatus'];
        xtc_db_query("INSERT INTO picab_transactions
                             (id,shopOrderID,transactionID,externalID,transactionType,description,amount,currency,paid,status,created,modified)
                      VALUES (NULL,'" . $shopOrderID . "','" . $transactionID . "','" . $externalID . "','cancel','" . $itemDesc1 . "'," . $cancelled . ",'" . $cabOrderDetails['currency'] . "',1,'" . $transactionStatus . "',NOW(),NOW())");
    } else {
        $messageBox = 'ERROR';
        $errorDescription = $requestResult['values']['detail']['errorDetails']['description'];
    }
} elseif ($actionSave && $cancelled > 0.00) {
    $messageBox = 'ERROR';
    $errorDescription = CLICKANDBUY_ORDER_CANCEL_IS_CANCELLED;
}

$cancelled = number_format($cancelled, 2, '.', '');
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
        <meta name="robots" content="noindex,nofollow">
        <title><?php echo TITLE; ?></title>
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
                <td width="100%" valign="top">
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                            <td>
                                <form name="refund" action="pi_clickandbuy_details.php" method="GET">
                                    <input type="hidden" value="<?php echo $shopOrderID; ?>" name="oID">
                                    <table border="0" width="100%" cellspacing="0" cellpadding="2" height="40">
                                        <tr>
                                            <td class="pageHeading"><?php echo CLICKANDBUY_ORDER_CLICKANDBUY . ' ' . CLICKANDBUY_ORDER_DETAILS_CANCELLATION; ?></td>
                                            <td class="pageHeading" align="right"><input class="submit" type="submit" value="<?php echo CLICKANDBUY_ORDER_DETAILS_BACK; ?>" name="back"></td>
                                        </tr>
                                    </table>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                if ($messageBox == 'SUCCESS') {
                                    $showMessage = $cabXtcApi->showMessageSuccess(CLICKANDBUY_ORDER_CANCEL_SUCCESSFUL);
                                    echo $showMessage;
                                } elseif ($messageBox == 'ERROR') {
                                    $showMessage = $cabXtcApi->showMessageError(CLICKANDBUY_ORDER_CANCEL_ERROR . ' ' . $errorDescription);
                                    echo $showMessage;
                                }
                                ?>
                                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                    <tr>
                                        <td>
                                            <!-- ClickandBuy Details START -->
                                            <form name="cancel" action="pi_clickandbuy_cancel.php" method="POST">
                                                <input type="hidden" name="oID" value="<?php echo $shopOrderID; ?>">
                                                <input type="hidden" name="actionSave" value="true">
                                                <table cellspacing="0" cellpadding="5" border="0" width="98%">
                                                    <tr>
                                                        <td width="50%">
                                                            <table cellspacing="0" cellpadding="5" border="0" width="400">
                                                                <tbody>
                                                                    <tr>
                                                                        <td valign="top" class="infoBoxHeading"colspan="2">
                                                                            <b><?php echo CLICKANDBUY_ORDER_DETAILS_CANCELLATION; ?>: </b>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" width="120"><?php echo CLICKANDBUY_ORDER_DETAILS_TOTAL_AMOUNT; ?>:</td>
                                                                        <td class="infoBoxContent"><?php echo $cabOrderDetails['amount'] . ' ' . $cabOrderDetails['currency']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" width="120"><?php echo CLICKANDBUY_ORDER_DETAILS_REFUNDED; ?>:</td>
                                                                        <td class="infoBoxContent"><?php echo $refunded . ' ' . $cabOrderDetails['currency']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" width="120"><?php echo CLICKANDBUY_ORDER_DETAILS_CANCELLED; ?>:</td>
                                                                        <td class="infoBoxContent"><?php echo $cabOrderDetails['cancelled'] . ' ' . $cabOrderDetails['currency']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" width="120"></td>
                                                                        <td class="infoBoxContent"><input class="submit" type="submit" value="<?php echo CLICKANDBUY_ORDER_CANCEL_CANCEL_NOW; ?>" name="cancel"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </form>
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