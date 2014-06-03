<?php
/*-----------------------------------------------------------------
* 	$Id: manufacturers.php 434 2013-06-25 17:30:40Z akausch $
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
$box_smarty->assign('language', $_SESSION['language']);

$_SESSION["MANUFACTURES_SORTBOX_IS_IN_USE"] = true;
if (!CacheCheck() && !FORCE_CACHE) {
	$cache = false;
	$box_smarty->caching = false;
} else {
	$cache = true;
	$box_smarty->caching = true;
	$box_smarty->cache_lifetime = CACHE_LIFETIME;
	$box_smarty->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].(int) $_GET['manufacturers_id'].'manufacturerbox';
}

if(!$box_smarty->isCached(CURRENT_TEMPLATE.'/boxes/box_manufacturers.html', $cache_id) || !$cache) {
	$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');

	$manufacturers_query = xtDBquery("SELECT
											m.manufacturers_id,
											m.manufacturers_name
										FROM
											".TABLE_MANUFACTURERS." AS m
										INNER JOIN
											".TABLE_PRODUCTS." AS p ON(m.manufacturers_id = p.manufacturers_id)
										GROUP BY m.manufacturers_name
										ORDER BY m.manufacturers_name;");
	if(xtc_db_num_rows($manufacturers_query)) {
		if (xtc_db_num_rows($manufacturers_query) <= MAX_DISPLAY_MANUFACTURERS_IN_A_LIST) {
			// Display a list
			$manufacturers_list = '';
			while ($manufacturers = xtc_db_fetch_array($manufacturers_query)) {
				$manufacturers_name = ((strlen($manufacturers['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? substr($manufacturers['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN).'..' : $manufacturers['manufacturers_name']);
				if (isset($_GET['manufacturers_id']) && ($_GET['manufacturers_id'] == $manufacturers['manufacturers_id']))
					$manufacturers_name = '<b>'.$manufacturers_name.'</b>';
				
				$manufacturers_list .= '<a href="'.xtc_href_link(FILENAME_DEFAULT, 'manufacturers_id='.$manufacturers['manufacturers_id']).'">'.$manufacturers_name.'</a><br>';
			}
			$box_manufacturers = $manufacturers_list;
		} else {
			$dropdown  = '<select name="" onchange="window.location.href=this.value" size="'.MAX_MANUFACTURERS_LIST.'" title="'.WCAG_MANUFACTURERS_LABEL.'" id="manufacturerdropdown">';
			$dropdown .= '<option>'.PULL_DOWN_DEFAULT.'</option>';
			while ($manufacturers = xtc_db_fetch_array($manufacturers_query, true)) {
				$name_raw = ((strlen($manufacturers['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? substr($manufacturers['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN).'..' : $manufacturers['manufacturers_name']);
				$dropdown .= "<option value='".xtc_href_link(FILENAME_DEFAULT, 'manufacturers_id='.$manufacturers['manufacturers_id'])."'>".$name_raw."</option>";
			}
			$dropdown .= '</select>';
			$box_manufacturers  = xtc_draw_form('manufacturers', DIR_WS_CATALOG, 'GET');
			$box_manufacturers .= $dropdown;
			$box_manufacturers .= '</form>';
		}
		$box_smarty->assign('BOX_CONTENT', $box_manufacturers);
		$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
		$box_smarty->assign('box_name', getBoxName('manufacturers'));
		$box_smarty->assign('box_class_name', getBoxCSSName('manufacturers'));
		if(!$cache)
			$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
		else
			$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html', $cache_id);
	}
}
