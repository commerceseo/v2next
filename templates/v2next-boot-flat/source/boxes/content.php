<?php
/*-----------------------------------------------------------------
* 	$Id: content.php 371 2013-06-10 12:44:12Z akausch $
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
$content_string = '';

$box_smarty->assign('language', $_SESSION['language']);

if (!CacheCheck()) {
	$cache=false;
	$box_smarty->caching = false;
} else {
	$cache=true;
	$box_smarty->caching = true;
	$box_smarty->cache_lifetime = CACHE_LIFETIME;
	$box_smarty->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'].'content';
}

if (!$box_smarty->isCached(CURRENT_TEMPLATE.'/boxes/box_content.html', $cache_id) || !$cache) {

	$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');

	if(GROUP_CHECK == 'true')
		$group_check = "and group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";

	$content_query = xtDBquery("SELECT
	 							content_id,
			 					categories_id,
			 					parent_id,
			 					content_title,
								content_out_link,
								content_link_target,
								content_link_type,
			 					content_group
			 					FROM ".TABLE_CONTENT_MANAGER."
			 					WHERE languages_id='".(int) $_SESSION['languages_id']."'
								AND parent_id = '0'
			 					AND file_flag = '1'
								".$group_check."
								AND content_status = '1'
								ORDER BY sort_order");

	$getChild = xtc_db_fetch_array(xtc_db_query("SELECT parent_id FROM content_manager WHERE content_group = '".$_GET['coID']."'"));
	$content_string = '<nav><ul>';
	while ($content_data = xtc_db_fetch_array($content_query, true)) {
		$active_link = '';

		if($_GET['coID'] == $content_data['content_group'])
			$active_link = ' class="Current"';

		if($content_data['content_out_link'])
			$content_string .= '<li class="list"><a'.$active_link.' title="'.$content_data['content_title'].'" rel="'.$content_data['content_link_type'].'" target="'.$content_data['content_link_target'].'" href="'.$content_data['content_out_link'].'">'.$content_data['content_title'].'</a>';
		else {
			if ($content_data['content_group'] == '7' && ENABLE_SSL == true) {
				$content_string .= '<li class="list"><a'.$active_link.' title="'.$content_data['content_title'].'" href="'.xtc_href_link(FILENAME_CONTENT, 'coID='.$content_data['content_group'], 'SSL').'">'.$content_data['content_title'].'</a>';
			} else {
				$content_string .= '<li class="list"><a'.$active_link.' title="'.$content_data['content_title'].'" href="'.xtc_href_link(FILENAME_CONTENT, 'coID='.$content_data['content_group']).'">'.$content_data['content_title'].'</a>';
			}
		}
		if($_GET['coID'] == $content_data['content_group'] || $getChild['parent_id'] == $content_data['content_group']) {
			$get_child = cseo_get_content_child($content_data['content_group']);
			if(is_array($get_child)){
				$content_string .= '<ul class="sub">';
				foreach($get_child AS $child_data){
					if($child_data['content_out_link'])
						$content_string .= '<li class="main_level_2"><a'.($child_data['content_group'] == $_GET['coID'] ? ' class="Current"':'').' title="'.$child_data['content_title'].'" rel="'.$child_data['content_link_target'].'" target="'.$child_data['content_link_type'].'" href="'.$child_data['content_out_link'].'">'.$child_data['content_title'].'</a></li>';
					else
						$content_string .= '<li class="main_level_2"><a'.($child_data['content_group'] == $_GET['coID'] ? ' class="Current"':'').' title="'.$child_data['content_title'].'" href="'.xtc_href_link(FILENAME_CONTENT, 'coID='.$child_data['content_group']).'">'.$child_data['content_title'].'</a></li>';
				}
				$content_string .= '</ul>';
			}
		}
		$content_string .= '</li>';
	}
	$content_string .= '</ul></nav>';

	if ($content_string != '') {
		$box_smarty->assign('BOX_CONTENT', $content_string);
	}
}
$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
$box_smarty->assign('box_name', getBoxName('content'));
$box_smarty->assign('box_class_name', getBoxCSSName('content'));

if(!$cache)
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
else
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html', $cache_id);
