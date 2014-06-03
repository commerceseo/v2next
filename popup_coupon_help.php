<?php

/* -----------------------------------------------------------------
 * 	$Id: popup_coupon_help.php 505 2013-07-18 16:37:52Z akausch $
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

require ('includes/application_top.php');
require_once (DIR_FS_INC . 'xtc_date_short.inc.php');

$smarty = new Smarty;

include ('includes/header.php');


$coupon_query = xtc_db_query("SELECT * FROM " . TABLE_COUPONS . " WHERE coupon_id = '" . (int) $_GET['cID'] . "'");
$coupon = xtc_db_fetch_array($coupon_query);
$coupon_desc_query = xtc_db_query("SELECT * FROM " . TABLE_COUPONS_DESCRIPTION . " WHERE coupon_id = '" . (int) $_GET['cID'] . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "'");
$coupon_desc = xtc_db_fetch_array($coupon_desc_query);
$text_coupon_help = TEXT_COUPON_HELP_HEADER;
$text_coupon_help .= sprintf(TEXT_COUPON_HELP_NAME, $coupon_desc['coupon_name']);
if (xtc_not_null($coupon_desc['coupon_description']))
    $text_coupon_help .= sprintf(TEXT_COUPON_HELP_DESC, $coupon_desc['coupon_description']);
$coupon_amount = $coupon['coupon_amount'];
switch ($coupon['coupon_type']) {
    case 'F' :
        $text_coupon_help .= sprintf(TEXT_COUPON_HELP_FIXED, $xtPrice->xtcFormat($coupon['coupon_amount'], true));
        break;
    case 'P' :
        $text_coupon_help .= sprintf(TEXT_COUPON_HELP_FIXED, number_format($coupon['coupon_amount'], 2) . '%');
        break;
    case 'S' :
        $text_coupon_help .= TEXT_COUPON_HELP_FREESHIP;
        break;
    default :
}

if ($coupon['coupon_minimum_order'] > 0)
    $text_coupon_help .= sprintf(TEXT_COUPON_HELP_MINORDER, $xtPrice->xtcFormat($coupon['coupon_minimum_order'], true));
$text_coupon_help .= sprintf(TEXT_COUPON_HELP_DATE, xtc_date_short($coupon['coupon_start_date']), xtc_date_short($coupon['coupon_expire_date']));
$text_coupon_help .= '<b>' . TEXT_COUPON_HELP_RESTRICT . '</b>';
$text_coupon_help .= '<br><br>' . TEXT_COUPON_HELP_CATEGORIES;
$coupon_get = xtc_db_query("SELECT restrict_to_categories FROM " . TABLE_COUPONS . " WHERE coupon_id='" . (int) $_GET['cID'] . "'");
$get_result = xtc_db_fetch_array($coupon_get);
if ($get_result['restrict_to_categories'] != '') {
$cat_ids = explode(",", $get_result['restrict_to_categories']);
for ($i = 0; $i < count($cat_ids); $i++) {
    $result = xtc_db_query("SELECT * FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd WHERE c.categories_id = cd.categories_id and cd.language_id = '" . $_SESSION['languages_id'] . "' and c.categories_id='" . $cat_ids[$i] . "'");
    if ($row = xtc_db_fetch_array($result)) {
        $cats .= '<br>' . $row["categories_name"];
    }
}
}
if ($cats == '')
    $cats = '<br>NONE';
$text_coupon_help .= $cats;
$text_coupon_help .= '<br><br>' . TEXT_COUPON_HELP_PRODUCTS;
$coupon_get = xtc_db_query("SELECT restrict_to_products FROM " . TABLE_COUPONS . "  WHERE coupon_id='" . (int) $_GET['cID'] . "'");
$get_result = xtc_db_fetch_array($coupon_get);
if ($get_result['restrict_to_products'] != '') {
	$pr_ids = explode(",", $get_result['restrict_to_products']);
	for ($i = 0; $i < count($pr_ids); $i++) {
		$result = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd WHERE p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "'and p.products_id = '" . $pr_ids[$i] . "'");
		if ($row = xtc_db_fetch_array($result)) {
			$prods .= '<br>' . $row["products_name"];
		}
	}
}
if ($prods == '')
    $prods = '<br>NONE';
$text_coupon_help .= $prods;

$smarty->assign('TEXT_HELP', $text_coupon_help);
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->caching = false;

$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/popup_coupon_help.html',USE_TEMPLATE_DEVMODE));

$smarty->assign('main_content', $main_content);

include ('includes/application_bottom.php');
