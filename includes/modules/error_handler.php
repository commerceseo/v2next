<?php

/* -----------------------------------------------------------------
 * 	$Id: error_handler.php 928 2014-03-31 13:56:47Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
include (DIR_WS_MODULES . 'fuzzy_search.php');

$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('ERROR', $error);
$module_smarty->assign('BUTTON', '<a href="javascript:history.back(1)">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
$module_smarty->assign('FORM_ACTION', xtc_draw_form('new_find', xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', $request_type, false), 'get') . xtc_hide_session_id());
$module_smarty->assign('INPUT_SEARCH', xtc_draw_input_field('keywords', '', 'size="30" maxlength="30"'));
$module_smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_quick_find.gif', BOX_HEADING_SEARCH));
$module_smarty->assign('LINK_ADVANCED', xtc_href_link(FILENAME_ADVANCED_SEARCH));
$module_smarty->assign('FORM_END', '</form>');
$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$module_smarty->caching = false;
$module = $module_smarty->fetch(cseo_get_usermod('base/module/error_message.html', USE_TEMPLATE_DEVMODE));

if (strstr($PHP_SELF, FILENAME_PRODUCT_INFO)) {
    $product_info = $module;
}

$smarty->assign('main_content', $module);
