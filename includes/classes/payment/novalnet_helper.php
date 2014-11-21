<?php
#########################################################
#                                                       #
#  This class file is used as a helper file             #
#  for all payment modules of Novalnet.                 #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_helper.php                         #
#                                                       #
#########################################################

include_once (DIR_FS_INC . 'xtc_format_price_order.inc.php');
include_once (DIR_FS_INC . 'xtc_validate_email.inc.php');

class novalnet_helper {

    var $redirect_method = array('novalnet_paypal','novalnet_ideal','novalnet_instantbanktransfer');
    var $form_method     = array('novalnet_cc','novalnet_sepa');

    /**  Assign the basic configuration values
    *
    *  @param  none
    *  @return none
    *
    */
    function __construct() {
        global $request_type;
            $this->ssl = ($request_type == 'SSL' ? 'https://' : 'http://');
            $this->paygate_url =  $this->ssl . 'payport.novalnet.de/paygate.jsp';
            $this->nn_info_port_url = $this->ssl . 'payport.novalnet.de/nn_infoport.xml';
            $this->online_transfer_url = $this->ssl.'payport.novalnet.de/online_transfer_payport';
            $this->novalnet_paypal_url =  $this->ssl .'payport.novalnet.de/paypal_payport';
            if(in_array($this->code,$this->redirect_method))
                $this->form_action_url = ($this->code != 'novalnet_paypal') ? $this->online_transfer_url : $this->novalnet_paypal_url;
            $this->cc3d_redirect_url = $this->ssl . 'payport.novalnet.de/global_pci_payport';
            $this->payment_config_str = 'MODULE_PAYMENT_' . strtoupper($this->code);
            $this->vendorid   = trim(constant($this->payment_config_str . '_VENDOR_ID'));
            $this->productid  = trim(constant($this->payment_config_str . '_PRODUCT_ID'));
            $this->authcode   = trim(constant($this->payment_config_str . '_AUTH_CODE'));
            $this->tariffid   = trim(constant($this->payment_config_str . '_TARIFF_ID'));
            $this->testmode   = (constant($this->payment_config_str . '_TEST_MODE') == 'True') ? 1 : 0;
            if ($this->code == 'novalnet_cc' && constant(MODULE_PAYMENT_NOVALNET_CC_3DSECURE_CHECK) == 'True') {
                $this->novalnet_cc3d = '1';
            }
            if (in_array($this->code, $this->redirect_method) || ($this->code == 'novalnet_cc' && $this->novalnet_cc3d)) {
                $this->payment_access_key = trim(constant($this->payment_config_str . '_PASSWORD'));
                if ($this->code == 'novalnet_paypal') {
                    $this->api_signature = trim(constant($this->payment_config_str . '_API_SIGNATURE'));
                    $this->api_user      = trim(constant($this->payment_config_str . '_API_USER'));
                    $this->api_password  = trim(constant($this->payment_config_str . '_API_PASSWORD'));
                }
            }
            if (in_array($this->code, $this->form_method)) {
                $this->auto_refill = ((constant($this->payment_config_str . '_AUTO_REFILL') == 'True') ? 1 : 0);
                $this->manual_check   = trim(constant($this->payment_config_str . '_MANUAL_CHECK_LIMIT'));
                $this->manual_check_limit   = preg_match('/^0+$/',$this->manual_check) ? 0 : $this->manual_check;
                $this->product_id2          = trim(constant($this->payment_config_str . '_PRODUCT_ID2'));
                $this->tariff_id2           = trim(constant($this->payment_config_str . '_TARIFF_ID2'));
            }
            $this->referrer_id = trim(constant($this->payment_config_str . '_REFERRER_ID'));
            $this->reference_1 = trim(constant($this->payment_config_str . '_REFERENCE1'));
            $this->reference_2 = trim(constant($this->payment_config_str . '_REFERENCE2'));
        }

    /**
     *  Check the payment configured Valid or not
     *
     *  @param  $data
     *  @return boolean
     *
     */
    function is_digit($value) {
       return preg_match('/^[0-9]+$/',$value);
    }
    /**
     *  Basic configuration validation
     *
     *  @param  none
     *  @return none
     *
     */

    function validate_basic_param() {
        $error='';
        if (!$this->is_digit($this->vendorid) || !$this->authcode || !$this->is_digit($this->productid) || !$this->is_digit($this->payment_key) || !$this->is_digit($this->tariffid) || (in_array($this->code,$this->redirect_method) && !$this->payment_access_key) || ($this->code == 'novalnet_cc' && $this->novalnet_cc3d == '1' && !$this->payment_access_key)) {
            $error = NOVALNET_TEXT_JS_NN_MISSING;
        }
        if(!empty($this->manual_check_limit) && empty($error)) {
          if($this->is_digit($this->manual_check_limit) && $this->manual_check_limit > 0) {
            if(!$this->is_digit($this->product_id2) || !$this->is_digit($this->tariff_id2)) {
                $error = NOVALNET_TEXT_JS_NN_ID2_MISSING;
            }
           } else {
              if(!$this->is_digit($this->manual_check_limit))
                 $error = NOVALNET_TEXT_JS_NN_ID2_MISSING;
           }
        }

        if ($error != '') {
            $this->error_redirect($error);
        }
    }

    /**
     *  Get manual check value
     *  @param  $manual_check_limit,
     *  @return $product_id,$tariff_id
     *
     */
    function assign_manual_check() {
        global $order;
        $order_amount = $_SESSION['novalnet'][$this->code.'_amount'];
        if ($this->manual_check_limit && $this->manual_check_limit > 0 && $order_amount >= $this->manual_check_limit) {
            $this->productid = $this->product_id2;
            $this->tariffid = $this->tariff_id2;
        }
    }

    /**
     *  Set curl request
     *
     *  @param  $nn_url
     *  @param  $urlparam
     *  @return array
     *
     */
    function perform_https_request($nn_url, $urlparams) {
        ## some prerquisites for the connection

        $ch = curl_init($nn_url);
        curl_setopt($ch, CURLOPT_POST, 1);  // a non-zero parameter tells the library to do a regular HTTP post.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlparams);  // add POST fields
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);  // don't allow redirects\
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // decomment it if you want to have effective ssl checking
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // decomment it if you want to have effective ssl checking
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 240);  // maximum time, in seconds, that you'll allow the CURL functions to take
        if ($this->proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        }
        ## establish connection
        $data = curl_exec($ch);
        ## determine if there were some problems on cURL execution
        $errno = curl_errno($ch);
        $errmsg = curl_error($ch);
        if ($errno < 0)
            $errno = 0;

        #close connection
        curl_close($ch);

        if ($errno or $errmsg) {
            ### Payment Gateway Error ###
            $error_message =  $errmsg . '(' . html_entity_decode($errno) . ')';
            $this->error_redirect($error_message);
        }
        return array($errno, $errmsg, $data);
    }

    /**
     *  Get merchant Information
     *
     *  @param  none
     *  @param  none
     *  @return array
     *
     */
    function get_common_params() {
        global $order, $customer_id;
        $shop_url       = (ENABLE_SSL ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG;
        $nn_customer_id = (isset($_SESSION['customer_id'])) ? $_SESSION['customer_id'] : '';
        $customer_query = xtc_db_query("SELECT customers_gender, customers_dob, customers_fax, customers_status FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" . (int) $nn_customer_id . "'");
        $customer       = xtc_db_fetch_array($customer_query);
        $shop_version   = xtc_db_fetch_array(xtc_db_query("SELECT version FROM database_version "));
        $version        = substr($shop_version['version'], strlen('commerce:SEO') );

        $nncustomer_no  = ($customer_query->fields['customers_status'] != 1) ? $nn_customer_id : NOVALNET_GUEST_USER;
        $customer_no    = (trim($order->customer['csID'])) ? $order->customer['csID'] : $nncustomer_no ;

        list($customer['customers_dob'], $extra) = explode(' ', $customer['customers_dob']);
        $amount   = $_SESSION['novalnet'][$this->code.'_amount'];
        $user_ip  = $this->get_real_ip();
        $user_ip  = ($user_ip == '::1')? '127.0.0.1' : $user_ip;
        $city     = !empty($order->customer['city']) ? $order->customer['city'] : $order->billing['city'];
        $postcode = !empty($order->customer['postcode']) ? $order->customer['postcode'] : $order->billing['postcode'];
        $street_address     = !empty($order->customer['street_address']) ? $order->customer['street_address'] : $order->billing['street_address'];
        $country_iso_code_2 = !empty($order->customer['country']['iso_code_2']) ? $order->customer['country']['iso_code_2'] : $order->billing['country']['iso_code_2'];
        $gender    = !empty($order->customer['gender']) ? $order->customer['gender'] : 'u';
        $firstname = !empty($order->customer['firstname']) ? $order->customer['firstname'] : $order->billing['firstname'];
        $lastname  = !empty($order->customer['lastname']) ? $order->customer['lastname'] : $order->billing['lastname'];
        $email     = !empty($order->customer['email_address']) ? $order->customer['email_address'] : $order->billing['email_address'];

        if(empty($firstname) || empty($lastname)) {
            $name = $firstname.$lastname;
            list($firstname, $lastname) = preg_match('/\s/', $name) ? explode(' ', $name, 2) : array($name,$name);
        }
        if(empty($firstname) || empty($lastname) || empty($email) || !xtc_validate_email($email)) {
            $error = NOVALNET_TEXT_ADDRESS_PARAMETERS_MISSING;
            $this->error_redirect($error);
        }
        $data = array(
            'auth_code'   => $this->authcode,
            'product'     => $this->productid,
            'tariff'      => $this->tariffid,
            'amount'      => $amount,
            'test_mode'   => $this->testmode,
                   );
        if(in_array($this->code,$this->redirect_method)) {
            $data['uniqid'] = uniqid();
            if ($this->code == 'novalnet_paypal') {
                $data['api_signature'] = $this->api_signature;
                $data['api_user']      = $this->api_user;
                $data['api_pw']        = $this->api_password;
            }
            $this->do_encoded_params($data);
        }

        if ($this->is_ajax && in_array($this->code,$this->redirect_method) ) {
            $firstname      = utf8_decode($firstname);
            $lastname       = utf8_decode($lastname);
            $email          = utf8_decode($email);
            $street_address = utf8_decode($street_address) ;
            $city           = utf8_decode($city) ;
        }

        $lang = NOVALNET_TEXT_LANG;
        $common_params = array(
                            'vendor'        => $this->vendorid,
                            'product'       => $data['product'],
                            'key'           => $this->payment_key,
                            'tariff'        => $data['tariff'],
                            'auth_code'     => $data['auth_code'],
                            'test_mode'     => $data['test_mode'],
                            'first_name'    => $firstname,
                            'last_name'     => $lastname,
                            'email'         => $email,
                            'currency'      => $order->info['currency'],
                            'amount'        => $data['amount'],
                            'street'        => $street_address,
                            'city'          => $city,
                            'zip'           => $postcode,
                            'country'       => $country_iso_code_2,
                            'country_code'  => $country_iso_code_2,
                            'search_in_street' => 1,
                            'tel'           => $order->customer['telephone'],
                            'remote_ip'     => $user_ip,
                            'gender'        => $gender,
                            'birth_date'    => $customer['customers_dob'],
                            'fax'           => $customer['customers_fax'],
                            'language'      => $lang,
                            'lang'          => $lang,
                            'customer_no'   => $customer_no,
                            'use_utf8'      => 1,
                            'system_name'   => 'commerce:SEO',
                            'system_version'=> $version . ' - NN1.1.1',
                            'system_url'    => xtc_href_link('', '', 'SSL', true, false),
                            'system_ip'     => ($_SERVER['SERVER_ADDR'] == '::1') ? '127.0.0.1' : $_SERVER['SERVER_ADDR']);

         if(in_array($this->code,$this->redirect_method) || ($this->code == 'novalnet_cc' && $this->novalnet_cc3d)) {

             $additional_param = array(
                            'return_url'          => xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL'),
                            'return_method'       => 'POST',
                            'error_return_url'    => xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL'),
                            'error_return_method' => 'POST',
                                 );
             if (!$this->novalnet_cc3d) {
                 $additional_param['user_variable_0'] = $shop_url;
                 $additional_param['hash']            = $this->make_hash($data);
                 $additional_param['uniqid']          = $data['uniqid'];
                 $additional_param['implementation']  = 'PHP';
                 if ($this->code == 'novalnet_paypal') {
                    $additional_param['api_signature']   = $data['api_signature'];
                    $additional_param['api_user']        = $data['api_user'];
                    $additional_param['api_pw']          = $data['api_pw'];
                }
            } else {
                $param['amount']  = $amount;
                $this->do_encoded_params($param);
                $additional_param['encoded_amount'] = $param['amount'];
            }
            $common_params = array_merge($common_params,$additional_param);
         }
         if($this->code =='novalnet_prepayment' || $this->code =='novalnet_invoice') {
             $common_params['invoice_type'] = ($this->code =='novalnet_prepayment') ? 'PREPAYMENT' : 'INVOICE';
             $invoice_duedate = $this->get_due_date();
             if(($this->code =='novalnet_invoice') && $invoice_duedate)
                $common_params['due_date'] = $invoice_duedate;
         }

         if (is_numeric($this->referrer_id)) {
             $common_params['referrer_id'] = $this->referrer_id;
         }
         if($this->reference_1) {
             $common_params['input1'] = 'reference1';
             $common_params['inputval1'] = strip_tags($this->reference_1);
         }
         if($this->reference_2 ) {
             $common_params['input2'] = 'reference2';
             $common_params['inputval2'] = strip_tags($this->reference_2);
         }
         return $common_params;
    }

    /**
     *  Set post back call
     *
     *  @param  $response
     *  @return none
     *
     */
    function send_postback_request($response) {
        global $insert_id;
        $trans_id = isset($response['tid']) ? $response['tid'] : '';
        if(($this->code == 'novalnet_tel') || ($this->code == 'novalnet_invoice') || ($this->code == 'novalnet_paypal' && $response['tid'] == 90))
        $trans_id  = $response;
        if(!in_array($this->code,$this->redirect_method))
        $response['amount'] =   $response['amount'] * 100;
        if($this->code != 'novalnet_tel')
        $this->insert_order_amount($response);
        $postback_params = array(
                         'vendor'    => $this->vendorid,
                         'auth_code' => $this->authcode,
                         'product'   => $this->productid,
                         'tariff'    => $this->tariffid,
                         'key'       => $this->payment_key,
                         'status'    => '100',
                         'tid'       => $trans_id,
                         'order_no'  => $insert_id
                            );

        if(in_array($this->code,array('novalnet_invoice', 'novalnet_prepayment'))) {
           $postback_params['invoice_ref'] = 'BNR-'.$this->productid.'-'.$insert_id;
        }
        if(!array_search('', $postback_params)) {
           if($this->is_digit($postback_params['vendor']) && $this->is_digit($postback_params['product']) &&
              $this->is_digit($postback_params['tariff']) && !empty($postback_params['auth_code'])
              && $this->is_digit($postback_params['tid'])) {
                    $server_response = $this->perform_https_request($this->paygate_url,$postback_params);
            }
            $this->do_unset();
        }
    }

    /**
     *  Get transaction comments
     *
     *  @param  $aryResponse
     *  @return String
     *
     */

    function prepare_comments($novalnet_response) {
       global $order;
       $old_comments = $order->info['comments'];
       $order->info['comments'] = '';
       $newlinebreak = "\n";

       $formatted_amount = xtc_format_price_order($novalnet_response['amount'] , 1, $order->info['currency']);
       ### final_comments ###
       $order->info['comments'] .= $old_comments;

       if ($this->get_test_mode($novalnet_response)) {
           $order->info['comments'] .= $newlinebreak . NOVALNET_TEST_ORDER_MESSAGE;
       }
       $order->info['comments'] .=  NOVALNET_TID_MESSAGE . ' ' .$novalnet_response['tid'] . $newlinebreak . $newlinebreak;
       if($this->code == 'novalnet_prepayment' || $this->code == 'novalnet_invoice') {
          $order->info['comments'] .= NOVALNET_TEXT_TRANSFER_INFO . $newlinebreak;
          $invoice_due_date = $this->get_due_date();
          if(($this->code =='novalnet_invoice') && $invoice_due_date)
              $order->info['comments'] .= NOVALNET_TEXT_DURATION_DUE_DATE. $invoice_due_date.$newlinebreak;
          $order->info['comments'] .= NOVALNET_TEXT_BANK_ACCOUNT_OWNER .' NOVALNET AG ' . $newlinebreak;
          $order->info['comments'] .= NOVALNET_TEXT_IBAN_TEXT . ' ' .$novalnet_response['invoice_iban'] . $newlinebreak;
          $order->info['comments'] .= NOVALNET_TEXT_BIC . ' ' .$novalnet_response['invoice_bic'] .$newlinebreak;
          $order->info['comments'] .= NOVALNET_TEXT_BANK_BANK .' '.$novalnet_response['invoice_bankname'] . ' '.trim($novalnet_response['invoice_bankplace']) . $newlinebreak;
          $order->info['comments'] .= NOVALNET_TEXT_AMOUNT . ' ' . $formatted_amount. $newlinebreak;
          $order->info['comments'] .= NOVALNET_REF_TID_MESSAGE.$novalnet_response['tid']. $newlinebreak . $newlinebreak;
          $order->info['comments'] = html_entity_decode($order->info['comments'], ENT_QUOTES, "UTF-8");
       }
    }
    /**
     *  Get do unset the session
     *
     *  @param  none
     *  @return none
     *
     */

    function do_unset() {
        if(isset($_SESSION['novalnet'])) {
            unset($_SESSION['novalnet']);
        }
    }

    function do_encoded_params(&$params = array()) {
        foreach($params as $key => $value) {
           $data = trim($value);
           if ($data == '')
               return'Error: no data';
           if (!function_exists('base64_encode') or !function_exists('pack') or !function_exists('crc32')) {
               return'Error: func n/a';
           }
           try {
               $crc = sprintf('%u', crc32($data)); # %u is a must for ccrc32 returns a signed value
               $data = $crc . "|" . $data;
               $data = bin2hex($data . $this->payment_access_key);
               $data = strrev(base64_encode($data));
           } catch (Exception $e) {
               echo('Error: ' . $e);
           }
           $params[$key] = $data;
        }
    }

    /**
    * Decode the params
    *
    * @param $data
    * @return none
    */

    function do_decode_params($data = '') {
        $data = trim($data);
        if ($data == '') {
            return'Error: no data';
        }
        if (!function_exists('base64_decode') or !function_exists('pack') or !function_exists('crc32')) {
            return'Error: func n/a';
        }
        try {
            $data = base64_decode(strrev($data));
            $data = pack("H" . strlen($data), $data);
            $data = substr($data, 0, stripos($data, $this->payment_access_key));
            $pos  = strpos($data, "|");
            if ($pos === false) {
                return("Error: CKSum not found!");
            }
            $crc = substr($data, 0, $pos);
            $value = trim(substr($data, $pos + 1));
            if ($crc != sprintf('%u', crc32($value))) {
                return("Error; CKSum invalid!");
            }
            return $value;
        } catch (Exception $e) {
            echo('Error: ' . $e);
        }
    }

    /**
    * To make hash value
    *
    * @param $request
    * @return Boolean
    */
    function make_hash($data) {
        if (!function_exists('md5')) {
            return'Error: func n/a';
        }
        return md5($data['auth_code'] . $data['product'] . $data['tariff'] . $data['amount'] . $data['test_mode'] . $data['uniqid'].strrev(trim($this->payment_access_key)));
    }

    /**
    * checkHash function
    *
    * @param $request
    * @return boolen
    */
    function check_hash($data ='') {
        if (!$data)
            return false;#'Error: no data';
        if ($data['hash2'] != $this->make_hash($data)) {
            return false;
        }
        return true;
    }

    /**
    * checkSecurity function
    *
    * @param $novalnet_response
    * @return none
    */
    function check_security($novalnet_response) {
        if ($novalnet_response['status'] == 100  || ($this->code == 'novalnet_paypal' && $novalnet_response['status'] == 90 )) {
            return true;
        } else {
            $this->do_unset();
            $error_message = (isset($novalnet_response['status_text']) ? $novalnet_response['status_text'] : $novalnet_response['status_desc']) . " (" . $novalnet_response['status'] . ")";
           $this->error_redirect($error_message);
        }
    }

    function validate_novalnet_response($server_response) {
        if($this->check_security($server_response)) {
            if (isset($server_response['hash2']) && isset($_SESSION['payment']) && $_SESSION['payment'] == $this->code) {
                if (!$this->check_hash($server_response)) {
                    $error = MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_HASH_ERROR;
                    $error_message = $server_response['status_text'] . (isset($error)?';'.utf8_encode($error):'');
                    if ($this->is_ajax) {
                        $_SESSION['checkout_payment_error'] = $error_message;
                    } else {
                        $this->error_redirect($error_message);
                    }
                }
            }
        }
    }

    /**
     *  Check the payment configured or not
     *
     *  @param  none
     *  @return none
     *
     */
    function check_configure() {
        if ($this->enabled && (!$this->is_digit($this->vendorid) || !$this->authcode || !$this->is_digit($this->tariffid) || !$this->is_digit($this->productid))) {
            $this->title .= '<br />' . NOVALNET_NOT_CONFIGURED;
        } else if(in_array($this->code,$this->redirect)) {
            if($this->enabled && !$this->payment_access_key || ($this->enabled && $this->code == 'novalnet_paypal' && (!$this->api_signature || !$this->api_user || !$this->api_pw))) {
               $this->title .= '<br />' .NOVALNET_NOT_CONFIGURED;
            }
        } else if ($this->enabled && $this->code == 'novalnet_cc' && $this->novalnet_cc3d && !$this->payment_access_key) {
            $this->title .= '<br />' .NOVALNET_NOT_CONFIGURED;
        }
        if ($this->testmode == '1') {
            $this->title .= '<br />' . NOVALNET_IN_TEST_MODE;
        }
    }

    /**
     *  To create table for callback execution
     *
     *  @param  none
     *  @return none
     *
     */
    function create_novalnet_callback_table() {
        xtc_db_query("CREATE TABLE IF NOT EXISTS `novalnet_callback` (
                    `ordernum` bigint(10) NOT NULL,
                    `callback_amount` varchar(15) NOT NULL,
                    `reference_tid` varchar(40) NOT NULL,
                    `callback_datetime` datetime NOT NULL,
                    `callback_tid` varchar(40) DEFAULT NULL,
                    `callback_log` text CHARACTER SET utf8,
                    KEY `ordernum` (`ordernum`),
                    KEY `callback_amount` (`callback_amount`),
                    KEY `reference_tid` (`reference_tid`),
                    KEY `callback_datetime` (`callback_datetime`),
                    KEY `callback_tid` (`callback_tid`));" );
    }

    /**
     * This is user defined function used for getting order amount in cents with tax
     *
     * @return int
     */
    public function get_order_total() {
        global $order;
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $total = $order->info['total'] + $order->info['tax'];
        } else {
            $total = $order->info['total'];
        }
        if (preg_match('/[^\d\.]/', $total) or !$total) {
            ### $amount contains some unallowed chars or empty ###
            $err = 'amount (' . $total . ') is empty or has a wrong format';
            $this->error_redirect($err);
        }
        $amount = sprintf('%0.2f', $total);
        $amount = preg_replace('/^0+/', '', $amount);
        $amount = str_replace('.', '', $amount);
        return $amount;
    }

     /* Error redirection function
     * Using SESSION to disply error message in AJAX single page.
     * */
     function error_redirect($error) {
        $error_message = 'payment_error=' . $this->code . '&error=' . urlencode($error);
        if ($this->is_ajax) {
            $_SESSION['checkout_payment_error'] = $error_message;
            if ($this->code == 'novalnet_cc' || $this->code == 'novalnet_tel')
               xtc_redirect(xtc_href_link(FILENAME_CHECKOUT, utf8_decode($error_message), 'SSL', true, false));
        } else {
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $error_message, 'SSL', true, false));
        }
    }
    
   /*
    * Method to return the error message
    *
    * @return array
    */
    function paymentErrrorMessage($aryResponse) { 
        $error = ($aryResponse['status_text'] != '') ? $aryResponse['status_text'] : (($aryResponse['status_desc'] != '') ? $aryResponse['status_desc'] : (($aryResponse['status_message'] != '') ? $aryResponse['status_message'] : MODULE_PAYMENT_NOVALNET_INVOICE_PAYMENT_MESSAGE) );
        return $error;
    }

    /**
     *  To get test mode
     *
     *  @param  $novalnet_response
     *  @return boolean
     *
     */
    function get_test_mode($novalnet_response) {
        $nn_test_mode = (((isset($novalnet_response['test_mode']) && $novalnet_response['test_mode'] == '1') || (isset($this->testmode) && $this->testmode == '1')) ? 1 : 0 );
        return $nn_test_mode;
    }
    /**
     *  To alter order table for novalnet
     *
     *  @param  none
     *  @return none
     *
     */
    function table_alter() {
        #Get the type of the comments field on TABLE_ORDERS
        $customer_query = xtc_db_query("SHOW COLUMNS FROM " . TABLE_ORDERS); # . " WHERE FIELD='comments'");#MySQL Version 3/4 dislike WHERE CLAUSE here :-(
        while ($customer = xtc_db_fetch_array($customer_query)) {
            if (strtolower($customer['Field']) == 'comments' and strtolower($customer['Type']) != 'text') {
                ### ALTER TABLE ORDERS modify the column comments ###
                xtc_db_query("ALTER TABLE " . TABLE_ORDERS . " MODIFY comments text");
            }
        }
    }
    /**
     *  To update order amount in callback table
     *
     *  @param  $response
     *  @return none
     *
     */
    function insert_order_amount($response) {
        global $insert_id;
        $order_total = isset($response['amount']) ? $response['amount'] : '0';
        if($this->code == 'novalnet_prepayment' || $this->code == 'novalnet_invoice' || ($this->code == 'novalnet_paypal' && $response['status'] == '90'))
         $order_total = '0';
        $trans_id = isset($response['tid']) ?  $response['tid'] : '';
        if($insert_id && $trans_id) {
        xtc_db_query('INSERT INTO `novalnet_callback` SET callback_log = "'.$_SERVER['REQUEST_URI'] .'", reference_tid = "'.$trans_id .'", callback_amount = "'.$order_total.'", ordernum = "'.$insert_id.' ", callback_datetime = "'.date('Y-m-d H:i:s').'"');
        }
    }
    /**
     *  To update order status
     *
     *  @param  $order_status
     *  @return none
     *
     */
    function update_order_status($order_status) {
        global $insert_id;
        if ($order_status) {
            xtc_db_query("UPDATE " . TABLE_ORDERS . "
                        SET orders_status='" . $order_status . "'
                        WHERE orders_id='" . $insert_id . "'");
        }

    }
    /**
     * Get real IPAddress
     *
     * @param none
     * @return string
     */
    function get_real_ip() {
        if ($this->is_public_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        if ($iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if ($this->is_public_ip($iplist[0]))
                return $iplist[0];
        }
        if ($this->is_public_ip($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];
        if ($this->is_public_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        if ($this->is_public_ip($_SERVER['HTTP_FORWARDED_FOR']))
            return $_SERVER['HTTP_FORWARDED_FOR'];
        return $_SERVER['REMOTE_ADDR'];
    }
    /**
     * Validate public IP
     *
     * @param $value
     * @return boolean
     */
    function is_public_ip($value) {
        if (!$value || count(explode('.', $value)) != 4)
            return false;
        return !preg_match('~^((0|10|172\.16|192\.168|169\.254|255|127\.0)\.)~', $value);
    }

    function check_shipping_method() {
        global $order;
        if (!$order->info['shipping_method']) {
            $payment_error_message = 'payment_error=' . $this->code . '&error=' . urlencode(utf8_encode(NOVALNET_REQUEST_FOR_CHOOSE_SHIPPING_METHOD));
            $_SESSION['checkout_payment_error'] = $payment_error_message;
            return;
        }

    }
    /**
     * To Validate Paypal API Details
     *
     * @param none
     * @return none
     */
    function validate_api_param() {

        if( !$this->api_signature ||  !$this->api_user  ||  !$this->api_password ) {
            $error = NOVALNET_TEXT_JS_NN_MISSING;
            $this->error_redirect($error);
        }
    }

    /*
     * Credit Card details validation function
     * return Error message
     */
    function cc_card_validation($nncc_values) { 
        if (empty($nncc_values['cc_type']) || empty($nncc_values['cc_owner']) || empty($nncc_values['cc_exp_month']) || empty($nncc_values['cc_exp_year']) || empty($nncc_values['cc_cid']) || empty($nncc_values['cc_panhash']) || empty($nncc_values['cc_uniqueid'])) { 
            $error =  MODULE_PAYMENT_NOVALNET_CC_TEXT_JS_COMMON_ERROR;
        } elseif (preg_match('/[%\#$!^<>=*]/', $nncc_values['cc_owner']) || ($nncc_values['cc_exp_year'] <= date('Y') && $nncc_values['cc_exp_month'] < date('m'))) { 
            $error = MODULE_PAYMENT_NOVALNET_CC_TEXT_JS_COMMON_ERROR;
        }
        if($error)
            return $error;

        return false;
    }
    /**
     * To get credit card Details
     *
     * @param $nncc_values
     * @param $cc_number
     * @param $uniqueid
     * @return array
     */
    function get_cc_params($nncc_values,$cc_number,$uniqueid) {
        $urlparam = array(
                    'cc_type' => $nncc_values['cc_type'],
                    'cc_holder' => $nncc_values['cc_owner'],
                    'cc_no' => '',
                    'cc_exp_month' => $nncc_values['cc_exp_month'],
                    'cc_exp_year' => $nncc_values['cc_exp_year'],
                    'cc_cvc2' => $nncc_values['cc_cid'],
                    'pan_hash' => $cc_number,
                    'unique_id' => $uniqueid);
         return $urlparam;
    }

    /**
     * To validate sepa bank Details
     *
     * @param $nnsepa_values
     * @return boolean
     */
    function validate_bank_details($nnsepa_values) {
      $due_date = trim(MODULE_PAYMENT_NOVALNET_SEPA_DUE_DATE);
       if($due_date !='' && (!preg_match('/^[0-9]+$/',$due_date) || (int)$due_date <= 6)) {
            $error = MODULE_PAYMENT_NOVALNET_SEPA_TEXT_DUE_DATE_ERROR;
        } elseif(empty($nnsepa_values['sepa_owner']) || empty($nnsepa_values['sepa_panhash']) || empty($nnsepa_values['sepa_uniqueid']) || preg_match('/[%\^<>=*]/', $nnsepa_values['sepa_owner'])) {
                    $error =  MODULE_PAYMENT_NOVALNET_SEPA_TEXT_DD_COMMON_ERROR;
        } elseif($nnsepa_values['sepa_confirm'] == 0 ) {
                    $error =  MODULE_PAYMENT_NOVALNET_SEPA_TEXT_JS_COMMON_ERROR;
        }
        if($error)
        return $error;

        return false;
    }
    /**
     * To check curl installation
     *
     * @param none
     * @return none
     */
    function check_curl() {
        if (!function_exists('curl_init')) {
            $error = NOVALNET_MODULE_CURL_MISSING;
                if ($error != '') {
                $payment_error_message = 'payment_error=' . $this->code . '&error=' . urlencode(html_entity_decode($error));
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_message, 'SSL', true, false));
            }
        }
   }
     /**
     * To get invoice due date
     *
     * @param none
     * @return date
     */
   function get_due_date()  {
      $payment_duration = trim(MODULE_PAYMENT_NOVALNET_INVOICE_DURATION);
      $payment_duration = str_replace(' ', '', $payment_duration);
      $due_date = '';
      $due_date_string = '';
      if(preg_match("/^[0-9]+$/", $payment_duration)) {
        $due_date = date("d.m.Y", mktime(0, 0, 0, date("m"), date("d") + $payment_duration, date("Y")));
        return  $due_date;
      } else {
        return false;
      }
   }
}
