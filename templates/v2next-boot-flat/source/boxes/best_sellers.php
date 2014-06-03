<?php
/*-----------------------------------------------------------------
* 	$Id: best_sellers.php 371 2013-06-10 12:44:12Z akausch $
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

if (!$product->isProduct()) {
	$box_smarty = new smarty;
	$box_content = '';
	
	$box_smarty->assign('language', $_SESSION['language']);
	$box_smarty->assign('box_name', getBoxName('best_sellers'));
	$box_smarty->assign('box_class_name', getBoxCSSName('best_sellers'));
	$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');

	if (!CacheCheck() || !FORCE_CACHE) {
	 	$cache=false;
		$box_smarty->caching = false;
	} else {
		$cache=true;
		$box_smarty->caching = true;
		$box_smarty->cache_lifetime = CACHE_LIFETIME;
		$box_smarty->cache_modified_check = CACHE_CHECK;
		$cache_id = $_SESSION['language'].$current_category_id.'best_sellers';
	}
	
	if (!$box_smarty->isCached(CURRENT_TEMPLATE.'/boxes/box_best_sellers.html', $cache_id) || !$cache) {
		$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
		require_once (DIR_FS_INC.'xtc_row_number_format.inc.php');
		
		//fsk18 lock
		$fsk_lock = '';
		$group_check = '';
		if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
			$fsk_lock = ' AND p.products_fsk18!=1';
		}
		if (GROUP_CHECK == 'true') {
			$group_check = " AND p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		}
		if (isset ($current_category_id) && ($current_category_id > 0)) {
			$best_sellers_query = "SELECT DISTINCT
										p.*,
										pd.products_name
									FROM 
										".TABLE_PRODUCTS." p
									LEFT JOIN 
										".TABLE_PRODUCTS_DESCRIPTION." pd ON(p.products_id = pd.products_id)
									LEFT JOIN 
										".TABLE_PRODUCTS_TO_CATEGORIES." p2c ON(p.products_id = p2c.products_id)
									LEFT JOIN 
										".TABLE_CATEGORIES." c ON(p2c.categories_id = c.categories_id )
									WHERE 
										p.products_status = '1'
									AND 
										c.categories_status = '1'
									AND 
										p.products_ordered > 0
									AND 
										pd.language_id = '".(int)$_SESSION['languages_id']."'
									".$group_check."
									".$fsk_lock."
									AND 
										(c.categories_id = '" . (int)$current_category_id . "' or c.parent_id = '" . (int)$current_category_id . "')
									ORDER BY p.products_ordered DESC
									LIMIT ".MAX_DISPLAY_BESTSELLERS;
		} else {
			$best_sellers_query = "SELECT DISTINCT
										p.*,
										pd.products_name 
									FROM 
										".TABLE_PRODUCTS." p 
									LEFT JOIN 
										".TABLE_PRODUCTS_DESCRIPTION." pd ON(p.products_id = pd.products_id)
									WHERE 
										p.products_status = '1'
									AND 
										p.products_ordered > 0
									".$group_check."
									 ".$fsk_lock."
									AND 
										pd.language_id = '".(int)$_SESSION['languages_id']."'
									ORDER BY 
										p.products_ordered DESC
									LIMIT ".MAX_DISPLAY_BESTSELLERS;
		}
		$best_sellers_query = xtDBquery($best_sellers_query);
		if (xtc_db_num_rows($best_sellers_query, true) >= MIN_DISPLAY_BESTSELLERS) {
			$rows = 0;
			$box_bestseller_content = array ();
			while ($best_sellers = xtc_db_fetch_array($best_sellers_query, true)) {
				$rows ++;
				$image = '';
				$best_sellers = array_merge($best_sellers, array ('ID' => xtc_row_number_format($rows)));
				$box_bestseller_content[] = $product->buildDataArray($best_sellers,'mini','best_sellers',$rows);
			}
			$box_smarty->assign('box_bestseller_content', $box_bestseller_content);
		}
		if (!$cache) {
			if ($box_bestseller_content!='') {
				$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_best_sellers.html');
		 	}
		} else {
			$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_best_sellers.html', $cache_id);
		}
	}
}
