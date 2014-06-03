<?php
/* -----------------------------------------------------------------
 * 	$Id: price_change.php 1034 2014-05-11 17:38:44Z akausch $
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
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$shipping_statuses = array();
$shipping_statuses = xtc_get_shipping_status();
if ($action == 'save' && is_array($_POST['product'])) {
// print_r($_POST);die;

    foreach ($_POST['product'] AS $pid => $price) {
        if ($price != '') {
            $price = str_replace(',', '.', $price);
            if ($_POST['netto'] == 0) {
                $tax_rate = xtc_db_fetch_array(xtc_db_query(" SELECT products_tax_class_id FROM products WHERE products_id = '" . (int) $pid . "' "));
                $price = round(($price / ((xtc_get_tax_rate($tax_rate['products_tax_class_id']) / 100) + 1 )), PRICE_PRECISION);
            }
            xtc_db_query("UPDATE products SET products_price = '" . $price . "' WHERE products_id = '" . (int) $pid . "'");
        }
    }
    foreach ($_POST['products_quantity'] AS $pid => $products_quantity) {
		if ($products_quantity != '') {
			xtc_db_query("UPDATE products SET products_quantity = '" . $products_quantity . "' WHERE products_id = '" . (int) $pid . "'");
		}
    }
    foreach ($_POST['products_ean'] AS $pid => $products_ean) {
		if ($products_ean != '') {
			xtc_db_query("UPDATE products SET products_ean = '" . $products_ean . "' WHERE products_id = '" . (int) $pid . "'");
		}
    }
    foreach ($_POST['products_model'] AS $pid => $products_model) {
		if ($products_model != '') {
			xtc_db_query("UPDATE products SET products_model = '" . $products_model . "' WHERE products_id = '" . (int) $pid . "'");
		}
    }
    foreach ($_POST['products_shippingtime'] AS $pid => $products_shippingtime) {
		if ($products_shippingtime != '') {
			xtc_db_query("UPDATE products SET products_shippingtime = '" . $products_shippingtime . "' WHERE products_id = '" . (int) $pid . "'");
		}
    }
    xtc_redirect('price_change.php' . ($_GET['page'] != '' ? '?page=' . $_GET['page'] : '') . ($_GET['anzahl'] != '' ? '&anzahl=' . $_GET['anzahl'] : ''));
}
require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="3">
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td colspan="3" class="pageHeading">Schnelle Artikelpreis &Auml;nderung</td>
                            </tr>
                        </table>
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top">Geben Sie bei den gew&uuml;nschten Produkten den neuen Preis an. Auch gerne mehrere gleichzeitig. Danach am Ende der Seite die Art ausw&auml;hlen und abspeichern.</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="3">
                        <form method="POST" action="price_change.php?action=save<?php if ($_GET['page'] != '') echo '&page=' . $_GET['page']; ?><?php if ($_GET['anzahl'] != '') echo '&anzahl=' . $_GET['anzahl']; ?>">
                            <table width="100%" class="dataTable" cellspacing="0" cellpadding="0">
                                <tr class="dataTableHeadingRow">
                                    <td class="dataTableHeadingContent">ID</td>
                                    <td class="dataTableHeadingContent">Name</td>
                                    <td class="dataTableHeadingContent">Art.-Nr.</td>
                                    <td class="dataTableHeadingContent">alter Preis (netto)</td>
                                    <td class="dataTableHeadingContent">alter Preis (brutto)</td>
                                    <td class="dataTableHeadingContent">neuer Preis</td>
                                    <td class="dataTableHeadingContent">Bestand</td>
                                    <td class="dataTableHeadingContent">Model</td>
                                    <td class="dataTableHeadingContent">EAN</td>
                                    <td class="dataTableHeadingContent">Lieferzeit</td>
                                </tr>
                                <?php
                                $products_query_raw = " SELECT p.products_id, p.products_quantity, p.products_model, p.products_price, p.products_ean, p.products_tax_class_id, p.products_shippingtime, pd.products_name FROM products p, products_description pd WHERE pd.products_id = p.products_id AND pd.language_id = '" . $_SESSION['languages_id'] . "' ORDER BY p.products_model, pd.products_name ASC";
                                $products_split = new splitPageResults($_GET['page'], ($_GET['anzahl'] != '' ? $_GET['anzahl'] : '50'), $products_query_raw, $products_query_numrows);
                                $products_query = xtc_db_query($products_query_raw);
                                while ($products_data = xtc_db_fetch_array($products_query)) {
                                    echo '<tr class="dataTableRow">';
                                    echo '<td class="categories_view_data">' . $products_data['products_id'] . '</td>';
                                    echo '<td class="categories_view_data" align="left" style="text-align:left">' . $products_data['products_name'] . '</td>';
                                    echo '<td class="categories_view_data" align="left" style="text-align:left"><a href="categories.php?subsite=products&pID='.$products_data['products_id'].'&action=new_product">'.$products_data['products_model'].'</a></td>';	
                                    echo '<td class="categories_view_data" style="text-align:left">' . format_price($products_data['products_price'], 1, $_SESSION['currency'], 0, 0) . '</td>';
                                    echo '<td class="categories_view_data" style="text-align:left">' . format_price(round(($products_data['products_price'] * ((xtc_get_tax_rate($products_data['products_tax_class_id']) / 100) + 1)), PRICE_PRECISION), 1, $_SESSION['currency'], 0, 0) . '</td>';
                                    echo '<td class="categories_view_data" style="text-align:left">' . xtc_draw_input_field('product[' . $products_data['products_id'] . ']', '', 'size="12"') . '</td>';
                                    echo '<td class="categories_view_data" style="text-align:left">' . xtc_draw_input_field('products_quantity[' . $products_data['products_id'] . ']', $products_data['products_quantity'] == '' ? '' : $products_data['products_quantity'], 'size="12"') . '</td>';
                                    echo '<td class="categories_view_data" style="text-align:left">' . xtc_draw_input_field('products_model[' . $products_data['products_id'] . ']',  $products_data['products_model'] == '' ? '' : $products_data['products_model'], 'size="12"') . '</td>';
                                    echo '<td class="categories_view_data" style="text-align:left">' . xtc_draw_input_field('products_ean[' . $products_data['products_id'] . ']', $products_data['products_ean'] == '' ? '' : $products_data['products_ean'], 'size="12"') . '</td>';
                                    echo '<td class="categories_view_data" style="text-align:left">' . xtc_draw_pull_down_menu('products_shippingtime[' . $products_data['products_id'] . ']', $shipping_statuses, $products_data['products_shippingtime'] == '' ? (int) (DEFAULT_SHIPPING_STATUS_ID) : $products_data['products_shippingtime']) . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                                <tr>
                                    <td colspan="5" align="right" class="main"> <br />
                                        <table width="250px">
                                            <tr>
                                                <td class="main">
                                                    <strong>Art der Preiseingabe</strong><br>
                                                    <?php
                                                    if (PRICE_IS_BRUTTO == 'true') {
                                                        ?>
                                                        <input type="radio" name="netto" value="1"/> Netto <br />
                                                        <input type="radio" name="netto" value="0" checked="checked" /> Brutto
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <input type="radio" name="netto" value="1" checked="checked"/> Netto <br />
                                                        <input type="radio" name="netto" value="0" /> Brutto
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td class="main" valign="bottom" align="right">
                                                    <input type="submit" class="button" value="speichern" />
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="smallText" valign="top" width="33.3%"><?php echo $products_split->display_count($products_query_numrows, ($_GET['anzahl'] != '' ? $_GET['anzahl'] : '50'), $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                    <td class="smallText" align="center" width="33.3%"><?php echo $products_split->display_links($products_query_numrows, ($_GET['anzahl'] != '' ? $_GET['anzahl'] : '50'), MAX_DISPLAY_PAGE_LINKS, $_GET['page'], xtc_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
                    <td class="smallText" align="right" width="33.3%">
                        <?php
                        $products_per_page = '<form name="anzahl" action="' . $_SERVER['REQUEST_URI'] . '" method="GET">' . "\n";
                        if ($_GET['page'] != '') {
                            $products_per_page .= xtc_draw_hidden_field('page', $_GET['page']) . "\n";
						}
						if ($_GET['anzahl'] != '') {
                            xtc_draw_hidden_field('anzahl', $_GET['anzahl']);
						}
                        $products_per_page_options = array();
                        $products_per_page_options[] = array('id' => '10', 'text' => '10');
                        $products_per_page_options[] = array('id' => '20', 'text' => '20');
                        $products_per_page_options[] = array('id' => '50', 'text' => '50');
                        $products_per_page_options[] = array('id' => '100', 'text' => '100');
                        $products_per_page_options[] = array('id' => '1000', 'text' => '1000');
                        $products_per_page_options[] = array('id' => '10000', 'text' => 'alle');

                        $products_per_page .= xtc_draw_pull_down_menu('anzahl', $products_per_page_options, ($_GET['anzahl'] != '' ? $_GET['anzahl'] : '50'), 'onchange="this.form.submit()"') . "\n";

                        $products_per_page .= '</form>' . "\n";
                        ?>
                        Produkte pro Seite: <?php echo $products_per_page; ?>
                    </td>
                </tr>
            </table></td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
