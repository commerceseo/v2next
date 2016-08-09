<?php

/* -----------------------------------------------------------------
 * 	$Id: products_item_main.php 522 2013-07-24 11:44:51Z akausch $
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

$items = array();
if (GROUP_CHECK == 'true') {
	$group_check = "AND group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
}

// Abfrage der einzelnen Blogbeitraege aller Kategorien
$select_items = xtc_db_fetch_array(xtDBquery("SELECT * 
									FROM " . TABLE_BLOG_ITEMS . " 
									WHERE status = 2  
									AND item_id = " . (int)$mytext['bitem_id'] . "
									AND language_id = '" . (int)$_SESSION['languages_id'] . "' ".$group_check."
									GROUP BY item_id;"));

    $kommentare_query = xtDBquery("SELECT id FROM blog_comment WHERE blog_id = '" . (int)$select_items['id'] . "' GROUP BY blog_id;");
    $kommentare = xtc_db_num_rows($kommentare_query);
	$t_item_url = xtc_href_link(FILENAME_BLOG . '?blog_cat=' . (int) $select_items['categories_id'] . '&blog_item=' . (int) $select_items['item_id']);

    $items[$mytext['bitem_id']] = array('title' => $select_items['title'],
        'name' => $select_items['name'],
        'shortdesc' => $select_items['shortdesc'],
        'kommentare' => $kommentare,
        'date' => $select_items['date'],
        'blog_link' => $t_item_url);

if (sizeof($items) >= 1) {
	$smarty->assign('language', $_SESSION['language']);
    $smarty->assign('blog_items', $items);
	if (!CacheCheck()) {
		$smarty->caching = false;
		$itemmodule = $smarty->fetch(cseo_get_usermod('base/module/products_item.html', USE_TEMPLATE_DEVMODE));
	} else {
		$smarty->caching = true;
		$smarty->cache_lifetime = CACHE_LIFETIME;
		$smarty->cache_modified_check = CACHE_CHECK;
		$cache_id = $select_items['id'] . $_SESSION['language'] . $_SESSION['customers_status']['customers_status_name'] . $_SESSION['currency'] . 'priteman';
		$itemmodule = $smarty->fetch(cseo_get_usermod('base/module/products_item.html', USE_TEMPLATE_DEVMODE), $cache_id);
	}
}
