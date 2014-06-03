<?php

/* -----------------------------------------------------------------
 * 	$Id: gv_send.php 928 2014-03-31 13:56:47Z akausch $
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

// WENN GUTSCHEIN_SYSTEM DEAKTIVIERT, DANN WEITERLEITEN
if (ACTIVATE_GIFT_SYSTEM != 'true') {
    xtc_redirect(FILENAME_DEFAULT);
}

// WENN KUNDE NICHT ANGEMELDET, DANN WEITERLEITEN
if (!isset($_SESSION['customer_id'])) {
    xtc_redirect(FILENAME_LOGIN);
}

// GUTHABEN DES KUNDEN AUSLESEN
$gv_query = xtc_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id = '" . (int) $_SESSION['customer_id'] . "'");
$gv_result = xtc_db_fetch_array($gv_query);

// INCLUDED FILES
require (DIR_WS_CLASSES . 'class.http_client.php');
require_once (DIR_FS_INC . 'xtc_validate_email.inc.php');

// START SMARTY
$smarty = new Smarty;

// INCLUDED BOXES
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

if (($_POST['back_x']) || ($_POST['back_y'])) {
    $_GET['action'] = '';
}


///////////////////////////////////////////////////////////////////////
// EMAIL VERSENDEN
///////////////////////////////////////////////////////////////////////
if ($_GET['action'] == 'send') {

    $error = false;

    // ERROR : KEINE EMAIL ADRESSE
    if (!xtc_validate_email(trim($_POST['email']))) {
        $error = true;
        $messageStack->add('gv_send', ERROR_ENTRY_EMAIL_ADDRESS_CHECK);
    }

    // ERROR : KEIN NAME ANGEGEBEN
    if ($_POST['to_name'] == '') {
        $error = true;
        $messageStack->add('gv_send', ERROR_ENTRY_NO_NAME);
    }

    // EINGETRAGENER BETRAG BEREINIGEN
    $gv_amount = str_replace(",", ".", $_POST['amount']);
    $gv_amount = str_replace("+", "", $gv_amount);
    $gv_amount = str_replace("-", "", $gv_amount);

    // ERROR : GUTHABEN REICHT NICHT AUS
    if ($gv_amount > $gv_result['amount'] && $_POST['amount'] != '' && $gv_amount != 0) {
        $error = true;
        $messageStack->add('gv_send', ERROR_ENTRY_AMOUNT_CHECK);
        // ERROR : KEIN GĂśLTIGES GUTHABEN ANGEGEBEN
    } else if ($gv_amount == 0 || $_POST['amount'] == '' || is_numeric($gv_amount) == false) {
        $error = true;
        $messageStack->add('gv_send', ERROR_ENTRY_NO_AMOUNT);
    }
}


///////////////////////////////////////////////////////////////////////
// EMAIL VERSENDEN
///////////////////////////////////////////////////////////////////////
if ($_GET['action'] == 'process') {

    // EINGETRAGENER BETRAG BEREINIGEN
    $gv_amount = str_replace(",", ".", $_POST['amount']);
    $gv_amount = str_replace("+", "", $gv_amount);
    $gv_amount = str_replace("-", "", $gv_amount);

    // NEUES GUTHABEN BERECHNEN UND EINTRAGEN
    $new_amount = $gv_result['amount'] - $gv_amount;
    $gv_query = xtc_db_query("update " . TABLE_COUPON_GV_CUSTOMER . " set amount = '" . $new_amount . "' where customer_id = '" . (int) $_SESSION['customer_id'] . "'");

    // NEUEN GUTSCHEIN GENERIEREN
    $id1 = create_coupon_code();
    $gv_query = xtc_db_query("insert into " . TABLE_COUPONS . " (coupon_type, coupon_code, date_created, coupon_amount) values ('G', '" . $id1 . "', now(), '" . $gv_amount . "')");
    $insert_id = xtc_db_insert_id($gv_query);

    // Name des Gutscheins eintragen
    $insert_query = xtc_db_query("insert into " . TABLE_COUPONS_DESCRIPTION . " (coupon_id, language_id, coupon_name) values ('" . $insert_id . "', '" . $_SESSION['languages_id'] . "', '" . $_POST['to_name'] . "')");

    // NAME DES ABSENDERS ERMITTELN
    $gv_query = xtc_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . (int) $_SESSION['customer_id'] . "'");
    $gv_customer = xtc_db_fetch_array($gv_query);

    // EMAIL VERSAND INS PROTOKOLL EINTRAGEN	
    $gv_query = xtc_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, sent_lastname, emailed_to, date_sent) values ('" . $insert_id . "' ,'" . (int) $_SESSION['customer_id'] . "', '" . addslashes($gv_customer['customers_firstname']) . "', '" . addslashes($gv_customer['customers_lastname']) . "', '" . xtc_db_input($_POST['email']) . "', now())");

    // VARIABLEN EMAIL-TEMPLATE
    $smarty->assign('language', $_SESSION['language']);
    $smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
    $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
    $smarty->assign('GIFT_LINK', xtc_href_link(FILENAME_GV_REDEEM, 'gv_no=' . $id1, 'SSL', false));
    $smarty->assign('WEBSITE', HTTP_SERVER . DIR_WS_CATALOG);
    $smarty->assign('AMOUNT', $xtPrice->xtcFormat($gv_amount, true));
    $smarty->assign('GIFT_CODE', $id1);
    $smarty->assign('MESSAGE', $_POST['message']);
    $smarty->assign('NAME', $_POST['to_name']);
    $smarty->assign('FROM_NAME', $_POST['send_name']);
    $smarty->caching = false;

    // EMAIL BETREFF
    $gv_email_subject = sprintf(EMAIL_GV_TEXT_SUBJECT, stripslashes($_POST['send_name']));

    require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
    $html_mail = $smarty->fetch('html:send_gift_to_friend');
    $html_mail .= $signatur_html;
    $txt_mail = $smarty->fetch('txt:send_gift_to_friend');
    $txt_mail .= $signatur_text;
    require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
    $mail_data = cseo_get_mail_data('send_gift_to_friend');

    $gv_mail_name = str_replace('{$shop}', STORE_NAME, $mail_data['EMAIL_ADDRESS_NAME']);


    xtc_php_mail($mail_data['EMAIL_ADDRESS'], $gv_mail_name, $_POST['email'], $_POST['to_name'], '', $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], '', '', $gv_email_subject, $html_mail, $txt_mail);

    // EMAIL VERSENDEN
    // xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $_POST['email'], $_POST['to_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $gv_email_subject, $html_mail, $txt_mail);
}


///////////////////////////////////////////////////////////////////////
// BREADCRUMB
///////////////////////////////////////////////////////////////////////
$breadcrumb->add(NAVBAR_GV_SEND);
require (DIR_WS_INCLUDES . 'header.php');


///////////////////////////////////////////////////////////////////////
// EMAIL VERSENDET
///////////////////////////////////////////////////////////////////////
if ($_GET['action'] == 'process') {
    $smarty->assign('action', 'process');
    $smarty->assign('LINK_DEFAULT', '<a href="' . xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '">' . xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>');
}


///////////////////////////////////////////////////////////////////////
// VORSCHAU ANZEIGEN
///////////////////////////////////////////////////////////////////////
if ($_GET['action'] == 'send' && !$error) {

    $smarty->assign('action', 'send');

    // NAME DES ABSENDERS ERMITTELN
    $gv_query = xtc_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . (int) $_SESSION['customer_id'] . "'");
    $gv_result = xtc_db_fetch_array($gv_query);
    $send_name = $gv_result['customers_firstname'] . ' ' . $gv_result['customers_lastname'];

    // TEMPLATE VARIABLEN
    $smarty->assign('LINK_BACK', xtc_image_submit('button_back.gif', IMAGE_BUTTON_BACK, 'name=back') . '</a>');
    $smarty->assign('LINK_SUBMIT', xtc_image_submit('button_send.gif', IMAGE_BUTTON_CONTINUE));
    $smarty->assign('FORM_ACTION', '<form action="' . xtc_href_link(FILENAME_GV_SEND, 'action=process', 'SSL') . '" method="post">');
    $smarty->assign('MAIN_MESSAGE', sprintf(MAIN_MESSAGE, $xtPrice->xtcFormat($gv_amount, true), stripslashes($_POST['to_name']), $_POST['email'], stripslashes($_POST['to_name']), $xtPrice->xtcFormat($gv_amount, true), $send_name));
    if ($_POST['message'] != '') {
        $smarty->assign('PERSONAL_MESSAGE', sprintf(PERSONAL_MESSAGE, $gv_result['customers_firstname']));
        $smarty->assign('POST_MESSAGE', stripslashes($_POST['message']));
    }

    // VERSTECKTE FELDER
    $hidden_fields = xtc_draw_hidden_field('send_name', $send_name);
    $hidden_fields .= xtc_draw_hidden_field('to_name', stripslashes($_POST['to_name']));
    $hidden_fields .= xtc_draw_hidden_field('email', $_POST['email']);
    $hidden_fields .= xtc_draw_hidden_field('amount', $gv_amount);
    $hidden_fields .= xtc_draw_hidden_field('message', stripslashes($_POST['message']));
    $smarty->assign('HIDDEN_FIELDS', $hidden_fields);
}


///////////////////////////////////////////////////////////////////////
// FORMULAR
///////////////////////////////////////////////////////////////////////
if ($_GET['action'] == '' || $error) {

    // ANZEIGE DER FEHLERMELDUNGEN
    if ($messageStack->size('gv_send') > 0) {
        $smarty->assign('error', $messageStack->output('gv_send'));
    }

    // GUTHABEN DES KUNDEN AUSLESEN
    $gv_query = xtc_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id = '" . (int) $_SESSION['customer_id'] . "'");
    $gv_result = xtc_db_fetch_array($gv_query);
    $smarty->assign('GV_AMOUNT', $xtPrice->xtcFormat($gv_result['amount'], true, 0, true));

    // FORMULAR FELDER
    $smarty->assign('action', '');
    $smarty->assign('FORM_ACTION', '<form action="' . xtc_href_link(FILENAME_GV_SEND, 'action=send', 'SSL') . '" method="post">');
    $smarty->assign('LINK_SEND', xtc_href_link(FILENAME_GV_SEND, 'action=send', 'NONSSL'));
    $smarty->assign('INPUT_TO_NAME', xtc_draw_input_field('to_name', stripslashes($_POST['to_name'])));
    $smarty->assign('INPUT_EMAIL', xtc_draw_input_field('email', $_POST['email']));
    $smarty->assign('ERROR_EMAIL', $error_email);
    $smarty->assign('INPUT_AMOUNT', xtc_draw_input_field('amount', $gv_amount, '', 'text', false));
    $smarty->assign('ERROR_AMOUNT', $error_amount);
    $smarty->assign('TEXTAREA_MESSAGE', xtc_draw_textarea_field('message', 'soft', 50, 15, stripslashes($_POST['message'])));
    $smarty->assign('LINK_SUBMIT', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));
}


///////////////////////////////////////////////////////////////////////
// ENDE DER SEITE UND AUSGABE
///////////////////////////////////////////////////////////////////////
$smarty->assign('FORM_END', '</form>');
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;

$main_content = $smarty->fetch(cseo_get_usermod('base/module/gv_send.html', USE_TEMPLATE_DEVMODE));
$smarty->assign('main_content', $main_content);

$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));

include ('includes/application_bottom.php');
