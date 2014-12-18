<?php

/* -----------------------------------------------------------------
 * 	$Id: class.account.php 816 2014-01-28 07:07:08Z akausch $
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

class account_ORIGINAL {

    function account_ORIGINAL() {
        
    }

    function accountorderhistory($cid) {
        require_once (DIR_FS_INC . 'xtc_date_short.inc.php');
        $order_content = array();
        $orders_query = xtc_db_query("SELECT
										o.*,
										ot.text as order_total,
										s.orders_status_name
									FROM 
										" . TABLE_ORDERS . " o, 
										" . TABLE_ORDERS_TOTAL . " ot, 
										" . TABLE_ORDERS_STATUS . " s
									WHERE 
										o.customers_id = '" . (int) $cid . "'
									AND 
										o.orders_id = ot.orders_id
									AND 
										ot.class = 'ot_total'
									AND 
										o.orders_status = s.orders_status_id
									AND 
										s.language_id = '" . (int) $_SESSION['languages_id'] . "'
									ORDER BY orders_id DESC");

        while ($orders = xtc_db_fetch_array($orders_query)) {
            if (xtc_not_null($orders['delivery_name'])) {
                $order_name = $orders['delivery_name'];
                $order_country = $orders['delivery_country'];
            } else {
                $order_name = $orders['billing_name'];
                $order_country = $orders['billing_country'];
            }
            $order_content[] = array('ORDER_ID' => $orders['orders_id'],
                'ORDER_DATE' => xtc_date_short($orders['date_purchased']),
                'ORDER_STATUS' => $orders['orders_status_name'],
                'ORDER_TOTAL' => $orders['order_total'],
                'ORDER_LINK' => xtc_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $orders['orders_id'], 'SSL'),
                'ORDER_BUTTON' => '<a href="' . xtc_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $orders['orders_id'], 'SSL') . '">' . xtc_image_button('small_view.gif', SMALL_IMAGE_BUTTON_VIEW) . '</a>');
        }
        return $order_content;
    }

    function account_smarty($site) {
        if ($site == 'account') {
            if (ACTIVATE_GIFT_SYSTEM == 'true') {
                $account_smarty['ACTIVATE_GIFT'] = 'true';
            }
            if (!isset($_SESSION['customer_id'])) {
                $account_smarty['LINK_LOGIN'] = xtc_href_link(FILENAME_LOGIN, '', 'SSL');
            }
            $account_smarty['LINK_EDIT'] = xtc_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL');
            $account_smarty['LINK_ADDRESS'] = xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL');
            $account_smarty['LINK_PASSWORD'] = xtc_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL');
            $account_smarty['LINK_DELETE'] = xtc_href_link(FILENAME_ACCOUNT_DELETE, '', 'SSL');
            $account_smarty['LINK_ORDERS'] = xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL');
            $account_smarty['LINK_NEWSLETTER'] = xtc_href_link(FILENAME_NEWSLETTER, '', 'SSL');
            $account_smarty['LINK_ALL'] = xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL');
            if (RMA_MODUL_ON == 'true') {
                $account_smarty['RMA_STEP1'] = xtc_href_link(FILENAME_RMA_STEP1, '', 'SSL');
            }

            return $account_smarty;
        }
    }

    function account_delete_smarty($site) {
        if ($site == 'account_delete') {
            $account_smarty['FORM_ACTION'] = xtc_draw_form('account_delete', xtc_href_link(FILENAME_ACCOUNT_DELETE, '', 'SSL'), 'post') . xtc_draw_hidden_field('action', 'process_account_delete');
            $account_smarty['BUTTON_BACK'] = '<a href="' . xtc_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>';
            $account_smarty['BUTTON_SUBMIT'] = xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE);
            $account_smarty['FORM_END'] = '</form>';
            return $account_smarty;
        }
    }

    function account_history($history_query) {
        while ($history = xtc_db_fetch_array($history_query)) {
            $products = xtc_db_fetch_array(xtc_db_query("SELECT count(*) AS count FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id = '" . (int) $history['orders_id'] . "';"));

            if (xtc_not_null($history['delivery_name'])) {
                $order_type = TEXT_ORDER_SHIPPED_TO;
                $order_name = $history['delivery_name'];
            } else {
                $order_type = TEXT_ORDER_BILLED_TO;
                $order_name = $history['billing_name'];
            }
            $module_content[] = array('ORDER_ID' => $history['orders_id'],
                'ORDER_STATUS' => $history['orders_status_name'],
                'ORDER_DATE' => xtc_date_short($history['date_purchased']),
                'ORDER_PRODUCTS' => $products['count'],
                'ORDER_TOTAL' => strip_tags($history['order_total']),
                'ORDER_BUTTON' => '<a href="' . xtc_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $history['orders_id'] . '&page=' . (empty($_GET['page']) ? "1" : (int) $_GET['page']), 'SSL') . '">' . xtc_image_button('small_view.gif', SMALL_IMAGE_BUTTON_VIEW) . '</a>');
        }
        return $module_content;
    }

    function account_edit_smarty($site) {
        if (isset($_POST['action']) && ($_POST['action'] == 'process_account')) {
            $account_edit_check = $this->account_edit_check($site);
            $account_smarty['error'] = $account_edit_check;
            $account['customers_firstname'] = xtc_db_prepare_input($_POST['firstname']);
            $account['customers_lastname'] = xtc_db_prepare_input($_POST['lastname']);
            $account['customers_email_address'] = xtc_db_prepare_input($_POST['email_address']);
            $account['customers_telephone'] = xtc_db_prepare_input($_POST['telephone']);
            $account['customers_fax'] = xtc_db_prepare_input($_POST['fax']);
            $account['customers_gender'] = xtc_db_prepare_input($_POST['gender']);
            $account['customers_vat_id'] = xtc_db_prepare_input($_POST['vat']);
            $account['customers_dob'] = xtc_db_prepare_input($_POST['dob']);
        } else {
            $account = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "';"));
        }
        $account_smarty['FORM_ACTION'] = xtc_draw_form('account_edit', xtc_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'), 'post') . xtc_draw_hidden_field('action', 'process_account');
        $account_smarty['INPUT_FIRSTNAME'] = xtc_draw_input_fieldNote(array('name' => 'firstname', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_FIRST_NAME_TEXT . '</span>' : '')), $account['customers_firstname']);
        $account_smarty['INPUT_LASTNAME'] = xtc_draw_input_fieldNote(array('name' => 'lastname', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), $account['customers_lastname']);
        $account_smarty['csID'] = $account['customers_cid'];
        $account_smarty['INPUT_EMAIL'] = xtc_draw_input_fieldNote(array('name' => 'email_address', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_EMAIL_ADDRESS_TEXT . '</span>' : '')), $account['customers_email_address']);
        $account_smarty['INPUT_TEL'] = xtc_draw_input_fieldNote(array('name' => 'telephone', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_TELEPHONE_NUMBER_TEXT) ? '<span class="inputRequirement">' . ENTRY_TELEPHONE_NUMBER_TEXT . '</span>' : '')), $account['customers_telephone']);
        $account_smarty['INPUT_FAX'] = xtc_draw_input_fieldNote(array('name' => 'fax', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_FAX_NUMBER_TEXT) ? '<span class="inputRequirement">' . ENTRY_FAX_NUMBER_TEXT . '</span>' : '')), $account['customers_fax']);
        $account_smarty['BUTTON_BACK'] = '<a href="' . xtc_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>';
        $account_smarty['BUTTON_SUBMIT'] = xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE);
        $account_smarty['FORM_END'] = '</form>';

        if (ACCOUNT_GENDER == 'true') {
            $account_smarty['gender'] = '1';
            $male = ($account['customers_gender'] == 'm') ? true : false;
            $female = !$male;
            $account_smarty['INPUT_MALE'] = xtc_draw_radio_field(array('name' => 'gender', 'suffix' => MALE . '&nbsp;'), 'm', $male);
            $account_smarty['INPUT_FEMALE'] = xtc_draw_radio_field(array('name' => 'gender', 'suffix' => FEMALE . '&nbsp;', 'text' => (xtc_not_null(ENTRY_GENDER_TEXT) ? '<span class="inputRequirement">' . ENTRY_GENDER_TEXT . '</span>' : '')), 'f', $female);
        }

        if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
            $account_smarty['vat'] = '1';
            $account_smarty['INPUT_VAT'] = xtc_draw_input_fieldNote(array('name' => 'vat', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_VAT_TEXT) ? '<span class="inputRequirement">' . ENTRY_VAT_TEXT . '</span>' : '')), $account['customers_vat_id']);
        } else {
            $account_smarty['vat'] = '0';
        }

        if (ACCOUNT_DOB == 'true') {
            $account_smarty['birthdate'] = '1';
            if (ENTRY_DOB_MIN_LENGTH > 0) {
                $account_smarty['INPUT_DOB'] = xtc_draw_input_fieldNote(array('name' => 'dob', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_DATE_OF_BIRTH_TEXT) ? '<span class="inputRequirement">' . ENTRY_DATE_OF_BIRTH_TEXT . '</span>' : '')), xtc_date_short($account['customers_dob']));
            } else {
                $account_smarty['INPUT_DOB'] = xtc_draw_input_fieldNote(array('name' => 'dob', 'text' => ''), xtc_date_short($account['customers_dob']));
            }
        }


        return $account_smarty;
    }

    function account_edit_check($site) {
        $messageStack = new messageStack;
        if (ACCOUNT_GENDER == 'true') {
            $gender = xtc_db_prepare_input($_POST['gender']);
        }
        $firstname = xtc_db_prepare_input($_POST['firstname']);
        $lastname = xtc_db_prepare_input($_POST['lastname']);
        if (ACCOUNT_DOB == 'true') {
            $dob = xtc_db_prepare_input($_POST['dob']);
        }
        if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
            $vat = xtc_db_prepare_input($_POST['vat']);
        }
        $email_address = xtc_db_prepare_input($_POST['email_address']);
        $telephone = xtc_db_prepare_input($_POST['telephone']);
        $fax = xtc_db_prepare_input($_POST['fax']);

        $error = false;

        if (ACCOUNT_GENDER == 'true') {
            if (($gender != 'm') && ($gender != 'f')) {
                $error = true;
                $messageStack->add('account_edit', ENTRY_GENDER_ERROR);
            }
        }

        if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
            $error = true;
            $messageStack->add('account_edit', ENTRY_FIRST_NAME_ERROR);
        }

        if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
            $error = true;
            $messageStack->add('account_edit', ENTRY_LAST_NAME_ERROR);
        }

        if (ACCOUNT_DOB == 'true') {
            if (ENTRY_DOB_MIN_LENGTH > 0 AND $dob != '') {
                if (checkdate(substr(xtc_date_raw($dob), 4, 2), substr(xtc_date_raw($dob), 6, 2), substr(xtc_date_raw($dob), 0, 4)) == false) {
                    $error = true;
                    $messageStack->add('account_edit', ENTRY_DATE_OF_BIRTH_ERROR);
                }
            }
        }

        $country = xtc_get_customers_country((int) $_SESSION['customer_id']);
        require_once(DIR_WS_CLASSES . 'class.vat_validation.php');
        $vatID = new vat_validation($vat, (int) $_SESSION['customer_id'], '', $country);

        $customers_status = $vatID->vat_info['status'];
        $customers_vat_id_status = $vatID->vat_info['vat_id_status'];
        if ($vatID->vat_info['error'] == 1) {
            $messageStack->add('account_edit', ENTRY_VAT_ERROR);
            $error = true;
        }

        if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
            $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_ERROR);
            $error = true;
        }

        if (xtc_validate_email($email_address) == false) {
            $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
            $error = true;
        } else {
            $check_email = xtc_db_fetch_array(xtc_db_query("SELECT count(*) as total FROM " . TABLE_CUSTOMERS . " WHERE customers_email_address = '" . xtc_db_input($email_address) . "' AND account_type = '0' AND customers_id != '" . (int) $_SESSION['customer_id'] . "'"));
            if ($check_email['total'] > 0) {
                $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);
                $error = true;
            }
        }

        if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
            $messageStack->add('account_edit', ENTRY_TELEPHONE_NUMBER_ERROR);
            $error = true;
        }

        if ($error == false) {
            $sql_data_array = array('customers_vat_id' => $vat,
                'customers_vat_id_status' => $customers_vat_id_status,
                'customers_firstname' => $firstname,
                'customers_lastname' => $lastname,
                'customers_email_address' => $email_address,
                'customers_telephone' => $telephone,
                'customers_fax' => $fax,
                'customers_last_modified' => 'now()');

            if (ACCOUNT_GENDER == 'true') {
                $sql_data_array['customers_gender'] = $gender;
            }
            if (ACCOUNT_DOB == 'true') {
                $sql_data_array['customers_dob'] = xtc_date_raw($dob);
            }

            xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" . (int) $_SESSION['customer_id'] . "'");

            xtc_db_query("UPDATE " . TABLE_CUSTOMERS_INFO . " SET customers_info_date_account_last_modified = now() WHERE customers_info_id = '" . (int) $_SESSION['customer_id'] . "';");

            // reset the session variables
            $customer_first_name = $firstname;
            $messageStack->add_session('account', SUCCESS_ACCOUNT_UPDATED, 'success');
            xtc_redirect(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
        }
        if ($messageStack->size('account_edit') > 0) {
            return $messageStack->output('account_edit');
        }
    }

    function adress_book() {

        $addresses_data = array();
        $addresses_query = xtc_db_query("SELECT 
											address_book_id, 
											entry_firstname AS firstname, 
											entry_lastname AS lastname, 
											entry_company AS company, 
											entry_street_address AS street_address, 
											entry_suburb AS suburb, 
											entry_city AS city, 
											entry_postcode AS postcode, 
											entry_state AS state, 
											entry_zone_id AS zone_id, 
											entry_country_id AS country_id 
										FROM 
											" . TABLE_ADDRESS_BOOK . " 
										WHERE 
											customers_id = '" . (int) $_SESSION['customer_id'] . "' 
										ORDER BY 
											firstname, lastname");

        while ($addresses = xtc_db_fetch_array($addresses_query)) {
            $format_id = xtc_get_address_format_id($addresses['country_id']);
            if ($addresses['address_book_id'] == $_SESSION['customer_default_address_id']) {
                $primary = 1;
            } else {
                $primary = 0;
            }
            $addresses_data[] = array(
                'NAME' => $addresses['firstname'] . ' ' . $addresses['lastname'],
                'BUTTON_EDIT' => '<a href="' . xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'edit=' . $addresses['address_book_id'], 'SSL') . '">' . xtc_image_button('small_edit.gif', SMALL_IMAGE_BUTTON_EDIT) . '</a>',
                'BUTTON_DELETE' => '<a href="' . xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'delete=' . $addresses['address_book_id'], 'SSL') . '">' . xtc_image_button('small_delete.gif', SMALL_IMAGE_BUTTON_DELETE) . '</a>',
                'ADDRESS' => xtc_address_format($format_id, $addresses, true, ' ', '<br />'),
                'PRIMARY' => $primary);
        }
        return $addresses_data;
    }

    function address_book_process($entry) {
        if (ACCOUNT_GENDER == 'true') {
            $male = ($entry['entry_gender'] == 'm') ? true : false;
            $female = ($entry['entry_gender'] == 'f') ? true : false;
            $account_smarty['gender'] = '1';
            $account_smarty['INPUT_MALE'] = xtc_draw_radio_field(array('name' => 'gender', 'suffix' => MALE . '&nbsp;'), 'm', $male);
            $account_smarty['INPUT_FEMALE'] = xtc_draw_radio_field(array('name' => 'gender', 'suffix' => FEMALE . '&nbsp;', 'text' => (xtc_not_null(ENTRY_GENDER_TEXT) ? '<span class="inputRequirement">&nbsp;' . ENTRY_GENDER_TEXT . '</span>' : '')), 'f', $female);
        }
        if (ACCOUNT_COMPANY == 'true') {
            $account_smarty['company'] = '1';
            $account_smarty['INPUT_COMPANY'] = xtc_draw_input_fieldNote(array('name' => 'company', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_COMPANY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COMPANY_TEXT . '</span>' : '')), $entry['entry_company']);
        }

        if (ACCOUNT_SUBURB == 'true') {
            $account_smarty['suburb'] = '1';
            $account_smarty['INPUT_SUBURB'] = xtc_draw_input_fieldNote(array('name' => 'suburb', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_SUBURB_TEXT) ? '<span class="inputRequirement">' . ENTRY_SUBURB_TEXT . '</span>' : '')), $entry['entry_suburb']);
        }

        if (ACCOUNT_STATE == 'true') {
            $account_smarty['state'] = '1';
            $check = xtc_db_fetch_array(xtc_db_query("SELECT count(*) AS total FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . (int) $entry['entry_country_id'] . "';"));
            if ($check['total'] > 0) {
                $entry_state_has_zones = true;
            } else {
                $entry_state_has_zones = false;
            }
            if ($entry_state_has_zones) {
                $zones_array = array();
                $zones_query = xtc_db_query("SELECT zone_id, zone_name FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . (isset($entry['entry_country_id']) ? (int) $entry['entry_country_id'] : STORE_COUNTRY) . "' ORDER BY zone_name");
                while ($zones_values = xtc_db_fetch_array($zones_query)) {
                    $zones_array[] = array('id' => $zones_values['zone_id'], 'text' => $zones_values['zone_name']);
                }
                $account_smarty['INPUT_STATE'] = xtc_draw_pull_down_menuNote(array('name' => 'state', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">' . ENTRY_STATE_TEXT . '</span>' : '')), $zones_array, xtc_db_prepare_input($entry['entry_zone_id']), ' class="create_account_state" id="create_state"');
            } else {
                $account_smarty['INPUT_STATE'] = 'false';
            }
        } else {
            $account_smarty['state'] = '0';
        }
        if (isset($entry['entry_country_id'])) {
            $selected = $entry['entry_country_id'];
        } elseif (isset($_SESSION['country'])) {
            $selected = $_SESSION['country'];
        } else {
            $selected = STORE_COUNTRY;
        }
        $account_smarty['INPUT_FIRSTNAME'] = xtc_draw_input_fieldNote(array('name' => 'firstname', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_FIRST_NAME_TEXT . '</span>' : '')), $entry['entry_firstname']);
        $account_smarty['INPUT_LASTNAME'] = xtc_draw_input_fieldNote(array('name' => 'lastname', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), $entry['entry_lastname']);
        $account_smarty['INPUT_STREET'] = xtc_draw_input_fieldNote(array('name' => 'street_address', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_STREET_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_STREET_ADDRESS_TEXT . '</span>' : '')), $entry['entry_street_address']);
        $account_smarty['INPUT_CODE'] = xtc_draw_input_fieldNote(array('name' => 'postcode', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_POST_CODE_TEXT) ? '<span class="inputRequirement">' . ENTRY_POST_CODE_TEXT . '</span>' : '')), $entry['entry_postcode']);
        $account_smarty['INPUT_CITY'] = xtc_draw_input_fieldNote(array('name' => 'city', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_CITY_TEXT) ? '<span class="inputRequirement">' . ENTRY_CITY_TEXT . '</span>' : '')), $entry['entry_city']);
        $account_smarty['SELECT_COUNTRY'] = xtc_get_country_list(array('name' => 'country', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COUNTRY_TEXT . '</span>' : '')), $selected, 'id="country" class="create_account_country"');
        return $account_smarty;
    }

    function address_book_process_delete($Aid) {
        $messageStack = new messageStack;
        xtc_db_query("DELETE FROM " . TABLE_ADDRESS_BOOK . " WHERE address_book_id = '" . (int) $Aid . "' AND customers_id = '" . (int) $_SESSION['customer_id'] . "';");
        $messageStack->add_session('addressbook', SUCCESS_ADDRESS_BOOK_ENTRY_DELETED, 'success');
        xtc_redirect(xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    }

    function address_book_process_edit() {
        $messageStack = new messageStack;
        $process = true;
        $error = false;

        if (ACCOUNT_GENDER == 'true') {
            $gender = xtc_db_prepare_input($_POST['gender']);
        }
        if (ACCOUNT_COMPANY == 'true') {
            $company = xtc_db_prepare_input($_POST['company']);
        }
        $firstname = xtc_db_prepare_input($_POST['firstname']);
        $lastname = xtc_db_prepare_input($_POST['lastname']);
        $street_address = xtc_db_prepare_input($_POST['street_address']);
        if (ACCOUNT_SUBURB == 'true') {
            $suburb = xtc_db_prepare_input($_POST['suburb']);
        }
        $postcode = xtc_db_prepare_input($_POST['postcode']);
        $city = xtc_db_prepare_input($_POST['city']);
        $country = xtc_db_prepare_input($_POST['country']);
        if (ACCOUNT_STATE == 'true') {
            $state = xtc_db_prepare_input($_POST['state']);
        }

        if (ACCOUNT_GENDER == 'true') {
            if (($gender != 'm') && ($gender != 'f')) {
                $error = true;
                $messageStack->add('addressbook', ENTRY_GENDER_ERROR);
            }
        }

        if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
            $error = true;
            $messageStack->add('addressbook', ENTRY_FIRST_NAME_ERROR);
        }

        if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
            $error = true;
            $messageStack->add('addressbook', ENTRY_LAST_NAME_ERROR);
        }

        if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
            $error = true;
            $messageStack->add('addressbook', ENTRY_STREET_ADDRESS_ERROR);
        }

        if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
            $error = true;
            $messageStack->add('addressbook', ENTRY_POST_CODE_ERROR);
        }

        if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
            $error = true;
            $messageStack->add('addressbook', ENTRY_CITY_ERROR);
        }

        if (is_numeric($country) == false) {
            $error = true;
            $messageStack->add('addressbook', ENTRY_COUNTRY_ERROR);
        }

        if (ACCOUNT_STATE == 'true') {
            $zone_id = 0;
            $check = xtc_db_fetch_array(xtc_db_query("SELECT count(*) AS total FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . (int) $country . "';"));
            if ($check['total'] > 0) {
                $entry_state_has_zones = true;
            } else {
                $entry_state_has_zones = false;
            }
            if ($entry_state_has_zones == true) {
                $zone_query = xtc_db_query("SELECT zone_id, zone_name from " . TABLE_ZONES . " WHERE zone_country_id = '" . (int) $country . "' AND zone_id = '" . (int) $state . "' ");
                if (xtc_db_num_rows($zone_query) >= 1) {
                    $zone = xtc_db_fetch_array($zone_query);
                    $zone_id = $zone['zone_id'];
                } else {
                    $error = true;
                    $messageStack->add('addressbook', ENTRY_STATE_ERROR_SELECT);
                }
            }
        }

        if ($error == false) {
            $sql_data_array = array('entry_firstname' => $firstname,
                'entry_lastname' => $lastname,
                'entry_street_address' => $street_address,
                'entry_postcode' => $postcode,
                'entry_city' => $city,
                'entry_country_id' => (int) $country,
                'address_last_modified' => 'now()');

            if (ACCOUNT_GENDER == 'true') {
                $sql_data_array['entry_gender'] = $gender;
            }
            if (ACCOUNT_COMPANY == 'true') {
                $sql_data_array['entry_company'] = $company;
            }
            if (ACCOUNT_SUBURB == 'true') {
                $sql_data_array['entry_suburb'] = $suburb;
            }
            if (ACCOUNT_STATE == 'true') {
                if ($zone_id > 0) {
                    $sql_data_array['entry_zone_id'] = (int) $zone_id;
                    $sql_data_array['entry_state'] = '';
                } else {
                    $sql_data_array['entry_zone_id'] = '0';
                    $sql_data_array['entry_state'] = $state;
                }
            }

            if ($_POST['action'] == 'update') {
                xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "address_book_id = '" . (int) $_GET['edit'] . "' and customers_id ='" . (int) $_SESSION['customer_id'] . "'");
                // reregister session variables
                if ((isset($_POST['primary']) && ($_POST['primary'] == 'on')) || ($_GET['edit'] == $_SESSION['customer_default_address_id'])) {
                    $_SESSION['customer_first_name'] = $firstname;
                    $_SESSION['customer_country_id'] = $country_id;
                    $_SESSION['customer_zone_id'] = (($zone_id > 0) ? (int) $zone_id : '0');
                    $_SESSION['customer_default_address_id'] = (int) $_GET['edit'];

                    $sql_data_array = array('customers_firstname' => $firstname,
                        'customers_lastname' => $lastname,
                        'customers_default_address_id' => (int) $_GET['edit'],
                        'customers_last_modified' => 'now()');

                    if (ACCOUNT_GENDER == 'true') {
                        $sql_data_array['customers_gender'] = $gender;
                    }

                    xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" . (int) $_SESSION['customer_id'] . "'");
                }
            } else {
                $sql_data_array['customers_id'] = (int) $_SESSION['customer_id'];
                $sql_data_array['address_date_added'] = 'now()';
                xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

                $new_address_book_id = xtc_db_insert_id();

                // reregister session variables
                if (isset($_POST['primary']) && ($_POST['primary'] == 'on')) {
                    $_SESSION['customer_first_name'] = $firstname;
                    $_SESSION['customer_country_id'] = $country_id;
                    $_SESSION['customer_zone_id'] = (($zone_id > 0) ? (int) $zone_id : '0');
                    if (isset($_POST['primary']) && ($_POST['primary'] == 'on')) {
                        $_SESSION['customer_default_address_id'] = $new_address_book_id;
                    }
                    $sql_data_array = array('customers_firstname' => $firstname,
                        'customers_lastname' => $lastname,
                        'customers_last_modified' => 'now()',
                        'customers_date_added' => 'now()');

                    if (ACCOUNT_GENDER == 'true') {
                        $sql_data_array['customers_gender'] = $gender;
                    }
                    if (isset($_POST['primary']) && ($_POST['primary'] == 'on')) {
                        $sql_data_array['customers_default_address_id'] = $new_address_book_id;
                    }
                    xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" . (int) $_SESSION['customer_id'] . "'");
                }
            }
            $messageStack->add_session('addressbook', SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED, 'success');

            if ($messageStack->size('addressbook') > 0) {
                return $messageStack->output('addressbook');
            }
            xtc_redirect(xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
        }
    }

    function account_history_info($Oid) {
        $order = new order($Oid);
        // Delivery Info
        if ($order->delivery != false) {
            $account_smarty['DELIVERY_LABEL'] = xtc_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />');
            if ($order->info['shipping_method']) {
                $account_smarty['SHIPPING_METHOD'] = $order->info['shipping_method'];
            }
        }

        $order_total = $order->getTotalData($Oid);
        $account_smarty['order_data'] = $order->getOrderData($Oid);
        $account_smarty['order_total'] = $order_total['data'];

        // Payment Method
        if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') {
            include (DIR_WS_LANGUAGES . '/' . $_SESSION['language'] . '/modules/payment/' . $order->info['payment_method'] . '.php');
            $account_smarty['PAYMENT_METHOD'] = constant(MODULE_PAYMENT_ . strtoupper($order->info['payment_method']) . _TEXT_TITLE);
        }

        // Order History
        $statuses_query = xtc_db_query("SELECT 
											os.orders_status_name, 
											osh.date_added, 
											osh.comments 
										FROM 
											" . TABLE_ORDERS_STATUS . " AS os
										INNER JOIN
											" . TABLE_ORDERS_STATUS_HISTORY . " AS osh ON(osh.orders_status_id = os.orders_status_id AND osh.customer_notified = '1')
										WHERE 
											osh.orders_id = '" . $Oid . "' 
										AND 
											os.language_id = '" . (int) $_SESSION['languages_id'] . "'
										ORDER BY osh.date_added;");
        while ($statuses = xtc_db_fetch_array($statuses_query)) {
            $history_block .= xtc_date_short($statuses['date_added']);
            $history_block .= ' <b>' . $statuses['orders_status_name'] . '</b><br />';
            $history_block .= (empty($statuses['comments']) ? '' : '<em>' . nl2br(htmlspecialchars($statuses['comments'])) . '</em><br />');
        }

        $account_smarty['HISTORY_BLOCK'] = $history_block;
        // Download-PDF Bill
        if (file_exists('download_pdf_bill.php')) {
            //PDF Rechnung Download
            $pdf_bill_query = xtc_db_fetch_array(xtc_db_query("SELECT order_id, bill_name FROM " . TABLE_ORDERS_PDF . " WHERE order_id = '" . $Oid . "';"));
            $pdfbill = xtc_href_link(FILENAME_DOWNLOAD_PDF_BILL, 'order=' . $pdf_bill_query['order_id']);
            if ($pdf_bill_query['order_id'] != '') {
                $account_smarty['IPDFBILL_INVOICE_DOWNLOAD'] = $pdfbill;
            }
        }

        $account_smarty['ORDER_NUMBER'] = $Oid;
        $account_smarty['ORDER_DATE'] = xtc_date_short($order->info['date_purchased']);
        $account_smarty['ORDER_STATUS'] = $order->info['orders_status'];
        $account_smarty['BILLING_LABEL'] = xtc_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />');
        $account_smarty['PRODUCTS_EDIT'] = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
        $account_smarty['SHIPPING_ADDRESS_EDIT'] = xtc_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL');
        $account_smarty['BILLING_ADDRESS_EDIT'] = xtc_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL');
        $account_smarty['BUTTON_PRINT'] = '<a class="shipping" href="' . xtc_href_link(FILENAME_PRINT_ORDER, 'oID=' . $Oid) . '">' . xtc_image_button('button_print.gif', IMAGE_BUTTON_PRINT) . '</a>';

        if (WITHDRAWAL_WEBFORM_ACTIVE == 'true') {
            $account_smarty['WITHDRAWAL_BUTTON'] = '<a href="' . xtc_href_link(FILENAME_WITHDRAWAL, 'order=' . $Oid . '', 'SSL') . '">' . BUTTON_WITHDRAWAL . '</a>';
        }
        if (WRCHECKOUT == 'true' && WRCHECKOUTFILE != '') {
            $account_smarty['WITHDRAWAL_BUTTON_PDF'] = '<a href="' . xtc_href_link(WRCHECKOUTFILE, '', 'SSL') . '">' . BUTTON_WITHDRAWAL_PDF . '</a>';
        }
        return $account_smarty;
    }

}
