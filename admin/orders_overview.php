<?php
/* -----------------------------------------------------------------
 * 	$Id: orders_overview.php 420 2013-06-19 18:04:39Z akausch $
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
require_once (DIR_FS_INC . 'xtc_format_price.inc.php');

if ($_GET['bestell_nr'] == 'up') { // Bestellnummer
    $sortierung = 'ORDER BY o.orders_id ASC';
} elseif ($_GET['bestell_nr'] == 'down') {
    $sortierung = 'ORDER BY o.orders_id DESC';
} elseif ($_GET['name'] == 'up') { // Name
    $sortierung = 'ORDER BY o.customers_name ASC';
} elseif ($_GET['name'] == 'down') {
    $sortierung = 'ORDER BY o.customers_name DESC';
} elseif ($_GET['land'] == 'up') { // Land
    $sortierung = 'ORDER BY o.customers_country ASC';
} elseif ($_GET['land'] == 'down') {
    $sortierung = 'ORDER BY o.customers_country DESC';
} elseif ($_GET['datum'] == 'up') { // Datum
    $sortierung = 'ORDER BY o.date_purchased ASC';
} elseif ($_GET['datum'] == 'down') {
    $sortierung = 'ORDER BY o.date_purchased DESC';
} elseif ($_GET['menge'] == 'up') { // Artikelmenge
    $sortierung = 'ORDER BY o.customers_name ASC';
} elseif ($_GET['menge'] == 'down') {
    $sortierung = 'ORDER BY o.customers_name DESC';
} elseif ($_GET['zahlart'] == 'up') { // Zahlart
    $sortierung = 'ORDER BY o.payment_method ASC';
} elseif ($_GET['zahlart'] == 'down') {
    $sortierung = 'ORDER BY o.payment_method DESC';
} elseif ($_GET['brutto'] == 'up') { // Brutto
    $sortierung = 'ORDER BY o.customers_name ASC';
} elseif ($_GET['brutto'] == 'down') {
    $sortierung = 'ORDER BY o.customers_name DESC';
} elseif ($_GET['tax'] == 'up') { // UST
    $sortierung = 'ORDER BY o.customers_name ASC';
} elseif ($_GET['tax'] == 'down') {
    $sortierung = 'ORDER BY o.customers_name DESC';
} elseif ($_GET['netto'] == 'up') { // Netto
    $sortierung = 'ORDER BY o.customers_name ASC';
} elseif ($_GET['netto'] == 'down') {
    $sortierung = 'ORDER BY o.customers_name DESC';
} elseif ($_GET['versandart'] == 'up') {
    $sortierung = 'ORDER BY o.shipping_method ASC';
} elseif ($_GET['versandart'] == 'down') {
    $sortierung = 'ORDER BY o.shipping_method DESC';
} elseif ($_GET['status'] == 'up') {
    $sortierung = 'ORDER BY o.orders_status ASC';
} elseif ($_GET['status'] == 'down') {
    $sortierung = 'ORDER BY o.orders_status DESC';
} else { // Default
    $sortierung = 'ORDER BY o.orders_id ASC';
}

$months = array('1' => '01', '2' => '02', '3' => '03', '4' => '04', '5' => '05', '6' => '06', '7' => '07', '8' => '08', '9' => '09', '10' => '10', '11' => '11', '12' => '12');

if (($_GET['monat'] == '') || ($_GET['monat'] == '0')) {
    $monat = date("m");
} else {
    $monat = $months[$_GET['monat']];
}
if (($_GET['jahr'] == '') || ($_GET['jahr'] == '0')) {
    $jahr = date("Y");
} else {
    $jahr = (int) $_GET['jahr'];
}

$orders_overview_query_raw = "SELECT o.*, 
									s.orders_status_name, 
									ot.text as order_total 
									FROM " . TABLE_ORDERS . " o 
									LEFT JOIN " . TABLE_ORDERS_TOTAL . " ot ON (o.orders_id = ot.orders_id), 
									" . TABLE_ORDERS_STATUS . " s 
									WHERE o.date_purchased LIKE '" . $jahr . "-" . $monat . "%' 
									AND (o.orders_status = s.orders_status_id AND s.language_id = '" . $_SESSION['languages_id'] . "' AND ot.class = 'ot_total') 
									OR (o.orders_status = '0' AND ot.class = 'ot_total' AND  s.orders_status_id = '1' AND s.language_id = '" . $_SESSION['languages_id'] . "') 
									" . $sortierung . ";";

$orders_query = xtc_db_query($orders_overview_query_raw);
$gesamtsumme_brutto = xtc_db_fetch_array(xtc_db_query("SELECT sum(ot.value) AS brutto FROM " . TABLE_ORDERS . " o, " . TABLE_ORDERS_TOTAL . " ot WHERE o.date_purchased LIKE '" . $jahr . "-" . $monat . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' "));
$gesamtsumme_tax = xtc_db_fetch_array(xtc_db_query("SELECT sum(ot.value) AS tax FROM " . TABLE_ORDERS . " o, " . TABLE_ORDERS_TOTAL . " ot WHERE o.date_purchased LIKE '" . $jahr . "-" . $monat . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_tax' "));
$gesamtsumme_netto = xtc_format_price($gesamtsumme_brutto['brutto'] - $gesamtsumme_tax['tax'], 2);
require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td width="100%" valign="top" class="boxCenter">
            <?php
            $monate_namen = array('1' => 'Januar', '2' => 'Februar', '3' => 'M&auml;rz', '4' => 'April', '5' => 'Mai', '6' => 'Juni', '7' => 'Juli', '8' => 'August', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Dezember');
            echo xtc_draw_form('select', 'orders_overview.php', '', 'get', '');
            $erste_bestellung_query = xtc_db_query("SELECT date_purchased FROM " . TABLE_ORDERS . " ORDER BY date_purchased ASC LIMIT 1");
            if (xtc_db_num_rows($erste_bestellung_query)) {
                if ($_GET['monat'] > 0)
                    $dropdown_monat = '<select name="monat" onchange="javascript:this.form.submit();">';
                else
                    $dropdown_monat = '<select name="monat" onchange="javascript:this.form.submit();"><option selected="" value="0">w&auml;hlen Sie einen Monat</option>';

                for ($monate = 1; $monate <= 12; $monate++)
                    $dropdown_monat .= '<option value="' . $monate . '"' . ($monate == (int) $_GET['monat'] ? 'selected=""' : '') . '>' . $monate_namen[$monate] . '</option>';

                $dropdown_monat .= '</select>';

                #########################

                if ($_GET['jahr'] > 0)
                    $dropdown_jahr = '<select name="jahr" onchange="javascript:this.form.submit();">';
                else
                    $dropdown_jahr = '<select name="jahr" onchange="javascript:this.form.submit();"><option selected="" value="0">w&auml;hlen Sie einen Jahr</option>';

                $erste_bestellung = xtc_db_fetch_array($erste_bestellung_query);
                $erstes_jahr = substr($erste_bestellung['date_purchased'], 0, 4);
                $dieses_jahr = date('Y');
                for ($jahre = $erstes_jahr; $jahre <= $dieses_jahr; $jahre++)
                    $dropdown_jahr .= '<option value="' . $jahre . '"' . ($jahre == (int) $_GET['jahr'] ? 'selected=""' : '') . '>' . $jahre . '</option>';

                $dropdown_jahr .= '</select>';

                $print_button = '<input class="button" type="button" value="Drucken" onclick="window.open(\'orders_overview_print.php?action=print&monat=' . $monat . '&jahr=' . $jahr . '\', \'popup\', \'toolbar=0,scrollbars=yes, width=740, height=680\')" href="javascript:void();" />';
            }
            ?>
            <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="pageHeading"><?php echo 'Bestell&uuml;bersicht - ' . $monat . '/' . $jahr; ?></td>
                </tr>
            </table>
            <table cellpadding="5" cellspacing="0" width="100%">
                <tr>
                    <td width="1">
                        <?php echo $dropdown_monat; ?>
                    </td>
                    <td width="1">
                        <?php echo $dropdown_jahr; ?>
                    </td>
                    <td>
                        <?php
                        $value = $monat . '/' . $jahr . ' Drucken';
                        ?>
                        <?php echo $print_button; ?>
                    </td>
                </tr>
            </table>
            </form>
            <?php echo xtc_draw_form('sort', 'orders_overview.php', '', 'get', ''); ?>
            <input type="hidden" value="<?php echo $_GET['jahr']; ?>" name="jahr" />
            <input type="hidden" value="<?php echo $_GET['monat']; ?>" name="monat" />
            <table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                <tr class="dataTableHeadingRow"><?php
            if ($_GET['jahr'])
                $get_jahr = '&jahr=' . $_GET['jahr'];
            if ($_GET['monat'])
                $get_monat = '&monat=' . $_GET['monat'];
            ?>
                    <th class="dataTableHeadingContent" align="center">
                        Best-Nr
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?bestell_nr=up' . $get_monat . $get_jahr; ?>">
                            <img src="images/up.gif" alt="" title="" />
                        </a>
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?bestell_nr=down' . $get_monat . $get_jahr; ?>">
                            <img src="images/down.gif" alt="" title="" />
                        </a>
                    </th>
                    <th class="dataTableHeadingContent">
                        Kundenname
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?name=up' . $get_monat . $get_jahr; ?>">
                            <img src="images/up.gif" alt="" title="" />
                        </a>
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?name=down' . $get_monat . $get_jahr; ?>">
                            <img src="images/down.gif" alt="" title="" />
                        </a>
                    </th>
                    <th class="dataTableHeadingContent" align="center">
                        Land
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?land=up' . $get_monat . $get_jahr; ?>">
                            <img src="images/up.gif" alt="" title="" />
                        </a>
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?land=down' . $get_monat . $get_jahr; ?>">
                            <img src="images/down.gif" alt="" title="" />
                        </a>
                    </th>
                    <th class="dataTableHeadingContent" align="center">
                        Best-Datum
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?datum=up' . $get_monat . $get_jahr; ?>">
                            <img src="images/up.gif" alt="" title="" />
                        </a>
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?datum=down' . $get_monat . $get_jahr; ?>">
                            <img src="images/down.gif" alt="" title="" />
                        </a>
                    </th>
                    <th class="dataTableHeadingContent" align="center">
                        Artikel ?
                    </th>
                    <th class="dataTableHeadingContent" align="center">
                        PDF
                    </th>						
                    <th class="dataTableHeadingContent" align="center">
                        Zahlart
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?zahlart=up' . $get_monat . $get_jahr; ?>">
                            <img src="images/up.gif" alt="" title="" />
                        </a>
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?zahlart=down' . $get_monat . $get_jahr; ?>">
                            <img src="images/down.gif" alt="" title="" />
                        </a>
                    </th>
                    <th class="dataTableHeadingContent" align="center">
                        Versandart
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?versandart=up' . $get_monat . $get_jahr; ?>">
                            <img src="images/up.gif" alt="" title="" />
                        </a>
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?versandart=down' . $get_monat . $get_jahr; ?>">
                            <img src="images/down.gif" alt="" title="" />
                        </a>
                    </th>
                    <th class="dataTableHeadingContent" align="center">
                        Status
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?status=up' . $get_monat . $get_jahr; ?>">
                            <img src="images/up.gif" alt="" title="" />
                        </a>
                        <a href="<?php echo basename($_SERVER['SCRIPT_NAME']) . '?status=down' . $get_monat . $get_jahr; ?>">
                            <img src="images/down.gif" alt="" title="" />
                        </a>
                    </th>
                    <th class="dataTableHeadingContent" align="right">
                        Brutto
                    </th>
                    <th class="dataTableHeadingContent" align="right">
                        davon UST
                    </th>
                    <th class="dataTableHeadingContent" align="right">
                        Netto
                    </th>
                </tr>
                <tbody>
                    <?php
                    if (xtc_db_num_rows($orders_query) < 0)
                        echo '<tr><td colspan="9" align="center" class="main"><br /><strong>Es wurden keine Ums&auml;tze gefunden.</strong><br /><br /></td></tr>';
                    else {
                        $i = 1;
                        while ($orders_overview = xtc_db_fetch_array($orders_query)) {
                            $pdf_rechnung = xtc_db_fetch_array(xtc_db_query("SELECT order_id, bill_name, pdf_bill_nr  FROM orders_pdf WHERE '" . $orders_overview['orders_id'] . "' = order_id"));

                            $menge_query = xtc_db_query("SELECT products_id FROM orders_products WHERE orders_id = '" . $orders_overview['orders_id'] . "'");
                            $menge = xtc_db_num_rows($menge_query);

                            include (DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $orders_overview['payment_method'] . '.php');
                            $payment_method = constant(strtoupper('MODULE_PAYMENT_' . $orders_overview['payment_method'] . '_TEXT_TITLE'));

                            $order_status = xtc_db_fetch_array(xtc_db_query("SELECT orders_status_name FROM orders_status WHERE orders_status_id = '" . $orders_overview['orders_status'] . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "'"));

                            $orders_brutto = xtc_db_fetch_array(xtc_db_query("SELECT text, value FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = '" . $orders_overview['orders_id'] . "' AND class = 'ot_total' "));

                            $orders_tax = xtc_db_fetch_array(xtc_db_query("SELECT text, value FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = '" . $orders_overview['orders_id'] . "' AND class = 'ot_tax' "));

                            $orders_netto = xtc_format_price($orders_brutto['value'] - $orders_tax['value'], $price_special = 1, $calculate_currencies = false);

                            if ($orders_tax['text'] == '')
                                $tax = '-';
                            else
                                $tax = xtc_format_price($orders_tax['value'], $price_special = 1, $calculate_currencies = false);



                            echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '">';
                            echo '<td align="center" class="smallText"><a href="' . xtc_href_link('orders.php', 'oID=' . $orders_overview['orders_id'] . '&action=edit') . '">' . $orders_overview['orders_id'] . '</a></td>';
                            echo '<td><a href="' . xtc_href_link('customers.php', 'cID=' . $orders_overview['customers_id'] . '&action=edit') . '">' . $orders_overview['customers_name'] . '</a></td>';
                            echo '<td align="center">' . $orders_overview['delivery_country_iso_code_2'] . '</td>';
                            echo '<td align="center">' . $orders_overview['date_purchased'] . '</td>';
                            echo '<td align="center">' . $menge . 'x</td>';
                            echo '<td align="center"><a target="_blank" href="/admin/' . $pdf_rechnung['bill_name'] . '">' . $pdf_rechnung['pdf_bill_nr'] . '</a></td>';
                            echo '<td align="center">' . strip_tags($payment_method) . '</td>';
                            echo '<td align="center">' . $order_status['orders_status_name'] . '</td>';
                            echo '<td align="center">' . $orders_overview['shipping_method'] . '</td>';
                            echo '<td align="right">' . xtc_format_price(strip_tags($orders_brutto['value']), $price_special = 1, $calculate_currencies = false) . '</td>';
                            echo '<td align="right">' . $tax . '</td>';
                            echo '<td align="right"><strong>' . $orders_netto . '</strong></td>';
                            echo '</tr>';

                            $i++;
                        }
                        ?>
                        <tr>
                            <td colspan="8" align="right"  class="main">
                                Gesamt:
                            </td>
                            <td align="right"  class="main">
                                &nbsp;
                            </td>								
                            <td align="right"  class="main">
                                <strong><nobr><?php echo xtc_format_price($gesamtsumme_brutto['brutto'], $price_special = 1, $calculate_currencies = false); ?></nobr></strong>
                            </td>
                            <td align="right"  class="main">
                                <strong><nobr><?php echo xtc_format_price($gesamtsumme_tax['tax'], $price_special = 1, $calculate_currencies = false); ?></nobr></strong>
                            </td>
                            <td  class="main" align="right">
                                <strong><nobr><?php echo xtc_format_price($gesamtsumme_netto, $price_special = 1, $calculate_currencies = false); ?></nobr></strong>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            </form><br />

        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
