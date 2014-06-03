<?php

/* -----------------------------------------------------------------
 * 	$Id: product_reviews_info.php 807 2014-01-22 15:54:54Z akausch $
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
// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');
// include needed functions
require_once (DIR_FS_INC . 'xtc_break_string.inc.php');
require_once (DIR_FS_INC . 'xtc_date_short.inc.php');


// lets retrieve all $HTTP_GET_VARS keys and values..
$get_params = xtc_get_all_get_params(array('reviews_id'));
$get_params = substr($get_params, 0, -1); //remove trailing &

$reviews_query = "select rd.reviews_text, r.reviews_rating, r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, p.products_id, pd.products_name, p.products_image from " . TABLE_REVIEWS . " r left join " . TABLE_PRODUCTS . " p on (r.products_id = p.products_id) left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on (p.products_id = pd.products_id and pd.language_id = '" . (int) $_SESSION['languages_id'] . "'), " . TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . (int) $_GET['reviews_id'] . "' and r.reviews_id = rd.reviews_id and p.products_status = '1'";
$reviews_query = xtc_db_query($reviews_query);

if (!xtc_db_num_rows($reviews_query)) {
    xtc_redirect(xtc_href_link(FILENAME_REVIEWS));
}

$reviews = xtc_db_fetch_array($reviews_query);

$breadcrumb->add(NAVBAR_TITLE_PRODUCT_REVIEWS, xtc_href_link(FILENAME_PRODUCT_REVIEWS, $get_params));

xtc_db_query("update " . TABLE_REVIEWS . " set reviews_read = reviews_read+1 where reviews_id = '" . $reviews['reviews_id'] . "'");

$reviews_text = xtc_break_string(htmlspecialchars($reviews['reviews_text']), 60, '-<br />');


$smarty->assign('PRODUCTS_NAME', $reviews['products_name']);
$smarty->assign('AUTHOR', substr($reviews['customers_name'], 0, strrpos($reviews['customers_name'], " ") + 2) . '.');

$smarty->assign('DATE', xtc_date_short($reviews['date_added']));
$smarty->assign('REVIEWS_TEXT', nl2br($reviews_text));
$smarty->assign('RATING', xtc_image('templates/' . CURRENT_TEMPLATE . '/img/stars_' . $reviews['reviews_rating'] . '.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])));
$smarty->assign('RATINGNUM', $reviews['reviews_rating']);
$smarty->assign('PRODUCTS_LINK', xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($reviews['products_id'], $reviews['products_name'])));
$smarty->assign('BUTTON_BACK', '<a href="' . xtc_href_link(FILENAME_PRODUCT_REVIEWS, $get_params) . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
$smarty->assign('PRODUCTS_BUTTON_DETAILS', '<a href="' . xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($reviews['products_id'], $reviews['products_name'])) . '">' . xtc_image_button('button_details.gif', 'Details') . '</a>');
$smarty->assign('IMAGE', xtc_image(DIR_WS_THUMBNAIL_IMAGES . $reviews['products_image'], $reviews['products_name']));

require (DIR_WS_INCLUDES . 'header.php');
$smarty->assign('language', $_SESSION['language']);

// set cache ID
if (!CacheCheck()) {
    $smarty->caching = false;
    $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_reviews_info.html', USE_TEMPLATE_DEVMODE));
} else {
    $smarty->caching = true;
    $smarty->cache_lifetime = CACHE_LIFETIME;
    $smarty->cache_modified_check = CACHE_CHECK;
    $cache_id = $_SESSION['language'] . $reviews['reviews_id'] . '-reviewinfo';
    $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_reviews_info.html', USE_TEMPLATE_DEVMODE), $cache_id);
}
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$smarty->loadFilter('output', 'note');
$smarty->loadFilter('output', 'trimwhitespace');
$smarty->assign('main_content', $main_content);

$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));

include ('includes/application_bottom.php');
