<?php
/* -----------------------------------------------------------------
 * 	$Id: gv_queue.php 420 2013-06-19 18:04:39Z akausch $
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

require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.phpmailer.php');
require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');

// initiate template engine for mail
$smarty = new Smarty;

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

if ($_GET['action'] == 'confirmrelease' && isset($_GET['gid'])) {
    $gv_query = xtc_db_query("select release_flag from " . TABLE_COUPON_GV_QUEUE . " where unique_id='" . $_GET['gid'] . "'");
    $gv_result = xtc_db_fetch_array($gv_query);
    if ($gv_result['release_flag'] == 'N') {
        $gv_query = xtc_db_query("select customer_id, amount from " . TABLE_COUPON_GV_QUEUE . " where unique_id='" . $_GET['gid'] . "'");
        if ($gv_resulta = xtc_db_fetch_array($gv_query)) {
            $gv_amount = $gv_resulta['amount'];
            //Let's build a message object using the email class
            $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . $gv_resulta['customer_id'] . "'");
            $mail = xtc_db_fetch_array($mail_query);


            // assign language to template for caching
            $smarty->assign('language', $_SESSION['language']);
            $smarty->caching = false;

            // set dirs manual
            $smarty->template_dir = DIR_FS_CATALOG . 'templates';
            $smarty->compile_dir = DIR_FS_CATALOG . 'templates_c';
            $smarty->config_dir = DIR_FS_CATALOG . 'lang';

            $smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
            $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

            $smarty->assign('AMMOUNT', $currencies->format($gv_amount));

            require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
            $html_mail = $smarty->fetch('html:gift_accepted');
            $html_mail .= $signatur_html;
            $txt_mail = $smarty->fetch('txt:gift_accepted');
            $txt_mail .= $signatur_text;
            require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
            $mail_data = cseo_get_mail_data('gift_accepted');

            $gv_mail_name = str_replace('{$shop}', STORE_NAME, $mail_data['EMAIL_ADDRESS_NAME']);

            if ($subject == '')
                $subject = $mail_data['EMAIL_SUBJECT'];

            xtc_php_mail($mail_data['EMAIL_ADDRESS'], $gv_mail_name, $mail['customers_email_address'], $mail['customers_firstname'] . ' ' . $mail['customers_lastname'], '', $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], '', '', $subject, $html_mail, $txt_mail);
            // $html_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$_SESSION['language'].'/gift_accepted.html');
            // $txt_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$_SESSION['language'].'/gift_accepted.txt');
            // xtc_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME,$mail['customers_email_address'] , $mail['customers_firstname'] . ' ' . $mail['customers_lastname'] , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_BILLING_SUBJECT, $html_mail , $txt_mail);


            $gv_amount = $gv_resulta['amount'];
            $gv_query = xtc_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id='" . $gv_resulta['customer_id'] . "'");
            $customer_gv = false;
            $total_gv_amount = 0;
            if ($gv_result = xtc_db_fetch_array($gv_query)) {
                $total_gv_amount = $gv_result['amount'];
                $customer_gv = true;
            }
            $total_gv_amount = $total_gv_amount + $gv_amount;
            if ($customer_gv) {
                $gv_update = xtc_db_query("update " . TABLE_COUPON_GV_CUSTOMER . " set amount='" . $total_gv_amount . "' where customer_id='" . $gv_resulta['customer_id'] . "'");
            } else {
                $gv_insert = xtc_db_query("insert into " . TABLE_COUPON_GV_CUSTOMER . " (customer_id, amount) values ('" . $gv_resulta['customer_id'] . "','" . $total_gv_amount . "')");
            }
            $gv_update = xtc_db_query("update " . TABLE_COUPON_GV_QUEUE . " set release_flag='Y' where unique_id='" . $_GET['gid'] . "'");
        }
    }
}
require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td width="100%">
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading">
<?php echo HEADING_TITLE; ?>
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
                                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                        <tr class="dataTableHeadingRow">
                                            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS; ?></td>
                                            <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ORDERS_ID; ?></td>
                                            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_VOUCHER_VALUE; ?></td>
                                            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></td>
                                            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                                        </tr>
<?php
$gv_query_raw = "select c.customers_firstname, c.customers_lastname, gv.unique_id, gv.date_created, gv.amount, gv.order_id from " . TABLE_CUSTOMERS . " c, " . TABLE_COUPON_GV_QUEUE . " gv where (gv.customer_id = c.customers_id and gv.release_flag = 'N')";
$gv_split = new splitPageResults($_GET['page'], '20', $gv_query_raw, $gv_query_numrows);
$gv_query = xtc_db_query($gv_query_raw);
while ($gv_list = xtc_db_fetch_array($gv_query)) {
    $rows++;
    if (((!$_GET['gid']) || (@$_GET['gid'] == $gv_list['unique_id'])) && (!$gInfo)) {
        $gInfo = new objectInfo($gv_list);
    }
    if ((is_object($gInfo)) && ($gv_list['unique_id'] == $gInfo->unique_id)) {
        echo '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link('gv_queue.php', xtc_get_all_get_params(array('gid', 'action')) . 'gid=' . $gInfo->unique_id . '&action=edit') . '\'">' . "\n";
    } else {
        echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link('gv_queue.php', xtc_get_all_get_params(array('gid', 'action')) . 'gid=' . $gv_list['unique_id']) . '\'">' . "\n";
    }
    ?>
                                            <td class="dataTableContent"><?php echo $gv_list['customers_firstname'] . ' ' . $gv_list['customers_lastname']; ?></td>
                                            <td class="dataTableContent" align="center"><?php echo $gv_list['order_id']; ?></td>
                                            <td class="dataTableContent" align="right"><?php echo $currencies->format($gv_list['amount']); ?></td>
                                            <td class="dataTableContent" align="right"><?php echo xtc_datetime_short($gv_list['date_created']); ?></td>
                                            <td class="dataTableContent" align="right">
                                            <?php
                                            if ((is_object($gInfo)) && ($gv_list['unique_id'] == $gInfo->unique_id)) {
                                                echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif');
                                            } else {
                                                echo '<a href="' . xtc_href_link(FILENAME_GV_QUEUE, 'page=' . $_GET['page'] . '&gid=' . $gv_list['unique_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                                            }
                                            ?>&nbsp;
                                            </td>
                                </tr>
<?php } ?>
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
                            switch ($_GET['action']) {
                                case 'release':
                                    $heading[] = array('text' => '[' . $gInfo->unique_id . '] ' . xtc_datetime_short($gInfo->date_created) . ' ' . $currencies->format($gInfo->amount));

                                    $contents[] = array('align' => 'center', 'text' => '<a class="button" style="font-color: red;" onClick="this.blur();" href="' . xtc_href_link('gv_queue.php', 'action=confirmrelease&gid=' . $gInfo->unique_id, 'NONSSL') . '">' . BUTTON_CONFIRM . '</a> <a class="button" onClick="this.blur();" href="' . xtc_href_link('gv_queue.php', 'action=cancel&gid=' . $gInfo->unique_id, 'NONSSL') . '">' . BUTTON_CANCEL . '</a>');
                                    break;
                                default:
                                    $heading[] = array('text' => '[' . $gInfo->unique_id . '] ' . xtc_datetime_short($gInfo->date_created) . ' ' . $currencies->format($gInfo->amount));

                                    $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link('gv_queue.php', 'action=release&gid=' . $gInfo->unique_id, 'NONSSL') . '">' . BUTTON_RELEASE . '</a>');
                                    break;
                            }

                            if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
                                echo '            <td width="25%" valign="top">' . "\n";

                                $box = new box;
                                echo $box->infoBox($heading, $contents);

                                echo '            </td>' . "\n";
                            }
                            ?>
                </tr>
            </table>
        </td>
    </tr>
</table>
</td>
</tr>
</table>
                    <?php
                    require(DIR_WS_INCLUDES . 'footer.php');
                    require(DIR_WS_INCLUDES . 'application_bottom.php');

                    