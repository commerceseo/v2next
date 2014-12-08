<?php
/*-----------------------------------------------------------------
* 	$Id: application_bottom.php 1025 2014-05-09 06:55:22Z akausch $
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
echo '<script src="'.DIR_WS_CATALOG.'shopscripte/head.min.js"></script>';
echo '<script>
	head.js(
	';
if($browser->getBrowser() == Browser::BROWSER_IE && $browser->getVersion() <= 8 ) {
echo '"//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js",';
echo '"'.DIR_WS_CATALOG.'shopscripte/js/jquery-migrate.min.js",';
} else {
echo '"//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js",';
}
if (AJAXBOOTSTRAP == 'true') {
	echo '"'.DIR_WS_CATALOG.'shopscripte/js/bootstrap.min.js",';
}
if (AJAXCOLORBOX == 'true') {
	echo '"'.DIR_WS_CATALOG.'shopscripte/js/jquery.colorbox-min.js",';
}
if (AJAXFLEXNAV == 'true') {
	echo '"'.DIR_WS_CATALOG.'shopscripte/js/jquery.flexnav.min.js",';
}
echo '"'.DIR_WS_CATALOG.'shopscripte/js/formsizecheck.js",';
echo '"'.DIR_WS_CATALOG.'shopscripte/js/jquery.rating.pack.js",';
$ticker_query = xtc_db_query("SELECT ticker_text FROM news_ticker WHERE language_id = '".(int)$_SESSION['languages_id']."' AND status = '1' ");
$boxda = xtc_db_query("SELECT box_name FROM boxes WHERE box_name = 'news_ticker' AND status = '1'"); 
if(xtc_db_num_rows($boxda) && xtc_db_num_rows($ticker_query)){
	echo '"'.DIR_WS_CATALOG.'shopscripte/js/jquery.tickertype.js",';
}
if (AJAXRESPSLIDE == 'true') {
	echo '"'.DIR_WS_CATALOG.'shopscripte/js/responsiveslides.min.js",';
}
if (file_exists('templates/'.CURRENT_TEMPLATE.'/javascript/template.js')) {
	echo '"'.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/javascript/template.js",';
}
if(is_dir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/plugins')) {
	$cseo_plugin_js_path = DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/plugins/js/';
	$cseo_path_pattern = DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/plugins/js/*.js';
	$cseo_glob_data_array = glob($cseo_path_pattern);
	if(is_array($cseo_glob_data_array)) {
		foreach($cseo_glob_data_array AS $cseo_result) {
		$cseo_entry = basename($cseo_result);
		echo '"'.$cseo_plugin_js_path . $cseo_entry . '",';
		}
	}
}

if (AJAXJQUERYUI == 'true') {
	echo '"'.DIR_WS_CATALOG.'shopscripte/js/jquery-ui-1.10.3.custom.min.js",';
}
//Seitenspezifische JS
if (PRODUCT_ID > 0 && strpos($PHP_SELF, FILENAME_SHOPPING_CART) === false) {
	if (AJAXJZOOM == 'true') {
		echo '"'.DIR_WS_CATALOG.'shopscripte/js/jquery.jqzoom.js",';
	}
	if ($_SESSION['SPECIAL_DATE'] != '' && PRODUCT_DETAILS_SPECIALS_COUNTER == 'true') { 
		echo '"'.DIR_WS_CATALOG.'shopscripte/js/jquery.countdown.js",';
	}
	if (file_exists('templates/'.CURRENT_TEMPLATE.'/javascript/product_info.js')) {
		echo '"'.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/javascript/product_info.js",';
	}
	echo '"'.DIR_WS_CATALOG.'shopscripte/js/product_info.js",';
	if (PRODUCT_DETAILS_SOCIAL == 'true') {
		echo '"'.DIR_WS_CATALOG.'shopscripte/js/jquery.socialshareprivacy.min.js",';
	}
} else {
	if (PRODUCT_DETAILS_SOCIAL == 'true') {
		echo '"'.DIR_WS_CATALOG.'shopscripte/js/jquery.socialshareprivacy.min.js",';
	}
}
if (AJAXRESPTABS == 'true') {
	echo '"'.DIR_WS_CATALOG.'shopscripte/js/jquery.responsiveTabs.min.js",';
}
echo '"'.DIR_WS_CATALOG.'shopscripte/js/main.js"';
echo ');
</script>';

require_once ('templates/'.CURRENT_TEMPLATE.'/javascript/general.js.php');
require_once ('templates/shopscripte/shopscript.php');

if(TRACKING_PIWIK_ACTIVE == 'true') {
	include('includes/piwik.js.php');
}

if(ETRACKER_CODE !='' && ETRACKER_ON == 'true') {
	include('includes/etracker_analytics.js.php');
}

$t_products_id = 0;
if(isset($product) && $product->data['products_id'] > 0) {
	$t_products_id = $product->data['products_id'];
}

$cseo_application_bottom_extender_component = cseohookfactory::create_object('ApplicationBottomExtenderComponent');
$cseo_application_bottom_extender_component->set_data('GET', $_GET);
$cseo_application_bottom_extender_component->set_data('POST', $_POST);
$cseo_application_bottom_extender_component->set_data('cPath', $cPath);
$cseo_application_bottom_extender_component->set_data('products_id', $t_products_id);
$cseo_application_bottom_extender_component->init_page();
$cseo_application_bottom_extender_component->get_response();
$cseo_application_bottom_extender_component->proceed();

if (STORE_PAGE_PARSE_TIME == 'true') {
	$time_start = explode(' ', PAGE_PARSE_START_TIME);
	$time_end = explode(' ', microtime());
	$parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
	error_log(strftime(STORE_PARSE_DATE_TIME_FORMAT) . ' - ' . getenv('REQUEST_URI') . ' (' . $parse_time . 's)' . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
}


if (DISPLAY_PAGE_PARSE_TIME == 'true') {
	$time_start = explode(' ', PAGE_PARSE_START_TIME);
	$time_end = explode(' ', microtime());
	$parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
	echo '<div id="parsetime">Parse Time: ' . $parse_time . 's</div>';
}
include('templates/admin/admin.php');
echo '
<!-- Shopsoftware commerce:SEO v2 by www.commerce-seo.de based on xt:Commerce 3 - The Shopsoftware is redistributable under the GNU General Public License (Version 2) [http://www.gnu.org/licenses/gpl-2.0.html] -->
</body>
</html>';
if((GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded == 1) && ($ini_zlib_output_compression < 1) ){
	require(DIR_FS_INC.'xtc_gzip_output.inc.php');
	xtc_gzip_output(GZIP_LEVEL);
}