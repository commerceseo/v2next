<?php
/*-----------------------------------------------------------------
* 	$Id: box.php 420 2013-06-19 18:04:39Z akausch $
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
$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');

$title = getBoxTitle($box_name);
$box_smarty->assign('box_name', $title);
$box_smarty->assign('box_class_name', getBoxCSSName($box_name));
$box_smarty->assign('BOX_TITLE', $title);

$content = '';
$content = getBoxFlag($box_name);

if($content =='0' || $content == '')
	$content = getBoxContent($box_name);
else
	$content = cseo_get_content($content);

	
$box_smarty->assign('BOX_CONTENT', $content);

$box_smarty->assign('language', $_SESSION['language']);

if (!CacheCheck()) {
	$box_smarty->caching = false;
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
} else {
	$box_smarty->caching = true;	
	$box_smarty->cache_lifetime=CACHE_LIFETIME;
	$box_smarty->cache_modified_check=CACHE_CHECK;
	$cache_id = $_SESSION['language'].'_'.$box_name;
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html',$cache_id);
}
