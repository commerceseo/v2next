<?php
#########################################################
#                                                       #
#  TELEPHONE payment method class                       #
#  This module is used for real time processing of      #
#  TELEPHONE payment of customers.                      #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_tel.php                            #
#                                                       #
#########################################################
include_once DIR_FS_CATALOG . 'includes/classes/payment/novalnet_helper.php';
class novalnet_tel extends novalnet_helper {
    /**
    * Constructor
    *
    * @return void
    */
    function novalnet_tel() {
        global $order;
        $this->code        = 'novalnet_tel';
        $this->payment_key = '18';
        parent :: __construct();
        $this->logo_title  = MODULE_PAYMENT_NOVALNET_TEL_LOGO_TITLE;
        $this->payment_logo_title = MODULE_PAYMENT_NOVALNET_TEL_PAYMENT_LOGO_TITLE;
        $this->title       = MODULE_PAYMENT_NOVALNET_TEL_TEXT_TITLE.'<br>'.$this->logo_title.$this->payment_logo_title;
        $this->public_title= MODULE_PAYMENT_NOVALNET_TEL_TEXT_PUBLIC_TITLE;
        $this->description = MODULE_PAYMENT_NOVALNET_TEL_TEXT_DESCRIPTION;
        $this->sort_order  = MODULE_PAYMENT_NOVALNET_TEL_SORT_ORDER;
        $this->enabled     = ((MODULE_PAYMENT_NOVALNET_TEL_STATUS == 'True') ? true : false);
        $this->proxy       = trim(MODULE_PAYMENT_NOVALNET_TEL_PROXY);
        $this->image       = MODULE_PAYMENT_NOVALNET_TEL_LOGO_STATUS . MODULE_PAYMENT_NOVALNET_TEL_PAYMENT_LOGO_STATUS . '<br />';
        $this->check_configure();

        $this->is_ajax = (CHECKOUT_AJAX_STAT == 'true') ? true : false;
        if ((int) MODULE_PAYMENT_NOVALNET_TEL_COMPLETE_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_NOVALNET_TEL_COMPLETE_ORDER_STATUS_ID;
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

        if (($this->enabled == true) && ((int) MODULE_PAYMENT_NOVALNET_TEL_ZONE > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_TEL_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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

        $amount = $this->get_order_total();
        #check its a same order or not based on the order id if Novalnet tid is set
        if($_SESSION['novalnet']['nn_tid_tel']){
            if(isset($_COOKIE['cSEOid'])){
               if($_COOKIE['cSEOid'] != $_SESSION['cSEOid']){
                   unset($_SESSION['novalnet']['nn_tid_tel'], $_SESSION['novalnet']['server_amount_tel'], $_SESSION['novalnet']['novaltel_no']);
               }
            }
            $server_amount = str_replace('.','', $_SESSION['novalnet']['server_amount_tel']);
        }
        if($this->is_ajax || (!$this->is_ajax && empty($_SESSION['novalnet']['nn_tid_tel']) && strtoupper(MODULE_PAYMENT_NOVALNET_TEL_STATUS) == 'TRUE')) {
           $selection = array('id' => $this->code,
                             'module' => $this->public_title,
                             'fields' => array(array('title' => '', 'field' => str_replace('../', '', $this->image).$this->description))
                            );
        }else if(!$this->is_ajax && !empty($_SESSION['novalnet']['nn_tid_tel']) && $_SESSION['novalnet']['server_amount_tel']){
           $amount = $this->get_order_total();
           if(isset($_SESSION['novalnet']['first_call_amt']) && $_SESSION['novalnet']['first_call_amt'] != $amount) {
               unset($_SESSION['novalnet']['nn_tid_tel'], $_SESSION['novalnet']['first_call_amt']);
               $selection = array('id' => $this->code,
                              'module' => $this->public_title,
                              'fields' => array(array('title' => '', 'field' => str_replace('../', '', $this->image).$this->description),
                               array('title' => '',
                                     'field' => '<b>'.MODULE_PAYMENT_NOVALNET_TEL_AMOUNT_ERROR.'</b>')
                                    ));
           } else {
               $sess_tel = trim($_SESSION['novalnet']['novaltel_no']);
               $sess_tel = $this->session_tel($sess_tel);
               $selection = array('id' => $this->code,
                                 'module' => $this->public_title,
                                 'fields' => array(array('title' => '', 'field' => str_replace('../', '', $this->image).$this->description),
                                         array('title' => '',
                                               'field' => "<b>".MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP_INFO."</b>"),
                                         array('title' => MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP1,
                                               'field' => MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP1_DESC." <b>$sess_tel</b> <br /> ".MODULE_PAYMENT_NOVALNET_TEL_TEXT_COST_INFO.$_SESSION['novalnet']['server_amount_tel'].MODULE_PAYMENT_NOVALNET_TEL_TEXT_TAX_INFO),
                                              array('title' => MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP2,
                                                    'field' => MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP2_DESC)
                                 ));
            }
        }
        $selection['fields'][] = array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_TEL_INFO);
        $selection['fields'][] = array('title' => '', 'field' =>$mode);
        if (function_exists(get_percent)) {
            $selection['module_cost'] = $GLOBALS['ot_payment']->get_percent($this->code);
        }
        return $selection;
    }

    /*
    * Precheck to Evaluate the Bank Datas
    *
    * @return void
    */
    function pre_confirmation_check($vars = null) {
        global $order;
        $this->check_curl();
        $request = $_POST;
        if ($this->is_ajax) {
            $request = array_merge($request,$vars);
        }
        $this->check_shipping_method();
        $this->validate_basic_param();
        $amount = $this->get_order_total();
        if(isset($_SESSION['novalnet']['first_call_amt']) && $_SESSION['novalnet']['first_call_amt'] != $amount && $this->is_ajax) {
            $error = urlencode(MODULE_PAYMENT_NOVALNET_TEL_AMOUNT_ERROR);
            unset($_SESSION['novalnet']['nn_tid_tel'], $_SESSION['novalnet']['first_call_amt']);
            $this->error_redirection($error);
        }

        if(empty($error) && empty($_SESSION['novalnet']['nn_tid_tel']) && $this->is_ajax){#telephone first call
            if($amount < 99 || $amount > 1000){
                $this->error_redirection(MODULE_PAYMENT_NOVALNET_TEL_TEXT_AMOUNT_ERROR1);
            } else {
                $this->first_call($amount);
            }
        }
    }
     /*
    * To send first call request to novalnet
    *
    * @return void
    */
    function first_call($amount) {
        global $order;
        $errno  ='';
        $errmsg ='';
        $_SESSION['cSEOid'] = $_COOKIE['cSEOid'];
        $error = "";
        $this->validate_basic_param();
        $novalnet_request = $this->get_common_params();
        ### firstcall_server_request ###
        list($errno, $errmsg, $data) = $this->perform_https_request($this->paygate_url, $novalnet_request);
        if ($errno or $errmsg) {
            $this->error_redirection($errmsg);
        }
        $aryResponse = array();
        parse_str($data, $aryResponse);

        ### firstcall_server response ###
        if($aryResponse['status']==100 && $aryResponse['tid'])
        {
            $_SESSION['novalnet']['server_amount_tel'] = $aryResponse['amount'];
            $_SESSION['novalnet']['first_call_amt']    = $amount;
            $aryResponse['status_desc']='';
            if(!$_SESSION['novalnet']['nn_tid_tel'])
            {
                $_SESSION['novalnet']['nn_tid_tel']  = $aryResponse['tid'];
                $_SESSION['novalnet']['novaltel_no'] = $aryResponse['novaltel_number'];
                $_SESSION['novalnet']['test_mode']   = $aryResponse['test_mode'];
            }
        }
        elseif($aryResponse['status']==18){}
        elseif($aryResponse['status']==19)
        {
           $_SESSION['novalnet']['nn_tid_tel'] = '';
           $_SESSION['novalnet']['novaltel_no'] = '';
        }
        else {
           $error = $aryResponse['status_desc'];
        }
        if($aryResponse['status']==100) {
           if($this->is_ajax) {
               $sess_tel = $this->session_tel($_SESSION['novalnet']['novaltel_no']);
               $info     = '<br /> <b>' . MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP_INFO . '</b> <br />';
               $info    .= MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP1;
               $info    .= MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP1_DESC . '<b>' . $sess_tel . '</b> <br />';
               $info    .= MODULE_PAYMENT_NOVALNET_TEL_TEXT_COST_INFO . $_SESSION['novalnet']['server_amount_tel'] .' ';
               $info    .= MODULE_PAYMENT_NOVALNET_TEL_TEXT_TAX_INFO . '<br />';
               $info    .= MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP2;
               $info    .= MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP2_DESC;
               $error    = utf8_decode($info);
           } else{ $error = ' '; }
        }

        if( $error != '' ) {
            $error_message = 'payment_error=' . $this->code . '&error='.$error;
            if($aryResponse['status']==100) {
                $error_message = 'payment_error=' . $this->code . '&error=' .urlencode(MODULE_PAYMENT_NOVALNET_TEL_FIRST_CALL_NOTIFY);
            }
            if($this->is_ajax) {
               $error_message = 'payment_error=' . $this->code . '&error='.urlencode($error);
               $_SESSION['checkout_payment_error'] = $error_message;
            } else{
               xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $error_message, 'SSL', true, false));
            }
        }
    }

   /*
    * To concatenate display instruction
    *
    * @return string
    */
    function session_tel($sess_tel) {
       if($sess_tel) {
          $aryTelDigits = str_split($sess_tel, 4);
          $count = 0;
          $str_sess_tel = '';
          foreach ($aryTelDigits as $ind=>$digits) {
            $count++;
            $str_sess_tel .= $digits;
            if($count==1) $str_sess_tel .= '-';
            else $str_sess_tel .= ' ';
          }
          $str_sess_tel=trim($str_sess_tel);
          if($str_sess_tel) $sess_tel=$str_sess_tel;
       }
       return $sess_tel;
    }

    /**
     * Display Information on the Checkout Confirmation Page
     *
     *
     * @ return array
     */
    function confirmation() {

        return '';
    }

           /**
        * Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen.
        * These are hidden fields on the checkout confirmation page
        *
        * @ return string
        */
    function process_button($vars = null) {
        $_SESSION['novalnet']['novalnet_tel_amount'] = $this->get_order_total();
        $_SESSION['novalnet']['nn_total_amount_tel'] = $this->get_order_total();
        if(empty($_SESSION['novalnet']['nn_tid_tel']) && !$this->is_ajax && ($_SESSION['novalnet']['nn_total_amount_tel'] < 99 || $_SESSION['novalnet']['nn_total_amount_tel'] > 1000)) {
            $error_message = 'payment_error=' . $this->code . '&error=' . urlencode(utf8_encode(MODULE_PAYMENT_NOVALNET_TEL_TEXT_AMOUNT_ERROR1));
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $error_message, 'SSL', true, false));
        }
        return '';
    }

   /**
    *
    * This sends the data to the Novalnet
    *
    * @ param array vars
    *
    * @ return array
    */
    function before_process() {
        global $order;
        if ($this->order_status) {
            $order->info['order_status'] = $this->order_status;
        }

        if(empty($_SESSION['novalnet']['nn_tid_tel']) && !$this->is_ajax){#telephone first call
            $this->first_call($_SESSION['novalnet']['nn_total_amount_tel']);
        } elseif($_SESSION['novalnet']['nn_tid_tel']) {
            if($_SESSION['novalnet']['nn_tid_tel']){
                $server_amount =  $_SESSION['novalnet']['server_amount_tel'];
                $server_amount = str_replace('.','',$server_amount);
                $amount = $this->get_order_total();
            }
            if($server_amount !=  $_SESSION['novalnet']['nn_total_amount_tel']  &&  $_SESSION['novalnet']['nn_tid_tel'] ) {
                unset($_SESSION['novalnet']['nn_tid_tel'], $_SESSION['novalnet']['server_amount_tel'], $_SESSION['novalnet']['novaltel_no'], $_SESSION['novalnet']['nn_total_amount_tel']);
                $errmsg = MODULE_PAYMENT_NOVALNET_TEL_AMOUNT_ERROR;
                $this->error_redirection($errmsg);
            }
            $customer_query = xtc_db_query("SHOW COLUMNS FROM " . TABLE_ORDERS); # . " WHERE FIELD='comments'");#MySQL Version 3/4 dislike WHERE CLAUSE here :-(
            while ($customer = xtc_db_fetch_array($customer_query)) {
               if (strtolower($customer['Field']) == 'comments' and strtolower($customer['Type']) != 'text') {
                   xtc_db_query("ALTER TABLE " . TABLE_ORDERS . " MODIFY comments text");
               }
            }
            $lang = trim(MODULE_PAYMENT_NOVALNET_TEXT_LANG);
            $urlparam = '<?xml version="1.0" encoding="UTF-8"?><nnxml><info_request><vendor_id>'.$this->vendorid.'</vendor_id>';
            $urlparam .= '<vendor_authcode>'.$this->authcode.'</vendor_authcode>';
            $urlparam .= '<request_type>NOVALTEL_STATUS</request_type><tid>'.$_SESSION['novalnet']['nn_tid_tel'].'</tid>';
            $urlparam .= '<lang>'.$lang.'</lang></info_request></nnxml>';
            if(empty($this->vendorid) || empty($this->authcode) || empty($_SESSION['novalnet']['nn_tid_tel']) || empty($lang)){
                $error =  NOVALNET_TEXT_JS_NN_MISSING;
                unset($_SESSION['novalnet']['nn_tid_tel'], $_SESSION['novalnet']['server_amount_tel'], $_SESSION['novalnet']['novaltel_no'], $_SESSION['novalnet']['nn_total_amount_tel']);
                $this->error_redirection($error);
            } else {
                ### second_call_request ###
                list($errno, $errmsg, $data) = $this->perform_https_request($this->nn_info_port_url, $urlparam);
           }

            $aryResponse = array();
            parse_str($data , $aryResponse);

            if(strstr($data, '<novaltel_status>'))
            {
                preg_match('/novaltel_status>?([^<]+)/i', $data, $matches);
                $aryResponse['status'] = $matches[1];
                preg_match('/novaltel_status_message>?([^<]+)/i', $data, $matches);
                $aryResponse['status_desc'] = $matches[1];
            }
            ### second_call_response ###
            //Manual Testing
            //$aryResponse['status_desc'] = 'successfull';
            //$aryResponse['status'] = 100;

            if($_SESSION['novalnet']['nn_tid_tel']  && $aryResponse['status'] == 100){
                $old_comments = $order->info['comments'];
                $order->info['comments'] = '';
                $test_mode = $this->testmode;
                if ($this->order_status)
                    $order->info['order_status'] = $this->order_status;
                ### final_comments ###
                $test_order_status = (((isset($_SESSION['novalnet']['test_mode']) && $_SESSION['novalnet']['test_mode'] == 1) || (isset($this->testmode) && $this->testmode == 1)) ? 1 : 0 );
                if ( $test_order_status == 1){
                        $order->info['comments'] .= MODULE_PAYMENT_NOVALNET_TEST_ORDER_MESSAGE;
                }
                $newlinebreak = "\n";
                $order->info['comments'] .= MODULE_PAYMENT_NOVALNET_TEL_TID_MESSAGE . $_SESSION['novalnet']['nn_tid_tel'] . $newlinebreak;
                $order->info['comments']  = html_entity_decode($order->info['comments'], ENT_QUOTES, "UTF-8");
                $order->info['comments'] .= $old_comments;
                $_SESSION['novalnet']['novaltel_no'] = '';
            } else {    #### On payment failure ####
                $status = '';
                unset($_SESSION['novalnet']['test_mode']);

                if($wrong_amount == 1){
                        $status = '1';$aryResponse['status_desc'] = MODULE_PAYMENT_NOVALNET_TEL_TEXT_AMOUNT_ERROR1;
                } elseif ($aryResponse['status']==18){
                        $this->error_redirect($aryResponse['status_desc']);
                } elseif ($aryResponse['status']==19) {
                        $_SESSION['novalnet']['tid'] = '';
                        $_SESSION['novalnet']['novaltel_no'] = '';
                } else {
                        $this->error_redirect($aryResponse['status_desc']);
                }
            }
        }
    }
       /*
    * Sending the postback params to Novalnet
    * Updating to order details into Shop DB
    *
    * @ return boolean
    */
    function after_process() {
        $this->update_order_status($this->order_status);
        $tel_tid = isset($_SESSION['novalnet']['nn_tid_tel']) ? $_SESSION['novalnet']['nn_tid_tel'] : '';
        $this->send_postback_request($tel_tid);
        return false;
    }
    /*
    * Used to display error message details
    * function call at checkout_payment.php
    *
    * @ return array
    */
    function get_error() {
        $error = array('title' => MODULE_PAYMENT_NOVALNET_TEL_TEXT_ERROR, 'error' =>  stripslashes(urldecode($_GET['error'])));
        return $error;
    }


    /*
    * Check to see whether module is installed
    *
    * @ return boolean
    */
    function check() {

        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key =
            'MODULE_PAYMENT_NOVALNET_TEL_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    /*
    *
    * Install the payment module and its configuration settings
    *
    * @ return void
    */
    function install() {
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_TEL_ALLOWED', '', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)
        values ('MODULE_PAYMENT_NOVALNET_TEL_STATUS', 'False', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)
        values ('MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE', 'False', '6', '2', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID', '', '6', '3', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE', '', '6', '4', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_TEL_PRODUCT_ID', '', '6', '5', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_TEL_TARIFF_ID', '', '6', '6', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_TEL_INFO', '', '6', '7', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_TEL_SORT_ORDER', '0', '6', '8', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_NOVALNET_TEL_COMPLETE_ORDER_STATUS_ID', '0', '6', '9', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function,
        date_added) values ('MODULE_PAYMENT_NOVALNET_TEL_ZONE', '0', '6', '10', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_TEL_PROXY', '', '6', '11', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_TEL_REFERENCE1', '', '6', '12', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_TEL_REFERENCE2', '', '6', '13', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_TEL_REFERRER_ID', '', '6', '14', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_TEL_NOVALNET_LOGO_ACTIVE_MODE', 'True', '6', '15', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_TEL_PAYMENT_LOGO_ACTIVE_MODE', 'True', '6', '16', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
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
    * @ return array
    */
    function keys() {
        return array('MODULE_PAYMENT_NOVALNET_TEL_ALLOWED', 'MODULE_PAYMENT_NOVALNET_TEL_STATUS', 'MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE',
            'MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_TEL_PRODUCT_ID',
            'MODULE_PAYMENT_NOVALNET_TEL_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_TEL_INFO', 'MODULE_PAYMENT_NOVALNET_TEL_SORT_ORDER',
            'MODULE_PAYMENT_NOVALNET_TEL_COMPLETE_ORDER_STATUS_ID',  'MODULE_PAYMENT_NOVALNET_TEL_ZONE', 'MODULE_PAYMENT_NOVALNET_TEL_PROXY', 'MODULE_PAYMENT_NOVALNET_TEL_NOVALNET_LOGO_ACTIVE_MODE', 'MODULE_PAYMENT_NOVALNET_TEL_PAYMENT_LOGO_ACTIVE_MODE', 'MODULE_PAYMENT_NOVALNET_TEL_REFERENCE1', 'MODULE_PAYMENT_NOVALNET_TEL_REFERENCE2', 'MODULE_PAYMENT_NOVALNET_TEL_REFERRER_ID');
    }
    /*
    * Error redirection function
    * Using SESSION to disply error message in AJAX single page.
    * */
    function error_redirection($error) {
        $error_msg = 'payment_error=' . $this->code . '&error=' . urlencode($error);
        if ($this->is_ajax) {
            $_SESSION['checkout_payment_error'] = $error_msg;
        } else{
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $error_msg, 'SSL', true, false));
        }
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
