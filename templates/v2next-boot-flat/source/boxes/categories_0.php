<?php
/*-----------------------------------------------------------------
* 	$Id: categories_0.php 371 2013-06-10 12:44:12Z akausch $
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
		$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'].'cat_0';
	} else {
		$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'].$cPath.'cat_0';
	}
}

if(!$box_smarty->isCached(CURRENT_TEMPLATE.'/boxes/box.html', $cache_id) || !$cache){
	$CatConfig = array(
		'MinLevel'					=>	1,
		'MaxLevel'					=>	false,
		'HideEmpty'					=>	false
	);

	function gunnartCategories($CatID=0,$Level=1) {
	
		global 	$cPath,
				$current_category_id,
				$CatConfig;

		$myPathArray = explode('_',$cPath);
		
		// Kundengruppen-Check
		if(GROUP_CHECK=='true') {
			$group_check = "AND c.group_permission_".$_SESSION['customers_status']['customers_status_id']." = 1 ";
		}
		
		//Multikategorie
		if(MODULE_CSEO_MULTICAT=='true') {
			$multi_kat_0 = "AND	c.section = 0";
		}
		
		// Datenbank ...
		$dbQuery = xtDBquery(" 
			SELECT	c.categories_id,
					c.categories_nav_image,  
					cd.categories_heading_title, 
					cd.categories_name 
			FROM	".TABLE_CATEGORIES." c
			INNER JOIN	".TABLE_CATEGORIES_DESCRIPTION." cd ON(cd.categories_id = c.categories_id)
			WHERE 	c.parent_id = ".intval($CatID)." 
			AND		c.categories_status = 1 
				".$multi_kat_0."
				".$group_check." 
			AND 	cd.language_id = ".intval($_SESSION['languages_id'])." 
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
				require_once (DIR_FS_INC.'xtc_count_products_in_category.inc.php');
				$ProdsInCat = xtc_count_products_in_category($dbQueryResult['categories_id']);
			}
			if(($ProdsInCat != 0 && $CatConfig['HideEmpty'] == true) || ($CatConfig['HideEmpty'] == false)) {
				if ($dbQueryResult['categories_nav_image'] != '' && $Level == 1) {
					$nav_pic = '<div class="catpic"><img src="'.DIR_WS_IMAGES.'/categories_nav/'.$dbQueryResult['categories_nav_image'].'" alt="'.$dbQueryResult['categories_name'].'" title="'.$dbQueryResult['categories_heading_title'].'" '.cseo_get_img_size(DIR_WS_IMAGES.'/categories_nav/'.$dbQueryResult['categories_nav_image']).' /></div>';
				} else {
					$nav_pic = '';
				}
				$Return 	.= 	"\n"
							.	'<li class="main_level_'.$Level.'">'
							.	'<a'.$Current.' href="'
							.	xtc_href_link(FILENAME_DEFAULT,xtc_category_link($dbQueryResult['categories_id'],$dbQueryResult['categories_name']))
							.	'" title="'.$dbQueryResult['categories_heading_title'].'">'
							. 	$nav_pic 
							.	$dbQueryResult['categories_name'];
				if(SHOW_COUNTS == 'true') {
					$Return .=	' <em>('
							.	$ProdsInCat
							.	')</em>';
				}
				$Return 	.=	'</a>';
				if(($Level < $CatConfig['MinLevel'] || $Current) && ($Level < $CatConfig['MaxLevel'] || !$CatConfig['MaxLevel'])) {
					$Return	.= 	gunnartCategories($dbQueryResult['categories_id'],$Level+1); // <-- Rekursion!
				}
				$Return 	.=	'</li>';
			}
		}
		
		// HTML-Output ...
		if($Return) {
			return 	"\n<ul$CSS>$Return\n</ul>\n";
		}
	}
	$box_smarty->assign('language', $_SESSION['language']);
	$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
	$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
	$box_smarty->assign('box_name', getBoxName('categories_0'));
	$box_smarty->assign('box_class_name', getBoxCSSName('categories_0'));
	$box_smarty->assign('BOX_CONTENT',gunnartCategories());
}
if (!$cache) {
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
} else {
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html', $cache_id);
}
