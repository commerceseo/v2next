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
!empty($_POST['externalID']) ? $externalID = $_POST['externalID'] : $externalID = substr(md5(uniqid(rand())), 0, 12);
!empty($_POST['intPlaces']) ? $intPlaces = $_POST['intPlaces'] : $intPlaces = '0';
!empty($_POST['decPlaces']) ? $decPlaces = $_POST['decPlaces'] : $decPlaces = '00';
!empty($_POST['actionSave']) ? $actionSave = true : $actionSave = false;

if (!empty($_POST['emailAddress']))
    $emailAddress = $_POST['emailAddress'];
if (!empty($_POST['itemDesc1']))
    $itemDesc1 = $_POST['itemDesc1'];

$postAmount = (float) $intPlaces . '.' . $decPlaces;

$value_query = xtc_db_query("SELECT customers_email_address, billing_country_iso_code_2 FROM orders WHERE orders_id ='" . $shopOrderID . "' ");
$orders = xtc_db_fetch_array($value_query);
!empty($_POST['emailAddress']) ? $emailAddress = $_POST['emailAddress'] : $emailAddress = $orders['customers_email_address'];

$value_query = xtc_db_query("SELECT * FROM picab_orders WHERE shopOrderID ='" . $shopOrderID . "' ");
$cabOrderDetails = xtc_db_fetch_array($value_query);

$authentication = $cabXtcApi->getCabSettings($cabOrderDetails['paymentType']);

$amount = (float) $cabOrderDetails['amount'];
$refunded = (float) $cabOrderDetails['refunded'] + $postAmount;
$cancelled = (float) $cabOrderDetails['cancelled'];
$openAmount = ($amount - $refunded - $cancelled);

if (($actionSave)) {
    if ($postAmount <= 0.00) {
        $postAmount = 0;
        $messageBox = 'ERROR';
        $errors[] = $errorDescription = 'Invalid Credit amount';
    }

    if (!$errors) {
        $items = array();
        $details = array();

        $details['amount'] = $postAmount;
        $details['currency'] = $cabOrderDetails['currency'];
        $details['currency'] = 'EUR';
        $details['emailAddress'] = $emailAddress;
        $details['consumer_language'] = $orders['billing_country_iso_code_2'];
        $items[1]['itemType'] = 'item1Text';
        $items[1]['textItemDescription'] = $itemDesc1;

        $requestResult = $cabApi->creditRequest($authentication, $details, $items);
        $requestValues = $requestResult['values'];

        if ($requestResult['success'] == 1) {
            $messageBox = 'SUCCESS';
            $transactionID = $requestValues['transaction']['transactionID'];
            $transactionStatus = $requestValues['transaction']['transactionStatus'];

            xtc_db_query("INSERT INTO picab_transactions (id,shopOrderID,transactionID,externalID,transactionType,description,amount,currency,paid,status,created,modified) VALUES (NULL,'" . $shopOrderID . "','" . $transactionID . "','" . $externalID . "','credit','" . $itemDesc1 . "'," . $postAmount . ",'" . $cabOrderDetails['currency'] . "',1,'" . $transactionStatus . "',NOW(),NOW())");
        } else {
            $messageBox = 'ERROR';
            $errorDescription = $requestValues['detail']['errorDetails']['description'];
        }
    }
}
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
                                            <td class="pageHeading"><?php echo CLICKANDBUY_ORDER_CLICKANDBUY . ' ' . CLICKANDBUY_ORDER_DETAILS_CREDIT; ?></td>
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
                                    $showMessage = $cabXtcApi->showMessageSuccess(CLICKANDBUY_ORDER_CREDIT_SUCCESSFUL);
                                    echo $showMessage;
                                } elseif ($messageBox == 'ERROR') {
                                    $showMessage = $cabXtcApi->showMessageError(CLICKANDBUY_ORDER_CREDIT_ERROR . ' ' . $errorDescription);
                                    echo $showMessage;
                                }
                                ?>
                                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                    <tr>
                                        <td>
                                            <!-- ClickandBuy Details START -->
                                            <form name="credit" action="pi_clickandbuy_credit.php" method="POST">
                                                <input type="hidden" name="oID" value="<?php echo $shopOrderID; ?>">
                                                <input type="hidden" name="actionSave" value="true">
                                                <input type="hidden" value="<?php echo $externalID; ?>" name="externalID">
                                                <table cellspacing="0" cellpadding="0" border="0" width="98%">
                                                    <tr>
                                                        <td width="50%">
                                                            <table cellspacing="0" cellpadding="5" border="0" width="400">
                                                                <tbody>
                                                                    <tr>
                                                                        <td valign="top" class="infoBoxHeading" colspan="2">
                                                                            <b><?php echo CLICKANDBUY_ORDER_DETAILS_CREDIT; ?>: </b>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20"><?php echo CLICKANDBUY_ORDER_DETAILS_EXTERNALID; ?>:</td>
                                                                        <td class="infoBoxContent"><b><?php echo $externalID; ?></b></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" width="120"><?php echo CLICKANDBUY_ORDER_CREDIT_EMAIL; ?>:</td>
                                                                        <td class="infoBoxContent"><input type="text" value="<?php echo $emailAddress; ?>" name="emailAddress" maxlength="255" size="25" class="editinput"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" width="120"><?php echo CLICKANDBUY_ORDER_DETAILS_AMOUNT; ?>:</td>
                                                                        <td class="infoBoxContent">
                                                                            <input name="intPlaces" type="text"  value="<?php echo $intPlaces; ?>" size="3" maxlength="12">,
                                                                            <input name="decPlaces" type="text"  value="<?php echo $decPlaces; ?>" size="2" maxlength="2"> <?php echo $cabOrderDetails['currency']; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" width="120"><?php echo CLICKANDBUY_ORDER_DETAILS_DESCRIPTION; ?>:</td>
                                                                        <td class="infoBoxContent"><input type="text" value="<?php echo $itemDesc1; ?>" name="itemDesc1" maxlength="255" size="25"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="infoBoxContent" height="20" width="120">&nbsp;</td>
                                                                        <td class="infoBoxContent"><input type="submit" value="<?php echo CLICKANDBUY_ORDER_CREDIT_CREDIT_NOW; ?>" name="credit" class="submit"></td>
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