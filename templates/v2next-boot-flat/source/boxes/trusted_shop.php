<?php
/*-----------------------------------------------------------------
* 	$Id: trusted_shop.php 371 2013-06-10 12:44:12Z akausch $
* 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
* 	http://www.commerce-seo.de
* ------------------------------------------------------------------
* 	based on:
* 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
* 	(c) 2002-2003 osCommerce - www.oscommerce.com
* 	(c) 2003     nextcommerce - www.nextcommerce.org
* 	(c) 2005     xt:Commerce - www.xt-commerce.com
* 	Released under the GNU General Public License
* ---------------------------------------------------------------*/

if (TRUSTED_SHOP_STATUS == 'true') {
	$box_smarty = new smarty;
	$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
	
	$box_smarty->assign('FORM_ACTION', '<form name="formSiegel" method="post" action="https://www.trustedshops.com/shop/certificate.php">'."\n".xtc_draw_hidden_field('shop_id',TRUSTED_SHOP_NR));
	$box_smarty->assign('SHOP_NAME', STORE_NAME);
	$box_smarty->assign('IMG','<input type="image" src="templates/'.CURRENT_TEMPLATE.'/img/trustedshops.gif" />');
	$box_smarty->assign('SHOP_ID', TRUSTED_SHOP_NR);
	$box_smarty->assign('FORM_END', '</form>');
	$box_smarty->assign('language', $_SESSION['language']);
	$box_smarty->assign('box_name', getBoxName('trusted_shop'));
	$box_smarty->assign('box_class_name', getBoxCSSName('trusted_shop'));
	$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
	
	if (!CacheCheck()) {
		$box_smarty->caching = false;
		$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/trusted_shop/'.TRUSTED_SHOP_TEMPLATE);
	} else {
		$box_smarty->caching = true;	
		$box_smarty->cache_lifetime=CACHE_LIFETIME;
		$box_smarty->cache_modified_check=CACHE_CHECK;
		$cache_id = $_SESSION['language'].$_SESSION['currency'].'trusted_shop';
		$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/trusted_shop/'.TRUSTED_SHOP_TEMPLATE);
	}
}
