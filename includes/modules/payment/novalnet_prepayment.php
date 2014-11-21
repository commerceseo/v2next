<?php
#########################################################
#                                                       #
#  PREPAYMENT payment method class                      #
#  This module is used for real time processing of      #
#  PREPAYMENT payment of customers.                     #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_prepayment.php                     #
#                                                       #
#########################################################
include_once DIR_FS_CATALOG . 'includes/classes/payment/novalnet_helper.php';

class novalnet_prepayment extends novalnet_helper {
    /**
    * Constructor
    *
    * @return void
    */
    function novalnet_prepayment() {
        global $order;
        $this->code = 'novalnet_prepayment';
        $this->payment_key = '27';
        parent :: __construct();
        $this->logo_title = MODULE_PAYMENT_NOVALNET_PREPAYMENT_LOGO_TITLE;
        $this->payment_logo_title = MODULE_PAYMENT_NOVALNET_PREPAYMENT_PAYMENT_LOGO_TITLE;
        $this->title = MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_TITLE . '<br />' . $this->logo_title . $this->payment_logo_title;
        $this->public_title = MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_PUBLIC_TITLE;
        $this->description = MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_PAYMENT_NOVALNET_PREPAYMENT_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS == 'True') ? true : false);
        $this->proxy = trim(MODULE_PAYMENT_NOVALNET_PREPAYMENT_PROXY);
        $this->image = MODULE_PAYMENT_NOVALNET_PREPAYMENT_LOGO_STATUS . MODULE_PAYMENT_NOVALNET_PREPAYMENT_PAYMENT_LOGO_STATUS . '<br />';

        $this->check_configure();
        $this->is_ajax = (CHECKOUT_AJAX_STAT == 'true') ? true : false;

        if ((int) MODULE_PAYMENT_NOVALNET_PREPAYMENT_BEFORE_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_NOVALNET_PREPAYMENT_BEFORE_ORDER_STATUS_ID;
        }
        if (is_object($order)) {
            $this->update_status();
        }
    }

    /**
    * calculate zone matches and flag settings to determine whether this module should display to customers or not
    *
    * @return void
    */
    function update_status() {
    global $order;

        if (($this->enabled == true) && ((int) MODULE_PAYMENT_NOVALNET_PREPAYMENT_ZONE > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_PREPAYMENT_ZONE . "' and
            zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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

    /**
    * JS validation which does error-checking of data-entry if this module is selected for use
    * the fields to be checked are (Bank Owner, Bank Account Number and Bank Code Lengths)
    * currently this function is not in use
    *
    *
    * @return string
    */
    function javascript_validation() {
        return false;
    }

    /**
    * Builds set of fields for frontend
    *
    *
    * @return array
    */
    function selection() {
        global $order;

        $mode = ($this->testmode) ? NOVALNET_TEXT_TESTMODE_FRONT : '';

        $selection = array('id' => $this->code,
                    'module' => $this->public_title,
                    'fields' => array(array('title' => '', 'field' => str_replace('../', '', $this->image) . $this->description))
                    );

        $selection['fields'][] = array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_PREPAYMENT_INFO);
        $selection['fields'][] = array('title' => '', 'field' =>$mode);
        if (function_exists(get_percent)) {
            $selection['module_cost'] = $GLOBALS['ot_payment']->get_percent($this->code);
        }
        return $selection;
    }


    function pre_confirmation_check($vars = null) {
        $this->check_curl();
        $this->check_shipping_method();
        $this->validate_basic_param();
            if ($this->is_ajax) {
                $this->confirmation();
            }
     }

    function confirmation() {
        return '';
    }

    /**
     * Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen.
     * These are hidden fields on the checkout confirmation page
     *
     * @return string
     */
    function process_button($vars = null) {
            $_SESSION['novalnet']['novalnet_prepayment_amount'] = $this->get_order_total();
        return '';

    }

    /**
     *
     * This sends the data to the Novalnet
     *
     * @param array vars
     *
     * @return array
     */
    function before_process() {
        global $order;
        $error = '';
        if ($this->order_status) {
            $order->info['order_status'] = $this->order_status;
        }
        $common_params = $this->get_common_params();

        $this->novalnet_request = urldecode(http_build_query($common_params));
         ### firstcall_server_request ###
        list($errno, $errmsg, $data) = $this->perform_https_request($this->paygate_url, $this->novalnet_request);
        if ($errno or $errmsg) {
            ### Payment Gateway Error ###
            $this->error_redirect($errmsg);
        }
        #capture the result and message and other parameters from response data '$data' in an array
        parse_str($data, $this->novalnet_response);
        if ($this->novalnet_response['status'] == 100) {
            $this->prepare_comments($this->novalnet_response);
        } else {
            ### Passing through the Error Response from Novalnet's paygate into order-info ###
            $error = $this->novalnet_response['status_desc'];
            $this->error_redirect($error);
        }
        return;
    }

    /*
    * Sending the postback params to Novalnet
    * Updating to order details into  Sho DB
    *
    * @return boolean
    */
    function after_process() {
         global $order;
         $this->update_order_status($this->order_status);
         $this->send_postback_request($this->novalnet_response);
    }

    /*
    * Used to display error message details
    * function call at checkout_payment.php
    *
    *  @return array
    */
    function get_error() {
        if ($this->is_ajax) {
            unset($_SESSION['shipping']);
        }
        $error = array('title' => MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_ERROR, 'error' => stripslashes(urldecode($_GET['error'])));
        return $error;
    }

    /*
    * Check to see whether module is installed
    *
    *  @return boolean
    */
    function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key =
                                       'MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    /*
    * Install the payment module and its configuration settings
    *
    * @ return void
    */
    function install() {
        $this->table_alter();
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_ALLOWED', '', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)
        values ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS', 'False', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)
        values ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE', 'False', '6', '2', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_VENDOR_ID', '', '6', '3', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_AUTH_CODE', '', '6', '4', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_PRODUCT_ID', '', '6', '5', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TARIFF_ID', '', '6', '6', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_INFO', '', '6', '7', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_SORT_ORDER', '0', '6', '8', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function,
        date_added) values ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_BEFORE_ORDER_STATUS_ID', '0', '6', '9', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name',
        now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function,
        date_added) values ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_AFTER_ORDER_STATUS_ID', '0', '6', '10', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name',
        now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function,
        date_added) values ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_ZONE', '0', '6', '11', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_PROXY', '', '6', '12', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_REFERENCE1', '', '6', '13', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_REFERENCE2', '', '6', '14', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_REFERRER_ID', '', '6', '15', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)
        values ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_NOVALNET_LOGO_ACTIVE_MODE', 'True', '6', '16', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)
        values ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_PAYMENT_LOGO_ACTIVE_MODE', 'True', '6', '17', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $this->create_novalnet_callback_table();
        }

    /*
    *
    * Remove the module and all its settings
    * @ return void
    */
    function remove() {

        xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    /*
    * Internal list of configuration keys used for configuration of the module
    *
    * @return array
    */
    function keys() {

        return array('MODULE_PAYMENT_NOVALNET_PREPAYMENT_ALLOWED', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_INFO', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_SORT_ORDER', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_BEFORE_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_AFTER_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_ZONE', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_PROXY', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_NOVALNET_LOGO_ACTIVE_MODE', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_PAYMENT_LOGO_ACTIVE_MODE', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_REFERENCE1', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_REFERENCE2', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_REFERRER_ID');
    }

/*
order of functions:
selection              -> $order-info['total'] wrong, cause shipping_cost is net
pre_confirmation_check -> $order-info['total'] wrong, cause shipping_cost is net
confirmation           -> $order-info['total'] right, cause shipping_cost is gross
process_button         -> $order-info['total'] right, cause shipping_cost is gross
before_process         -> $order-info['total'] wrong, cause shipping_cost is net
after_process          -> $order-info['total'] right, cause shipping_cost is gross
*/
}
