<?php

/* -----------------------------------------------------------------
 * 	$Id: graduated_prices.php 522 2013-07-24 11:44:51Z akausch $
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

$staffel_data = $product->getGraduated();

if (sizeof($staffel_data) > 1) {
	$module_smarty = new Smarty;
	$module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
    $module_smarty->assign('language', $_SESSION['language']);
    $module_smarty->assign('module_content', $staffel_data);
    $module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $module_smarty->caching = false;
    $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/graduated_price.html', USE_TEMPLATE_DEVMODE));
    $info_smarty->assign('MODULE_graduated_price', $module);
}
