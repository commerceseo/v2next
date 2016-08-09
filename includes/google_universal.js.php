<?php

/* -----------------------------------------------------------------
 * 	$Id: google_universal.js.php 1436 2015-02-06 14:47:02Z akausch $
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
 
/*
For more Information visit: https://developers.google.com/analytics/devguides/collection/analyticsjs/
*/

$js = "\n"."<script async src='//www.google-analytics.com/analytics.js'></script>";

$js .= "\n"."<script>
		window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
		ga('create', '" . GOOGLE_ANAL_CODE . "', 'auto');
		ga('require', 'ecommerce');
		ga('send', 'pageview');
	";
if (GOOGLE_ANALYTICS_ANONYMI == 'true') {
    $js .= "ga('set', 'anonymizeIp', true);";
}
if (strstr($_REQUEST['linkurl'], FILENAME_CHECKOUT_SUCCESS) || strstr($PHP_SELF, FILENAME_CHECKOUT_SUCCESS) || strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SUCCESS)) {
    require_once(DIR_WS_CLASSES . 'class.order.php');
    require_once(DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
    require_once(DIR_FS_INC . 'xtc_get_product_path.inc.php');
    $orders = xtc_db_fetch_array(xtc_db_query("SELECT orders_id FROM " . TABLE_ORDERS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' ORDER BY orders_id DESC LIMIT 1;"));
    $order = new order($orders['orders_id']);
    $ot_total = xtc_db_fetch_array(xtc_db_query("SELECT value FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id='" . $orders['orders_id'] . "' AND class = 'ot_total';"));
    $ot_shipping = xtc_db_fetch_array(xtc_db_query("SELECT value FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id='" . $orders['orders_id'] . "' AND class = 'ot_shipping';"));
    $ot_tax = xtc_db_fetch_array(xtc_db_query("SELECT value FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id='" . $orders['orders_id'] . "' AND class = 'ot_tax' "));

    $js .= "ga('ecommerce:addTransaction', {
			   'id': '" . $orders['orders_id'] . "',
			   'affiliation': '" . STORE_NAME . "',
			   'revenue': '" . number_format($ot_total['value'], 2, '.', '') . "',
			   'shipping': '" . number_format($ot_shipping['value'], 2, '.', '') . "',
			   'tax': '" . number_format($ot_tax['value'], 2, '.', '') . "'
			});";
	
	$query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id = '" . $orders['orders_id'] . "'");

    while ($order_data_values = xtc_db_fetch_array($query)) {
        $CatName = xtc_db_fetch_array(xtc_db_query("SELECT categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id = '" . xtc_get_product_path($order_data_values['products_id']) . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "';"));
        $js .= "ga('ecommerce:addItem', {
				   'id': '" . $orders['orders_id'] . "',
				   'name': '" . $order_data_values['products_name'] . "',
				   'sku': '" . $order_data_values['products_id'] . "',
				   'category': '" . $CatName . "',
				   'price': '" . number_format($order_data_values['products_price'], 2, '.', '') . "',
				   'quantity': '" . $order_data_values['products_quantity'] . "'
				});";
    }
    $js .= "ga('ecommerce:send');";
}

$js .= "</script>"."\n";

echo $js;
