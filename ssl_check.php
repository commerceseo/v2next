<?php

/* -----------------------------------------------------------------
 * 	$Id: ssl_check.php 521 2013-07-24 11:34:09Z akausch $
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
// include boxes
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

$breadcrumb->add(NAVBAR_TITLE_SSL_CHECK, xtc_href_link(FILENAME_SSL_CHECK));

require (DIR_WS_INCLUDES . 'header.php');
$smarty->assign('BUTTON_CONTINUE', '<a href="' . xtc_href_link(FILENAME_DEFAULT) . '">' . xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>');

$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;
if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/ssl_check.html')) {
	$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/ssl_check.html', USE_TEMPLATE_DEVMODE));
}else{
	$main_content = $smarty->fetch(cseo_get_usermod('base/module/ssl_check.html', USE_TEMPLATE_DEVMODE));
}
$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
