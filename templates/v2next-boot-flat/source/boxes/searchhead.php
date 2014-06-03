<?php
/*-----------------------------------------------------------------
* 	$Id: searchhead.php 371 2013-06-10 12:44:12Z akausch $
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
$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
require_once (DIR_FS_INC.'xtc_image_submit.inc.php');
require_once (DIR_FS_INC.'xtc_hide_session_id.inc.php');

$box_smarty->assign('FORM_ACTION', xtc_draw_form('quick_find', xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', $request_type, false), 'get', '' , 'class="navbar-form navbar-right" role="search"')."\n".xtc_hide_session_id()."\n".xtc_draw_hidden_field('n','1'));
$box_smarty->assign('INPUT_SEARCH', '<input type="search" name="keywords" value="'.NAVBAR_TITLE_SEARCH.'" id="txtSearch" onclick="if(this.value==this.defaultValue) this.value=\'\';return false" onblur="if( this.value.replace(/\s/g, \'\') == \'\' ) this.value=this.defaultValue; return false" onkeyup="auto_suggest();" autocomplete="off" accesskey="s" maxlength="60" class="form-control">');
$box_smarty->assign('BUTTON_SUBMIT', '<button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-search"></i></button>');
$box_smarty->assign('FORM_END', '</form>');
$box_smarty->assign('language', $_SESSION['language']);
$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
$box_smarty->assign('box_name', getBoxName('searchhead'));
$box_smarty->assign('box_class_name', getBoxCSSName('searchhead'));
// set cache ID
if (!CacheCheck()) {
	$box_smarty->caching = false;
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_searchhead.html');
} else {
	$box_smarty->caching = true;
	$box_smarty->cache_lifetime = CACHE_LIFETIME;
	$box_smarty->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].'searchhead';
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_searchhead.html', $cache_id);
}
