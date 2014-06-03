<?php

/* -----------------------------------------------------------------
 * 	$Id: cross_selling.php 571 2013-08-21 16:02:17Z akausch $
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

$data = $product->getCrossSells();

if (!empty($data) && PRODUCT_DETAILS_TAB_CROSS_SELLING == 'true') {
    $module_smarty = new Smarty;

    $module_smarty->assign('language', $_SESSION['language']);
    $module_smarty->assign('module_content', $data);
    $module_smarty->assign('cross_selling', true);
	$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
    $module_smarty->assign('TITLE', CROSS_SELLING);
    $module_smarty->assign('CLASS', 'cross_selling');
    $module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $module_smarty->caching = false;
    $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE));
    $info_smarty->assign('MODULE_cross_selling', $module);
}

if (ACTIVATE_REVERSE_CROSS_SELLING == 'true' && PRODUCT_DETAILS_TAB_REVERSE_CROSS_SELLING == 'true') {
    $module_smarty = new Smarty;

    $data = $product->getReverseCrossSells();
    if (count($data) > 0) {
        $module_smarty->assign('language', $_SESSION['language']);
        $module_smarty->assign('module_content', $data);
        $module_smarty->assign('reverse_cross_selling', true);
		$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
        $module_smarty->assign('TITLE', REVERSE_CROSS_SELLING);
        $module_smarty->assign('CLASS', 'reverse_cross_selling');
        $module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $module_smarty->caching = false;
        $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_listing/product_listings.html', USE_TEMPLATE_DEVMODE));
        $info_smarty->assign('MODULE_reverse_cross_selling', $module);
    }
}
