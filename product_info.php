<?php

/* -----------------------------------------------------------------
 * 	$Id: product_info.php 857 2014-03-06 20:03:55Z akausch $
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

require_once (DIR_FS_INC . 'xtc_get_download.inc.php');
require_once (DIR_FS_INC . 'xtc_date_long.inc.php');
require_once (DIR_FS_INC . 'xtc_image_submit.inc.php');


if ($_GET['products_id']) {
    $cat = xtDBquery("SELECT categories_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id='" . (int) $_GET['products_id'] . "' LIMIT 1;");
    $catData = xtc_db_fetch_array($cat);
    require_once (DIR_FS_INC . 'xtc_get_path.inc.php');
    if ($catData['categories_id'])
        $cPath = xtc_input_validation(xtc_get_path($catData['categories_id']), 'cPath', '');
}



if ($_GET['action'] == 'get_download') {
    xtc_get_download($_GET['cID']);
}

include (DIR_WS_MODULES . 'product_info.php');

require_once (DIR_WS_INCLUDES . 'header.php');

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->caching = false;

$cseo_product_info = cseohookfactory::create_object('ProductInfoExtender');
$cseo_product_info->proceed();
$cseo_extender_result_array = $cseo_product_info->get_response();
if (is_array($cseo_extender_result_array)) {
	foreach ($cseo_extender_result_array AS $t_key => $t_value) {
		$smarty->assign($t_key, $t_value);
	}
}
$smarty->loadFilter('output', 'note');
$smarty->loadFilter('output', 'trimwhitespace');
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));

include ('includes/application_bottom.php');
