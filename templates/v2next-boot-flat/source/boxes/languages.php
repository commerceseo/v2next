<?php
/*-----------------------------------------------------------------
* 	ID:						languages.php
* 	Letzter Stand:			v2.3
* 	zuletzt geaendert von:	cseoak
* 	Datum:					2012/11/19
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

require_once(DIR_FS_INC . 'xtc_get_all_get_params.inc.php');
if (!isset($lng) && !is_object($lng)) {
	include(DIR_WS_CLASSES . 'class.language.php');
	$lng = new language;
}

$languages_string = '';
$count_lng = '';
reset($lng->catalog_languages);
if(sizeof($lng->catalog_languages) > 1) {
	if(MODULE_COMMERCE_SEO_INDEX_STATUS == 'True') {
		require_once (DIR_FS_INC.'commerce_seo.inc.php');
		!$commerceSeo ? $commerceSeo = new CommerceSeo() : false;
	}
	if(basename($PHP_SELF) != 'commerce_seo_url.php')
		$uri = basename($PHP_SELF);
	else
		$uri = $_SERVER['REQUEST_URI'];
		
	while (list($key, $value) = each($lng->catalog_languages)) {
		$count_lng++;
			if(isset($_GET['cPath']) && !isset($_GET['products_id'])) {
				$cat_tmp = explode('_', $_GET['cPath']);
				$cat = array_reverse($cat_tmp);
				$uri = xtc_href_link(FILENAME_DEFAULT, 'cPath='.$cat[0].'&language='.$key, $request_type);
			} elseif(isset($_GET['products_id'])) {
				$uri = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id='.PRODUCT_ID.'&language=' . $key, $request_type).'&language=' . $key;
			} elseif(isset($_GET['coID']) && $_GET['coID'] != 5) {
				$uri = xtc_href_link(FILENAME_CONTENT, 'coID='.$_GET['coID'].'&language=' . $key, $request_type);
			} else {
				$uri =  xtc_href_link(FILENAME_DEFAULT, 'language='.$key, $request_type);
			}
		$languages_string .= ' <a href="' . $uri . '">' . xtc_image('lang/' .  $value['directory'] .'/' . $value['image'], $value['name']) . '</a> ';
	}

	if ($count_lng > 1 ) {
		$box_smarty = new smarty;
		$box_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
		$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
		$box_smarty->assign('box_name', getBoxName('languages'));
		$box_smarty->assign('box_class_name', getBoxCSSName('languages'));
		$box_smarty->assign('BOX_CONTENT', $languages_string);
		$box_smarty->assign('language', $_SESSION['language']);

		$box_smarty->caching = false;
		$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
	}
}
