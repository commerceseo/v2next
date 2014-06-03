<?php
/* -----------------------------------------------------------------
 * 	$Id: stats_recover_cart_sales.php 420 2013-06-19 18:04:39Z akausch $
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

function xtc_date_order_stat($raw_date) {
    if ($raw_date == '')
        return false;
    $year = substr($raw_date, 2, 2);
    $month = (int) substr($raw_date, 4, 2);
    $day = (int) substr($raw_date, 6, 2);
    return date(DATE_FORMAT, mktime(0, 0, 0, $month, $day, $year));
}

function seadate($day) {
    $ts = date("U");
    $rawtime = strtotime("-" . $day . " days", $ts);
    $ndate = date("Ymd", $rawtime);
    return $ndate;
}

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="6">
                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <tr>
                                <td class="pageHeading" align="left"><?php echo HEADING_TITLE; ?></td>
                                <td class="pageHeading" align="right">
                                    <?php
                                    $tdate = isset($_POST['tdate']) ? $_POST['tdate'] : '';
                                    if ($tdate == '')
                                        $tdate = RCS_REPORT_DAYS;
                                    $ndate = seadate($tdate);
                                    ?>
                                    <form method=post action=<?php echo $PHP_SELF; ?> >
                                        <table align="right" width="100%">
                                            <tr class="dataTableContent" align="right">
                                                <td nowrap>
                                                    <?php echo DAYS_FIELD_PREFIX; ?>
                                                    <input type=text size=4 width=4 value=<?php echo $tdate; ?> name=tdate><?php echo DAYS_FIELD_POSTFIX; ?>
                                                    <input class="button" type=submit value="<?php echo DAYS_FIELD_BUTTON; ?>">
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php
// Init vars
                $custknt = 0;
                $total_recovered = 0;
                $custlist = '';

// Query database for abandoned carts within our timeframe
                $conquery = xtc_db_query("select * from " . TABLE_SCART . " where dateadded >= '" . $ndate . "' order by dateadded DESC");
                $rc_cnt = xtc_db_num_rows($conquery);

// Loop though each one and process it
                for ($i = 0; $i < $rc_cnt; $i++) {
                    $inrec = xtc_db_fetch_array($conquery);
                    $cid = $inrec['customers_id'];
                    // we have to get the customer data in order to better locate matching orders
                    $query1 = xtc_db_query("select c.customers_firstname, c.customers_lastname, c.customers_email_address from " . TABLE_CUSTOMERS . " c where c.customers_id ='" . $cid . "'");
                    $crec = xtc_db_fetch_array($query1);

                    // Query DB for the FIRST order that matches this customer ID and came after the abandoned cart
                    $orders_query_raw = "select o.orders_id, o.customers_id, o.date_purchased, s.orders_status_name, ot.text as order_total, ot.value from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where (o.customers_id = " . (int) $cid . ' OR o.customers_email_address like "' . $crec['customers_email_address'] . '" OR o.customers_name like "' . $crec['customers_firstname'] . ' ' . $crec['customers_lastname'] . '") and o.orders_status >= ' . RCS_PENDING_SALE_STATUS . ' and s.orders_status_id = o.orders_status and o.date_purchased >= "' . $inrec['dateadded'] . '" and ot.class = "ot_total"';
                    $orders_query = xtc_db_query($orders_query_raw);
                    $orders = xtc_db_fetch_array($orders_query);

                    // If we got a match, create the table entry to display the information
                    if ($orders) {
                        $custknt++;
                        $total_recovered += $orders['value'];
                        $custknt % 2 ? $class = RCS_REPORT_EVEN_STYLE : $class = RCS_REPORT_ODD_STYLE;
                        $custlist .= "<tr class=" . $class . ">" .
                                "<td class=datatablecontent align=right>" . $inrec['scartid'] . "</td>" .
                                "<td>&nbsp;</td>" .
                                "<td class=datatablecontent align=center>" . xtc_date_order_stat($inrec['dateadded']) . "</td>" .
                                "<td>&nbsp;</td>" .
                                "<td class=datatablecontent><a href='" . xtc_href_link(FILENAME_CUSTOMERS, 'search=' . $crec['customers_lastname'], 'NONSSL') . "'>" . $crec['customers_firstname'] . " " . $crec['customers_lastname'] . "</a></td>" .
                                "<td class=datatablecontent>" . xtc_date_short($orders['date_purchased']) . "</td>" .
                                "<td class=datatablecontent align=center>" . $orders['orders_status_name'] . "</td>" .
                                "<td class=datatablecontent align=right>" . strip_tags($orders['order_total']) . "</td>" .
                                "<td>&nbsp;</td>" .
                                "</tr>";
                    }
                }
                $cline = "<tr><td height=\"15\" colspan=8> </td></tr>" .
                        "<tr>" .
                        "<td align=right colspan=3 class=main><b>" . TOTAL_RECORDS . "</b></td>" .
                        "<td>&nbsp;</td>" .
                        "<td align=left colspan=5 class=main>" . $rc_cnt . "</td>" .
                        "</tr>" .
                        "<tr>" .
                        "<td align=right colspan=3 class=main><b>" . TOTAL_SALES . "</b></td>" .
                        "<td>&nbsp;</td>" .
                        "<td align=left colspan=5 class=main>" . $custknt . TOTAL_SALES_EXPLANATION . " </td>" .
                        "</tr>" .
                        "<tr><td height=\"12\" colspan=6> </td></tr>";
                echo $cline;
                ?>
                <tr class="dataTableHeadingRow">	<!-- Header -->
                    <th width="7%" class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_SCART_ID ?></th>
                    <th width="1%" class="dataTableHeadingContent">&nbsp;</th>
                    <th width="10%" class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_SCART_DATE ?></th>
                    <th width="1%" class="dataTableHeadingContent">&nbsp;</th>
                    <th width="50%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMER ?></th>
                    <th width="10%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDER_DATE ?></th>
                    <th width="10%" class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ORDER_STATUS ?></th>
                    <th width="10%" class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ORDER_AMOUNT ?></th>
                    <th width="1%" class="dataTableHeadingContent">&nbsp;</th>
                </tr>
                <?php
                echo $custlist; // BODY: <tr> sections with recovered cart data
                ?>
                <tr>
                    <td colspan=9 valign="bottom"><hr width="100%" size="1" color="#800000" noshade></td>
                </tr>
                <tr class="main">
                    <td align="right" valign="center" colspan=4 class="main"><b><?php echo TOTAL_RECOVERED ?>&nbsp;</b></font></td>
                    <td align=left colspan=3 class="main"><b><?php echo $rc_cnt ? xtc_round(($custknt / $rc_cnt) * 100, 2) : 0 ?>%</b></font></td>
                    <td class="main" align="right"><b><?php echo $currencies->format(xtc_round($total_recovered, 2)) ?></b></font></td>
                    <td class="main">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
