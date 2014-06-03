<?php

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category  PayIntelligent
 * @package   PayIntelligent_ClickandBuy
 * @copyright (C) 2010 PayIntelligent GmbH  <http://www.payintelligent.de/>
 * @license   GPLv2
 */
class pi_clickandbuy_recurring {

    var $code;
    var $title;
    var $public_title;
    var $description;
    var $enabled;
    var $_check;
    var $secret_key;
    var $amount;
    var $sandbox;
    var $merchantId;

    /**
     * Construct
     */
    function pi_clickandbuy_recurring() {
        global $order;
        $this->code = 'pi_clickandbuy_recurring';
        $this->title = MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_TITLE . '<br/><img src="' . DIR_WS_CATALOG . 'images/icons/pi_cab_logo_admin.png" alt="ClickandBuy Logo"/>';
        $this->public_title = MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_TITLE;
        $this->description = utf8_decode($this->getConfigHtml());
        $this->sort_order = MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_STATUS == 'True') ? true : false);
        $this->secret_key = MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY;
        $this->sandbox = MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SANDBOX;
        $this->merchantId = MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_MERCHANT_ID;

        if ((int) MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ORDER_STATUS_ID;
        }

        $this->check();
        if (is_object($order))
            $this->update_status();
    }

    /**
     * Getter for the html code of the head of the clickandbuy config
     *
     * @return string html
     */
    function getConfigHtml() {
        $_SESSION['payment'] = 'pi_clickandbuy_recurring';
        $mercahntId  = (string) MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_MERCHANT_ID;
        $description = ''
                       . '<div align="center">'
                       . '<img src="' . DIR_WS_CATALOG . 'images/icons/pi_clickandbuy_logo.gif" alt="ClickandBuy Logo"/>'
                       . '</div>'
                       . '<link rel="stylesheet" type="text/css" href="' . DIR_WS_CATALOG . 'admin/merchant_registration/css/piCabStyle.css">'
                       . '<script type="text/javascript" src="' . DIR_WS_CATALOG . 'admin/merchant_registration/js/piCabAjax.js"></script>'
                       . '<script type="text/javascript" src="' . DIR_WS_CATALOG . 'admin/merchant_registration/js/config.js"></script>'
                       . '<div class="clickandbuy-payment-notice">'
                       . '<h3 align="center">'
                       . MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION_HEAD
                       . '</h3>';
        if ($mercahntId == '') {
            $description .= '<div align="center">'
                           . MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION_TEXT_1
                           . '</div>'
                           . '<div align="center">'
                           . MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION_TEXT_2
                           . '</div>'
                           . '<h3 align="center">'
                           . MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION_SUBHEAD
                           . '</h3>'
                           . '<div class="piCabCenter piCabLinkStyle">'
                           .    '<a href="javascript:piCabNextPage(\'first\', \'piCabEmbeddedRegistration\');toggleWrapper();">'
                           .        '<div class="piCabButton" style="background-image: url(' . DIR_WS_CATALOG . 'admin/merchant_registration/img/clickandbuy_button.png);">' . MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_MERCHANT_REGISTRATION_BUTTON . '</div>'
                           .    '</a>'
                           . '</div>'
                           . '</div>';
        }
        $html = $this->getMerchantRegistrationHtml();
        return $description . $html;
    }

    /**
     * Getter for the merchant registration html code
     *
     * @return string html code
     */
    function getMerchantRegistrationHtml() {
        $html = ''
                . '<script type="text/javascript">'
                . 'if (getGetParam("success") == "true"){'
                . 'window.onload = piCabInitSuccess;'
                . '} else {'
                . 'window.onload = piCabInitRegistration;'
                . '}'
                . '</script>'
                . '<div id="piCabCompleteRegistration">'
                . '<div id="piCabOverlay"></div>'
                . '<div class="piCabEmbeddedContainerHead" id="piCabHead">'
                . '<div class="piCabCloseOrHideRight">'
                . '<b>'
                . '<a onclick="toggleWrapper(\'piCabEmbeddedRegistration\')" class="piCabCloseOrHide" style="font-size:13px;">X</a>'
                . '</b>'
                . '</div>'
                . '</div>'
                . '<div id="piCabEmbeddedRegistration" class="piCabEmbeddedContainer">'
                . '</div>'
                . '</div>';
        return $html;
    }

    /**
     * Retrieve the info destination code
     *
     * @param string $customer_country_code
     * @return string
     */
    function get_info_destination($customer_country_code) {
        $info_destination = "GB_en";

        switch ($customer_country_code) {
            case "AT" :
                $info_destination = "AT_de";
                break;
            case "US" :
                $info_destination = "US_en";
                break;
            case "DE" :
                $info_destination = "DE_de";
                break;
            case "GB" :
                $info_destination = "GB_en";
                break;
            case "ES" :
                $info_destination = "ES_es";
                break;
            case "FR" :
                $info_destination = "FR_fr";
                break;
            case "NL" :
                $info_destination = "NL_nl";
                break;
            case "IT" :
                $info_destination = "IT_it";
                break;
            case "DK" :
                $info_destination = "DK_da";
                break;
            case "NO" :
                $info_destination = "NO_no";
                break;
            case "FI" :
                $info_destination = "FI_fi";
                break;
            case "SE" :
                $info_destination = "SE_sv";
                break;
            case "PT" :
                $info_destination = "PT_pt";
                break;
        }
        return $info_destination;
    }

    /**
     * Update the status of this payment method
     */
    function update_status() {
        global $order;
        $this->form_action_url = 'pi_clickandbuy_do_trans.php';

        if (($this->enabled == true) && ((int) MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ZONE > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ZONE . "' and zone_country_id = '" . $order->billing ['country'] ['id'] . "' order by zone_id");

            while ($check = xtc_db_fetch_array($check_query)) {
                if ($check ['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check ['zone_id'] == $order->billing ['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }

            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    /**
     *
     * @return boolean false
     */
    function javascript_validation() {
        return false;
    }

    /**
     * Retrieve the html code for this payment method
     *
     * @return string html
     */
    function selection() {
        global $order;

        return array('id' => $this->code,
            'module' => $this->public_title,
            'description' => xtc_image(DIR_WS_ICONS . '/pi_clickandbuy_logo.png') . '<br /></b>' . MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_CHECKOUT_TEXT_INFO . '<br /><b><small>(<a href="http://clickandbuy.com/' . CLICKANDBUY_LANG_CODE . '/info.html" target="_blank" >' . MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_CHECKOUT_MORE_INFO_LINK_TITLE . '</a>)</small>');
    }

    /**
     *
     * @return boolean false
     */
    function pre_confirmation_check() {
        return false;
    }

    /**
     *
     * @return boolean false
     */
    function confirmation() {
        return false;
    }

    /**
     * Retrieve the html code for some hidden input fields
     * with nessecary data for the clickandbuy checkout
     *
     * @return string
     */
    function process_button() {
        global $HTTP_POST_VARS, $order, $xtPrice;
        $_SESSION['pi']['order'] = serialize($order);
        if (MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SPECIAL_CASE == 'Partial Delivery') {
            $payment_type = 'clickandbuypartialdelivery';
        } elseif (MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SPECIAL_CASE == 'Fast Checkout') {
            $payment_type = 'clickandbuyfastcheckout';
        } else {
            $payment_type = 'clickandbuyrecurring';
        }

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $this->amount = $order->info['total'] + $order->info['tax'];
        } else {
            $this->amount = $order->info['total'];
        }
        
        $this->amount = round($xtPrice->xtcCalculateCurrEx($this->amount, $_SESSION['currency']), $xtPrice->get_decimal_places($_SESSION['currency']));
        $this->amount = number_format($this->amount, 2, '.', '');

        $currency = $_SESSION ['currency'];

        // generate externalID
        $external_id = substr(md5(uniqid(rand())), 0, 12);
        $shash = $this->generate_shash($this->amount, $currency, $external_id, $payment_type);

        $process_button_string = xtc_draw_hidden_field('paymentType', $payment_type) . xtc_draw_hidden_field('externalID', $external_id) . xtc_draw_hidden_field('sHash', $shash);
        return $process_button_string;
    }

    /**
     * Redirect to checkout payment section if a error occurs or retunr false
     *
     * @return boolean false
     */
    function before_process() {
        global $language, $order, $xtPrice;

        $XTCsid = $_GET ['XTCsid'];
        $external_id = $_GET ['externalID'];
        $amount = $_GET ['amount'];
        $shash = $_GET ['sHash'];
        $payment_type = $_GET ['paymentType'];

        $currency = $_SESSION ['currency'];

        $shash_check = $this->generate_shash($amount, $currency, $external_id, $payment_type);
        //Security check
        if ($shash != $shash_check) {
            $error_message = CLICKANDBUY_ERROR_MESSAGE_2 . CLICKANDBUY_ERROR_MESSAGE_4;
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'XTCsid=' . $XTCsid . '&error_message=' . $error_message, 'SSL'));
        }

        if ($payment_type != 'clickandbuyfastcheckout') {
            $check_query = xtc_db_query("SELECT handshake FROM picab_orders WHERE externalID = '$external_id'");
            $check = xtc_db_fetch_array($check_query);
            $handshake = (int) $check ['handshake'];

            //Handshake check if transaction is already confirmed
            if ($handshake != 0) {
                $error_message = CLICKANDBUY_ERROR_MESSAGE_2 . CLICKANDBUY_ERROR_MESSAGE_5;
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'XTCsid=' . $XTCsid . '&error_message=' . $error_message, 'SSL'));
            }
        }
        return false;
    }

    /**
     * Update the clickandbuy order table after successful checkout
     *
     * @return boolean false
     */
    function after_process() {
        global $insert_id;
        $external_id = $_GET ['externalID'];

        xtc_db_query("UPDATE picab_orders SET handshake = '1',shopOrderID='" . $insert_id . "',handshake='1',modified='" . date("Y-m-d H:i:s") . "' WHERE externalID = '" . $external_id . "'");
        
        if ($this->order_status) {
            xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");
        }
        
        return false;
    }

    /**
     *
     * @return boolean false
     */
    function get_error() {
        return false;
    }

    /**
     * Retrieve the number of the module configuration_value entrys
     *
     * @return integer $this->_check
     */
    function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    /**
     * Create the db tables for this payment method
     */
    function install() {
        global $language;
        @include (DIR_FS_CATALOG . DIR_WS_LANGUAGES . $language . '/modules/payment/pi_clickandbuy_recurring.php');

        $check_query = xtc_db_query("SHOW TABLES LIKE 'picab_mms'");
        if (xtc_db_num_rows($check_query) == 0) {
            xtc_db_query("CREATE TABLE picab_mms (
			  eventID varchar(64) NOT NULL,
			  shopOrderID varchar(64) character set latin1 collate latin1_general_ci NOT NULL,
			  externalID varchar(64) NOT NULL,
			  transactionID varchar(64) NOT NULL,
			  oldState varchar(64) NOT NULL,
			  newState varchar(64) NOT NULL,
			  xml text NOT NULL,
			  created timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP)
      ");
        }

        $check_query = xtc_db_query("SHOW TABLES LIKE 'picab_orders'");
        if (xtc_db_num_rows($check_query) == 0) {
            xtc_db_query("CREATE TABLE picab_orders (
			  id int(11) NOT NULL auto_increment,
			  shopOrderID varchar(32) character set latin1 collate latin1_general_ci NOT NULL,
			  shopUserID varchar(32) character set latin1 collate latin1_general_ci NOT NULL,
			  transactionID varchar(64) NOT NULL,
			  externalID varchar(64) NOT NULL,
			  paymentType varchar(32) NOT NULL,
			  authorization tinyint(1) NOT NULL,
			  authorizationID varchar(64) NOT NULL,
			  handshake tinyint(1) NOT NULL,
			  closed tinyint(1) NOT NULL,
			  recAmount decimal(9,2) NOT NULL,
			  amount decimal(9,2) NOT NULL default '0.00',
			  debited decimal(9,2) NOT NULL,
			  refunded decimal(9,2) NOT NULL default '0.00',
			  cancelled decimal(9,2) NOT NULL default '0.00',
			  currency varchar(4) NOT NULL,
			  created datetime NOT NULL,
			  modified datetime NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `orders_id` (`shopOrderID`))
      ");
        }

        $check_query = xtc_db_query("SHOW TABLES LIKE 'picab_transactions'");
        if (xtc_db_num_rows($check_query) == 0) {
            xtc_db_query("CREATE TABLE picab_transactions (
			  id int(11) NOT NULL auto_increment,
			  shopOrderID varchar(64) character set latin1 collate latin1_general_ci NOT NULL,
			  transactionID varchar(64) NOT NULL,
			  externalID varchar(64) NOT NULL,
			  transactionType varchar(64) NOT NULL,
			  description text NOT NULL,
			  amount decimal(9,2) NOT NULL default '0.00',
			  currency varchar(4) NOT NULL,
			  paid tinyint(1) NOT NULL,
			  status varchar(64) NOT NULL,
			  created datetime NOT NULL,
			  modified datetime NOT NULL,
			  PRIMARY KEY  (`id`))
      ");
        }

        $check_query = xtc_db_query("show columns from admin_access like 'pi_clickandbuy%'");

        if (xtc_db_num_rows($check_query) == 0) {
            xtc_db_query("ALTER TABLE admin_access ADD pi_clickandbuy_cancel INT(1) NOT NULL DEFAULT '0'");
            xtc_db_query("ALTER TABLE admin_access ADD pi_clickandbuy_cancel_authorize INT(1) NOT NULL DEFAULT '0'");
            xtc_db_query("ALTER TABLE admin_access ADD pi_clickandbuy_credit INT(1) NOT NULL DEFAULT '0'");
            xtc_db_query("ALTER TABLE admin_access ADD pi_clickandbuy_details INT(1) NOT NULL DEFAULT '0'");
            xtc_db_query("ALTER TABLE admin_access ADD pi_clickandbuy_mms INT(1) NOT NULL DEFAULT '0'");
            xtc_db_query("ALTER TABLE admin_access ADD pi_clickandbuy_recurring_debit INT(1) NOT NULL DEFAULT '0'");
            xtc_db_query("ALTER TABLE admin_access ADD pi_clickandbuy_recurring INT(1) NOT NULL DEFAULT '0'");
            xtc_db_query("ALTER TABLE admin_access ADD pi_clickandbuy_refund INT(1) NOT NULL DEFAULT '0'");
            xtc_db_query("ALTER TABLE admin_access ADD piCabLang INT(1) NOT NULL DEFAULT '0'");
            xtc_db_query("ALTER TABLE admin_access ADD registrationSuccess INT(1) NOT NULL DEFAULT '0'");
            xtc_db_query("ALTER TABLE admin_access ADD merchantRegistrationCall INT(1) NOT NULL DEFAULT '0'");
            xtc_db_query("UPDATE admin_access SET pi_clickandbuy_cancel= '1', pi_clickandbuy_cancel_authorize = '1', pi_clickandbuy_credit= '1', pi_clickandbuy_details= '1', pi_clickandbuy_mms= '1', pi_clickandbuy_recurring_debit= '1', pi_clickandbuy_recurring= '1', pi_clickandbuy_refund= '1', piCabLang = '1', registrationSuccess = '1', merchantRegistrationCall = '1'  WHERE customers_id='1' OR customers_id='groups'");
        }

        xtc_db_query("INSERT INTO  " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_MERCHANT_ID', '', '6', '3', NOW())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_PROJECT_ID', '', '6', '3', NOW())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY', '', '6', '3', NOW())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY_MMS', '', '6', '3', NOW())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DESCRIPTION', 'Billing Agreement', '6', '3', NOW())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_NUMBER_LIMIT', '', '6', '3', NOW())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_AMOUNT', '', '6', '3', NOW())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_CURRENCY', '', '6', '3', NOW())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DATE_LIMIT', '', '6', '3', NOW())");
        xtc_db_query("INSERT INTO  " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_REVOKABLE', 'False', '6', '6', 'xtc_cfg_select_option(array(\'True\',\'False\'),', NOW())");
        xtc_db_query("INSERT INTO  " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_INITIAL_AMOUNT', 'False', '6', '6', 'xtc_cfg_select_option(array(\'True\',\'False\'),', NOW())");
        xtc_db_query("INSERT INTO  " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SPECIAL_CASE', 'None', '6', '6', 'xtc_cfg_select_option(array(\'None\',\'Fast Checkout\',\'Partial Delivery\'),', NOW())");
        xtc_db_query("INSERT INTO  " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SORT_ORDER', '0', '6', '0', now())");
        xtc_db_query("INSERT INTO  " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        xtc_db_query("INSERT INTO  " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, sort_order, use_function, set_function, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ORDER_STATUS_ID', '6', '0', 'xtc_get_order_status_name', 'xtc_cfg_pull_down_order_statuses(', NOW())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ALLOWED', '', '6', '0', now())");
        xtc_db_query("INSERT INTO  " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SANDBOX', 'False', '6', '6', 'xtc_cfg_select_option(array(\'True\',\'False\'),', NOW())");


    }

    /**
     * Remove all db entrys form the module
     */
    function remove() {
        xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    /**
     * Retrieve all module entrys
     *
     * @return array module entrys
     */
    function keys() {
        return array(
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_STATUS',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SANDBOX',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_MERCHANT_ID',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_PROJECT_ID',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY_MMS',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DESCRIPTION',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_NUMBER_LIMIT',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_AMOUNT',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_CURRENCY',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DATE_LIMIT',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_REVOKABLE',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_INITIAL_AMOUNT',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SPECIAL_CASE',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SORT_ORDER',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ALLOWED',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ZONE',
            'MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ORDER_STATUS_ID'
        );
    }

    /**
     * Generate a md5 hash from the given params
     *
     * @param float $amount
     * @param string $currency
     * @param string $externalID
     * @param string $additional_1
     * @param string $additional_2
     * @return string $shash
     */
    function generate_shash($amount, $currency, $externalID, $additional_1 = '', $additional_2 = '') {
        $shash = md5($amount . $currency . $externalID . $this->secret_key . $additional_1 . $additional_2);
        return $shash;
    }

}

?>