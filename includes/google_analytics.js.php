<?php
/*-----------------------------------------------------------------
* 	$Id: google_analytics.js.php 1133 2014-07-08 16:31:23Z akausch $
* 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
* 	http://www.commerce-seo.de
* ------------------------------------------------------------------
* 	based on:
* 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
* 	(c) 2002-2003 osCommerce - www.oscommerce.com
* 	(c) 2003     nextcommerce - www.nextcommerce.org
* 	(c) 2005     xt:Commerce - www.xt-commerce.com
* 	Released under the GNU General Public License
* ---------------------------------------------------------------*/

$js = '
<script>
var gaProperty = \''.GOOGLE_ANAL_CODE.'\';
// Disable tracking if the opt-out cookie exists.
var disableStr = \'ga-disable-\' + gaProperty;
if (document.cookie.indexOf(disableStr + \'=true\') > -1) {
window[disableStr] = true;
}
// Opt-out function
function gaOptout() {
document.cookie = disableStr + \'=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/\';
window[disableStr] = true;
}
</script>';
if(strstr($_REQUEST['linkurl'], FILENAME_CHECKOUT_SUCCESS) || strstr($PHP_SELF, FILENAME_CHECKOUT_SUCCESS) || strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SUCCESS)) {
$js .= "\n".'<script>
(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');

ga(\'create\', \''.GOOGLE_ANAL_CODE.'\', \'auto\');'."\n";
if (GOOGLE_ANALYTICS_ANONYMI == 'true') {
$js .= 'ga(\'set\', \'anonymizeIp\', true);'."\n";
}
$js .= 'ga(\'send\', \'pageview\');

ga(\'require\', \'ecommerce\', \'ecommerce.js\')'."\n";

		require_once(DIR_WS_CLASSES.'class.order.php');
		require_once(DIR_FS_INC.'xtc_get_attributes_model.inc.php');
		require_once(DIR_FS_INC.'xtc_get_product_path.inc.php');
		$orders = xtc_db_fetch_array(xtc_db_query("SELECT orders_id FROM ".TABLE_ORDERS." WHERE customers_id = '".(int)$_SESSION['customer_id']."' ORDER BY orders_id DESC LIMIT 1;"));
		$order = new order($orders['orders_id']);
		$order_query = xtc_db_query("SELECT * FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_id = '".$orders['orders_id']."'");

		$ot_total = xtc_db_fetch_array(xtc_db_query("SELECT value FROM ".TABLE_ORDERS_TOTAL." WHERE orders_id='".$orders['orders_id']."' AND class = 'ot_total';"));
		$ot_shipping = xtc_db_fetch_array(xtc_db_query("SELECT value FROM ".TABLE_ORDERS_TOTAL." WHERE orders_id='".$orders['orders_id']."' AND class = 'ot_shipping';"));
		$ot_tax = xtc_db_fetch_array(xtc_db_query("SELECT value FROM ".TABLE_ORDERS_TOTAL." WHERE orders_id='".$orders['orders_id']."' AND class = 'ot_tax' "));

$js .= 	'ga(\'ecommerce:addTransaction\', {
	\'id\':\''.$orders['orders_id'].'\',
	\'affiliation\':\''.STORE_NAME.'\',
	\'revenue\':\''.number_format($ot_total['value'], 2, '.', '').'\',
	\'shipping\':\''.number_format($ot_shipping['value'], 2, '.', '').'\',
	\'tax\':\''.number_format($ot_tax['value'], 2, '.', '').'\'
});'."\n";

while($order_data_values = xtc_db_fetch_array($order_query)) {
	$attributes_query = xtc_db_query("SELECT * FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." WHERE orders_products_id='".$order_data_values['orders_products_id']."';");
	$attributes_data = '';
	$attributes_model = '';
	$i = 0;
	while($attributes_data_values = xtc_db_fetch_array($attributes_query)) { $i++;
		$attributes_data .= ($i > 1 ? ' | ' : '').$attributes_data_values['products_options'].' '.$attributes_data_values['products_options_values'];
	}
	$CatName = xtc_db_fetch_array(xtc_db_query("SELECT categories_name FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id = '".xtc_get_product_path($order_data_values['products_id'])."' AND language_id = '".(int)$_SESSION['languages_id']."';"));

$js .= 'ga(\'ecommerce:addItem\', {
	\'id\':\''.$orders['orders_id'].'\',
	\'name\':\''.$order_data_values['products_name'].'\',
	\'sku\':\''.$order_data_values['products_id'].'\',
	\'category\':\''.(!empty($attributes_data) ? $attributes_data : $CatName).'\',
	\'price\':\''.number_format($order_data_values['products_price'], 2, '.', '').'\',
	\'quantity\':\''.$order_data_values['products_quantity'].'\'
});'."\n";

}		
$js .= 'ga(\'ecommerce:send\');'."\n";
$js .= '</script>'."\n";
} else {
$js .= "\n".'<script>
(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');
ga(\'create\', \''.GOOGLE_ANAL_CODE.'\', \'auto\');
ga(\'send\', \'pageview\');
';
if (GOOGLE_ANALYTICS_ANONYMI == 'true') {
$js .= 'ga(\'set\', \'anonymizeIp\', true);'."\n";
}
$js .= '</script>'."\n";
}
echo $js;
?>