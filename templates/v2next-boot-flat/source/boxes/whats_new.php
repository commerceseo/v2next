<?php
/*-----------------------------------------------------------------
* 	ID:						whats_new.php
* 	Letzter Stand:			v2.2 R365
* 	zuletzt geaendert von:	akausch
* 	Datum:					2012/07/03
*
* 	Copyright (c) since 2010 commerce:SEO by Webdesign Erfurt
* 	http://www.commerce-seo.de
* ------------------------------------------------------------------
* 	based on:
* 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
* 	(c) 2002-2003 osCommerce - www.oscommerce.com
* 	(c) 2003     nextcommerce - www.nextcommerce.org
* 	(c) 2005     xt:Commerce - www.xt-commerce.com
* 	Released under the GNU General Public License
* ---------------------------------------------------------------*/

if (substr(basename($PHP_SELF), 0,8) != 'advanced') {	
	$box_smarty = new smarty;
	require_once (DIR_FS_INC.'xtc_random_select.inc.php');
	require_once (DIR_FS_INC.'xtc_get_products_name.inc.php');
	
	//fsk18 lock
	$fsk_lock = '';
	if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
		$fsk_lock = ' AND p.products_fsk18 != 1';
	}
	if (GROUP_CHECK == 'true') {
		$group_check = " AND p.group_permission_".$_SESSION['customers_status']['customers_status_id']." = 1 ";
	}
	if (MAX_DISPLAY_NEW_PRODUCTS_DAYS != '0') {
		$date_new_products = date("Y.m.d", mktime(1, 1, 1, date(m), date(d) - MAX_DISPLAY_NEW_PRODUCTS_DAYS, date(Y)));
		$days = " AND p.products_date_added > '".$date_new_products."' ";
	}
	if ($random_product = xtc_random_select("SELECT DISTINCT
												*
											FROM 
												".TABLE_PRODUCTS." p
											INNER JOIN
												".TABLE_PRODUCTS_TO_CATEGORIES." p2c ON(p.products_id = p2c.products_id)
											INNER JOIN
												".TABLE_CATEGORIES." c ON(c.categories_id = p2c.categories_id)
											INNER JOIN
												" . TABLE_CATEGORIES_DESCRIPTION . " cd ON(c.categories_id = cd.categories_id)
											WHERE 
												p.products_status = '1'
											AND 
												p.products_id !='".(int) $_GET['products_id']."'
											".$group_check."
											".$fsk_lock."
											".$days."
											AND 
												c.categories_status = '1'
											ORDER BY p.products_date_added DESC 
											LIMIT ".MAX_RANDOM_SELECT_NEW)) {

		$whats_new_price = $xtPrice->xtcGetPrice($random_product['products_id'], $format = true, 1, $random_product['products_tax_class_id'], $random_product['products_price']);
	}
	
	$random_product['products_name'] = xtc_get_products_name($random_product['products_id']);
	
	if ($random_product['products_name'] != '') {
	
		$box_smarty->assign('box_content',$product->buildDataArray($random_product,'thumbnail','whats_new'));
		$box_smarty->assign('LINK_NEW_PRODUCTS',xtc_href_link(FILENAME_PRODUCTS_NEW));
		$box_smarty->assign('NEWS_BUTTON', xtc_image_button('button_quick_find.gif', IMAGE_BUTTON_MORE_NEWS));
		$box_smarty->assign('CATEGORY_LINK',xtc_href_link(FILENAME_DEFAULT, xtc_category_link($random_product['categories_id'],$random_product['categories_name'])));
		$box_smarty->assign('CATEGORY_NAME',$random_product['categories_name']);
		$box_smarty->assign('language', $_SESSION['language']);
		$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
		$box_smarty->assign('box_name', getBoxName('whats_new'));
		$box_smarty->assign('box_class_name', getBoxCSSName('whats_new'));
		
		// set cache ID
		 if (!CacheCheck()) {
			$box_smarty->caching = false;
			$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_whatsnew.html');
		} else {
			$box_smarty->caching = true;
			$box_smarty->cache_lifetime = CACHE_LIFETIME;
			$box_smarty->cache_modified_check = CACHE_CHECK;
			$cache_id = $_SESSION['language'].$random_product['products_id'].$_SESSION['customers_status']['customers_status_name'];
			$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_whatsnew.html', $cache_id);
		}
	}
}
?>