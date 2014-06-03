<?php

/* -----------------------------------------------------------------
 * 	$Id: taglisting.php 937 2014-04-04 18:14:33Z akausch $
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
$breadcrumb->add(NAVBAR_TITLE_TAGLIST, xtc_href_link('taglisting.php', '', 'SSL'));

include ('includes/header.php');

$result = true;
$_GET['tag'] = stripslashes(trim(urldecode($_GET['tag'])));
if (isset($_GET['tag']) && ($_GET['tag'] != '')) {

    $fsk_lock = '';
    if ($_SESSION['customers_status']['customers_fsk18_display'] == '0')
        $fsk_lock = ' and p.products_fsk18!=1';

    $group_check = '';
    if (GROUP_CHECK == 'true')
        $group_check = " and p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";

    $listing_sql = "SELECT DISTINCT 
						p.*,
						pd.*,
						m.*,
						t2p.pID,
						t2p.tag
					FROM
						tag_to_product t2p
					LEFT JOIN
						" . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id = t2p.pID
					LEFT JOIN
						" . TABLE_PRODUCTS . " p ON p.products_id = t2p.pID
						LEFT JOIN
						" . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id
						LEFT JOIN
						" . TABLE_SPECIALS . " s ON p.products_id = s.products_id
					WHERE
						p.products_status = '1'
					AND
						t2p.tag = '" . urldecode($_GET['tag']) . "'
						   " . $group_check . "
						   " . $fsk_lock . "
					AND
						pd.language_id = '" . (int) $_SESSION['languages_id'] . "'";

    $getCount = xtc_db_fetch_array(xtDBquery("SELECT
												COUNT(pID) AS anzahl
											FROM
												tag_to_product
											WHERE
												tag = '" . $_GET['tag'] . "'"));

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
        $page_links = $listing_split->getLinksArrayTag(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array('page', 'tag', 'info', 'x', 'y', (MODULE_COMMERCE_SEO_INDEX_STATUS == 'True' ? 'cPath' : ''), 'cat', 'per_site', 'view_as')), TEXT_DISPLAY_NUMBER_OF_PRODUCTS, '', $_GET['tag']);
        $navigation_smarty->assign('LINKS', $page_links);
        $navigation_smarty->assign('language', $_SESSION['language']);
        $navigation_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
        $navigation_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $navigation = $navigation_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_page_navigation.html', USE_TEMPLATE_DEVMODE));
        $smarty->assign('NAVIGATION', $navigation);
    }

    $module_content = array();
    $listing_query = xtDBquery($listing_split->sql_query);
    $rows = 0;
    while ($tag = xtc_db_fetch_array($listing_query, true)) {
        $rows++;
        $module_content[] = $product->buildDataArray($tag, 'thumbnail', $list_name, $rows);
    }

    if (PRODUCT_LIST_VIEW_PER_SITE == 'true') {
        $getCols = xtc_db_fetch_array(xtDBquery("SELECT col FROM products_listings WHERE list_name = 'tagcloud'"));
        $navigation_per_site = new cseo_navigation;
        $per_site_html = new Smarty;
        $per_site_html->assign('LINKS_PER_SITE', $navigation_per_site->view_per_site('tagcloud', $getCols['col'], $per_site));
        $per_site_html->assign('language', $_SESSION['language']);
        $per_site_html->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $products_persite = $per_site_html->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/products_per_site.html', USE_TEMPLATE_DEVMODE));
        $smarty->assign('PRODUCTS_PER_SITE', $products_persite);
    }

    //Nur wenn auch Treffer da sind, sonst 404
    if ($getCount['anzahl'] > 0) {
        $smarty->assign('TAG_COUNT', TEXT_TAG_TREFFER1 . $getCount['anzahl'] . TEXT_TAG_TREFFER2);
        $smarty->assign('TITLE', TEXT_TAG_HEAD . $_GET['tag']);
    } else {
        header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
        header('Status: 404 Not Found');
        header('Content-type: text/html');
    }

    $smarty->assign('language', $_SESSION['language']);
    $smarty->assign('CLASS', 'tagcloud');
    $smarty->assign('module_content', $module_content);
    $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    if (!CacheCheck()) {
        $smarty->caching = false;
        $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE));
    } else {
        $smarty->caching = true;
        $smarty->cache_lifetime = CACHE_LIFETIME;
        $smarty->cache_modified_check = CACHE_CHECK;
        $cache_id = $_SESSION['language'] . '_' . $_SESSION['customers_status']['customers_status_name'] . '_' . $_SESSION['currency'] . '-taglisting';
        $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE), $cache_id);
    }
    $smarty->assign('main_content', $main_content);
    $smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
    include ('includes/application_bottom.php');
} else {
    $error = TEXT_TAG_NOT_FOUND;
    include (DIR_WS_MODULES . FILENAME_ERROR_HANDLER);

    $smarty->assign('error', $error);

    function kshuffle2(&$array) {
        if (!is_array($array) || empty($array))
            return false;
        $tmp = array();
        foreach ($array as $key => $value)
            $tmp[] = array('k' => $key, 'v' => $value);

        shuffle($tmp);
        $array = array();
        foreach ($tmp as $entry)
            $array[$entry['k']] = $entry['v'];
        return true;
    }

    function printTagCloud2($tags) {

        kshuffle2($tags); // Zufaellige Anzeige

        $max_size = 32; // max font size in pixels
        $min_size = 12; // min font size in pixels

        $max_qty = max(array_values($tags));
        $min_qty = min(array_values($tags));

        $spread = $max_qty - $min_qty;
        if ($spread == 0)
            $spread = 1;

        $step = ($max_size - $min_size) / ($spread);

        foreach ($tags as $key => $value) {
            $size = round($min_size + (($value - $min_qty) * $step));
            $cloud .= '<a href="' . xtc_href_link('tag/' . urlencode($key) . '/') . '" style="color:#' . mt_rand(000000, 999999) . ';font-size:' . $size . 'px;" title="' . $value . ' Produkte wurden mit ' . $key . ' getagged" rel="follow">' . $key . '</a> ';
        }
        return $cloud;
    }

    $data_query = xtDBquery("SELECT
									tag, count(tag) AS tag_anzahl
								FROM
									tag_to_product
								WHERE
									lID = '" . $_SESSION['languages_id'] . "'
								GROUP BY
									tag ");

    if (xtc_db_num_rows($data_query)) {
        $tag_array = array();
        while ($data = xtc_db_fetch_array($data_query)) {
            if (!empty($data))
                $tag_array[$data['tag']] = $data['tag_anzahl'];
        }
    }
    if (is_array($tag_array))
        $tag_cloud = printTagCloud2($tag_array);
    $smarty->loadFilter('output', 'note');
    $smarty->loadFilter('output', 'trimwhitespace');
    $smarty->assign('language', $_SESSION['language']);
    $smarty->assign('TITLE', 'Tagcloud');
    $smarty->assign('CLASS', 'tagcloud');
    $smarty->assign('module_content', $tag_cloud);
    $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $smarty->caching = false;
    $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/taglistings.html', USE_TEMPLATE_DEVMODE));
    $smarty->assign('main_content', $main_content);
    $smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
    include ('includes/application_bottom.php');
}
