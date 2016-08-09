<?php

/* -----------------------------------------------------------------
 * 	$Id: default.php 1424 2015-02-03 15:30:26Z akausch $
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

$default_smarty = new smarty;
$default_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
$default_smarty->assign('session', session_id());
$main_content = '';
$classdefault = new classdefault();

require_once (DIR_FS_INC . 'xtc_customer_greeting.inc.php');
require_once (DIR_FS_INC . 'xtc_get_path.inc.php');
require_once (DIR_FS_INC . 'xtc_check_categories_status.inc.php');

if (xtc_check_categories_status($current_category_id) >= 1) {
    $error = CATEGORIE_NOT_FOUND;
    include (DIR_WS_MODULES . FILENAME_ERROR_HANDLER);
} else {
	if (file_exists(DIR_WS_INCLUDES . 'addons/default_top_addon.php')) {
		include (DIR_WS_INCLUDES . 'addons/default_top_addon.php');
	}
    if ($category_depth == 'nested') {
        $t_view_html = $classdefault->category($current_category_id);
        if (is_array($t_view_html)) {
            foreach ($t_view_html AS $t_key => $t_value) {
                $default_smarty->assign($t_key, $t_value);
            }
        }
        $new_products_category_id = $current_category_id;
        include (DIR_WS_MODULES . FILENAME_NEW_PRODUCTS);
		$category = xtc_db_fetch_array(xtDBquery("SELECT categories_template FROM " . TABLE_CATEGORIES . " c WHERE c.categories_id = '" . $current_category_id . "';"));

        if ($category['categories_template'] == '' or $category['categories_template'] == 'default') {
            $files = array();
            if ($dir = opendir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/categorie_listing/')) {
                while (($file = readdir($dir)) !== false) {
                    if (is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/categorie_listing/' . $file) && (substr($file, -5) == '.html') && ($file != 'index.html') && (substr($file, 0, 1) != '.'))
                        $files[] = $file;
                }
                closedir($dir);
            }
            sort($files);
            $category['categories_template'] = $files[0];
        }

        $default_smarty->assign('language', $_SESSION['language']);
        $default_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        if (!CacheCheck()) {
            $default_smarty->caching = false;
            $main_content = $default_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/categorie_listing/' . $category['categories_template'], USE_TEMPLATE_DEVMODE));
        } else {
            $slider_smarty->caching = true;
            $slider_smarty->cache_lifetime = CACHE_LIFETIME;
            $slider_smarty->cache_modified_check = CACHE_CHECK;
            $cache_id = $_SESSION['language'] . $current_category_id . $_SESSION['customer_id'] . 'slider';
            $main_content = $default_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/categorie_listing/' . $category['categories_template'], USE_TEMPLATE_DEVMODE), $cache_id);
        }

        $smarty->assign('main_content', $main_content);
        //Slider
        $slider_smarty = new smarty;
        $slider_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $coo_slider = cseohookfactory::create_object('SliderManager');
        $t_view_html = $coo_slider->proceed($current_category_id, 'cat');
        if (is_array($t_view_html)) {
            foreach ($t_view_html AS $t_key => $t_value) {
                $slider_smarty->assign($t_key, $t_value);
            }
        }
        if (!CacheCheck()) {
            $slider_smarty->caching = false;
            $slider_content = $slider_smarty->fetch(cseo_get_usermod('base/module/slider_content.html', USE_TEMPLATE_DEVMODE));
        } else {
            $slider_smarty->caching = true;
            $slider_smarty->cache_lifetime = CACHE_LIFETIME;
            $slider_smarty->cache_modified_check = CACHE_CHECK;
            $cache_id = $_SESSION['language'] . $_SESSION['currency'] . $_SESSION['customer_id'] . $cPath . 'slider';
            $slider_content = $slider_smarty->fetch(cseo_get_usermod('base/module/slider_content.html', USE_TEMPLATE_DEVMODE), $cache_id);
        }
        $smarty->assign('slider_content', $slider_content);
    } elseif ($category_depth == 'products' || (isset($_GET['manufacturers_id']) && $_GET['manufacturers_id'] > 0)) {
        $listing_sql = $classdefault->products($current_category_id, $new_products_category_id);
        $multisort_dropdown = $classdefault->multisort_dropdown($current_category_id);
        $manufacturer_dropdown = $classdefault->manufacturer_dropdown($current_category_id);
        include (DIR_WS_MODULES . FILENAME_PRODUCT_LISTING);
    } else {
        // Content Manager default page
        include (DIR_WS_INCLUDES . FILENAME_CENTER_MODULES);

        $t_view_html = $classdefault->content();
        if (is_array($t_view_html)) {
            foreach ($t_view_html AS $t_key => $t_value) {
                $default_smarty->assign($t_key, $t_value);
            }
        }

        if (file_exists(DIR_WS_INCLUDES . 'addons/default_addon.php')) {
            include (DIR_WS_INCLUDES . 'addons/default_addon.php');
        }

        $default_smarty->assign('language', $_SESSION['language']);
        $default_smarty->assign('SORTING', $classdefault->getSorting());
        $default_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);

        if (!CacheCheck()) {
            $default_smarty->caching = false;
            $main_content = $default_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/main_content.html', USE_TEMPLATE_DEVMODE));
        } else {
            $default_smarty->caching = true;
            $default_smarty->cache_lifetime = CACHE_LIFETIME;
            $default_smarty->cache_modified_check = CACHE_CHECK;
            $cache_id = $_SESSION['language'] . $_SESSION['currency'] . $_SESSION['customer_id'];
            $main_content = $default_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/main_content.html', USE_TEMPLATE_DEVMODE), $cache_id);
        }
        $smarty->assign('main_content', $main_content);

        $slider_smarty = new smarty;
        $slider_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $coo_slider = cseohookfactory::create_object('SliderManager');
        $t_view_html = $coo_slider->proceed('5', 'content');
        if (is_array($t_view_html)) {
            foreach ($t_view_html AS $t_key => $t_value) {
                $slider_smarty->assign($t_key, $t_value);
            }
        }

        if (!CacheCheck()) {
            $slider_smarty->caching = false;
            $slider_content = $slider_smarty->fetch(cseo_get_usermod('base/module/slider_content.html', USE_TEMPLATE_DEVMODE));
        } else {
            $slider_smarty->caching = true;
            $slider_smarty->cache_lifetime = CACHE_LIFETIME;
            $slider_smarty->cache_modified_check = CACHE_CHECK;
            $cache_id = $_SESSION['language'] . $_SESSION['currency'] . $_SESSION['customer_id'] . $cPath . 'slider';
            $slider_content = $slider_smarty->fetch(cseo_get_usermod('base/module/slider_content.html', USE_TEMPLATE_DEVMODE), $cache_id);
        }
        $smarty->assign('slider_content', $slider_content);
    }
}
