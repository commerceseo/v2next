<?php

/**
 * @version SOFORT iDEAL - $Date: 2013-06-05 15:01:19 +0200 (Mi, 05 Jun 2013) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: sofort_ideal.php 420 2013-06-19 18:04:39Z akausch $
 */
require_once(DIR_FS_CATALOG . 'callback/sofort/helperFunctions.php');
require_once(DIR_FS_CATALOG . 'callback/sofort/library/sofortLib.php');
require_once(DIR_FS_CATALOG . 'callback/sofort/library/sofortLib_ideal_classic.php');

class sofort_ideal {

    var $code, $title, $description, $enabled, $sofort;

    function sofort_ideal() {
        global $order;

        $this->code = 'sofort_ideal';
        $this->_checkExistingSofortConstants();
        $this->title = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_TITLE_ADMIN; //admin-view
        $this->title_extern = MODULE_PAYMENT_SOFORT_IDEAL_TEXT_TITLE; //public-view

        if (defined('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_RECOMMENDED_PAYMENT') && MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_RECOMMENDED_PAYMENT == 'True')
            $this->title_extern .= ' ' . MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_RECOMMENDED_PAYMENT_TEXT;

        $this->description = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_DESCRIPTION . '<br />' . MODULE_PAYMENT_SOFORT_MULTIPAY_VERSIONNUMBER . ': ' . HelperFunctions::getSofortmodulVersion();
        $this->sort_order = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_STATUS == 'True') ? true : false);
        $this->tmpOrders = true;
        $this->tmpStatus = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TMP_STATUS_ID;
        $this->icons_available = '';

        if ((int) MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ORDER_STATUS_ID > 0)
            $this->order_status = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ORDER_STATUS_ID;
        if (is_object($order))
            $this->update_status();

        //$this->defaultCurrency = DEFAULT_CURRENCY;
        $this->sofort = new SofortLib_iDealClassic(MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CONFIGURATION_KEY, MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_PROJECT_PASSWORD, 'sha1');
        $this->sofort->setVersion(HelperFunctions::getSofortmodulVersion());
        $this->form_action_url = "";
    }

    /**
     * This function outputs the payment method title/text and if required, the input fields when user has finished shopping
     */
    function selection() {
        $description = '';

        switch (MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_IMAGE) {
            case 'Logo & Text': $description .= $this->setImageText('logo_155x50.png', MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_TEXT);
                break;
            case 'Infographic':
            default: $description .= $this->setImageText('banner_300x100.png', '');
                break;
        }

        //get all available banks from SOFORT-server
        $banks = $this->sofort->getRelatedBanks();

        //create <select> - usage of xtc_draw_pull_down_menu() is more code than following lines
        $selectBox = '<select name="ideal_bank_name" size="1"><option value="0">---</option>';

        if (!empty($banks) && is_array($banks)) {
            foreach ($banks as $value) {
                $selectBox .= '<option value="' . $value['code'] . '"';

                if (isset($_SESSION['ideal_bank_name']) && $_SESSION['ideal_bank_name'] == $value['code'])
                    $selectBox .= ' selected="selected"';

                $selectBox .= '>' . $value['name'] . '</option>';
            }
        }

        $selectBox .= '</select>';
        $fields = array(
            array(
                'title' => MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_SELECTBOX_TITLE,
                'field' => $selectBox)
        );
        $cost = '';

        //commerce:SEO - Bugfix
        if (isset($_REQUEST['xajax']) && !empty($_REQUEST['xajax'])) {
            $fields[0]['title'] = $fields[0]['title'];
            $fields[0]['field'] = $fields[0]['field'];
            return array('id' => $this->code, 'module' => $this->title_extern, 'fields' => $fields, 'description' => $description, 'module_cost' => $cost);
        } else {
            return array('id' => $this->code, 'module' => $this->title_extern, 'fields' => $fields, 'description' => $description, 'module_cost' => $cost);
        }
    }

    function pre_confirmation_check($vars = '') {
        // Fix for XTC Bug
        if (empty($_SESSION['cart']->cartID))
            $_SESSION['cart']->cartID = $_SESSION['cart']->generate_cart_id();

        //in CommerceSEO check is done with Ajax
        if (isset($_POST['xajax']) && $_POST['xajax'] == 'updatePaymentModule') {
            $requestData = $vars;
            $is_ajax = true;
        } else {
            $requestData = $_POST;
            $is_ajax = false;
        }

        if (isset($requestData['ideal_bank_name']))
            $_SESSION['ideal_bank_name'] = HelperFunctions::htmlMask($requestData['ideal_bank_name']);

        if ((!isset($requestData['ideal_bank_name']) || !$requestData['ideal_bank_name'] ) && (!isset($_SESSION['ideal_bank_name']) || !$_SESSION['ideal_bank_name'])) {
            if ($is_ajax) {
                $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_10000);
                $_SESSION['checkout_payment_error'] = $payment_error_return;
            } else {
                $paymentError = 'payment_error=' . $this->code . '&error_codes=10000';
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $paymentError, 'SSL', true, false));
            }
        }

        return false;
    }

    function install() {
        $sofortStatuses = $this->_insertAndReturnSofortStatus();
        $tempStatus = (isset($sofortStatuses['ideal_temp']) && !empty($sofortStatuses['ideal_temp'])) ? $sofortStatuses['ideal_temp'] : '';
        $abortedStatus = (isset($sofortStatuses['ideal_aborted']) && !empty($sofortStatuses['ideal_aborted'])) ? $sofortStatuses['ideal_aborted'] : '';
        $confirmedStatus = (isset($sofortStatuses['ideal_confirmed']) && !empty($sofortStatuses['ideal_confirmed'])) ? $sofortStatuses['ideal_confirmed'] : '';
        $idealLossStatus = (isset($sofortStatuses['ideal_loss']) && !empty($sofortStatuses['ideal_loss'])) ? $sofortStatuses['ideal_loss'] : '';
        $idealRefRefStatus = (isset($sofortStatuses['ideal_ref_ref']) && !empty($sofortStatuses['ideal_ref_ref'])) ? $sofortStatuses['ideal_ref_ref'] : '';
        $idealRefComStatus = (isset($sofortStatuses['ideal_ref_com']) && !empty($sofortStatuses['ideal_ref_com'])) ? $sofortStatuses['ideal_ref_com'] : '';

        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_STATUS', 'False', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_RECOMMENDED_PAYMENT', 'False', '6', '2', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CONFIGURATION_KEY', '',  '6', '3', now())");
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_AUTH', '---',  '6', '4', 'xtc_cfg_select_option(array(),', now())");  //hide the input-field with an empty <select>
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_PROJECT_PASSWORD', '',  '6', '4', now())");
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_NOTIFICATION_PASSWORD', '',  '6', '5', now())");
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added)
			VALUES 	('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ZONE', '0', '6', '6', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REASON_1', 'Nr. {{order_id}} Kd-Nr. {{customer_id}}',  '6', '7', 'xtc_cfg_select_option(array(\'Nr. {{order_id}} Kd-Nr. {{customer_id}}\'), ', now())");
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_ALLOWED', 'NL',  '6', '8', now())");
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REASON_2', '', '6', '9', now())");
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_IMAGE', 'Infographic',  '6', '10', 'xtc_cfg_select_option(array(\'Infographic\',\'Logo & Text\'), ', now())");
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_SORT_ORDER', '0', '6', '14', now())");

        //abort-status: set if buyer cancels the wizard (Warning: in about 2%-3% of all live-transactions, this status is not set correct! (Problem: iDEAL is not fast enough and SOFORT gets wrong status from iDEAL)
        //this may cause unusual history-entries but never the "paid"-status!
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CANCELED_ORDER_STATUS_ID', '" . HelperFunctions::escapeSql($abortedStatus) . "',  '6', '13', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        //Temp-Status - set while checkout and also set with notification pending-not_credited_yet
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TMP_STATUS_ID', '" . HelperFunctions::escapeSql($tempStatus) . "',  '6', '11', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        //Paid-Status - received_credited
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ORDER_STATUS_ID', '" . HelperFunctions::escapeSql($confirmedStatus) . "',  '6', '12', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        //Buyer did not pay - loss-not_credited
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_LOS_NOT_CRE_STATUS_ID', '" . HelperFunctions::escapeSql($idealLossStatus) . "',  '6', '30', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        //partically refunded - refunded-compensation
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REF_COM_STATUS_ID', '" . HelperFunctions::escapeSql($idealRefComStatus) . "',  '6', '30', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        //refunded by seller - refunded-refunded
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REF_REF_STATUS_ID', '" . HelperFunctions::escapeSql($idealRefRefStatus) . "',  '6', '30', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        //automatically(!) refunded - late_succeed-automatic_refund_to_customer
        xtc_db_query("INSERT INTO " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES
			('MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_LAT_SUC_AUT_STATUS_ID', '" . HelperFunctions::escapeSql($idealRefRefStatus) . "',  '6', '30', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
    }

    function remove() {
        xtc_db_query("DELETE FROM " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " WHERE configuration_key LIKE 'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_%'");
        xtc_db_query("DELETE FROM " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " WHERE configuration_key LIKE 'MODULE_PAYMENT_SOFORT_IDEAL_%'");

        //if this is the last deletion of a multipay-paymentmethod --> we also delete the configurationKey and other values
        $check_query = xtc_db_query("SELECT * FROM " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " WHERE configuration_key like 'MODULE_PAYMENT_SOFORT_%_STATUS'");

        if (xtc_db_num_rows($check_query) === 0) {
            xtc_db_query("DELETE FROM " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " WHERE configuration_key LIKE 'MODULE_PAYMENT_SOFORT_%'");
        }
    }

    function keys() {
        return array(
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_STATUS',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_RECOMMENDED_PAYMENT',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CONFIGURATION_KEY',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_AUTH',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_PROJECT_PASSWORD',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_NOTIFICATION_PASSWORD',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ZONE',
            'MODULE_PAYMENT_SOFORT_IDEAL_ALLOWED', //used inside of xt:commerce (not in this module)
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REASON_1',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REASON_2',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_IMAGE',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TMP_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ORDER_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CANCELED_ORDER_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_LOS_NOT_CRE_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REF_COM_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REF_REF_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_LAT_SUC_AUT_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_SORT_ORDER'
        );
    }

    function update_status() {
        global $order;

        if (($this->enabled == true) && ((int) MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ZONE > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("SELECT   zone_id
										 FROM	  " . HelperFunctions::escapeSql(TABLE_ZONES_TO_GEO_ZONES) . "
										 WHERE	  geo_zone_id = '" . HelperFunctions::escapeSql(MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ZONE) . "'
										 AND	  zone_country_id = '" . HelperFunctions::escapeSql($order->billing['country']['id']) . "'
										 ORDER BY zone_id");

            while ($check = xtc_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1 || $check['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }

            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    function javascript_validation() {
        return false;
    }

    function setImageText($image, $text) {
        $lng = HelperFunctions::getShortCode($_SESSION['language']);
        $image = 'https://images.sofort.com/' . $lng . '/ideal/' . $image;
        $image = '<img src="'.$image.'" title="'.MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGEALT.'" alt="'.MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGEALT.'" />';
        $title = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGE;
        $title = str_replace('{{image}}', $image, $title);
        $title = str_replace('{{text}}', $text, $title);

        return $title;
    }

    function confirmation() {
        global $order;

        /* If temporary order is still in session, check if order ID exists and delete order and all relating (session) data
          User might have returned to the shop for changing the order or payment method */
        if (!empty($_SESSION['cart_pn_sofortueberweisung_ID'])) {
            $cartID = substr($_SESSION['cart_pn_sofortueberweisung_ID'], 0, strlen($_SESSION['cart']->cartID));
            $order_id = substr($_SESSION['cart_pn_sofortueberweisung_ID'], strpos($_SESSION['cart_pn_sofortueberweisung_ID'], '-') + 1);
            $check_query = xtc_db_query("SELECT currency, orders_status FROM " . HelperFunctions::escapeSql(TABLE_ORDERS) . " WHERE orders_id = '" . (int) $order_id . "'");
            $result = xtc_db_fetch_array($check_query);

            if (($result['orders_status'] == MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TMP_STATUS_ID) || ($result['currency'] != $order->info['currency']) || ($_SESSION['cart']->cartID != $cartID)) {
                $this->_cancel_order((int) $order_id, 'on');
                unset($_SESSION['cart_pn_sofortueberweisung_ID']);
                unset($_SESSION['tmp_oID']);
            }
        }

        return false;
    }

    function process_button() {
        return false;
    }

    function before_process() {
        return false;
    }

    /**
     * xtCommerce calls this function if this module was chosen for payment
     * The corresponding order is being processed here, bought articles being credited
     * SofortLib calls SOFORT API
     */
    function payment_action() {
        global $order, $xtPrice, $insert_id, $shopEncoding;

        $customer_id = $_SESSION['customer_id'];
        $order_id = $insert_id;
        $_SESSION['cart_pn_sofortueberweisung_ID'] = $_SESSION['cart']->cartID . '-' . $insert_id;

        $total = ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) ? $order->info['total'] + $order->info['tax'] : $total = $order->info['total'];

        // Fix for XTC Bug
        $amount = round($total, $xtPrice->get_decimal_places($_SESSION['currency']));
        $amount = number_format($amount, 2, '.', '');

        $currency = $_SESSION['currency'];

        $reason_1 = str_replace('{{order_id}}', $order_id, MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REASON_1);
        $reason_1 = str_replace('{{customer_id}}', $customer_id, $reason_1);
        $reason_1 = substr($reason_1, 0, 27);
        $reason_2 = str_replace('{{order_id}}', $order_id, MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REASON_2);
        $reason_2 = str_replace('{{customer_id}}', $customer_id, $reason_2);
        $reason_2 = str_replace('{{order_date}}', strftime(DATE_FORMAT_SHORT), $reason_2);
        $reason_2 = str_replace('{{customer_name}}', $order->customer['firstname'] . ' ' . $order->customer['lastname'], $reason_2);
        $reason_2 = str_replace('{{customer_company}}', $order->customer['company'], $reason_2);
        $reason_2 = str_replace('{{customer_email}}', $order->customer['email_address'], $reason_2);
        $reason_2 = substr($reason_2, 0, 27);

        $user_variable_0 = $order_id;
        $user_variable_1 = $customer_id;
        $user_variable_2 = $_SESSION['cart']->cartID;

        $this->sofort->setAmount($amount, $currency);
        $this->sofort->setReason(HelperFunctions::convertEncoding($reason_1, 3), HelperFunctions::convertEncoding($reason_2, 3));
        $this->sofort->addUserVariable(HelperFunctions::convertEncoding($user_variable_0, 3));
        $this->sofort->addUserVariable(HelperFunctions::convertEncoding($user_variable_1, 3));
        $this->sofort->addUserVariable(HelperFunctions::convertEncoding($user_variable_2, 3));

        $success_url = xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL', true, false);
        $success_url = HelperFunctions::cleanUrlParameter($success_url);
        $this->sofort->setSuccessUrl(HelperFunctions::convertEncoding($success_url, 3));    //will be set in -USER_VARIABLE_3-

        $cancel_url = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'SSL', true, false);
        $cancel_url = HelperFunctions::cleanUrlParameter($cancel_url);
        $this->sofort->setAbortUrl(HelperFunctions::convertEncoding($cancel_url, 3));       //will be set in -USER_VARIABLE_4-

        $notification_url = xtc_href_link('callback/sofort/callback.php', 'action=ideal', 'SSL', true, false);
        $notification_url = HelperFunctions::cleanUrlParameter($notification_url);
        $this->sofort->setNotificationUrl(HelperFunctions::convertEncoding($notification_url, 3)); //will be set in -USER_VARIABLE_5-

        $this->sofort->setSenderCountryId('NL');
        $this->sofort->setSenderBankCode($_SESSION['ideal_bank_name']);

        $url = $this->sofort->getPaymentUrl();

        $time = date("d.m.Y, G:i:s");
        HelperFunctions::insertHistoryEntry($order_id, MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TMP_STATUS_ID, MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TMP_COMMENT, '', $time);

        xtc_redirect($url);
    }

    function after_process() {
        /* Clear our session data
          All other session data will be handled in checkout_process.php */
        if (isset($_SESSION['cart_pn_sofortueberweisung_ID']))
            unset($_SESSION['cart_pn_sofortueberweisung_ID']);
        if (isset($_SESSION['ideal_bank_name']))
            unset($_SESSION['ideal_bank_name']);
    }

    function output_error() {
        return false;
    }

    function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("SELECT configuration_value FROM " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " WHERE configuration_key = 'MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }

        return $this->_check;
    }

    function get_error() {
        if (isset($_GET['payment_error']) && $_GET['payment_error'] != 'sofort_ideal')
            return false;

        $error = false;
        $errormsg = '';

        if (!empty($_GET['payment_error'])) {
            //redirect from SOFORT or abortion by customer or other for customer unimportant error
            $errormsg = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ERROR_DEFAULT;
        }

        if (isset($_GET['xmlError'])) {
            //error in our module/SofortLib
            $errormsg = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ERROR_ALL_CODES;
        }

        if (isset($_GET['error_codes'])) { //error is important for customer (e.g. customer-errors)
            $errormsg = '';
            $errorCodes = array_unique(explode(',', HelperFunctions::htmlMask($_GET['error_codes'])));

            foreach ($errorCodes as $errorCode) {
                $constant = 'MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_' . $errorCode;
                if (defined($constant)) {
                    //show specific errorinfo to customer
                    $errormsg .= constant($constant) . " ";
                } else {
                    //specific errorinfo not defined - should never appear
                    $errormsg = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ERROR_ALL_CODES;
                    break;
                }
            }

            //error exists but no defined errorcode set
            if (empty($errormsg)) {
                $errormsg = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ERROR_ALL_CODES;
            }
        }

        return array(
            'title' => MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TEXT_ERROR_HEADING,
            'error' => $errormsg);
    }

    // @see xtc_remove_order() in admin/includes/functions/general.php
    // mods by Gambio
    function _cancel_order($orderId, $restock = false) {
        //only Gambio
        if (HelperFunctions::isGambio()) {
            $this->_gambio_remove_order($orderId, $restock, true, $restock);
            //only xtc3, comSeo
        } else {
            if ($restock == 'on') {
                //following code is (nearly 100%) copy&paste from function xtc_remove_order() - there is no other way for stock-update :-|
                //compatible and checked with xtc3_sp2 and comseo_2.0 and comseo_2.1
                $order_query = xtc_db_query("
					SELECT orders_products_id, products_id, products_quantity
					FROM " . TABLE_ORDERS_PRODUCTS . "
					WHERE orders_id = '" . xtc_db_input($orderId) . "'");

                while ($order = xtc_db_fetch_array($order_query)) {
                    xtc_db_query("
						UPDATE " . TABLE_PRODUCTS . "
						SET products_quantity = products_quantity + " . $order['products_quantity'] . ", products_ordered = products_ordered - " . xtc_db_input($order['products_quantity']) . "
						WHERE products_id = '" . xtc_db_input($order['products_id']) . "'");

                    //only comSeo, not xtc3
                    if (function_exists('nc_get_products_attributes_id')) {
                        $result = xtc_db_query('
							SELECT *
							FROM orders_products_attributes
							WHERE orders_id = "' . xtc_db_input($orderId) . '"
							AND orders_products_id = "' . xtc_db_input($order['orders_products_id']) . '"');

                        while (($row = xtc_db_fetch_array($result))) {
                            $attributes_id = nc_get_products_attributes_id($order['products_id'], $row['products_options'], $row['products_options_values']);
                            xtc_db_query('
								UPDATE products_attributes
								SET attributes_stock = attributes_stock + ' . xtc_db_input($order['products_quantity']) . '
								WHERE products_attributes_id = "' . xtc_db_input($attributes_id) . '"');
                        }
                    }
                }
            }
        }

        //update status and customer-history
        $time = date("d.m.Y, G:i:s");
        xtc_db_query('UPDATE orders
					  SET	 orders_status = "' . HelperFunctions::escapeSql(MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CANCELED_ORDER_STATUS_ID) . '",
							 last_modified = now()
					  WHERE	 orders_id = "' . HelperFunctions::escapeSql($orderId) . '"');
        HelperFunctions::insertHistoryEntry($orderId, MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CANCELED_ORDER_STATUS_ID, MODULE_PAYMENT_SOFORT_MULTIPAY_ORDER_CANCELED, '', $time);
    }

    /**
     * copy of function xtc_remove_order() in /admin/includes/functions/general.php - Gambioversion: GX2.0.10g - no direct access possible/useful
     * @param int $orderId
     * @param $restock
     * @param bool $canceled - set to FALSE (default) will delete orderinformation completly from the DB!
     * @param $reshipp
     * @see xtc_remove_order() in gambio-admin-folder
     */
    function _gambio_remove_order($order_id, $restock = false, $canceled = false, $reshipp = false) {
        //following code is NOT formatted for better comparison in case of future changes!

        if ($restock == 'on' || $reshipp == 'on') {
            // BOF GM_MOD:
            $order_query = xtc_db_query("
										SELECT DISTINCT
											op.orders_products_id,
											op.products_id,
											op.products_quantity,
											opp.products_properties_combis_id,
											o.date_purchased
										FROM " . TABLE_ORDERS_PRODUCTS . " op
											LEFT JOIN " . TABLE_ORDERS . " o ON op.orders_id = o.orders_id
											LEFT JOIN orders_products_properties opp ON opp.orders_products_id = op.orders_products_id
										WHERE
											op.orders_id = '" . xtc_db_input($order_id) . "'
			");

            while ($order = xtc_db_fetch_array($order_query)) {
                if ($restock == 'on') {
                    /* BOF SPECIALS RESTOCK */
                    $t_query = xtc_db_query("
											SELECT
												specials_date_added
											AS
												date
											FROM " .
                            TABLE_SPECIALS . "
											WHERE
												specials_date_added < '" . xtc_db_input($order['date_purchased']) . "'
											AND
												products_id			= '" . xtc_db_input($order['products_id']) . "'
					");

                    if ((int) xtc_db_num_rows($t_query) > 0) {
                        xtc_db_query("
										UPDATE " .
                                TABLE_SPECIALS . "
										SET
											specials_quantity = specials_quantity + " . xtc_db_input($order['products_quantity']) . "
										WHERE
											products_id = '" . xtc_db_input($order['products_id']) . "'
						");
                    }
                    /* EOF SPECIALS RESTOCK */

                    // check if combis exists
                    $t_combis_query = xtc_db_query("
									SELECT
	                                    products_properties_combis_id
	                                FROM
										products_properties_combis
									WHERE
										products_id = '" . xtc_db_input($order['products_id']) . "'
					");
                    $t_combis_array_length = xtc_db_num_rows($t_combis_query);

                    if ($t_combis_array_length > 0) {
                        $coo_combis_admin_control = MainFactory::create_object("PropertiesCombisAdminControl");
                        $t_use_combis_quantity = $coo_combis_admin_control->get_use_properties_combis_quantity($order['products_id']);
                    } else {
                        $t_use_combis_quantity = 0;
                    }

                    if ($t_combis_array_length == 0 || ($t_combis_array_length > 0 && $t_use_combis_quantity == 1)) {
                        xtc_db_query("
	                                    UPDATE " .
                                TABLE_PRODUCTS . "
	                                    SET
	                                        products_quantity = products_quantity + " . xtc_db_input($order['products_quantity']) . "
	                                    WHERE
	                                        products_id = '" . xtc_db_input($order['products_id']) . "'
	                    ");
                    }

                    xtc_db_query("
	                                UPDATE " .
                            TABLE_PRODUCTS . "
	                                SET
	                                    products_ordered = products_ordered - " . xtc_db_input($order['products_quantity']) . "
	                                WHERE
	                                    products_id = '" . xtc_db_input($order['products_id']) . "'
	                ");

                    if ($t_combis_array_length > 0 && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_combis_quantity == 2)) {
                        xtc_db_query("
	                                    UPDATE
	                                        products_properties_combis
	                                    SET
	                                        combi_quantity = combi_quantity + " . xtc_db_input($order['products_quantity']) . "
	                                    WHERE
	                                        products_properties_combis_id = '" . xtc_db_input($order['products_properties_combis_id']) . "' AND
	                                        products_id = '" . xtc_db_input($order['products_id']) . "'
	                    ");
                    }


                    // BOF GM_MOD
                    if (ATTRIBUTE_STOCK_CHECK == 'true') {
                        $gm_get_orders_attributes = xtc_db_query("
																SELECT
																	products_options,
																	products_options_values
																FROM
																	orders_products_attributes
																WHERE
																	orders_id = '" . xtc_db_input($order_id) . "'
																AND
																	orders_products_id = '" . xtc_db_input($order['orders_products_id']) . "'
						");

                        while ($gm_orders_attributes = xtc_db_fetch_array($gm_get_orders_attributes)) {
                            $gm_get_attributes_id = xtc_db_query("
																SELECT
																	pa.products_attributes_id
																FROM
																	products_options_values pov,
																	products_options po,
																	products_attributes pa
																WHERE
																	po.products_options_name = '" . xtc_db_input($gm_orders_attributes['products_options']) . "'
																	AND po.products_options_id = pa.options_id
																	AND pov.products_options_values_id = pa.options_values_id
																	AND pov.products_options_values_name = '" . xtc_db_input($gm_orders_attributes['products_options_values']) . "'
																	AND pa.products_id = '" . xtc_db_input($order['products_id']) . "'
																LIMIT 1
							");

                            if (xtc_db_num_rows($gm_get_attributes_id) == 1) {
                                $gm_attributes_id = xtc_db_fetch_array($gm_get_attributes_id);

                                xtc_db_query("
												UPDATE
													products_attributes
												SET
													attributes_stock = attributes_stock + " . xtc_db_input($order['products_quantity']) . "
												WHERE
													products_attributes_id = '" . xtc_db_input($gm_attributes_id['products_attributes_id']) . "'
								");
                            }
                        }
                    }
                    // EOF GM_MOD
                }

                // BOF GM_MOD products_shippingtime:
                if ($reshipp == 'on') {
                    require_once(DIR_FS_CATALOG . 'gm/inc/set_shipping_status.php');
                    set_shipping_status($order['products_id']);
                }
                // BOF GM_MOD products_shippingtime:
            }
        }
    }

    /**
     * Check, if needed sofort-lang-constants exists.
     * If not, include the english-lang-file(s).
     * @return always true
     */
    function _checkExistingSofortConstants() {
        //security check - constant exists if lang-file exists
        if (defined('MODULE_PAYMENT_SOFORT_IDEAL_TEXT_TITLE'))
            return true;

        $lngdir = DIR_FS_CATALOG . 'lang/';
        $iterator = new DirectoryIterator($lngdir);

        foreach ($iterator as $file)
            if ($file->isDir() && file_exists($lngdir . $file->getFilename() . '/modules/payment/sofort_general.php'))
                $installedModulLangs[] = $file->getFilename();

        //currently installed in this module
        if (!in_array($_SESSION['language'], $installedModulLangs))
            include_once('lang/english/modules/payment/sofort_ideal.php');

        return true;
    }

    /**
     * Insert all SOFORT-Status into table orders_status and return them
     * @return array with all status
     */
    function _insertAndReturnSofortStatus() {
        require_once(DIR_FS_CATALOG . 'callback/sofort/sofortInstallIdeal.php');

        //SOFORT-langs in this module
        $lngdir = DIR_FS_CATALOG . 'lang/';
        $iterator = new DirectoryIterator($lngdir);

        foreach ($iterator as $file)
            if ($file->isDir() && file_exists($lngdir . $file->getFilename() . '/modules/payment/sofort_general.php'))
                $sofortLangs[] = strtoupper($file->getFilename());

        //current installed langs
        $installedLangs = array();
        $orderQuery = xtc_db_query("SELECT languages_id, directory FROM " . HelperFunctions::escapeSql(TABLE_LANGUAGES));

        while ($result = xtc_db_fetch_array($orderQuery))
            $installedLangs[$result['languages_id']] = strtoupper($result['directory']);

        //get the current highest orders_status_id
        $orderQuery = xtc_db_query("SELECT MAX(orders_status_id) AS max_orders_status_id FROM orders_status");
        $maxOrdersStatusIdTemp = xtc_db_fetch_array($orderQuery);
        $ordersStatusIdTemp = $maxOrdersStatusIdTemp['max_orders_status_id'] + 1;
        $ordersStatusIdConfirmed = $ordersStatusIdTemp + 1;
        $ordersStatusIdAborted = $ordersStatusIdConfirmed + 1;
        $ordersStatusIdLoss = $ordersStatusIdAborted + 1;
        $ordersStatusIdRefCom = $ordersStatusIdLoss + 1;
        $ordersStatusIdRefRef = $ordersStatusIdRefCom + 1;
        $sofortStatuses = array();

        foreach ($installedLangs as $installedLang) {
            //insert english for shop-languages, which are not included in this module
            if (in_array($installedLang, $sofortLangs)) {
                $sofortLang = $installedLang;
                $langId = array_search($sofortLang, $installedLangs);
            } else {
                $sofortLang = 'ENGLISH';
                $langId = array_search($installedLang, $installedLangs);
            }

            //insert temp-Status if not exists
            $newOrdersStatusName = $this->_getNewOrdersStatusName('ideal_temp', $sofortLang);
            $sofortStatuses['ideal_temp'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

            if ($sofortStatuses['ideal_temp'] === false || $sofortStatuses['ideal_temp'] == '')
                $sofortStatuses['ideal_temp'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdTemp, $langId);

            //insert confirmed-Status if not exists
            $newOrdersStatusName = $this->_getNewOrdersStatusName('ideal_confirmed', $sofortLang);
            $sofortStatuses['ideal_confirmed'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

            if ($sofortStatuses['ideal_confirmed'] === false || $sofortStatuses['confirmed'] == '')
                $sofortStatuses['ideal_confirmed'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdConfirmed, $langId);

            //insert aborted-Status if not exists
            $newOrdersStatusName = $this->_getNewOrdersStatusName('ideal_aborted', $sofortLang);
            $sofortStatuses['ideal_aborted'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

            if ($sofortStatuses['ideal_aborted'] === false || $sofortStatuses['ideal_aborted'] == '')
                $sofortStatuses['ideal_aborted'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdAborted, $langId);

            //insert loss-Status if not exists
            $newOrdersStatusName = $this->_getNewOrdersStatusName('ideal_loss', $sofortLang);
            $sofortStatuses['ideal_loss'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

            if ($sofortStatuses['ideal_loss'] === false || $sofortStatuses['ideal_loss'] == '')
                $sofortStatuses['ideal_loss'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdLoss, $langId);

            //insert refunded_compensation-Status if not exists
            $newOrdersStatusName = $this->_getNewOrdersStatusName('ideal_ref_com', $sofortLang);
            $sofortStatuses['ideal_ref_com'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

            if ($sofortStatuses['ideal_ref_com'] === false || $sofortStatuses['ideal_ref_com'] == '')
                $sofortStatuses['ideal_ref_com'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdRefCom, $langId);

            //insert refunded_refunded-Status if not exists
            $newOrdersStatusName = $this->_getNewOrdersStatusName('ideal_ref_ref', $sofortLang);
            $sofortStatuses['ideal_ref_ref'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

            if ($sofortStatuses['ideal_ref_ref'] === false || $sofortStatuses['ideal_ref_ref'] == '')
                $sofortStatuses['ideal_ref_ref'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdRefRef, $langId);
        }

        return $sofortStatuses;
    }

    function _getStatusIdIfExistInDb($newOrdersStatusName, $langId) {
        if (!$newOrdersStatusName)
            return false;

        $checkQuery = xtc_db_query('SELECT orders_status_id
									FROM   orders_status
									WHERE  language_id = "' . HelperFunctions::escapeSql($langId) . '"
									AND	   orders_status_name = "' . HelperFunctions::escapeSql($newOrdersStatusName) . '"
									LIMIT  1');

        if (xtc_db_num_rows($checkQuery) < 1) {
            return false;
        } else {
            $neededOrdersStatusId = xtc_db_fetch_array($checkQuery);

            return $neededOrdersStatusId['orders_status_id'];
        }
    }

    /**
     * insert given statusstring into DB - empty strings will not be inserted
     * @return $orders_status_id from DB OR false
     */
    function _insertStatusInDb($newOrdersStatusName, $ordersStatusId, $langId) {
        if (!$newOrdersStatusName)
            return false;

        xtc_db_query("INSERT INTO orders_status (orders_status_id, language_id, orders_status_name)
			values ('" . HelperFunctions::escapeSql($ordersStatusId) . "', '" . HelperFunctions::escapeSql($langId) . "', '" . HelperFunctions::escapeSql($newOrdersStatusName) . "')");

        return $ordersStatusId;
    }

    /**
     * returns the statusname for the given $status and $lang
     * return max the first 32 chars! (because db-field = varchar(32) )
     */
    function _getNewOrdersStatusName($status, $lang) {
        switch ($status) {
            case 'ideal_temp': $newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_IDEAL_STATE_TEMP_' . $lang);
                break;
            case 'ideal_confirmed': $newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_IDEAL_STATE_CONFIRMED_' . $lang);
                break;
            case 'ideal_loss': $newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_IDEAL_STATE_LOSS_' . $lang);
                break;
            case 'ideal_aborted': $newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_IDEAL_STATE_ABORTED_' . $lang);
                break;
            case 'ideal_ref_com': $newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_COM_' . $lang);
                break;
            case 'ideal_ref_ref': $newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_REF_' . $lang);
                break;
        }

        // if string is not cut to 32 chars, status will be reinserted with every installation
        $newOrdersStatusName = substr($newOrdersStatusName, 0, 32);

        return $newOrdersStatusName;
    }

}

