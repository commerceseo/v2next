<?php
/*-----------------------------------------------------------------
* 	$Id: add_a_quickie.php 371 2013-06-10 12:44:12Z akausch $
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

if ($_SESSION['customers_status']['customers_status_show_price'] != '0') {
	$box_smarty = new smarty;
	$box_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
	$box_smarty->assign('FORM_ACTION','<form id="quick_add" method="post" action="' . xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array('action')) . 'action=add_a_quickie', 'NONSSL') . '">');
	$box_smarty->assign('INPUT_FIELD',xtc_draw_input_field('quickie','','size="14"'));
	$box_smarty->assign('SUBMIT_BUTTON',xtc_image_submit('button_add_quick.gif', IMAGE_BUTTON_ADD_A_QUICKIE));
	$box_smarty->assign('FORM_END','</form>');
	$box_smarty->assign('BOX_CONTENT', $box_content);
	$box_smarty->assign('language', $_SESSION['language']);
	$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
	$box_smarty->assign('box_name', getBoxName('add_a_quickie'));
	$box_smarty->assign('box_class_name', getBoxCSSName('add_a_quickie'));

	if (!CacheCheck()) {
		$box_smarty->caching = false;
		$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_add_a_quickie.html');
	} else {
		$box_smarty->caching = true;	
		$box_smarty->cache_lifetime=CACHE_LIFETIME;
		$box_smarty->cache_modified_check=CACHE_CHECK;
		$cache_id = $_SESSION['language'].'add_a_quickie';
		$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_add_a_quickie.html',$cache_id);
	}
}
