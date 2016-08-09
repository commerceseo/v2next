<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_get_mail_body.inc.php 462 2015-09-10 12:17:28Z akausch $
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

// definieren Sie folgende Funktion in Ihrer Applikation
function html_get_template($tpl_name, &$tpl_source) {
    // Datenbankabfrage um unser Template zu laden,
    // und '$tpl_source' zuzuweisen
	if (isset($_SESSION['languages_id']) && $_SESSION['languages_id'] != '') {
		$langid = (int) $_SESSION['languages_id'];
	} else {
		$langid = 2;
	}
    $tpl_data = xtc_db_fetch_array(xtc_db_query("SELECT email_content_html FROM emails WHERE email_name = '" . $tpl_name . "' AND languages_id = '" . $langid . "' "));
    if (sizeof($tpl_data) == 1) {
        $tpl_source = $tpl_data['email_content_html'];
        return true;
    } else {
        return false;
    }
}

function html_get_timestamp($tpl_name, &$tpl_timestamp) {
	if (isset($_SESSION['languages_id']) && $_SESSION['languages_id'] != '') {
		$langid = (int) $_SESSION['languages_id'];
	} else {
		$langid = 2;
	}
	$tpl_data = xtc_db_fetch_array(xtc_db_query("SELECT email_timestamp FROM emails WHERE email_name = '" . $tpl_name . "' AND languages_id = '" . $langid . "' "));

    if (sizeof($tpl_data['email_timestamp']) == 1) {
        $tpl_timestamp = $tpl_data['email_timestamp'];
        return true;
    } else {
        return false;
    }
}

function html_get_secure($tpl_name, &$smarty_obj) {
    // angenommen alle Templates sind sicher
    return true;
}

function html_get_trusted($tpl_name, &$smarty_obj) {
// wird fuer Templates nicht verwendet
}

// definieren Sie folgende Funktion in Ihrer Applikation
function txt_get_template($tpl_name, &$tpl_source) {
    // Datenbankabfrage um unser Template zu laden,
    // und '$tpl_source' zuzuweisen

    $tpl_data = xtc_db_fetch_array(xtc_db_query("SELECT email_content_text FROM emails WHERE email_name = '" . $tpl_name . "' AND languages_id = '" . (int) $_SESSION['languages_id'] . "' "));

    if (sizeof($tpl_data) == 1) {
        $tpl_source = $tpl_data['email_content_text'];
        return true;
    } else {
        return false;
    }
}

function txt_get_timestamp($tpl_name, &$tpl_timestamp) {
    global $db;
    $tpl_data = xtc_db_fetch_array(xtc_db_query("SELECT email_timestamp FROM emails WHERE email_name = '" . $tpl_name . "' AND languages_id = '" . (int) $_SESSION['languages_id'] . "' "));

    if (sizeof($tpl_data['email_timestamp']) == 1) {
        $tpl_timestamp = $tpl_data['email_timestamp'];
        return true;
    } else {
        return false;
    }
}

function txt_get_secure($tpl_name, &$smarty_obj) {
    return true;
}

function txt_get_trusted($tpl_name, &$smarty_obj) {
    
}

$smarty->registerResource("html", array("html_get_template", "html_get_timestamp", "html_get_secure", "html_get_trusted"));
$smarty->registerResource("txt", array("txt_get_template", "txt_get_timestamp", "txt_get_secure", "txt_get_trusted"));
if (isset($_SESSION['language']) && $_SESSION['language'] != '') {
$smarty->assign('language', $_SESSION['language']);
} else {
$smarty->assign('language', 'german');
}
$smarty->caching = false;
$smarty->force_compile = true;
$smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

//Signatur
$signatursmarty = new Smarty;
$signatursmarty->caching = false;
$signatursmarty->force_compile = true;

$signatursmarty->registerResource("html", array("html_get_template", "html_get_timestamp", "html_get_secure", "html_get_trusted"));

$signatursmarty->registerResource("txt", array("txt_get_template", "txt_get_timestamp", "txt_get_secure", "txt_get_trusted"));


$adresse = xtc_db_fetch_array(xtc_db_query("SELECT entry_firstname, entry_lastname, entry_street_address, entry_postcode, entry_city FROM address_book WHERE customers_id = 1"));
if (isset($_SESSION['language']) && $_SESSION['language'] != '') {
$signatursmarty->assign('language', $_SESSION['language']);
} else {
$signatursmarty->assign('language', 'german');
}

$signatursmarty->assign('SHOP_NAME', STORE_NAME);
$signatursmarty->assign('SHOP_BESITZER', STORE_OWNER);
$signatursmarty->assign('SHOP_USTID', STORE_OWNER_VAT_ID);
$signatursmarty->assign('SHOP_ADRESSE_VNAME', $adresse['entry_firstname']);
$signatursmarty->assign('SHOP_ADRESSE_NNAME', $adresse['entry_lastname']);
$signatursmarty->assign('SHOP_ADRESSE_STRASSE', $adresse['entry_street_address']);
$signatursmarty->assign('SHOP_ADRESSE_PLZ', $adresse['entry_postcode']);
$signatursmarty->assign('SHOP_ADRESSE_ORT', $adresse['entry_city']);
$signatursmarty->assign('SHOP_EMAIL', STORE_OWNER_EMAIL_ADDRESS);
$signatursmarty->assign('SHOP_URL', HTTP_SERVER);

$signatur_html = $signatursmarty->fetch('html:signatur');
$signatur_text = $signatursmarty->fetch('txt:signatur');
