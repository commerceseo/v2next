<?php

/* -----------------------------------------------------------------
 * 	$Id: popup_coupon_help.php 522 2013-07-24 11:44:51Z akausch $
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

// POSITION DER SCHWEBENDEN GUTSCHEIN INFOS
$popup_pos_left = 300;
$popup_pos_top = 100;

// START SMARTY
$module_smarty = new Smarty;
$module_smarty->assign('language', $_SESSION['language']);
global $xtPrice;
$restricted_categories = '';
$restricted_products = '';

// INFOS ÜBER KUPON AUSLESEN
$coupon_query = xtc_db_query("SELECT * FROM " . TABLE_COUPONS . " WHERE coupon_id = '" . (int) $_SESSION['cc_id'] . "' LIMIT 1");
$coupon = xtc_db_fetch_array($coupon_query);
// echo '<pre>';
// print_r($coupon);
// echo '</pre>';
// RABATT TYP
if ($coupon['coupon_type'] == 'F') {
    $module_smarty->assign('COUPON_TYPE', COUPON_TYPE_F);
} else if ($coupon['coupon_type'] == 'P') {
    $module_smarty->assign('COUPON_TYPE', COUPON_TYPE_P);
} else if ($coupon['coupon_type'] == 'S') {
    $module_smarty->assign('COUPON_TYPE', COUPON_TYPE_S);
}

// RABATT CODE
$module_smarty->assign('COUPON_CODE', $coupon['coupon_code']);

// RABATT WERT
if ($coupon['coupon_type'] == 'P') {
    $module_smarty->assign('COUPON_AMOUNT', round($coupon['coupon_amount'], 2) . ' %');
} else if ($coupon['coupon_type'] == 'F') {
    $module_smarty->assign('COUPON_AMOUNT', $xtPrice->xtcFormat($coupon['coupon_amount'], true));
} else if ($coupon['coupon_type'] == 'S') {
    $module_smarty->assign('COUPON_AMOUNT', SHIPPING_COSTS);
}

// NAME UND BESCHREIBUNG DES RABATTS
$coupon_desc_query = xtc_db_query("SELECT coupon_description, coupon_name FROM " . TABLE_COUPONS_DESCRIPTION . " WHERE coupon_id = '" . (int) $coupon['coupon_id'] . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "' LIMIT 1");
$coupon_desc = xtc_db_fetch_array($coupon_desc_query);
if ($coupon_desc['coupon_name'] != '') {
    $module_smarty->assign('COUPON_NAME', $coupon_desc['coupon_name']);
}
if ($coupon_desc['coupon_description'] != '') {
    $module_smarty->assign('COUPON_DESCRIPTION', $coupon_desc['coupon_description']);
}

// START- UND ABLAUF-DATUM
require_once (DIR_FS_INC . 'xtc_date_short.inc.php');
$module_smarty->assign('COUPON_START_DATE', xtc_date_short($coupon['coupon_start_date']));
$module_smarty->assign('COUPON_FINISH_DATE', xtc_date_short($coupon['coupon_expire_date']));

// MINDESTBESTELLMENGE
if ($coupon['coupon_minimum_order'] > '0') {
    $module_smarty->assign('COUPON_MINIMUM_ORDER', $xtPrice->xtcFormat($coupon['coupon_minimum_order'], true));
}

// BENUTZUNG PRO KUNDE
if ($coupon['uses_per_user'] != '0') {
    $count_actual_customer = xtc_db_query("SELECT * FROM " . TABLE_COUPON_REDEEM_TRACK . " WHERE coupon_id = '" . (int) $coupon['coupon_id'] . "' AND customer_id = '" . (int) $_SESSION['customer_id'] . "'");
    $module_smarty->assign('COUPON_USES_PER_USER', $coupon['uses_per_user']);
    // $module_smarty->assign('COUPON_CUSTOMERS_USES_DONE', xtc_db_num_rows($count_actual_customer));
    // $module_smarty->assign('COUPON_CUSTOMERS_USES_LEFT', $coupon['uses_per_user'] - xtc_db_num_rows($count_actual_customer));		
}

// LISTE DER GÜLTIGEN KATEGORIEN ZUSAMMENSTELLEN
if ($coupon['restrict_to_categories'] != '') {
    $restricted_categories .= '<ul class="restriction_list">';
    $cat_ids = explode(",", $coupon['restrict_to_categories']);
    // NAME DER KATEGORIE AUSLESEN UND LINK BILDEN
    for ($ii = 0; $ii < count($cat_ids); $ii++) {
        $cat_info_query = xtc_db_query("SELECT * FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd WHERE c.categories_id = cd.categories_id AND cd.language_id = '" . (int) $_SESSION['languages_id'] . "' AND c.categories_id='" . $cat_ids[$ii] . "'");
        $cat_info = xtc_db_fetch_array($cat_info_query);
        $restricted_categories .= '<li><a href="' . xtc_href_link(FILENAME_DEFAULT, xtc_category_link($cat_info["categories_id"], $cat_info["categories_name"])) . '" title="' . $cat_info["categories_name"] . '" target="_blank">' . $cat_info["categories_name"] . '</a></li>';
    }
    $restricted_categories .= '</ul>';
}
if ($restricted_categories != '') {
    $module_smarty->assign('COUPON_CATEGORIES_LIST', $restricted_categories);
}

// LISTE DER GÜLTIGEN ARTIKEL ZUSAMMENSTELLEN	
if ($coupon['restrict_to_products'] != '') {
    $restricted_products .= '<ul class="restriction_list">';
    $pr_ids = explode(",", $coupon['restrict_to_products']);
    // NAME DES ARTIKELS AUSLESEN UND LINK BILDEN
    for ($ii = 0; $ii < count($pr_ids); $ii++) {
        $product_info_query = xtc_db_query("SELECT products_name FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd WHERE p.products_id = pd.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "' AND p.products_id = '" . $pr_ids[$ii] . "'");
        $product_info = xtc_db_fetch_array($product_info_query);
        $restricted_products .= '<li><a href="' . xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($pr_ids[$ii], $product_info["products_name"])) . '" title="' . $product_info["products_name"] . '" target="_blank">' . $product_info["products_name"] . '</a></li>';
    }
    $restricted_products .= '</ul>';
}
if ($restricted_products != '') {
    $module_smarty->assign('COUPON_PRODUCTS_LIST', $restricted_products);
}

// AUSGABE AN TEMPLATE
$module_smarty->caching = false;
$coupon_infos_popup = $module_smarty->fetch(CURRENT_TEMPLATE . '/module/popup_coupon_help.html');
