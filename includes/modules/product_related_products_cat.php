<?php

/* -----------------------------------------------------------------
 * 	$Id: product_related_products_cat.php 522 2013-07-24 11:44:51Z akausch $
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

if ($_SESSION['customers_status']['customers_fsk18_display'] == '0')
    $fsk_lock = ' AND p.products_fsk18 != 1';

if (GROUP_CHECK == 'true')
    $group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " = 1 ";

$path = explode('_', $cPath);
$cat = array_reverse($path);

$products_query = xtDBquery("SELECT p.products_id, p.products_image, p.products_slave_in_list, p.products_master, pd.products_name
								FROM  " . TABLE_PRODUCTS . " AS p
								JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS pc ON (p.products_id = pc.products_id)									
								JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON (p.products_id = pd.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
								WHERE pc.categories_id='" . $cat[0] . "'
								AND p.products_status = 1
								AND (p.products_slave_in_list = '1' OR p.products_master = '1' OR ((p.products_slave_in_list = '0' OR p.products_slave_in_list = '') AND (p.products_master_article = '' OR p.products_master_article = '0')))
								AND p.products_id <> '" . $product->data['products_id'] . "'
								" . $fsk_lock . $group_check . "
								ORDER BY rand() LIMIT " . PRODUCT_DETAILS_RELATED_RAND);

$product_related_products_cat = array();

while ($products_data = xtc_db_fetch_array($products_query)) {
	if ($products_data['products_image'] != '') {
		if(substr($products_data['products_image'],'0','7') == 'http://') {
			$img = str_replace('images/','images/',$products_data['products_image']);
			$img = '<img src="'.$img.'" alt="'.$products_data['products_name'].'" style="max-width:'.PRODUCT_IMAGE_MINI_WIDTH.'px;height:auto;">';
		} elseif (substr($products_data['products_image'],'0','8') == 'https://') {
			$img = str_replace('images/','images/',$products_data['products_image']);
			$img = '<img src="'.$img.'" alt="'.$products_data['products_name'].'" style="max-width:'.PRODUCT_IMAGE_MINI_WIDTH.'px;height:auto;">';
		} else {
			$img = xtc_image(DIR_WS_MINI_IMAGES . $products_data['products_image'], $products_data['products_name']);
		}
    } else {
        $img = xtc_image(DIR_WS_MINI_IMAGES . 'no_img.jpg', $products_data['products_name']);
    }

    $product_related_products_cat[] = array(
        'pID' => $products_data['products_id'],
        'pName' => $products_data['products_name'],
        'pImage' => $img,
        'prdlink' => xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($products_data['products_id'])));
}


$products_query = xtDBquery("SELECT pc.products_id, p.products_image, p.products_slave_in_list, p.products_master, pd.products_name
								FROM " . TABLE_PRODUCTS . " AS p
								JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS pc ON (p.products_id = pc.products_id)									
								JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON (p.products_id = pd.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
								WHERE p.products_id <> '" . $product->data['products_id'] . "'
								AND p.products_status = 1
								AND (p.products_slave_in_list = '1' OR p.products_master = '1' OR ((p.products_slave_in_list = '0' OR p.products_slave_in_list = '') AND (p.products_master_article = '' OR p.products_master_article = '0')))
								" . $fsk_lock . $group_check . "
								ORDER BY rand() LIMIT " . PRODUCT_DETAILS_RELATED_RAND);

$product_related_products_all = array();

while ($products_data = xtc_db_fetch_array($products_query, true)) {
	if ($products_data['products_image'] != '') {
		if(substr($products_data['products_image'],'0','7') == 'http://') {
			$img = str_replace('images/','images/',$products_data['products_image']);
			$img = '<img src="'.$img.'" alt="'.$products_data['products_name'].'" style="max-width:'.PRODUCT_IMAGE_MINI_WIDTH.'px;height:auto;">';
		} elseif (substr($products_data['products_image'],'0','8') == 'https://') {
			$img = str_replace('images/','images/',$products_data['products_image']);
			$img = '<img src="'.$img.'" alt="'.$products_data['products_name'].'" style="max-width:'.PRODUCT_IMAGE_MINI_WIDTH.'px;height:auto;">';
		} else {
			$img = xtc_image(DIR_WS_MINI_IMAGES . $products_data['products_image'], $products_data['products_name']);
		}
    } else {
        $img = xtc_image(DIR_WS_MINI_IMAGES . 'no_img.jpg', $products_data['products_name']);
    }
    $product_related_products_all[] = array(
        'pID' => $products_data['products_id'],
        'pName' => $products_data['products_name'],
        'pImage' => $img,
        'prdlink' => xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($products_data['products_id'])));
}

$module_smarty->assign('PRODUCT_RELATED_CAT', $product_related_products_cat);
$module_smarty->assign('PRODUCT_RELATED_CAT_ALL', $product_related_products_all);
$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$module_smarty->caching = false;

//Hookpoint
$cseo_account = cseohookfactory::create_object('ProductRelatedCat');
$cseo_account->proceed();
$cseo_account->get_response();

$product_related_products_cat = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_related_products_cat.html', USE_TEMPLATE_DEVMODE));

$info_smarty->assign('MODUL_product_related_cat', $product_related_products_cat);
