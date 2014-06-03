<?php
/* -----------------------------------------------------------------
 * 	$Id: order_overview.php 872 2014-03-21 14:46:30Z akausch $
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

function xtc_get_orders_status_color($orders_status_id, $language_id = '') {
    if (!$language_id)
        $language_id = $_SESSION['languages_id'];
    $orders_status_query = xtc_db_query("SELECT orders_status_color FROM " . TABLE_ORDERS_STATUS . " WHERE orders_status_id = '" . $orders_status_id . "' AND language_id = '" . $language_id . "'");
    $orders_status = xtc_db_fetch_array($orders_status_query);
    return $orders_status['orders_status_color'];
}
?>
<script type="text/javascript">
    function CheckAll(wert)
    {
        var maf = document.multistatusform;
        var len = maf.length;
        for (var i = 0; i < len; i++)
        {
            var e = maf.elements[i];
            if (e.name == "multistatus_ids[]")
            {
                e.checked = wert;
            }
        }
    }
</script>
<table>
    <tr>
        <td width="100%">
            <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                    <td valign="middle" align="right">
                        <?php echo xtc_draw_form('search', FILENAME_ORDERS, '', 'get'); ?>
                        <?php echo HEADING_TITLE_SEARCH . ' ' . xtc_draw_input_field('oID', '', 'size="12"') . xtc_draw_hidden_field('action', 'edit'); ?>
                        </form>   		
                        <?php echo xtc_draw_form('status', FILENAME_ORDERS, '', 'get'); ?>
                        <?php echo HEADING_TITLE_STATUS . ' ' . xtc_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), array(array('id' => '0', 'text' => TEXT_VALIDATING)), $orders_statuses), $_GET['status'], 'onChange="this.form.submit();"') . xtc_draw_hidden_field('anzahl', $_GET['anzahl']); ?>
                        </form>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td valign="top">
                        <table width="100%" class="dataTable" cellspacing="0" cellpadding="0">
                            <tr class="dataTableHeadingRow">
                                <th class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_EDIT; ?><input type="checkbox" onClick="javascript:CheckAll(this.checked);"></th>
                                <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS; ?></th>
                                <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS_CID; ?></th>
                                <th class="dataTableHeadingContent" align="right">Nr</th>
                                <th class="dataTableHeadingContent" align="center">PDF</th>
                                <th class="dataTableHeadingContent" align="center">R-Nr</th>
                                <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ORDER_TOTAL; ?></th>
                                <th class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></th>
                                <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></th>
                                <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_COUNTRY; ?></th>
                                <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PAYMENT; ?></th>
                                <th class="dataTableHeadingContent last" align="right"><?php echo TABLE_HEADING_ACTION; ?></th>
                            </tr>
                            <form action="orders.php?action=multistatus&page=<?php echo $_GET['page']; ?>&status=<?php echo $_GET['status']; ?>&anzahl=<?php echo $_GET['anzahl']; ?>" method="post" name="multistatusform">

                                <?php
                                require_once(DIR_FS_INC . 'cseo_get_pdf_nr.inc.php');
                                require_once(DIR_FS_INC . 'cseo_get_pdf.inc.php');
								if ($_GET['cID']) {
									$cID = xtc_db_prepare_input($_GET['cID']);
									$orders_query_raw = "select o.orders_id, o.order_delivery_id, o.order_tracking_id, o.customers_country, o.afterbuy_success, o.afterbuy_id, o.customers_name, o.customers_id, o.customers_cid, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, o.orders_status, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where o.customers_id = '".xtc_db_input($cID)."' and (o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and ot.class = 'ot_total') or (o.orders_status = '0' and ot.class = 'ot_total' and  s.orders_status_id = '1' and s.language_id = '".$_SESSION['languages_id']."') order by date_purchased DESC";
								} elseif ($_GET['status']=='0') {
										$orders_query_raw = "select o.orders_id, o.order_delivery_id, o.order_tracking_id, o.customers_country, o.afterbuy_success, o.afterbuy_id, o.customers_name, o.customers_cid, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, o.orders_status, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id) where o.orders_status = '0' and ot.class = 'ot_total' order by o.date_purchased DESC";
								} elseif ($_GET['status']) {
										$status = xtc_db_prepare_input($_GET['status']);
										$orders_query_raw = "select o.orders_id, o.order_delivery_id, o.order_tracking_id, o.afterbuy_success, o.afterbuy_id, o.customers_name, o.customers_cid, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and s.orders_status_id = '".xtc_db_input($status)."' and ot.class = 'ot_total' order by o.date_purchased DESC";
								} else {
									$orders_query_raw = "select o.orders_id, o.order_delivery_id, o.order_tracking_id, o.orders_status, o.customers_country, o.afterbuy_success, o.afterbuy_id, o.customers_name, o.customers_cid, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where (o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and ot.class = 'ot_total') or (o.orders_status = '0' and ot.class = 'ot_total' and  s.orders_status_id = '1' and s.language_id = '".$_SESSION['languages_id']."') order by o.date_purchased DESC";
								}

                                $orders_split = new splitPageResults($_GET['page'], ($_GET['anzahl'] != '') ? $_GET['anzahl'] : '20', $orders_query_raw, $orders_query_numrows);
                                $orders_query = xtc_db_query($orders_query_raw);
                                $rows = 1;
                                while ($orders = xtc_db_fetch_array($orders_query)) {
                                    if (((!$_GET['oID']) || ($_GET['oID'] == $orders['orders_id'])) && (!$oInfo)) {
                                        $oInfo = new objectInfo($orders);
                                    }

                                    $rech_nr = '-';
                                    $pdf_ico = '-';
                                    $delivery_ico = '-';
                                    $email = '';
                                    if (cseo_get_pdf_nr($orders['orders_id'], true) > '0') {
                                        $rech_nr = '<b>' . cseo_get_pdf_nr($orders['orders_id'], true) . '</b>';
                                        $pdf_ico_i = xtc_image('images/icons/document-pdf-text.png', 'PDF');
										$pdf_ico = '<a href="'.xtc_href_link('create_pdf_download.php', 'oID=' . $orders['orders_id'].'&pdf=bill').'">'.$pdf_ico_i.'</a>';
                                        if (cseo_get_pdf_nr($orders['orders_id'], false, false, true) > 0)
                                            $email = xtc_image('images/icons/mail--arrow.png', 'Email');
                                    }
                                    if (cseo_get_pdf_delivery_nr($orders['orders_id'], true) > '0') {
                                        $delivery_ico_i = xtc_image('images/icons/documents-text.png', 'PDF');
										$delivery_ico = '&nbsp;<a href="'.xtc_href_link('create_pdf_download.php', 'oID=' . $orders['orders_id'].'&pdf=delivery').'">'.$delivery_ico_i.'</a>';
                                    }

                                    if ((is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id)) {
                                        echo '<tr class="dataTableRowSelected">' . "\n";
                                    } else {
                                        $f = xtc_get_orders_status_color($orders['orders_status'], $_SESSION['languages_id']);
                                        if ($f == '') {
                                            if ($rows % 2 == 0) {
                                                $f = 'dataTableRow';
                                            } else {
                                                $f = '';
                                            }
                                            echo '<tr class="' . $f . '">' . "\n";
                                        } else {
                                            echo '<tr style="background-color:#' . $f . '">' . "\n";
                                        }
                                    }
                                    ?>
                                    <td class="dataTableContent" align="center">
                                        <input id="multistatus_ids" type="checkbox" name="multistatus_ids[]" value="<?php echo($orders['orders_id']) ?>"/>
                                    </td>
                                    <td <?php if (function_exists('magnaExecute')) echo magnaExecute('magnaRenderOrderPlatformIcon', array('oID' => $orders['orders_id']), array('order_details.php')); ?>>
    <?php echo '<a href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit') . '">' . $orders['customers_name'] . '</a>'; ?>
                                    </td>
                                    <td align="right"><?php echo ($orders['customers_cid'] != '') ? $orders['customers_cid'] : '-'; ?></td>
                                        <?php
                                        if ((is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id)) {
                                            echo '<td class="dataTableContent" align="right"><a href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '">' . $orders['orders_id'] . '</a></td>';
                                        } else {
                                            echo '<td class="dataTableContent" align="right"><a href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID')) . 'oID=' . $orders['orders_id']) . '">' . $orders['orders_id'] . '</a></td>';
                                        }
                                        ?>
                                    <td align="center"><?php echo $pdf_ico . $delivery_ico . $email; ?></td>
                                    <td align="center"><?php echo $rech_nr; ?></td>
                                    <td align="right"><?php echo strip_tags($orders['order_total']); ?></td>
                                    <td align="center"><?php echo xtc_datetime_short($orders['date_purchased']); ?></td>
                                    <td align="right"><?php
                                if ($orders['orders_status'] != '0') {
                                    echo $orders['orders_status_name'];
                                } else {
                                    echo '<font color="#FF0000">' . TEXT_VALIDATING . '</font>';
                                }
                                        ?></td>
                                    <td align="right"><?php echo $orders['customers_country']; ?></td>
                                    <td align="right"><?php echo $orders['payment_method']; ?></td>
                                        <?php if (AFTERBUY_ACTIVATED == 'true') { ?>
                                        <td align="right"><?php
                                    if ($orders['afterbuy_success'] == 1) {
                                        echo $orders['afterbuy_id'];
                                    } else {
                                        echo 'TRANSMISSION_ERROR';
                                    }
                                            ?></td>
                                        <?php } ?>
                                    <td align="right" class="last"><?php
                                        if ((is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id)) {
                                            echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', '');
                                        } else {
                                            echo '<a href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'dl', 'gp', 'ms')) . 'oID=' . $orders['orders_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                                            echo ' <a href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'dl', 'gp', 'ms')) . 'oID=' . $orders['orders_id']) . '&action=edit">' . xtc_image(DIR_WS_IMAGES . 'icon_edit.gif', IMAGE_ICON_ORDER_EDIT) . '</a>';
                                        }
                                        ?>
                                    </td>
                                    </tr>		
                                        <?php
                                        $rows++;
                                    }
                                    ?>
                        </table>
                    </td>
<?php
$heading = array();
$contents = array();
switch ($_GET['action']) {
    case 'delete' :
        $heading[] = array('text' => '</form>');
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ORDER . '</b>');
        $contents = array('form' => xtc_draw_form('orders', FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=deleteconfirm'));
        $contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('restock') . ' ' . TEXT_INFO_RESTOCK_PRODUCT_QUANTITY);

        if (defined('TABLE_PAYPAL')):
            $db_installed = false;
            $tables = mysql_list_tables(DB_DATABASE);
            while ($row = mysql_fetch_row($tables)) {
                if ($row[0] == TABLE_PAYPAL)
                    $db_installed = true;
            }
            if ($db_installed == true):
                $query = "SELECT * FROM " . TABLE_PAYPAL . " WHERE xtc_order_id = '" . $oInfo->orders_id . "'";
                $query = xtc_db_query($query);
                if (xtc_db_num_rows($query) > 0):
                    $contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('paypaldelete') . ' ' . TEXT_INFO_PAYPAL_DELETE);
                endif;
            endif;
        endif;
        $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" value="' . BUTTON_DELETE . '"><a class="button" href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id) . '">' . BUTTON_CANCEL . '</a>');
        break;

    default :
        if (is_object($oInfo)) {
            $heading[] = array('text' => '<b>[' . $oInfo->orders_id . ']&nbsp;&nbsp;' . xtc_datetime_short($oInfo->date_purchased) . '</b>');
            $contents[] = array('align' => 'center', 'text' => '<b>' . TEXT_ACTIVE_ELEMENT . '</b>');
            $contents[] = array('align' => 'center', 'text' => '<br /><a class="button" href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'action', 'print_oID')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '">' . BUTTON_EDIT . '</a> <a class="button" href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'action', 'print_oID')) . 'oID=' . $oInfo->orders_id . '&action=delete') . '">' . BUTTON_DELETE . '</a><br /><br />');
            if ($oInfo->payment_method == 'billsafe_2') {
                $contents[] = array('align' => 'center', 'text' => '<a class="button" href="billsafe_orders_2.php?oID=' . $oInfo->orders_id . '">BillSAFE Details</a>');
            }
            if (file_exists('print_intraship_label.php')) {
                $contents[] = array('align' => 'center', 'text' => '<a class="button" href="' . xtc_href_link('print_intraship_label.php', 'oID=' . $oInfo->orders_id) . '">DHL Label</a>');
            }

            $contents[] = array('align' => 'center', 'text' => '<a class="button" href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=send&sta=1&stc=0') . '">' . 'An Admin Erneut Versenden' . '</a><br /><br />');
            $contents[] = array('align' => 'center', 'text' => '<a class="button" href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=send&sta=0&stc=1') . '">' . 'An Kunden Erneut Versenden' . '</a><br /><br />');

            if (AFTERBUY_ACTIVATED == 'true') {
                $contents[] = array('align' => 'center', 'text' => '<a class="button" href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=afterbuy_send') . '">' . BUTTON_AFTERBUY_SEND . '</a>');
            }
            if (MODULE_PAYMENT_PI_CLICKANDBUY_STATUS == 'True' || MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_STATUS == 'True') {
            // ClickandBuy START
				$contents[] = array('align' => 'center', 'text' => '<br /><a class="button" href="pi_clickandbuy_details.php?selected_box=customers&oID=' . $oInfo->orders_id . '">ClickandBuy Details</a>');
            // ClickandBuy END
			}
            if (MODULE_SHIPPING_HERMESPROPS_STATUS == 'True') {
            // BEGIN Hermes
                $contents[] = array('align' => 'left', 'text' => '<div align="center"><a class="button" href="' . xtc_href_link('hermes_order.php', 'orders_id=' . $oInfo->orders_id) . '">Hermes Versand</a></div>');
            // END Hermes
            }
			
            $contents[] = array('align' => 'center', 'text' => '<hr size="1" style="color:#ccc" />');
            $contents[] = array('align' => 'center', 'text' => '<b>' . TEXT_INFORMATIONS . '</b>');
			// print_r($oInfo);
			if ($oInfo->order_tracking_id != '') {
            $contents[] = array('text' => TEXT_TRACKING_SHIPPING . xtc_get_shipping_tracking_name($oInfo->order_delivery_id));
            $contents[] = array('text' => TEXT_TRACKING_CODE . $oInfo->order_tracking_id);
			
			}
            $contents[] = array('text' => '<br />' . TEXT_DATE_ORDER_CREATED . ' ' . xtc_date_short($oInfo->date_purchased));
            if (xtc_not_null($oInfo->last_modified)) {
                $contents[] = array('text' => TEXT_DATE_ORDER_LAST_MODIFIED . ' ' . xtc_date_short($oInfo->last_modified));
            }
            if ($oInfo->payment_method != '' && $oInfo->payment_method != 'no_payment') {
                include(DIR_FS_CATALOG . 'lang/' . $_SESSION['language'] . '/modules/payment/' . $oInfo->payment_method . '.php');
                $payment_method = constant(strtoupper('MODULE_PAYMENT_' . $oInfo->payment_method . '_TEXT_TITLE'));
                $contents[] = array('text' => '<br /><u>' . TEXT_INFO_PAYMENT_METHOD . '</u><br />&nbsp;' . $payment_method);
            }
            $order = new order($oInfo->orders_id);
            $contents[] = array('text' => '<br /><u>' . sizeof($order->products) . ' ' . PRODUCTS . '</u>');
            for ($i = 0; $i < sizeof($order->products); $i++) {
                $contents[] = array('text' => $order->products[$i]['qty'] . 'x&nbsp;' . $order->products[$i]['name']);

                if (sizeof($order->products[$i]['attributes']) > 0) {
                    for ($j = 0; $j < sizeof($order->products[$i]['attributes']); $j++) {
                        $contents[] = array('text' => '<small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small></nobr>');
                    }
                }
            }

            
			
			if ($_GET['page'] != '') {
                $contents[] = array('text' => xtc_draw_hidden_field('page', $_GET['page']));
            }
            if ($_GET['status'] != '') {
                $contents[] = array('text' => xtc_draw_hidden_field('statusn', $_GET['status']));
            }
            if ($_GET['anzahl'] != '') {
                $contents[] = array('text' => xtc_draw_hidden_field('anzahl', $_GET['anzahl']));
            }
			


        }
        break;
}

if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
    echo '<td width="25%" valign="top">' . "\n";
    $box = new box;
    echo $box->infoBox($heading, $contents) . '<br />';
    echo '</td>' . "\n";
}
$order_page_dropdown = '<form name="anzahl" action="' . $_SERVER['REQUEST_URI'] . '" method="GET">' . "\n";

if ($_GET['oID'] != '')
    $order_page_dropdown .= xtc_draw_hidden_field('oID', $_GET['oID']);
if ($_GET['page'] != '')
    $order_page_dropdown .= xtc_draw_hidden_field('page', $_GET['page']) . "\n";
if ($_GET['status'] != '')
    $order_page_dropdown .= xtc_draw_hidden_field('status', $_GET['status']) . "\n";

$customers_options = array();

$order_options[] = array('id' => '10', 'text' => '10');
$order_options[] = array('id' => '20', 'text' => '20');
$order_options[] = array('id' => '50', 'text' => '50');
$order_options[] = array('id' => '100', 'text' => '100');

$order_page_dropdown .= xtc_draw_pull_down_menu('anzahl', $order_options, ($_GET['anzahl'] != '' ? $_GET['anzahl'] : '20'), 'onchange="this.form.submit()"') . "\n";

$order_page_dropdown .= '</form>' . "\n";
?>
                </tr>
                <tr>
                    <td colspan="5">
                        <table border="0" cellspacing="0" cellpadding="10" width="100%">
                            <tr>

                                <td class="smallText" valign="top" width="33.33%"><?php echo $orders_split->display_count($orders_query_numrows, ($_GET['anzahl'] != '') ? $_GET['anzahl'] : '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
                                <td class="smallText" align="right" width="33.33%"><?php echo $orders_split->display_links($orders_query_numrows, ($_GET['anzahl'] != '') ? $_GET['anzahl'] : '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page'], xtc_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
                                <td align="right" width="33.33%">
<?php echo 'Bestellungen pro Seite: ' . $order_page_dropdown; ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>