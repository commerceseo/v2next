<?php

/* -----------------------------------------------------------------
 * 	$Id: products_cat_main.php 522 2013-07-24 11:44:51Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	06.03.2014 www.indiv-style.de Copyright by H&S eCom 
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

if (GROUP_CHECK == 'true') {
	$group_check = "AND c.group_permission_" . (int) $_SESSION['customers_status']['customers_status_id'] . " = '1' ";
}

$category = xtc_db_fetch_array(xtDBquery("SELECT cd.*, c.* 
					FROM " . TABLE_CATEGORIES . " AS c 
					JOIN " . TABLE_CATEGORIES_DESCRIPTION . " AS cd ON(cd.categories_id = c.categories_id AND cd.language_id = '" . (int) $_SESSION['languages_id'] . "')
					WHERE c.categories_id = '".(int)$mytext['kat_id']."'
						" . $group_check . " GROUP BY c.categories_id;"));
 
if ($category['categories_blogs'] != 0) {
		$category_link = xtc_href_link(FILENAME_BLOG, 'blog_cat=' . $category['categories_blogs']);
} elseif ($category['categories_contents'] != 0) {
		$mygrid = xtc_db_fetch_array(xtc_db_query("SELECT content_group FROM ".TABLE_CONTENT_MANAGER." WHERE content_id = '" . $category['categories_contents'] . "' "));
		$category_link = xtc_href_link(FILENAME_CONTENT, 'coID=' . $mygrid['content_group']);
} elseif ($category['categories_blogs'] == 0 && $category['categories_contents'] == 0) {
		$t_category_link = xtc_category_link($mytext['kat_id'], $category['categories_name'], true);
		$category_link = xtc_href_link(FILENAME_DEFAULT, $t_category_link);
}
$image = '';
if ($category['categories_image'] != '') {
	$image = xtc_image(DIR_WS_IMAGES . 'categories/' . $category['categories_image'], $category['categories_name'], $category['categories_title']);
}
$smarty->assign('CATEGORIES_LINK',$category_link);
$smarty->assign('CATEGORIES_NAME', $category['categories_name']);
$smarty->assign('CATEGORIES_HEADING_TITLE', $category['categories_heading_title']);
$smarty->assign('CATEGORIES_IMAGE', $image);
$smarty->assign('CATEGORIES_DESCRIPTION', $category['categories_description']);

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_catend', $category_content);

if (!CacheCheck()) {
	$smarty->caching = false;
	$main_kcatend = $smarty->fetch(cseo_get_usermod('base/module/products_cat_main.html', USE_TEMPLATE_DEVMODE));
} else {
	$smarty->caching = true;
	$smarty->cache_lifetime = CACHE_LIFETIME;
	$smarty->cache_modified_check = CACHE_CHECK;
	$cache_id = $category['categories_id'] . $_SESSION['language'] . $_SESSION['customers_status']['customers_status_name'] . $_SESSION['currency'] . 'procatmain';
	$main_kcatend = $smarty->fetch(cseo_get_usermod('base/module/products_cat_main.html', USE_TEMPLATE_DEVMODE), $cache_id);
}