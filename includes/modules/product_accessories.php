<?php

/* -----------------------------------------------------------------
 * 	$Id: product_accessories.php 1468 2015-07-22 20:29:10Z akausch $
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
$data = $product->getAccessories();
if (sizeof($data) > 0) {
	$module_smarty = new Smarty;
	$module_smarty->assign('language', $_SESSION['language']);
	$module_smarty->assign('accessories', $data);
	$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
	$module_smarty->assign('product_accessories', true);
	$module_smarty->assign('TITLE', ALSO_PURCHASED);
	$module_smarty->assign('CLASS', 'product_accessories');
	$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
	$module_smarty->caching = false;
	$module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/accessories.html', USE_TEMPLATE_DEVMODE));
	$info_smarty->assign('MODULE_accessories', $module);
}
