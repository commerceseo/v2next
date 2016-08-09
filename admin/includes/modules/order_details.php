<?php
/* -----------------------------------------------------------------
 * 	$Id: order_details.php 1380 2015-01-26 20:38:25Z akausch $
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
$shipping_tracking = array();
$shipping_tracking = xtc_get_tracking_status();

?>
<table>
    <tr>
        <td class="pageHeading">
            <h1>
                <?php
                $last_status = xtc_db_fetch_array(xtc_db_query("SELECT orders_status_id, date_added FROM " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_id = '" . xtc_db_input($oID) . "' ORDER BY date_added DESC LIMIT 1"));
                echo HEADING_TITLE . ' Nr : ' . $oID . ' / ' . $order->info['date_purchased'] . ' / ' . $orders_status_array[$last_status['orders_status_id']];
                ?>
            </h1>
        </td>
        <td align="right">
            <?php
            // BEGIN HERMES
            if (MODULE_SHIPPING_HERMESPROPS_STATUS == 'True' && $hermes->getUsername() != '') {
                echo '<a class="button float_right" href="' . xtc_href_link('hermes_order.php', 'orders_id=' . $_GET['oID']) . '">Hermes-Versand</a>';
            }
            // END HERMES 
            ?>
            <?php echo '<a class="button" href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('action'))) . '">' . BUTTON_BACK . '</a>'; ?>
            <a class="button" href="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'oID=' . $_GET['oID'] . '&cID=' . $order->customer['ID']); ?>"><?php echo BUTTON_EDIT ?></a>
            <?php
            // Intraship
            if (file_exists('print_intraship_label.php')) {
                $label_query = "SELECT label_url FROM orders_intraship_labels WHERE orders_id = '" . $oID . "'";
                $label_result = xtc_db_query($label_query);
                if (xtc_db_num_rows($label_result) > 0) {
                    $row = xtc_db_fetch_array($label_result);
                    $label_url = $row['label_url'];
                } else {
                    $label_url = '';
                }
                ?>
                <?php if (!empty($label_url)) { ?>
                    <a class="button float_right" href="<?php echo $label_url ?>">DHL Label</a>
                    <?php
                }
            }
            ?> 
        </td>
    </tr>
</table>
<table>
    <tr>
        <td>
            <div class="left">
                <table class="subTable" cellspacing="0" cellpadding="2" width="100%">
                    <tr>
                        <th colspan="2" class="subTable_head">
<?php echo TEXT_CUSTOMERS_INFO; ?> <?php if ($order->customer['cID'] != '') echo ' - ' . $order->customer['cID']; ?>
                        </th>
                    </tr>
                    <tr class="bb">
                        <td><?php echo TEXT_CUSTOMERS_NAME; ?></td>
                        <td>
							<?php echo $order->customer['name']; ?>&nbsp;
                            (<a href="<?php echo xtc_href_link('customers.php?cID=' . $order->customer[ID] . '&action=edit'); ?>"><i class="glyphicon glyphicon-pencil"></i></a>)&nbsp;
                            (<a href="<?php echo xtc_href_link('orders.php?cID=' . $order->customer[ID] . '&action=edit'); ?>"><i class="glyphicon glyphicon-th-list"></i></a>)
                        </td>
                    </tr>
                    <tr class="bb">
                        <td><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
                        <td>
							<?php echo $order->customer['email_address']; ?>&nbsp;
                            (<a href="mailto:<?php echo $order->customer['email_address']; ?>"><i class="glyphicon glyphicon-envelope"></i></a>)&nbsp;
                            (<a href="mail.php?selected_box=tools&customer=<?php echo $order->customer['email_address']; ?>"><i class="glyphicon glyphicon-comment"></i></a>)
                        </td>
                    </tr>
                    <tr class="bb">
                        <td><?php echo ENTRY_TELEPHONE; ?></td>
                        <td><i class="glyphicon glyphicon-earphone"></i> <?php echo $order->customer['telephone']; ?></td>
                    </tr>
<?php if ($order->customer['vat_id'] != '') { ?>
                        <tr class="bb">
                            <td class="main"><?php echo ENTRY_CUSTOMERS_VAT_ID; ?></td>
                            <td class="main"><?php echo $order->customer['vat_id']; ?></td>
                        </tr>
<?php } ?>
                    <tr class="bb">
                        <td><?php echo TEXT_CUSTOMERS_STATUS; ?></td>
                        <td><i class="glyphicon glyphicon-user"></i> <?php echo $order->info['status_name']; ?></td>
                    </tr>
                </table>
            </div>
            <div class="right">
                <table class="subTable" cellspacing="0" cellpadding="2" width="100%">
                    <tr>
                        <th colspan="2" class="subTable_head">
<?php echo TEXT_CUSTOMERS_HISTORY; ?>
                        </th>
                    </tr>
                    <tr class="bb">
                        <td>
<?php echo TEXT_CUSTOMERS_UMSATZ; ?>
                        </td>
                        <td>
                            <?php
                            $umsatz_query = xtc_db_query("SELECT SUM(op.final_price) AS ordersum
														FROM " . TABLE_ORDERS_PRODUCTS . " op
														JOIN " . TABLE_ORDERS . " o ON o.orders_id = op.orders_id
													   WHERE '" . $order->customer['ID'] . "' = o.customers_id");
                            $umsatz = xtc_db_fetch_array($umsatz_query);
                            if ($umsatz['ordersum'] > 0) {
                                echo $currencies->format($umsatz['ordersum']);
                            }
                            ?>
                        </td>
                    </tr>
                    <tr class="bb">
                        <td><?php echo TEXT_ORDER_IP; ?></td>
                        <td><?php echo $order->customer['cIP']; ?></td>
                    </tr>
                    <tr class="bb">
                        <td>
<?php echo CUSTOMERS_MEMO; ?>
                        </td>
                        <td>
                            <?php
                            $memo_count = xtc_db_fetch_array(xtc_db_query("SELECT count(*) AS count FROM " . TABLE_CUSTOMERS_MEMO . " WHERE customers_id='" . $order->customer['ID'] . "'"));
                            echo $memo_count['count'];
                            ?>  <a style="cursor:pointer" onclick="javascript:window.open('<?php echo xtc_href_link(FILENAME_POPUP_MEMO, 'ID=' . $order->customer['ID']); ?>', 'popup', 'scrollbars=yes, width=500, height=500')">(<?php echo DISPLAY_MEMOS; ?>)
                        </td>
                    </tr>
                    <tr class="bb">
                        <td><?php echo ENTRY_LANGUAGE; ?></td>
                        <td><?php echo $order->info['language']; ?></td>
                    </tr>
                </table>
            </div>
            <br style="clear:both" />


            <div class="left">
                <table class="subTable" cellspacing="0" cellpadding="4">
                    <tr>
                        <th class="subTable_head" colspan="2"><?php echo ENTRY_PAYMENT_METHOD . ' / ' . ENTRY_BILLING_ADDRESS; ?></th>
                    </tr>
                    <tr class="bb">
                        <td>
                            <?php
                            if ($order->info['payment_method'] != '') {
                                include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $order->info['payment_method'] . '.php');
                                echo constant(strtoupper('MODULE_PAYMENT_' . $order->info['payment_method'] . '_TEXT_TITLE'));
                            } else {
                                echo '<em>Es liegen keine Zahlungsinformationen vor.</em>';
                            }
                            ?>
                        </td>
                    </tr>

                    <?php
                    if ($order->info['payment_method'] == 'paypal_ipn' or $order->info['payment_method'] == 'paypal_directpayment' or $order->info['payment_method'] == 'paypal' or $order->info['payment_method'] == 'paypalexpress') {
                        require(DIR_WS_CLASSES . 'class.paypal_admin.php');
                        $paypal = new paypal_admin();
                        $paypal->admin_notification((int) $_GET['oID']);
                    }
					
					include(DIR_FS_ADMIN . 'includes/modules/order_sepa.php');

                    // begin modification for banktransfer Deprecated!!!
                    $banktransfer = xtc_db_fetch_array(xtc_db_query("SELECT * FROM banktransfer WHERE orders_id = '" . xtc_db_input($_GET['oID']) . "'"));
                    if (($banktransfer['banktransfer_bankname']) || ($banktransfer['banktransfer_blz']) || ($banktransfer['banktransfer_number'])) {
                        ?>
                        <tr>
                            <td><?php echo TEXT_BANK_NAME; ?></td>
                            <td><?php echo $banktransfer['banktransfer_bankname']; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo TEXT_BANK_BLZ; ?></td>
                            <td><?php echo $banktransfer['banktransfer_blz']; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo TEXT_BANK_NUMBER; ?></td>
                            <td><?php echo $banktransfer['banktransfer_number']; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo TEXT_BANK_OWNER; ?></td>
                            <td><?php echo $banktransfer['banktransfer_owner']; ?></td>
                        </tr>
                        <?php
                        if ($banktransfer['banktransfer_status'] == 0) {
                            ?>
                            <tr>
                                <td><?php echo TEXT_BANK_STATUS; ?></td>
                                <td><?php echo "OK"; ?></td>
                            </tr>
                            <?php
                        } else {
                            ?>
                            <tr>
                                <td><?php echo TEXT_BANK_STATUS; ?></td>
                                <td><?php echo $banktransfer['banktransfer_status']; ?></td>
                            </tr>
                            <?php
                            switch ($banktransfer['banktransfer_status']) {
                                case 1 :
                                    $error_val = TEXT_BANK_ERROR_1;
                                    break;
                                case 2 :
                                    $error_val = TEXT_BANK_ERROR_2;
                                    break;
                                case 3 :
                                    $error_val = TEXT_BANK_ERROR_3;
                                    break;
                                case 4 :
                                    $error_val = TEXT_BANK_ERROR_4;
                                    break;
                                case 5 :
                                    $error_val = TEXT_BANK_ERROR_5;
                                    break;
                                case 8 :
                                    $error_val = TEXT_BANK_ERROR_8;
                                    break;
                                case 9 :
                                    $error_val = TEXT_BANK_ERROR_9;
                                    break;
                            }
                            ?>
                            <tr>
                                <td><?php echo TEXT_BANK_ERRORCODE; ?></td>
                                <td><?php echo $error_val; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo TEXT_BANK_PRZ; ?></td>
                                <td><?php echo $banktransfer['banktransfer_prz']; ?></td>
                            </tr>
                            <?php
                        }
                    }
                    if ($banktransfer['banktransfer_fax']) {
                        ?>
                        <tr>
                            <td><?php echo TEXT_BANK_FAX; ?></td>
                            <td><?php echo $banktransfer['banktransfer_fax']; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td><?php echo xtc_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'); ?></td>
                    </tr>
                    <?php
                    if ($order->info['payment_method'] == 'amoneybookers' || $order->info['payment_method'] == 'xml_payolution_invoice_b2c' || $order->info['payment_method'] == 'xml_payolution_invoice_b2b' || $order->info['payment_method'] == 'xml_payolution_installment') {
                        if (file_exists(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/' . $order->info['payment_method'] . '.php')) {
                            include(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/' . $order->info['payment_method'] . '.php');
                            include(DIR_FS_CATALOG . 'lang/' . $order->info['language'] . '/modules/payment/' . $order->info['payment_method'] . '.php');
                            $class = $order->info['payment_method'];
                            $payment = new $class();
                            $payment->admin_order($_GET['oID']);
                        }
                    }
                    ?>
                </table>
            </div>

            <div class="right">
                <table class="subTable" cellspacing="0" cellpadding="4">
                    <tr>
                        <th class="subTable_head"><?php echo ENTRY_SHIPPING_INFO . ' / ' . ENTRY_SHIPPING_ADDRESS; ?></th>
                    </tr>
                    <tr class="bb">
                        <td>
                            <?php
                            if ($order->info['shipping_method'] != '') {
                                echo $order->info['shipping_method'];
                                ?> = <b><?php echo $order->info['shipping_cost'] . ' ' . $order->info['currency'] ?></b>
                                <?php
                            } else {
                                echo '<em>Es liegen keine Versandinformationen vor.</em>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo xtc_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'); ?></td>
                    </tr>
                </table>
            </div>
            <br style="clear:both" />
            <?php
            $download_query = xtc_db_query("SELECT * FROM orders_products_download WHERE orders_id = '" . $oID . "' ");
            if (xtc_db_num_rows($download_query)) {

                function formatBytes($bytes, $precision = 2) {
                    $units = array('B', 'KB', 'MB', 'GB', 'TB');
                    $bytes = max($bytes, 0);
                    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                    $pow = min($pow, count($units) - 1);
                    $bytes /= pow(1024, $pow);
                    return round($bytes, $precision) . ' ' . $units[$pow];
                }
                ?>
        <tr>
            <td>
                <table class="subTable" cellspacing="0" cellpadding="4" width="100%">
                    <tr>
                        <th class="subTable_head" width="100%">
                            Downloads
                        </th>
                    </tr>
                    <tr>
                        <td>
    <?php echo xtc_draw_form('downloads', FILENAME_ORDERS, xtc_get_all_get_params(array('action')) . 'action=update_order&download', 'post'); ?>
                            <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
                                <tr class="dataTableHeadingRow">
                                    <td class="dataTableHeadingContent"><?php echo NAME_OF_FILE; ?></td>
                                    <td class="dataTableHeadingContent"><?php echo VALID_THROUGH; ?></td>
                                    <td class="dataTableHeadingContent" align="center"><?php echo OPEN_DOWNLOADS; ?></td>
                                    <td class="dataTableHeadingContent"><?php echo LAST_DOWNLOAD; ?></td>
                                    <td class="dataTableHeadingContent"><?php echo DOWNLOAD_IP; ?></td>
                                    <td class="dataTableHeadingContent"><?php echo REACTIVATE; ?></td>
                                </tr>
                                <?php
                                while ($download = xtc_db_fetch_array($download_query, true)) {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php
                                            echo $download['orders_products_filename'];
                                            $size = filesize(DIR_FS_CATALOG . 'download/' . $download['orders_products_filename']);
                                            echo ' | ' . formatBytes($size);
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $dt = $order->info['date_purchased'];
                                            $date = date("d.m.Y", mktime(substr($dt, 11, 2), substr($dt, 14, 2), 0, substr($dt, 5, 2), substr($dt, 8, 2) + $download['download_maxdays'], substr($dt, 0, 4)));
                                            echo $date;
                                            ?>
                                        </td>
                                        <td align="center">
        <?php echo $download['download_count']; ?>
                                        </td>
                                        <td>
        <?php echo ($download['download_time'] != '0000-00-00 00:00:00') ? $download['download_time'] : OPEN_DOWNLOAD; ?>
                                        </td>
                                        <td align="center">
        <?php echo ($download['download_ip'] != '') ? $download['download_ip'] : '-'; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($download['download_count'] <= 0)
                                                echo xtc_draw_input_field('download[' . $download['orders_products_download_id'] . ']', '0', 'size=3') . ' <input type="image" src="images/icons/update.gif" style="position:relative;top:4px" />';
                                            ?>
                                        </td>
                                    </tr>
    <?php } ?>
                            </table>
                            </form>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
<?php } ?>
    <tr>
        <td>

				<?php 
					require_once DIR_FS_CATALOG . 'includes/classes/class.klarna.php';
					$is_klarna2 = $klarna instanceof GMKlarna;
					if($is_klarna2) {
						$okdata = $klarna->getOrdersKlarnaData($_GET['oID']);
						$has_klarna2_invoice = !empty($okdata['inv_rno']);
					}
					// BEGIN Klarna2
					$is_klarna2 = $order->info['payment_method'] == 'klarna2_invoice' || $order->info['payment_method'] == 'klarna2_partpay';
					if($is_klarna2) {
						$klarna = new GMKlarna();
						$okdata = $klarna->getOrdersKlarnaData($_GET['oID']);
						$has_klarna2_invoice = !empty($okdata['inv_rno']);
					} else {
						$has_klarna2_invoice = false;
					}
					// var_dump($okdata);
					if ($is_klarna2) {
						include_once('includes/modules/orders_klarna2.php'); 
					}
						
				?>

            <table class="subTable" cellspacing="0" cellpadding="2" width="100%">
                <tr>
                    <th class="subTable_head" width="100%">
<?php echo PRODUCTS; ?><a name="products">&nbsp;</a>
                    </th>
                </tr>
                <tr>
                    <td>
                        <?php
                        if (file_exists('includes/modules/order_create_credit.php')) {
                            if (isset($_SESSION['msg'])) {
                                echo '<script type="text/javascript">jQuery(document).ready(function() {jQuery(".msg").fadeIn(function(){setTimeout(function(){jQuery(".msg").hide("slow");}, 4500);});	});	</script>';
                                if (isset($_SESSION['msg']['no_val']))
                                    $msg = $_SESSION['msg']['no_val'];
                                elseif (isset($_SESSION['msg']['ok_val']))
                                    $msg .= $_SESSION['msg']['ok_val'];
                                elseif (isset($_SESSION['msg']['no_qty']))
                                    $msg .= $_SESSION['msg']['no_qty'];
                                unset($_SESSION['msg']);
                                echo $msg;
                            }
                            echo xtc_draw_form('products_credit', FILENAME_ORDERS, xtc_get_all_get_params(array('action')) . 'action=create_credit', 'post', 'id="submit_credit"');
                        }
                        ?>

                        <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
                            <tr class="dataTableHeadingRow">
                                <th class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></th>
                                <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></th>
                                <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></th>
								<?php if ($order->products[0]['allow_tax'] == 1) { ?>
                                    <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TAX; ?></th>
                                    <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></th>
                                    <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?></th>
                                <?php } else { ?>
                                    <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></th>
                            <?php } ?>
                            </tr>
                            <?php
                            for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
                                echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : '') . '">';
                                echo '<td valign="middle" align="left">';
                                if (file_exists('includes/modules/order_create_credit.php')) {
                                    echo '<input type="text" name="credit_products[][' . $order->products[$i]['id'] . ']" style="text-align:center" value="0" size="2" />&nbsp;&nbsp;';
									echo '<input type="hidden" name="credit_products_opid[][' . $order->products[$i]['opid'] . ']" />';
                                }
                                echo $order->products[$i]['qty'] . '&nbsp;x&nbsp;</td>
	                                    <td valign="top"><b>' . $order->products[$i]['name'] . '</b>';
                                if (sizeof($order->products[$i]['attributes']) > 0) {
                                    for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j++) {
                                        echo '<br /><nobr>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
                                    }
                                    echo '</i></nobr>';
                                }

                                if ($order->products[$i]['products_shipping_time'] != '') {
                                    echo '<br>' . BOX_SHIPPING_STATUS . ' ' . $order->products[$i]['products_shipping_time'];
                                }
                                echo '</td>' . "\n" . '<td valign="middle">';

                                if ($order->products[$i]['model'] != '') {
                                    echo $order->products[$i]['model'];
                                } else {
                                    echo '<br />';
                                }

                                // attribute models
                                if (sizeof($order->products[$i]['attributes']) > 0) {
                                    for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j++) {

                                        $model = xtc_get_attributes_model($order->products[$i]['id'], $order->products[$i]['attributes'][$j]['value'], $order->products[$i]['attributes'][$j]['option']);
                                        if ($model != '') {
                                            echo $model . '<br />';
                                        } else {
                                            echo '<br />';
                                        }
                                    }
                                }

                                echo '&nbsp;</td>' . "\n" . '
	                                <td align="right" valign="top">' . format_price($order->products[$i]['final_price'] / $order->products[$i]['qty'], 1, $order->info['currency'], $order->products[$i]['allow_tax'], $order->products[$i]['tax']) . '</td>' . "\n";

                                if ($order->products[$i]['allow_tax'] == 1) {
                                    echo '<td align="right" valign="top">';
                                    echo xtc_display_tax_value($order->products[$i]['tax']) . '%';
                                    echo '</td>' . "\n";
                                    echo '<td align="right" valign="top"><b>';

                                    echo format_price($order->products[$i]['final_price'] / $order->products[$i]['qty'], 1, $order->info['currency'], 0, 0);


                                    echo '</b></td>' . "\n";
                                }
                                echo '<td align="right" valign="top"><b>' . format_price(($order->products[$i]['final_price']), 1, $order->info['currency'], 0, 0) . '</b></td>' . "\n";
                                echo '</tr>' . "\n";
                            }
                            ?>
<?php if (file_exists('includes/modules/order_create_credit.php')) { ?>
                                <tr>
                                    <td colspan="8">
                                        <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
                                        <input class="button" type="submit" value="<?php echo STORNO_INCOICE; ?>" />
                                    </td>
                                </tr>
                                </form>
<?php } ?>
                        </table>

                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>
            <div class="left">
<?php echo xtc_draw_form('status', FILENAME_ORDERS, xtc_get_all_get_params(array('action')) . 'action=update_order'); ?>
                <table class="subTable" cellspacing="0" cellpadding="4">
                    <tr>
                        <th class="subTable_head" colspan="2">
<?php echo TABLE_HEADING_COMMENTS; ?><a name="comment" id="comment">&nbsp;</a>
                        </th>
                    </tr>
                    <tr>
                        <td colspan="2">
<?php echo ENTRY_STATUS; ?> <?php echo xtc_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status']); ?>

                        </td>
                    </tr>
<?php if (function_exists('magnaExecute')) magnaExecute('magnaRenderOrderStatusSync', array(), array('order_details.php')); ?>
                    <tr>
                        <td>
                            <label for="notify"><?php echo ENTRY_NOTIFY_CUSTOMER; ?> </label>
                            <?php 
							if (ADMIN_CSEO_ORDER_SEND == 'true') {
								echo xtc_draw_checkbox_field('notify', '', true);
							} else {
								echo xtc_draw_checkbox_field('notify', '', false);
							}
							?>
                        </td>
                        <td>
                            <label for="notify_comments"><?php echo ENTRY_NOTIFY_COMMENTS; ?></label>
                            <?php 
							if (ADMIN_CSEO_ORDER_SEND == 'true') {
								echo xtc_draw_checkbox_field('notify_comments', '', true);
							} else {
								echo xtc_draw_checkbox_field('notify_comments', '', false);
							}
							?>
                        </td>
                    </tr>
                    <tr>
                        <td align="left" colspan="2">
                            <?php
                            if (MODULE_PAYMENT_RMAMAZON_STATUS == 'True') {
                                include('amazon_storno.php');
                            }
                            ?>
                        </td>
                    </tr>
					<tr>
						<td class="main"><?php echo BOX_SHIPPING_TRACKING; ?></td>
						<td class="main"><?php echo xtc_draw_pull_down_menu('order_delivery_id', $shipping_tracking, $order->info['order_delivery_id'] == '' ? (int) (DEFAULT_SHIPPING_TRACKING_ID) : $order->info['order_delivery_id']); ?></td>
					</tr>
					<tr>
						<td class="main"><?php echo BOX_SHIPPING_TRACKING_CODE; ?></td>
						<td class="main"><?php echo xtc_draw_input_field('order_tracking_id', $order->info['order_tracking_id'], 'maxlength="100"'); ?></td>
					</tr>
                    <tr>
                        <td align="right" colspan="2">
                            <input type="submit" class="button" value="<?php echo BUTTON_SEND; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <table width="100%">
                                <?php
                                $orders_history_query = xtc_db_query("SELECT
																	orders_status_history_id, orders_status_id, date_added, customer_notified, comments
																FROM
																	" . TABLE_ORDERS_STATUS_HISTORY . "
																WHERE
																	orders_id = '" . xtc_db_input($oID) . "'
																ORDER BY
																	date_added");
                                if (xtc_db_num_rows($orders_history_query)) {
                                    $i = 1;
                                    while ($orders_history = xtc_db_fetch_array($orders_history_query)) {
                                        echo '<tr><td style="position:relative">';
                                        echo '<span class="history_del_link">
											<a href="' . xtc_href_link(FILENAME_ORDERS, 'action=update_order&del_id=' . $orders_history['orders_status_history_id'] . '&oID=' . $oID) . '">
												' . xtc_image('images/icons/document--minus.png', 'Eintrag l&ouml;schen', '', '', 'style="position:relative; top:4px; margin-right:3px"') . '
											</a>
										  </span>';
                                        echo xtc_image('images/icons/document.png', '', '', '', 'style="position:relative; top:4px; margin-right:3px"');
                                        echo '<small>' . xtc_datetime_short($orders_history['date_added'], DATE_TIME_FORMATED) . '</small> | ';
                                        echo ($orders_history['orders_status_id'] != '0') ? '<b>' . $orders_status_array[$orders_history['orders_status_id']] . '</b> | ' : '';
                                        if ($orders_history['customer_notified'] == '1') {
                                            echo '<small>' . CUSTOMER_NOTIFIED . '</small> ' . xtc_image('images/tick.gif', ICON_TICK, '', '', 'style="position:relative; top:4px"') . '<br />';
                                        } else {
                                            echo '<small' . CUSTOMER_NOT_NOTIFIED . '</small> ' . xtc_image('images/icons/minus-circle.png', ICON_TICK, '', '', 'style="position:relative; top:4px"') . '<br />';
                                        }
                                        echo '</td></tr><tr class="bb"><td>';
                                        echo($orders_history['comments'] != '') ? '<div class="order_comment">' . nl2br(strip_tags(xtc_db_output($orders_history['comments']))) . '</div>' : '';
                                        echo '</td></tr>';
                                        $i++;
                                    }
                                } else {
                                    echo TEXT_NO_ORDER_HISTORY;
                                }
                                ?>
                            </table>
                        </td>
                    </tr>
                </table>
                </form>

<?php if (function_exists('magnaExecute')) echo magnaExecute('magnaRenderOrderDetails', array('oID' => $oID), array('order_details.php')); ?>
            </div>
            <div class="right">
                <table class="subTable" cellspacing="0" cellpadding="4" width="100%">
                    <tr>
                        <th class="subTable_head" width="100%"><?php echo TOTAL; ?></th>
                    </tr>
                    <tr>
                        <td>
                            <table border="0" cellspacing="0" cellpadding="2" width="100%">
                                <?php
                                for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {
                                    echo '<tr>' . "\n" . '
											<td align="right" width="90%">' . $order->totals[$i]['title'] . '</td>' . "\n" . '
											<td align="right" width="10%" nowrap="nowrap">' . $order->totals[$i]['text'] . '</td>' . "\n" . '
										</tr>' . "\n";
                                }
                                ?>
                            </table>
                        </td>
                    </tr>
                </table>
<?php include('includes/modules/pdf_rechnung.php'); ?>

            </div>
            <br style="clear:both" />
        </td>
    </tr>
    <tr>
        <td colspan="2" align="right">
			<!-- Paymill begin -->
			<?php if ($order->info['payment_method'] == 'paymill_cc' || $order->info['payment_method'] == 'paymill_elv') { ?>
			<?php include(dirname(__FILE__) . '/../lang/' . $_SESSION['language'] . '/modules/payment/' . $order->info['payment_method'] . '.php'); ?>
			<a class="button" href="<?php echo xtc_href_link('paymill_refund.php','oID=' . $_GET['oID']); ?>"><?php echo PAYMILL_REFUND_BUTTON_TEXT; ?></a>
			<?php } ?>
            <a class="button" href="javascript:void()" onclick="window.open('<?php echo xtc_href_link(FILENAME_PRINT_ORDER, 'oID=' . $_GET['oID']); ?>', 'popup', 'toolbar=0, width=640, height=600, scrollbars=yes')"><?php echo BUTTON_INVOICE; ?></a> 
            <a class="button" href="javascript:void()" onclick="window.open('<?php echo xtc_href_link(FILENAME_PRINT_PACKINGSLIP, 'oID=' . $_GET['oID']); ?>', 'popup', 'toolbar=0, width=640, height=600, scrollbars=yes')"><?php echo BUTTON_PACKINGSLIP; ?></a>
            <?php
            if (ACTIVATE_GIFT_SYSTEM == 'true')
                echo '<a class="button" href="' . xtc_href_link(FILENAME_GV_MAIL, xtc_get_all_get_params(array('cID', 'action')) . 'cID=' . $order->customer['ID']) . '">' . BUTTON_SEND_COUPON . '</a>';
            ?>
<?php echo '<a class="button" href="' . xtc_href_link(FILENAME_ORDERS, 'page=' . $_GET['page'] . '&oID=' . $_GET['oID']) . '">' . BUTTON_BACK . '</a>'; ?>
        </td>
    </tr>
</table>