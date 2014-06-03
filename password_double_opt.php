<?php

/* -----------------------------------------------------------------
 * 	$Id: password_double_opt.php 521 2013-07-24 11:34:09Z akausch $
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

require ('includes/application_top.php');

// create smarty elements
$smarty = new Smarty;

// include boxes
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

// include needed functions
require_once (DIR_FS_INC . 'xtc_random_charcode.inc.php');
require_once (DIR_FS_INC . 'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC . 'xtc_validate_password.inc.php');
require_once (DIR_FS_INC . 'xtc_rand.inc.php');

$case = double_opt;
$info_message = '';
// $info_message = TEXT_PASSWORD_FORGOTTEN;

if (isset($_GET['action']) && ($_GET['action'] == 'first_opt_in')) {
    $check_customer_query = xtc_db_query("SELECT customers_email_address, customers_id FROM " . TABLE_CUSTOMERS . " WHERE customers_email_address = '" . xtc_db_input($_POST['email']) . "'");
    $check_customer = xtc_db_fetch_array($check_customer_query);

    $vlcode = xtc_random_charcode(32);
    $link = xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=verified&customers_id=' . $check_customer['customers_id'] . '&key=' . $vlcode, 'SSL');

    // assign language to template for caching
    $smarty->assign('language', $_SESSION['language']);
    $smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
    $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

    // assign vars
    $smarty->assign('EMAIL', $check_customer['customers_email_address']);
    $smarty->assign('LINK', $link);

    $smarty->caching = false;
    require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
    $html_mail = $smarty->fetch('html:password_verification');
    $html_mail .= $signatur_html;
    $txt_mail = $smarty->fetch('txt:password_verification');
    $txt_mail .= $signatur_text;
    require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
    $mail_data = cseo_get_mail_data('password_verification');
    //Antispam
    $antispam_query = xtc_db_fetch_array(xtDBquery("SELECT 
													id, answer 
													FROM " . TABLE_CSEO_ANTISPAM . " 
													WHERE language_id = '" . (int) $_SESSION['languages_id'] . "'
													AND id = '" . $_POST['antispamid'] . "'
													"));

    // if(strtoupper($antispam_query['answer']) == strtoupper($_POST['codeanwser'])) {
    if (mb_strtolower($antispam_query['answer'], 'UTF-8') == mb_strtolower($_POST["codeanwser"], 'UTF-8')) {
        if (!xtc_db_num_rows($check_customer_query)) {
            $case = wrong_mail;
            $info_message = TEXT_EMAIL_ERROR;
        } else {
            $case = first_opt_in;
            xtc_db_query("update " . TABLE_CUSTOMERS . " set password_request_key = '" . $vlcode . "' where customers_id = '" . $check_customer['customers_id'] . "'");

            $password_verification_subject = str_replace('{$shop_name}', STORE_NAME, $mail_data['EMAIL_SUBJECT']);

            xtc_php_mail($mail_data['EMAIL_ADDRESS'], $mail_data['EMAIL_ADDRESS_NAME'], $check_customer['customers_email_address'], '', '', $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], '', '', $password_verification_subject, $html_mail, $txt_mail);
        }
    } else {
        $case = code_error;
        $info_message = TEXT_CODE_ERROR;
    }
}

// Verification
if (isset($_GET['action']) && ($_GET['action'] == 'verified')) {
    $check_customer_query = xtc_db_query("SELECT customers_id, customers_email_address, password_request_key from " . TABLE_CUSTOMERS . " WHERE customers_id = '" . (int) $_GET['customers_id'] . "' AND password_request_key = '" . xtc_db_input($_GET['key']) . "'");
    $check_customer = xtc_db_fetch_array($check_customer_query);
    if (!xtc_db_num_rows($check_customer_query) || $_GET['key'] == "") {
        $case = no_account;
        $info_message = TEXT_NO_ACCOUNT;
    } else {

        $newpass = xtc_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
        $crypted_password = xtc_encrypt_password($newpass);

        xtc_db_query("UPDATE " . TABLE_CUSTOMERS . " SET customers_password = '" . $crypted_password . "' WHERE customers_email_address = '" . xtc_db_input($check_customer['customers_email_address']) . "'");
        xtc_db_query("UPDATE " . TABLE_CUSTOMERS . " SET password_request_key = '' WHERE customers_id = '" . $check_customer['customers_id'] . "'");
        // assign language to template for caching
        $smarty->assign('language', $_SESSION['language']);
        $smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
        $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

        // assign vars
        $smarty->assign('EMAIL', $check_customer['customers_email_address']);
        $smarty->assign('NEW_PASSWORD', $newpass);

        $smarty->caching = false;
        require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
        $html_mail = $smarty->fetch('html:new_password');
        $html_mail .= $signatur_html;
        $txt_mail = $smarty->fetch('txt:new_password');
        $txt_mail .= $signatur_text;
        require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
        $mail_data = cseo_get_mail_data('new_password');

        $new_password_subject = str_replace('{$shop_name}', STORE_NAME, $mail_data['EMAIL_SUBJECT']);

        xtc_php_mail($mail_data['EMAIL_ADDRESS'], $mail_data['EMAIL_ADDRESS_NAME'], $check_customer['customers_email_address'], '', '', $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], '', '', $new_password_subject, $html_mail, $txt_mail);
        if (!isset($mail_error)) {
            xtc_redirect(xtc_href_link(FILENAME_LOGIN, 'info_message=' . urlencode(TEXT_PASSWORD_SENT), 'SSL', true, false));
        }
    }
}

$breadcrumb->add(NAVBAR_TITLE_PASSWORD_DOUBLE_OPT, xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, '', 'SSL'));

require (DIR_WS_INCLUDES . 'header.php');


switch ($case) {
    case first_opt_in :
        $smarty->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
        $smarty->assign('info_message', TEXT_LINK_MAIL_SENDED);
        $smarty->assign('language', $_SESSION['language']);
        $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $smarty->caching = false;
        $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/password_messages.html', USE_TEMPLATE_DEVMODE));
        break;

    case second_opt_in :
        $smarty->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
        $smarty->assign('info_message', TEXT_PASSWORD_MAIL_SENDED);
        $smarty->assign('language', $_SESSION['language']);
        $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $smarty->caching = false;
        $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/password_messages.html', USE_TEMPLATE_DEVMODE));
        break;

    case code_error :

        $smarty->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
        $smarty->assign('info_message', TEXT_CODE_ERROR);
        $smarty->assign('message', TEXT_PASSWORD_FORGOTTEN);
        $smarty->assign('SHOP_NAME', STORE_NAME);
        $smarty->assign('FORM_ACTION', xtc_draw_form('sign', xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=first_opt_in', 'SSL')));
        $smarty->assign('INPUT_EMAIL', xtc_draw_input_field('email', xtc_db_input($_POST['email'])));
        //Antispam beginn
        $antispam_query = xtc_db_fetch_array(xtDBquery("SELECT id, question FROM " . TABLE_CSEO_ANTISPAM . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY rand() LIMIT 1"));
        $smarty->assign('ANTISPAMCODEID', xtc_draw_hidden_field('antispamid', $antispam_query['id']));
        $smarty->assign('ANTISPAMCODEQUESTION', $antispam_query['question']);
        $smarty->assign('INPUT_ANTISPAMCODE', xtc_draw_input_field('codeanwser', '', 'size="6" maxlength="6"', 'text', false));
        $smarty->assign('ANTISPAMCODEACTIVE', ANTISPAM_PASSWORD);
        //Antispam end
        $smarty->assign('BUTTON_SEND', xtc_image_submit('button_send.gif', IMAGE_BUTTON_CONTINUE));
        $smarty->assign('language', $_SESSION['language']);
        $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $smarty->caching = false;
        $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/password_double_opt_in.html', USE_TEMPLATE_DEVMODE));
        break;

    case wrong_mail :

        $smarty->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
        $smarty->assign('info_message', TEXT_EMAIL_ERROR);
        $smarty->assign('message', TEXT_PASSWORD_FORGOTTEN);
        $smarty->assign('SHOP_NAME', STORE_NAME);
        $smarty->assign('FORM_ACTION', xtc_draw_form('sign', xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=first_opt_in', 'SSL')));
        $smarty->assign('INPUT_EMAIL', xtc_draw_input_field('email', xtc_db_input($_POST['email'])));
        //Antispam beginn
        $antispam_query = xtc_db_fetch_array(xtDBquery("SELECT id, question FROM " . TABLE_CSEO_ANTISPAM . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY rand() LIMIT 1"));
        $smarty->assign('ANTISPAMCODEID', xtc_draw_hidden_field('antispamid', $antispam_query['id']));
        $smarty->assign('ANTISPAMCODEQUESTION', $antispam_query['question']);
        $smarty->assign('INPUT_ANTISPAMCODE', xtc_draw_input_field('codeanwser', '', 'size="6" maxlength="6"', 'text', false));
        $smarty->assign('ANTISPAMCODEACTIVE', ANTISPAM_PASSWORD);
        //Antispam end
        $smarty->assign('BUTTON_SEND', xtc_image_submit('button_send.gif', IMAGE_BUTTON_CONTINUE));
        $smarty->assign('language', $_SESSION['language']);
        $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $smarty->caching = false;
        $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/password_double_opt_in.html', USE_TEMPLATE_DEVMODE));
        break;

    case no_account :
        $smarty->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
        $smarty->assign('info_message', TEXT_NO_ACCOUNT);
        $smarty->assign('language', $_SESSION['language']);
        $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $smarty->caching = false;
        $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/password_messages.html', USE_TEMPLATE_DEVMODE));
        break;

    case double_opt :
        $smarty->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
        $smarty->assign('info_message', $info_message);
        $smarty->assign('message', TEXT_PASSWORD_FORGOTTEN);
        $smarty->assign('SHOP_NAME', STORE_NAME);
        $smarty->assign('FORM_ACTION', xtc_draw_form('sign', xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=first_opt_in', 'SSL')));
        //Antispam beginn
        $antispam_query = xtc_db_fetch_array(xtDBquery("SELECT id, question FROM " . TABLE_CSEO_ANTISPAM . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY rand() LIMIT 1"));
        $smarty->assign('ANTISPAMCODEID', xtc_draw_hidden_field('antispamid', $antispam_query['id']));
        $smarty->assign('ANTISPAMCODEQUESTION', $antispam_query['question']);
        $smarty->assign('INPUT_ANTISPAMCODE', xtc_draw_input_field('codeanwser', '', 'size="6" maxlength="6"', 'text', false));
        $smarty->assign('ANTISPAMCODEACTIVE', ANTISPAM_PASSWORD);
        //Antispam end
        $smarty->assign('INPUT_EMAIL', xtc_draw_input_field('email', xtc_db_input($_POST['email'])));
        $smarty->assign('BUTTON_SEND', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));
        $smarty->assign('FORM_END', '</form>');
        $smarty->assign('language', $_SESSION['language']);
        $smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
        $smarty->caching = false;
        $main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/password_double_opt_in.html', USE_TEMPLATE_DEVMODE));
        break;
}
$smarty->assign('main_content', $main_content);
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;

$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));

include ('includes/application_bottom.php');
