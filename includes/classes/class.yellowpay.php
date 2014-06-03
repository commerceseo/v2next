<?php

/* -----------------------------------------------------------------
 * 	$Id: class.yellowpay.php 645 2013-09-30 21:16:12Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	xt:Commerce v 3.x PostFinance  Zahlungs-Modul by customweb GmbH
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

class yellowpay {

    var $code = NULL;
    var $title = NULL;
    var $description = false;
    var $enabled = false;
    var $paymentMethod = NULL;
    var $paymentMethodBrand = NULL;
    var $arrCurrencies = array('CHF', 'EUR', 'USD');

    function yellowpay() {
        global $order;
        $this->upperCode = strtoupper($this->code);
        $this->sort_order = @constant('MODULE_PAYMENT_' . $this->upperCode . '_SORT_ORDER');
        $this->zone = @constant('MODULE_PAYMENT_' . $this->upperCode . '_ZONE');
        $this->immediate = @constant('MODULE_PAYMENT_' . $this->upperCode . '_IMMEDIATE');
        $this->enabled = @constant('MODULE_PAYMENT_' . $this->upperCode . '_STATUS') == 'True' ? true : false;

        if ($this->backendCheck()) {
            $this->title = @constant('MODULE_PAYMENT_' . $this->upperCode . '_TEXT_TITLE_ADMIN');
        } else {
            $this->title = @constant('MODULE_PAYMENT_' . $this->upperCode . '_TEXT_TITLE');
        }
        $this->description = @constant('MODULE_PAYMENT_' . $this->upperCode . '_TEXT_DESCRIPTION');

        if ((int) @constant('MODULE_PAYMENT_' . $this->upperCode . '_ORDER_STATUS_ID') > 0) {
            $this->order_status = constant('MODULE_PAYMENT_' . $this->upperCode . '_ORDER_STATUS_ID');
        }

        if (is_object($order)) {
            $this->update_status();
        }
    }

    function currencyCheck() {
        if (in_array($_SESSION['currency'], $this->arrCurrencies) && @constant('MODULE_PAYMENT_' . $this->upperCode . '_' . $_SESSION['currency']) == 'True') {
            return true;
        } else {
            return false;
        }
    }

    function backendCheck() {
        global $order;
        if (preg_match('/.*\/modules.*/i', $_SERVER['REQUEST_URI'])) {
            return true;
        } else {
            return false;
        }
    }

    function findeModules() {
        $query = xtc_db_query("SELECT configuration_value, configuration_key FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE_PAYMENT_YELLOWPAY_%_STATUS'");
        while ($row = xtc_db_fetch_array($query)) {
            if ($row['configuration_key'] != 'MODULE_PAYMENT_YELLOWPAY_BASIC_STATUS' and $row['configuration_value'] == 'True') {
                return true;
            }
        }
        return false;
    }

    function update_status() {
        global $order;

        if ($this->enabled == true && $this->currencyCheck() != true) {
            $this->enabled = false;
        }
        if ($this->enabled == true && $this->code == 'yellowpay_basic' && $this->findeModules() != false) {
            $this->enabled = false;
        }
        if ($this->enabled == true && MODULE_PAYMENT_YELLOWPAY_BASIC_STATUS == 'False') {
            $this->enabled = false;
        }
        if ($this->enabled == true && $this->currencyCheck() != true) {
            $this->enabled = false;
        }
        if (($this->enabled == true) && ((int) $this->zone > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("SELECT zone_id FROM " . TABLE_ZONES_TO_GEO_ZONES . " WHERE geo_zone_id = '" . $this->zone . "' AND zone_country_id = '" . $order->billing['country']['id'] . "' ORDER BY zone_id");
            while ($check = xtc_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check['zone_id'] == $order->billing['zone_id']) {
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
        
    }

    function selection() {
        global $xtPrice, $order;
        $_SESSION['customerIsRedirected'] = false;

        $selection = array('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
        if (defined(MODULE_ORDER_TOTAL_PAYMENT_STATUS) and MODULE_ORDER_TOTAL_PAYMENT_STATUS == 'True') {
            $arrCosts = ot_payment::getPaymentCosts($this->code);
            $selection['module_cost'] = $arrCosts['text'];
        }
        return $selection;
    }

    function pre_confirmation_check() {
        
    }

    function confirmation() {
        $confirmation = array('title' => $this->title);
        return $confirmation;
    }

    function process_button() {
        
    }

    function generateRedirectUrl($orderId, $callbackId) {
        global $customer_id, $currencies, $currency, $order, $sidretour, $customers_id, $language, $xtPrice;

        $parameters = array();

        switch ($_SESSION['language']) {
            case 'german':
                $parameters['language'] = 'de_CH';
                break;
            case 'english':
                $parameters['language'] = 'en_US';
                break;
            case 'italian':
                $parameters['language'] = 'it_IT';
                break;
            case 'french':
                $parameters['language'] = 'fr_FR';
                break;
            // if no lang match -> default en_US
            default:
                $parameters['language'] = 'en_US';
        }

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $total = $order->info['total'] + $order->info['tax'];
        } else {
            $total = $order->info['total'];
        }

        $parameters['amount'] = number_format($total, $xtPrice->get_decimal_places($_SESSION['currency']), '', '');
        $parameters['currency'] = $_SESSION['currency'];

        if (MODULE_PAYMENT_YELLOWPAY_MODE === 'False') {
            $parameters['PSPID'] = MODULE_PAYMENT_YELLOWPAY_TEST_PSPID;
        } else {
            $parameters['PSPID'] = MODULE_PAYMENT_YELLOWPAY_PSPID;
        }

        // Customer data:
        $parameters['CN'] = $order->billing['firstname'] . ' ' . $order->billing['lastname'];
        $parameters['EMAIL'] = $order->customer['email_address'];
        $parameters['owneraddress'] = $order->billing['street_address'];
        $parameters['ownerZIP'] = $order->billing['postcode'];
        $parameters['ownertown'] = $order->billing['city'];
        $parameters['ownercty'] = $order->billing['country']['title'];
        $parameters['ownertelno'] = $order->customer['telephone'];
        $parameters['orderID'] = $orderId;

        $parameters['paramplus'] .= 'callback_id=' . $callbackId . '&';
        $parameters['paramplus'] .= xtc_session_name() . '=' . xtc_session_id() . '&';

        // Set Paymentmethod
        if ($this->code != 'yellowpay_basic') {
            $parameters['PM'] = $this->paymentMethod;
            $parameters['BRAND'] = $this->paymentMethodBrand;
        }

        // Create Hash
        $hash = constant('MODULE_PAYMENT_YELLOWPAY_HASH_SEND');
        if (!empty($hash)) {
            $string_before_hash = $parameters['orderID'] . $parameters['amount'] . $parameters['currency'] . $parameters['PSPID'] . $hash;
            $encoded = sha1($string_before_hash);
            $parameters['SHASign'] = $encoded;
        }

        // Reaction URL's
        $parameters['accepturl'] = xtc_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL', true);
        if (CHECKOUT_AJAX_STAT == 'true') {
            $parameters['declineurl'] = xtc_href_link(FILENAME_CHECKOUT, 'payment=' . $this->paymentCode, 'SSL', true);
            $parameters['exceptionurl'] = xtc_href_link(FILENAME_CHECKOUT, 'payment=' . $this->paymentCode, 'SSL', true);
            $parameters['cancelurl'] = xtc_href_link(FILENAME_CHECKOUT, '', 'SSL', true);
        } else {
            $parameters['declineurl'] = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment=' . $this->paymentCode, 'SSL', true);
            $parameters['exceptionurl'] = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment=' . $this->paymentCode, 'SSL', true);
            $parameters['cancelurl'] = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true);
        }

        // Add the shop idendifier to the paramplus:
        $shopId = constant('MODULE_PAYMENT_YELLOWPAY_SHOP_ID');
        if (!empty($shopId)) {
            $parameters['paramplus'] .= 'shop_id=' . $shopId . '&';
        }

        // Add Template URL
        if (defined('MODULE_PAYMENT_YELLOWPAY_TEMPLATE_FILE') && MODULE_PAYMENT_YELLOWPAY_TEMPLATE_FILE != '') {
            $parameters['TP'] = xtc_href_link(MODULE_PAYMENT_YELLOWPAY_TEMPLATE_FILE, xtc_session_name() . '=' . xtc_session_id(), 'SSL', true);
        }

        // Set the target for the form (Test or Live)
        if (MODULE_PAYMENT_YELLOWPAY_MODE === 'False') {
            $process_button_string = 'https://e-payment.postfinance.ch/ncol/test/orderstandard.asp?';
        } else {
            $process_button_string = 'https://e-payment.postfinance.ch/ncol/prod/orderstandard.asp?';
        }

        // cut the last '&' from the paramplus:
        $parameters['paramplus'] = substr($parameters['paramplus'], 0, -1);

        // Produce the output string:
        foreach ($parameters as $key => $value) {
            $process_button_string .= $key . '=' . urlencode($value) . '&';
        }

        $process_button_string = substr($process_button_string, 0, -1);

        return $process_button_string;
    }

    function executeRedirect() {
        xtc_db_query('INSERT INTO payment_callbacks (customers_id, module_name) Values (\'' . $_SESSION['customer_id'] . '\', \'' . $this->code . '\')');
        require_once (DIR_WS_CLASSES . 'class.order_total.php');
        $order_total_modules = new order_total();
        $order_totals = $order_total_modules->process();
        $callbackId = xtc_db_insert_id();
        $orderId = $callbackId;
        if (defined('MODULE_PAYMENT_YELLOWPAY_ORDER_PREFIX') && MODULE_PAYMENT_YELLOWPAY_ORDER_PREFIX != '') {
            $orderId = str_replace('{id}', $orderId, MODULE_PAYMENT_YELLOWPAY_ORDER_PREFIX);
        }
        $redirectUrl = $this->generateRedirectUrl($orderId, $callbackId);
        xtc_db_query('UPDATE payment_callbacks SET external_order_id = \'' . $orderId . '\' WHERE callback_id = \'' . $callbackId . '\'');
        $this->writeCallbackLog($callbackId, 'Redirection', 'info');
        $_SESSION['customerIsRedirected'] = true;
        header('Location: ' . $redirectUrl);
        exit();
    }

    function before_process() {
        $error = false;
        if (!isset($_POST['orderID'])) {
            $this->executeRedirect();
        } elseif ($_SESSION['customerIsRedirected'] == true && isset($_POST['callback_id'])) {
            $this->writeCallbackLog($_POST['callback_id'], 'ProceedCall', 'info');
            $error = $this->proceedInComingCall() ? false : true;
        }

        if ($error) {
            if (CHECKOUT_AJAX_STAT == 'true') {
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT, '', 'SSL', true));
            } else {
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true));
            }
        }
    }

    function proceedInComingCall() {
        if (!empty($_GET['CARDNO'])) {
            $order->info['cc_number'] = $_POST['CARDNO'];
        }

        $hash = constant('MODULE_PAYMENT_YELLOWPAY_HASH_BACK');
        if (!empty($hash)) {
            $string_before_hash =
                    $_POST['orderID'] .
                    $_POST['currency'] .
                    $_POST['amount'] .
                    $_POST['PM'] .
                    $_POST['ACCEPTANCE'] .
                    $_POST['STATUS'] .
                    $_POST['CARDNO'] .
                    $_POST['PAYID'] .
                    $_POST['NCERROR'] .
                    $_POST['BRAND'] .
                    $hash;
            $encoded = sha1($string_before_hash);

            // something is wrong -> Change Order Status
            if (strtoupper($_POST['SHASIGN']) != strtoupper($encoded)) {
                $this->writeCallbackLog($_POST['callback_id'], 'HackAttempt', 'warning');
                return false;
            } else {
                switch (substr($_POST['STATUS'], 0, 1)) {
                    // Payment was successfull
                    case '5':
                        $this->writeCallbackLog($_POST['callback_id'], 'PaymentHasBeenAutorised', 'info');
                        xtc_db_query('UPDATE payment_callbacks SET external_payment_id = \'' . $_POST['PAYID'] . '\' WHERE callback_id = \'' . $_POST['callback_id'] . '\'');
                        return true;
                        break;
                    case '9':
                        $this->writeCallbackLog($_POST['callback_id'], 'PaymentWasCaptured', 'info');
                        xtc_db_query('UPDATE payment_callbacks SET external_payment_id = \'' . $_POST['PAYID'] . '\' WHERE callback_id = \'' . $_POST['callback_id'] . '\'');
                        return true;
                        break;

                    // Payment failed:
                    case '0':
                        $this->writeCallbackLog($_POST['callback_id'], 'DataValidation', 'error');
                        return false;
                        break;
                    case '1':
                        $this->writeCallbackLog($_POST['callback_id'], 'CustomerCancelled', 'error');
                        return false;
                        break;
                    case '2':
                        $this->writeCallbackLog($_POST['callback_id'], 'AcquirerRejectPayment', 'error');
                        return false;
                        break;
                    default:
                        $this->writeCallbackLog($_POST['callback_id'], 'PaymentIsNotAccepted', 'error');
                        return false;
                        break;
                }
            }
        }
    }

    function writeCallbackLog($callbackId, $status, $type = 'error') {
        switch (strtolower($type)) {
            default:
            case 'error':
                $type = 'error';
                break;

            case 'info':
            case 'information':
                $type = 'info';
                break;

            case 'warning':
                $type = 'waring';
                break;
        }
        xtc_db_query('INSERT INTO payment_callbacks_log (info, added, callback_id, type) VALUES (\'' . $status . '\', NOW(), \'' . $callbackId . '\', \'' . $type . '\')');
    }

    function after_process() {
        global $insert_id;
        if ($this->order_status) {
            xtc_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status='" . $this->order_status . "' WHERE orders_id='" . $insert_id . "'");
        }
        $this->writeCallbackLog($_POST['callback_id'], 'OrderAdded', 'info');
        xtc_db_query('UPDATE payment_callbacks SET orders_id = \'' . $insert_id . '\' WHERE callback_id = \'' . $_POST['callback_id'] . '\'');
        $_SESSION['customerIsRedirected'] = false;
    }

    function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_PAYMENT_" . $this->upperCode . "_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    function normalInstallation() {
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_" . $this->upperCode . "_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_" . $this->upperCode . "_ALLOWED', '', '6', '3', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_" . $this->upperCode . "_ORDER_STATUS_ID', '0', '6', '8', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('MODULE_PAYMENT_" . $this->upperCode . "_ZONE', '0', '6', '4', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_" . $this->upperCode . "_SORT_ORDER', '0', '6', '7', now())");
        foreach ($this->arrCurrencies as $currency) {
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_" . $this->upperCode . "_" . $currency . "', 'True','6', '11', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        }
    }

    function remove() {
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
        $keys = array('MODULE_PAYMENT_' . $this->upperCode . '_STATUS',
            'MODULE_PAYMENT_' . $this->upperCode . '_ALLOWED',
            'MODULE_PAYMENT_' . $this->upperCode . '_ORDER_STATUS_ID',
            'MODULE_PAYMENT_' . $this->upperCode . '_ZONE',
            'MODULE_PAYMENT_' . $this->upperCode . '_SORT_ORDER'
        );

        foreach ($this->arrCurrencies as $currency) {
            $keys[] = 'MODULE_PAYMENT_' . $this->upperCode . '_' . $currency;
        }
        return $keys;
    }

}
