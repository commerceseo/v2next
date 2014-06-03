<?php
/* -----------------------------------------------------------------
 * 	$Id: customers_sik.php 420 2013-06-19 18:04:39Z akausch $
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
require_once (DIR_FS_INC . 'xtc_validate_vatid_status.inc.php');
require_once (DIR_FS_INC . 'xtc_get_geo_zone_code.inc.php');
require_once (DIR_FS_INC . 'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC . 'xtc_js_lang.php');

$customers_statuses_array = xtc_get_customers_statuses();

if ($_GET['special'] == 'remove_memo') {
    $mID = xtc_db_prepare_input($_GET['mID']);
    xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_MEMO . " WHERE memo_id = '" . $mID . "'");
    xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS_SIK, 'cID=' . (int) $_GET['cID'] . '&action=edit'));
}

if ($_GET['action'] == 'edit' || $_GET['action'] == 'update') {
    if ($_GET['cID'] == 1 && $_SESSION['customer_id'] == 1) {
        
    } else {
        if ($_GET['cID'] != 1) {
            
        } else {
            xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS_SIK, ''));
        }
    }
}

if ($_GET['action']) {
    switch ($_GET['action']) {
        case 'new_order' :

            $customers1_query = xtc_db_query("select * from " . TABLE_CUSTOMERS_SIK . " where customers_id = '" . $_GET['cID'] . "'");
            $customers1 = xtc_db_fetch_array($customers1_query);

            $customers_query = xtc_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $_GET['cID'] . "'");
            $customers = xtc_db_fetch_array($customers_query);

            $country_query = xtc_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . $customers['entry_country_id'] . "'");
            $country = xtc_db_fetch_array($country_query);

            $stat_query = xtc_db_query("select * from " . TABLE_CUSTOMERS_STATUS . " where customers_status_id = '" . $customers1[customers_status] . "' ");
            $stat = xtc_db_fetch_array($stat_query);

            $sql_data_array = array('customers_id' => xtc_db_prepare_input($customers['customers_id']), 'customers_cid' => xtc_db_prepare_input($customers1['customers_cid']), 'customers_vat_id' => xtc_db_prepare_input($customers1['customers_vat_id']), 'customers_status' => xtc_db_prepare_input($customers1['customers_status']), 'customers_status_name' => xtc_db_prepare_input($stat['customers_status_name']), 'customers_status_image' => xtc_db_prepare_input($stat['customers_status_image']), 'customers_status_discount' => xtc_db_prepare_input($stat['customers_status_discount']), 'customers_name' => xtc_db_prepare_input($customers['entry_firstname'] . ' ' . $customers['entry_lastname']), 'customers_company' => xtc_db_prepare_input($customers['entry_company']), 'customers_street_address' => xtc_db_prepare_input($customers['entry_street_address']), 'customers_suburb' => xtc_db_prepare_input($customers['entry_suburb']), 'customers_city' => xtc_db_prepare_input($customers['entry_city']), 'customers_postcode' => xtc_db_prepare_input($customers['entry_postcode']), 'customers_state' => xtc_db_prepare_input($customers['entry_state']), 'customers_country' => xtc_db_prepare_input($country['countries_name']), 'customers_telephone' => xtc_db_prepare_input($customers1['customers_telephone']), 'customers_email_address' => xtc_db_prepare_input($customers1['customers_email_address']), 'customers_address_format_id' => '5', 'customers_ip' => '0', 'delivery_name' => xtc_db_prepare_input($customers['entry_firstname'] . ' ' . $customers['entry_lastname']), 'delivery_company' => xtc_db_prepare_input($customers['entry_company']), 'delivery_street_address' => xtc_db_prepare_input($customers['entry_street_address']), 'delivery_suburb' => xtc_db_prepare_input($customers['entry_suburb']), 'delivery_city' => xtc_db_prepare_input($customers['entry_city']), 'delivery_postcode' => xtc_db_prepare_input($customers['entry_postcode']), 'delivery_state' => xtc_db_prepare_input($customers['entry_state']), 'delivery_country' => xtc_db_prepare_input($country['countries_name']), 'delivery_address_format_id' => '5', 'billing_name' => xtc_db_prepare_input($customers['entry_firstname'] . ' ' . $customers['entry_lastname']), 'billing_company' => xtc_db_prepare_input($customers['entry_company']), 'billing_street_address' => xtc_db_prepare_input($customers['entry_street_address']), 'billing_suburb' => xtc_db_prepare_input($customers['entry_suburb']), 'billing_city' => xtc_db_prepare_input($customers['entry_city']), 'billing_postcode' => xtc_db_prepare_input($customers['entry_postcode']), 'billing_state' => xtc_db_prepare_input($customers['entry_state']), 'billing_country' => xtc_db_prepare_input($country['countries_name']), 'billing_address_format_id' => '5', 'payment_method' => 'cod', 'cc_type' => '', 'cc_owner' => '', 'cc_number' => '', 'cc_expires' => '', 'cc_start' => '', 'cc_issue' => '', 'cc_cvv' => '', 'comments' => '', 'last_modified' => 'now()', 'date_purchased' => 'now()', 'orders_status' => '1', 'orders_date_finished' => '', 'currency' => 'EUR', 'currency_value' => '1.0000', 'account_type' => '0', 'payment_class' => 'cod', 'shipping_method' => 'Pauschale Versandkosten', 'shipping_class' => 'flat_flat', 'customers_ip' => '', 'language' => 'german');

            $insert_sql_data = array('currency_value' => '1.0000');
            $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
            xtc_db_perform(TABLE_ORDERS, $sql_data_array);
            $orders_id = xtc_db_insert_id();

            $sql_data_array = array('orders_id' => $orders_id, 'title' => '<b>Summe</b>:', 'text' => '0', 'value' => '0', 'class' => 'ot_total');

            $insert_sql_data = array('sort_order' => MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER);
            $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
            xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);

            $sql_data_array = array('orders_id' => $orders_id, 'title' => '<b>Zwischensumme</b>:', 'text' => '0', 'value' => '0', 'class' => 'ot_subtotal');

            $insert_sql_data = array('sort_order' => MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER);
            $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
            xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);

            xtc_redirect(xtc_href_link(FILENAME_ORDERS, 'oID=' . $orders_id . '&action=edit'));

            break;
        case 'statusconfirm' :
            $customers_id = xtc_db_prepare_input($_GET['cID']);
            $customer_updated = false;
            $check_status_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address , customers_status, member_flag from " . TABLE_CUSTOMERS_SIK . " where customers_id = '" . xtc_db_input($_GET['cID']) . "'");
            $check_status = xtc_db_fetch_array($check_status_query);
            if ($check_status['customers_status'] != $status) {
                xtc_db_query("update " . TABLE_CUSTOMERS_SIK . " set customers_status = '" . xtc_db_input($_POST['status']) . "' where customers_id = '" . xtc_db_input($_GET['cID']) . "'");

                // create insert for admin access table if customers status is set to 0
                if ($_POST['status'] == 0) {
                    xtc_db_query("INSERT into " . TABLE_ADMIN_ACCESS . " (customers_id,start) VALUES ('" . xtc_db_input($_GET['cID']) . "','1')");
                } else {
                    xtc_db_query("DELETE FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . xtc_db_input($_GET['cID']) . "'");
                }
                //Temporarily set due to above commented lines
                $customer_notified = '0';
                xtc_db_query("insert into " . TABLE_CUSTOMERS_STATUS_HISTORY . " (customers_id, new_value, old_value, date_added, customer_notified) values ('" . xtc_db_input($_GET['cID']) . "', '" . xtc_db_input($_POST['status']) . "', '" . $check_status['customers_status'] . "', now(), '" . $customer_notified . "')");
                $customer_updated = true;
            }
            xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS_SIK, 'page=' . $_GET['page'] . '&cID=' . $_GET['cID']));
            break;

        case 'update' :
            $customers_id = xtc_db_prepare_input($_GET['cID']);
            $customers_cid = xtc_db_prepare_input($_POST['csID']);
            $customers_vat_id = xtc_db_prepare_input($_POST['customers_vat_id']);
            $customers_vat_id_status = xtc_db_prepare_input($_POST['customers_vat_id_status']);
            $customers_firstname = xtc_db_prepare_input($_POST['customers_firstname']);
            $customers_lastname = xtc_db_prepare_input($_POST['customers_lastname']);
            $customers_email_address = xtc_db_prepare_input($_POST['customers_email_address']);
            $customers_telephone = xtc_db_prepare_input($_POST['customers_telephone']);
            $customers_fax = xtc_db_prepare_input($_POST['customers_fax']);
            $customers_newsletter = xtc_db_prepare_input($_POST['customers_newsletter']);

            $customers_gender = xtc_db_prepare_input($_POST['customers_gender']);
            $customers_dob = xtc_db_prepare_input($_POST['customers_dob']);

            $default_address_id = xtc_db_prepare_input($_POST['default_address_id']);
            $entry_street_address = xtc_db_prepare_input($_POST['entry_street_address']);
            $entry_suburb = xtc_db_prepare_input($_POST['entry_suburb']);
            $entry_postcode = xtc_db_prepare_input($_POST['entry_postcode']);
            $entry_city = xtc_db_prepare_input($_POST['entry_city']);
            $entry_country_id = xtc_db_prepare_input($_POST['entry_country_id']);

            $entry_company = xtc_db_prepare_input($_POST['entry_company']);
            $entry_state = xtc_db_prepare_input($_POST['entry_state']);
            $entry_zone_id = xtc_db_prepare_input($_POST['entry_zone_id']);

            $memo_title = xtc_db_prepare_input($_POST['memo_title']);
            $memo_text = xtc_db_prepare_input($_POST['memo_text']);

            $payment_unallowed = xtc_db_prepare_input($_POST['payment_unallowed']);
            $shipping_unallowed = xtc_db_prepare_input($_POST['shipping_unallowed']);
            $password = xtc_db_prepare_input($_POST['entry_password']);


            if ($memo_text != '' && $memo_title != '') {
                $sql_data_array = array('customers_id' => $_GET['cID'], 'memo_date' => date("Y-m-d"), 'memo_title' => $memo_title, 'memo_text' => $memo_text, 'poster_id' => $_SESSION['customer_id']);
                xtc_db_perform(TABLE_CUSTOMERS_MEMO, $sql_data_array);
            }
            $error = false; // reset error flag

            if (strlen($customers_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
                $error = true;
                $entry_firstname_error = true;
            } else {
                $entry_firstname_error = false;
            }

            if (strlen($customers_lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
                $error = true;
                $entry_lastname_error = true;
            } else {
                $entry_lastname_error = false;
            }

            if (ACCOUNT_DOB == 'true') {
                if (checkdate(substr(xtc_date_raw($customers_dob), 4, 2), substr(xtc_date_raw($customers_dob), 6, 2), substr(xtc_date_raw($customers_dob), 0, 4))) {
                    $entry_date_of_birth_error = false;
                } else {
                    $error = true;
                    $entry_date_of_birth_error = true;
                }
            }

            // New VAT Check
            if (xtc_get_geo_zone_code($entry_country_id) != '6') {
                require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.vat_validation.php');
                $vatID = new vat_validation($customers_vat_id, $customers_id, '', $entry_country_id);

                $customers_vat_id_status = $vatID->vat_info['vat_id_status'];
                $error = $vatID->vat_info['error'];

                if ($error == 1) {
                    $entry_vat_error = true;
                    $error = true;
                }
            }
            // New VAT CHECK END

            if (strlen($customers_email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
                $error = true;
                $entry_email_address_error = true;
            } else {
                $entry_email_address_error = false;
            }

            if (!xtc_validate_email($customers_email_address)) {
                $error = true;
                $entry_email_address_check_error = true;
            } else {
                $entry_email_address_check_error = false;
            }

            if (strlen($entry_street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
                $error = true;
                $entry_street_address_error = true;
            } else {
                $entry_street_address_error = false;
            }

            if (strlen($entry_postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
                $error = true;
                $entry_post_code_error = true;
            } else {
                $entry_post_code_error = false;
            }

            if (strlen($entry_city) < ENTRY_CITY_MIN_LENGTH) {
                $error = true;
                $entry_city_error = true;
            } else {
                $entry_city_error = false;
            }

            if ($entry_country_id == false) {
                $error = true;
                $entry_country_error = true;
            } else {
                $entry_country_error = false;
            }

            if (ACCOUNT_STATE == 'true') {
                if ($entry_country_error == true) {
                    $entry_state_error = true;
                } else {
                    $zone_id = 0;
                    $entry_state_error = false;
                    $check_query = xtc_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . xtc_db_input($entry_country_id) . "'");
                    $check_value = xtc_db_fetch_array($check_query);
                    $entry_state_has_zones = ($check_value['total'] > 0);
                    if ($entry_state_has_zones == true) {
                        $zone_query = xtc_db_query("select zone_id from " . TABLE_ZONES . " where zone_country_id = '" . xtc_db_input($entry_country_id) . "' and zone_name = '" . xtc_db_input($entry_state) . "'");
                        if (xtc_db_num_rows($zone_query) == 1) {
                            $zone_values = xtc_db_fetch_array($zone_query);
                            $entry_zone_id = $zone_values['zone_id'];
                        } else {
                            $zone_query = xtc_db_query("select zone_id from " . TABLE_ZONES . " where zone_country_id = '" . xtc_db_input($entry_country) . "' and zone_code = '" . xtc_db_input($entry_state) . "'");
                            if (xtc_db_num_rows($zone_query) >= 1) {
                                $zone_values = xtc_db_fetch_array($zone_query);
                                $zone_id = $zone_values['zone_id'];
                            } else {
                                $error = true;
                                $entry_state_error = true;
                            }
                        }
                    } else {
                        if ($entry_state == false) {
                            $error = true;
                            $entry_state_error = true;
                        }
                    }
                }
            }

            if (strlen($customers_telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
                $error = true;
                $entry_telephone_error = true;
            } else {
                $entry_telephone_error = false;
            }

            $check_email = xtc_db_query("select customers_email_address from " . TABLE_CUSTOMERS_SIK . " where customers_email_address = '" . xtc_db_input($customers_email_address) . "' and customers_id <> '" . xtc_db_input($customers_id) . "'");
            if (xtc_db_num_rows($check_email)) {
                $error = true;
                $entry_email_address_exists = true;
            } else {
                $entry_email_address_exists = false;
            }

            if ($error == false) {
                $sql_data_array = array('customers_firstname' => $customers_firstname, 'customers_cid' => $customers_cid, 'customers_vat_id' => $customers_vat_id, 'customers_vat_id_status' => $customers_vat_id_status, 'customers_lastname' => $customers_lastname, 'customers_email_address' => $customers_email_address, 'customers_telephone' => $customers_telephone, 'customers_fax' => $customers_fax, 'payment_unallowed' => $payment_unallowed, 'shipping_unallowed' => $shipping_unallowed, 'customers_newsletter' => $customers_newsletter, 'customers_last_modified' => 'now()');

                // if new password is set
                if ($password != "") {
                    $sql_data_array = array_merge($sql_data_array, array('customers_password' => xtc_encrypt_password($password)));
                }

                if (ACCOUNT_GENDER == 'true')
                    $sql_data_array['customers_gender'] = $customers_gender;
                if (ACCOUNT_DOB == 'true')
                    $sql_data_array['customers_dob'] = xtc_date_raw($customers_dob);

                xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" . xtc_db_input($customers_id) . "'");

                xtc_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . xtc_db_input($customers_id) . "'");

                if ($entry_zone_id > 0)
                    $entry_state = '';

                $sql_data_array = array('entry_firstname' => $customers_firstname, 'entry_lastname' => $customers_lastname, 'entry_street_address' => $entry_street_address, 'entry_postcode' => $entry_postcode, 'entry_city' => $entry_city, 'entry_country_id' => $entry_country_id, 'address_last_modified' => 'now()');


                if (ACCOUNT_COMPANY == 'true')
                    $sql_data_array['entry_company'] = $entry_company;
                if (ACCOUNT_SUBURB == 'true')
                    $sql_data_array['entry_suburb'] = $entry_suburb;

                if (ACCOUNT_STATE == 'true') {
                    if ($entry_zone_id > 0) {
                        $sql_data_array['entry_zone_id'] = $entry_zone_id;
                        $sql_data_array['entry_state'] = '';
                    } else {
                        $sql_data_array['entry_zone_id'] = '0';
                        $sql_data_array['entry_state'] = $entry_state;
                    }
                }

                xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "customers_id = '" . xtc_db_input($customers_id) . "' and address_book_id = '" . xtc_db_input($default_address_id) . "'");
                xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS_SIK, xtc_get_all_get_params(array('cID', 'action')) . 'cID=' . $customers_id));
            } elseif ($error == true) {
                $cInfo = new objectInfo($_POST);
                $processed = true;
            }

            break;
        case 'deleteconfirm' :
            $customers_id = xtc_db_prepare_input($_GET['cID']);

            if ($_POST['delete_reviews'] == 'on') {
                $reviews_query = xtc_db_query("select reviews_id from " . TABLE_REVIEWS . " where customers_id = '" . xtc_db_input($customers_id) . "'");
                while ($reviews = xtc_db_fetch_array($reviews_query)) {
                    xtc_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $reviews['reviews_id'] . "'");
                }
                xtc_db_query("delete from " . TABLE_REVIEWS . " where customers_id = '" . xtc_db_input($customers_id) . "'");
            } else {
                xtc_db_query("update " . TABLE_REVIEWS . " set customers_id = null where customers_id = '" . xtc_db_input($customers_id) . "'");
            }

            xtc_db_query("delete from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . xtc_db_input($customers_id) . "'");
            xtc_db_query("delete from " . TABLE_CUSTOMERS_SIK . " where customers_id = '" . xtc_db_input($customers_id) . "'");
            xtc_db_query("delete from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . xtc_db_input($customers_id) . "'");
            xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . xtc_db_input($customers_id) . "'");
            xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . xtc_db_input($customers_id) . "'");
            xtc_db_query("delete from " . TABLE_PRODUCTS_NOTIFICATIONS . " where customers_id = '" . xtc_db_input($customers_id) . "'");
            xtc_db_query("delete from " . TABLE_WHOS_ONLINE . " where customer_id = '" . xtc_db_input($customers_id) . "'");
            xtc_db_query("delete from " . TABLE_CUSTOMERS_STATUS_HISTORY . " where customers_id = '" . xtc_db_input($customers_id) . "'");
            xtc_db_query("delete from " . TABLE_CUSTOMERS_IP . " where customers_id = '" . xtc_db_input($customers_id) . "'");
            xtc_db_query("DELETE FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . xtc_db_input($customers_id) . "'");

            xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS_SIK, xtc_get_all_get_params(array('cID', 'action'))));
            break;

        default :
            $customers_query = xtc_db_query("select c.customers_id,c.customers_cid, c.customers_gender, c.customers_firstname, c.customers_lastname, c.customers_dob, c.customers_email_address, a.entry_company, a.entry_street_address, a.entry_suburb, a.entry_postcode, a.entry_city, a.entry_state, a.entry_zone_id, a.entry_country_id, c.customers_telephone, c.customers_fax, c.customers_newsletter, c.customers_default_address_id from " . TABLE_CUSTOMERS_SIK . " c left join " . TABLE_ADDRESS_BOOK . " a on c.customers_default_address_id = a.address_book_id where a.customers_id = c.customers_id and c.customers_id = '" . $_GET['cID'] . "'");
            $customers = xtc_db_fetch_array($customers_query);
            $cInfo = new objectInfo($customers);
    }
}

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td>
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading">
                                    <?php echo HEADING_TITLE; ?>
                                </td>
                            </tr>
                        </table>

                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="smallText" align="right" width="50%">
                                    <?php echo xtc_draw_form('status', FILENAME_CUSTOMERS_SIK, '', 'get'); ?>
                                    <?php echo HEADING_TITLE_STATUS . ' ' . xtc_draw_pull_down_menu('status', xtc_array_merge($select_data, $customers_statuses_array), '99', 'onChange="this.form.submit();"') . xtc_draw_hidden_field(xtc_session_name(), xtc_session_id()); ?>
                                    <?php echo '</form>'; ?>
                                </td>
                                <td class="smallText" align="right">
                                    <?php echo xtc_draw_form('search', FILENAME_CUSTOMERS_SIK, '', 'get'); ?>
                                    <?php echo HEADING_TITLE_SEARCH . ' ' . xtc_draw_input_field('search') . xtc_draw_hidden_field(xtc_session_name(), xtc_session_id()); ?>
                                    <?php echo '</form>'; ?>
                                </td>
                            </tr>
                        </table></td>
                </tr>
                <tr>
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top"><table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                                        <tr class="dataTableHeadingRow">
                                            <th class="dataTableHeadingContent" width="40"><?php echo TABLE_HEADING_ACCOUNT_TYPE; ?></th>
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_LASTNAME . xtc_sorting(FILENAME_CUSTOMERS_SIK, 'customers_lastname'); ?></th>
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_FIRSTNAME . xtc_sorting(FILENAME_CUSTOMERS_SIK, 'customers_firstname'); ?></th>
                                            <th class="dataTableHeadingContent" align="left"><?php echo HEADING_TITLE_STATUS; ?></th>
                                            <?php if (ACCOUNT_COMPANY_VAT_CHECK == 'true') { ?>
                                                <th class="dataTableHeadingContent" align="left"><?php echo HEADING_TITLE_VAT; ?></th>
                                            <?php } ?>
                                            <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACCOUNT_CREATED . xtc_sorting(FILENAME_CUSTOMERS_SIK, 'date_account_created'); ?></th>
                                            <th class="dataTableHeadingContent last" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</th>
                                        </tr>
                                        <?php
                                        $search = '';
                                        if (($_GET['search']) && (xtc_not_null($_GET['search']))) {
                                            $keywords = xtc_db_input(xtc_db_prepare_input($_GET['search']));
                                            $search = "and (c.customers_lastname like '%" . $keywords . "%' or c.customers_firstname like '%" . $keywords . "%' or c.customers_email_address like '%" . $keywords . "%')";
                                        }

                                        if ($_GET['status'] && $_GET['status'] != '100' or $_GET['status'] == '0') {
                                            $status = xtc_db_prepare_input($_GET['status']);
                                            //  echo $status;
                                            $search = "and c.customers_status = '" . $status . "'";
                                        }

                                        if ($_GET['sorting']) {
                                            switch ($_GET['sorting']) {

                                                case 'customers_firstname' :
                                                    $sort = 'order by c.customers_firstname';
                                                    break;

                                                case 'customers_firstname-desc' :
                                                    $sort = 'order by c.customers_firstname DESC';
                                                    break;

                                                case 'customers_lastname' :
                                                    $sort = 'order by c.customers_lastname';
                                                    break;

                                                case 'customers_lastname-desc' :
                                                    $sort = 'order by c.customers_lastname DESC';
                                                    break;

                                                case 'date_account_created' :
                                                    $sort = 'order by ci.customers_info_date_account_created';
                                                    break;

                                                case 'date_account_created-desc' :
                                                    $sort = 'order by ci.customers_info_date_account_created DESC';
                                                    break;
                                            }
                                        }

                                        $customers_query_raw = "select
	                                c.account_type,
	                                c.customers_id,
	                                c.customers_vat_id,
	                                c.customers_vat_id_status,
	                                c.customers_lastname,
	                                c.customers_firstname,
	                                c.customers_email_address,
	                                a.entry_country_id,
	                                c.customers_status,
	                                c.member_flag,
	                                ci.customers_info_date_account_created
	                                from
	                                " . TABLE_CUSTOMERS_SIK . " c ,
	                                " . TABLE_ADDRESS_BOOK . " a,
	                                " . TABLE_CUSTOMERS_INFO . " ci
	                                Where
	                                c.customers_id = a.customers_id
	                                and c.customers_default_address_id = a.address_book_id
	                                and ci.customers_info_id = c.customers_id
	                                " . $search . "
	                                group by c.customers_id
	                                " . $sort;

                                        $customers_split = new splitPageResults($_GET['page'], '20', $customers_query_raw, $customers_query_numrows);
                                        $customers_query = xtc_db_query($customers_query_raw);
                                        $i = 1;
                                        while ($customers = xtc_db_fetch_array($customers_query)) {
                                            $info_query = xtc_db_query("select customers_info_date_account_created as date_account_created, customers_info_date_account_last_modified as date_account_last_modified, customers_info_date_of_last_logon as date_last_logon, customers_info_number_of_logons as number_of_logons from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . $customers['customers_id'] . "'");
                                            $info = xtc_db_fetch_array($info_query);

                                            if (((!$_GET['cID']) || (@ $_GET['cID'] == $customers['customers_id'])) && (!$cInfo)) {
                                                $country_query = xtc_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . $customers['entry_country_id'] . "'");
                                                $country = xtc_db_fetch_array($country_query);

                                                $reviews_query = xtc_db_query("select count(*) as number_of_reviews from " . TABLE_REVIEWS . " where customers_id = '" . $customers['customers_id'] . "'");
                                                $reviews = xtc_db_fetch_array($reviews_query);

                                                $customer_info = xtc_array_merge($country, $info, $reviews);

                                                $cInfo_array = xtc_array_merge($customers, $customer_info);
                                                $cInfo = new objectInfo($cInfo_array);
                                            }

                                            if ((is_object($cInfo)) && ($customers['customers_id'] == $cInfo->customers_id)) {
                                                echo '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link(FILENAME_CUSTOMERS_SIK, xtc_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=edit') . '\'">' . "\n";
                                            } else {
                                                echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link(FILENAME_CUSTOMERS_SIK, xtc_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']) . '\'">' . "\n";
                                            }

                                            if ($customers['account_type'] == 1) {

                                                echo '<td class="dataTableContent">';
                                                echo TEXT_GUEST;
                                            } else {
                                                echo '<td class="dataTableContent">';
                                                echo TEXT_ACCOUNT;
                                            }
                                            echo '</td>';
                                            ?>
                                            <td class="dataTableContent"><b><?php echo $customers['customers_lastname']; ?></b></td>
                                            <td class="dataTableContent"><?php echo $customers['customers_firstname']; ?></td>
                                            <td class="dataTableContent" align="left"><?php echo $customers_statuses_array[$customers['customers_status']]['text'] . ' (' . $customers['customers_status'] . ')'; ?></td>
                                            <?php if (ACCOUNT_COMPANY_VAT_CHECK == 'true') { ?>
                                                <td class="dataTableContent" align="left">&nbsp;
        <?php
        if ($customers['customers_vat_id']) {
            echo $customers['customers_vat_id'] . '<br /><span style="font-size:8pt"><nobr>(' . xtc_validate_vatid_status($customers['customers_id']) . ')</nobr></span>';
        }
        ?>
                                                </td>
                                                <?php } ?>
                                            <td class="dataTableContent" align="right"><?php echo xtc_date_short($info['date_account_created']); ?></td>
                                            <td class="dataTableContent last" align="right">
                                                <?php
                                                if ((is_object($cInfo)) && ($customers['customers_id'] == $cInfo->customers_id)) {
                                                    echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', '');
                                                } else {
                                                    echo '<a href="' . xtc_href_link(FILENAME_CUSTOMERS_SIK, xtc_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                                                }
                                                ?>&nbsp;</td>
                                </tr>
                                                <?php $i++;
                                            } ?>
                        </table>
                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <tr>
                                <td class="smallText" valign="top"><?php echo $customers_split->display_count($customers_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                                <td class="smallText" align="right"><?php echo $customers_split->display_links($customers_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page'], xtc_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></td>
                            </tr>
<?php if (xtc_not_null($_GET['search'])) { ?>
                                <tr>
                                    <td align="right" colspan="2"><?php echo '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CUSTOMERS) . '">' . BUTTON_RESET . '</a>'; ?></td>
                                </tr>
<?php } ?>
                        </table>
                    </td>
                            <?php
                            $heading = array();
                            $contents = array();
                            switch ($_GET['action']) {
                                case 'confirm' :
                                    $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CUSTOMER . '</b>');

                                    $contents = array('form' => xtc_draw_form('customers', FILENAME_CUSTOMERS_SIK, xtc_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=deleteconfirm'));
                                    $contents[] = array('text' => TEXT_DELETE_INTRO . '<br /><br /><b>' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname . '</b>');
                                    if ($cInfo->number_of_reviews > 0)
                                        $contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('delete_reviews', 'on', true) . ' ' . sprintf(TEXT_DELETE_REVIEWS, $cInfo->number_of_reviews));
                                    $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" value="' . BUTTON_DELETE . '"><a class="button" href="' . xtc_href_link(FILENAME_CUSTOMERS_SIK, xtc_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id) . '">' . BUTTON_CANCEL . '</a>');
                                    break;

                                case 'editstatus' :
                                    if ($_GET['cID'] != 1) {
                                        $customers_history_query = xtc_db_query("select new_value, old_value, date_added, customer_notified from " . TABLE_CUSTOMERS_STATUS_HISTORY . " where customers_id = '" . xtc_db_input($_GET['cID']) . "' order by customers_status_history_id desc");
                                        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_STATUS_CUSTOMER . '</b>');
                                        $contents = array('form' => xtc_draw_form('customers', FILENAME_CUSTOMERS_SIK, xtc_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=statusconfirm'));
                                        $contents[] = array('text' => '<br />' . xtc_draw_pull_down_menu('status', $customers_statuses_array, $cInfo->customers_status));
                                        $contents[] = array('text' => '<table nowrap border="0" cellspacing="0" cellpadding="0"><tr><td style="border-bottom: 1px solid; border-color: #000000;" nowrap class="smallText" align="center"><b>' . TABLE_HEADING_NEW_VALUE . ' </b></td><td style="border-bottom: 1px solid; border-color: #000000;" nowrap class="smallText" align="center"><b>' . TABLE_HEADING_DATE_ADDED . '</b></td></tr>');

                                        if (xtc_db_num_rows($customers_history_query)) {
                                            while ($customers_history = xtc_db_fetch_array($customers_history_query)) {

                                                $contents[] = array('text' => '<tr>' . "\n" . '<td class="smallText">' . $customers_statuses_array[$customers_history['new_value']]['text'] . '</td>' . "\n" . '<td class="smallText" align="center">' . xtc_datetime_short($customers_history['date_added']) . '</td>' . "\n" . '<td class="smallText" align="center">');

                                                $contents[] = array('text' => '</tr>' . "\n");
                                            }
                                        } else {
                                            $contents[] = array('text' => '<tr>' . "\n" . ' <td class="smallText" colspan="2">' . TEXT_NO_CUSTOMER_HISTORY . '</td>' . "\n" . ' </tr>' . "\n");
                                        }
                                        $contents[] = array('text' => '</table>');
                                        $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" value="' . BUTTON_UPDATE . '"><a class="button" href="' . xtc_href_link(FILENAME_CUSTOMERS_SIK, xtc_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id) . '">' . BUTTON_CANCEL . '</a>');
                                        $status = xtc_db_prepare_input($_POST['status']); // maybe this line not needed to recheck...
                                    }
                                    break;

                                default :
                                    $customer_status = xtc_get_customer_status($_GET['cID']);
                                    $cs_id = $customer_status['customers_status'];
                                    $cs_member_flag = $customer_status['member_flag'];
                                    $cs_name = $customer_status['customers_status_name'];
                                    $cs_image = $customer_status['customers_status_image'];
                                    $cs_discount = $customer_status['customers_status_discount'];
                                    $cs_ot_discount_flag = $customer_status['customers_status_ot_discount_flag'];
                                    $cs_ot_discount = $customer_status['customers_status_ot_discount'];
                                    $cs_staffelpreise = $customer_status['customers_status_staffelpreise'];
                                    $cs_payment_unallowed = $customer_status['customers_status_payment_unallowed'];

                                    if (is_object($cInfo)) {
                                        $heading[] = array('text' => '<b>' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname . '</b>');

                                        if ($cInfo->customers_id != 1) {
                                            $contents[] = array('align' => 'center', 'text' => '<br /><a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CUSTOMERS_SIK, xtc_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=confirm') . '">' . BUTTON_DELETE . '</a> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_ORDERS, 'cID=' . $cInfo->customers_id) . '">' . BUTTON_ORDERS . '</a>');
                                        }

                                        $contents[] = array('align' => 'center', 'text' => '<br /><a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_MAIL, 'selected_box=tools&customer=' . $cInfo->customers_email_address) . '">' . BUTTON_EMAIL . '</a> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CUSTOMERS_SIK, xtc_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=iplog') . '">' . BUTTON_IPLOG . '</a>');
                                        $contents[] = array('text' => '<br />' . TEXT_DATE_ACCOUNT_CREATED . ' ' . xtc_date_short($cInfo->date_account_created));
                                        $contents[] = array('text' => '<br />' . TEXT_DATE_ACCOUNT_LAST_MODIFIED . ' ' . xtc_date_short($cInfo->date_account_last_modified));
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_LAST_LOGON . ' ' . xtc_date_short($cInfo->date_last_logon));
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_NUMBER_OF_LOGONS . ' ' . $cInfo->number_of_logons);
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY . ' ' . $cInfo->countries_name);
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_NUMBER_OF_REVIEWS . ' ' . $cInfo->number_of_reviews);
                                    }

                                    if ($_GET['action'] == 'iplog') {
                                        if (isset($_GET['cID'])) {
                                            $contents[] = array('text' => '<br /><b>IP-Log :');
                                            $customers_id = xtc_db_prepare_input($_GET['cID']);
                                            $customers_log_info_array = xtc_get_user_info($customers_id);
                                            if (xtc_db_num_rows($customers_log_info_array)) {
                                                while ($customers_log_info = xtc_db_fetch_array($customers_log_info_array)) {
                                                    $contents[] = array('text' => '<tr>' . "\n" . '<td class="smallText">' . $customers_log_info['customers_ip_date'] . ' ' . $customers_log_info['customers_ip'] . ' ' . $customers_log_info['customers_advertiser']);
                                                }
                                            }
                                        }
                                        break;
                                    }
                            }
                            if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
                                echo '            <td width="25%" valign="top">' . "\n";

                                $box = new box;
                                echo $box->infoBox($heading, $contents);

                                echo '            </td>' . "\n";
                            }
                            ?>
                </tr>
            </table></td>
    </tr>
</table></td>
</tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
