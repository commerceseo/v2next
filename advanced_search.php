<?php

/* -----------------------------------------------------------------
 * 	$Id: advanced_search.php 873 2014-03-25 16:42:10Z akausch $
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

include ('includes/application_top.php');
$smarty = new Smarty;
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

require_once (DIR_FS_INC . 'xtc_get_categories.inc.php');
require_once (DIR_FS_INC . 'xtc_get_manufacturers.inc.php');
require_once (DIR_FS_INC . 'xtc_checkdate.inc.php');
require_once (DIR_FS_INC . 'xtc_hide_session_id.inc.php');

$breadcrumb->add(NAVBAR_TITLE_ADVANCED_SEARCH, xtc_href_link(FILENAME_ADVANCED_SEARCH));
require_once (DIR_WS_INCLUDES . 'header.php');

$error = '';
if (isset($_GET['errorno'])) {
    if (($_GET['errorno'] & 1) == 1) {
        $error .= str_replace('\n', '<br />', JS_AT_LEAST_ONE_INPUT);
    }
    if (($_GET['errorno'] & 10) == 10) {
        $error .= str_replace('\n', '<br />', JS_INVALID_FROM_DATE);
    }
    if (($_GET['errorno'] & 100) == 100) {
        $error .= str_replace('\n', '<br />', JS_INVALID_TO_DATE);
    }
    if (($_GET['errorno'] & 1000) == 1000) {
        $error .= str_replace('\n', '<br />', JS_TO_DATE_LESS_THAN_FROM_DATE);
    }
    if (($_GET['errorno'] & 10000) == 10000) {
        $error .= str_replace('\n', '<br />', JS_PRICE_FROM_MUST_BE_NUM);
    }
    if (($_GET['errorno'] & 100000) == 100000) {
        $error .= str_replace('\n', '<br />', JS_PRICE_TO_MUST_BE_NUM);
    }
    if (($_GET['errorno'] & 1000000) == 1000000) {
        $error .= str_replace('\n', '<br />', JS_PRICE_TO_LESS_THAN_PRICE_FROM);
    }
    if (($_GET['errorno'] & 10000000) == 10000000) {
        $error .= str_replace('\n', '<br />', JS_INVALID_KEYWORDS);
    }
} elseif (isset($_SESSION['error_msg'])) {
    $error = urldecode($_SESSION['error_msg']);
    unset($_SESSION['error_msg']);
}

$smarty->assign('error', $error);
$smarty->assign('FORM_ACTION', xtc_draw_form('advanced_search', xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get', 'onsubmit="return check_form(this);"') . xtc_hide_session_id());
$smarty->assign('INPUT_KEYWORDS', xtc_draw_input_field('keywords', '', 'style="width: 100%"'));
$smarty->assign('HELP_LINK', xtc_href_link(FILENAME_POPUP_SEARCH_HELP));
$smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_search.gif', IMAGE_BUTTON_SEARCH));
$smarty->assign('SELECT_CATEGORIES', xtc_draw_pull_down_menu('categories_id', xtc_get_categories(array(array('id' => '', 'text' => TEXT_ALL_CATEGORIES)))));
$smarty->assign('ENTRY_SUBCAT', xtc_draw_checkbox_field('inc_subcat', '1', true));
$smarty->assign('SELECT_MANUFACTURERS', xtc_draw_pull_down_menu('manufacturers_id', xtc_get_manufacturers(array(array('id' => '', 'text' => TEXT_ALL_MANUFACTURERS)))));
$smarty->assign('SELECT_PFROM', xtc_draw_input_field('pfrom'));
$smarty->assign('SELECT_PTO', xtc_draw_input_field('pto'));
$smarty->assign('FORM_END', '</form>');

$cseo_advanced_search = cseohookfactory::create_object('AdvancedSearchExtender');
$cseo_advanced_search->proceed();
$cseo_extender_result_array = $cseo_advanced_search->get_response();
if (is_array($cseo_extender_result_array)) {
    foreach ($cseo_extender_result_array AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}

$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;

if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/advanced_search.html')) {
	$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/advanced_search.html', USE_TEMPLATE_DEVMODE));
}else{
	$main_content = $smarty->fetch(cseo_get_usermod('base/module/advanced_search.html', USE_TEMPLATE_DEVMODE));
}
$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
