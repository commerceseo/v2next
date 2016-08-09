<?php
/* -----------------------------------------------------------------
 * 	$Id: product_prices_advanced.php 1471 2015-07-22 20:34:59Z akausch $
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
    $products_uvpprice = xtc_round($pInfo->products_uvpprice * ((100 + xtc_get_tax_rate($pInfo->products_tax_class_id)) / 100), PRICE_PRECISION);
} else {
    $products_uvpprice = xtc_round($pInfo->products_uvpprice, PRICE_PRECISION);
}
$products_ekpprice = xtc_round($pInfo->products_ekpprice, PRICE_PRECISION);

echo '<table width="100%" class="tablePrice">';
echo '<tr>';
echo '<td class="main">'.TEXT_PRODUCTS_UVPPRICE.'</td>';
echo '<td class="main">';
echo xtc_draw_input_field('products_uvpprice', $products_uvpprice);
if (PRICE_IS_BRUTTO == 'true') {
	echo '&nbsp;' . TEXT_NETTO . '<b>' . $xtPrice->xtcFormat($pInfo->products_uvpprice, false) . '</b>  ';
}
echo '</tr>';
echo '<tr>';
echo '<td class="main">'.TEXT_PRODUCTS_EKPPRICE.'</td>';
echo '<td class="main">';
echo xtc_draw_input_field('products_ekpprice', $products_ekpprice);
echo '</tr>';
echo '<tr>';
echo '<td class="main">'.TEXT_PRODUCTS_EXTRA_SHIPPING.'</td>';
echo '<td class="main">'.xtc_draw_input_field('products_shipping_costs', $pInfo->products_shipping_costs).'</td>';
echo '</tr>';
echo '<tr>';
echo '<td class="main">'.TEXT_PRODUCTS_MIN_ORDER.'</td>';
echo '<td class="main">'.xtc_draw_input_field('products_minorder', $pInfo->products_minorder).'</td>';
echo '</tr>';
echo '<tr>';
echo '<td class="main">'.TEXT_PRODUCTS_MAX_ORDER.'</td>';
echo '<td class="main">'.xtc_draw_input_field('products_maxorder', $pInfo->products_maxorder).'</td>';
echo '</tr>';
echo '<tr>';
echo '<td class="main">'.TEXT_PRODUCTS_SPERRGUT.'</td>';
echo '<td class="main">'.xtc_draw_pull_down_menu('products_sperrgut', $sperrgut_array, $pInfo->products_sperrgut).'</td>';
echo '</tr>';
echo '<tr>';
echo '<td class="main">'.TEXT_PRODUCTS_DISCOUNT_ALLOWED.'</td>';
echo '<td class="main">'.xtc_draw_input_field('products_discount_allowed', $pInfo->products_discount_allowed).'</td>';
echo '</tr>';
echo '</table>';
