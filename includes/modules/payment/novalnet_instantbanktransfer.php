<?php
#########################################################
#                                                       #
#  Sofortüberweisung / INSTANTBANKTRANSFER payment      #
#  method class                                         #
#  This module is used for real time processing of      #
#  German Bankdata of customers.                        #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_instantbanktransfer.php            #
#                                                       #
#########################################################
include_once DIR_FS_CATALOG . 'includes/classes/payment/novalnet_helper.php';
class novalnet_instantbanktransfer extends novalnet_helper {
        /**
        * Constructor
        *
        * @return void
        */
    function novalnet_instantbanktransfer() {
        global $order;
        $this->code         = 'novalnet_instantbanktransfer';
        $this->payment_key  = '33';
        parent :: __construct();
        $this->logo_title   = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_LOGO_TITLE;
        $this->payment_logo_title = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PAYMENT_LOGO_TITLE;
        $this->title        = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_TITLE.'<br />'.$this->logo_title.$this->payment_logo_title;
        $this->public_title = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_PUBLIC_TITLE;
        $this->description  = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_DESCRIPTION;
        $this->sort_order   = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_SORT_ORDER;
        $this->enabled      = ((MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_STATUS == 'True') ? true : false);
        $this->image        = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_LOGO_STATUS . MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PAY_LOGO_STATUS . '<br />';
        $this->proxy        = trim(MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PROXY);
        $this->check_configure();
        $this->is_ajax = (CHECKOUT_AJAX_STAT == 'true') ? true : false ;

        #check encoded data
        $server_response = $_REQUEST;
        if (isset($server_response['status']) && $_SESSION['payment'] == $this->code) {
             $this->validate_novalnet_response($server_response);
        }
        if ((int) MODULE_PAYMENT_NOVALNET_BANKTRANSFER_COMPLETE_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_NOVALNET_BANKTRANSFER_COMPLETE_ORDER_STATUS_ID;
        }
        if (is_object($order))
            $this->update_status();
    }

    /**
    * calculate zone matches and flag settings to determine whether this module should display to customers or not
    *
    * @return void
    */
    function update_status() {
        global $order;
        if (($this->enabled == true) && ((int) MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ZONE > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
    * the fields to be cheked are (Bank Owner, Bank Account Number and Bank Code Lengths)
    * currently this function is not in use
    *
    * @return string
    */
    function javascript_validation() {
        return false;
    }

    /**
    * Builds set of fields for frontend
    *
    * @return array
    */
    function selection() {
        global $order;
        if($this->testmode)
        $mode = NOVALNET_TEXT_TESTMODE_FRONT;
        else
        $mode = '';

        $selection = array('id' => $this->code,
            'module' => $this->public_title,
            'fields' => array(
                array('title' => '', 'field' => str_replace('../', '', $this->image).$this->description),
                array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_INFO),
                array('title' => '', 'field' =>$mode)
                ));
        if (function_exists(get_percent)) {
            $selection['module_cost'] = $GLOBALS['ot_payment']->get_percent($this->code);
        }
        return $selection;
    }

    /**
    * Precheck to Evaluate the Novalnet backend params
    *
    * @return void
    */
    function pre_confirmation_check($vars = null) {
        $this->check_curl();
        $this->check_shipping_method();
         $this->validate_basic_param();
    }

    /**
    * Display Information on the Checkout Confirmation Page
    *
    *
    * @return array
    */
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
        $process_button_string = '';
        $_SESSION['novalnet']['novalnet_instantbanktransfer_amount'] = $this->get_order_total();
        $common_params = $this->get_common_params($common_params);
        foreach($common_params as $key => $value) {
                $process_button_string  .=  xtc_draw_hidden_field($key, $value);
        }
        ### firstcall_server_request ###
        return $process_button_string;
    }

   /**
    * Checking the server Response
    *
    * @return void
    */
    function before_process() {
        global $order;
        ### firstcall_server response ###
        $this->novalnet_response = $_POST;
        ### firstcall_server response ###
        if ($this->novalnet_response['tid'] && $this->novalnet_response['status'] == '100') {
            $this->novalnet_response['amount'] =  $this->do_decode_params($this->novalnet_response['amount']);
            $this->novalnet_response['test_mode'] =  $this->do_decode_params($this->novalnet_response['test_mode']);
            if ($this->order_status)
                $order->info['order_status'] = $this->order_status;
                $this->prepare_comments($this->novalnet_response);
        }
        else {
            if ($this->is_ajax) {
                 $error_message = 'payment_error=' . $this->code . '&error=' . urlencode(utf8_decode($this->novalnet_response['status_text']));
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT, $error_message, 'SSL', true, false));
            } else {
                $error_message = $this->novalnet_response['status_text'];
                $this->error_redirect($error_message);
            }
        }
    }

    /**
    * Sending the postback params to Novalnet
    * Updating to order details into  Sho DB
    *
    * @return boolean
    */
    function after_process() {
        $this->update_order_status($this->order_status);
        $this->send_postback_request($this->novalnet_response);
    }

   /**
    * Used to display error message details
    * function call at checkout_payment.php
    *
    * @return array
    */
    function get_error() {
        if ($this->is_ajax) {
            unset($_SESSION['shipping']);
        }
        $error = array('title' => MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_ERROR, 'error' => stripslashes(urldecode($_GET['error'])));
        return $error;
    }

    /**
    * Check to see whether module is installed
    *
    * @return boolean
    */
    function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    /**
    *
    * Install the payment module and its configuration settings
    *
    * @ return void
    */
    function install() {
        $this->table_alter();
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ALLOWED', '', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_STATUS', 'False', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE', 'False', '6', '2', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PASSWORD', '', '6', '3', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_VENDOR_ID', '', '6', '4', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_AUTH_CODE', '', '6', '5', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PRODUCT_ID', '', '6', '6', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TARIFF_ID', '', '6', '7', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_SORT_ORDER', '0', '6', '8', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_NOVALNET_BANKTRANSFER_COMPLETE_ORDER_STATUS_ID', '0', '6', '9', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ZONE', '0', '6', '10', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_INFO', '', '6', '11', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PROXY', '', '6', '12', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_REFERENCE1', '', '6', '13', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_REFERENCE2', '', '6', '14', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_REFERRER_ID', '', '6', '15', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_NN_LOGO_MODE', 'True', '6', '16', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PAY_LOGO_MODE', 'True', '6', '17', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $this->create_novalnet_callback_table();

    }

    /**
    *
    * Remove the module and all its settings
    * @ return void
    */
    function remove() {
        xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    /**
    * Internal list of configuration keys used for configuration of the module
    *
    * @return array
    */
    function keys() {
        return array('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ALLOWED', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_STATUS',
            'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TARIFF_ID','MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PASSWORD',  'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_INFO', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_SORT_ORDER', 'MODULE_PAYMENT_NOVALNET_BANKTRANSFER_COMPLETE_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ZONE','MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PROXY', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_NN_LOGO_MODE', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PAY_LOGO_MODE', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_REFERENCE1', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_REFERENCE2', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_REFERRER_ID');
    }

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
