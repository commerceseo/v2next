<?php
/* -----------------------------------------------------------------
 * 	$Id: gv_sent.php 420 2013-06-19 18:04:39Z akausch $
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

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td width="100%">
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                                        <tr class="dataTableHeadingRow">
                                            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SENDERS_NAME; ?></td>
                                            <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_VOUCHER_VALUE; ?></td>
                                            <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_VOUCHER_CODE; ?></td>
                                            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_DATE_SENT; ?></td>		
                                            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                                        </tr>
                                        <?php
                                        if ($_GET['delete'] == '1') {
                                            xtc_db_query("DELETE FROM coupons WHERE coupon_id = '" . (int) $_GET['gID'] . "' ");
                                            xtc_db_query("DELETE FROM coupon_email_track WHERE coupon_id = '" . (int) $_GET['gID'] . "' ");
                                        }

                                        $gv_query_raw = "select c.coupon_amount, c.coupon_code, c.coupon_id, et.sent_firstname, et.sent_lastname, et.customer_id_sent, et.emailed_to, et.date_sent, c.coupon_id from " . TABLE_COUPONS . " c, " . TABLE_COUPON_EMAIL_TRACK . " et where c.coupon_id = et.coupon_id";
                                        $gv_split = new splitPageResults($_GET['page'], '20', $gv_query_raw, $gv_query_numrows);
                                        $gv_query = xtc_db_query($gv_query_raw);
                                        while ($gv_list = xtc_db_fetch_array($gv_query)) {
                                            if (((!$_GET['gid']) || (@$_GET['gid'] == $gv_list['coupon_id'])) && (!$gInfo)) {
                                                $gInfo = new objectInfo($gv_list);
                                            }
                                            if ((is_object($gInfo)) && ($gv_list['coupon_id'] == $gInfo->coupon_id)) {
                                                echo '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link('gv_sent.php', xtc_get_all_get_params(array('gid', 'action')) . 'gid=' . $gInfo->coupon_id . '&action=edit') . '\'">' . "\n";
                                            } else {
                                                echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link('gv_sent.php', xtc_get_all_get_params(array('gid', 'action')) . 'gid=' . $gv_list['coupon_id']) . '\'">' . "\n";
                                            }
                                            ?>
                                            <td class="dataTableContent"><?php echo $gv_list['sent_firstname'] . ' ' . $gv_list['sent_lastname']; ?></td>
                                            <td class="dataTableContent" align="center"><?php echo $currencies->format($gv_list['coupon_amount']); ?></td>
                                            <td class="dataTableContent" align="center"><?php echo $gv_list['coupon_code']; ?></td>
                                            <td class="dataTableContent" align="right"><?php echo xtc_date_short($gv_list['date_sent']); ?></td>
                                            <td class="dataTableContent" align="right"><?php if ((is_object($gInfo)) && ($gv_list['coupon_id'] == $gInfo->coupon_id)) {
                                            echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif');
                                        } else {
                                            echo '<a href="' . xtc_href_link(FILENAME_GV_SENT, 'page=' . $_GET['page'] . '&gid=' . $gv_list['coupon_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                                        } ?>&nbsp;</td>
                                            <?php
                                            echo '</tr>';
                                        }
                                        ?>
                                        <tr>
                                            <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                                                    <tr>
                                                        <td class="smallText" valign="top"><?php echo $gv_split->display_count($gv_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_GIFT_VOUCHERS); ?></td>
                                                        <td class="smallText" align="right"><?php echo $gv_split->display_links($gv_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                                                    </tr>
                                                </table></td>
                                        </tr>
                                    </table></td>
                                <?php
                                $heading = array();
                                $contents = array();

                                $heading[] = array('text' => '[' . $gInfo->coupon_id . '] ' . ' ' . $currencies->format($gInfo->coupon_amount));
                                $redeem_query = xtc_db_query("select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $gInfo->coupon_id . "'");
                                $redeemed = 'No';
                                if (xtc_db_num_rows($redeem_query) > 0)
                                    $redeemed = 'Yes';
                                $contents[] = array('align' => 'center', 'text' => '<form action=""><a href="' . xtc_href_link(FILENAME_GV_SENT, 'page=' . $_GET['page'] . '&delete=1&gID=' . $gInfo->coupon_id) . '" class="button">' . TABLE_HEADING_VOUCHER_CODE . ' ' . BUTTON_DELETE . '</a>');
                                $contents[] = array('text' => TEXT_INFO_SENDERS_ID . ' ' . $gInfo->customer_id_sent);
                                $contents[] = array('text' => TEXT_INFO_AMOUNT_SENT . ' ' . $currencies->format($gInfo->coupon_amount));
                                $contents[] = array('text' => TEXT_INFO_DATE_SENT . ' ' . xtc_date_short($gInfo->date_sent));
                                $contents[] = array('text' => TEXT_INFO_VOUCHER_CODE . ' ' . $gInfo->coupon_code);
                                $contents[] = array('text' => TEXT_INFO_EMAIL_ADDRESS . ' ' . $gInfo->emailed_to);
                                if ($redeemed == 'Yes') {
                                    $redeem = xtc_db_fetch_array($redeem_query);
                                    $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_REDEEMED . ' ' . xtc_date_short($redeem['redeem_date']));
                                    $contents[] = array('text' => TEXT_INFO_IP_ADDRESS . ' ' . $redeem['redeem_ip']);
                                    $contents[] = array('text' => TEXT_INFO_CUSTOMERS_ID . ' ' . $redeem['customer_id']);
                                } else {
                                    $contents[] = array('text' => '<br />' . TEXT_INFO_NOT_REDEEMED);
                                }

                                if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
                                    echo '            <td width="25%" class="border" valign="top">' . "\n";

                                    $box = new box;
                                    echo $box->infoBox($heading, $contents);

                                    echo '            </td>' . "\n";
                                }
                                ?>
                            </tr>
                        </table></td>
                </tr>
            </table></td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
