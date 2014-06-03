<?php
/* -----------------------------------------------------------------
 * 	$Id: orders_overview_print.php 420 2013-06-19 18:04:39Z akausch $
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


require ('includes/application_top.php');

$sortierung = 'order by o.orders_id ASC';


$months = array('1' => '01', '2' => '02', '3' => '03', '4' => '04', '5' => '05', '6' => '06', '7' => '07', '8' => '08', '9' => '09', '10' => '10', '11' => '11', '12' => '12');
$monate_namen = array('1' => 'Januar', '2' => 'Februar', '3' => 'M&auml;rz', '4' => 'April', '5' => 'Mai', '6' => 'Juni', '7' => 'Juli', '8' => 'August', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Dezember');

if ($_GET['monat'] == '') {
    $monat = date("m");
    $monat_als_name = $monate_namen[date("m")];
} else {
    $monat = $months[(int) $_GET['monat']];
    $monat_als_name = $monate_namen[(int) $_GET['monat']];
}
if ($_GET['jahr'] == '') {
    $jahr = date("Y");
} else {
    $jahr = (int) $_GET['jahr'];
}

$orders_overview_query_raw = "select o.orders_id, 
							o.orders_status, 
							o.customers_country, 
							o.afterbuy_success, 
							o.afterbuy_id, 
							o.customers_name, 
							o.customers_id, 
							o.customers_cid, 
							o.delivery_country_iso_code_2,  
							o.payment_method, 
							o.date_purchased, 
							o.last_modified, 
							o.currency, 
							o.currency_value, 
							s.orders_status_name, 
							ot.text as order_total from " . TABLE_ORDERS . " o 
							left join " . TABLE_ORDERS_TOTAL . " ot 
							on (o.orders_id = ot.orders_id), 
							" . TABLE_ORDERS_STATUS . " s 
							where o.date_purchased LIKE '" . $jahr . "-" . $monat . "%' 
							and (o.orders_status = s.orders_status_id and s.language_id = '" . $_SESSION['languages_id'] . "' and ot.class = 'ot_total') 
							or (o.orders_status = '0' and ot.class = 'ot_total' and  s.orders_status_id = '1' and s.language_id = '" . $_SESSION['languages_id'] . "') 
							" . $sortierung . "";

$orders_query = xtc_db_query($orders_overview_query_raw);

$gesamtsumme_brutto = xtc_db_fetch_array(xtc_db_query("SELECT sum(ot.value) AS brutto FROM " . TABLE_ORDERS . " o, " . TABLE_ORDERS_TOTAL . " ot WHERE o.date_purchased LIKE '" . $jahr . "-" . $monat . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' "));

$gesamtsumme_tax = xtc_db_fetch_array(xtc_db_query("SELECT sum(ot.value) AS tax FROM " . TABLE_ORDERS . " o, " . TABLE_ORDERS_TOTAL . " ot WHERE o.date_purchased LIKE '" . $jahr . "-" . $monat . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_tax' "));

$gesamtsumme_netto = number_format($gesamtsumme_brutto['brutto'] - $gesamtsumme_tax['tax'], 2);
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
    <head>
        <meta http-equiv="Content-Language" content="de">
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
        <title>Bestell&uuml;bersicht - <?php echo $monat_als_name . '/' . $jahr ?></title>
        <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
        <style type="text/css">
            body {background: #ffffff}
            h2 { font-size: 14px; font-weight: 700; margin: 10px 0 }
            a:focus {outline: none}
            table td { font-family: Verdana, Arial, sans-serif; font-size: 10px;}
            table.overview td a {text-decoration: underline}
            table.overview td.th_head {font-weight: 700; font-size: 12px}
            table.overview td.td_bg_dunkel {background: #e4e4e4}
            table.overview td.td_bg_hell {border-bottom: 1px solid #e4e4e4}
        </style> 
    </head>
    <body onload="window.print()">
        <div id="wrapper">
            <table width="100%">
                <tr>
                    <td width="100%" valign="top">
                        <h2><?php echo 'Bestell&uuml;bersicht - ' . $monat . '/' . $jahr; ?> f&uuml;r <?php echo STORE_NAME; ?></h2>
                        <table cellpadding="4" cellspacing="0" class="dataTable" width="100%" border="0">
                            <thead>
                                <tr>
                                    <td class="th_head" align="center">
                                        <strong>Best-Nr</strong>
                                    </td>
                                    <td class="th_head">
                                        <strong>Kundenname</strong>
                                    </td>
                                    <td class="th_head" align="center">
                                        <strong>Land</strong>
                                    </td>
                                    <td class="th_head" align="center">
                                        <strong>Best-Datum</strong>
                                    </td>
                                    <td class="th_head" align="center">
                                        <strong>Menge</strong>
                                    </td>
                                    <td class="th_head" align="center">
                                        <strong>Re-Nr</strong>
                                    </td>						
                                    <td class="th_head">
                                        <strong>Zahlart</strong>
                                    </td>
                                    <td class="th_head" align="right">
                                        <strong>Brutto</strong>
                                    </td>
                                    <td class="th_head" align="right">
                                        <strong>UST</strong>
                                    </td>
                                    <td class="th_head" align="right">
                                        <strong>Netto</strong>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (xtc_db_num_rows($orders_query) < 0) {
                                    echo '<tr><td colspan="9" align="center" class="main"><br /><strong>Es wurden keine Ums&auml;tze gefunden.</strong><br /><br /></td></tr>';
                                } else {
                                    $i = 1;
                                    while ($orders_overview = xtc_db_fetch_array($orders_query)) {
                                        $pdf_rechnung = xtc_db_fetch_array(xtc_db_query("SELECT order_id, bill_name, pdf_bill_nr  FROM orders_pdf WHERE '" . $orders_overview['orders_id'] . "' = order_id"));
                                        $menge = '';
                                        $menge_query = xtc_db_query("SELECT products_quantity FROM orders_products WHERE orders_id = '" . $orders_overview['orders_id'] . "'");
                                        while ($menge_data = xtc_db_fetch_array($menge_query)) {
                                            $menge += $menge_data['products_quantity'];
                                        }

                                        include (DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $orders_overview['payment_method'] . '.php');
                                        $payment_method = constant(strtoupper('MODULE_PAYMENT_' . $orders_overview['payment_method'] . '_TEXT_TITLE'));
                                        if ($payment_method != '')
                                            $payment_method = $payment_method;
                                        else
                                            $payment_method = 'N/A';

                                        $orders_brutto = xtc_db_fetch_array(xtc_db_query("SELECT text, value FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = '" . $orders_overview['orders_id'] . "' AND class = 'ot_total' "));

                                        $orders_tax = xtc_db_fetch_array(xtc_db_query("SELECT text, value FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = '" . $orders_overview['orders_id'] . "' AND class = 'ot_tax' "));

                                        $orders_netto = number_format($orders_brutto['value'] - $orders_tax['value'], 2);

                                        if ($orders_tax['text'] == '')
                                            $tax = '-';
                                        else
                                            $tax = number_format($orders_tax['value'], 2) . ' &euro;';

                                        echo '<tr>';
                                        echo '<td align="center">' . $orders_overview['orders_id'] . '</td>';
                                        echo '<td>' . $orders_overview['customers_name'] . '</td>';
                                        echo '<td align="center">' . $orders_overview['delivery_country_iso_code_2'] . '</td>';
                                        echo '<td align="center">' . $orders_overview['date_purchased'] . '</td>';
                                        echo '<td align="center">' . $menge . 'x</td>';
                                        echo '<td align="center">' . $pdf_rechnung['pdf_bill_nr'] . '</td>';
                                        echo '<td>' . strip_tags(trim($payment_method)) . '</td>';
                                        echo '<td align="right">' . number_format(strip_tags($orders_brutto['value']), 2) . ' &euro;</td>';
                                        echo '<td align="right">' . $tax . '</td>';
                                        echo '<td align="right"><strong>' . $orders_netto . ' &euro;</strong></td>';
                                        echo '</tr>';

                                        $i++;
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="6" align="right" class="th_head">
                                            <strong>Gesamt:</strong>
                                        </td>
                                        <td align="right" class="th_head">
                                            &nbsp;
                                        </td>						
                                        <td align="right" class="th_head">
                                            <strong><?php echo number_format($gesamtsumme_brutto['brutto'], 2); ?> &euro;</strong>
                                        </td>
                                        <td align="right" class="th_head">
                                            <strong><?php echo number_format($gesamtsumme_tax['tax'], 2); ?> &euro;</strong>
                                        </td>
                                        <td class="th_head" align="right">
                                            <strong><?php echo $gesamtsumme_netto; ?> &euro;</strong>
                                        </td>
                                    </tr>
<?php } ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div> 
    </body>
</html>