<?php

/* -----------------------------------------------------------------
 * 	$Id: specials.php 937 2014-04-04 18:14:33Z akausch $
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

include ('includes/application_top.php');
$smarty = new Smarty;

require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

$breadcrumb->add(NAVBAR_TITLE_SPECIALS, xtc_href_link(FILENAME_SPECIALS));

if ($_SESSION['customers_status']['customers_fsk18_display'] == '0')
    $fsk_lock = ' AND p.products_fsk18 != 1';

if (GROUP_CHECK == 'true')
    $group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " = 1 ";

$specials_query_raw = "SELECT 
						p.*,
                        pd.*,
                        s.specials_new_products_price 
					FROM 
						" . TABLE_PRODUCTS . " p
					LEFT JOIN
						" . TABLE_PRODUCTS_DESCRIPTION . " pd ON(p.products_id = pd.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "') 
					LEFT JOIN
						" . TABLE_SPECIALS . " s ON(s.products_id = p.products_id)
                    WHERE 
						p.products_status = '1'
                        " . $group_check . "
                        " . $fsk_lock . "
                    AND 
						s.status = '1' 
					ORDER BY 
						s.specials_date_added DESC";

if (isset($_GET['per_site']) && !empty($_GET['per_site']))
    $per_site = $_GET['per_site'];
elseif (isset($_SESSION['per_site']))
    $per_site = $_SESSION['per_site'];
elseif (!isset($_SESSION['per_site']) || !isset($_GET['per_site']))
    $per_site = MAX_DISPLAY_SEARCH_RESULTS;

$_SESSION['per_site'] = $per_site;

$listing_split = new splitPageResults($specials_query_raw, $_GET['page'], (int) $_SESSION['per_site']);
if (($listing_split->number_of_rows > 0)) {
    $navigation_smarty = new Smarty;
    $page_links = $listing_split->getLinksArraySpecials(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array('page', 'info', 'x', 'y')), TEXT_DISPLAY_NUMBER_OF_PRODUCTS);
    $navigation_smarty->assign('COUNT', $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_SPECIALS));
    $navigation_smarty->assign('LINKS', $page_links);
    $navigation_smarty->assign('language', $_SESSION['language']);
    $navigation_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
    $navigation_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $navigation = $navigation_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_page_navigation.html', USE_TEMPLATE_DEVMODE));
    $smarty->assign('NAVIGATION', $navigation);
}


$file_name = FILENAME_SPECIALS;

if (PRODUCT_LIST_VIEW_PER_SITE == 'true') {
    $getCols = xtc_db_fetch_array(xtDBquery("SELECT col FROM products_listings WHERE list_name = 'specials'"));
    $navigation_per_site = new cseo_navigation;
    $per_site_html = new Smarty;
    $per_site_html->assign('LINKS_PER_SITE', $navigation_per_site->view_per_site('specials', $getCols['col'], $per_site));
    $per_site_html->assign('language', $_SESSION['language']);
    $per_site_html->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $products_persite = $per_site_html->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_per_site.html', USE_TEMPLATE_DEVMODE));
    $smarty->assign('PRODUCTS_PER_SITE', $products_persite);
}

$module_content = array();
$row = 0;
if ($listing_split->number_of_rows == 0)
    xtc_redirect(xtc_href_link(FILENAME_DEFAULT));

require_once (DIR_WS_INCLUDES . 'header.php');
$specials_query = xtc_db_query($listing_split->sql_query);
$i = 0;
while ($specials = xtc_db_fetch_array($specials_query)) {
    $i++;
    $module_content[] = $product->buildDataArray($specials, 'thumbnail', 'specials', $i);
}

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('module_content', $module_content);
$smarty->assign('TITLE', SPECIALS);
$smarty->assign('sonderangebote', 'true');
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
if (!CacheCheck()) {
    $smarty->caching = false;
    $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE));
} else {
    $smarty->caching = true;
    $smarty->cache_lifetime = CACHE_LIFETIME;
    $smarty->cache_modified_check = CACHE_CHECK;
    $cache_id = $_SESSION['language'] . '_' . $_SESSION['customers_status']['customers_status_name'] . '_' . $_SESSION['currency'] . '-specials';
    $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE), $cache_id);
}

$smarty->assign('main_content', $main_content);
$smarty->loadFilter('output', 'note');
$smarty->loadFilter('output', 'trimwhitespace');
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));

include ('includes/application_bottom.php');
