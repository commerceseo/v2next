<?php
/*-----------------------------------------------------------------
* 	$Id: loginbox.php 371 2013-06-10 12:44:12Z akausch $
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
if (!isset($_SESSION['customer_id'])) {

	require_once (DIR_FS_INC.'xtc_image_submit.inc.php');
	$box_smarty->assign('FORM_ACTION', '<form id="loginForm" method="post" action="'.xtc_href_link(FILENAME_LOGIN, 'action=process', 'SSL').'">');
	$box_smarty->assign('FIELD_EMAIL', xtc_draw_input_field('email_address', '', 'maxlength="50" tabindex="1" id="loginemail" required value="" placeholder="'.BOX_EMAIL_VALUE.'"'));
	$box_smarty->assign('FIELD_PWD', xtc_draw_password_field('password', '', 'maxlength="30" tabindex="2" placeholder="'.BOX_EMAIL_PASSWD.'" required id="loginpassword"'));
	$box_smarty->assign('BUTTON', xtc_image_submit('button_login_small.gif', IMAGE_BUTTON_LOGIN));
	$box_smarty->assign('LINK_LOST_PASSWORD', xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, '', 'SSL'));
	$box_smarty->assign('FORM_END', '</form>');
	$box_smarty->assign('LOGGEDIN', 'true');

} else {

	$box_smarty->assign('LINK_LOGOFF', xtc_href_link(FILENAME_LOGOFF, '', 'SSL'));
	$box_smarty->assign('LINK_ACCOUNT', xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
	$box_smarty->assign('LINK_EDIT', xtc_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
	$box_smarty->assign('LINK_ADDRESS', xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
	$box_smarty->assign('LINK_PASSWORD', xtc_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL'));
	$box_smarty->assign('LINK_ORDERS', xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
	$box_smarty->assign('LINK_NEWSLETTER', xtc_href_link(FILENAME_NEWSLETTER, '', 'SSL'));

}
$box_smarty->caching = false;
$box_smarty->assign('language', $_SESSION['language']);
$box_smarty->assign('box_name', getBoxName('loginbox'));
$box_smarty->assign('box_class_name', getBoxCSSName('loginbox'));
$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_login.html');

$smarty->assign('box_LOGIN', $box_select);
