<?php

/* -----------------------------------------------------------------
 * 	$Id: reviews.php 713 2013-11-04 21:27:13Z akausch $
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
require_once (DIR_FS_INC . 'xtc_word_count.inc.php');
require_once (DIR_FS_INC . 'xtc_date_long.inc.php');
require_once (DIR_FS_INC . 'cseo_get_url_friendly_text.inc.php');

$breadcrumb->add(NAVBAR_TITLE_REVIEWS, xtc_href_link(FILENAME_REVIEWS));
if ($_SESSION['customers_status']['customers_status_read_reviews'] == 0) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

require (DIR_WS_INCLUDES . 'header.php');

if ((isset($_GET['show_pid']) && ($_GET['show_pid'] != ''))) {
    $reviews_query_raw = "select r.reviews_id,
							left(rd.reviews_text, 250) as reviews_text,
							r.reviews_rating,
							r.date_added,
							p.products_id,
							pd.products_name,
							p.products_image,
							r.customers_name
							from " . TABLE_REVIEWS . " r,
							" . TABLE_REVIEWS_DESCRIPTION . " rd,
							" . TABLE_PRODUCTS . " p,
							" . TABLE_PRODUCTS_DESCRIPTION . " pd
							where p.products_status = '1'
							and p.products_id = '" . (int) $_GET['show_pid'] . "'
							and rd.reviews_id = r.reviews_id
							and r.reviews_status = '1'
							and r.products_id = '" . (int) $_GET['show_pid'] . "'
							and pd.products_id = '" . (int) $_GET['show_pid'] . "'
							and pd.language_id = '" . (int) $_SESSION['languages_id'] . "'
							and rd.languages_id = '" . (int) $_SESSION['languages_id'] . "'
							ORDER BY r.reviews_id DESC";
} else {
    $reviews_query_raw = "select r.reviews_id,
							left(rd.reviews_text, 250) as reviews_text,
							r.reviews_rating,
							r.date_added,
							p.products_id,
							pd.products_name,
							p.products_image,
							r.customers_name
							from " . TABLE_REVIEWS . " r,
							" . TABLE_REVIEWS_DESCRIPTION . " rd,
							" . TABLE_PRODUCTS . " p,
							" . TABLE_PRODUCTS_DESCRIPTION . " pd
							where p.products_status = '1'
							and p.products_id = r.products_id
							and r.reviews_id = rd.reviews_id
							and r.reviews_status = '1'
							and p.products_id = pd.products_id
							and pd.language_id = '" . (int) $_SESSION['languages_id'] . "'
							and rd.languages_id = '" . (int) $_SESSION['languages_id'] . "'
							order by r.reviews_id DESC";
}

$reviews_split = new splitPageResults($reviews_query_raw, (int) $_GET['page'], MAX_DISPLAY_NEW_REVIEWS);
if ($reviews_split->number_of_rows > 0) {
    $navigation_smarty = new Smarty;
    $page_links = $reviews_split->getLinksArrayReviews(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array('page', 'info', 'x', 'y')), TEXT_DISPLAY_NUMBER_OF_PRODUCTS, 'reviews');

    $navigation_smarty->assign('LINKS', $page_links);
    $navigation_smarty->assign('language', $_SESSION['language']);
    $navigation_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
    $navigation = $navigation_smarty->fetch(CURRENT_TEMPLATE . '/module/product_navigation/products_page_navigation.html');
}

$module_data = array();
if ($reviews_split->number_of_rows > 0) {
    $reviews_query = xtc_db_query($reviews_split->sql_query);
    while ($reviews = xtc_db_fetch_array($reviews_query)) {
        if ($reviews['products_image'] != '')
            $product_img = DIR_WS_THUMBNAIL_IMAGES . $reviews['products_image'];
        else
            $product_img = DIR_WS_THUMBNAIL_IMAGES . 'no_img.jpg';
        $module_data[] = array('PRODUCTS_IMAGE' => $product_img,
            'PRODUCTS_LINK' => xtc_href_link('review-' . $reviews['reviews_id'] . '/' . cseo_get_url_friendly_text($reviews['products_name']) . '.html'),
            'PRODUCTS_NAME' => $reviews['products_name'],
            'AUTHOR' => $reviews['customers_name'],
            'TEXT' => '(' . sprintf(TEXT_REVIEW_WORD_COUNT, xtc_word_count($reviews['reviews_text'], ' ')) . ')<br />' . htmlspecialchars($reviews['reviews_text']) . '..',
            'RATINGNUM' => $reviews['reviews_rating'],
            'RATING' => xtc_image('templates/' . CURRENT_TEMPLATE . '/img/stars_' . $reviews['reviews_rating'] . '.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])));
    }
    $smarty->assign('module_content', $module_data);
}
$smarty->assign('NAVIGATION', $navigation);
$smarty->assign('BUTTON_BACK', '<a href="javascript:history.back(1)">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
$smarty->assign('REVIEWS_COUNT', $reviews_split->number_of_rows);
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;
$smarty->loadFilter('output', 'note');
$smarty->loadFilter('output', 'trimwhitespace');
if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/reviews.html')) {
	$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/reviews.html', USE_TEMPLATE_DEVMODE));
}else{
	$main_content = $smarty->fetch(cseo_get_usermod('base/module/reviews.html', USE_TEMPLATE_DEVMODE));
}
$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
