<?php
/* -----------------------------------------------------------------
 * 	$Id: create_account.php 485 2013-07-15 14:27:21Z akausch $
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
require_once (DIR_FS_INC . 'xtc_encrypt_password.inc.php');
require_once (DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.phpmailer.php');
require_once (DIR_FS_INC . 'xtc_php_mail.inc.php');
require_once (DIR_FS_INC . 'xtc_create_password.inc.php');
require_once (DIR_FS_INC . 'xtc_get_geo_zone_code.inc.php');

$smarty = new Smarty;

$customers_statuses_array = xtc_get_customers_statuses();

if ($customers_password == '')
    $customers_password = xtc_RandomString(6);

if ($_GET['action'] == 'edit') {

    $customers_firstname = xtc_db_prepare_input($_POST['customers_firstname']);
    $customers_cid = xtc_db_prepare_input($_POST['csID']);
    $customers_vat_id = xtc_db_prepare_input($_POST['customers_vat_id']);
    $customers_vat_id_status = xtc_db_prepare_input($_POST['customers_vat_id_status']);
    $customers_lastname = xtc_db_prepare_input($_POST['customers_lastname']);
    $customers_email_address = xtc_db_prepare_input($_POST['customers_email_address']);
    $customers_telephone = xtc_db_prepare_input($_POST['customers_telephone']);
    $customers_fax = xtc_db_prepare_input($_POST['customers_fax']);
    $customers_status_c = xtc_db_prepare_input($_POST['status']);

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

    $customers_send_mail = xtc_db_prepare_input($_POST['customers_mail']);
    $customers_password = xtc_db_prepare_input($_POST['entry_password']);

    $customers_mail_comments = xtc_db_prepare_input($_POST['mail_comments']);

    $payment_unallowed = xtc_db_prepare_input($_POST['payment_unallowed']);
    $shipping_unallowed = xtc_db_prepare_input($_POST['shipping_unallowed']);

    if ($customers_password == '')
        $customers_password = xtc_RandomString(6);
    $error = false; // reset error flag

    if (ACCOUNT_GENDER == 'true') {
        if (($customers_gender != 'm') && ($customers_gender != 'f')) {
            $error = true;
            $entry_gender_error = true;
        }
        else
            $entry_gender_error = false;
    }

    if (strlen($customers_password) < ENTRY_PASSWORD_MIN_LENGTH) {
        $error = true;
        $entry_password_error = true;
    }
    else
        $entry_password_error = false;

    if (($customers_send_mail != 'yes') && ($customers_send_mail != 'no')) {
        $error = true;
        $entry_mail_error = true;
    }
    else
        $entry_mail_error = false;

    if (strlen($customers_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
        $error = true;
        $entry_firstname_error = true;
    }
    else
        $entry_firstname_error = false;

    if (strlen($customers_lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
        $error = true;
        $entry_lastname_error = true;
    }
    else
        $entry_lastname_error = false;

    if (ACCOUNT_DOB == 'true') {
        if (checkdate(substr(xtc_date_raw($customers_dob), 4, 2), substr(xtc_date_raw($customers_dob), 6, 2), substr(xtc_date_raw($customers_dob), 0, 4))) {
            $entry_date_of_birth_error = false;
        } else {
            $error = true;
            $entry_date_of_birth_error = true;
        }
    }

    // Vat Check
    if (xtc_get_geo_zone_code($entry_country_id) != '6') {
        if ($customers_vat_id != '') {
            if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
                require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.vat_validation.php');
                $valObj = new vat_validation();

                $validate_vatid = $valObj->validate_vatid($customers_vat_id);

                if ($validate_vatid == '0') {
                    if (ACCOUNT_VAT_BLOCK_ERROR == 'true') {
                        $entry_vat_error = true;
                        $error = true;
                    }
                    $customers_vat_id_status = '0';
                }

                if ($validate_vatid == '1')
                    $customers_vat_id_status = '1';

                if ($validate_vatid == '8') {
                    if (ACCOUNT_VAT_BLOCK_ERROR == 'true') {
                        $entry_vat_error = true;
                        $error = true;
                    }
                    $customers_vat_id_status = '8';
                }

                if ($validate_vatid == '9') {
                    if (ACCOUNT_VAT_BLOCK_ERROR == 'true') {
                        $entry_vat_error = true;
                        $error = true;
                    }
                    $customers_vat_id_status = '9';
                }
            }
        }
    }
    // Vat Check

    if (xtc_get_geo_zone_code($entry_country_id) != '6') {
        require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.vat_validation.php');
        $vatID = new vat_validation($customers_vat_id, '', '', $entry_country_id);

        $customers_vat_id_status = $vatID->vat_info['vat_id_status'];
        $error = $vatID->vat_info['error'];

        if ($error == 1) {
            $entry_vat_error = true;
            $error = true;
        }
    }

    if (strlen($customers_email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
        $error = true;
        $entry_email_address_error = true;
    }
    else
        $entry_email_address_error = false;

    if (!xtc_validate_email($customers_email_address)) {
        $error = true;
        $entry_email_address_check_error = true;
    }
    else
        $entry_email_address_check_error = false;

    if (strlen($entry_street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
        $error = true;
        $entry_street_address_error = true;
    }
    else
        $entry_street_address_error = false;

    if (strlen($entry_postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
        $error = true;
        $entry_post_code_error = true;
    }
    else
        $entry_post_code_error = false;

    if (strlen($entry_city) < ENTRY_CITY_MIN_LENGTH) {
        $error = true;
        $entry_city_error = true;
    }
    else
        $entry_city_error = false;

    if ($entry_country_id == false) {
        $error = true;
        $entry_country_error = true;
    }
    else
        $entry_country_error = false;

    if (ACCOUNT_STATE == 'true') {
        if ($entry_country_error == true)
            $entry_state_error = true;
        else {
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
    }
    else
        $entry_telephone_error = false;

    $check_email = xtc_db_query("select customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($customers_email_address) . "' and customers_id <> '" . xtc_db_input($customers_id) . "'");
    if (xtc_db_num_rows($check_email)) {
        $error = true;
        $entry_email_address_exists = true;
    }
    else
        $entry_email_address_exists = false;

    if ($error == false) {
        $sql_data_array = array('customers_status' => $customers_status_c,
            'customers_cid' => $customers_cid,
            'customers_vat_id' => $customers_vat_id,
            'customers_vat_id_status' => $customers_vat_id_status,
            'customers_firstname' => $customers_firstname,
            'customers_lastname' => $customers_lastname,
            'customers_email_address' => $customers_email_address,
            'customers_telephone' => $customers_telephone,
            'customers_fax' => $customers_fax,
            'payment_unallowed' => $payment_unallowed,
            'shipping_unallowed' => $shipping_unallowed,
            'customers_password' => xtc_encrypt_password($customers_password),
            'customers_date_added' => 'now()',
            'customers_last_modified' => 'now()');

        if (ACCOUNT_GENDER == 'true')
            $sql_data_array['customers_gender'] = $customers_gender;
        if (ACCOUNT_DOB == 'true')
            $sql_data_array['customers_dob'] = xtc_date_raw($customers_dob);

        function new_customer_id($space = '-') {
            $new_cid = '';
            $day = date("d");
            $mon = date("m");
            $year = date("y");

            $cid_query = xtc_db_query("SELECT customers_id FROM " . TABLE_CUSTOMERS . " ORDER BY customers_id DESC LIMIT 1");
            $last_cid = xtc_db_fetch_array($cid_query);
            $new_cid = $day . $mon . $year . $space . ($last_cid['customers_id'] + 1000);

            return $new_cid;
        }

        $sql_data_array['customers_cid'] = ($_POST['csID'] != '' ? $_POST['csID'] : new_customer_id());

        xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array);


        $cc_id = xtc_db_insert_id();

        $sql_data_array = array('customers_id' => $cc_id,
            'entry_firstname' => $customers_firstname,
            'entry_lastname' => $customers_lastname,
            'entry_street_address' => $entry_street_address,
            'entry_postcode' => $entry_postcode,
            'entry_city' => $entry_city,
            'entry_country_id' => $entry_country_id,
            'address_date_added' => 'NOW()',
            'address_last_modified' => 'NOW()');

        if (ACCOUNT_GENDER == 'true')
            $sql_data_array['entry_gender'] = $customers_gender;
        if (ACCOUNT_COMPANY == 'true')
            $sql_data_array['entry_company'] = $entry_company;
        if (ACCOUNT_SUBURB == 'true')
            $sql_data_array['entry_suburb'] = $entry_suburb;
        if (ACCOUNT_STATE == 'true') {
            if ($zone_id > 0) {
                $sql_data_array['entry_zone_id'] = $entry_zone_id;
                $sql_data_array['entry_state'] = '';
            } else {
                $sql_data_array['entry_zone_id'] = '0';
                $sql_data_array['entry_state'] = $entry_state;
            }
        }

        xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

        $address_id = xtc_db_insert_id();

        xtc_db_query("update " . TABLE_CUSTOMERS . " set customers_default_address_id = '" . $address_id . "' where customers_id = '" . $cc_id . "'");

        xtc_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . $cc_id . "', '0', now())");

        if ($customers_status_c == '0')
            xtc_db_query("INSERT into " . TABLE_ADMIN_ACCESS . " (customers_id,start) VALUES ('" . $cc_id . "', '1')");

        if (($_POST['customers_mail'] == 'yes')) {

            $smarty->assign('language', $_SESSION['language']);
            $smarty->caching = false;

            $smarty->template_dir = DIR_FS_CATALOG . 'templates';
            $smarty->compile_dir = DIR_FS_CATALOG . 'templates_c';
            $smarty->config_dir = DIR_FS_CATALOG . 'lang';

            $smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
            $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

            $smarty->assign('NNAME', $customers_lastname);
            $smarty->assign('USERNAME4MAIL', $customers_email_address);
            $smarty->assign('PASSWORT4MAIL', $customers_password);
            $smarty->assign('GENDER', $customers_gender);
            $smarty->assign('STORE_URL', HTTP_CATALOG_SERVER);
            $smarty->assign('STORE_NAME', STORE_NAME);

            require_once (DIR_FS_INC . 'cseo_get_mail_body.inc.php');
            $smarty->caching = false;
            $html_mail = $smarty->fetch('html:create_account_admin');
            $html_mail .= $signatur_html;
            $smarty->caching = false;
            $txt_mail = $smarty->fetch('txt:create_account_admin');
            $txt_mail .= $signatur_text;
            require_once (DIR_FS_INC . 'cseo_get_mail_data.inc.php');
            $mail_data = cseo_get_mail_data('create_account_admin');

            $html_mail = str_replace('{#support_mail_address#}', $mail_data['EMAIL_REPLAY_ADDRESS'], $html_mail);
            $txt_mail = str_replace('{#support_mail_address#}', $mail_data['EMAIL_REPLAY_ADDRESS'], $txt_mail);

            xtc_php_mail($mail_data['EMAIL_ADDRESS'], $mail_data['EMAIL_ADDRESS_NAME'], $customers_email_address, $customers_lastname . ' ' . $customers_firstname, $mail_data['EMAIL_FORWARD'], $mail_data['EMAIL_REPLAY_ADDRESS'], $mail_data['EMAIL_REPLAY_ADDRESS_NAME'], '', '', $mail_data['EMAIL_SUBJECT'], $html_mail, $txt_mail);
        }
        xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, 'cID=' . $cc_id, 'SSL'));
    }
}
require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
<?php echo xtc_draw_form('customers', FILENAME_CREATE_ACCOUNT, xtc_get_all_get_params(array('action')) . 'action=edit', 'POST', 'onSubmit="return check_form();"') . xtc_draw_hidden_field('default_address_id', $customers_default_address_id); ?>
            <table border="0" width="100%" cellspacing="0" cellpadding="6">
                <tr>
                    <td colspan="3">
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="48%">
                        <table cellspacing="0" cellpadding="10" class="formArea" width="100%">
                            <tr>
                                <td class="formAreaTitle"><?php echo CATEGORY_PERSONAL; ?></td>
                            </tr>
<?php if (ACCOUNT_GENDER == 'true') { ?>
                                <tr>
                                    <td class="main" width="40%"><?php echo ENTRY_GENDER; ?><span style="color:red">*</span></td>
                                    <td class="main">
                                        <?php
                                        if ($error == true) {
                                            if ($entry_gender_error == true)
                                                echo xtc_draw_radio_field('customers_gender', 'm', false, $customers_gender) . ' ' . MALE . ' ' . xtc_draw_radio_field('customers_gender', 'f', false, $customers_gender) . ' ' . FEMALE . '&nbsp;' . ENTRY_GENDER_ERROR;
                                            else {
                                                echo ($customers_gender == 'm') ? MALE : FEMALE;
                                                echo xtc_draw_radio_field('customers_gender', 'm', false, $customers_gender) . ' ' . MALE . '&nbsp;&nbsp;' . xtc_draw_radio_field('customers_gender', 'f', false, $customers_gender) . ' ' . FEMALE;
                                            }
                                        }
                                        else
                                            echo xtc_draw_radio_field('customers_gender', 'm', false, $customers_gender) . ' ' . MALE . ' ' . xtc_draw_radio_field('customers_gender', 'f', false, $customers_gender) . ' ' . FEMALE;
                                        ?>
                                    </td>
                                </tr>
                                    <?php } ?>
                            <tr>
                                <td class="main"><?php echo ENTRY_CID; ?><span style="color:red">*</span></td>
                                <td class="main">
<?php
echo xtc_draw_input_field('csID', $customers_cid, 'maxlength="32"');
?>
                                </td>
                            </tr>
                            <tr>
                                <td class="main"><?php echo ENTRY_FIRST_NAME; ?><span style="color:red">*</span></td>
                                <td class="main">
                                    <?php
                                    if ($error == true) {
                                        if ($entry_firstname_error == true)
                                            echo xtc_draw_input_field('customers_firstname', $customers_firstname, 'maxlength="32"') . '&nbsp;' . ENTRY_FIRST_NAME_ERROR;
                                        else
                                            echo xtc_draw_input_field('customers_firstname', $customers_firstname, 'maxlength="32"');
                                    }
                                    else
                                        echo xtc_draw_input_field('customers_firstname', $customers_firstname, 'maxlength="32"');
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="main"><?php echo ENTRY_LAST_NAME; ?><span style="color:red">*</span></td>
                                <td class="main"><?php
                                    if ($error == true) {
                                        if ($entry_lastname_error == true)
                                            echo xtc_draw_input_field('customers_lastname', $customers_lastname, 'maxlength="32"') . '&nbsp;' . ENTRY_LAST_NAME_ERROR;
                                        else
                                            echo xtc_draw_input_field('customers_lastname', $customers_lastname, 'maxlength="32"');
                                    }
                                    else
                                        echo xtc_draw_input_field('customers_lastname', $customers_lastname, 'maxlength="32"');
                                    ?>
                                </td>
                            </tr>
                                    <?php if (ACCOUNT_DOB == 'true') { ?>
                                <tr>
                                    <td class="main"><?php echo ENTRY_DATE_OF_BIRTH; ?><span style="color:red">*</span></td>
                                    <td class="main">
                                        <?php
                                        if ($error == true) {
                                            if ($entry_date_of_birth_error == true)
                                                echo xtc_draw_input_field('customers_dob', xtc_date_short($customers_dob), 'maxlength="10"') . '&nbsp;' . ENTRY_DATE_OF_BIRTH_ERROR;
                                            else
                                                echo xtc_draw_input_field('customers_dob', xtc_date_short($customers_dob), 'maxlength="10"');
                                        }
                                        else
                                            echo xtc_draw_input_field('customers_dob', xtc_date_short($customers_dob), 'maxlength="10"');
                                        ?>
                                    </td>
                                </tr>
                                    <?php } ?>
                            <tr>
                                <td class="main last_td"><?php echo ENTRY_EMAIL_ADDRESS; ?><span style="color:red">*</span></td>
                                <td class="main last_td">
                                    <?php
                                    if ($error == true) {
                                        if ($entry_email_address_error == true)
                                            echo xtc_draw_input_field('customers_email_address', $customers_email_address, 'maxlength="96"') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR;
                                        elseif ($entry_email_address_check_error == true)
                                            echo xtc_draw_input_field('customers_email_address', $customers_email_address, 'maxlength="96"') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_CHECK_ERROR;

                                        elseif ($entry_email_address_exists == true)
                                            echo xtc_draw_input_field('customers_email_address', $customers_email_address, 'maxlength="96"') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
                                        else
                                            echo xtc_draw_input_field('customers_email_address', $customers_email_address, 'maxlength="96"');
                                    }
                                    else
                                        echo xtc_draw_input_field('customers_email_address', $customers_email_address, 'maxlength="96"');
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="2%">&nbsp;</td>
<?php if (ACCOUNT_COMPANY == 'true') { ?>
                        <td valign="top">
                            <table border="0" cellspacing="0" cellpadding="10" class="formArea" width="100%">
                                <tr>
                                    <td class="formAreaTitle" colspan="2"><?php echo CATEGORY_COMPANY; ?></td>
                                </tr>
                                <tr>
                                    <td class="main" width="40%"><?php echo ENTRY_COMPANY; ?></td>
                                    <td class="main">
                                        <?php
                                        if ($error == true) {
                                            if ($entry_company_error == true)
                                                echo xtc_draw_input_field('entry_company', $entry_company, 'maxlength="32"') . '&nbsp;' . ENTRY_COMPANY_ERROR;
                                            else
                                                echo xtc_draw_input_field('entry_company', $entry_company, 'maxlength="32"');
                                        }
                                        else
                                            echo xtc_draw_input_field('entry_company', $entry_company, 'maxlength="32"');
                                        ?>
                                    </td>
                                </tr>
                                        <?php if (ACCOUNT_COMPANY_VAT_CHECK == 'true') { ?>
                                    <tr>
                                        <td class="main last_td"><?php echo ENTRY_VAT_ID; ?></td>
                                        <td class="main last_td">
                                            <?php
                                            if ($error == true) {
                                                if ($entry_vat_error == true)
                                                    echo xtc_draw_input_field('customers_vat_id', $customers_vat_id, 'maxlength="32"') . '&nbsp;' . ENTRY_VAT_ERROR;
                                                else
                                                    echo xtc_draw_input_field('customers_vat_id', $customers_vat_id, 'maxlength="32"');
                                            }
                                            else
                                                echo xtc_draw_input_field('customers_vat_id', $customers_vat_id, 'maxlength="32"');
                                            ?>
                                        </td>
                                    </tr>
    <?php } ?>
                            </table>
                        </td>
<?php } ?>
                </tr>
                <tr>
                    <td width="48%">
                        <table cellspacing="0" cellpadding="10" class="formArea" width="100%">
                            <tr>
                                <td colspan="2" class="formAreaTitle"><?php echo CATEGORY_ADDRESS; ?></td>
                            </tr>
                            <tr>
                                <td class="main" width="40%"><?php echo ENTRY_STREET_ADDRESS; ?><span style="color:red">*</span></td>
                                <td class="main">
<?php
if ($error == true) {
    if ($entry_street_address_error == true)
        echo xtc_draw_input_field('entry_street_address', $entry_street_address, 'maxlength="64"') . '&nbsp;' . ENTRY_STREET_ADDRESS_ERROR;
    else
        echo xtc_draw_input_field('entry_street_address', $entry_street_address, 'maxlength="64"');
}
else
    echo xtc_draw_input_field('entry_street_address', $entry_street_address, 'maxlength="64"');
?>
                                </td>
                            </tr>
                                    <?php if (ACCOUNT_SUBURB == 'true') { ?>
                                <tr>
                                    <td class="main"><?php echo ENTRY_SUBURB; ?></td>
                                    <td class="main">
                                <?php
                                if ($error == true) {
                                    if ($entry_suburb_error == true)
                                        echo xtc_draw_input_field('suburb', $entry_suburb, 'maxlength="32"') . '&nbsp;' . ENTRY_SUBURB_ERROR;
                                    else
                                        echo xtc_draw_input_field('entry_suburb', $entry_suburb, 'maxlength="32"');
                                }
                                else
                                    echo xtc_draw_input_field('entry_suburb', $entry_suburb, 'maxlength="32"');
                                ?>
                                    </td>
                                </tr>
                                    <?php } ?>
                            <tr>
                                <td class="main"><?php echo ENTRY_POST_CODE; ?><span style="color:red">*</span></td>
                                <td class="main">
<?php
if ($error == true) {
    if ($entry_post_code_error == true)
        echo xtc_draw_input_field('entry_postcode', $entry_postcode, 'maxlength="8"') . '&nbsp;' . ENTRY_POST_CODE_ERROR;
    else
        echo xtc_draw_input_field('entry_postcode', $entry_postcode, 'maxlength="8"');
}
else
    echo xtc_draw_input_field('entry_postcode', $entry_postcode, 'maxlength="8"');
?>
                                </td>
                            </tr>
                            <tr>
                                <td class="main"><?php echo ENTRY_CITY; ?><span style="color:red">*</span></td>
                                <td class="main">
<?php
if ($error == true) {
    if ($entry_city_error == true)
        echo xtc_draw_input_field('entry_city', $entry_city, 'maxlength="32"') . '&nbsp;' . ENTRY_CITY_ERROR;
    else
        echo xtc_draw_input_field('entry_city', $entry_city, 'maxlength="32"');
}
else
    echo xtc_draw_input_field('entry_city', $entry_city, 'maxlength="32"');
?>
                                </td>
                            </tr>
                                    <?php if (ACCOUNT_STATE == 'true') { ?>
                                <tr>
                                    <td class="main"><?php echo ENTRY_STATE; ?><span style="color:red">*</span></td>
                                    <td class="main">
                                        <?php
                                        $entry_state = xtc_get_zone_name($entry_country_id, $entry_zone_id, $entry_state);
                                        if ($error == true) {
                                            if ($entry_state_error == true) {
                                                if ($entry_state_has_zones == true) {
                                                    $zones_array = array();
                                                    $zones_query = xtc_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . xtc_db_input($entry_country_id) . "' order by zone_name");
                                                    while ($zones_values = xtc_db_fetch_array($zones_query))
                                                        $zones_array[] = array('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);

                                                    echo xtc_draw_pull_down_menu('entry_state', $zones_array) . '&nbsp;' . ENTRY_STATE_ERROR;
                                                }
                                                else
                                                    echo xtc_draw_input_field('entry_state', xtc_get_zone_name($entry_country_id, $entry_zone_id, $entry_state)) . '&nbsp;' . ENTRY_STATE_ERROR;
                                            }
                                            else
                                                echo xtc_draw_input_field('entry_state', xtc_get_zone_name($entry_country_id, $entry_zone_id, $entry_state));
                                        }
                                        else
                                            echo xtc_draw_input_field('entry_state', xtc_get_zone_name($entry_country_id, $entry_zone_id, $entry_state));
                                        ?>
                                    </td>
                                </tr>
<?php } ?>
                            <tr>
                                <td class="main last_td"><?php echo ENTRY_COUNTRY; ?><span style="color:red">*</span></td>
                                <td class="main last_td">
<?php
if ($error == true) {
    if ($entry_country_error == true)
        echo xtc_draw_pull_down_menu('entry_country_id', xtc_get_countries(xtc_get_country_name(STORE_COUNTRY)), $entry_country_id) . '&nbsp;' . ENTRY_COUNTRY_ERROR;
    else
        echo xtc_draw_pull_down_menu('entry_country_id', xtc_get_countries(xtc_get_country_name(STORE_COUNTRY)), $entry_country_id);
}
else
    echo xtc_draw_pull_down_menu('entry_country_id', xtc_get_countries(xtc_get_country_name(STORE_COUNTRY)), $entry_country_id);
?>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="2%">&nbsp;</td>
                    <td valign="top">
                        <table cellspacing="0" cellpadding="10" class="formArea" width="100%">
                            <tr><td class="formAreaTitle"><?php echo CATEGORY_CONTACT; ?></td></tr>
                            <tr>
                                <td class="main"><?php echo ENTRY_TELEPHONE_NUMBER; ?><span style="color:red">*</span></td>
                                <td class="main">
<?php
if ($error == true) {
    if ($entry_telephone_error == true) {
        echo xtc_draw_input_field('customers_telephone', $customers_telephone) . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_ERROR;
    } else {
        echo xtc_draw_input_field('customers_telephone', $customers_telephone);
    }
} else {
    echo xtc_draw_input_field('customers_telephone', $customers_telephone);
}
?>
                                </td>
                            </tr>
                            <tr>
                                <td class="main last_td"><?php echo ENTRY_FAX_NUMBER; ?></td>
                                <td class="main last_td"><?php echo xtc_draw_input_field('customers_fax'); ?></td>
                            </tr>
                        </table>
                        <br /><?php echo ENTRY_REQUIRE; ?>
                    </td>
                </tr>
                <tr>
                    <td valign="top" colspan="3">
                        <table cellspacing="0" cellpadding="10" class="formArea" width="100%">
                            <tr>
                                <td class="formAreaTitle" colspan="2"><?php echo CATEGORY_OPTIONS; ?></td>
                            </tr>
                            <tr>
                                <td class="main" width="30%"><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
                                <td class="main">
                                    <?php
                                    if ($processed == true)
                                        echo xtc_draw_hidden_field('status');
                                    else
                                        echo xtc_draw_pull_down_menu('status', $customers_statuses_array, ($customers_status_c != '' ? $customers_status_c : '2'));
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="main"><?php echo ENTRY_MAIL; ?></td>
                                <td class="main">
<?php
if ($error == true) {
    if ($entry_mail_error == true)
        echo xtc_draw_radio_field('customers_mail', 'yes', true, $customers_send_mail) . '&nbsp;&nbsp;' . YES . '&nbsp;&nbsp;' . xtc_draw_radio_field('customers_mail', 'no', false, $customers_send_mail) . '&nbsp;&nbsp;' . NO . '&nbsp;' . ENTRY_MAIL_ERROR;
    else {
        echo ($customers_gender == 'm') ? YES : NO;
        echo xtc_draw_radio_field('customers_mail', 'yes', true, $customers_send_mail) . '&nbsp;&nbsp;' . YES . '&nbsp;&nbsp;' . xtc_draw_radio_field('customers_mail', 'no', false, $customers_send_mail) . '&nbsp;&nbsp;' . NO;
    }
}
else
    echo xtc_draw_radio_field('customers_mail', 'yes', true, $customers_send_mail) . '&nbsp;&nbsp;' . YES . '&nbsp;&nbsp;' . xtc_draw_radio_field('customers_mail', 'no', false, $customers_send_mail) . '&nbsp;&nbsp;' . NO;
?>
                                </td>
                            </tr>
                            <tr>
                                <td class="main"><?php echo ENTRY_PAYMENT_UNALLOWED; ?></td>
                                <td class="main"><?php echo xtc_draw_input_field('payment_unallowed'); ?></td>
                            </tr>
                            <tr>
                                <td class="main"><?php echo ENTRY_SHIPPING_UNALLOWED; ?></td>
                                <td class="main"><?php echo xtc_draw_input_field('shipping_unallowed'); ?></td>
                            </tr>
                            <tr>
                                <td class="main" bgcolor="#fcf5dd"><?php echo ENTRY_PASSWORD; ?></td>
                                <td class="main" bgcolor="#fcf5dd">
                        <?php
                        if ($error == true) {
                            if ($entry_password_error == true)
                                echo xtc_draw_password_field('entry_password', $customers_password) . '&nbsp;' . ENTRY_PASSWORD_ERROR;
                            else
                                echo xtc_draw_password_field('entry_password', $customers_password);
                        }
                        else
                            echo xtc_draw_password_field('entry_password', $customers_password);
                        ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="right" colspan="3">
<?php
echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_INSERT . '">
	        				<a class="button" onclick="this.blur();" href="' . xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params(array('action'))) . '">' . BUTTON_CANCEL . '</a>';
?>
                    </td>
                </tr>
            </table>
            </form>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
