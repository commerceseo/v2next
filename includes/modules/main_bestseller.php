<?php

/* -----------------------------------------------------------------
 * 	$Id: main_bestseller.php 522 2013-07-24 11:44:51Z akausch $
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

if (MAX_DISPLAY_BESTSELLERS > 0) {

    $module_smarty = new Smarty;
    $module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
    $group_check = '';
    $fsk_lock = '';
    if (GROUP_CHECK == 'true') {
        $group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " = 1 ";
    }

    if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
        $fsk_lock = ' AND p.products_fsk18!=1';
    }

    $best_sellers_query = xtDBquery("SELECT DISTINCT
										p.*,
										pd.products_name 
									FROM 
										" . TABLE_PRODUCTS . " p 
										LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON(p.products_id = pd.products_id)
									WHERE 
										p.products_status = '1'
									" . $group_check . "
									AND 
										p.products_ordered > 0
									 " . $fsk_lock . "
									AND 
										pd.language_id = '" . (int) $_SESSION['languages_id'] . "'
									AND
										(p.products_slave_in_list = '1' OR p.products_master = '1' OR ((p.products_slave_in_list = '0' OR p.products_slave_in_list = '') AND (p.products_master_article = '' OR p.products_master_article = '0')))
									ORDER BY 
										p.products_ordered DESC
									LIMIT " . MAX_DISPLAY_BESTSELLERS);

    if (xtc_db_num_rows($best_sellers_query, true) >= MIN_DISPLAY_BESTSELLERS) {
        $module_content = array();
        $row = 0;
        while ($best_sellers = xtc_db_fetch_array($best_sellers_query, true)) {
            $row++;
            $module_content[] = $product->buildDataArray($best_sellers, 'thumbnail', 'random_bestseller', $row);
        }
    }

    if (sizeof($module_content) >= 1) {
        $module_smarty->assign('language', $_SESSION['language']);
        $module_smarty->assign('module_content', $module_content);
        $module_smarty->assign('TITLE', MAIN_BESTSELLER);
        $module_smarty->assign('CLASS', 'main_bestseller');
        $module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        if (!CacheCheck()) {
            $module_smarty->caching = false;
            if (DISPLAY_RAND_PRODUCTS_SLIDE == 'true') {
                $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/main_products.html', USE_TEMPLATE_DEVMODE));
            } else {
                $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE));
            }
        } else {
            $module_smarty->caching = true;
            $module_smarty->cache_lifetime = CACHE_LIFETIME;
            $module_smarty->cache_modified_check = CACHE_CHECK;
            $cache_id = $_SESSION['language'] . '_' . $_SESSION['customers_status']['customers_status_id'] . '_bestseller_';
            if (DISPLAY_RAND_PRODUCTS_SLIDE == 'true') {
                $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/main_products.html', USE_TEMPLATE_DEVMODE), $cache_id);
            } else {
                $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE), $cache_id);
            }
        }
        $default_smarty->assign('MODULE_main_bestseller', $module);
    }
}
