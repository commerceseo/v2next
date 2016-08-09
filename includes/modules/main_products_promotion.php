<?php

/* -----------------------------------------------------------------
 * 	$Id: main_products_promotion.php 1468 2015-07-22 20:29:10Z akausch $
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

require_once( 'includes/application_top.php');

if (MODULE_PRODUCT_PROMOTION_STATUS == 'true') {
    $module_smarty = new Smarty;
    $module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
    $pp_query = xtc_db_query("SELECT p.*, pd.*
							FROM " . TABLE_PRODUCTS . " p
							JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON(p.products_id = pd.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
							WHERE p.products_status = '1'
							AND p.products_promotion_status = '1'
							GROUP BY p.products_id
							ORDER BY p.products_id");

    $row = 0;
    $pp_modul = array();
	if (xtc_db_num_rows($pp_query) > 0) {
    while ($pp_data = xtc_db_fetch_array($pp_query)) {
        if ($pp_data['products_promotion_image'] != '') {
            $productimage = DIR_WS_IMAGES . 'products_promotion/' . $pp_data['products_promotion_image'];
        } elseif ($pp_data['products_image'] != '') {
            $productimage = DIR_WS_POPUP_IMAGES . $pp_data['products_image'];
        }

        if ($pp_data['products_promotion_product_desc'] == '0' && $pp_data['products_promotion_desc'] == '') {
            $promo_text = '';
        } elseif ($pp_data['products_promotion_product_desc'] == '0' && $pp_data['products_promotion_desc'] != '') {
            $promo_text = $pp_data['products_promotion_desc'];
        } elseif ($pp_data['products_promotion_product_desc'] == '1') {
            $promo_text = $pp_data['products_description'];
        }

        if ($pp_data['products_promotion_product_title'] == '1') {
            $promo_title = $pp_data['products_name'];
        } elseif ($pp_data['products_promotion_product_title'] == '0' && $pp_data['products_promotion_title'] != '') {
            $promo_title = $pp_data['products_promotion_title'];
        }

        $pp_modul[] = array(
            'PRODUCT_NAME' => $promo_title,
            'PRODUCT_LINK' => xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $pp_data['products_id']),
            'PRODUCT_DESCRIPTION' => $promo_text,
            'PRODUCT_IMAGE' => $productimage);
        $row++;
    }
    }
    $module_smarty->assign('language', $_SESSION['language']);
    $module_smarty->assign('promotion_modul', $pp_modul);
    $pp_template = xtc_db_fetch_array($pp_query, true);
    $module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);

    if (!CacheCheck()) {
        $module_smarty->caching = false;
        $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/products_promotion.html', USE_TEMPLATE_DEVMODE));
    } else {
        $module_smarty->caching = true;
        $module_smarty->cache_lifetime = CACHE_LIFETIME;
        $module_smarty->cache_modified_check = CACHE_CHECK;
        $cache_id = $_SESSION['language'] . '_' . $_SESSION['customers_status']['customers_status_id'] . '_promotion_';
        $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/products_promotion.html', USE_TEMPLATE_DEVMODE), $cache_id);
    }
    $default_smarty->assign('MODULE_products_promotion', $module);
}
