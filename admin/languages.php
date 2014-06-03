<?php

/* -----------------------------------------------------------------
 * 	$Id: languages.php 1072 2014-05-27 08:38:11Z akausch $
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

require('includes/application_top.php');
$smarty = new Smarty;

switch ($_GET['action']) {
    case 'setlflag':
        $language_id = xtc_db_prepare_input($_GET['lID']);
        $status = xtc_db_prepare_input($_GET['flag']);
        xtc_db_query("UPDATE " . TABLE_LANGUAGES . " SET status = '" . xtc_db_input($status) . "' WHERE languages_id = '" . xtc_db_input($language_id) . "';");
        xtc_redirect(xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $language_id));
        break;
    case 'setaflag':
        $language_id = xtc_db_prepare_input($_GET['lID']);
        $status = xtc_db_prepare_input($_GET['flag']);
        xtc_db_query("UPDATE " . TABLE_LANGUAGES . " SET status_admin = '" . xtc_db_input($status) . "' WHERE languages_id = '" . xtc_db_input($language_id) . "';");
        xtc_redirect(xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $language_id));
        break;
    case 'insert':
        $name = xtc_db_prepare_input($_POST['name']);
        $code = xtc_db_prepare_input($_POST['code']);
        $image = xtc_db_prepare_input($_POST['image']);
        $directory = xtc_db_prepare_input($_POST['directory']);
        if (empty($_POST['sort_order'])) {
            $check_sort_order = xtc_db_fetch_array(xtc_db_query("SELECT sort_order FROM " . TABLE_LANGUAGES . " ORDER BY sort_order DESC LIMIT 1"));
            $sort_order = $check_sort_order['sort_order'] + 1;
        } else {
            $sort_order = xtc_db_prepare_input($_POST['sort_order']);
        }
        $charset = xtc_db_prepare_input($_POST['charset']);
        $status = xtc_db_prepare_input($_POST['status']);
        $status_admin = xtc_db_prepare_input($_POST['status_admin']);

        xtc_db_query("INSERT INTO " . TABLE_LANGUAGES . " (name, code, image, directory, sort_order, language_charset, status, status_admin) values ('" . xtc_db_input($name) . "', '" . xtc_db_input($code) . "', '" . xtc_db_input($image) . "', '" . xtc_db_input($directory) . "', '" . xtc_db_input($sort_order) . "', '" . xtc_db_input($charset) . "', '" . xtc_db_input($status) . "', '" . xtc_db_input($status_admin) . "')");
        $insert_id = xtc_db_insert_id();
        if (file_exists(DIR_WS_INCLUDES . 'addons/languages_insert_addon.php')) {
            include (DIR_WS_INCLUDES . 'addons/languages_insert_addon.php');
        }

        // create additional categories_description records
        $categories_query = xtc_db_query("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c left join " . TABLE_CATEGORIES_DESCRIPTION . " cd on c.categories_id = cd.categories_id where cd.language_id = '" . $_SESSION['languages_id'] . "'");
        while ($categories = xtc_db_fetch_array($categories_query)) {
            xtc_db_query("insert into " . TABLE_CATEGORIES_DESCRIPTION . " (categories_id, language_id, categories_name) values ('" . $categories['categories_id'] . "', '" . $insert_id . "', '" . xtc_db_input($categories['categories_name']) . "')");
        }

        // create additional products_description records
        $products_query = xtc_db_query("SELECT p.products_id, pd.products_name, pd.products_description, pd.products_url from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id where pd.language_id = '" . $_SESSION['languages_id'] . "'");
        while ($products = xtc_db_fetch_array($products_query)) {
            xtc_db_query("insert into " . TABLE_PRODUCTS_DESCRIPTION . " (products_id, language_id, products_name, products_description, products_url) values ('" . $products['products_id'] . "', '" . $insert_id . "', '" . xtc_db_input($products['products_name']) . "', '" . xtc_db_input($products['products_description']) . "', '" . xtc_db_input($products['products_url']) . "')");
        }

        xtc_db_query("ALTER TABLE products_images ADD alt_langID_" . $insert_id . " VARCHAR(64) NOT NULL");

        // create additional products_options records
        $products_options_query = xtc_db_query("select products_options_id, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . $_SESSION['languages_id'] . "'");
        while ($products_options = xtc_db_fetch_array($products_options_query)) {
            xtc_db_query("insert into " . TABLE_PRODUCTS_OPTIONS . " (products_options_id, language_id, products_options_name) values ('" . $products_options['products_options_id'] . "', '" . $insert_id . "', '" . xtc_db_input($products_options['products_options_name']) . "')");
        }

        // create additional products_options_values records
        $products_options_values_query = xtc_db_query("select products_options_values_id, products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where language_id = '" . $_SESSION['languages_id'] . "'");
        while ($products_options_values = xtc_db_fetch_array($products_options_values_query)) {
            xtc_db_query("insert into " . TABLE_PRODUCTS_OPTIONS_VALUES . " (products_options_values_id, language_id, products_options_values_name) values ('" . $products_options_values['products_options_values_id'] . "', '" . $insert_id . "', '" . xtc_db_input($products_options_values['products_options_values_name']) . "')");
        }

        // create additional orders_status records
        $orders_status_query = xtc_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "'");
        while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
            xtc_db_query("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('" . $orders_status['orders_status_id'] . "', '" . $insert_id . "', '" . xtc_db_input($orders_status['orders_status_name']) . "')");
        }

        // create additional shipping_status records
        $shipping_status_query = xtc_db_query("select shipping_status_id, shipping_status_name from " . TABLE_SHIPPING_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "'");
        while ($shipping_status = xtc_db_fetch_array($shipping_status_query)) {
            xtc_db_query("insert into " . TABLE_SHIPPING_STATUS . " (shipping_status_id, language_id, shipping_status_name) values ('" . $shipping_status['shipping_status_id'] . "', '" . $insert_id . "', '" . xtc_db_input($shipping_status['shipping_status_name']) . "')");
        }

        // create additional orders_status records
        $xsell_grp_query = xtc_db_query("select products_xsell_grp_name_id,xsell_sort_order, groupname from " . TABLE_PRODUCTS_XSELL_GROUPS . " where language_id = '" . $_SESSION['languages_id'] . "'");
        while ($xsell_grp = xtc_db_fetch_array($xsell_grp_query)) {
            xtc_db_query("insert into " . TABLE_PRODUCTS_XSELL_GROUPS . " (products_xsell_grp_name_id,xsell_sort_order, language_id, groupname) values ('" . $xsell_grp['products_xsell_grp_name_id'] . "','" . $xsell_grp['xsell_sort_order'] . "', '" . $insert_id . "', '" . xtc_db_input($xsell_grp['groupname']) . "')");
        }
		// create additional manufacturers_info records
		$manufacturers_query = xtc_db_query("select m.manufacturers_id, mi.manufacturers_url from " . TABLE_MANUFACTURERS . " m left join " . TABLE_MANUFACTURERS_INFO . " mi on m.manufacturers_id = mi.manufacturers_id where mi.languages_id = '" . $_SESSION['languages_id'] . "'");
		while ($manufacturers = xtc_db_fetch_array($manufacturers_query)) {
			xtc_db_query("insert into " . TABLE_MANUFACTURERS_INFO . " (manufacturers_id, languages_id, manufacturers_url) values ('" . $manufacturers['manufacturers_id'] . "', '" . $insert_id . "', '" . xtc_db_input($manufacturers['manufacturers_url']) . "')");
		}

        // create additional customers status
        $customers_status_query = xtc_db_query("SELECT DISTINCT customers_status_id FROM " . TABLE_CUSTOMERS_STATUS);
        while ($data = xtc_db_fetch_array($customers_status_query)) {

            $group_data = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id='" . $data['customers_status_id'] . "';"));

            $c_data = array(
                'customers_status_id' => $data['customers_status_id'],
                'language_id' => $insert_id,
                'customers_status_name' => $group_data['customers_status_name'],
                'customers_status_public' => $group_data['customers_status_public'],
                'customers_status_image' => $group_data['customers_status_image'],
                'customers_status_discount' => $group_data['customers_status_discount'],
                'customers_status_ot_discount_flag' => $group_data['customers_status_ot_discount_flag'],
                'customers_status_ot_discount' => $group_data['customers_status_ot_discount'],
                'customers_status_graduated_prices' => $group_data['customers_status_graduated_prices'],
                'customers_status_show_price' => $group_data['customers_status_show_price'],
                'customers_status_show_price_tax' => $group_data['customers_status_show_price_tax'],
                'customers_status_add_tax_ot' => $group_data['customers_status_add_tax_ot'],
                'customers_status_payment_unallowed' => $group_data['customers_status_payment_unallowed'],
                'customers_status_shipping_unallowed' => $group_data['customers_status_shipping_unallowed'],
                'customers_status_discount_attributes' => $group_data['customers_status_discount_attributes']);

            xtc_db_perform(TABLE_CUSTOMERS_STATUS, $c_data);
        }

        if (file_exists(DIR_FS_CATALOG . 'lang/' . $_POST['directory'] . '/' . $_POST['directory'] . '.sql')) {
            require_once(DIR_FS_INC . 'cseo_sql_query.inc.php');
            cseo_sql_query(DIR_FS_CATALOG . 'lang/' . $_POST['directory'] . '/' . $_POST['directory'] . '.sql', $insert_id);
        }
        if ($_POST['default'] == 'on') {
            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . xtc_db_input($code) . "' WHERE configuration_key = 'DEFAULT_LANGUAGE'");
        }

        xtc_redirect(xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $insert_id . '&new=1'));
        break;

    case 'save':
        $lID = xtc_db_prepare_input($_GET['lID']);
        $name = xtc_db_prepare_input($_POST['name']);
        $code = xtc_db_prepare_input($_POST['code']);
        $image = xtc_db_prepare_input($_POST['image']);
        $directory = xtc_db_prepare_input($_POST['directory']);
        $sort_order = xtc_db_prepare_input($_POST['sort_order']);
        $charset = xtc_db_prepare_input($_POST['charset']);
        $status = xtc_db_prepare_input($_POST['status']);
        $status_admin = xtc_db_prepare_input($_POST['status_admin']);

        xtc_db_query("UPDATE " . TABLE_LANGUAGES . " SET name = '" . xtc_db_input($name) . "', code = '" . xtc_db_input($code) . "', image = '" . xtc_db_input($image) . "', directory = '" . xtc_db_input($directory) . "', sort_order = '" . xtc_db_input($sort_order) . "', language_charset = '" . xtc_db_input($charset) . "', status = '" . xtc_db_input($status) . "', status_admin = '" . xtc_db_input($status_admin) . "' WHERE languages_id = '" . xtc_db_input($lID) . "'");

        if ($_POST['default'] == 'on') {
            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . xtc_db_input($code) . "' WHERE configuration_key = 'DEFAULT_LANGUAGE'");
        }

        xtc_redirect(xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $_GET['lID']));
        break;

    case 'deleteconfirm':
        $lID = xtc_db_prepare_input($_GET['lID']);

        $lng_query = xtc_db_query("SELECT languages_id FROM " . TABLE_LANGUAGES . " WHERE code = '" . DEFAULT_CURRENCY . "'");
        $lng = xtc_db_fetch_array($lng_query);
        if ($lng['languages_id'] == $lID) {
            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '' WHERE configuration_key = 'DEFAULT_CURRENCY'");
        }

        xtc_db_query("DELETE FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_OPTIONS . " WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_MANUFACTURERS_INFO . " WHERE languages_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_ORDERS_STATUS . " WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_LANGUAGES . " WHERE languages_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_CONTENT_MANAGER . " WHERE languages_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_CONTENT . " WHERE languages_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_STATUS . " WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_PERSONAL_LINKS_URL . " WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_BLOG_CATEGORIES . " WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_BLOG_ITEMS . " WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_PARAMETERS_DESCRIPTION . " WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM emails WHERE languages_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM orders_pdf_profile WHERE languages_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM cseo_lang_button WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM cseo_antispam WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_NEWS_TICKER . " WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM boxes_names WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM shipping_status WHERE language_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM addon_languages WHERE languages_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("DELETE FROM admin_navigation WHERE languages_id = '" . xtc_db_input($lID) . "'");
        xtc_db_query("ALTER TABLE products_images DROP COLUMN alt_langID_" . xtc_db_input($lID));
        if (file_exists(DIR_WS_INCLUDES . 'addons/languages_delete_addon.php')) {
            include (DIR_WS_INCLUDES . 'addons/languages_delete_addon.php');
        }

        xtc_redirect(xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page']));
        break;

    case 'delete':
        $lID = xtc_db_prepare_input($_GET['lID']);

        $lng_query = xtc_db_query("SELECT code FROM " . TABLE_LANGUAGES . " WHERE languages_id = '" . xtc_db_input($lID) . "'");
        $lng = xtc_db_fetch_array($lng_query);

        $remove_language = true;
        if ($lng['code'] == DEFAULT_LANGUAGE) {
            $remove_language = false;
            $messageStack->add(ERROR_REMOVE_DEFAULT_LANGUAGE, 'error');
        }
        break;
}

$lang_array = array();
$check_language = xtc_db_query("SELECT code FROM " . TABLE_LANGUAGES . "");
while ($check = xtc_db_fetch_array($check_language)) {
    array_push($lang_array, $check['code']);
}

if ($_GET['new'] == '1') {
    $messageStack->add(INFO_INDEX_URL_START, 'info');
}

unset($_GET['new']);

require(DIR_WS_INCLUDES . 'header.php');

$smarty->assign('HEADING_TITLE', HEADING_TITLE);

if (!isset($_GET['action'])) {

    if (file_exists('../lang/english/english.sql')) {
        if (!in_array('en', $lang_array, true)) {
            $lang_button .= xtc_draw_form('languages', FILENAME_LANGUAGES, 'action=insert');
            $lang_button .= xtc_draw_hidden_field('name', 'English') .
                    xtc_draw_hidden_field('status', '0') .
                    xtc_draw_hidden_field('code', 'en') .
                    xtc_draw_hidden_field('charset', 'utf-8') .
                    xtc_draw_hidden_field('image', 'icon.gif') .
                    xtc_draw_hidden_field('directory', 'english');
            $lang_button .= '<input class="button" type="submit" value="Englisch" />' . "\n" . '</form>';
        }
    }

    if (file_exists('../lang/french/french.sql')) {
        if (!in_array('fr', $lang_array, true)) {
            $lang_button .= xtc_draw_form('languages', FILENAME_LANGUAGES, 'action=insert');
            $lang_button .= xtc_draw_hidden_field('name', 'French') .
                    xtc_draw_hidden_field('status', '0') .
                    xtc_draw_hidden_field('code', 'fr') .
                    xtc_draw_hidden_field('charset', 'utf-8') .
                    xtc_draw_hidden_field('image', 'icon.gif') .
                    xtc_draw_hidden_field('directory', 'french');
            $lang_button .= '<input class="button" type="submit" value="French" />' . "\n" . '</form>';
        }
    }

    if (file_exists('../lang/swedish/swedish.sql')) {
        if (!in_array('sv', $lang_array, true)) {
            $lang_button .= xtc_draw_form('languages', FILENAME_LANGUAGES, 'action=insert');
            $lang_button .= xtc_draw_hidden_field('name', 'Swedish') .
                    xtc_draw_hidden_field('status', '0') .
                    xtc_draw_hidden_field('code', 'sv') .
                    xtc_draw_hidden_field('charset', 'utf-8') .
                    xtc_draw_hidden_field('image', 'icon.gif') .
                    xtc_draw_hidden_field('directory', 'swedish');
            $lang_button .= '<input class="button" type="submit" value="Swedish" />' . "\n" . '</form>';
        }
    }

    if (file_exists('../lang/spanish/spanish.sql')) {
        if (!in_array('es', $lang_array, true)) {
            $lang_button .= xtc_draw_form('languages', FILENAME_LANGUAGES, 'action=insert');
            $lang_button .= xtc_draw_hidden_field('name', 'Spanish') .
                    xtc_draw_hidden_field('status', '0') .
                    xtc_draw_hidden_field('code', 'es') .
                    xtc_draw_hidden_field('charset', 'utf-8') .
                    xtc_draw_hidden_field('image', 'icon.gif') .
                    xtc_draw_hidden_field('directory', 'spanish');
            $lang_button .= '<input class="button" type="submit" value="Spanish" />' . "\n" . '</form>';
        }
    }

    if (file_exists('../lang/dutch/dutch.sql')) {
        if (!in_array('nl', $lang_array, true)) {
            $lang_button .= xtc_draw_form('languages', FILENAME_LANGUAGES, 'action=insert');
            $lang_button .= xtc_draw_hidden_field('name', 'Nederlands') .
                    xtc_draw_hidden_field('status', '0') .
                    xtc_draw_hidden_field('code', 'nl') .
                    xtc_draw_hidden_field('charset', 'utf-8') .
                    xtc_draw_hidden_field('image', 'icon.gif') .
                    xtc_draw_hidden_field('directory', 'dutch');
            $lang_button .= '<input class="button" type="submit" value="Nederlands" />' . "\n" . '</form>';
        }
    }

    if (file_exists('../lang/italian/italian.sql')) {
        if (!in_array('it', $lang_array, true)) {
            $lang_button .= xtc_draw_form('languages', FILENAME_LANGUAGES, 'action=insert');
            $lang_button .= xtc_draw_hidden_field('name', 'Italian') .
                    xtc_draw_hidden_field('status', '0') .
                    xtc_draw_hidden_field('code', 'it') .
                    xtc_draw_hidden_field('charset', 'utf-8') .
                    xtc_draw_hidden_field('image', 'icon.gif') .
                    xtc_draw_hidden_field('directory', 'italian');
            $lang_button .= '<input class="button" type="submit" value="Italian" />' . "\n" . '</form>';
        }
    }

    if (file_exists('../lang/tr/tr.sql')) {
        if (!in_array('tr', $lang_array, true)) {
            $lang_button .= xtc_draw_form('languages', FILENAME_LANGUAGES, 'action=insert');
            $lang_button .= xtc_draw_hidden_field('name', 'Turky') .
                    xtc_draw_hidden_field('status', '0') .
                    xtc_draw_hidden_field('code', 'tr') .
                    xtc_draw_hidden_field('charset', 'utf-8') .
                    xtc_draw_hidden_field('image', 'icon.gif') .
                    xtc_draw_hidden_field('directory', 'tr');
            $lang_button .= '<input class="button" type="submit" value="Turky" />' . "\n" . '</form>';
        }
    }

    if (file_exists('../lang/russian/russian.sql')) {
        if (!in_array('ru', $lang_array, true)) {
            $lang_button .= xtc_draw_form('languages', FILENAME_LANGUAGES, 'action=insert');
            $lang_button .= xtc_draw_hidden_field('name', 'Russian') .
                    xtc_draw_hidden_field('status', '0') .
                    xtc_draw_hidden_field('code', 'ru') .
                    xtc_draw_hidden_field('charset', 'utf-8') .
                    xtc_draw_hidden_field('image', 'icon.gif') .
                    xtc_draw_hidden_field('directory', 'russian');
            $lang_button .= '<input class="button" type="submit" value="Russian" />' . "\n" . '</form>';
        }
    }
    $smarty->assign('lang_button', $lang_button);
}

$smarty->assign('TABLE_HEADING_LANGUAGE_NAME', TABLE_HEADING_LANGUAGE_NAME);
$smarty->assign('TABLE_HEADING_LANGUAGE_CODE', TABLE_HEADING_LANGUAGE_CODE);
$smarty->assign('TABLE_HEADING_LANGUAGE_STATUS', TABLE_HEADING_LANGUAGE_STATUS);
$smarty->assign('TABLE_HEADING_LANGUAGE_STATUS_ADMIN', TABLE_HEADING_LANGUAGE_STATUS_ADMIN);
$smarty->assign('TABLE_HEADING_ACTION', TABLE_HEADING_ACTION);

$languages_query_raw = "select languages_id, name, code, image, directory, sort_order, language_charset, status, status_admin from " . TABLE_LANGUAGES . " ORDER BY sort_order";
$languages_split = new splitPageResults($_GET['page'], '20', $languages_query_raw, $languages_query_numrows);
$languages_query = xtc_db_query($languages_query_raw);

while ($languages = xtc_db_fetch_array($languages_query)) {
    $rows++;
    if (((!$_GET['lID']) || (@$_GET['lID'] == $languages['languages_id'])) && (!$lInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
        $lInfo = new objectInfo($languages);
    }

    if ((is_object($lInfo)) && ($languages['languages_id'] == $lInfo->languages_id)) {
        $tr_row = '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=edit') . '\'">' . "\n";
    } else {
        $tr_row = '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $languages['languages_id']) . '\'">' . "\n";
    }
    if (DEFAULT_LANGUAGE == $languages['code']) {
        $td_row_lang = '<b>' . $languages['name'] . ' (' . TEXT_DEFAULT . ')</b>';
    } else {
        $td_row_lang = $languages['name'];
    }


    if ($languages['status'] == 1) {
        $td_row_status_lang = '<a href="' . xtc_href_link(FILENAME_LANGUAGES, xtc_get_all_get_params(array('page', 'action', 'lID')) . 'action=setlflag&flag=0&lID=' . $languages['languages_id'] . '&page=' . $_GET['page']) . '"><button class="btn btn-success btn-xs">Online</button></a>';
    } else {
        $td_row_status_lang = '<a href="' . xtc_href_link(FILENAME_LANGUAGES, xtc_get_all_get_params(array('page', 'action', 'lID')) . 'action=setlflag&flag=1&lID=' . $languages['languages_id'] . '&page=' . $_GET['page']) . '"><button class="btn btn-danger btn-xs">Offline</button></a>';
    }

    if ($languages['status_admin'] == '1') {
        $td_row_status_admin_lang = '<a href="' . xtc_href_link(FILENAME_LANGUAGES, xtc_get_all_get_params(array('page', 'action', 'lID')) . 'action=setaflag&flag=0&lID=' . $languages['languages_id'] . '&page=' . $_GET['page']) . '"><button class="btn btn-success btn-xs">Online</button></a>';
    } else {
        $td_row_status_admin_lang = '<a href="' . xtc_href_link(FILENAME_LANGUAGES, xtc_get_all_get_params(array('page', 'action', 'lID')) . 'action=setaflag&flag=1&lID=' . $languages['languages_id'] . '&page=' . $_GET['page']) . '"><button class="btn btn-danger btn-xs">Offline</button></a>';
    }

    if ((is_object($lInfo)) && ($languages['languages_id'] == $lInfo->languages_id)) {
        $td_row_action = xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif');
    } else {
        $td_row_action = '<a href="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $languages['languages_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
    }
    $lang_icon = xtc_image(DIR_WS_LANGUAGES . $languages['directory'] . '/' . $languages['image'], $languages['name']);

    $languagearray[] = array(
        'LANG_TR' => $tr_row,
        'TD_ROW_LANG' => $td_row_lang,
        'TD_ROW_CODE' => $languages['code'],
        'TD_ROW_STATUS_LANG' => $td_row_status_lang,
        'TD_ROW_STATUS_ADMIN_LANG' => $td_row_status_admin_lang,
        'TD_ROW_ACTION' => $td_row_action,
        'LANG_ICON' => $lang_icon);
}
$smarty->assign('languagearray', $languagearray);




$direction_options = array(array('id' => '', 'text' => TEXT_INFO_LANGUAGE_DIRECTION_DEFAULT),
    array('id' => 'ltr', 'text' => TEXT_INFO_LANGUAGE_DIRECTION_LEFT_TO_RIGHT),
    array('id' => 'rtl', 'text' => TEXT_INFO_LANGUAGE_DIRECTION_RIGHT_TO_LEFT));

$heading = array();
$contents = array();
switch ($_GET['action']) {
    case 'new':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_LANGUAGE . '</b>');
        $contents = array('form' => xtc_draw_form('languages', FILENAME_LANGUAGES, 'action=insert'));
        $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
        $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_NAME . '<br />' . xtc_draw_input_field('name'));
        $contents[] = array('text' => '<br />' . TABLE_HEADING_LANGUAGE_NAME . ':<br />' . xtc_draw_radio_field('status', 1, true) . ' ' . TEXT_LANGUAGE_ACTIVE . ' <br />' . xtc_draw_radio_field('status', 0, false) . ' ' . TEXT_LANGUAGE_INACTIVE);
        $contents[] = array('text' => '<br />' . TABLE_HEADING_LANGUAGE_STATUS_ADMIN . ':<br />' . xtc_draw_radio_field('status_admin', 1, true) . ' ' . TEXT_LANGUAGE_ACTIVE . ' <br />' . xtc_draw_radio_field('status_admin', 0, false) . ' ' . TEXT_LANGUAGE_INACTIVE);
        $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_CODE . '<br />' . xtc_draw_input_field('code'));
        $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_CHARSET . '<br />' . xtc_draw_input_field('charset'));
        $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_IMAGE . '<br />' . xtc_draw_input_field('image', 'icon.gif'));
        $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_DIRECTORY . '<br />' . xtc_draw_input_field('directory'));
        $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_SORT_ORDER . '<br />' . xtc_draw_input_field('sort_order'));
        $contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
        $contents[] = array('align' => 'center', 'text' => '<br /><input class="button" type="submit" value="' . BUTTON_INSERT . '" /> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $_GET['lID']) . '">' . BUTTON_CANCEL . '</a>');
        break;

    case 'edit':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_LANGUAGE . '</b>');
        $contents = array('form' => xtc_draw_form('languages', FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=save'));
        $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
        $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_NAME . '<br />' . xtc_draw_input_field('name', $lInfo->name));
        $contents[] = array('text' => '<br />' . TABLE_HEADING_LANGUAGE_NAME . ':<br />' . xtc_draw_radio_field('status', '1', ($lInfo->status == 1) ? true : false) . ' ' . TEXT_LANGUAGE_ACTIVE . ' <br />' . xtc_draw_radio_field('status', '0', ($lInfo->status == 0) ? true : false) . ' ' . TEXT_LANGUAGE_INACTIVE);
        $contents[] = array('text' => '<br />' . TABLE_HEADING_LANGUAGE_STATUS_ADMIN . ':<br />' . xtc_draw_radio_field('status_admin', '1', ($lInfo->status_admin == 1) ? true : false) . ' ' . TEXT_LANGUAGE_ACTIVE . ' <br />' . xtc_draw_radio_field('status_admin', '0', ($lInfo->status_admin == 0) ? true : false) . ' ' . TEXT_LANGUAGE_INACTIVE);
        $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_CODE . '<br />' . xtc_draw_input_field('code', $lInfo->code));
        $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_CHARSET . '<br />' . xtc_draw_input_field('charset', $lInfo->language_charset));
        $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_IMAGE . '<br />' . xtc_draw_input_field('image', $lInfo->image));
        $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_DIRECTORY . '<br />' . xtc_draw_input_field('directory', $lInfo->directory));
        $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_SORT_ORDER . '<br />' . xtc_draw_input_field('sort_order', $lInfo->sort_order));
        if (DEFAULT_LANGUAGE != $lInfo->code)
            $contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);

        $contents[] = array('align' => 'center', 'text' => '<br /><button type="submit" class="btn btn-primary">' . BUTTON_UPDATE . '</button> <a href="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id) . '"><button type="button" class="btn btn-default">' . BUTTON_CANCEL . '</button></a>');
        break;

    case 'delete':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_LANGUAGE . '</b>');
        $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
        $contents[] = array('text' => '<br /><b>' . $lInfo->name . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<br />' . (($remove_language) ? '<a href="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=deleteconfirm') . '"><button type="button" class="btn btn-danger">' . BUTTON_DELETE . '</button></a>' : '') . ' <a href="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id) . '"><button type="button" class="btn btn-default">' . BUTTON_CANCEL . '</button></a>');
        break;

    default:
        if (is_object($lInfo)) {
            $heading[] = array('text' => '<b><b>' . $lInfo->name . '</b></b>');
            $contents[] = array('align' => 'center', 'text' => '<a href="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=edit') . '"><button type="button" class="btn btn-primary">' . BUTTON_EDIT . '</button></a> <a href="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=delete') . '"><button type="button" class="btn btn-danger">' . BUTTON_DELETE . '</button></a>');
            $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_NAME . ' ' . $lInfo->name);

            if ($lInfo->status == 1) {
                $contents[] = array('text' => '<br />' . TABLE_HEADING_LANGUAGE_NAME . ': <b>' . TEXT_LANGUAGE_ACTIVE . '</b>');
            } else {
                $contents[] = array('text' => '<br />' . TABLE_HEADING_LANGUAGE_NAME . ': <b>' . TEXT_LANGUAGE_INACTIVE . '</b>');
            }
            if ($lInfo->status_admin == 1) {
                $contents[] = array('text' => TABLE_HEADING_LANGUAGE_STATUS_ADMIN . ': <b>' . TEXT_LANGUAGE_ACTIVE . '</b>');
            } else {
                $contents[] = array('text' => TABLE_HEADING_LANGUAGE_STATUS_ADMIN . ': <b>' . TEXT_LANGUAGE_INACTIVE . '</b>');
            }
            $contents[] = array('text' => TEXT_INFO_LANGUAGE_CODE . ' ' . $lInfo->code);
            $contents[] = array('text' => TEXT_INFO_LANGUAGE_CHARSET_INFO . ' ' . $lInfo->language_charset);
            $contents[] = array('text' => 'Icon: ' . xtc_image(DIR_WS_LANGUAGES . $lInfo->directory . '/' . $lInfo->image, $lInfo->name));
            $contents[] = array('text' => TEXT_INFO_LANGUAGE_DIRECTORY . '<br />' . DIR_WS_LANGUAGES . '<b>' . $lInfo->directory . '</b>');
            $contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_SORT_ORDER . ' ' . $lInfo->sort_order);
        }
        break;
}


if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
    $box = new box;
    $smarty->assign('SITE_BOX', $box->infoBox($heading, $contents));
}

$smarty->assign('DISPLAY_NUMBER', $languages_split->display_count($languages_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LANGUAGES));
$smarty->assign('DISPLAY_SITE', $languages_split->display_links($languages_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']));

if (!$_GET['action']) {
    $smarty->assign('NEW_BUTTON', xtc_button_link(BUTTON_NEW_LANGUAGE, xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=new')));
}
$smarty->assign('currencies', DEFAULT_CURRENCY);
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
$smarty->caching = false;
$smarty->template_dir = DIR_FS_CATALOG . 'admin/templates/';
$smarty->compile_dir = DIR_FS_CATALOG . 'admin/templates_c';
$smarty->config_dir = DIR_FS_CATALOG . 'lang';
$smarty->display(CURRENT_ADMIN_TEMPLATE . '/languages.html');


require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
