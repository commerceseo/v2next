<?php

/* -----------------------------------------------------------------
 * 	$Id: hashtags.php 940 2014-04-05 10:24:17Z akausch $
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
$smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');

require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

$error = 0; // reset error flag to false
$breadcrumb->add('Hashtag', xtc_href_link('hashtags.php', '', 'SSL'));

include ('includes/header.php');

$result = true;
$_GET['hashtags'] = stripslashes(trim(urldecode($_GET['hashtags'])));
if (isset($_GET['hashtags']) && ($_GET['hashtags'] != '')) {

    $fsk_lock = '';
    if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
        $fsk_lock = ' AND p.products_fsk18!=1';
    }

    $group_check = '';
    if (GROUP_CHECK == 'true') {
        $group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
    }

    $listing_sql = "SELECT *
					FROM
						" . TABLE_PRODUCTS . " AS p
					LEFT JOIN
						" . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON(pd.products_id = p.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
					WHERE
						p.products_status = '1'
					AND
						pd.products_name LIKE  '%" . $_GET['hashtags'] . "%'
					OR 
						pd.products_description LIKE  '%" . $_GET['hashtags'] . "%'
						   " . $group_check . "
						   " . $fsk_lock . "
					";

    if (isset($_GET['per_site']) && !empty($_GET['per_site']))
        $per_site = $_GET['per_site'];
    elseif (isset($_SESSION['per_site']))
        $per_site = $_SESSION['per_site'];
    elseif (!isset($_SESSION['per_site']) || !isset($_GET['per_site']))
        $per_site = MAX_DISPLAY_SEARCH_RESULTS;

    $_SESSION['per_site'] = $per_site;

    $listing_split = new splitPageResults($listing_sql, (int) $_GET['page'], (int) $_SESSION['per_site'], 'p.products_id');

    $list_name = 'tagcloud';

    if (($listing_split->number_of_rows > 0)) {
        $navigation_smarty = new Smarty;
        $page_links = $listing_split->getLinksArrayHashtag(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array('page', 'hashtags', 'info', 'x', 'y', (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True' ? 'cPath' : ''), 'cat', 'per_site', 'view_as')), TEXT_DISPLAY_NUMBER_OF_PRODUCTS, '', $_GET['hashtags']);
        $navigation_smarty->assign('LINKS', $page_links);
        $navigation_smarty->assign('language', $_SESSION['language']);
        $navigation_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
        $navigation_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $navigation = $navigation_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_page_navigation.html', USE_TEMPLATE_DEVMODE));
    } else {
        header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
        header('Status: 404 Not Found');
        header('Content-type: text/html');
        $error = 'Hashtag not found';
        include (DIR_WS_MODULES . FILENAME_ERROR_HANDLER);
        $smarty->assign('error', $error);
    }
    $module_content = array();
    $listing_query = xtDBquery($listing_split->sql_query);
    $rows = 0;
    while ($tag = xtc_db_fetch_array($listing_query, true)) {
        $rows++;
        $module_content[] = $product->buildDataArray($tag, 'thumbnail', $list_name, $rows);
    }

    if (PRODUCT_LIST_VIEW_PER_SITE == 'true') {
        $getCols = xtc_db_fetch_array(xtDBquery("SELECT col FROM products_listings WHERE list_name = '" . $list_name . "'"));
        $navigation_per_site = new cseo_navigation;
        $per_site_html = new Smarty;
        $per_site_html->assign('LINKS_PER_SITE', $navigation_per_site->view_per_site('hashtags', $getCols['col'], $per_site));
        $per_site_html->assign('language', $_SESSION['language']);
        $per_site_html->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $products_persite = $per_site_html->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_per_site.html', USE_TEMPLATE_DEVMODE));
        $smarty->assign('PRODUCTS_PER_SITE', $products_persite);
    }


    $smarty->assign('NAVIGATION', $navigation);

    $smarty->assign('CLASS', 'hashtag');
    $smarty->assign('module_content', $module_content);
    $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $smarty->assign('language', $_SESSION['language']);
    if (!CacheCheck()) {
        $smarty->caching = false;
        $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE));
    } else {
        $smarty->caching = true;
        $smarty->cache_lifetime = CACHE_LIFETIME;
        $smarty->cache_modified_check = CACHE_CHECK;
        $cache_id = $_SESSION['language'] . '_' . $_SESSION['customers_status']['customers_status_name'] . '_' . $_SESSION['currency'] . '-hashtag';
        $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE), $cache_id);
    }
    $smarty->loadFilter('output', 'note');
    $smarty->loadFilter('output', 'trimwhitespace');
    $smarty->assign('main_content', $main_content);
    $smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
    include ('includes/application_bottom.php');
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    header('Status: 404 Not Found');
    header('Content-type: text/html');
    $error = 'Hashtag not found';
    include (DIR_WS_MODULES . FILENAME_ERROR_HANDLER);

    $smarty->assign('error', $error);
    $smarty->loadFilter('output', 'trimwhitespace');
    $smarty->assign('TITLE', 'Hashtag');
    $smarty->assign('CLASS', 'hashtag');
    $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $smarty->assign('language', $_SESSION['language']);
    $smarty->caching = false;
    $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/taglistings.html', USE_TEMPLATE_DEVMODE));
    $smarty->assign('main_content', $main_content);
    $smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
    include ('includes/application_bottom.php');
}
