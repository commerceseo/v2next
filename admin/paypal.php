<?php
/* -----------------------------------------------------------------
 * 	$Id: paypal.php 872 2014-03-21 14:46:30Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

require('includes/application_top.php');
// load classes
// require('../includes/classes/paypal_checkout.php');
require_once(DIR_FS_INC . 'xtc_format_price.inc.php');
$paypal = new paypal_checkout();
// refunding
switch ($_GET['view']) {
    case 'refund' :
        if (isset($_GET['paypal_ipn_id'])) {
            $query = "SELECT * FROM " . TABLE_PAYPAL . " WHERE paypal_ipn_id = '" . (int) $_GET['paypal_ipn_id'] . "'";
            $query = xtc_db_query($query);
            $ipn_data = xtc_db_fetch_array($query);
        }
        if ($_GET['action'] == 'perform') {
            // refunding
            $txn_id = xtc_db_prepare_input($_POST['txn_id']);
            $ipn_id = xtc_db_prepare_input($_POST['ipn_id']);
            $amount = xtc_db_prepare_input($_POST['amount']);
            $note = xtc_db_prepare_input($_POST['refund_info']);
            $refund_amount = xtc_db_prepare_input($_POST['refund_amount']);
            $query = "SELECT * FROM " . TABLE_PAYPAL . " WHERE paypal_ipn_id = '" . (int) $ipn_id . "'";
            $query = xtc_db_query($query);
            $ipn_data = xtc_db_fetch_array($query);
            $response = $paypal->RefundTransaction($txn_id, $ipn_data['mc_currency'], $amount, $refund_amount, $note);
            if ($response['ACK'] == 'Success') {
                xtc_redirect(xtc_href_link(FILENAME_PAYPAL, 'err=refund_Success'));
            } else {
                xtc_redirect(xtc_href_link(FILENAME_PAYPAL, 'view=detail&paypal_ipn_id=' . (int) $ipn_id . '&err=error_' . $response['L_ERRORCODE0']));
            }
        }
        break;
    case 'search' :
        $date = array();
        $date['actual']['tt'] = date('d');
        $date['actual']['mm'] = date('m');
        $date['actual']['yyyy'] = date('Y');
        $last_month = mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
        $date['last_month']['tt'] = date('d', $last_month);
        $date['last_month']['mm'] = date('m', $last_month);
        $date['last_month']['yyyy'] = date('Y', $last_month);
        if ($_GET['action'] == 'perform') {
            $response = $paypal->TransactionSearch($_POST);
        }
        break;
    case 'capture' :
        if (PAYPAL_COUNTRY_MODE != 'uk')
            xtc_redirect(xtc_href_link(FILENAME_PAYPAL));
        if (isset($_GET['paypal_ipn_id'])) {
            $query = "SELECT * FROM " . TABLE_PAYPAL . " WHERE paypal_ipn_id = '" . (int) $_GET['paypal_ipn_id'] . "'";
            $query = xtc_db_query($query);
            $ipn_data = xtc_db_fetch_array($query);
        }
        if ($_GET['action'] == 'perform') {
            // refunding
            $txn_id = xtc_db_prepare_input($_POST['txn_id']);
            $ipn_id = xtc_db_prepare_input($_POST['ipn_id']);
            $amount = xtc_db_prepare_input($_POST['amount']);
            $note = xtc_db_prepare_input($_POST['refund_info']);
            $capture_amount = xtc_db_prepare_input($_POST['capture_amount']);
            $query = "SELECT * FROM " . TABLE_PAYPAL . " WHERE paypal_ipn_id = '" . (int) $ipn_id . "'";
            $query = xtc_db_query($query);
            $ipn_data = xtc_db_fetch_array($query);
            $response = $paypal->DoCapture($txn_id, $ipn_data['mc_currency'], $amount, $capture_amount, $note);
            if ($response['ACK'] == 'Success') {
                $response = $paypal->GetTransactionDetails($ipn_data['txn_id']);
                $data = array();
                $data['paypal_ipn_id'] = $ipn_id;
                $data['txn_id'] = $txn_id;
                $data['payment_status'] = 'Pending';
                $data['pending_reason'] = 'partial-capture';
                $data['mc_amount'] = $capture_amount;
                $data['date_added'] = 'now()';
                if ($response['PAYMENTSTATUS'] == 'Completed') {
                    $data['payment_status'] = 'Completed';
                    $data['pending_reason'] = 'completed-capture';
                    xtc_db_query("UPDATE " . TABLE_PAYPAL . " SET payment_status='Completed',pending_reason='',mc_gross=mc_authorization WHERE paypal_ipn_id='" . $ipn_id . "'");
                }
                // update captured amount
                xtc_db_query("UPDATE " . TABLE_PAYPAL . " SET mc_captured = (mc_captured+" . $capture_amount . ") WHERE paypal_ipn_id='" . $ipn_id . "'");
                // save capture in DB
                xtc_db_perform('paypal_status_history', $data);
                // update transaction
                xtc_redirect(xtc_href_link(FILENAME_PAYPAL, 'err=capture_Success'));
            } else {
                xtc_redirect(xtc_href_link(FILENAME_PAYPAL, 'view=capture&paypal_ipn_id=' . (int) $ipn_id . '&err=error_' . $response['L_ERRORCODE0']));
            }
        }
        break;
}

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td width="100%">
                        <table border="0" class="table_pageHeading" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="100" rowspan="2"><img src="images/logo_paypal.gif" /></td>
                                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>			
                                    <?php if (!isset($_GET['view'])) { ?>
                                    <td valign="middle" align="right"><a class="button" href="<?php echo xtc_href_link(FILENAME_PAYPAL, 'view=search'); ?>"><?php echo BUTTON_SEARCH; ?></a></td>
<?php } ?>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        // errors
                        if (isset($_GET['err']))
                            $error = $paypal->getErrorDescription($_GET['err']);
                        switch ($_GET['view']) {
                            case 'detail' :
                                include(DIR_WS_MODULES . 'paypal_transactiondetail.php');
                                break;
                            case 'refund' :
                                include(DIR_WS_MODULES . 'paypal_refundtransaction.php');
                                break;
                            case 'capture' :
                                include(DIR_WS_MODULES . 'paypal_capturetransaction.php');
                                break;
                            case 'search' :
                                include(DIR_WS_MODULES . 'paypal_searchtransaction.php');
                                break;
                            case 'auth' :
                                include(DIR_WS_MODULES . 'paypal_authtransaction.php');
                                break;
                            default :
                                include(DIR_WS_MODULES . 'paypal_listtransactions.php');
                                break;
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
