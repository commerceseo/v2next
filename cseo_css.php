<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_css.php 1002 2014-05-05 15:14:06Z akausch $
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

if(file_exists('includes/local/configure.php') && filesize('includes/local/configure.php') !== false) {
	include('includes/local/configure.php');
} elseif(file_exists('includes/configure.php') && filesize('includes/configure.php') !== false) {
	include('includes/configure.php');
} else {
	header('Location: installer/');
	exit;
}

require_once(DIR_WS_INCLUDES.'database_tables.php');
define('SQL_CACHEDIR', DIR_FS_CATALOG.'cache/');

if(!defined('STORE_DB_TRANSACTIONS')) {
	define('STORE_DB_TRANSACTIONS', 'false');
}
require_once(DIR_FS_INC.'cseo_db.inc.php');
xtc_db_connect() or die('Der Datenbankserver konnte nicht erreicht werden!');
$configuration_query = xtc_db_query("SELECT configuration_key as cfgKey, configuration_value as cfgValue FROM ".TABLE_CONFIGURATION.";");
while ($configuration = xtc_db_fetch_array($configuration_query)) {
	define($configuration['cfgKey'], $configuration['cfgValue']);
}

if (function_exists('ini_set')) {
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set("log_errors" , "1");
	ini_set("error_log" , DIR_FS_CATALOG . "logfiles/Errors.log.txt");
	ini_set("display_errors" , "0"); 
}
header('Content-Type: text/css');
include(DIR_WS_CLASSES . 'class.cachefiles.php');
$css = new cacheFile('css');

$css_base_path = 'templates/base/css/';
$css_path = 'templates/' . CURRENT_TEMPLATE . '/css/';
$js_css_path = 'templates/' . CURRENT_TEMPLATE . '/css/jscss/';

$css->setCSS($css_path . 'reset.css');
$css->setCSS($css_base_path . 'base.css');
$css->setCSS($css_path . 'stylesheet.css');
$css->setCSS($css_path . 'mediaqueries.css');
$css->setCSS($css_path . 'css3_browser.css');
$css->setCSS($css_path . 'product_info.css');
$css->setCSS($css_path . 'checkout.css');
$css->setCSS($js_css_path . 'blog.css');
$css->setCSS($js_css_path . 'colorbox.css');
$css->setCSS($js_css_path . 'jquery.rating.css');
$css->setCSS($js_css_path . 'jquery.ui.tabs.css');

if (CSS_BUTTON_ACTIVE == 'css') {
    $css->setCSS($css_path . 'buttons.css');
}

if (MODULE_CSEO_SHOPVOTING_STATUS == 'true') {
    $css->setCSS($css_path . 'shopbewertung.css');
}

if (is_dir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/plugins')) {
    $cseo_plugin_css_path = DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/plugins/css/';
    $cseo_path_pattern = DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/plugins/css/*.css';
    $cseo_glob_data_array = glob($cseo_path_pattern);
    if (is_array($cseo_glob_data_array)) {
        foreach ($cseo_glob_data_array AS $cseo_result) {
            $cseo_entry = basename($cseo_result);
            $css->setCSS($cseo_plugin_css_path . $cseo_entry);
        }
    }
}

if (USE_TEMPLATE_CACHE == 'false') {
    $cache_file = $css->getCachePath('reset.css');
    $t_last_modified = filemtime($cache_file);

    if (date('I', $t_last_modified) != 1 && date('I') == 1) {
        $t_last_modified += 3600;
    } elseif (date('I', $t_last_modified) == 1 && date('I') != 1) {
        $t_last_modified -= 3600;
    }

    $t_hashes_array = array();
    $t_hashes_array[] = md5_file($cache_file);
    $t_etag = '"' . md5(implode('', $t_hashes_array)) . '"';

    header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $t_last_modified) . ' GMT');
    header('Etag: ' . $t_etag);
    $max_age = 60 * 60 * 24 * 7;
    header('Cache-Control: public, max-age=' . $max_age);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . ' GMT');
    header('Connection: Keep-Alive');

    if ((isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $t_etag) || (!isset($_SERVER['HTTP_IF_NONE_MATCH']) && isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $t_last_modified)) {
        header('HTTP/1.1 304 Not Modified');
        exit;
    }
}

echo $css->outputCSS();


if (CSS_BUTTON_ACTIVE == 'true' && CSS_BUTTON_ACTIVE != 'css') {
    include('css_styler.php');
}


if ((GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded == 1) && ($ini_zlib_output_compression < 1)) {
    require(DIR_FS_INC . 'xtc_gzip_output.inc.php');
    xtc_gzip_output(GZIP_LEVEL);
}