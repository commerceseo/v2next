<?php

/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2013-06-05 15:01:19 +0200 (Mi, 05 Jun 2013) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: sofort_lastschrift.php 420 2013-06-19 18:04:39Z akausch $
 */
require_once(DIR_FS_CATALOG . 'callback/sofort/sofort.php');
require_once(DIR_FS_CATALOG . 'callback/sofort/library/sofortLib.php');

class sofort_lastschrift extends sofort {

    function sofort_lastschrift() {
        global $order;

        parent::sofort();

        $this->_checkExistingSofortConstants('ls');
        $this->code = 'sofort_lastschrift';
        $this->title = MODULE_PAYMENT_SOFORT_LASTSCHRIFT_TEXT_TITLE_ADMIN;
        $this->title_extern = MODULE_PAYMENT_SOFORT_LS_TEXT_TITLE;
        $this->paymentMethod = 'LS';

        if (defined('MODULE_PAYMENT_SOFORT_LS_RECOMMENDED_PAYMENT') && MODULE_PAYMENT_SOFORT_LS_RECOMMENDED_PAYMENT == 'True')
            $this->title_extern .= ' ' . MODULE_PAYMENT_SOFORT_LS_RECOMMENDED_PAYMENT_TEXT;

        $this->enabled = ((defined('MODULE_PAYMENT_SOFORT_LS_STATUS') && MODULE_PAYMENT_SOFORT_LS_STATUS == 'True') ? true : false);
        $this->description = MODULE_PAYMENT_SOFORT_LS_TEXT_DESCRIPTION . '<br />' . MODULE_PAYMENT_SOFORT_MULTIPAY_VERSIONNUMBER . ': ' . HelperFunctions::getSofortmodulVersion();

        if ($this->_isInstalled() && !$this->_modulVersionCheck()) {
            $this->description = '<span style ="color:red; font-weight: bold; font-size: 1.2em">' . MODULE_PAYMENT_SOFORT_MULTIPAY_UPDATE_NOTICE . '</span><br /><br />' . $this->description;
        }

        $this->description .= MODULE_PAYMENT_SOFORT_LS_TEXT_DESCRIPTION_EXTRA;
        $this->sort_order = (defined('MODULE_PAYMENT_SOFORT_LS_SORT_ORDER') ? MODULE_PAYMENT_SOFORT_LS_SORT_ORDER : false);

        if (is_object($order))
            $this->update_status();

        if (defined('MODULE_PAYMENT_SOFORT_LS_STATUS')) {
            $this->sofort = new SofortLib_Multipay(MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY);
            $this->sofort->setVersion(HelperFunctions::getSofortmodulVersion());

            if (defined('MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED') && MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED == "True")
                $this->sofort->setLogEnabled();
        }
    }

    function selection() {
        global $order;

        if (!parent::selection()) {
            $this->sofort->log("Notice: Paymentmethod " . $this->code . " will be deactivated for selection.");
            $this->enabled = false;
            return false;
        }

        $title = MODULE_PAYMENT_SOFORT_MULTIPAY_JS_LIBS;

        $cost = '';

        if (array_key_exists('ot_sofort', $GLOBALS))
            $cost = $GLOBALS['ot_sofort']->get_percent($this->code, 'price');

        //if Gambio-Lightbox then show normal conditions-link
        $conditionsText = (defined('GM_LIGHTBOX_CHECKOUT') && function_exists('gm_get_conf') && gm_get_conf('GM_LIGHTBOX_CHECKOUT') == 'true') ? MODULE_PAYMENT_SOFORT_LS_CHECKOUT_CONDITIONS_WITH_LIGHTBOX : MODULE_PAYMENT_SOFORT_LS_CHECKOUT_CONDITIONS;

        $fields = array(
            array(
                'title' => MODULE_PAYMENT_SOFORT_LS_TEXT_HOLDER,
                'field' => xtc_draw_input_field('ls_sender_holder', array_key_exists('ls_sender_holder', $_SESSION['sofort']) ? strip_tags($_SESSION['sofort']['ls_sender_holder']) : strip_tags($order->billing['firstname'] . ' ' . $order->billing['lastname']))),
            array(
                'title' => MODULE_PAYMENT_SOFORT_LS_TEXT_ACCOUNT_NUMBER,
                'field' => xtc_draw_input_field('ls_account_number', array_key_exists('ls_account_number', $_SESSION['sofort']) ? strip_tags($_SESSION['sofort']['ls_account_number']) : '')),
            array(
                'title' => MODULE_PAYMENT_SOFORT_LS_TEXT_BANK_CODE,
                'field' => xtc_draw_input_field('ls_bank_code', array_key_exists('ls_bank_code', $_SESSION['sofort']) ? strip_tags($_SESSION['sofort']['ls_bank_code']) : '')),
            array(
                'title' => $conditionsText,
                'field' => xtc_draw_checkbox_field('sofort_conditions_ls', 'sofort_conditions_ls', false))
        );

        //commerce:SEO - Bugfix
        if (isset($_REQUEST['xajax']) && !empty($_REQUEST['xajax'])) {
            $fields[0]['title'] = utf8_decode($fields[0]['title']); //holder
            $fields[1]['title'] = utf8_decode($fields[1]['title']); //account-nr
            $fields[2]['title'] = utf8_decode($fields[2]['title']); //bankcode
            $fields[3]['title'] = utf8_decode($fields[3]['title']); //conditions

            return array('id' => $this->code, 'module' => utf8_decode($this->title_extern), 'fields' => $fields, 'description' => utf8_decode($title), 'module_cost' => utf8_decode($cost));
        } else {
            return array('id' => $this->code, 'module' => $this->title_extern, 'fields' => $fields, 'description' => $title, 'module_cost' => $cost);
        }
    }

    function pre_confirmation_check($vars = '') {
        parent::pre_confirmation_check($vars);

        //in CommerceSEO check is done with Ajax
        if (isset($_POST['xajax']) && $_POST['xajax'] == 'updatePaymentModule') {
            $isAjax = true;
            $requestData = $vars;
        } else {
            $isAjax = false;
            $requestData = $_POST;
        }

        $_SESSION['sofort']['ls_sender_holder'] = trim($requestData['ls_sender_holder']);
        $_SESSION['sofort']['ls_account_number'] = trim($requestData['ls_account_number']);
        $_SESSION['sofort']['ls_bank_code'] = trim($requestData['ls_bank_code']);
        $requestData['sofort_conditions_ls'] = trim($requestData['sofort_conditions_ls']);
        $_SESSION['sofort']['sofort_conditions_ls'] = isset($requestData['sofort_conditions_ls']) ? $requestData['sofort_conditions_ls'] : '';

        $errorCodes = array();
        $errorFound = false;
        $paymentAjaxErrorReturn = '';

        if ($_SESSION['sofort']['sofort_conditions_ls'] != 'sofort_conditions_ls') {
            $paymentAjaxErrorReturn = '&payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_10002);
            $errorFound = true;
            $errorCodes[] = '10002';
        }

        if (!$_SESSION['sofort']['ls_sender_holder'] || !$_SESSION['sofort']['ls_account_number'] || !$_SESSION['sofort']['ls_bank_code']) {
            $paymentAjaxErrorReturn = '&payment_error=' . $this->code .
                    '&error=' . urlencode(MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_10001) .
                    '&ls_sender_holder=' . urlencode($_SESSION['sofort']['ls_sender_holder']) .
                    '&ls_account_number=' . urlencode($_SESSION['sofort']['ls_account_number']) .
                    '&ls_bank_code=' . urlencode($_SESSION['sofort']['ls_bank_code']);
            $errorFound = true;
            $errorCodes[] = '10001';
        }

        if ($errorFound) {
            if ($isAjax) {
                $_SESSION['checkout_payment_error'] = $paymentAjaxErrorReturn;
            } else {
                $errorString = 'payment_error=' . $this->code . '&error_codes=' . implode(',', $errorCodes);
                $redirectUrl = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $errorString, 'SSL', true, false);
                xtc_redirect(HelperFunctions::cleanUrlParameter($redirectUrl));
            }
        }

        return false;
    }

    function install() {
        $sofortStatuses = $this->_insertAndReturnSofortStatus();
        $refundedStatus = (isset($sofortStatuses['refunded']) && !empty($sofortStatuses['refunded'])) ? $sofortStatuses['refunded'] : '';
        $unchangedStatus = (isset($sofortStatuses['unchanged']) && !empty($sofortStatuses['unchanged'])) ? $sofortStatuses['unchanged'] : '';
        $directDebitStatus = (isset($sofortStatuses['direct_debit']) && !empty($sofortStatuses['direct_debit'])) ? $sofortStatuses['direct_debit'] : '';
        $returnedDebitStatus = (isset($sofortStatuses['returned_debit']) && !empty($sofortStatuses['returned_debit'])) ? $sofortStatuses['returned_debit'] : '';

        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_LS_STATUS', 'False', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_SOFORT_LS_SORT_ORDER', '0', '6', '16', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_LS_RECOMMENDED_PAYMENT', 'False', '6', '5', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_SOFORT_LASTSCHRIFT_ALLOWED', '', '6', '12', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_LS_ZONE', '0', '6', '13', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");

        //"Best�tigt - Bisher kein Geldeingang" - pending-not_credited_yet
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_LS_PEN_NOT_CRE_YET_STATUS_ID', '" . HelperFunctions::escapeSql($directDebitStatus) . "',  '6', '35', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

        //"R�cklastschrift" - loss-rejected
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_LS_LOS_REJ_STATUS_ID', '" . HelperFunctions::escapeSql($returnedDebitStatus) . "',  '6', '35', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

        //"Geldeingang" - received-credited
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_LS_REC_CRE_STATUS_ID', '" . HelperFunctions::escapeSql($unchangedStatus) . "',  '6', '35', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

        //"Teilr�ckbuchung" - refunded-compensation
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_LS_REF_COM_STATUS_ID', '" . HelperFunctions::escapeSql($unchangedStatus) . "',  '6', '35', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

        //"Vollst�ndige R�ckbuchung" - refunded-refunded
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_LS_REF_REF_STATUS_ID', '" . HelperFunctions::escapeSql($refundedStatus) . "',  '6', '35', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

        //install shared keys, that are used by all/most multipay-modules
        parent::install();
    }

    function remove() {
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE_PAYMENT_SOFORT_LS%'");
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE_PAYMENT_SOFORT_LASTSCHRIFT%'");

        //if this is the last removing of a multipay-paymentmethod --> we also remove all shared keys, that are used by all/most multipay-modules
        parent::remove();
    }

    function keys() {
        parent::keys();

        return array(
            'MODULE_PAYMENT_SOFORT_LS_STATUS',
            'MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY',
            'MODULE_PAYMENT_SOFORT_MULTIPAY_AUTH',
            'MODULE_PAYMENT_SOFORT_LS_RECOMMENDED_PAYMENT',
            'MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_1',
            'MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_2',
            'MODULE_PAYMENT_SOFORT_LASTSCHRIFT_ALLOWED',
            'MODULE_PAYMENT_SOFORT_LS_ZONE',
            'MODULE_PAYMENT_SOFORT_LS_SORT_ORDER',
            'MODULE_PAYMENT_SOFORT_MULTIPAY_PROF_SETTINGS',
            'MODULE_PAYMENT_SOFORT_MULTIPAY_TEMP_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_MULTIPAY_ABORTED_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_LS_PEN_NOT_CRE_YET_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_LS_LOS_REJ_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_LS_REC_CRE_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_LS_REF_COM_STATUS_ID',
            'MODULE_PAYMENT_SOFORT_LS_REF_REF_STATUS_ID',
                //'MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED',
        );
    }

}

