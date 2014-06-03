<?php

/* -----------------------------------------------------------------
 * 	$Id: main_categories_list.php 873 2014-03-25 16:42:10Z akausch $
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

$module_smarty = new smarty;
$module_content = '';

if (!CacheCheck()) {
    $cache = false;
    $module_smarty->caching = false;
} else {
    $cache = true;
    $module_smarty->caching = true;
    $module_smarty->cache_lifetime = CACHE_LIFETIME;
    $module_smarty->cache_modified_check = CACHE_CHECK;
    $cache_id = $_SESSION['language'] . $_SESSION['customers_status']['customers_status_id'] . $cPath . '_maincat';
}
if (!$module_smarty->isCached(CURRENT_TEMPLATE . '/module/categories_list.html', $cache_id) || !$cache) {
    $module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
    $categories_string = '';

    if (GROUP_CHECK == 'true') {
        $group_check = "c.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1  and";
    }

    $categories_query = xtDBquery("SELECT
							*
						FROM
							" . TABLE_CATEGORIES . " c
						LEFT JOIN 
							" . TABLE_CATEGORIES_DESCRIPTION . " cd ON(c.categories_id = cd.categories_id AND cd.language_id = '" . (int) $_SESSION['languages_id'] . "')
						WHERE
							c.parent_id = '0' 
						AND
							" . $group_check . " 
							c.categories_status = '1' 
						ORDER BY
							c.sort_order ASC");

    while ($categories = xtc_db_fetch_array($categories_query, true)) {

        if ($categories['categories_main_status'] == '1') {
            if ($categories['categories_image'] != '' && CATEGORY_LISTING_START_PICTURE == 'true') {
                $image = xtc_image(DIR_WS_IMAGES . 'categories/' . $categories['categories_image'], ($categories['categories_pic_alt'] != '' ? $categories['categories_pic_alt'] : $categories['categories_name']), ($categories['categories_heading_title'] != '' ? $categories['categories_heading_title'] : $categories['categories_name']));
                if (!file_exists(DIR_WS_IMAGES . 'categories/' . $categories['categories_image']))
                    $image = xtc_image(DIR_WS_IMAGES . 'categories/noimage.gif', ($categories['categories_pic_alt'] != '' ? $categories['categories_pic_alt'] : $categories['categories_name']), ($categories['categories_heading_title'] != '' ? $categories['categories_heading_title'] : $categories['categories_name']));
            } else {
                $image = '';
            }
            if (CATEGORY_LISTING_START_HEAD == 'true') {
                $catname = $categories['categories_name'];
            } else {
                $catname = '';
            }
            if (CATEGORY_LISTING_START_DESCR == 'true') {
                if ($categories['categories_short_description'] != '') {
					$catdesc = $categories['categories_short_description'];
                } else {
					$catdesc = cseo_truncate($categories['categories_description'], 121);
                }
            } else {
                $catdesc = '';
            }

            $category_link = xtc_category_link($categories['categories_id'], $categories['categories_name']);
            $module_content[] = array('CATEGORY_NAME' => $catname,
                'CATEGORY_IMAGE' => $image,
                'CATEGORY_LINK' => xtc_href_link(FILENAME_DEFAULT, xtc_get_all_get_params(array(array('cat', 'page', 'filter_id', 'manufacturers_id'))) . $category_link),
                'CATEGORY_DESCRIPTION' => $catdesc);
        }
    }

    $module_smarty->assign('module_content', $module_content);
}
$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);

if (!$cache) {
    $module_categories = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/categories_list.html', USE_TEMPLATE_DEVMODE));
} else {
    $module_categories = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/categories_list.html', USE_TEMPLATE_DEVMODE), $cache_id);
}
$default_smarty->assign('CATEGORIES_LIST', $module_categories);
