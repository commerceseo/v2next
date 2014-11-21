<?php

/* -----------------------------------------------------------------
 * 	$Id: create_account.php 1268 2014-11-19 07:00:44Z akausch $
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

require_once(DIR_FS_INC . 'cseo_db.inc.php');
require_once(DIR_FS_INC . 'xtc_rand.inc.php');
require_once(DIR_FS_INC . 'xtc_encrypt_password.inc.php');
require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');
require_once(DIR_FS_INC . 'xtc_redirect.inc.php');
require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
require_once(DIR_FS_INC . 'cseo_form.inc.php');
require_once(DIR_FS_INC . 'xtc_get_country_list.inc.php');
require_once(DIR_FS_INC . 'strlen_wrapper.inc.php');

// connect do database
xtc_db_connect() or die('Unable to connect to database server!');

// get configuration data
$configuration_query = xtc_db_query('SELECT configuration_key as cfgKey, configuration_value AS cfgValue FROM ' . TABLE_CONFIGURATION);
while ($configuration = xtc_db_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
}

$process = false;
if (isset($_POST['action']) && ($_POST['action'] == 'create_account')) {
    $process = true;

    $gender = xtc_db_prepare_input($_POST['GENDER']);
    $firstname = addslashes(xtc_db_prepare_input($_POST['FIRST_NAME']));
    $lastname = addslashes(xtc_db_prepare_input($_POST['LAST_NAME']));
    $email_address = xtc_db_prepare_input($_POST['EMAIL_ADRESS']);
    $street_address = addslashes(xtc_db_prepare_input($_POST['STREET_ADRESS']));
    $postcode = xtc_db_prepare_input($_POST['POST_CODE']);
    $city = addslashes(xtc_db_prepare_input($_POST['CITY']));
    $zone_id = xtc_db_prepare_input($_POST['zone_id']);
    $state = xtc_db_prepare_input($_POST['STATE']);
    $country = xtc_db_prepare_input($_POST['COUNTRY']);
    $telephone = xtc_db_prepare_input($_POST['TELEPHONE']);
    $password = xtc_db_prepare_input($_POST['PASSWORD']);
    $confirmation = xtc_db_prepare_input($_POST['PASSWORD_CONFIRMATION']);
    $store_name = addslashes(xtc_db_prepare_input($_POST['STORE_NAME']));
    $email_from = xtc_db_prepare_input($_POST['EMAIL_ADRESS_FROM']);
    $zone_setup = xtc_db_prepare_input($_POST['ZONE_SETUP']);
    $company = addslashes(xtc_db_prepare_input($_POST['COMPANY']));
    $ustid = addslashes(xtc_db_prepare_input($_POST['USTID']));

    $error = false;


    if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
        $error = true;
    }

    if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
        $error = true;
    }

    if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
        $error = true;
    } elseif (xtc_validate_email($email_address) == false) {
        $error = true;
    }

    if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
        $error = true;
    }

    if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
        $error = true;
    }

    if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
        $error = true;
    }

    if (is_numeric($country) == false) {
        $error = true;
    }

    $zone_id = 0;
    $check_query = xtc_db_query("SELECT count(*) AS total FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . (int) $country . "'");
    $check = xtc_db_fetch_array($check_query);
    $entry_state_has_zones = ($check['total'] > 0);
    if ($entry_state_has_zones == true) {
        $zone_query = xtc_db_query("SELECT DISTINCT zone_id FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . (int) $country . "' AND (zone_name LIKE '" . xtc_db_input($state) . "%' OR zone_code LIKE '%" . xtc_db_input($state) . "%')");
        if (xtc_db_num_rows($zone_query) > 0) {
            $zone = xtc_db_fetch_array($zone_query);
            $zone_id = $zone['zone_id'];
        } else {
            echo 'Error Zone';
            $error = true;
        }
    } else {
        if (strlen($state) < ENTRY_STATE_MIN_LENGTH) {
            echo 'Error State';
            $error = true;
        }
    }

    if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
        $error_code = "Telefon zu kurz";
		$error = true;
    }


    if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) {
        $error_code = "Passwort zu kurz";
		$error = true;
    } elseif ($password != $confirmation) {
		$error_code = "Passwort falsch";
        $error = true;
    }

    if (strlen($store_name) < '3') {
		$error_code = "Store Name zu kurz";
        $error = true;
    }
    if (strlen($company) < '2') {
		$error_code = "Company Name zu kurz";
        $error = true;
    }

    if (strlen($email_from) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
		$error_code = "E-Mail From zu kurz";
        $error = true;
    } elseif (xtc_validate_email($email_from) == false) {
		$error_code = "E-Mail From falsch";
        $error = true;
    }
    if (($zone_setup != 'yes') && ($zone_setup != 'no')) {
        echo 'Error Zone Setup';
        $error = true;
    }


    if ($error == false) {
        xtc_db_query("insert into " . TABLE_CUSTOMERS . " (
										customers_id,
										customers_cid,
										customers_vat_id,
										customers_status,
										customers_firstname,
										customers_lastname,
										customers_gender,
										customers_email_address,
										customers_default_address_id,
										customers_telephone,
										customers_password,
										delete_user) VALUES
										('1',
										'1',
										'" . $ustid . "',
										'0',
										'" . $firstname . "',
										'" . $lastname . "',
										'" . $gender . "',
										'" . $email_address . "',
										'1',
										'" . $telephone . "',
										'" . xtc_encrypt_password($password) . "',
										'0')");

        xtc_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (
										customers_info_id,
										customers_info_date_of_last_logon,
										customers_info_number_of_logons,
										customers_info_date_account_created,
										customers_info_date_account_last_modified,
										global_product_notifications) VALUES
										('1','','','','','')");
        xtc_db_query("insert into " . TABLE_ADDRESS_BOOK . " (
										customers_id,
										entry_company,
										entry_gender,
   										entry_firstname,
   										entry_lastname,
   										entry_street_address,
   										entry_postcode,
   										entry_city,
   										entry_state,
   										entry_country_id,
   										entry_zone_id) VALUES
										('1',
										'" . ($company) . "',
										'" . ($gender) . "',
										'" . ($firstname) . "',
										'" . ($lastname) . "',
										'" . ($street_address) . "',
										'" . ($postcode) . "',
										'" . ($city) . "',
										'" . ($state) . "',
										'" . ($country) . "',
										'" . ($zone_id) . "'
										)");


        xtc_db_query("UPDATE configuration SET configuration_value = '" . cseo_local_install_path() . "cache' WHERE configuration_key = 'SESSION_WRITE_DIRECTORY'");
        xtc_db_query("UPDATE configuration SET configuration_value = '" . cseo_local_install_path() . "admin/backups/page_parse_time.log' WHERE configuration_key = 'STORE_PAGE_PARSE_TIME_LOG'");

        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($ustid) . "' WHERE configuration_key = 'STORE_OWNER_VAT_ID'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($email_address) . "' WHERE configuration_key = 'STORE_OWNER_EMAIL_ADDRESS'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($store_name) . "' WHERE configuration_key = 'STORE_NAME'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($email_from) . "' WHERE configuration_key = 'EMAIL_FROM'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($country) . "' WHERE configuration_key = 'SHIPPING_ORIGIN_COUNTRY'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($zone_id) . "' WHERE configuration_key = 'STORE_ZONE'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($postcode) . "' WHERE configuration_key = 'SHIPPING_ORIGIN_ZIP'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($company) . "' WHERE configuration_key = 'STORE_OWNER'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($ustid) . "' WHERE configuration_key = 'STORE_OWNER_VAT_ID'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($email_from) . "' WHERE configuration_key = 'EMAIL_BILLING_FORWARDING_STRING'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($email_from) . "' WHERE configuration_key = 'EMAIL_BILLING_ADDRESS'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($email_from) . "' WHERE configuration_key = 'CONTACT_US_EMAIL_ADDRESS'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($email_from) . "' WHERE configuration_key = 'EMAIL_SUPPORT_ADDRESS'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($firstname) . "' WHERE configuration_key = 'TRADER_FIRSTNAME'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($lastname) . "' WHERE configuration_key = 'TRADER_NAME'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($street_address) . "' WHERE configuration_key = 'TRADER_STREET'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($postcode) . "' WHERE configuration_key = 'TRADER_ZIPCODE'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($city) . "' WHERE configuration_key = 'TRADER_LOCATION'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . ($company . '\n' . $firstname . ' ' . $lastname . '\n' . $street_address . '\n' . $postcode . ' ' . $city) . "' WHERE configuration_key = 'STORE_NAME_ADDRESS'");
        xtc_db_query("UPDATE " . TABLE_COUNTRIES . " SET status='0' WHERE countries_id != '" . ($country) . " '");

        xtc_db_query("UPDATE emails SET email_address = '" . $email_from . "', email_replay_address = '" . $email_from . "' ");
        xtc_db_query("UPDATE emails SET email_address_name = '" . $store_name . "', email_replay_address_name = '" . $store_name . "' ");
        xtc_db_query("UPDATE configuration SET configuration_value = '" . $email_from . "' WHERE configuration_key = 'META_REPLY_TO' ");

        if ($zone_setup == 'yes') {

            // Steuersätze des jewiligen landes einstellen!
            $tax_normal = '';
            $tax_normal_text = '';
            $tax_special = '';
            $tax_special_text = '';
            switch ($country) {

                case '14':
                    // Austria
                    $tax_normal = '20.0000';
                    $tax_normal_text = '20% MwSt.';
                    $tax_special = '10.0000';
                    $tax_special_text = '10% MwSt.';
                    break;
                case '21':
                    // Belgien
                    $tax_normal = '21.0000';
                    $tax_normal_text = 'UST 21%';
                    $tax_special = '6.0000';
                    $tax_special_text = 'UST 6%';
                    break;
                case '57':
                    // Dänemark
                    $tax_normal = '25.0000';
                    $tax_normal_text = 'UST 25%';
                    $tax_special = '25.0000';
                    $tax_special_text = 'UST 25%';
                    break;
                case '72':
                    // Finnland
                    $tax_normal = '22.0000';
                    $tax_normal_text = 'UST 22%';
                    $tax_special = '8.0000';
                    $tax_special_text = 'UST 8%';
                    break;
                case '73':
                    // Frankreich
                    $tax_normal = '19.6000';
                    $tax_normal_text = 'UST 19.6%';
                    $tax_special = '2.1000';
                    $tax_special_text = 'UST 2.1%';
                    break;
                case '81':
                    // Deutschland
                    $tax_normal = '19.0000';
                    $tax_normal_text = '19% MwSt.';
                    $tax_special = '7.0000';
                    $tax_special_text = '7% MwSt.';
                    break;
                case '84':
                    // Griechenland
                    $tax_normal = '18.0000';
                    $tax_normal_text = 'UST 18%';
                    $tax_special = '4.0000';
                    $tax_special_text = 'UST 4%';
                    break;
                case '103':
                    // Irland
                    $tax_normal = '21.0000';
                    $tax_normal_text = 'UST 21%';
                    $tax_special = '4.2000';
                    $tax_special_text = 'UST 4.2%';
                    break;
                case '105':
                    // Italien
                    $tax_normal = '20.0000';
                    $tax_normal_text = 'UST 20%';
                    $tax_special = '4.0000';
                    $tax_special_text = 'UST 4%';
                    break;
                case '124':
                    // Luxemburg
                    $tax_normal = '15.0000';
                    $tax_normal_text = 'UST 15%';
                    $tax_special = '3.0000';
                    $tax_special_text = 'UST 3%';
                    break;
                case '150':
                    // Niederlande
                    $tax_normal = '19.0000';
                    $tax_normal_text = 'UST 19%';
                    $tax_special = '6.0000';
                    $tax_special_text = 'UST 6%';
                    break;
                case '171':
                    // Portugal
                    $tax_normal = '17.0000';
                    $tax_normal_text = 'UST 17%';
                    $tax_special = '5.0000';
                    $tax_special_text = 'UST 5%';
                    break;
                case '195':
                    // Spain
                    $tax_normal = '16.0000';
                    $tax_normal_text = 'UST 16%';
                    $tax_special = '4.0000';
                    $tax_special_text = 'UST 4%';
                    break;
                case '203':
                    // Schweden
                    $tax_normal = '25.0000';
                    $tax_normal_text = 'UST 25%';
                    $tax_special = '6.0000';
                    $tax_special_text = 'UST 6%';
                    break;
                case '222':
                    // UK
                    $tax_normal = '17.5000';
                    $tax_normal_text = 'UST 17.5%';
                    $tax_special = '5.0000';
                    $tax_special_text = 'UST 5%';
                    break;
                case '204':
                // Switzerland
                case '122':
                    // Liechtenstein
                    $tax_normal = '8.0000';
                    $tax_normal_text = '8% MwSt.';
                    $tax_special = '2.5000';
                    $tax_special_text = '2,5% MwSt.';
                    break;
            }

            if ($country == '204' || $country == '122') {
                // Steuersätze / tax_rates
                xtc_db_query("INSERT INTO tax_rates (tax_rates_id, tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, last_modified, date_added) VALUES (1, 5, 1, 1, '0.0000', '0% MwSt.', '', '')");
                xtc_db_query("INSERT INTO tax_rates (tax_rates_id, tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, last_modified, date_added) VALUES (2, 5, 2, 1, '0.0000', '0% MwSt.', '', '')");
                xtc_db_query("INSERT INTO tax_rates (tax_rates_id, tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, last_modified, date_added) VALUES (3, 6, 1, 1, '0.0000', 'EU-AUS-UST 0%', '', '')");
                xtc_db_query("INSERT INTO tax_rates (tax_rates_id, tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, last_modified, date_added) VALUES (4, 6, 2, 1, '0.0000', 'EU-AUS-UST 0%', '', '')");
                xtc_db_query("INSERT INTO tax_rates (tax_rates_id, tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, last_modified, date_added) VALUES (5, 8, 1, 1, '" . $tax_normal . "', '" . $tax_normal_text . "', '', '')");
                xtc_db_query("INSERT INTO tax_rates (tax_rates_id, tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, last_modified, date_added) VALUES (6, 8, 2, 1, '" . $tax_special . "', '" . $tax_special_text . "', '', '')");

                // Steuersätze
                xtc_db_query("INSERT INTO geo_zones (geo_zone_id, geo_zone_name, geo_zone_description, last_modified, date_added) VALUES (8, 'Schweiz & Lichtenstein', 'Steuerzone für Schweiz & Lichtenstein', '', now())");

                if ($country == '204') {
                    // do not display VAT info under product prices
                    xtc_db_query("UPDATE configuration SET configuration_value = 'false' WHERE configuration_key = 'DISPLAY_TAX'");
                }
            } else {
                // Steuersätze / tax_rates
                xtc_db_query("INSERT INTO tax_rates (tax_rates_id, tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, last_modified, date_added) VALUES (1, 5, 1, 1, '" . $tax_normal . "', '" . $tax_normal_text . "', '', '')");
                xtc_db_query("INSERT INTO tax_rates (tax_rates_id, tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, last_modified, date_added) VALUES (2, 5, 2, 1, '" . $tax_special . "', '" . $tax_special_text . "', '', '')");
                xtc_db_query("INSERT INTO tax_rates (tax_rates_id, tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, last_modified, date_added) VALUES (3, 6, 1, 1, '0.0000', 'EU-AUS-UST 0%', '', '')");
                xtc_db_query("INSERT INTO tax_rates (tax_rates_id, tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, last_modified, date_added) VALUES (4, 6, 2, 1, '0.0000', 'EU-AUS-UST 0%', '', '')");
            }

            // Steuerklassen
            xtc_db_query("INSERT INTO tax_class (tax_class_id, tax_class_title, tax_class_description, last_modified, date_added) VALUES (1, 'Standardsatz', '', '', now())");
            xtc_db_query("INSERT INTO tax_class (tax_class_id, tax_class_title, tax_class_description, last_modified, date_added) VALUES (2, 'ermäßigter Steuersatz', '', NULL, now())");

            // Steuersätze
            xtc_db_query("INSERT INTO geo_zones (geo_zone_id, geo_zone_name, geo_zone_description, last_modified, date_added) VALUES (6, 'Steuerzone EU-Ausland', '', '', now())");
            xtc_db_query("INSERT INTO geo_zones (geo_zone_id, geo_zone_name, geo_zone_description, last_modified, date_added) VALUES (5, 'Steuerzone EU', 'Steuerzone für die EU', '', now())");
            xtc_db_query("INSERT INTO geo_zones (geo_zone_id, geo_zone_name, geo_zone_description, last_modified, date_added) VALUES (7, 'Steuerzone B2B', '', NULL, now())");


            // EU-Steuerzonen Stand 01.01.2007
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (14, 14, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (21, 21, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (33, 33, 0, 5, NULL, now())");
			xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (53, 53, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (55, 55, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (56, 56, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (57, 57, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (67, 67, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (72, 72, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (73, 73, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (81, 81, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (84, 84, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (97, 97, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (103, 103, 0, 5, NULL,now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (105, 105, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (117, 117, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (123, 123, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (124, 124, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (132, 132, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (150, 150, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (170, 170, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (171, 171, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (175, 175, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (189, 189, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (190, 190, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (195, 195, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (203, 203, 0, 5, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (222, 222, 0, 5, NULL, now())");

            // Rest der Welt
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (1, 1, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (2, 2, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (3, 3, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (4, 4, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (5, 5, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (6, 6, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (7, 7, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (8, 8, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (9, 9, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (10, 10, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (11, 11, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (12, 12, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (13, 13, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (15, 15, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (16, 16, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (17, 17, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (18, 18, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (19, 19, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (20, 20, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (22, 22, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (23, 23, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (24, 24, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (25, 25, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (26, 26, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (27, 27, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (28, 28, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (29, 29, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (30, 30, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (31, 31, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (32, 32, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (34, 34, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (35, 35, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (36, 36, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (37, 37, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (38, 38, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (39, 39, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (40, 40, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (41, 41, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (42, 42, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (43, 43, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (44, 44, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (45, 45, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (46, 46, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (47, 47, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (48, 48, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (49, 49, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (50, 50, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (51, 51, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (52, 52, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (54, 54, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (58, 58, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (59, 59, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (60, 60, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (61, 61, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (62, 62, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (63, 63, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (64, 64, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (65, 65, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (66, 66, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (68, 68, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (69, 69, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (70, 70, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (71, 71, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (74, 74, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (75, 75, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (76, 76, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (77, 77, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (78, 78, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (79, 79, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (80, 80, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (82, 82, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (83, 83, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (85, 85, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (86, 86, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (87, 87, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (88, 88, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (89, 89, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (90, 90, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (91, 91, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (92, 92, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (93, 93, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (94, 94, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (95, 95, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (96, 96, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (98, 98, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (99, 99, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (100, 100, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (101, 101, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (102, 102, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (104, 104, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (106, 106, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (107, 107, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (108, 108, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (109, 109, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (110, 110, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (111, 111, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (112, 112, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (113, 113, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (114, 114, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (115, 115, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (116, 116, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (118, 118, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (119, 119, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (120, 120, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (121, 121, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (125, 125, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (126, 126, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (127, 127, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (128, 128, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (129, 129, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (130, 130, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (131, 131, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (133, 133, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (134, 134, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (135, 135, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (136, 136, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (137, 137, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (138, 138, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (139, 139, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (140, 140, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (141, 141, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (142, 142, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (143, 143, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (144, 144, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (145, 145, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (146, 146, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (147, 147, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (148, 148, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (149, 149, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (151, 151, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (152, 152, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (153, 153, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (154, 154, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (155, 155, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (156, 156, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (157, 157, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (158, 158, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (159, 159, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (160, 160, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (161, 161, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (162, 162, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (163, 163, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (164, 164, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (165, 165, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (166, 166, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (167, 167, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (168, 168, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (169, 169, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (172, 172, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (173, 173, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (174, 174, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (176, 176, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (177, 177, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (178, 178, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (179, 179, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (180, 180, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (181, 181, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (182, 182, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (183, 183, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (184, 184, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (185, 185, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (186, 186, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (187, 187, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (188, 188, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (191, 191, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (192, 192, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (193, 193, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (194, 194, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (196, 196, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (197, 197, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (198, 198, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (199, 199, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (200, 200, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (201, 201, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (202, 202, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (205, 205, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (206, 206, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (207, 207, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (208, 208, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (209, 209, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (210, 210, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (211, 211, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (212, 212, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (213, 213, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (214, 214, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (215, 215, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (216, 216, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (217, 217, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (218, 218, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (219, 219, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (220, 220, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (221, 221, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (223, 223, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (224, 224, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (225, 225, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (226, 226, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (227, 227, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (228, 228, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (229, 229, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (230, 230, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (231, 231, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (232, 232, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (233, 233, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (234, 234, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (235, 235, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (236, 236, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (237, 237, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (238, 238, 0, 6, NULL, now())");
            xtc_db_query("INSERT INTO zones_to_geo_zones VALUES (239, 239, 0, 6, NULL, now())");

            // store located in Switzerland or Lichtenstein
            if ($country == '204' || $country == '122') {
                xtc_db_query("INSERT INTO zones_to_geo_zones (association_id, zone_country_id, zone_id, geo_zone_id, last_modified, date_added) VALUES (122, 122, 0, 8, NULL,  now())");
            } else {
                xtc_db_query("INSERT INTO zones_to_geo_zones (association_id, zone_country_id, zone_id, geo_zone_id, last_modified, date_added) VALUES (122, 122, 0, 6, NULL,  now())");
            }


            // store located in Switzerland or Lichtenstein
            if ($country == '204' || $country == '122') {
                xtc_db_query("INSERT INTO zones_to_geo_zones (association_id, zone_country_id, zone_id, geo_zone_id, last_modified, date_added) VALUES (204, 204, 0, 8, NULL,  now())");
            } else {
                xtc_db_query("INSERT INTO zones_to_geo_zones (association_id, zone_country_id, zone_id, geo_zone_id, last_modified, date_added) VALUES (204, 204, 0, 6, NULL,  now())");
            }
        }
    } else {
		echo 'Error: ';
		echo $error_code;
		xtc_redirect('index.php');
	}
}
