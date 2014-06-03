<?php
/*-----------------------------------------------------------------
* 	$Id: cross_selling.php 371 2013-06-10 12:44:12Z akausch $
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

if ($_GET['products_id'] != '') {

	$cs_groups = "SELECT products_xsell_grp_name_id FROM ".TABLE_PRODUCTS_XSELL." WHERE products_id = '".$_GET['products_id']."' GROUP BY products_xsell_grp_name_id";
	$cs_groups = xtDBquery($cs_groups);
	$cross_sells = xtc_db_fetch_array($cs_groups, true);
	$fsk_lock = '';
	if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
		$fsk_lock = ' AND p.products_fsk18!=1';
	}
	$group_check = "";
	if (GROUP_CHECK == 'true') {
		$group_check = " AND p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
	}
//Cross Selling
	$cross_selling_query = xtDBquery("SELECT DISTINCT
									p.*,
									pd.*,
									xp.sort_order 
									FROM 
									".TABLE_PRODUCTS_XSELL." xp 
									LEFT JOIN 
										".TABLE_PRODUCTS." p ON(xp.products_id = '".$_GET['products_id']."')
									LEFT JOIN 
										".TABLE_PRODUCTS_DESCRIPTION." pd ON(p.products_id = pd.products_id)
									WHERE 
										xp.xsell_id = p.products_id 
										".$fsk_lock.$group_check."
									AND 
										xp.products_xsell_grp_name_id = '".$cross_sells['products_xsell_grp_name_id']."'
									AND 
										pd.language_id = '".(int)$_SESSION['languages_id']."'
									AND
										(p.products_slave_in_list = '1' OR p.products_master = '1' OR ((p.products_slave_in_list = '0' OR p.products_slave_in_list = '') AND (p.products_master_article = '' OR p.products_master_article = '0')))
									AND 
										p.products_status = '1'
									ORDER BY xp.sort_order ASC;");


//Reverse Cross Selling
	$cross_query = xtDBquery("SELECT DISTINCT
									p.*, 
									pd.*,
									xp.sort_order
								FROM
									".TABLE_PRODUCTS_XSELL." xp 
								LEFT JOIN 
									".TABLE_PRODUCTS." p ON(xp.xsell_id = '".$_GET['products_id']."')
								LEFT JOIN 
									".TABLE_PRODUCTS_DESCRIPTION." pd ON(p.products_id = pd.products_id)
								WHERE
									xp.products_id = p.products_id
									".$fsk_lock.$group_check."
								AND
									pd.language_id = '".(int)$_SESSION['languages_id']."'
								AND 
									(p.products_slave_in_list = '1' OR p.products_master = '1' OR ((p.products_slave_in_list = '0' OR p.products_slave_in_list = '') AND (p.products_master_article = '' OR p.products_master_article = '0')))
								AND
									p.products_status = '1'
								ORDER BY
									xp.sort_order ASC;");
	
	if (xtc_db_num_rows($cross_query, true) > 0) {
		$box_smarty = new smarty;
		$i = 0;
		$cross_sell_data = array ();
			while ($xsell = xtc_db_fetch_array($cross_query, true)) { 
				$i++;
				$cross_sell_data[] = $product->buildDataArray($xsell,'mini','reverse_cross_selling',$i);
			}
		if(!$box_smarty->isCached(CURRENT_TEMPLATE.'/boxes/box_cross_selling.html', $cache_id) || !$cache) {
			$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
			$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
			$box_smarty->assign('language', $_SESSION['language']);
			$box_smarty->assign('box_name', getBoxName('cross_selling'));
			$box_smarty->assign('box_class_name', getBoxCSSName('cross_selling'));
			$box_smarty->assign('box_cross_selling_content', $cross_sell_data);
		}
		if (!CacheCheck()) {
			$cache = false;
			$box_smarty->caching = 0;
		} else {
			$cache = true;
			$box_smarty->caching = 1;
			$box_smarty->cache_lifetime = CACHE_LIFETIME;
			$box_smarty->cache_modified_check = CACHE_CHECK;
			$cache_id = $_SESSION['language'].(int) $_GET['manufacturers_id'];
		}		
		
		
		if(!$cache)
			$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_cross_selling.html');
		else
			$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_cross_selling.html', $cache_id);
	}

	if (xtc_db_num_rows($cross_selling_query, true) > 0) {
		$box_smarty = new smarty;
		$row = 0;
		$cross_sell_data = array ();
		while ($xsell = xtc_db_fetch_array($cross_selling_query, true)) {
			$row++;
			$cross_sell_data[] = $product->buildDataArray($xsell,'mini','cross_selling',$row);
		}
		if(!$box_smarty->isCached(CURRENT_TEMPLATE.'/boxes/box_cross_selling.html', $cache_id) || !$cache) {
			$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
			$box_smarty->assign('language', $_SESSION['language']);
			$box_smarty->assign('box_name', getBoxName('cross_selling'));
			$box_smarty->assign('box_class_name', getBoxCSSName('cross_selling'));
			$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
			$box_smarty->assign('box_cross_selling_content', $cross_sell_data);
		}
		if (!CacheCheck()) {
			$cache = false;
			$box_smarty->caching = false;
		} else {
			$cache = true;
			$box_smarty->caching = true;
			$box_smarty->cache_lifetime = CACHE_LIFETIME;
			$box_smarty->cache_modified_check = CACHE_CHECK;
			$cache_id = $_SESSION['language'].(int) $_GET['manufacturers_id'].'crosselling';
		}		
		
		
		if(!$cache)
			$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_cross_selling.html');
		else
			$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_cross_selling.html', $cache_id);
	}
}
