<?php

/* -----------------------------------------------------------------
 * 	$Id: class.create_account.php 816 2014-01-28 07:07:08Z akausch $
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

class create_account_ORIGINAL {

    function create_account_ORIGINAL() {
        
    }

    function create_account_smarty($site) {
        require_once (DIR_FS_INC . 'xtc_get_country_list.inc.php');
        if ($site == 'login') {
            $create_account_smarty['FORM_ACTION'] = xtc_draw_form('create_account', xtc_href_link(FILENAME_LOGIN, '', 'SSL'), 'post', 'autocomplete="off"') . xtc_draw_hidden_field('action', 'process_create_account');
        } elseif ($site == 'checkout') {
            $create_account_smarty['FORM_ACTION'] = xtc_draw_form('create_account', xtc_href_link(FILENAME_CHECKOUT, '', 'SSL'), 'post', 'autocomplete="off"') . xtc_draw_hidden_field('action', 'process_create_account');
        } elseif ($site == 'create_account') {
            $create_account_smarty['FORM_ACTION'] = xtc_draw_form('create_account', xtc_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'), 'post', 'autocomplete="off"') . xtc_draw_hidden_field('action', 'process_create_account');
        } else {
            $create_account_smarty['FORM_ACTION'] = xtc_draw_form('create_account', xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'), 'post', 'autocomplete="off"') . xtc_draw_hidden_field('action', 'process_create_account');
        }

        if (ACCOUNT_GENDER == 'true') {
            $create_account_smarty['gender'] = '1';
            $create_account_smarty['INPUT_MALE'] = xtc_draw_radio_field(array('name' => 'gender', 'suffix' => MALE), 'm', xtc_db_prepare_input($_POST['gender']) == 'm' ? 'm' : '', 'id="gender-1"');
            $create_account_smarty['INPUT_FEMALE'] = xtc_draw_radio_field(array('name' => 'gender', 'suffix' => FEMALE, 'text' => (xtc_not_null(ENTRY_GENDER_TEXT) ? '<span class="inputRequirement">' . ENTRY_GENDER_TEXT . '</span>' : '')), 'f', xtc_db_prepare_input($_POST['gender']) == 'f' ? 'f' : '', 'id="gender-1"');
        } else {
            $create_account_smarty['gender'] = '0';
        }

        $create_account_smarty['INPUT_FIRSTNAME'] = xtc_draw_input_fieldNote(array('name' => 'firstname', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_FIRST_NAME_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['firstname']), 'size="29" class="create_account_firstname" required id="create_firstname"');
        $create_account_smarty['INPUT_LASTNAME'] = xtc_draw_input_fieldNote(array('name' => 'lastname', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['lastname']), 'size="29" class="create_account_lastname" required id="create_lastname"');

        if (ACCOUNT_DOB == 'true') {
            $create_account_smarty['birthdate'] = '1';
            if (ENTRY_DOB_MIN_LENGTH > 0) {
                $create_account_smarty['INPUT_DOB'] = xtc_draw_input_fieldNote(array('name' => 'dob', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_DATE_OF_BIRTH_TEXT) ? '<span class="inputRequirement">' . ENTRY_DATE_OF_BIRTH_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['dob']), 'size="29" required class="create_account_dob" id="create_dob"', 'text');
            } else {
                $create_account_smarty['INPUT_DOB'] = xtc_draw_input_fieldNote(array('name' => 'dob', 'text' => ''), '', 'size="29" required class="create_account_dob" id="create_dob"', 'text');
            }
        } else {
            $create_account_smarty['birthdate'] = '0';
        }
        $create_account_smarty['INPUT_EMAIL'] = xtc_draw_input_fieldNote(array('name' => 'email_address', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_EMAIL_ADDRESS_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['email_address']), 'size="29" required class="create_account_email" id="create_email"', 'email');

        if (ACCOUNT_COMPANY == 'true') {
            $create_account_smarty['company'] = '1';
            $create_account_smarty['INPUT_COMPANY'] = xtc_draw_input_fieldNote(array('name' => 'company', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_COMPANY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COMPANY_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['company']), 'size="29" class="create_account_company" id="create_company"');
        } else {
            $create_account_smarty['company'] = '0';
        }

        if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
            $create_account_smarty['vat'] = '1';
            $create_account_smarty['INPUT_VAT'] = xtc_draw_input_fieldNote(array('name' => 'vat', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_VAT_TEXT) ? '<span class="inputRequirement">' . ENTRY_VAT_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['vat']), 'size="29" class="create_account_vat" id="create_vat"');
        } else {
            $create_account_smarty['vat'] = '0';
        }
        $create_account_smarty['INPUT_STREET'] = xtc_draw_input_fieldNote(array('name' => 'street_address', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_STREET_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_STREET_ADDRESS_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['street_address']), 'size="21" required class="create_account_street" id="create_street_address"');
        $create_account_smarty['INPUT_STREET_NUM'] = xtc_draw_input_fieldNote(array('name' => 'street_address_num', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_STREET_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_STREET_ADDRESS_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['street_address_num']), 'size="3" class="create_account_street_num"');

        if (ACCOUNT_SUBURB == 'true') {
            $create_account_smarty['suburb'] = '1';
            $create_account_smarty['INPUT_SUBURB'] = xtc_draw_input_fieldNote(array('name' => 'suburb', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_SUBURB_TEXT) ? '<span class="inputRequirement">' . ENTRY_SUBURB_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['suburb']), 'size="29" class="create_account_suburb" id="create_suburb"');
        } else {
            $create_account_smarty['suburb'] = '0';
        }

        $create_account_smarty['INPUT_CODE'] = xtc_draw_input_fieldNote(array('name' => 'postcode', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_POST_CODE_TEXT) ? '<span class="inputRequirement">' . ENTRY_POST_CODE_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['postcode']), 'size="5" required class="create_account_postcode" id="create_postcode"');
        $create_account_smarty['INPUT_CITY'] = xtc_draw_input_fieldNote(array('name' => 'city', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_CITY_TEXT) ? '<span class="inputRequirement">' . ENTRY_CITY_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['city']), 'size="19" required class="create_account_city" id="create_city"');

        if (isset($_POST['country'])) {
            $selected = $_POST['country'];
        } elseif (isset($_SESSION['country'])) {
            $selected = $_SESSION['country'];
        } else {
            $selected = STORE_COUNTRY;
        }

		if (ACCOUNT_STATE == 'true') {
			$create_account_smarty['state'] = '1';
			$zones_array = array();
			$zones_query = xtc_db_query("SELECT zone_id, zone_name FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . (int)$selected . "' ORDER BY zone_name");
			while ($zones_values = xtc_db_fetch_array($zones_query)) {
				$zones_array[] = array('id' => $zones_values['zone_id'], 'text' => $zones_values['zone_name']);
			}
			$state_input = xtc_draw_pull_down_menuNote(array('name' => 'state', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">' . ENTRY_STATE_TEXT . '</span>' : '')), $zones_array, xtc_db_prepare_input($_POST['state']), ' class="create_account_state" id="create_state"');
			$create_account_smarty['INPUT_STATE'] = $state_input;
		} else {
			$create_account_smarty['state'] = '0';
		}
		
        $counrty_count_query = xtc_db_query("SELECT countries_id, status FROM " . TABLE_COUNTRIES . " WHERE status = '1'");
        $counrty_count = xtc_db_num_rows($counrty_count_query);
        if ($counrty_count > 1) {
            $create_account_smarty['SELECT_COUNTRY'] = xtc_get_country_list(array('name' => 'country', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COUNTRY_TEXT . '</span>' : '')), $selected, 'id="country" class="create_account_country"');
            $create_account_smarty['country'] = '1';
        } else {
            $create_account_smarty['country'] = '0';
            $create_account_smarty['SELECT_COUNTRY_ENABLE'] = 'false';
            $create_account_smarty['SELECT_COUNTRY'] = xtc_draw_hidden_field('country', $selected);
        }
        if (ACCOUNT_TELEFON == 'true') {
            $create_account_smarty['INPUT_TEL'] = xtc_draw_input_fieldNote(array('name' => 'telephone', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_TELEPHONE_NUMBER_TEXT) ? '<span class="inputRequirement">' . ENTRY_TELEPHONE_NUMBER_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['telephone']), 'size="29" required class="telephone" id="create_telephone"');
        }
        if (ACCOUNT_FAX == 'true') {
            $create_account_smarty['INPUT_FAX'] = xtc_draw_input_fieldNote(array('name' => 'fax', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_FAX_NUMBER_TEXT) ? '<span>' . ENTRY_FAX_NUMBER_TEXT . '</span>' : '')), xtc_db_prepare_input($_POST['fax']), 'size="29" id="create_fax"');
        }
        $create_account_smarty['INPUT_PASSWORD'] = xtc_draw_password_fieldNote(array('name' => 'password', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_PASSWORD_TEXT) ? '<span class="inputRequirement">' . ENTRY_PASSWORD_TEXT . '</span>' : '')), '', 'size="29" id="create_password" onkeyup="passwordStrength(this.value)"');
        $create_account_smarty['INPUT_CONFIRMATION'] = xtc_draw_password_fieldNote(array('name' => 'confirmation', 'text' => '&nbsp;' . (xtc_not_null(ENTRY_PASSWORD_CONFIRMATION_TEXT) ? '<span class="inputRequirement">' . ENTRY_PASSWORD_CONFIRMATION_TEXT . '</span>' : '')), '', 'size="29" class="password" id="create_confirmation"');
        $create_account_smarty['INPUT_NEWSLETTER'] = xtc_draw_checkbox_field('newsletter', '1', false, 'id="create_newsletter"');
        $create_account_smarty['CHECKBOX_NEWSLETTER'] = xtc_draw_checkbox_field('newsletter', '1', false, 'id="create_newsletter"');

        if (TRUSTED_SHOP_CREATE_ACCOUNT_DS == 'true') {
            $shop_content_query = xtc_db_query("SELECT content_text, content_file FROM " . TABLE_CONTENT_MANAGER . " WHERE content_group = '2' AND languages_id='" . (int) $_SESSION['languages_id'] . "'");
            $shop_content_data = xtc_db_fetch_array($shop_content_query);
            if ($shop_content_data['content_file'] != '') {
                if ($shop_content_data['content_file'] == 'janolaw_datenschutz.php') {
                    include (DIR_FS_INC . 'janolaw.inc.php');
                    $datensg = JanolawContent('datenschutzerklaerung', 'txt');
                } elseif ($shop_content_data['content_file'] == 'protected_shops_datenschutz.php') {
                    $datensg = '<div class="agbframe">' . file_get_contents(DIR_FS_DOCUMENT_ROOT . 'media/content/ps_datenschutz.html') . '</div>';
				} else {
                    $datensg = '<div class="agbframe">' . file_get_contents(DIR_FS_DOCUMENT_ROOT . 'media/content/' . $shop_content_data['content_file']) . '</div>';
                }
            } else {
                $datensg = '<div class="agbframe">' . $shop_content_data['content_text'] . '</div>';
            }
            $create_account_smarty['DSG'] = $datensg;
            $create_account_smarty['BUTTON_PRINT'] = '<a style="cursor:pointer" onclick="javascript:window.open(\'' . xtc_href_link(FILENAME_PRINT_CONTENT, 'coID=2') . '\', \'popup\', \'toolbar=0, width=640, height=600\')">' . PRINT_CONTENT . '</a>';
            $create_account_smarty['DATENSG_CHECKBOX'] = '<input id="create_dsg" type="checkbox" value="datensg" name="datensg" />';
        } else {
            $create_account_smarty['TRUSTED_DSG'] = 'false';
        }
        $create_account_smarty['BUTTON_SUBMIT'] = xtc_image_submit('button_send.gif', IMAGE_BUTTON_CONTINUE);
        $create_account_smarty['FORM_END'] = '</form>';

        if (isset($_POST['action']) && ($_POST['action'] == 'process_create_account')) {
            $check_create_account = $this->check_create_account($site);
            $create_account_smarty['error'] = $check_create_account;
        }

        return $create_account_smarty;
    }

    function check_create_account($site) {
        $messageStack = new messageStack;
        require_once (DIR_FS_INC . 'xtc_write_user_info.inc.php');
        require_once (DIR_FS_INC . 'xtc_get_country_list.inc.php');
        require_once (DIR_FS_INC . 'xtc_validate_email.inc.php');
        require_once (DIR_FS_INC . 'xtc_encrypt_password.inc.php');

        $process = false;
        if (isset($_POST['action']) && ($_POST['action'] == 'process_create_account')) {
            $process = true;
            $smarty = new Smarty;

            if (ACCOUNT_GENDER == 'true') {
                $gender = xtc_db_prepare_input($_POST['gender']);
            }
            if (ACCOUNT_DOB == 'true') {
                $dob = xtc_db_prepare_input($_POST['dob']);
            }
            if (ACCOUNT_COMPANY == 'true') {
                $company = xtc_db_prepare_input($_POST['company']);
            }
            if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
                $vat = xtc_db_prepare_input($_POST['vat']);
            }
            if (ACCOUNT_SUBURB == 'true') {
                $suburb = xtc_db_prepare_input($_POST['suburb']);
            }
            if (ACCOUNT_STATE == 'true') {
                $state = xtc_db_prepare_input($_POST['state']);
            }
            if (TRUSTED_SHOP_CREATE_ACCOUNT_DS == 'true') {
                $datensg = xtc_db_prepare_input($_POST['datensg']);
            }
            if (ACCOUNT_TELEFON == 'true') {
                $telephone = xtc_db_prepare_input($_POST['telephone']);
            }
            if (ACCOUNT_FAX == 'true') {
                $fax = xtc_db_prepare_input($_POST['fax']);
            }
            $firstname = xtc_db_prepare_input($_POST['firstname']);
            $lastname = xtc_db_prepare_input($_POST['lastname']);
            $email_address = xtc_db_prepare_input($_POST['email_address']);
            $street_address = xtc_db_prepare_input($_POST['street_address']);
            $street_address_num = xtc_db_prepare_input($_POST['street_address_num']);
            $postcode = xtc_db_prepare_input($_POST['postcode']);
            $city = xtc_db_prepare_input($_POST['city']);
            $zone_id = xtc_db_prepare_input($_POST['zone_id']);
            $country = xtc_db_prepare_input($_POST['country']);
            $newsletter = xtc_db_prepare_input($_POST['newsletter']);

            if ($site != 'create_guest_account') {
                $password = xtc_db_prepare_input($_POST['password']);
                $confirmation = xtc_db_prepare_input($_POST['confirmation']);
            }

            $error = false;

            if (ACCOUNT_GENDER == 'true') {
                if (($gender != 'm') && ($gender != 'f')) {
                    $error = true;
                    $messageStack->add('create_account', ENTRY_GENDER_ERROR);
                }
            }
            if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
                $error = true;
                $messageStack->add('create_account', ENTRY_FIRST_NAME_ERROR);
            }

            if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
                $error = true;
                $messageStack->add('create_account', ENTRY_LAST_NAME_ERROR);
            }

            if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
                require_once(DIR_WS_CLASSES . 'class.vat_validation.php');
                $vatID = new vat_validation($vat, '', '', $country);

                $customers_status = $vatID->vat_info['status'];
                $customers_vat_id_status = $vatID->vat_info['vat_id_status'];
                $error = $vatID->vat_info['error'];

                if ($error == 1) {
                    $error = true;
                    $messageStack->add('create_account', ENTRY_VAT_ERROR);
                }
            }

            if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
                $error = true;
                $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR);
            } elseif (xtc_validate_email($email_address) == false) {
                $error = true;
                $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
            } else {
                $check_email_query = xtc_db_query("select count(*) as total from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($email_address) . "'");
                $check_email = xtc_db_fetch_array($check_email_query);
                if ($check_email['total'] > 0) {
                    $error = true;
                    $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);
                }
            }

            if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
                $error = true;
                $messageStack->add('create_account', ENTRY_STREET_ADDRESS_ERROR);
            }

            if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
                $error = true;
                $messageStack->add('create_account', ENTRY_POST_CODE_ERROR);
            }

            if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
                $error = true;
                $messageStack->add('create_account', ENTRY_CITY_ERROR);
            }

            if (is_numeric($country) == false) {
                $error = true;
                $messageStack->add('create_account', ENTRY_COUNTRY_ERROR);
            }

            if (ACCOUNT_STATE == 'true') {
                $zone_id = 0;
                $check = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(*) AS total FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . (int) $country . "';"));
                $entry_state_has_zones = ($check['total'] > 0);
                if ($entry_state_has_zones == true) {
                    $zone_query = xtc_db_query("SELECT zone_id, zone_name FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . (int) $country . "' AND zone_id = '" . (int) $state . "';");
                    if (xtc_db_num_rows($zone_query) >= 1) {
                        $zone = xtc_db_fetch_array($zone_query);
                        $zone_id = $zone['zone_id'];
                    } else {
                        $error = true;
                        $messageStack->add('create_account', ENTRY_STATE_ERROR_SELECT);
                    }
                } else {
                    if (strlen($state) < ENTRY_STATE_MIN_LENGTH) {
                        $error = true;
                        $messageStack->add('create_account', ENTRY_STATE_ERROR);
                    }
                }
            }

            if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH && ACCOUNT_TELEFON == 'true') {
                $error = true;
                $messageStack->add('create_account', ENTRY_TELEPHONE_NUMBER_ERROR);
            }

            if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH && $site != 'create_guest_account') {
                $error = true;
                $messageStack->add('create_account', ENTRY_PASSWORD_ERROR);
            } elseif ($password != $confirmation && $site != 'create_guest_account') {
                $error = true;
                $messageStack->add('create_account', ENTRY_PASSWORD_ERROR_NOT_MATCHING);
            }
            if (TRUSTED_SHOP_CREATE_ACCOUNT_DS == 'true') {
                if (!isset($datensg) || empty($datensg)) {
                    $error = true;
                    $messageStack->add('create_account', ERROR_DATENSG_NOT_ACCEPTED);
                }
            }
            if (ACCOUNT_DOB == 'true') {
                $date_kunde = substr(xtc_date_raw($dob), 0, 4);
                $date_aktuell = date('Y');
                $min_age = (int) ACCOUNT_MIN_AGE;
                if (ENTRY_DOB_MIN_LENGTH > 0 && $dob != '') {
                    if (checkdate(substr(xtc_date_raw($dob), 4, 2), substr(xtc_date_raw($dob), 6, 2), substr(xtc_date_raw($dob), 0, 4)) == false) {
                        $error = true;
                        $messageStack->add('create_account', ENTRY_DATE_OF_BIRTH_ERROR);
                    } elseif ($date_aktuell - $date_kunde < 0) {
						$error = true;
                        $messageStack->add('create_account', ENTRY_DATE_OF_BIRTH_ERROR);
                    } elseif ($date_aktuell - $date_kunde < $min_age && ACCOUNT_AGE_VERIFICATION == 'true') {
                        $error = true;
                        $messageStack->add('create_account', ENTRY_DATE_OF_BIRTH_OLD);
                    }
                } elseif (ENTRY_DOB_MIN_LENGTH > 0 && $dob == '') {
                    $error = true;
                    $messageStack->add('create_account', ENTRY_DATE_OF_BIRTH_ERROR);
                }
            }
            if ($site == 'create_guest_account') {
                if (ACCOUNT_COMPANY_VAT_CHECK == 'true' && $vatID->vat_info['error'] == '' && $vatID->vat_info['vat_id_status'] == '1') {
                    if ($customers_status != 0 || $customers_status) {
						$customers_status = $customers_status;
					} else {
						$customers_status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
					}
                } else {
                    $customers_status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
                }
                $account_type = '1';
                $_SESSION['account_type'] = '1';
            }

            if ($customers_status == 0 || !$customers_status) {
                $customers_status = DEFAULT_CUSTOMERS_STATUS_ID;
                $account_type = '0';
            }
            if ($site == 'create_guest_account') {
                require_once (DIR_FS_INC . 'xtc_create_password.inc.php');
                $password = xtc_create_password(8);
            }

            if ($error == false) {
                $sql_data_array = array('customers_vat_id' => $vat,
                    'customers_vat_id_status' => ($customers_vat_id_status != '' ? $customers_vat_id_status : '0'),
                    'customers_status' => $customers_status,
                    'customers_firstname' => $firstname,
                    'customers_lastname' => $lastname,
                    'customers_email_address' => $email_address,
                    'customers_telephone' => $telephone,
                    'customers_fax' => $fax,
                    'customers_password' => xtc_encrypt_password($password),
                    'customers_newsletter' => $newsletter,
                    'account_type' => $account_type,
                    'customers_date_added' => 'now()',
                    'customers_last_modified' => 'now()',
                    'datensg' => 'now()');

                if (ACCOUNT_GENDER == 'true') {
                    $sql_data_array['customers_gender'] = $gender;
                }
                if (ACCOUNT_DOB == 'true') {
                    $sql_data_array['customers_dob'] = xtc_date_raw($dob);
                }

                xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array);

                $_SESSION['customer_id'] = xtc_db_insert_id();
                $user_id = xtc_db_insert_id();
                xtc_write_user_info($user_id);

                if (CUSTOMER_CID_FORM == 'date') {
                    $new_cid = '';
                    $day = date("d");
                    $mon = date("m");
                    $year = date("y");
                    $ccid = $day . $mon . $year . '-' . ($_SESSION['customer_id'] + 1000);
                } elseif (CUSTOMER_CID_FORM == 'custom' && CUSTOMER_CID_FORM_CUSTOM != '') {
                    $ccid = CUSTOMER_CID_FORM_CUSTOM . '-' . $_SESSION['customer_id'];
                } else {
                    $ccid = $_SESSION['customer_id'];
                }

                $sql_data_array = array('customers_id' => $_SESSION['customer_id'],
                    'entry_firstname' => $firstname,
                    'entry_lastname' => $lastname,
                    'entry_street_address' => $street_address . ' ' . $street_address_num,
                    'entry_postcode' => $postcode,
                    'entry_city' => $city,
                    'entry_country_id' => $country,
                    'address_date_added' => 'now()',
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
                        $sql_data_array['entry_zone_id'] = $zone_id;
                        $sql_data_array['entry_state'] = '';
                    } else {
                        $sql_data_array['entry_zone_id'] = '0';
                        $sql_data_array['entry_state'] = $zone['zone_name'];
                    }
                }

                xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
                $address_id = xtc_db_insert_id();
                xtc_db_query("UPDATE " . TABLE_CUSTOMERS . " SET customers_default_address_id = '" . $address_id . "' WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "'");
                xtc_db_query("UPDATE " . TABLE_CUSTOMERS . " SET customers_cid = '" . $ccid . "' WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "'");
                xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) VALUES ('" . (int) $_SESSION['customer_id'] . "', '0', now())");

                if (SESSION_RECREATE == 'true') {
                    xtc_session_recreate();
                }

                if ($newsletter == '1') {

                    require_once (DIR_FS_INC . 'xtc_random_charcode.inc.php');
                    $vlcode = xtc_random_charcode(32);

                    $sql_newletter_array = array('customers_email_address' => $email_address,
                        'customers_id' => (int) $_SESSION['customer_id'],
                        'customers_status' => $customers_status,
                        'customers_firstname' => $firstname,
                        'customers_lastname' => $lastname,
                        'mail_status' => '0',
                        'mail_key' => $vlcode,
                        'date_added' => 'now()');

                    xtc_db_perform(TABLE_NEWSLETTER_RECIPIENTS, $sql_newletter_array);

                    $link = xtc_href_link(FILENAME_NEWSLETTER, 'action=activate&email=' . $email_address . '&key=' . $vlcode, 'SSL');

                    $smarty->assign('EMAIL', xtc_db_input($_POST['email']));
                    $smarty->assign('LINK', $link);

                    $smarty->assign('language', $_SESSION['language']);
                    $smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
                    $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

                    $smarty->caching = false;
                    require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
                    $html_mail = $smarty->fetch('html:newsletter_aktivierung');
                    $html_mail .= $signatur_html;
                    $txt_mail = $smarty->fetch('txt:newsletter_aktivierung');
                    $txt_mail .= $signatur_text;
                    require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
                    $mail_data = cseo_get_mail_data('newsletter_aktivierung');

                    $newsletter_subject = str_replace('{$shop_besitzer}', STORE_OWNER, $mail_data['EMAIL_SUBJECT']);
                    $newsletter_subject = str_replace('{$shop_name}', STORE_NAME, $newsletter_subject);

                    $newsletter_from = str_replace('{$shop_name}', STORE_NAME, $mail_data['EMAIL_ADDRESS_NAME']);

                    if (SEND_EMAILS == true) {
                        xtc_php_mail($mail_data['EMAIL_ADDRESS'], $newsletter_from, xtc_db_input($email_address), $firstname . ' ' . $lastname, $mail_data['EMAIL_FORWARD'], $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], '', '', $newsletter_subject, $html_mail, $txt_mail);
                    }
                } else {
                    $newsletter = '0';
                }

                $_SESSION['customer_first_name'] = $firstname;
                $_SESSION['customer_last_name'] = $lastname;
                $_SESSION['customer_default_address_id'] = $address_id;
                $_SESSION['customer_country_id'] = $country;
                $_SESSION['customer_zone_id'] = $zone_id;
                $_SESSION['customer_vat_id'] = $vat;

                $_SESSION['cart']->restore_contents();

                $smarty->assign('language', $_SESSION['language']);
                if (ACCOUNT_GENDER == 'true') {
                    $smarty->assign('GENDER', $gender);
                }
                require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
                $mail_data = cseo_get_mail_data('create_account');
                $smarty->assign('MAIL_REPLY_ADDRESS', $mail_data['EMAIL_ADDRESS']);
                $smarty->assign('VNAME', $_SESSION['customer_first_name']);
                $smarty->assign('NNAME', $_SESSION['customer_last_name']);
                $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

                $smarty->assign('content', $module_content);
                $smarty->caching = false;
                if (TRUSTED_SHOP_PASSWORD_EMAIL == 'true') {
                    $smarty->assign('USERNAME4MAIL', $email_address);
                    $smarty->assign('PASSWORT4MAIL', $password);
                }

                if (isset($_SESSION['tracking']['refID'])) {
                    $campaign_check_query_raw = "SELECT * FROM " . TABLE_CAMPAIGNS . " WHERE campaigns_refID = '" . $_SESSION[tracking][refID] . "'";
                    $campaign_check_query = xtc_db_query($campaign_check_query_raw);
                    if (xtc_db_num_rows($campaign_check_query) > 0) {
                        $campaign = xtc_db_fetch_array($campaign_check_query);
                        $refID = $campaign['campaigns_id'];
                    } else
                        $refID = 0;

                    xtc_db_query("UPDATE " . TABLE_CUSTOMERS . " SET refferers_id = '" . $refID . "' WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "'");

                    $leads = $campaign['campaigns_leads'] + 1;
                    xtc_db_query("UPDATE " . TABLE_CAMPAIGNS . " SET campaigns_leads = '" . $leads . "' WHERE campaigns_id = '" . $refID . "'");
                }

                if (ACTIVATE_GIFT_SYSTEM == 'true') {
                    if (NEW_SIGNUP_GIFT_VOUCHER_AMOUNT > 0) {
                        $coupon_code = create_coupon_code();
                        $insert_query = xtc_db_query("insert into " . TABLE_COUPONS . " (coupon_code, coupon_type, coupon_amount, date_created) values ('" . $coupon_code . "', 'G', '" . NEW_SIGNUP_GIFT_VOUCHER_AMOUNT . "', now())");
                        $insert_id = xtc_db_insert_id($insert_query);
                        $insert_query = xtc_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $insert_id . "', '0', 'Admin', '" . $email_address . "', now() )");
                        $xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
                        $smarty->assign('SEND_GIFT', 'true');
                        $smarty->assign('GIFT_AMMOUNT', $xtPrice->xtcFormat(NEW_SIGNUP_GIFT_VOUCHER_AMOUNT, true));
                        $smarty->assign('GIFT_CODE', $coupon_code);
                        $smarty->assign('GIFT_LINK', xtc_href_link(FILENAME_GV_REDEEM, 'gv_no=' . $coupon_code, 'NONSSL', false));
                    }
                    if (NEW_SIGNUP_DISCOUNT_COUPON != '') {
                        $coupon_code = NEW_SIGNUP_DISCOUNT_COUPON;
                        $coupon_query = xtc_db_query("select * from " . TABLE_COUPONS . " where coupon_code = '" . $coupon_code . "'");
                        $coupon = xtc_db_fetch_array($coupon_query);
                        $coupon_id = $coupon['coupon_id'];
                        $coupon_desc_query = xtc_db_query("select * from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $coupon_id . "' and language_id = '" . (int) $_SESSION['languages_id'] . "'");
                        $coupon_desc = xtc_db_fetch_array($coupon_desc_query);
                        $insert_query = xtc_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $coupon_id . "', '0', 'Admin', '" . $email_address . "', now() )");

                        $smarty->assign('SEND_COUPON', 'true');
                        $smarty->assign('COUPON_DESC', $coupon_desc['coupon_description']);
                        $smarty->assign('COUPON_CODE', $coupon['coupon_code']);
                    }
                }
            }

            if ($error == false) {
                if ($site != 'create_guest_account') {
                    require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
                    $smarty->caching = false;
                    $html_mail = $smarty->fetch('html:create_account');
                    $html_mail .= $signatur_html;
                    $smarty->caching = false;
                    $txt_mail = $smarty->fetch('txt:create_account');
                    $txt_mail .= $signatur_text;
                    require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
                    $mail_data = cseo_get_mail_data('create_account');

                    xtc_php_mail($mail_data['EMAIL_ADDRESS'], $mail_data['EMAIL_ADDRESS_NAME'], $email_address, $name, $mail_data['EMAIL_FORWARD'], $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], '', '', $mail_data['EMAIL_SUBJECT'], $html_mail, $txt_mail);
                }
                if (!isset($mail_error)) {
                    if ($_SESSION['cart']->count_contents() > 0) {
                        xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
                    } else {
                        xtc_redirect(xtc_href_link(FILENAME_DEFAULT, '', 'SSL'));
                    }
                } else {
                    echo $mail_error;
                }
            }
        }

        if ($messageStack->size('create_account') > 0) {
            return $messageStack->output('create_account');
        }
    }

}
