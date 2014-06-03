<?php
/*-----------------------------------------------------------------
* 	$Id: categories_1.php 371 2013-06-10 12:44:12Z akausch $
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

if (!CacheCheck() && !FORCE_CACHE) {
	$cache=false;
	$box_smarty->caching = false;
} else {
	$cache=true;
	$box_smarty->caching = true;
	$box_smarty->cache_lifetime = CACHE_LIFETIME;
	$box_smarty->cache_modified_check = CACHE_CHECK;
	if (CAT_NAV_AJAX == 'true') {
		$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'].'section';
	} else {
		$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'].'section'.$cPath.'cat_1';
	}
}

if(!$box_smarty->isCached(CURRENT_TEMPLATE.'/boxes/box.html', $cache_id) || !$cache){
	require_once (DIR_FS_INC.'xtc_count_products_in_category.inc.php');

	$CatConfig = array(
		'MinLevel'					=>	1,
		'MaxLevel'					=>	false,
		'HideEmpty'					=>	false
	);

	function gunnartCategoriesOne($CatID=0,$Level=1) {
	
		global 	$cPath,
				$current_category_id,
				$CatConfig;

		$myPathArray = explode('_',$cPath);
		
		// Kundengruppen-Check
		if(GROUP_CHECK=='true') {
			$group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']." = 1 ";
		}
		
		// Datenbank ...
		$dbQuery = xtDBquery(" 
			select	c.categories_id,
					cd.categories_name 
			from	".TABLE_CATEGORIES." c, 
					".TABLE_CATEGORIES_DESCRIPTION." cd 
			where 	c.parent_id = ".intval($CatID)." 
			and		c.categories_status = 1 
			and		c.section = 1
					".$group_check." 
			and 	c.categories_id = cd.categories_id 
			and 	cd.language_id = ".intval($_SESSION['languages_id'])." 
			order by sort_order, cd.categories_name
		");
		
		// Ergebnisse ... 
		while($dbQueryResult = xtc_db_fetch_array($dbQuery,true)) {
			
			$Current = false;
			if($dbQueryResult['categories_id'] == $current_category_id) {
				$Current = ' class="Current"';
			} elseif(in_array($dbQueryResult['categories_id'],$myPathArray)) {
				$Current = ' class="CurrentParent"';
			}
			if(SHOW_COUNTS == 'true' || $CatConfig['HideEmpty'] == true) {
				$ProdsInCat = xtc_count_products_in_category($dbQueryResult['categories_id']);
			}
			if(($ProdsInCat != 0 && $CatConfig['HideEmpty'] == true) || ($CatConfig['HideEmpty'] == false)) {
				$Return 	.= 	"\n"
							.	'<li class="main_level_'.$Level.'">'
							.	'<a'.$Current.' href="'
							.	xtc_href_link(FILENAME_DEFAULT,xtc_category_link($dbQueryResult['categories_id'],$dbQueryResult['categories_name']))
							.	'" title="'.$dbQueryResult['categories_name'].'">'
							.	$dbQueryResult['categories_name'];
				if(SHOW_COUNTS == 'true') {
					$Return .=	' <em>('
							.	$ProdsInCat
							.	')</em>';
				}
				$Return 	.=	'</a>';
				if(($Level < $CatConfig['MinLevel'] || $Current) && ($Level < $CatConfig['MaxLevel'] || !$CatConfig['MaxLevel'])) {
					$Return	.= 	gunnartCategoriesOne($dbQueryResult['categories_id'],$Level+1); // <-- Rekursion!
				}
				$Return 	.=	'</li>';
			}
		}
		
		// HTML-Output ...
		if($Return) {
			if($Level == 1) {
				$CSS .= ' id="main_nav1"';
			}
			return 	"\n<ul$CSS>$Return\n</ul>\n";
		}
	}

	$box_smarty->assign('language', $_SESSION['language']);
	$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
	$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
	$box_smarty->assign('box_name', getBoxName('categories_1'));
	$box_smarty->assign('box_class_name', getBoxCSSName('categories_1'));
	$box_smarty->assign('BOX_CONTENT',gunnartCategoriesOne());				
}
if (!$cache) {
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
} else {
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html', $cache_id);
}
