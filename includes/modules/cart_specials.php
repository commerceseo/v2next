<?php

/* -----------------------------------------------------------------
 * 	$Id: cart_specials.php 522 2013-07-24 11:44:51Z akausch $
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

$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');

if ($_SESSION['customers_status']['customers_fsk18_display'] == '0')
    $fsk_lock = ' p.products_fsk18!=1 AND';

if (GROUP_CHECK == 'true')
    $group_check = " p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 AND ";

$new_products_query = xtDBquery("SELECT 
									* 
								FROM
									" . TABLE_PRODUCTS . " p,
									" . TABLE_PRODUCTS_DESCRIPTION . " pd 
								WHERE
									p.products_cartspecial = '1' 
								AND 
									p.products_id = pd.products_id 
								AND
									" . $group_check . "
									" . $fsk_lock . "  
									p.products_status = '1' 
								AND 
									pd.language_id = '" . (int) $_SESSION['languages_id'] . "' 
								LIMIT 
									" . MAX_DISPLAY_CART_SPECIALS);

$row = 0;
$module_content = array();
while ($new_products = xtc_db_fetch_array($new_products_query, true)) {
    $row++;
    $module_content[] = $product->buildDataArray($new_products, 'thumbnail', 'cart_special', $row);
}

if (sizeof($module_content) >= 1) {
    $module_smarty->assign('language', $_SESSION['language']);
    $module_smarty->assign('module_content', $module_content);
    $module_smarty->assign('TITLE', CART_SPECIAL);
    $module_smarty->assign('CLASS', 'cart_special');
    $module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $module_smarty->caching = false;
    $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE));
    $smarty->assign('MODULE_cart_specials', $module);
}
