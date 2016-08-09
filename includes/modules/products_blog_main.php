<?php

/* -----------------------------------------------------------------
 * 	$Id: products_blog_main.php 522 2013-07-24 11:44:51Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	06.03.2014 www.indiv-style.de Copyright by H&S eCom 
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');

if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
    $fsk_lock = "AND p.products_fsk18!= '1' ";
}

if (GROUP_CHECK == 'true') {
    $group_check = "AND p.group_permission_" . (int)$_SESSION['customers_status']['customers_status_id'] . " = '1' ";
}

$new_products = xtc_db_fetch_array(xtDBquery("SELECT * 
								FROM " . TABLE_PRODUCTS . " AS p
								JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON(p.products_id = pd.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
								WHERE p.products_id = '".(int)$mytext['art_id']."' 
								AND p.products_status = '1'
									" . $group_check . "
									" . $fsk_lock . "
								GROUP BY p.products_id;"));

$module_content = array();
$module_content[$mytext['art_id']] = $product->buildDataArray($new_products, 'thumbnail', 'upcoming_product', $mytext['art_id']);

if (sizeof($module_content) >= 1) {
    $module_smarty->assign('language', $_SESSION['language']);
    $module_smarty->assign('module_content', $module_content);
    $module_smarty->assign('CLASS', 'upcoming_product');
    $module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
	if (!CacheCheck()) {
		$smarty->caching = false;
		$new_main_content = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE));
	} else {
		$smarty->caching = true;
		$smarty->cache_lifetime = CACHE_LIFETIME;
		$smarty->cache_modified_check = CACHE_CHECK;
		$cache_id = $mytext['art_id'] . $_SESSION['language'] . $_SESSION['customers_status']['customers_status_name'] . $_SESSION['currency'] . 'prodblogmain';
		$new_main_content = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE), $cache_id);
	}
}
