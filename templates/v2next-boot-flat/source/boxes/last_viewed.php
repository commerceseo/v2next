<?php
/*-----------------------------------------------------------------
* 	$Id: last_viewed.php 371 2013-06-10 12:44:12Z akausch $
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

if (isset ($_SESSION[tracking][products_history][0])) {
	require_once(DIR_FS_INC . 'xtc_rand.inc.php');
	$max = count($_SESSION[tracking][products_history]);
	$max--;
	$random_last_viewed = xtc_rand(0,$max);
	$fsk_lock='';
	$group_check='';
	if ($_SESSION['customers_status']['customers_fsk18_display']=='0') {
	$fsk_lock=' AND p.products_fsk18!=1';
	}
	 if (GROUP_CHECK=='true') {
	   $group_check=" AND p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
	}
	                   
	$random_query = "SELECT DISTINCT
						p.*,
						pd.products_name,
						p2c.categories_id,
						cd.categories_name 
					FROM 
						" . TABLE_PRODUCTS . " p 
						INNER JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON(pd.products_id = '".(int)$_SESSION[tracking][products_history][$random_last_viewed]."')
						INNER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON(p2c.products_id = '".(int)$_SESSION[tracking][products_history][$random_last_viewed]."')
						INNER JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON(cd.categories_id = p2c.categories_id)
					WHERE 
						p.products_status = '1'                                                                                               
					AND 
						p.products_id = '".(int)$_SESSION[tracking][products_history][$random_last_viewed]."'
					AND 
						pd.language_id = '" . $_SESSION['languages_id'] . "'
					".$group_check."
					".$fsk_lock."
					AND 
						cd.language_id = '" . $_SESSION['languages_id'] . "'";
	
	$random_query = xtDBquery($random_query);
	$random_product = xtc_db_fetch_array($random_query,true);
	
	$random_products_price = $xtPrice->xtcGetPrice($random_product['products_id'],$format=true,1,$random_product['products_tax_class_id'],$random_product['products_price']);
	
	if ($random_product['products_name']!='') {
		$box_smarty->assign('box_content',$product->buildDataArray($random_product,'thumbnail','last_viewed'));
		$box_smarty->assign('MY_PAGE', 'TEXT_MY_PAGE');
		$box_smarty->assign('WATCH_CATGORY', 'TEXT_WATCH_CATEGORY');
		$box_smarty->assign('MY_PERSONAL_PAGE',xtc_href_link(FILENAME_ACCOUNT));
		$box_smarty->assign('CATEGORY_LINK',xtc_href_link(FILENAME_DEFAULT, xtc_category_link($random_product['categories_id'],$random_product['categories_name'])));
		$box_smarty->assign('CATEGORY_NAME',$random_product['categories_name']);
		$box_smarty->assign('language', $_SESSION['language']);
		$box_smarty->assign('box_name', getBoxName('last_viewed'));
		$box_smarty->assign('box_class_name', getBoxCSSName('last_viewed'));
		$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
		$box_smarty->caching = false;
		$box_content= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_last_viewed.html');

	}
}
