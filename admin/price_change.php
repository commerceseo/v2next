<?php
/* -----------------------------------------------------------------
 * 	$Id: price_change.php 1323 2014-12-17 17:50:47Z akausch $
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
$orderlistingnum = ADMIN_DEFAULT_LISTING_NUM;
$shipping_statuses = array();
$shipping_statuses = xtc_get_shipping_status();

function getStrSqlAttributes($products_id) {
    $listing_sql = "SELECT
											pa.*,
											po.products_options_id,
											po.products_options_name,
											po.language_id,
											pov.products_options_values_id,
											pov.products_options_values_name
										FROM 	" . TABLE_PRODUCTS_ATTRIBUTES . " pa,
												" . TABLE_PRODUCTS_OPTIONS . " po,
												" . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
										WHERE pa.products_id = " . $products_id . "
											AND po.language_id = '" . (int) $_SESSION['languages_id'] . "'
											AND pov.language_id = '" . (int) $_SESSION['languages_id'] . "'
											AND pa.options_values_id = pov.products_options_values_id
											AND pa.options_id = po.products_options_id
										ORDER BY po.products_options_name ASC, pov.products_options_values_name ASC";
    return $listing_sql;
}

if ($action == 'save' && is_array($_POST['product'])) {
// print_r($_POST);die;

    foreach ($_POST['product'] AS $pid => $price) {
        if ($price != '') {
            $price = str_replace(',', '.', $price);
            if (PRICE_IS_BRUTTO == 'true') {
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
    foreach ($_POST['attributes_shippingtime'] AS $pid => $attributes_shippingtime) {
        if ($products_shippingtime != '') {
            xtc_db_query("UPDATE products_attributes SET attributes_shippingtime = '" . $attributes_shippingtime . "' WHERE products_attributes_id = '" . (int) $pid . "'");
        }
    }
    foreach ($_POST['attributes_stock'] AS $pid => $attributes_stock) {
        if ($products_shippingtime != '') {
            xtc_db_query("UPDATE products_attributes SET attributes_stock = '" . $attributes_stock . "' WHERE products_attributes_id = '" . (int) $pid . "'");
        }
    }
    foreach ($_POST['attributes_model'] AS $pid => $attributes_model) {
        if ($products_shippingtime != '') {
            xtc_db_query("UPDATE products_attributes SET attributes_model = '" . $attributes_model . "' WHERE products_attributes_id = '" . (int) $pid . "'");
        }
    }
    foreach ($_POST['attributes_ean'] AS $pid => $attributes_ean) {
        if ($products_shippingtime != '') {
            xtc_db_query("UPDATE products_attributes SET attributes_ean = '" . $attributes_ean . "' WHERE products_attributes_id = '" . (int) $pid . "'");
        }
    }
    foreach ($_POST['options_values_price'] AS $pid => $options_values_price) {
        if ($options_values_price != '') {
            $options_values_price = str_replace(',', '.', $options_values_price);
            if (PRICE_IS_BRUTTO == 'true') {
                $option_tax_rate = xtc_db_fetch_array(xtc_db_query("SELECT products_id FROM products_attributes WHERE products_attributes_id = '" . (int) $pid . "' LIMIT 1;"));
                $tax_rate = xtc_db_fetch_array(xtc_db_query("SELECT products_tax_class_id FROM products WHERE products_id = '" . (int) $option_tax_rate['products_id'] . "' LIMIT 1;"));
                
				
				
				$options_values_price = round(($options_values_price / ((xtc_get_tax_rate($tax_rate['products_tax_class_id']) / 100) + 1 )), PRICE_PRECISION);
            }
            xtc_db_query("UPDATE products_attributes SET options_values_price = '" . $options_values_price . "' WHERE products_attributes_id = '" . (int) $pid . "'");
        }
    }
    xtc_redirect('price_change.php' . ($_GET['page'] != '' ? '?page=' . $_GET['page'] : '') . ($_GET['anzahl'] != '' ? '&anzahl=' . $_GET['anzahl'] : ''));
}
require_once(DIR_WS_INCLUDES . 'header.php');
?>

<table class="table">
    <tr>
        <td>
            Schnelle Artikelpreis &Auml;nderung</td>
    </tr>
    <tr>
        <td valign="top">Geben Sie bei den gew&uuml;nschten Produkten den neuen Preis an. Auch gerne mehrere gleichzeitig. Danach am Ende der Seite die Art ausw&auml;hlen und abspeichern.</td>
    </tr>
    <tr>
        <td>
            <form method="POST" action="price_change.php?action=save<?php if ($_GET['page'] != '') echo '&page=' . $_GET['page']; ?><?php if ($_GET['anzahl'] != '') echo '&anzahl=' . $_GET['anzahl']; ?>">
                <table class="table">
                    <tr>
                        <td>
                            <input type="submit" class="button" value="speichern" />
                        </td>
                    </tr>
                </table>
				<table class="table table-bordered table-striped">
                    <tr>
                        <th class="col-xs-1">ID</th>
                        <th class="col-xs-4">Name</th>
                        <th class="col-xs-2">Preis</th>
                        <th class="col-xs-1">Bestand</th>
                        <th class="col-xs-1">Art.-Nr.</th>
                        <th class="col-xs-1">EAN</th>
                        <th class="col-xs-1">Lieferzeit</th>
                    </tr>
                    <?php
                    $products_query_raw = " SELECT p.products_id, p.products_quantity, p.products_model, p.products_price, p.products_ean, p.products_tax_class_id, p.products_shippingtime, pd.products_name FROM products AS p JOIN products_description AS pd ON(pd.products_id = p.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "') ORDER BY pd.products_name, p.products_model ASC";
                    $products_split = new splitPageResults($_GET['page'], ($_GET['anzahl'] != '' ? $_GET['anzahl'] : $orderlistingnum), $products_query_raw, $products_query_numrows);
                    $products_query = xtc_db_query($products_query_raw);
                    while ($products_data = xtc_db_fetch_array($products_query)) {
						$value = '';
						$pricevalue = '';
						$price_brutto = xtc_round(($products_data['products_price'] * (xtc_get_tax_rate($products_data['products_tax_class_id']) + 100) / 100), PRICE_PRECISION);
						if (PRICE_IS_BRUTTO == 'true') {
							$value = format_price($products_data['products_price'], 1, $_SESSION['currency'], 0, 0);
							$pricevalue = $price_brutto;
						} else {
							$value = format_price(xtc_round(($products_data['products_price'] * ((xtc_get_tax_rate($products_data['products_tax_class_id']) / 100) + 1)), PRICE_PRECISION), 1, $_SESSION['currency'], 0, 0);
							$pricevalue = xtc_round($products_data['products_price'], PRICE_PRECISION);
						}
                        echo '<tr>';
                        echo '<td>' . $products_data['products_id'] . '</td>';
                        echo '<td><a href="categories.php?subsite=products&pID=' . $products_data['products_id'] . '&action=new_product">' . $products_data['products_name'] . '</a></td>';
                        echo '<td>' . xtc_draw_input_field('product[' . $products_data['products_id'] . ']', $pricevalue, 'size="12"') . '('. $value .')</td>';
                        echo '<td>' . xtc_draw_input_field('products_quantity[' . $products_data['products_id'] . ']', $products_data['products_quantity'] == '' ? '' : $products_data['products_quantity'], 'size="12"') . '</td>';
                        echo '<td>' . xtc_draw_input_field('products_model[' . $products_data['products_id'] . ']', $products_data['products_model'] == '' ? '' : $products_data['products_model'], '') . '</td>';
                        echo '<td>' . xtc_draw_input_field('products_ean[' . $products_data['products_id'] . ']', $products_data['products_ean'] == '' ? '' : $products_data['products_ean'], '') . '</td>';
                        echo '<td>' . xtc_draw_pull_down_menu('products_shippingtime[' . $products_data['products_id'] . ']', $shipping_statuses, $products_data['products_shippingtime'] == 0 ? (int) (DEFAULT_SHIPPING_STATUS_ID) : $products_data['products_shippingtime']) . '</td>';
                        echo '</tr>';
                        // product attributes
                        $attributes_query = xtc_db_query(getStrSqlAttributes($products_data['products_id']));
                        if (xtc_db_num_rows($attributes_query)) {
                            echo '<tr>';
                            echo '<td colspan="10"><h2>' . HEADING_PRODUCTS_ATTRIBUTES_ATTRIBUTES_NAME . '</h2>';
							echo '<table class="table table-bordered">';
                    echo '<tr>
                        <th class="col-xs-1">ID</th>
                        <th class="col-xs-4">Name</th>
                        <th class="col-xs-2">Preis</th>
                        <th class="col-xs-1">Bestand</th>
                        <th class="col-xs-1">Art.-Nr.</th>
                        <th class="col-xs-1">EAN</th>
                        <th class="col-xs-1">Lieferzeit</th>
                    </tr>';
							$attrstock = 0;
                            while ($attributes = xtc_db_fetch_array($attributes_query)) {
                                $value = '';
                                $attrvalue = '';
                                $attr_price_brutto = xtc_round(($attributes['options_values_price'] * (xtc_get_tax_rate($products_data['products_tax_class_id']) + 100) / 100), PRICE_PRECISION);
                                echo '<tr>';
                                echo '<td>';
                                echo xtc_draw_hidden_field('attributes_price_' . $attributes['products_attributes_id'], $attributes['options_values_price']);
                                echo $attributes['products_attributes_id'];
                                echo xtc_draw_hidden_field('attributes_id[]', $attributes['products_attributes_id']);
                                echo '</td>';
                                if ($attributes['options_values_price'] > 0) {
                                    if (PRICE_IS_BRUTTO == 'true') {
                                        $value = format_price($attributes['options_values_price'], 1, $_SESSION['currency'], 0, 0);
										$attrvalue = $attr_price_brutto;
                                    } else {
										$value = format_price(xtc_round(($attributes['options_values_price'] * ((xtc_get_tax_rate($products_data['products_tax_class_id']) / 100) + 1)), PRICE_PRECISION), 1, $_SESSION['currency'], 0, 0);
                                        $attrvalue = xtc_round($attributes['options_values_price'], PRICE_PRECISION);
                                    }
                                }
                                echo '<td>' . $attributes['products_options_name'] . ": " . $attributes['products_options_values_name'] . '</td>';
                                echo '<td>' . xtc_draw_input_field('options_values_price[' . $attributes['products_attributes_id'] . ']', $attrvalue, 'size="12"') . '(' . $value . ')</td>';
                                echo '<td>' . xtc_draw_input_field('attributes_stock[' . $attributes['products_attributes_id'] . ']', $attributes['attributes_stock'], 'size="12"') . '</td>';
                                echo '<td>' . xtc_draw_input_field('attributes_model[' . $attributes['products_attributes_id'] . ']', $attributes['attributes_model'], '') . '</td>';
                                echo '<td>' . xtc_draw_input_field('attributes_ean[' . $attributes['products_attributes_id'] . ']', $attributes['attributes_ean'], '') . '</td>';
                                echo '<td>' . xtc_draw_pull_down_menu('attributes_shippingtime[' . $attributes['products_attributes_id'] . ']', $shipping_statuses, $attributes['attributes_shippingtime'] == 0 ? (int) (DEFAULT_SHIPPING_STATUS_ID) : $attributes['attributes_shippingtime']) . '</td>';
                                echo '</tr>';
								$attrstock += $attributes['attributes_stock'];
								
                            }
								echo '<tr>';
								echo '<td colspan="10"><h2>' . HEADING_PRODUCTS_ATTRIBUTES_ATTRIBUTES_BETSAND . $attrstock += $attributes['attributes_stock'] . '</h2></td>';
                                echo '</tr>';
                            echo '</table>';
                            echo '</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </table>
                <table class="table">
                    <tr>
                        <td>
                            <input type="submit" class="button" value="speichern" />
                        </td>
                    </tr>
                </table>
            </form>
            <table class="table">
                <tr>
                    <td class="smallText" valign="top" width="33.3%"><?php echo $products_split->display_count($products_query_numrows, ($_GET['anzahl'] != '' ? $_GET['anzahl'] : $orderlistingnum), $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                    <td class="smallText" align="center" width="33.3%"><?php echo $products_split->display_links($products_query_numrows, ($_GET['anzahl'] != '' ? $_GET['anzahl'] : $orderlistingnum), MAX_DISPLAY_PAGE_LINKS, $_GET['page'], xtc_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
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

                        $products_per_page .= xtc_draw_pull_down_menu('anzahl', $products_per_page_options, ($_GET['anzahl'] != '' ? $_GET['anzahl'] : $orderlistingnum), 'onchange="this.form.submit()"') . "\n";

                        $products_per_page .= '</form>' . "\n";
                        ?>
                        Produkte pro Seite: <?php echo $products_per_page; ?>
                    </td>
                </tr>
            </table>
            <?php
            require(DIR_WS_INCLUDES . 'footer.php');
            require(DIR_WS_INCLUDES . 'application_bottom.php');
            