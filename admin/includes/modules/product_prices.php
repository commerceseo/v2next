<?php
/* -----------------------------------------------------------------
 * 	$Id: product_prices.php 873 2014-03-25 16:42:10Z akausch $
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

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once (DIR_FS_INC . 'xtc_get_tax_rate.inc.php');

$xtPrice = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);

if (PRICE_IS_BRUTTO == 'true') {
    $products_price = xtc_round($pInfo->products_price * ((100 + xtc_get_tax_rate($pInfo->products_tax_class_id)) / 100), PRICE_PRECISION);
    if ($pInfo->products_tax_class_id == '') {
        $taxdefault = '1';
    } else {
        $taxdefault = $pInfo->products_tax_class_id;
    }
} else {
    $products_price = xtc_round($pInfo->products_price, PRICE_PRECISION);
    if ($pInfo->products_tax_class_id == '') {
        $taxdefault = '1';
    } else {
        $taxdefault = $pInfo->products_tax_class_id;
    }
}

echo '<table width="100%" class="tablePrice">';
echo '<tr>';
echo '<td id="productsPrice" class="main">'.TEXT_PRODUCTS_PRICE.'</td>';
echo '<td id="productsPriceInput" class="main">';
echo xtc_draw_input_field('products_price', $products_price);
if (PRICE_IS_BRUTTO == 'true') {
	echo '&nbsp;' . TEXT_NETTO . '<b>' . $xtPrice->xtcFormat($pInfo->products_price, false) . '</b>  ';
}
echo '</td>';
echo '</tr>';
echo '<tr>';
echo '<td class="main">'.TEXT_PRODUCTS_QUANTITY.'</td>';
echo '<td class="main">'.xtc_draw_input_field('products_quantity', $pInfo->products_quantity).'</td>';
echo '</tr>';
echo '<tr>';
echo '<td class="main">'.TEXT_PRODUCTS_MODEL.'</td>';
echo '<td class="main">'.xtc_draw_input_field('products_model', $pInfo->products_model).'</td>';
echo '</tr>';
echo '<tr>';
echo '<td class="main">'.TEXT_PRODUCTS_TAX_CLASS.'</td>';
echo '<td class="main">'.xtc_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $taxdefault).'</td>';
echo '</tr>';
if (table_exists('kfz') == true) {
	$res=xtc_db_query('SELECT * FROM partNrFields;');
	$fields=array();
	if(xtc_db_num_rows($res)>0){
		while($r=xtc_db_fetch_array($res)){
			$fields[]=$r;
		}
	}

	echo '<tr>';
	echo '<td class="main">Part Nr.:</td>';
	echo '<td class="main">'.xtc_draw_input_field('partNr', $pInfo->partNr).'</td>';
	echo '</tr>';
	foreach($fields as $field){
		echo '<tr>';
		echo '<td class="main">'.$field['name'].'</td>';
		echo '<td class="main">'.$name='partNr_'.$field['id'] . xtc_draw_input_field('partNr_'.$field['id'], $pInfo->$name).'</td>';
		echo '</tr>';
	} 
} 
echo '</table>';
