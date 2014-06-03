<?php

/* -----------------------------------------------------------------
 * 	$Id: new_products.php 1055 2014-05-16 15:58:42Z akausch $
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

$fsk_lock = '';
if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
    $fsk_lock = ' AND p.products_fsk18 != 1';
}
if (GROUP_CHECK == 'true') {
    $group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " = 1 ";
}

$sorting_data = xtc_db_fetch_array(xtDBquery("SELECT products_sorting, products_sorting2 FROM " . TABLE_CATEGORIES . " WHERE categories_id='" . $current_category_id . "';"));
if ($sorting_data['products_sorting'] == '') {
	$sorting_data['products_sorting'] = 'pd.products_name';
}
$order_str = ' GROUP BY p.products_id ORDER BY ' . $sorting_data['products_sorting'] . ' ' . $sorting_data['products_sorting2'] . ' ';		
if ($_GET['multisort'] == 'specialprice' || $_GET['multisort'] == 'new_asc' || $_GET['multisort'] == 'new_desc' || $_GET['multisort'] == 'name_asc' || $_GET['multisort'] == 'name_desc' || $_GET['multisort'] == 'price_asc' || $_GET['multisort'] == 'price_desc' || $_GET['multisort'] == 'manu_asc' || $_GET['multisort'] == 'manu_desc') {
	switch ($_GET['multisort']) {
		case 'new_asc':
			$order_str = ' GROUP BY p.products_id ORDER BY p.products_date_added ASC';
			break;
		case 'new_desc':
			$order_str = ' GROUP BY p.products_id ORDER BY p.products_date_added DESC';
			break;
		case 'name_asc':
			$order_str = ' GROUP BY p.products_id ORDER BY pd.products_name ASC';
			break;
		case 'name_desc':
			$order_str = ' GROUP BY p.products_id ORDER BY pd.products_name DESC';
			break;
		case 'price_asc':
			$order_str = ' GROUP BY p.products_id ORDER BY p.products_price ASC';
			break;
		case 'price_desc':
			$order_str = ' GROUP BY p.products_id ORDER BY p.products_price DESC';
			break;
		case 'manu_asc':
			$from_str .= ' INNER JOIN ' . TABLE_MANUFACTURERS . ' AS m ON ( p.manufacturers_id = m.manufacturers_id )';
			$order_str = ' GROUP BY p.products_id ORDER BY m.manufacturers_name ASC';
			break;
		case 'manu_desc':
			$from_str .= ' INNER JOIN ' . TABLE_MANUFACTURERS . ' AS m ON ( p.manufacturers_id = m.manufacturers_id )';
			$order_str = ' GROUP BY p.products_id ORDER BY m.manufacturers_name DESC';
			break;
		case 'specialprice':
			$from_str .= " INNER JOIN " . TABLE_SPECIALS . " AS s ON (p.products_id = s.products_id) AND s.status = '1'";
			$order_str = ' GROUP BY p.products_id ORDER BY s.specials_new_products_price DESC';
			break;
		default:
			$order_str = ' GROUP BY p.products_id ORDER BY ' . $sorting_data['products_sorting'] . ' ' . $sorting_data['products_sorting2'] . ' ';
	}
}

if (PRODUCT_LIST_FILTER_SORT == 'true') {
	$multisort_dropdown = xtc_draw_form('multisort', $_SERVER['REQUEST_URI'], 'GET') . "\n";
	$options = array(array('text' => MULTISORT_STANDARD));
	$options[] = array('id' => 'specialprice', 'text' => MULTISORT_SPECIALS_DESC);
	$options[] = array('id' => 'new_desc', 'text' => MULTISORT_NEW_DESC);
	$options[] = array('id' => 'new_asc', 'text' => MULTISORT_NEW_ASC);
	$options[] = array('id' => 'price_asc', 'text' => MULTISORT_PRICE_ASC);
	$options[] = array('id' => 'price_desc', 'text' => MULTISORT_PRICE_DESC);
	$options[] = array('id' => 'name_asc', 'text' => MULTISORT_ABC_AZ);
	$options[] = array('id' => 'name_desc', 'text' => MULTISORT_ABC_ZA);
	$options[] = array('id' => 'manu_asc', 'text' => MULTISORT_MANUFACTURER_ASC);
	$options[] = array('id' => 'manu_desc', 'text' => MULTISORT_MANUFACTURER_DESC);
	$multisort_dropdown .= xtc_draw_pull_down_menu('multisort', $options, $_GET['multisort'], 'onchange="this.form.submit()"') . "\n";
	$multisort_dropdown .= '</form>' . "\n";
	$module_smarty->assign('multisort', $multisort_dropdown);
	$multisort = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_multisort.html', USE_TEMPLATE_DEVMODE));
}
	
if ((!isset($new_products_category_id)) || ($new_products_category_id == '0')) {
    //Startseite neue Produkte
    $site = 'new_products_default';
    $title = NEW_PRODUCTS_DEFAULT;
    $new_products_query = "SELECT 
								p.*,
								pd.*
							FROM
								" . TABLE_PRODUCTS . " p
								LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON(p.products_id = pd.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
							WHERE
								p.products_status = '1' 
							AND
								p.products_startpage = '1'
								" . $group_check . "
								" . $fsk_lock . "
							GROUP BY 
								p.products_id
							ORDER BY 
								p.products_startpage_sort ASC 
							LIMIT " . MAX_DISPLAY_NEW_PRODUCTS;
    $row = 0;
    $module_content = array();
    $new_products_query = xtDBquery($new_products_query);
    while ($new_products = xtc_db_fetch_array($new_products_query, true)) {
        $row++;
        $module_content[] = $product->buildDataArray($new_products, 'thumbnail', $site, $row);
    }
	$display_nav = false;
} else {
    //Produkte anzeigen, die in Kategorien mit Subkategorien sind, aber nicht neu
    if (DISPLAY_SUBCAT_PRODUCTS == 'true') {
        $site = 'new_products';
        $title = SUBCAT_PRODUCTS;
        $new_products_query = "SELECT 
									p.*, 
									pd.*, 
									p2c.*, 
									c.*
								FROM
									" . TABLE_PRODUCTS . " p 
									LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id )
									LEFT OUTER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON (p.products_id = p2c.products_id)
									LEFT OUTER JOIN " . TABLE_CATEGORIES . " c ON (p2c.categories_id = c.categories_id)
									" . $from_str . "
								WHERE 
									c.categories_status = '1'
								AND
								pd.language_id = '" . (int) $_SESSION['languages_id'] . "'
									" . $group_check . "
									" . $fsk_lock . "
								AND 
									c.parent_id = '" . $new_products_category_id . "'
								AND 
									p.products_status = '1' 
								" . $order_str;

        if (isset($_GET['per_site']) && !empty($_GET['per_site']))
            $per_site = $_GET['per_site'];
        elseif (isset($_SESSION['per_site']))
            $per_site = $_SESSION['per_site'];
        elseif (!isset($_SESSION['per_site']) || !isset($_GET['per_site']))
            $per_site = MAX_DISPLAY_SEARCH_RESULTS;

        $_SESSION['per_site'] = $per_site;
        
		$listing_split = new splitPageResults($new_products_query, (int) $_GET['page'], (int) $_SESSION['per_site']);
		
		if ($_GET['view_as'] != '') {
			$site = $_GET['view_as'];
			$_SESSION['view_as'] = $_GET['view_as'];
		} elseif ($_SESSION['view_as'] != '') {
			$site = $_SESSION['view_as'];
		} elseif (!isset($_SESSION['view_as']) || !isset($_GET['view_as'])) {
			$site = 'product_listing_list';
			$_SESSION['view_as'] = 'product_listing_list';
		}

        $module_content = array();
        $listing_query = xtDBquery($listing_split->sql_query);
        $rows = 0;
        while ($new_products = xtc_db_fetch_array($listing_query, true)) {
            $rows++;
            $module_content[] = $product->buildDataArray($new_products, 'thumbnail', $site, $rows);
        }
		$display_nav = true;

    } else {
        //Nur neue Produkte in Kategorien
        $site = 'new_products';
        $title = NEW_PRODUCTS;

        if (MAX_DISPLAY_NEW_PRODUCTS_DAYS != '0') {
            $date_new_products = date("Y.m.d", mktime(1, 1, 1, date(m), date(d) - MAX_DISPLAY_NEW_PRODUCTS_DAYS, date(Y)));
            $days = " AND p.products_date_added > '" . $date_new_products . "' ";
        }

        $new_products_query = "SELECT 
									p.*, 
									pd.*, 
									p2c.*, 
									c.*
								FROM
									" . TABLE_PRODUCTS . " p 
									LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id )
									LEFT OUTER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON (p.products_id = p2c.products_id)
									LEFT OUTER JOIN " . TABLE_CATEGORIES . " c ON (p2c.categories_id = c.categories_id)
								" . $from_str . "
								WHERE 
									c.categories_status='1'
								AND
								pd.language_id = '" . (int) $_SESSION['languages_id'] . "'
									" . $group_check . "
									" . $days . "
									" . $fsk_lock . "
								AND 
									c.parent_id = '" . $new_products_category_id . "'
								AND 
									p.products_status = '1' 
								" . $order_str;

        if (isset($_GET['per_site']) && !empty($_GET['per_site']))
            $per_site = $_GET['per_site'];
        elseif (isset($_SESSION['per_site']))
            $per_site = $_SESSION['per_site'];
        elseif (!isset($_SESSION['per_site']) || !isset($_GET['per_site']))
            $per_site = MAX_DISPLAY_NEW_PRODUCTS;
			
        $_SESSION['per_site'] = $per_site;

        $listing_split = new splitPageResults($new_products_query, (int) $_GET['page'], $_SESSION['per_site']);
		
		if ($_GET['view_as'] != '') {
			$site = $_GET['view_as'];
			$_SESSION['view_as'] = $_GET['view_as'];
		} elseif ($_SESSION['view_as'] != '') {
			$site = $_SESSION['view_as'];
		} elseif (!isset($_SESSION['view_as']) || !isset($_GET['view_as'])) {
			$site = 'product_listing_list';
			$_SESSION['view_as'] = 'product_listing_list';
		}

        $module_content = array();
        $listing_query = xtDBquery($listing_split->sql_query);
        $rows = 0;
        while ($new_products = xtc_db_fetch_array($listing_query)) {
            $rows++;
            $module_content[] = $product->buildDataArray($new_products, 'thumbnail', $site, $rows);
        }
		$display_nav = true;
    }
}

if (sizeof($module_content) >= 1) {
    $module_smarty->assign('language', $_SESSION['language']);
    $module_smarty->assign('module_content', $module_content);
	if ($display_nav) {
		if (($listing_split->number_of_rows > 0)) {
			$navigation_smarty = new Smarty;
			if (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
				$page_links = $listing_split->getSEOLinksArray(MAX_DISPLAY_PAGE_LINKS, $get_param, TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW);
			} else {
				$page_links = $listing_split->getLinksArray(MAX_DISPLAY_PAGE_LINKS, $get_param, TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW);
			}
			$navigation_smarty->assign('COUNT', $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW));
			$navigation_smarty->assign('LINKS', $page_links);
			$navigation_smarty->assign('language', $_SESSION['language']);
			$navigation_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
			$navigation = $navigation_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_page_navigation.html', USE_TEMPLATE_DEVMODE));
			$module_smarty->assign('NAVIGATION', $navigation);
		}
		
		if (PRODUCT_LIST_VIEW_AS == 'true') {
			switch ($site) {
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
		
		if (PRODUCT_LIST_VIEW_PER_SITE == 'true') {
			$getCols = xtc_db_fetch_array(xtDBquery("SELECT col FROM products_listings WHERE list_name = '" . $site . "'"));
			$navigation_per_site = new cseo_navigation;
			$per_site_html = new Smarty;
			$per_site_html->assign('LINKS_PER_SITE', $navigation_per_site->view_per_site('new_products', $getCols['col'], $per_site));
			$per_site_html->assign('language', $_SESSION['language']);
			$per_site_html->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
			$products_persite = $per_site_html->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_per_site.html', USE_TEMPLATE_DEVMODE));
			$module_smarty->assign('PRODUCTS_PER_SITE', $products_persite);
		}
		
		$module_smarty->assign('MULTISORT_DROPDOWN', $multisort);
	}

    $module_smarty->assign('TITLE', $title);
    $module_smarty->assign('CLASS', $site);
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
        $cache_id = $current_category_id . '_' . $_SESSION['language'] . '_' . $_SESSION['customers_status']['customers_status_name'] . '_' . $_SESSION['currency'] . '_' . $_GET['manufacturers_id'] . '_' . $_GET['multisort'] . '_' . $_GET['filter_id'] . '_' . $_GET['page'] . '_' . $_GET['keywords'] . '_' . $_GET['categories_id'] . '_' . $_GET['x'] . '_' . $_GET['y'] . '_' . $_SESSION['view_as'] . '_' . $_SESSION['per_site'] . '_' . $_GET['page'] . 'newprod';
        if (DISPLAY_NEW_PRODUCTS_SLIDE == 'true') {
            $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/main_products.html', USE_TEMPLATE_DEVMODE), $cache_id);
        } else {
            $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE), $cache_id);
        }
    }
    $default_smarty->assign('MODULE_new_products', $module);
}
