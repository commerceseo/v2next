<?php
/* -----------------------------------------------------------------
 * 	$Id: amazon_storno.php 640 2013-09-27 13:44:53Z akausch $
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

require(DIR_FS_LANGUAGES . $_SESSION['language'] . '/amazon.php');

function checkForStorno($opid, $shipping, $price) {
    $res = xtc_db_fetch_array(xtc_db_query("SELECT SUM(price) AS summe FROM amz_storno WHERE orders_products_id = '" . (int) $opid . "' AND is_shipping = '" . (int) $shipping . "'"));
    return $res['summe'] < $price;
}

function getStornoInfo($opid, $shipping) {
    global $order;
    $res = xtc_db_query("SELECT * FROM amz_storno WHERE orders_products_id = '" . (int) $opid . "' AND is_shipping = '" . (int) $shipping . "'");
    if (xtc_db_num_rows($res) > 0) {
        $ret = array();
        while ($row = xtc_db_fetch_array($res)) {
            $ret[] = AMZ_DATE . ': ' . date("d.m.Y H:i:s", strtotime($row['created_at'])) . '<br />' . AMZ_BETRAG . ': ' . $row['price'] . ' ' . $order->info['currency'] . '<br />';
        }
        return join("<br />", $ret);
    }
}

function checkPriceOK($opid, $shipping, $price_set) {
    $summe = xtc_db_fetch_array(xtc_db_query("SELECT SUM(price) AS summe FROM amz_storno WHERE orders_products_id = '" . (int) $opid . "' AND is_shipping = '" . (int) $shipping . "'"));
    $res = xtc_db_fetch_array(xtc_db_query("SELECT final_price, AmazonShippingCosts, AmazonCoupon FROM orders_products WHERE orders_products_id = '" . (int) $opid . "'"));
    if ($shipping == 0) {
        return number_format($res['final_price'] - abs($res['AmazonCoupon']) - $summe['summe'], 2) >= number_format($price_set, 2);
    } elseif ($shipping == 1) {
        return number_format($res['AmazonShippingCosts'] - $summe['summe'], 2) >= number_format($price_set, 2);
    }
}

function getPossibleRefund($opid, $shipping) {
    $summe = xtc_db_fetch_array(xtc_db_query("SELECT SUM(price) AS summe FROM amz_storno WHERE orders_products_id = '" . (int) $opid . "' AND is_shipping = '" . (int) $shipping . "'"));
    $res = xtc_db_fetch_array(xtc_db_query("SELECT final_price, AmazonShippingCosts, AmazonCoupon FROM orders_products WHERE orders_products_id = '" . (int) $opid . "'"));
    if ($shipping == 0) {
        return $res['final_price'] - abs($res['AmazonCoupon']) - $summe['summe'];
    } elseif ($shipping == 1) {
        return $res['AmazonShippingCosts'] - $summe['summe'];
    }
}

$check_status_query = xtc_db_query("select customers_name, customers_email_address, orders_status, date_purchased from " . TABLE_ORDERS . " where orders_id = '" . xtc_db_input($oID) . "'");
$check_status = xtc_db_fetch_array($check_status_query);

if ($order->info['payment_method'] == 'rmamazon' && $check_status['orders_status'] == MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_SHIPPED) {

    if (isset($_POST['amz_action']) && $_POST['amz_action'] == 'amz_storno') {
        $has_errors = false;
        $toSetInDB = array();
        if ((isset($_POST['amz_storno']) && is_array($_POST['amz_storno'])) ||
                (isset($_POST['amz_shipping_storno']) && is_array($_POST['amz_shipping_storno']))) {
            $cba_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS . " WHERE orders_id = '" . xtc_db_input($oID) . "' LIMIT 1");
            $cba_result = xtc_db_fetch_array($cba_query);
            if ($cba_result['amazon_order_id'] != '') {
                chdir("../CheckoutByAmazon");

                include_once ('.config.inc.php');

                $feeds = new MarketplaceWebService_MWSFeedsClient();
                $MWSProperties = new MarketplaceWebService_MWSProperties();
                $envelope = new SimpleXMLElement("<AmazonEnvelope></AmazonEnvelope>");
                $envelope->Header->DocumentVersion = $MWSProperties->getDocumentVersion();
                $envelope->Header->MerchantIdentifier = $MWSProperties->getMerchantToken();
                $envelope->MessageType = "OrderAdjustment";
                $envelope->Message->MessageID = 1;
                $envelope->Message->OrderAdjustment->AmazonOrderID = $cba_result['amazon_order_id'];

                $cnt = 0;
                if (isset($_POST['amz_storno']) && is_array($_POST['amz_storno'])) {
                    foreach ($_POST['amz_storno'] as $key => $opid) {
                        $amzItemCode = xtc_db_fetch_array(xtc_db_query("SELECT AmazonOrderItemCode FROM orders_products WHERE orders_products_id = '" . (int) $opid . "'"));
                        $envelope->Message->OrderAdjustment->AdjustedItem[$cnt]->AmazonOrderItemCode = $amzItemCode['AmazonOrderItemCode'];
                        $envelope->Message->OrderAdjustment->AdjustedItem[$cnt]->AdjustmentReason = MarketplaceWebService_Model_AdjustmentReason::CustomerReturn;
                        $envelope->Message->OrderAdjustment->AdjustedItem[$cnt]->ItemPriceAdjustments->Component[0]->Type = MarketplaceWebService_Model_BuyerPriceType::Principal;
                        $amount = $envelope->Message->OrderAdjustment->AdjustedItem[$cnt]->ItemPriceAdjustments->Component[0]->addChild('Amount', number_format(str_replace(",", ".", $_POST['amz_storno_price'][$key]), 2));
                        $amount->addAttribute('currency', $order->info['currency']);
                        $cnt++;
                        if (checkPriceOK($opid, 0, number_format(str_replace(",", ".", $_POST['amz_storno_price'][$key]), 2)))
                            $toSetInDB[] = array('orders_id' => (int) $oID,
                                'orders_products_id' => (int) $opid,
                                'is_shipping' => 0,
                                'created_at' => 'now()',
                                'price' => number_format(str_replace(",", ".", $_POST['amz_storno_price'][$key]), 2));
                        else
                            $has_errors = true;
                    }
                }
                if (isset($_POST['amz_shipping_storno']) && is_array($_POST['amz_shipping_storno'])) {
                    foreach ($_POST['amz_shipping_storno'] as $key => $opid) {
                        $amzItemCode = xtc_db_fetch_array(xtc_db_query("SELECT AmazonOrderItemCode FROM orders_products WHERE orders_products_id = '" . (int) $opid . "'"));
                        $envelope->Message->OrderAdjustment->AdjustedItem[$cnt]->AmazonOrderItemCode = $amzItemCode['AmazonOrderItemCode'];
                        $envelope->Message->OrderAdjustment->AdjustedItem[$cnt]->AdjustmentReason = MarketplaceWebService_Model_AdjustmentReason::CustomerReturn;
                        $envelope->Message->OrderAdjustment->AdjustedItem[$cnt]->ItemPriceAdjustments->Component[0]->Type = MarketplaceWebService_Model_BuyerPriceType::Shipping;
                        $amount = $envelope->Message->OrderAdjustment->AdjustedItem[$cnt]->ItemPriceAdjustments->Component[0]->addChild('Amount', number_format(str_replace(",", ".", $_POST['amz_storno_shipping_price'][$key]), 2));
                        $amount->addAttribute('currency', $order->info['currency']);
                        $cnt++;
                        if (checkPriceOK($opid, 1, number_format(str_replace(",", ".", $_POST['amz_storno_shipping_price'][$key]), 2)))
                            $toSetInDB[] = array('orders_id' => (int) $oID,
                                'orders_products_id' => (int) $opid,
                                'is_shipping' => 1,
                                'created_at' => 'now()',
                                'price' => number_format(str_replace(",", ".", $_POST['amz_storno_shipping_price'][$key]), 2));
                        else
                            $has_errors = true;
                    }
                }

                if ($has_errors)
                    echo '<p style="background-color: #FFCC33; font-weight: bold">' . AMZ_REFUND_ERROR . '</p>';
                else {
                    foreach ($toSetInDB as $dataset) {
                        xtc_db_perform('amz_storno', $dataset);
                    }
                    $feedSubmissionId = $feeds->refundOrder($envelope, DIR_FS_CATALOG . 'cache');
                    echo '<p style="background-color: #FFCC33; font-weight: bold">' . AMZ_REFUND_SUCCESS . '</p>';
                }

                chdir("../admin");
            }
        }
    }
    ?>
    <style> #amz_storno_table td { vertical-align: top; padding-top: 3px; padding-bottom: 3px } #amz_storno_table td.dataTableCheckbox, #amz_storno_table td.dataTableInpt { vertical-align: top; padding-top: 0px; } #amz_storno_table td.dataTableInpt { width: 180px }</style>
    <?php echo xtc_draw_form('amazon_storno', FILENAME_ORDERS, xtc_get_all_get_params(array('action')) . 'action=edit'); ?>
    <input type="hidden" name="amz_action" value="amz_storno" />
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>
            <td class="main">
                <p>
                    <strong>Amazon Refund</strong>
                    (<a href="JavaScript:void(0)" onclick="if (document.getElementById('amz_storno_table').style.display == 'none') {
                                    document.getElementById('amz_storno_table').style.display = 'block'
                                } else {
                                    document.getElementById('amz_storno_table').style.display = 'none'
                                }"><?php echo AMZ_SHOW_HIDE ?></a>)
                </p>

                <table border="0" cellpadding="0" cellpadding="2" id="amz_storno_table" style="display: <?php echo (isset($_POST['amz_action']) ? 'block' : 'none') ?>">
                    <?php
                    $haveCoupons = false;
                    for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
                        $opInfo = xtc_db_fetch_array(xtc_db_query("SELECT * FROM orders_products WHERE orders_products_id = '" . $order->products[$i]['opid'] . "'"));
                        $product_enabled = checkForStorno($order->products[$i]['opid'], 0, $order->products[$i]['final_price'] - abs($opInfo['AmazonCoupon']));
                        $shipping_enabled = checkForStorno($order->products[$i]['opid'], 1, $opInfo['AmazonShippingCosts']);
                        ?>
                        <tr class="dataTableRow">
                            <td colspan="2" class="dataTableContent">
                                <strong><?php echo $order->products[$i]['name'] ?></strong>
                                <?php
                                if (sizeof($order->products[$i]['attributes']) > 0) {
                                    echo '(';
                                    $attribStr = array();
                                    for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j++) {
                                        $attribStr[] = $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
                                    }
                                    echo join(", ", $attribStr);
                                    echo ')';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr class="dataTableRow">
                            <td style="width: 20px">&nbsp;</td>
                            <td class="dataTableContent">
                                <table border="0" cellpadding="0" cellpadding="3">
                                    <tr class="dataTableRow">
                                        <td class="dataTableContent dataTableCheckbox"><input type="checkbox" name="amz_storno[<?php echo $order->products[$i]['opid'] ?>]" value="<?php echo $order->products[$i]['opid'] ?>" <?php echo (!$product_enabled ? ' disabled="disabled"' : '') ?> /></td>
                                        <td class="dataTableContent"><?php echo AMZ_PRODUKT ?></td>
                                        <td class="dataTableContent dataTableInpt">
                                            <?php if ($product_enabled) { ?>
                                                <input type="text" name="amz_storno_price[<?php echo $order->products[$i]['opid'] ?>]" value="<?php echo number_format(getPossibleRefund($order->products[$i]['opid'], 0), 2) ?>"  <?php echo (!$product_enabled ? ' disabled="disabled"' : '') ?> /> <?php echo $order->info['currency'] ?>
                                                <?php
                                                $chck = xtc_db_fetch_array(xtc_db_query("SELECT AmazonCoupon FROM orders_products WHERE orders_products_id = '" . (int) $order->products[$i]['opid'] . "'"));
                                                if ($chck['AmazonCoupon'] != 0) {
                                                    $haveCoupons = true;
                                                    echo '*';
                                                }
                                                ?>
                                            <?php } ?>
                                            &nbsp;
                                        </td>
                                        <td class="dataTableContent">
                                            <?php echo getStornoInfo($order->products[$i]['opid'], 0); ?>
                                        </td>
                                    </tr>
                                    <tr class="dataTableRow">
                                        <td class="dataTableContent dataTableCheckbox"><input type="checkbox" name="amz_shipping_storno[<?php echo $order->products[$i]['opid'] ?>]" value="<?php echo $order->products[$i]['opid'] ?>" <?php echo (!$shipping_enabled ? ' disabled="disabled"' : '') ?> /></td>
                                        <td class="dataTableContent"><?php echo AMZ_VERSANDANTEIL ?></td>
                                        <td class="dataTableContent dataTableInpt">
                                            <?php if ($shipping_enabled) { ?>
                                                <input type="text" name="amz_storno_shipping_price[<?php echo $order->products[$i]['opid'] ?>]" value="<?php echo number_format(getPossibleRefund($order->products[$i]['opid'], 1), 2) ?>" <?php echo (!$shipping_enabled ? ' disabled="disabled"' : '') ?> /> <?php echo $order->info['currency'] ?>
                                            <?php } ?>
                                            &nbsp;
                                        </td>
                                        <td class="dataTableContent">
                                            <?php echo getStornoInfo($order->products[$i]['opid'], 1); ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="2" style="text-align:right"><input type="submit" value="<?php echo AMZ_ADMIN_BTN ?>" /></td>
                    </tr>
                    <?php if ($haveCoupons) { ?>
                        <tr>
                            <td colspan="2" style="text-align:left; font-size: 11px" class="main">
                                <?php echo AMZ_ADMIN_HINT ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
    </table>
    </form>
    <?php
}
?>