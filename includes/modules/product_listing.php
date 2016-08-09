<?php

/* -----------------------------------------------------------------
 * 	$Id: product_listing.php 1354 2015-01-12 15:49:48Z akausch $
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

$result = true;
require_once (DIR_FS_INC . 'xtc_get_all_get_params.inc.php');
require_once (DIR_FS_INC . 'xtc_get_vpe_name.inc.php');

if (isset($_GET['per_site']) && !empty($_GET['per_site'])) {
    $per_site = $_GET['per_site'];
} elseif (isset($_SESSION['per_site'])) {
    $per_site = $_SESSION['per_site'];
} elseif (!isset($_SESSION['per_site']) || !isset($_GET['per_site'])) {
    $per_site = MAX_DISPLAY_SEARCH_RESULTS;
}

$_SESSION['per_site'] = $per_site;

$listing_split = new splitPageResults($listing_sql, (int) $_GET['page'], (int) $per_site, 'p.products_id');

if ($_GET['view_as'] != '') {
    $list_name = $_GET['view_as'];
    $_SESSION['view_as'] = $_GET['view_as'];
} elseif ($_SESSION['view_as'] != '') {
    $list_name = $_SESSION['view_as'];
} elseif (!isset($_SESSION['view_as']) || !isset($_GET['view_as'])) {
    $list_name = 'product_listing_list';
    $_SESSION['view_as'] = 'product_listing_list';
}

$module_content = array();

if ($listing_split->number_of_rows > 0) {
    $navigation_smarty = new Smarty;

    if (isset($_GET['keywords']) && !isset($_GET['manufacturers_id'])) {
        $file_name = FILENAME_ADVANCED_SEARCH_RESULT;
    } else {
        $file_name = FILENAME_DEFAULT;
    }

    if (isset($_GET['multisort']) && !empty($_GET['multisort'])) {
        $get_param = '&multisort=' . $_GET['multisort'];
    }

    if (isset($_GET['filter_id']) && $_GET['filter_id'] != '') {
        $get_param .= '&filter_id=' . $_GET['filter_id'];
    }

    if (isset($_GET['keywords']) || isset($_GET['manufactures_id'])) {
        $page_links = $listing_split->getLinksArraySearch(MAX_DISPLAY_PAGE_LINKS, $get_param, TEXT_DISPLAY_NUMBER_OF_PRODUCTS);
    } elseif (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
        $page_links = $listing_split->getSEOLinksArray(MAX_DISPLAY_PAGE_LINKS, $get_param, TEXT_DISPLAY_NUMBER_OF_PRODUCTS);
    } else {
        $page_links = $listing_split->getLinksArray(MAX_DISPLAY_PAGE_LINKS, $get_param, TEXT_DISPLAY_NUMBER_OF_PRODUCTS);
    }

    $navigation_smarty->assign('COUNT', $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS));
    $navigation_smarty->assign('LINKS', $page_links);
    $navigation_smarty->assign('language', $_SESSION['language']);
    $navigation_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
    $navigation = $navigation_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_page_navigation.html', USE_TEMPLATE_DEVMODE));

    if (GROUP_CHECK == 'true') {
        $group_check = " AND c.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " = '1'";
    }
    $category = xtc_db_fetch_array(xtDBquery("SELECT cd.*, c.*
								FROM " . TABLE_CATEGORIES . " AS c
								JOIN " . TABLE_CATEGORIES_DESCRIPTION . " AS cd ON (cd.categories_id = '" . (int) $current_category_id . "' AND cd.language_id = '" . (int) $_SESSION['languages_id'] . "')
                                WHERE c.categories_id = '" . (int) $current_category_id . "'
								" . $group_check . " GROUP BY c.categories_id;"));

    $image = '';
    if ($category['categories_image'] != '') {
        $image = xtc_image(DIR_WS_IMAGES . 'categories_info/' . $category['categories_image'], ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']), ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']));
        if (!file_exists(DIR_WS_IMAGES . 'categories_info/' . $category['categories_image'])) {
            $image = xtc_image(DIR_WS_IMAGES . 'categories_info/noimage.gif', ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']), ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']));
        }
        $image_small = xtc_image(DIR_WS_IMAGES . 'categories/' . $category['categories_image'], ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']), ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']));
        if (!file_exists(DIR_WS_IMAGES . 'categories/' . $category['categories_image'])) {
            $image_small = xtc_image(DIR_WS_IMAGES . 'categories/noimage.gif', ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']), ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']));
        }
        $image_org = xtc_image(DIR_WS_IMAGES . 'categories_org/' . $category['categories_image'], ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']), ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']), 'img-responsive');
        if (!file_exists(DIR_WS_IMAGES . 'categories_org/' . $category['categories_image'])) {
            $image_org = xtc_image(DIR_WS_IMAGES . 'categories_org/noimage.gif', ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']), ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']));
        }
    }
    $image_footer = '';
    if ($category['categories_footer_image'] != '') {
        $image_footer = xtc_image(DIR_WS_IMAGES . 'categories_footer/' . $category['categories_footer_image'], ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']), ($category['categories_pic_footer_alt'] != '' ? $category['categories_pic_footer_alt'] : $category['categories_name']), 'img-responsive');
        if (!file_exists(DIR_WS_IMAGES . 'categories_footer/' . $category['categories_footer_image'])) {
            $image_footer = xtc_image(DIR_WS_IMAGES . 'categories_info/noimage.gif', ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']), ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']));
        }
    }

    $module_smarty->assign('CATEGORIES_NAME', $category['categories_name']);
    $module_smarty->assign('CATEGORIES_HEADING_TITLE', $category['categories_heading_title']);
    $module_smarty->assign('CATEGORIES_IMAGE', $image);
    $module_smarty->assign('CATEGORIES_IMAGE_ORG', $image_org);
    $module_smarty->assign('CATEGORIES_IMAGE_SMALL', $image_small);
    $module_smarty->assign('CATEGORIES_FOOTER_IMAGE', $image_footer);
    $module_smarty->assign('BASE_PATH', $_SERVER['REQUEST_URI']);

    //Kategoriebeschreibung beim blaettern raus
    if (!isset($_GET['page']) || $_GET['page'] == '1') {
        $module_smarty->assign('CATEGORIES_DESCRIPTION', $category['categories_description']);
			if (MOBILE_CONF_CATEGORY_FOOTER == 'true' || $browser->getBrowser() != Browser::BROWSER_IPHONE) {
				$module_smarty->assign('CATEGORIES_DESCRIPTION_FOOTER', $category['categories_description_footer']);
			} elseif (MOBILE_CONF_CATEGORY_FOOTER == 'false' || $browser->getBrowser() == Browser::BROWSER_IPHONE) {
				$module_smarty->assign('CATEGORIES_DESCRIPTION_FOOTER', '');
			}
		
    }

    //Hersteller Ausgabe
    if ($_SESSION['MANUFACTURES_SORTBOX_IS_IN_USE'] == true) {
        $manRes = xtc_db_fetch_array(xtDBquery("SELECT m.*, mi.manufacturers_description 
								FROM " . TABLE_MANUFACTURERS . " AS m
								JOIN " . TABLE_MANUFACTURERS_INFO . " AS mi ON(mi.manufacturers_id = m.manufacturers_id AND mi.languages_id = '" . (int) $_SESSION['languages_id'] . "') 
								WHERE m.manufacturers_id = '" . (int) $_GET['manufacturers_id'] . "'
								GROUP BY manufacturers_id;"));

        $module_smarty->assign("MANUFACTURERS_NAME", $manRes['manufacturers_name']);
        $module_smarty->assign("MANUFACTURERS_DESCRIPTION", $manRes['manufacturers_description']);
        if ($manRes['manufacturers_image'] != NULL)
            $module_smarty->assign("MANUFACTURERS_IMAGE", xtc_image(DIR_WS_IMAGES . $manRes['manufacturers_image'], $manRes['manufacturers_name'], $manRes['manufacturers_name']));
        unset($_SESSION['MANUFACTURES_SORTBOX_IS_IN_USE']);
    }

    $listing_query = xtDBquery($listing_split->sql_query);
    $rows = 0;
    while ($listing = xtc_db_fetch_array($listing_query, true)) {
        $rows++;
        $module_content[] = $product->buildDataArray($listing, 'thumbnail', $list_name, $rows);
    }
	//Includes Addon
    if (file_exists(DIR_WS_INCLUDES . 'addons/product_listing_addon_cat.php')) {
        include (DIR_WS_INCLUDES . 'addons/product_listing_addon_cat.php');
    }
} else {
    //Keine Produkte, Kategoriebeschreibung wird aber trotzdem ausgegeben
    if (GROUP_CHECK == 'true') {
        $group_check = " AND c.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "='1'";
    }
    $category = xtc_db_fetch_array(xtDBquery("SELECT cd.*, c.*
								FROM " . TABLE_CATEGORIES . " AS c
								JOIN " . TABLE_CATEGORIES_DESCRIPTION . " AS cd ON (cd.categories_id = '" . (int) $current_category_id . "' AND cd.language_id = '" . (int) $_SESSION['languages_id'] . "')
                                WHERE c.categories_id = '" . (int) $current_category_id . "'
								" . $group_check . " 
								GROUP BY c.categories_id;"));
    $image = '';
    if ($category['categories_image'] != '') {
        $image = xtc_image(DIR_WS_IMAGES . 'categories_info/' . $category['categories_image'], ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']), ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']));
        $image_org = xtc_image(DIR_WS_IMAGES . 'categories_org/' . $category['categories_image'], ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']), ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']), 'img-responsive');
        if (!file_exists(DIR_WS_IMAGES . 'categories_info/' . $category['categories_image'])) {
            $image = xtc_image(DIR_WS_IMAGES . 'categories_info/noimage.gif', ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']), ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']));
            $image_org = xtc_image(DIR_WS_IMAGES . 'categories_org/noimage.gif', ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']), ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']));
        }
    }
    $image_footer = '';
    if ($category['categories_footer_image'] != '') {
        $image_footer = xtc_image(DIR_WS_IMAGES . 'categories_footer/' . $category['categories_footer_image'], ($category['categories_pic_footer_alt'] != '' ? $category['categories_pic_footer_alt'] : $category['categories_name']), ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']));
        if (!file_exists(DIR_WS_IMAGES . 'categories_footer/' . $category['categories_footer_image'])) {
            $image_footer = xtc_image(DIR_WS_IMAGES . 'categories_info/noimage.gif', ($category['categories_pic_alt'] != '' ? $category['categories_pic_alt'] : $category['categories_name']), ($category['categories_heading_title'] != '' ? $category['categories_heading_title'] : $category['categories_name']));
        }
    }

    $module_smarty->assign('CATEGORIES_NAME', $category['categories_name']);
    $module_smarty->assign('CATEGORIES_HEADING_TITLE', $category['categories_heading_title']);
    $module_smarty->assign('CATEGORIES_IMAGE', $image);
    $module_smarty->assign('CATEGORIES_IMAGE_ORG', $image_org);
    $module_smarty->assign('CATEGORIES_FOOTER_IMAGE', $image_footer);
    $module_smarty->assign('CATEGORIES_DESCRIPTION', $category['categories_description']);
    $module_smarty->assign('CATEGORIES_DESCRIPTION_FOOTER', $category['categories_description_footer']);
    $module_smarty->assign('BASE_PATH', xtc_href_link(FILENAME_DEFAULT, 'cPath=' . $current_category_id));
    $result = false;
}

if ($result) {
    $module_smarty->assign('manufacturer', $manufacturer_dropdown);
    $module_smarty->assign('language', $_SESSION['language']);
    $module_smarty->assign('module_content', $module_content);
    $module_smarty->assign('multisort', $multisort_dropdown);

    if (isset($_GET['page']) && $_GET['page'] != '') {
        $page .= '&page=' . $_GET['page'];
    }

    if (PRODUCT_LIST_VIEW_PER_SITE == 'true') {
        $getCols = xtc_db_fetch_array(xtDBquery("SELECT col FROM products_listings WHERE list_name = '" . $list_name . "'"));
        $navigation_per_site = new cseo_navigation;
        $per_site_html = new Smarty;
        $per_site_html->assign('LINKS_PER_SITE', $navigation_per_site->view_per_site('product_listing', $getCols['col'], $per_site));
        $per_site_html->assign('language', $_SESSION['language']);
        $per_site_html->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $products_persite = $per_site_html->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_per_site.html', USE_TEMPLATE_DEVMODE));
        $module_smarty->assign('PRODUCTS_PER_SITE', $products_persite);
    }

    if (PRODUCT_LIST_VIEW_AS == 'true') {
        switch ($list_name) {
            case 'product_listing_list' :
                $views_as = '<a rel="nofollow" href="' . xtc_href_link($file_name, xtc_get_all_get_params(array('products_id', 'x', 'y', 'cat', 'per_site', 'multisort', 'filter_id', 'page', 'view_as')) . 'view_as=product_listing_grid' . $get_param) . '">' . LISTING_GALLERY . '</a> ' . LISTING_LIST_ACTIVE;
                break;
            default :
                $views_as = LISTING_GALLERY_ACTIVE . ' <a rel="nofollow" href="' . xtc_href_link($file_name, xtc_get_all_get_params(array('products_id', 'x', 'y', 'cat', 'per_site', 'multisort', 'filter_id', 'page', 'view_as')) . 'view_as=product_listing_list' . $get_param) . '">' . LISTING_LIST . '</a>';
                break;
        }

        $view = new Smarty;
        $view->assign('LINKS_VIEW_AS', $views_as);
        $view->assign('language', $_SESSION['language']);
        $views = $view->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_view_as.html', USE_TEMPLATE_DEVMODE));
        $module_smarty->assign('PRODUCTS_VIEW_AS', $views);
    }

    $multisort = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_multisort.html', USE_TEMPLATE_DEVMODE));
    $module_smarty->assign('MULTISORT_DROPDOWN', $multisort);

    $manufacturer = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_manufacturer_sort.html', USE_TEMPLATE_DEVMODE));
    $module_smarty->assign('MANUFACTURER_DROPDOWN', $manufacturer);

	//Includes Addon
    if (file_exists(DIR_WS_INCLUDES . 'addons/product_listing_addon_prod.php')) {
        include (DIR_WS_INCLUDES . 'addons/product_listing_addon_prod.php');
    }
    $module_smarty->assign('NAVIGATION', $navigation);
    $module_smarty->assign('CLASS', 'product_listing');
    $module_smarty->assign('ONLY_ONE', $_SESSION['col_special_class']);
    $module_smarty->assign('language', $_SESSION['language']);
    $module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    if (!empty($keywords)) {
        $module_smarty->assign('KEYWORDS', sprintf(SEARCH_RESULTS_WORDS, $keywords, $listing_split->number_of_rows));
    }
    if ($category['listing_template'] == 'default' || $category['listing_template'] == '' || !file_exists(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_listing/' . $category['listing_template'])) {
        $category['listing_template'] = 'product_listings.html';
    }

    if (!CacheCheck()) {
        $module_smarty->caching = false;
        $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/' . $category['listing_template'], USE_TEMPLATE_DEVMODE));
    } else {
        $module_smarty->caching = true;
        $module_smarty->cache_lifetime = CACHE_LIFETIME;
        $module_smarty->cache_modified_check = CACHE_CHECK;
        $cache_id = $current_category_id . '_' . $_SESSION['language'] . '_' . $_SESSION['customers_status']['customers_status_name'] . '_' . $_SESSION['currency'] . '_' . $_GET['manufacturers_id'] . '_' . $_GET['filter_id'] . '_' . $_GET['page'] . '_' . $_GET['keywords'] . '_' . $_GET['categories_id'] . '_' . $_GET['pfrom'] . '_' . $_GET['pto'] . '_' . $_GET['x'] . '_' . $_GET['y'] . '_' . $_GET['multisort'] . '_' . $_GET['manufactures_id'] . '_' . $_SESSION['view_as'] . '_' . $_SESSION['per_site'] . '_' . $_GET['page'];
        $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/' . $category['listing_template'], USE_TEMPLATE_DEVMODE), $cache_id);
    }
    $smarty->assign('main_content', $module);
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
		$cache_id = $_SESSION['language'] . $_SESSION['currency'] . $_SESSION['customer_id'] . 'slider';
		$slider_content = $slider_smarty->fetch(cseo_get_usermod('base/module/slider_content.html', USE_TEMPLATE_DEVMODE), $cache_id);
	}
	$smarty->assign('slider_content', $slider_content);
} else {
    if ($category['listing_template'] == 'default' || $category['listing_template'] == '' || !file_exists(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_listing/' . $category['listing_template'])) {
        $category['listing_template'] = 'product_listings.html';
    }
    $module_smarty->assign('language', $_SESSION['language']);
    $module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);

    if (!CacheCheck()) {
        $module_smarty->caching = false;
        $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/' . $category['listing_template'], USE_TEMPLATE_DEVMODE));
    } else {
        $module_smarty->caching = true;
        $module_smarty->cache_lifetime = CACHE_LIFETIME;
        $module_smarty->cache_modified_check = CACHE_CHECK;
        $cache_id = $current_category_id . '_' . $_SESSION['language'] . '_' . $_SESSION['customers_status']['customers_status_name'] . '_' . $_SESSION['currency'] . '_' . $_GET['manufacturers_id'] . '_' . $_GET['filter_id'] . '_' . $_GET['page'] . '_' . $_GET['keywords'] . '_' . $_GET['categories_id'] . '_' . $_GET['pfrom'] . '_' . $_GET['pto'] . '_' . $_GET['x'] . '_' . $_GET['y'] . '_' . $_GET['multisort'] . '_' . $_GET['manufactures_id'] . '_' . $_SESSION['view_as'] . '_' . $_SESSION['per_site'] . '_' . $_GET['page'];
        $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/' . $category['listing_template'], USE_TEMPLATE_DEVMODE), $cache_id);
    }
	//Includes Addon
    if (file_exists(DIR_WS_INCLUDES . 'addons/product_listing_addon_list.php')) {
        include (DIR_WS_INCLUDES . 'addons/product_listing_addon_list.php');
    }
    $smarty->assign('main_content', $module);
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
		$cache_id = $_SESSION['language'] . $_SESSION['currency'] . $_SESSION['customer_id'] . 'slider';
		$slider_content = $slider_smarty->fetch(cseo_get_usermod('base/module/slider_content.html', USE_TEMPLATE_DEVMODE), $cache_id);
	}
	$smarty->assign('slider_content', $slider_content);

    // $error = TEXT_PRODUCT_NOT_FOUND;
    // include (DIR_WS_MODULES . FILENAME_ERROR_HANDLER);
}
