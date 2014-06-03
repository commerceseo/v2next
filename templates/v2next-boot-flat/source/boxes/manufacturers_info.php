<?php
/*-----------------------------------------------------------------
* 	$Id: manufacturers_info.php 434 2013-06-25 17:30:40Z akausch $
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

$box_smarty = new smarty;
$box_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');

$manufacturer_query = xtDBquery("SELECT m.manufacturers_id, 
										m.manufacturers_name, 
										m.manufacturers_image, 
										mi.manufacturers_url 
										FROM 
											" . TABLE_MANUFACTURERS . " m 
										INNER JOIN 
											" . TABLE_MANUFACTURERS_INFO . " mi ON(m.manufacturers_id = mi.manufacturers_id AND mi.languages_id = '" . (int)$_SESSION['languages_id'] . "')
										INNER JOIN
											" . TABLE_PRODUCTS . " p ON(p.manufacturers_id = m.manufacturers_id)
										WHERE 
											p.products_id = '" . $product->data['products_id'] . "';");
if (xtc_db_num_rows($manufacturer_query)) {
	$manufacturer = xtc_db_fetch_array($manufacturer_query);
	$image='';
	if (xtc_not_null($manufacturer['manufacturers_image'])) {
		$image = xtc_image(DIR_WS_IMAGES . $manufacturer['manufacturers_image'], $manufacturer['manufacturers_name']);
	}
	$box_smarty->assign('IMAGE',$image);
	$box_smarty->assign('NAME',$manufacturer['manufacturers_name']);
	if ($manufacturer['manufacturers_url']!='') {
		$box_smarty->assign('URL','<a href="' . xtc_href_link(FILENAME_REDIRECT, 'action=manufacturer&'.xtc_manufacturer_link($manufacturer['manufacturers_id'],$manufacturer['manufacturers_name'])) . '" onclick="window.open(this.href); return false;">' . sprintf(BOX_MANUFACTURER_INFO_HOMEPAGE, $manufacturer['manufacturers_name']) . '</a>');
	}
	$box_smarty->assign('LINK_MORE','<a href="' . xtc_href_link(FILENAME_DEFAULT, xtc_manufacturer_link($manufacturer['manufacturers_id'],$manufacturer['manufacturers_name'])) . '">' . BOX_MANUFACTURER_INFO_OTHER_PRODUCTS . '</a>');	
}

if ($manufacturer['manufacturers_name']!='') {
	$box_smarty->assign('language', $_SESSION['language']);
	$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
	$box_smarty->assign('box_name', getBoxName('manufacturers_info'));
	$box_smarty->assign('box_class_name', getBoxCSSName('manufacturers_info'));
	// set cache ID
	if (!CacheCheck() && !FORCE_CACHE) {
		$box_smarty->caching = false;
		$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_manufacturers_info.html');
	} else {
		$box_smarty->caching = true;
		$box_smarty->cache_lifetime=CACHE_LIFETIME;
		$box_smarty->cache_modified_check=CACHE_CHECK;
		$cache_id = $_SESSION['language'].$product->data['products_id'].'manufacturers_info';
		$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_manufacturers_info.html',$cache_id);
	}
}     
