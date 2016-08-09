<?php

/* -----------------------------------------------------------------
 * 	$Id: main_top_products.php 1200 2014-09-17 06:30:45Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

$module_smarty = new Smarty;
$items = array();
$module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');

//Startseite Top Produkte
$site = 'new_products_default';
$title = MAIN_TOP_PRODUCTS_DEFAULT;

$new_products_query = xtDBquery("SELECT p.*, pd.products_name, pd.products_description, pd.products_short_description
						FROM " . TABLE_PRODUCTS . " p
						JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON(p.products_id = pd.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
						WHERE p.products_status = '1' 
						AND p.products_startpage = '1'
							" . $group_check . "
							" . $fsk_lock . "
						GROUP BY p.products_id
						ORDER BY p.products_startpage_sort ASC 
						LIMIT " . MAX_DISPLAY_NEW_PRODUCTS . ";");

$row = 0;
$module_content = array();
if (xtc_db_num_rows($new_products_query) > 0) {
while ($new_products = xtc_db_fetch_array($new_products_query)) {
	$row++;
	$module_content[] = $product->buildDataArray($new_products, 'thumbnail', $site, $row);
}
}

if (sizeof($module_content) >= 1) {
    $module_smarty->assign('language', $_SESSION['language']);
    $module_smarty->assign('module_content', $module_content);
	$module_smarty->assign('TITLE', $title);
    $module_smarty->assign('CLASS', $site);
	$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
	if (!CacheCheck()) {
		$module_smarty->caching = false;
		$module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE));
	} else {
		$module_smarty->caching = true;
		$module_smarty->cache_lifetime = CACHE_LIFETIME;
		$module_smarty->cache_modified_check = CACHE_CHECK;
		$cache_id = $_SESSION['language'] . $_SESSION['customers_status']['customers_status_name'] . $_SESSION['currency'] . 'maintop';
		$module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE), $cache_id);
	}
	$default_smarty->assign('MODULE_main_top_products', $module);
}
