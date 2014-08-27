<?php

/* -----------------------------------------------------------------
 * 	$Id: main_random_specials.php 1118 2014-06-25 19:54:28Z akausch $
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

if (MAX_RANDOM_PRODUCTS > 0) {

    $module_smarty = new Smarty;
    $module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');

    $group_check = '';
    $fsk_lock = '';
    if (GROUP_CHECK == 'true') {
        $group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " = 1 ";
    }

    if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
        $fsk_lock = ' AND p.products_fsk18 !=1';
    }

    $random_products_query = xtDBquery("SELECT
											p.*,
											pd.*,
											s.*
										FROM 
											" . TABLE_PRODUCTS . " p
										LEFT JOIN 
											" . TABLE_PRODUCTS_DESCRIPTION . " pd ON(pd.products_id = p.products_id)
										LEFT JOIN 
											" . TABLE_SPECIALS . " s ON(p.products_id = s.products_id)
										WHERE 
											p.products_status = '1'
										AND 
											pd.language_id = '" . $_SESSION['languages_id'] . "'
										AND 
											s.status = '1'
										AND
											(p.products_slave_in_list = '1' OR p.products_master = '1' OR ((p.products_slave_in_list = '0' OR p.products_slave_in_list = '') AND (p.products_master_article = '' OR p.products_master_article = '0')))
										" . $fsk_lock . $group_check . "	
										GROUP BY p.products_id
										ORDER BY rand() 
										LIMIT " . MAX_RANDOM_PRODUCTS . ";");

    $module_content = array();
    $row = 0;
    while ($random_products = xtc_db_fetch_array($random_products_query, true)) {
        $row++;
        $module_content[] = $product->buildDataArray($random_products, 'thumbnail', 'random_specials', $row);
    }

    if (sizeof($module_content) >= 1) {
        $module_smarty->assign('language', $_SESSION['language']);
        $module_smarty->assign('module_content', $module_content);
        $module_smarty->assign('TITLE', RANDOM_SPECIALS);
        $module_smarty->assign('CLASS', 'random_specials');
        $module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        if (!CacheCheck()) {
            $module_smarty->caching = false;
            if (DISPLAY_NEW_PRODUCTS_SLIDE == 'true') {
                $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/main_products.html', USE_TEMPLATE_DEVMODE));
            } else {
                $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE));
            }
        } else {
            $module_smarty->caching = true;
            $module_smarty->cache_lifetime = CACHE_LIFETIME;
            $module_smarty->cache_modified_check = CACHE_CHECK;
            $cache_id = $_SESSION['language'] . '_' . $_SESSION['customers_status']['customers_status_id'] . '_randspec_';
            if (DISPLAY_NEW_PRODUCTS_SLIDE == 'true') {
                $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/main_products.html', USE_TEMPLATE_DEVMODE), $cache_id);
            } else {
                $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE), $cache_id);
            }
        }
        $default_smarty->assign('MODULE_random_specials', $module);
    }
}
