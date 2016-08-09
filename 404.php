<?php

/* -----------------------------------------------------------------
 * 	$Id: 404.php 420 2013-06-19 18:04:39Z akausch $
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

if (file_exists('includes/local/configure.php')) {
    include_once('includes/local/configure.php');
} elseif (file_exists('includes/configure.php')) {
    include_once('includes/configure.php');
} else {
    header('Location: installer/');
    exit;
}
include ('includes/application_top.php');

$offline_query = xtc_db_query("SELECT configuration_value FROM configuration WHERE configuration_key = 'DOWN_FOR_MAINTENANCE';");
$offline = xtc_db_fetch_array($offline_query);


$check = basename($_SERVER['REQUEST_URI']);
if (($offline['configuration_value'] == 'true') && ($check != 'login_offline.php')) {
    header('Location: login_offline.php');
    exit;
}


if (table_exists('commerce_seo_redirect')) {
    $cseo_redirect_query = xtc_db_query("SELECT * FROM commerce_seo_redirect;");
    $linkurl_404_301 = $_SERVER['REQUEST_URI'];

    if (xtc_db_num_rows($cseo_redirect_query)) {
        while ($cseo_redirect = xtc_db_fetch_array($cseo_redirect_query)) {
            if (DIR_WS_CATALOG . $cseo_redirect['old_url'] == $linkurl_404_301 && table_exists('commerce_seo_redirect')) {
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . HTTP_SERVER . DIR_WS_CATALOG . $cseo_redirect['new_url']);
                exit;
            }
        }
    }
}

if (MODULE_COMMERCE_SEO_URL_LENGHT == 'True' && MODULE_COMMERCE_SEO_URL_OLD_REWRITE == 'True') {
	// echo $_SERVER['REQUEST_URI'];
	// echo basename($_SERVER['REQUEST_URI']);
	$server_url = $_SERVER['REQUEST_URI'];
	if (DIR_WS_CATALOG != '/') {
		$server_url = str_replace(DIR_WS_CATALOG, '', $server_url);
	} else {
		$server_url = substr($server_url,1);
	}

	$product_query = xtc_db_query("SELECT products_id FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE url_old_text = '" . $server_url . "' AND language_id='" . (int) $_SESSION['languages_id'] . "';");
	if (xtc_db_num_rows($product_query) > 0) {
		$product_link = xtc_db_fetch_array($product_query);
		$redirectLink = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($product_link['products_id'], $product_link['products_name']));
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: " . $redirectLink);
		exit;
	}
}


// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

$breadcrumb->add(NAVBAR_TITLE_404, xtc_href_link('404.php', '', 'SSL'));

include ('includes/header.php');

if (CSEO_LOG_404 == 'true' && table_exists('commerce_seo_404_stats')) {
    xtc_db_query("INSERT INTO commerce_seo_404_stats VALUES ('','" . xtc_db_input($_SERVER['REQUEST_URI']) . "', '" . xtc_db_input($_SERVER['HTTP_REFERER']) . "') ");
}
$error_data = xtc_db_fetch_array(xtc_db_query("SELECT
							   content_title,
							   content_heading,
							   content_text
							   FROM " . TABLE_CONTENT_MANAGER . "
							   WHERE content_group='11' 
							   AND languages_id='" . (int) $_SESSION['languages_id'] . "'"));

if ($error_data) {
    $smarty->assign('404_TITLE', $error_data['content_heading']);
    $smarty->assign('404_TEXT', $error_data['content_text']);
}
require_once (DIR_FS_INC . 'xtc_hide_session_id.inc.php');
$smarty->assign('FORM_ACTION', xtc_draw_form('quick_find', xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get') . xtc_hide_session_id());
$smarty->assign('INPUT_SEARCH', xtc_draw_input_field('keywords', '', 'size="40" maxlength="30"'));
$smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_quick_find.gif', IMAGE_BUTTON_SEARCH));
$smarty->assign('FORM_END', '</form>');

$smarty->caching = false;
$smarty->assign('language', $_SESSION['language']);

if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/404.html')) {
	$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/404.html', USE_TEMPLATE_DEVMODE));
}else{
	$main_content = $smarty->fetch(cseo_get_usermod('base/module/404.html', USE_TEMPLATE_DEVMODE));
}

$smarty->assign('main_content', $main_content);
$smarty->caching = false;
$smarty->loadFilter('output', 'note');
$smarty->loadFilter('output', 'trimwhitespace');
$smarty->display(CURRENT_TEMPLATE . '/index.html');

include ('includes/application_bottom.php');
