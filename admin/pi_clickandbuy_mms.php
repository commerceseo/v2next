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

$shopOrderID = $_POST['oID'];

$mmsList = array();
$i = 0;

$orderQuery = xtc_db_query("SELECT externalID FROM picab_orders WHERE shopOrderID ='" . $shopOrderID . "'");
$row = xtc_db_fetch_array($orderQuery);
$externalID = $row['externalID'];


$mmsQuery = xtc_db_query("SELECT * FROM picab_mms WHERE shopOrderID ='" . $shopOrderID . "'  OR externalID ='" . $externalID . "' order by eventID");

while ($row = xtc_db_fetch_array($mmsQuery)) {
    $mmsList[$i] = $row;
    $i++;
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
                <td width="100%" valign="top">
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                            <td>
                                <form name="refund" action="pi_clickandbuy_details.php" method="GET">
                                    <input type="hidden" value="<?php echo $shopOrderID; ?>" name="oID">
                                    <table border="0" width="100%" cellspacing="0" cellpadding="2" height="40">
                                        <tr>
                                            <td class="pageHeading"><?php echo CLICKANDBUY_ORDER_CLICKANDBUY . ' ' . CLICKANDBUY_ORDER_MMS; ?></td>
                                            <td class="pageHeading" align="right"><input class="submit" type="submit" value="<?php echo CLICKANDBUY_ORDER_DETAILS_BACK; ?>" name="back"></td>
                                        </tr>
                                    </table>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                    <tr>
                                        <td>
                                            <!-- ClickandBuy Details START -->
                                            <?php
                                            if (empty($mmsList)) {
                                                ?>
                                                <p><?php echo CLICKANDBUY_ORDER_NO_ENTRIES; ?></p>
                                                <?php
                                            } else {
                                                ?>
                                                <table cellspacing="0" cellpadding="5" border="0" width="100%">
                                                    <tr>
                                                        <td class="infoBoxHeading" width="15%"><?php echo CLICKANDBUY_ORDER_MMS_EVENTID; ?></td>
                                                        <td class="infoBoxHeading"><?php echo CLICKANDBUY_ORDER_DETAILS_TRANSACTIONID; ?></td>
                                                        <td class="infoBoxHeading" width="15%"><?php echo CLICKANDBUY_ORDER_DETAILS_EXTERNALID; ?></th>
                                                        <td class="infoBoxHeading" width="15%"><?php echo CLICKANDBUY_ORDER_MMS_STATE_OLD; ?></td>
                                                        <td class="infoBoxHeading" width="15%"><?php echo CLICKANDBUY_ORDER_MMS_STATE_NEW; ?></td>
                                                        <td class="infoBoxHeading" width="15%"><?php echo CLICKANDBUY_ORDER_MMS_XML; ?></td>
                                                        <td class="infoBoxHeading" width="15%"><?php echo CLICKANDBUY_ORDER_DETAILS_DATE_TIME; ?></td>
                                                    </tr>								
                                                    <?php
                                                    foreach ($mmsList as $item => $value) {
                                                        ?>
                                                        <tr>
                                                            <td class="infoBoxContent" valign="top" width="15%"> <?php echo $value['eventID']; ?></td>
                                                            <td class="infoBoxContent" valign="top"> <?php echo $value['transactionID']; ?></td>
                                                            <td class="infoBoxContent" valign="top" width="15%"> <?php echo $value['externalID']; ?></td>
                                                            <td class="infoBoxContent" valign="top" width="15%"> <?php echo $value['oldState']; ?></td>
                                                            <td class="infoBoxContent" valign="top" width="15%"> <?php echo $value['newState']; ?></td>
                                                            <td class="infoBoxContent" valign="top" width="15%"> 
                                                                <div id="infoBoxMMS">
                                                                    <a href="#">
                                                                        <img src="images/icon_info.gif" border="0" alt="" title=""/>
                                                                        <span>
                                                                            <textarea class="infoBox" readonly cols="60" rows="20">
                                                                                <?php echo $value['xml']; ?>
                                                                            </textarea>
                                                                            <br/>
                                                                        </span>
                                                                    </a>
                                                                </div>								
                                                            </td>
                                                            <td class="infoBoxContent" valign="top" width="15%"> <?php echo $value['created']; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </table>
                                                <?php
                                            }
                                            ?>
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