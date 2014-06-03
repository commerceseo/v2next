<?php

/* -----------------------------------------------------------------
 * 	$Id: shop-bewertungen-schreiben.php 542 2013-08-02 13:02:54Z akausch $
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
require_once (DIR_FS_INC . 'xtc_validate_email.inc.php');

include(DIR_WS_CLASSES . 'class.shopvoting.php');

$smarty = new Smarty;
$voting = new Shopvoting();

$breadcrumb->add(NAVBAR_TITLE_SHOPBEWERTUNGEN_WRITE, xtc_href_link(Shopvoting::FILENAME_BEWERTUNGSSEITE_SCHREIBEN));

/**
 * check the group permission
 */
$writeCheck = $voting->getGroupAccess($voting->customer_group_write, (int) $_SESSION['customers_status']['customers_status_id']);
$captchaCheck = $voting->getGroupAccess($voting->customer_group_captcha, (int) $_SESSION['customers_status']['customers_status_id']);

if ($writeCheck != 1 || $voting->voting_module_aktive == 0) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

/**
 * get customers values if login
 */
$vars_array = array();

if (isset($_SESSION['customer_id'])) {
    $customer_values = array();
    $customer_values = $voting->getLoginCustomersValues((int) $_SESSION['customer_id']);
    $vars_array['voting_customers_firstname'] = $customer_values['customers_firstname'];
    $vars_array['voting_customers_lastname'] = $customer_values['customers_lastname'];
    $vars_array['voting_customers_email'] = $customer_values['customers_email_address'];
}

/**
 * check and insert the voting
 */
if (isset($_POST['send'])) {

    $vars_array['ratingshop'] = (int) $_POST[Shopvoting::COLUMN_SHOPRATING];
    $vars_array['ratingware'] = (int) $_POST[Shopvoting::COLUMN_WARE];
    $vars_array['ratingversand'] = (int) $_POST[Shopvoting::COLUMN_VERSAND];
    $vars_array['ratingservice'] = (int) $_POST[Shopvoting::COLUMN_SERVICE];
    $vars_array['ratingseite'] = (int) $_POST[Shopvoting::COLUMN_SEITE];
    $vars_array['kommentar'] = substr($_POST['kommentar'], 0, $voting->front_page_character);
    $vars_array['voting_customers_lastname'] = xtc_db_prepare_input($_POST['lastname']);
    $vars_array['voting_customers_firstname'] = xtc_db_prepare_input($_POST['firstname']);
    $vars_array['bewertung_kundenid'] = (int) $_SESSION['customer_id'];
    $vars_array['bewertungs_ip'] = $_SERVER['REMOTE_ADDR'];
    $vars_array['bewertung_sprache'] = (int) $_SESSION['languages_id'];
    $vars_array['voting_customers_email'] = $_POST['email'];
    $vars_array['orders_id'] = $_POST['orders_id'];
    $vars_array['vvcode'] = $_POST['vvcode'];
    $vars_array['captchacheck'] = $captchaCheck;

    $error = $voting->getErrorCheck($vars_array);

    if (strlen($error) == 0) {
        $voting->setDbVoting($vars_array);
        $vars_array['ratingshop'] = '';
        $vars_array['kommentar'] = '';
        $vars_array['voting_customers_email'] = '';
        xtc_redirect(xtc_href_link(Shopvoting::FILENAME_BEWERTUNGSSEITE, 'bewertung=ok', 'NONSSL'));
    }
}

require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');
require_once (DIR_WS_INCLUDES . 'header.php');

/**
 * assign some smarty
 */
$smarty->assign('FORMSTART', '<form action="' . xtc_href_link(Shopvoting::FILENAME_BEWERTUNGSSEITE_SCHREIBEN) . '"name="bewertungschreiben" method="post">');
$smarty->assign('VORNAME_POST', $vars_array['voting_customers_firstname']);
$smarty->assign('NACHNAME_POST', $vars_array['voting_customers_lastname']);
$smarty->assign('NAME_CHECK', $voting->required_name);
$smarty->assign('COMMENT_CHECK', $voting->required_comment);
$smarty->assign('EMAIL_POST', $vars_array['voting_customers_email']);
$smarty->assign('ORDERS_ID_POST', $vars_array['orders_id']);
$smarty->assign('ORDERS_ID_CHECK', $voting->required_order_id);
$smarty->assign('KOMMENTAR', $vars_array['kommentar']);
$smarty->assign('OPTFIELD_SHOP', $voting->getRadioButtons($vars_array['ratingshop'], Shopvoting::COLUMN_SHOPRATING));
$smarty->assign('OPTFIELD_WARE', $voting->getRadioButtons($vars_array['ratingware'], Shopvoting::COLUMN_WARE));
$smarty->assign('OPTFIELD_VERSAND', $voting->getRadioButtons($vars_array['ratingversand'], Shopvoting::COLUMN_VERSAND));
$smarty->assign('OPTFIELD_SERVICE', $voting->getRadioButtons($vars_array['ratingservice'], Shopvoting::COLUMN_SERVICE));
$smarty->assign('OPTFIELD_SEITE', $voting->getRadioButtons($vars_array['ratingseite'], Shopvoting::COLUMN_SEITE));
$smarty->assign('IMG_PATH', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/shopwertung/');
$smarty->assign('IMG_PATH_BUTTON', xtc_image_submit('button_send.gif', IMAGE_BUTTON_CONTINUE, 'value="send" name="send"'));
$smarty->assign('BUTTON_BACK', '<a href="' . xtc_href_link(Shopvoting::FILENAME_BEWERTUNGSSEITE, '', 'NONSSL') . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
$smarty->assign('INPUT_CODE', xtc_draw_input_field('vvcode', '', 'size="6" maxlength="6"', 'text', false));
$smarty->assign('VVIMG', xtc_href_link('display_captcha.php', '', 'SSL'));
$smarty->assign('ZEICHEN', $voting->front_page_character);

/**
 * check the captcha and maybe errors
 */
if ($captchaCheck == 1) {
    $smarty->assign('CAPTCHACHECK', 'captcha');
}

if (strlen($error) > 0) {
    $smarty->assign('ERROR', $voting->getErrorCheck($vars_array));
}

/**
 * caching is not necessary
 */
$smarty->caching = false;
$smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/shopbewertung_schreiben.html', USE_TEMPLATE_DEVMODE));
$smarty->assign('main_content', $main_content);
$smarty->loadFilter('output', 'note');
$smarty->loadFilter('output', 'trimwhitespace');
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
