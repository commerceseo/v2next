<?php

/* -----------------------------------------------------------------
 * 	$Id: address_book_process.php 879 2014-03-26 17:22:54Z akausch $
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
$account = new account;
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

require_once (DIR_FS_INC . 'xtc_count_customer_address_book_entries.inc.php');
require_once (DIR_FS_INC . 'xtc_address_label.inc.php');
require_once (DIR_FS_INC . 'xtc_get_country_name.inc.php');
require_once (DIR_FS_INC . 'xtc_get_zone_name.inc.php');
require_once (DIR_FS_INC . 'xtc_get_country_list.inc.php');

if (!isset($_SESSION['customer_id'])) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

if (isset($_GET['action']) && ($_GET['action'] == 'deleteconfirm') && isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $account->address_book_process_delete((int) $_GET['delete']);
}

$process = false;
if (isset($_POST['action']) && (($_POST['action'] == 'address_book_process') || ($_POST['action'] == 'update'))) {
    $address_book_process_edit_array = $account->address_book_process_edit();
	if ($address_book_process_edit_array != '') {
		$messageStack->add_session('addressbook', $address_book_process_edit_array);
	}
}

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $entry_query = xtc_db_query("SELECT * FROM " . TABLE_ADDRESS_BOOK . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND address_book_id = '" . (int) $_GET['edit'] . "';");
    if (xtc_db_num_rows($entry_query) == false) {
        $messageStack->add_session('addressbook', ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY);
        xtc_redirect(xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    }
    $entry = xtc_db_fetch_array($entry_query);
} elseif (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if ($_GET['delete'] == $_SESSION['customer_default_address_id']) {
        $messageStack->add_session('addressbook', WARNING_PRIMARY_ADDRESS_DELETION, 'warning');
        xtc_redirect(xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    } else {
        $check_query = xtc_db_query("SELECT COUNT(*) AS total FROM " . TABLE_ADDRESS_BOOK . " WHERE address_book_id = '" . (int) $_GET['delete'] . "' AND customers_id = '" . (int) $_SESSION['customer_id'] . "';");
        $check = xtc_db_fetch_array($check_query);
        if ($check['total'] < 1) {
            $messageStack->add_session('addressbook', ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY);
            xtc_redirect(xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
        }
    }
} else {
    $entry = array();
}

if (!isset($_GET['delete']) && !isset($_GET['edit'])) {
    if (xtc_count_customer_address_book_entries() >= MAX_ADDRESS_BOOK_ENTRIES) {
        $messageStack->add_session('addressbook', ERROR_ADDRESS_BOOK_FULL);
        xtc_redirect(xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    }
}

$breadcrumb->add(NAVBAR_TITLE_1_ADDRESS_BOOK_PROCESS, xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_ADDRESS_BOOK_PROCESS, xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $breadcrumb->add(NAVBAR_TITLE_MODIFY_ENTRY_ADDRESS_BOOK_PROCESS, xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'edit=' . $_GET['edit'], 'SSL'));
} elseif (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $breadcrumb->add(NAVBAR_TITLE_DELETE_ENTRY_ADDRESS_BOOK_PROCESS, xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'delete=' . $_GET['delete'], 'SSL'));
} else {
    $breadcrumb->add(NAVBAR_TITLE_ADD_ENTRY_ADDRESS_BOOK_PROCESS, xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, '', 'SSL'));
}

require_once (DIR_WS_INCLUDES . 'header.php');

if (isset($_GET['delete']) == false) {
    $action = xtc_draw_form('addressbook', xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, (isset($_GET['edit']) ? 'edit=' . $_GET['edit'] : ''), 'SSL'), 'post', 'onsubmit="return check_form(addressbook);"');
}

$smarty->assign('FORM_ACTION', $action);
if ($messageStack->size('addressbook') > 0) {
    $smarty->assign('error', $messageStack->output('addressbook'));
}

if (isset($_GET['delete'])) {
    $smarty->assign('delete', '1');
    $smarty->assign('ADDRESS', xtc_address_label($_SESSION['customer_id'], $_GET['delete'], true, ' ', '<br />'));
    $smarty->assign('BUTTON_BACK', '<a href="' . xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
    $smarty->assign('BUTTON_DELETE', '<a href="' . xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'delete=' . $_GET['delete'] . '&action=deleteconfirm', 'SSL') . '">' . xtc_image_button('button_delete.gif', IMAGE_BUTTON_DELETE) . '</a>');
} else {
    $abp_smarty = new Smarty;
    $abp_smarty_var = $account->address_book_process($entry);
    if (is_array($abp_smarty_var)) {
        foreach ($abp_smarty_var AS $t_key => $t_value) {
            $abp_smarty->assign($t_key, $t_value);
        }
    }

    if ((isset($_GET['edit']) && ($_SESSION['customer_default_address_id'] != $_GET['edit'])) || (isset($_GET['edit']) == false)) {
        $abp_smarty->assign('new', '1');
        $abp_smarty->assign('CHECKBOX_PRIMARY', xtc_draw_checkbox_field('primary', 'on', false, 'id="primary"'));
    }

    $abp_smarty->assign('language', $_SESSION['language']);
    $abp_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $abp_smarty->caching = false;
	if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/address_book_details.html')) {
		$main_content = $abp_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/address_book_details.html', USE_TEMPLATE_DEVMODE));
	}else{
		$main_content = $abp_smarty->fetch(cseo_get_usermod('base/module/address_book_details.html', USE_TEMPLATE_DEVMODE));
	}
    $smarty->assign('MODULE_address_book_details', $main_content);

    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $smarty->assign('BUTTON_BACK', '<a href="' . xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
        $smarty->assign('BUTTON_UPDATE', xtc_draw_hidden_field('action', 'update') . xtc_draw_hidden_field('edit', $_GET['edit']) . xtc_image_submit('button_update.gif', IMAGE_BUTTON_UPDATE));
    } else {
        if (sizeof($_SESSION['navigation']->snapshot) > 0) {
            $back_link = xtc_href_link($_SESSION['navigation']->snapshot['page'], xtc_array_to_string($_SESSION['navigation']->snapshot['get'], array(xtc_session_name())), $_SESSION['navigation']->snapshot['mode']);
        } else {
            $back_link = xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL');
        }
        $smarty->assign('BUTTON_BACK', '<a href="' . $back_link . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');
        $smarty->assign('BUTTON_UPDATE', xtc_draw_hidden_field('action', 'address_book_process') . xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));
    }
    $smarty->assign('FORM_END', '</form>');
}

$cseo_account = cseohookfactory::create_object('AddressBookProcessExtender');
$cseo_account->proceed();
$cseo_extender_result_array = $cseo_account->get_response();
if (is_array($cseo_extender_result_array)) {
    foreach ($cseo_extender_result_array AS $t_key => $t_value) {
        $smarty->assign($t_key, $t_value);
    }
}

$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = false;

if (file_exists('templates/'.CURRENT_TEMPLATE.'/module/address_book_process.html')) {
	$main_content = $smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE.'/module/address_book_process.html', USE_TEMPLATE_DEVMODE));
}else{
	$main_content = $smarty->fetch(cseo_get_usermod('base/module/address_book_process.html', USE_TEMPLATE_DEVMODE));
}
$smarty->assign('main_content', $main_content);
$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));
include ('includes/application_bottom.php');
