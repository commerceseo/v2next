<?php
/**
 * Barzahlen Payment Module (commerce:SEO)
 *
 * @copyright   Copyright (c) 2014 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-2.0  GNU General Public License, version 2 (GPL-2.0)
 */

require_once dirname(__FILE__) . '/barzahlen/loader.php';

class barzahlen
{
    /**
     * Constructor class, sets the settings.
     */
    function barzahlen()
    {
        $this->code = 'barzahlen';
        $this->version = '1.1.0';
        $this->title = MODULE_PAYMENT_BARZAHLEN_TEXT_TITLE;
        $this->description = '<div align="center">' . xtc_image('https://cdn.barzahlen.de/images/barzahlen_logo.png', MODULE_PAYMENT_BARZAHLEN_TEXT_TITLE) . '</div><br>' . MODULE_PAYMENT_BARZAHLEN_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_PAYMENT_BARZAHLEN_SORT_ORDER;
        $this->enabled = (MODULE_PAYMENT_BARZAHLEN_STATUS == 'True') ? true : false;
        $this->defaultCurrency = 'EUR';
        $this->tmpOrders = true;
        $this->tmpStatus = MODULE_PAYMENT_BARZAHLEN_NEW_STATUS;
        $this->form_action_url = '';

        $this->logFile = DIR_FS_CATALOG . 'logfiles/barzahlen.log';
        $this->currencies = array('EUR');
        $this->countries = array('DE');

        if ($this->check() && $this->checkLastAutoCancel()) {
            $this->autoCancel();
        }
    }

    /**
     * Settings update. Not used in this module.
     *
     * @return boolean
     */
    function update_status()
    {
        return false;
    }

    /**
     * Javascript code. Not used in this module.
     *
     * @return boolean
     */
    function javascript_validation()
    {
        return false;
    }

    /**
     * Sets information for checkout payment selection page.
     *
     * @return array with payment module information
     */
    function selection()
    {
        global $order;

        if(!in_array($order->customer['country']['iso_code_2'], $this->countries)) {
            return false;
        }

        if(!in_array($order->info['currency'], $this->currencies)) {
            return false;
        }

        if (!preg_match('/^[0-9]{1,3}(\.[0-9][0-9]?)?$/', MODULE_PAYMENT_BARZAHLEN_MAXORDERTOTAL)) {
            $this->bzLog('barzahlen/selection: Maximum order amount (' . MODULE_PAYMENT_BARZAHLEN_MAXORDERTOTAL . ') is not valid. Should be between 0.00 and 999.99 Euros.');
            return false;
        }

        if ($order->info['total'] <= MODULE_PAYMENT_BARZAHLEN_MAXORDERTOTAL) {
            $title = $this->title;

            $description = MODULE_PAYMENT_BARZAHLEN_TEXT_FRONTEND_DESCRIPTION . MODULE_PAYMENT_BARZAHLEN_TEXT_FRONTEND_PARTNER;
            for ($i = 1; $i <= 10; $i++) {
                $count = str_pad($i, 2, "0", STR_PAD_LEFT);
                $description .= '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_' . $count . '.png" alt="" style="vertical-align: middle; height: 25px;">';
            }
            $description .= '<script src="https://cdn.barzahlen.de/js/selection.js"></script>';

            return array('id' => $this->code, 'module' => $title, 'description' => $description);
        } else {
            return false;
        }
    }

    /**
     * Actions before confirmation. Not used in this module.
     *
     * @return boolean
     */
    function pre_confirmation_check()
    {
        return false;
    }

    /**
     * Payment method confirmation. Not used in this module.
     *
     * @return boolean
     */
    function confirmation()
    {
        return false;
    }

    /**
     * Module start via button. Not used in this module.
     *
     * @return boolean
     */
    function process_button()
    {
        return false;
    }

    /**
     * Before process. Not used in this module.
     *
     * @return boolean
     */
    function before_process()
    {
        return false;
    }

    /**
     * Payment process between final confirmation and success page.
     */
    function payment_action()
    {
        global $order, $insert_id;

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $total = $order->info['total'] + $order->info['tax'];
        } else {
            $total = $order->info['total'];
        }

        $payment = new Barzahlen_Request_Payment(
            $order->customer['email_address'],
            $order->customer['street_address'],
            $order->customer['postcode'],
            $order->customer['city'],
            $order->customer['country']['iso_code_2'],
            $total,
            $order->info['currency'],
            $insert_id
        );

        $api = $this->createApi();

        try {
            $api->handleRequest($payment);
        } catch (Exception $e) {
            $this->bzLog('barzahlen/payment: ' . $e);
        }

        if($payment->isValid()) {
            $_SESSION['infotext-1'] = $this->convertISO($payment->getInfotext1());

            xtc_db_query("UPDATE " . TABLE_ORDERS . "
                             SET barzahlen_transaction_id = '" . $payment->getTransactionId() . "' ,
                                 barzahlen_transaction_state = 'pending',
                                 orders_status = '" . MODULE_PAYMENT_BARZAHLEN_NEW_STATUS . "'
                           WHERE orders_id = '" . $insert_id . "'");

            $sql_data_history = array(
                'orders_id' => $insert_id,
                'orders_status_id' => MODULE_PAYMENT_BARZAHLEN_NEW_STATUS,
                'date_added' => 'now()',
                'customer_notified' => 1,
                'comments' => MODULE_PAYMENT_BARZAHLEN_TEXT_X_ATTEMPT_SUCCESS
            );
            xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_history);
        } else {
            xtc_db_query("UPDATE " . TABLE_ORDERS . "
                             SET orders_status = '" . MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS . "'
                           WHERE orders_id = '" . $insert_id . "'");

            $sql_data_history = array(
                'orders_id' => $insert_id,
                'orders_status_id' => MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS,
                'date_added' => 'now()',
                'customer_notified' => 1,
                'comments' => MODULE_PAYMENT_BARZAHLEN_TEXT_PAYMENT_ATTEMPT_FAILED
            );
            xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_history);
        }

        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PROCESS, session_name() . '=' . session_id(), 'SSL'));
    }

    /**
     * After process. Redirects user to payment selection if payment was not successful.
     *
     * @return boolean
     */
    function after_process()
    {
        global $insert_id;

        $query = xtc_db_query("SELECT barzahlen_transaction_id FROM " . TABLE_ORDERS . "
                                WHERE orders_id = '" . $insert_id . "'
                                  AND barzahlen_transaction_state = 'pending'  LIMIT 1");

        // if not, redirect to payment method selection
        if (xtc_db_num_rows($query) == 0) {
            // check if ajax checkout is enabled
            $query = xtc_db_query("SELECT configuration_id FROM " . TABLE_CONFIGURATION . "
                                    WHERE configuration_key = 'CHECKOUT_AJAX_STAT'
                                      AND configuration_value = 'true' LIMIT 1");

            if (xtc_db_num_rows($query) == 1) {
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT, 'payment_error=barzahlen', 'SSL'));
            } else {
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=barzahlen', 'SSL'));
            }
        }
        return false;
    }

    /**
     * Extracts and returns error.
     *
     * @return array with error information
     */
    function get_error()
    {
        $error = false;
        if (!empty($_GET['payment_error'])) {
            $error = array(
                'title' => MODULE_PAYMENT_BARZAHLEN_TEXT_ERROR,
                'error' => $this->convertISO(MODULE_PAYMENT_BARZAHLEN_TEXT_PAYMENT_ERROR)
            );
        }

        return $error;
    }

    /**
     * Error output. Not used in this module.
     *
     * @return boolean
     */
    function output_error()
    {
        return false;
    }

    /**
     * Checks if Barzahlen payment module is installed.
     *
     * @return integer
     */
    function check()
    {
        if (!isset($this->check)) {
            $check_query = xtc_db_query("SELECT configuration_value
                                           FROM " . TABLE_CONFIGURATION . "
                                          WHERE configuration_key = 'MODULE_PAYMENT_BARZAHLEN_STATUS'");
            $this->check = xtc_db_num_rows($check_query);
        }
        return $this->check;
    }

    /**
     * Install sql queries.
     */
    function install()
    {
        xtc_db_query(
            "INSERT INTO " . TABLE_CONFIGURATION . "
            (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)
            VALUES
            ('MODULE_PAYMENT_BARZAHLEN_STATUS', 'False', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now()),
            ('MODULE_PAYMENT_BARZAHLEN_SANDBOX', 'True', '6', '2', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now()),
            ('MODULE_PAYMENT_BARZAHLEN_DEBUG', 'False', '6', '12', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");

        xtc_db_query(
            "INSERT INTO " . TABLE_CONFIGURATION . "
            (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)
            VALUES
            ('MODULE_PAYMENT_BARZAHLEN_ALLOWED', 'DE', '6', '0', now()),
            ('MODULE_PAYMENT_BARZAHLEN_SHOPID', '', '6', '3', now()),
            ('MODULE_PAYMENT_BARZAHLEN_PAYMENTKEY', '', '6', '4', now()),
            ('MODULE_PAYMENT_BARZAHLEN_NOTIFICATIONKEY', '', '6', '5', now()),
            ('MODULE_PAYMENT_BARZAHLEN_MAXORDERTOTAL', '999.99', '6', '6', now()),
            ('MODULE_PAYMENT_BARZAHLEN_SORT_ORDER', '-1', '6', '11', now())");

        xtc_db_query(
            "INSERT INTO " . TABLE_CONFIGURATION . "
            (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added)
            VALUES
            ('MODULE_PAYMENT_BARZAHLEN_NEW_STATUS', '0', '6', '8', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
            ('MODULE_PAYMENT_BARZAHLEN_PAID_STATUS', '0', '6', '9', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
            ('MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS', '0', '6', '10', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

        $query = xtc_db_query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS
                                WHERE table_name = '" . TABLE_ORDERS . "'
                                  AND table_schema = '" . DB_DATABASE . "'
                                  AND column_name = 'barzahlen_transaction_id'");

        if (xtc_db_num_rows($query) == 0) {
            xtc_db_query("ALTER TABLE `" . TABLE_ORDERS . "` ADD `barzahlen_transaction_id` int(11) NOT NULL default '0';");
        }

        $query = xtc_db_query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS
                                WHERE table_name = '" . TABLE_ORDERS . "'
                                  AND table_schema = '" . DB_DATABASE . "'
                                  AND column_name = 'barzahlen_transaction_state'");

        if (xtc_db_num_rows($query) == 0) {
            xtc_db_query("ALTER TABLE `" . TABLE_ORDERS . "` ADD `barzahlen_transaction_state` varchar(15) NOT NULL default '';");
        } else {
            xtc_db_query("ALTER TABLE `" . TABLE_ORDERS . "` CHANGE `barzahlen_transaction_state` `barzahlen_transaction_state` varchar(15) NOT NULL default '';");
        }
    }

    /**
     * Uninstall sql queries.
     */
    function remove()
    {
        $parameters = $this->keys();
        $parameters[] = 'MODULE_PAYMENT_BARZAHLEN_ALLOWED';
        $parameters[] = 'MODULE_PAYMENT_BARZAHLEN_LAST_AUTO_CANCEL';
        $parameters[] = 'MODULE_PAYMENT_BARZAHLEN_LAST_UPDATE_CHECK';
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $parameters) . "')");
    }

    /**
     * All necessary configuration attributes for the payment module.
     *
     * @return array with configuration attributes
     */
    function keys()
    {
        return array(
            'MODULE_PAYMENT_BARZAHLEN_STATUS',
            'MODULE_PAYMENT_BARZAHLEN_SANDBOX',
            'MODULE_PAYMENT_BARZAHLEN_SHOPID',
            'MODULE_PAYMENT_BARZAHLEN_PAYMENTKEY',
            'MODULE_PAYMENT_BARZAHLEN_NOTIFICATIONKEY',
            'MODULE_PAYMENT_BARZAHLEN_MAXORDERTOTAL',
            'MODULE_PAYMENT_BARZAHLEN_NEW_STATUS',
            'MODULE_PAYMENT_BARZAHLEN_PAID_STATUS',
            'MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS',
            'MODULE_PAYMENT_BARZAHLEN_SORT_ORDER',
            'MODULE_PAYMENT_BARZAHLEN_DEBUG'
        );
    }

    /**
     * Gets settings and creates API object.
     *
     * @return Barzahlen_Api
     */
    function createApi()
    {
        $sandbox = MODULE_PAYMENT_BARZAHLEN_SANDBOX == 'True' ? true : false;
        $debug = MODULE_PAYMENT_BARZAHLEN_DEBUG == 'True' ? true : false;
        $api = new Barzahlen_Api(MODULE_PAYMENT_BARZAHLEN_SHOPID, MODULE_PAYMENT_BARZAHLEN_PAYMENTKEY, $sandbox);
        $api->setDebug($debug, $this->logFile);
        $api->setUserAgent(PROJECT_VERSION . ' / Plugin v' . $this->version);

        return $api;
    }

    /**
     * Checks if automatic cancelation run more than one hour ago.
     *
     * @return bool
     */
    function checkLastAutoCancel()
    {
        $lastQuery = xtc_db_query("SELECT configuration_value
                                     FROM " . TABLE_CONFIGURATION . "
                                    WHERE configuration_key = 'MODULE_PAYMENT_BARZAHLEN_LAST_AUTO_CANCEL'");
        $lastCheck = xtc_db_fetch_array($lastQuery);

        if(!$lastCheck) {
            $sql_data = array(
                'configuration_key' => 'MODULE_PAYMENT_BARZAHLEN_LAST_AUTO_CANCEL',
                'configuration_value' => 'now()',
                'configuration_group_id' => 6,
                'date_added' => 'now()'
            );
            xtc_db_perform(TABLE_CONFIGURATION, $sql_data);

            return true;
        }
        elseif ((time() - strtotime($lastCheck['configuration_value'])) > 3600) {
            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . "
                             SET configuration_value = NOW()
                           WHERE configuration_key = 'MODULE_PAYMENT_BARZAHLEN_LAST_AUTO_CANCEL'");

            return true;
        }

        return false;
    }

    /**
     * Automatic cancel payment slips.
     */
    function autoCancel()
    {
        $api = $this->createApi();

        $orders_query = xtc_db_query("SELECT orders_id, barzahlen_transaction_id
                                        FROM " . TABLE_ORDERS . "
                                       WHERE payment_method = 'barzahlen'
                                         AND orders_status = '" . MODULE_PAYMENT_BARZAHLEN_EXPIRED_STATUS . "'
                                         AND barzahlen_transaction_state = 'pending'");

        while ($orders = xtc_db_fetch_array($orders_query)) {
            $cancel = new Barzahlen_Request_Cancel($orders['barzahlen_transaction_id']);
            try {
                $api->handleRequest($cancel);
            } catch (Exception $e) {
                $this->bzLog('barzahlen/cancel: ' . $e);
            }

            xtc_db_query("UPDATE " . TABLE_ORDERS . "
                             SET barzahlen_transaction_state = 'canceled'
                           WHERE orders_id = '" . $orders['orders_id'] . "'");
        }
    }

    /**
     * Coverts text to iso-8859-15 encoding.
     *
     * @param string $text utf-8 text
     * @return string ISO-8859-15 text
     */
    function convertISO($text)
    {
        return mb_convert_encoding($text, 'iso-8859-15', 'utf-8');
    }

    /**
     * Logs errors into Barzahlen log file.
     *
     * @param string $message error message
     */
    function bzLog($message)
    {
        $time = date("[Y-m-d H:i:s] ");
        error_log($time . $message . "\r\r", 3, $this->logFile);
    }
}
