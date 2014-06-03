<?php

/* -----------------------------------------------------------------
 * 	$Id: product_navigator.php 844 2014-02-04 15:26:56Z akausch $
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
    $fsk_lock = " AND p.products_fsk18 != '1'";
}
$group_check = "";
if (GROUP_CHECK == 'true') {
    $group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " = '1' ";
}

$path = explode('_', $cPath);
$cat = array_reverse($path);

$sorting_data = xtc_db_fetch_array(xtDBquery("SELECT products_sorting, products_sorting2
                             FROM 
								" . TABLE_CATEGORIES . "
							 WHERE 
							 	categories_id = '" . $cat[0] . "'
                             AND 
                             	categories_status = '1';"));

if ($sorting_data['products_sorting'] == 'pd.products_name') {							
	$table = "INNER JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON(p.products_id = pd.products_id)";
} else {
	$table = "";
}
if ($sorting_data['products_sorting'] != '' || $sorting_data['products_sorting2']) {
	$sorting = ' ORDER BY ' . $sorting_data['products_sorting'] . ' ' . $sorting_data['products_sorting2'] . ' ';
} else {
	$sorting = ' ORDER BY p.products_id';
}
$products_query = xtDBquery("SELECT
                                 pc.products_id 
                             FROM 
								" . TABLE_PRODUCTS_TO_CATEGORIES . " pc
							INNER JOIN
								" . TABLE_PRODUCTS . " p ON(p.products_id = pc.products_id)
								" . $table . "
							 WHERE 
							 	pc.categories_id = '" . $cat[0] . "'
                             AND 
                             	p.products_status=1 
                                 " . $fsk_lock . $group_check . $sorting .";");
$i = 0;
while ($products_data = xtc_db_fetch_array($products_query, true)) {
    $p_data[$i] = array('pID' => $products_data['products_id'], 'pName' => $products_data['products_name']);
    if ($products_data['products_id'] == $product->data['products_id']) {
        $actual_key = $i;
	}
    $i++;
}

// check if array key = first
if ($actual_key == 0) {
    // aktuel key = first product
} else {
    $prev_id = $actual_key - 1;
    $prev_link = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($p_data[$prev_id]['pID'], $p_data[$prev_id]['pName']));
    // check if prev id = first
    if ($prev_id != 0) {
        $first_link = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($p_data[0]['pID'], $p_data[0]['pName']));
	}
}

// check if key = last
if ($actual_key == (sizeof($p_data) - 1)) {
    // actual key is last
} else {
    $next_id = $actual_key + 1;
    $next_link = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($p_data[$next_id]['pID'], $p_data[$next_id]['pName']));
    // check if next id = last
    if ($next_id != (sizeof($p_data) - 1)) {
        $last_link = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($p_data[(sizeof($p_data) - 1)]['pID'], $p_data[(sizeof($p_data) - 1)]['pName']));
	}
}

$module_smarty->assign('FIRST', $first_link);
$module_smarty->assign('PREVIOUS', $prev_link);
$module_smarty->assign('NEXT', $next_link);
$module_smarty->assign('LAST', $last_link);
$module_smarty->assign('ACTUAL_PRODUCT', $actual_key + 1);

$module_smarty->assign('PRODUCTS_COUNT', count($p_data));
$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);

$module_smarty->caching = false;
$product_navigator = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_navigation/product_navigator.html', USE_TEMPLATE_DEVMODE));

$info_smarty->assign('PRODUCT_NAVIGATOR', $product_navigator);
