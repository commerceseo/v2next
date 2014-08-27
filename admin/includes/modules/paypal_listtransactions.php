<?php
/* -----------------------------------------------------------------
 * 	$Id: paypal_listtransactions.php 420 2013-06-19 18:04:39Z akausch $
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
defined("_VALID_XTC") or die("Direct access to this location isn't allowed.");

if (isset($error))
    echo $error;
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" width="10">&nbsp;</td>
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDERS_ID; ?></td>
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDERS_STATUS; ?></td>
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYPAL_ID; ?></td>
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NAME; ?></td>
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TXN_TYPE; ?></td>
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYMENT_TYPE; ?></td>
                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYMENT_STATUS; ?></td>
                    <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_PENDING_REASON; ?></td>
                    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PAYMENT_AMOUNT; ?></td>
                    <td class="dataTableHeadingContent" align="right">&#187;<?php /* echo TABLE_HEADING_ACTION; */ ?></td>
                </tr>
                <?php
                if (xtc_not_null($selected_status)) {
                    $ipn_search = "and p.payment_status = '" . xtc_db_prepare_input($selected_status) . "'";
                    switch ($selected_status) {
                        case 'Pending':
                        case 'Completed':
                        default:
                            $order_by = ' ORDER BY payment_date DESC';
                            $ipn_query_raw = "select p.xtc_order_id,p.mc_authorization,p.mc_captured, p.paypal_ipn_id, p.txn_type, p.payment_type, p.payment_status, p.pending_reason, p.payer_status, p.mc_currency, p.date_added, p.mc_gross, p.first_name, p.last_name, p.payer_business_name, p.parent_txn_id, p.txn_id,o.orders_status from " . TABLE_PAYPAL . " as p, " . TABLE_ORDERS . " as o  where o.orders_id = p.xtc_order_id " . $ipn_search . $order_by;
                            break;
                    }
                } else {
                    //       $order_by = ' ORDER BY payment_date DESC';
                    $order_by = ' ORDER BY paypal_ipn_id DESC';
                    $ipn_query_raw = "select p.xtc_order_id,p.mc_authorization,p.mc_captured, p.paypal_ipn_id, p.txn_type, p.payment_type, p.payment_status, p.pending_reason, p.payer_status, p.mc_currency, p.date_added, p.mc_gross, p.first_name, p.last_name, p.payer_business_name, p.parent_txn_id, p.txn_id,o.orders_status from " . TABLE_PAYPAL . " as p left join " . TABLE_ORDERS . " as o on o.orders_id = p.xtc_order_id where p.txn_id!='' " . $order_by;
                }
                $ipn_split = new splitPageResults($_GET['page'], '24', $ipn_query_raw, $ipn_query_numrows);
                $ipn_query_raw = xtc_db_query($ipn_query_raw);
                while ($ipn_data = xtc_db_fetch_array($ipn_query_raw)) {
                    if ($ipn_data['txn_id'] != '') {
                        echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_PAYPAL, 'page=' . $_GET['page'] . '&view=detail&paypal_ipn_id=' . $ipn_data['paypal_ipn_id']) . '\'">' . "\n";
                        ?>
                        <td class="dataTableContent" nowrap><?php echo $paypal->getStatusSymbol($ipn_data['payment_status'], $ipn_data['txn_type'], $ipn_data['pending_reason']); ?></td>
                        <td class="dataTableContent" nowrap><?php echo $ipn_data['xtc_order_id']; ?></td>
                        <td class="dataTableContent" nowrap><?php echo xtc_get_orders_status_name($ipn_data['orders_status']); ?></td>
                        <td class="dataTableContent" nowrap><?php echo $ipn_data['txn_id']; ?></td>
                        <td class="dataTableContent" nowrap><?php echo $ipn_data['first_name'] . ' ' . $ipn_data['last_name'] . ($ipn_data['payer_business_name'] != '' ? '<br />' . $ipn_data['payer_business_name'] : ''); ?></td>
                        <td class="dataTableContent" nowrap><?php echo $ipn_data['txn_type'] . '<br />'; ?></td>
                        <td class="dataTableContent" nowrap><?php echo $ipn_data['payment_type']; ?></td>
                        <td class="dataTableContent" nowrap><?php echo $paypal->getStatusName($ipn_data['payment_status'], $ipn_data['txn_type']); ?></td>
                        <td class="dataTableContent" nowrap><?php echo $ipn_data['pending_reason']; ?></td>
                        <td class="dataTableContent" align="right" nowrap><?php
                            if ($ipn_data['pending_reason'] == 'authorization') {
                                echo $ipn_data['mc_authorization'] . ' / (' . $ipn_data['mc_captured'] . ') ' . $ipn_data['mc_currency'];
                            } else {
                                echo xtc_format_price($ipn_data['mc_gross'], 1, false, 0) . ' ' . $ipn_data['mc_currency'];
                            }
                            ?>
                        </td>
                        <td class="dataTableContent" align="right"><?php echo '<a href="' . xtc_href_link(FILENAME_PAYPAL, 'view=detail' . '&paypal_ipn_id=' . $ipn_data['paypal_ipn_id']) . '">' . xtc_image(DIR_WS_ICONS . 'page_find.gif', IMAGE_ICON_INFO) . '</a>'; ?></td>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
        <td colspan="8">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td class="smallText" valign="top"><?php echo $ipn_split->display_count($ipn_query_numrows, '30', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PAYPAL_TRANSACTIONS); ?></td>
                    <td class="smallText" align="right"><?php echo $ipn_split->display_links($ipn_query_numrows, '30', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                </tr>
            </table>	
        </td>
    </tr>
</table>
</td>
</tr>
</table>