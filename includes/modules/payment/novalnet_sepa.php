<?php
#########################################################
#                                                       #
#  SEPA / SEPA payment method class                     #
#  This module is used for real time processing of      #
#  Sepa data of customers.                              #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_sepa.php                           #
#                                                       #
#########################################################
include_once DIR_FS_CATALOG . 'includes/classes/payment/novalnet_helper.php';

class novalnet_sepa extends novalnet_helper {
   /**
    * Constructor
    *
    * @return void
    */
    function novalnet_sepa() {
        global $order;
        $this->code = 'novalnet_sepa';
        $this->payment_key = '37';
        parent :: __construct();
        $this->logo_title = MODULE_PAYMENT_NOVALNET_SEPA_LOGO_TITLE;
        $this->payment_logo_title = MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_LOGO_TITLE;
        $this->title = MODULE_PAYMENT_NOVALNET_SEPA_TEXT_TITLE.'<br />'.$this->logo_title . $this->payment_logo_title;
        $this->public_title = MODULE_PAYMENT_NOVALNET_SEPA_TEXT_PUBLIC_TITLE;
        $this->sepa_description = MODULE_PAYMENT_NOVALNET_SEPA_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_PAYMENT_NOVALNET_SEPA_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_NOVALNET_SEPA_STATUS == 'True') ? true : false);
        $this->proxy = trim(MODULE_PAYMENT_NOVALNET_SEPA_PROXY);
        $this->image    = MODULE_PAYMENT_NOVALNET_SEPA_LOGO_STATUS . MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_LOGO_STATUS . '<br />';
        $this->check_configure();
        $this->is_ajax = (CHECKOUT_AJAX_STAT == 'true') ? true : false;
        $this->sepa_pin_amount = trim(MODULE_PAYMENT_NOVALNET_SEPA_PIN_BY_CALLBACK_MIN_LIMIT);

        $this->isActivatedCallback = false;
        if (MODULE_NN_SEPA_PIN != 'False') {
            $this->isActivatedCallback = true;
        }
        
        if (isset($_SESSION['novalnet']['nn_tid_sepa']) && $this->isActivatedCallback) {
            if (isset($_SESSION['novalnet']['max_time_sepa']) && time() > $_SESSION['novalnet']['max_time_sepa']) {
                unset($_SESSION['novalnet']['nn_tid_sepa'], $_SESSION['novalnet']['max_time_sepa'], $_SESSION['novalnet']['sepa_nonajax_details']);
                $error_message = 'payment_error=' . $this->code . '&error=' . urlencode(utf8_encode(NOVALNET_PIN_BY_CALLBACK_SESSION_ERROR));
                if ($this->is_ajax) {
                    $_SESSION['checkout_payment_error'] = $error_message;
                    return;
                } else {
                    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $error_message, 'SSL', true, false));
                }
            } 
            if ($_GET['new_novalnet_sepa_pin'] == 'true') {
                $_SESSION['novalnet']['new_novalnet_sepa_pin'] = true;
                $response = $this->secondCall(); 
                if ($response['status'] != 100) {
                    $error_msg = $this->paymentErrrorMessage($response);
                    $error_message = 'payment_error=' . $this->code . '&error=' . urlencode($error_msg);
                    if ($response['status'] == '0529006' ) {
                        $_SESSION['novalnet']['nn_sepa_pin_max_exceed'] = true;
                    }
                    if ($this->is_ajax) {
                        $_SESSION['novalnet']['checkout_payment_error'] = $error_message;
                    } else {
                        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $error_message, 'SSL', true, false));
                    }
                }
            }
        }
        if ((int) MODULE_PAYMENT_NOVALNET_SEPA_COMPLETE_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_NOVALNET_SEPA_COMPLETE_ORDER_STATUS_ID;
        }
        if (is_object($order))
            $this->update_status();
    }
    /**
    * calculate zone matches and flag settings to determine whether this module should display to customers or not
    * @return void
    */
    function update_status() {
        global $order;
        if (($this->enabled == true) && ((int)MODULE_PAYMENT_NOVALNET_SEPA_ZONE > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_SEPA_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
    * Validation for form fields if necessary
    * @return bool
    */
    function javascript_validation() {
        return false;
    }

    /**
    * Builds set of input fields for collecting Bankdetail info
    * @return string
    */
    function selection() { 
        global $order,$request_type;
        if($this->is_ajax){
            $form_id = 'form_payment_modules';
            $height = 430;
            $width =  '100%';
            $val = 1;
        } else {
            $form_id = 'checkout_payment';
            $height = 450;
            $width =  550;
            $val = 0;
        }
        require_once DIR_FS_CATALOG . 'novalnet_css_link.php';
        $mode = ($this->testmode) ? html_entity_decode(NOVALNET_TEXT_TESTMODE_FRONT) : '';
        $_SESSION['novalnet']['sepa_pin_amount'] = $order->info['total'] * 100;
        $this->isActivatedCallback = ($this->isActivatedCallback && ($this->sepa_pin_amount <= (string)$_SESSION['novalnet']['sepa_pin_amount'])) ? true : false;
        
        $cust_name = $order->customer['firstname']. ' '.$order->customer['lastname'];
        $payment_params =  array(
                           'type'        => $this->code,
                           'lang'        => MODULE_PAYMENT_NOVALNET_SEPA_TEXT_LANG,
                           'payment_id'  => $this->payment_key,
                           'country'     => $order->customer['country']['iso_code_2'],
                           'name'        => $order->customer['firstname']. ' '.$order->customer['lastname'],
                           'address'     => $order->customer['street_address'],
                           'company'     => $order->customer['company'],
                           'zip'         => $order->customer['postcode'],
                           'city'        => $order->customer['city'],
                           'email'       => $order->customer['email_address']
                               );
        $payment_params = http_build_query($payment_params);

        if(($this->isActivatedCallback || $this->auto_refill) && isset($_SESSION['novalnet']['nn_auto_refill']) && $_SESSION['novalnet']['nn_auto_refill'] == $this->code && $_SESSION['payment'] == $this->code) {
            $nnsepa_values = ($this->is_ajax) ? $this->deformatNvp($_SESSION['novalnet']['sepa_ajax_details']) : $_SESSION['novalnet']['sepa_nonajax_details'];
            $nnsepa_values['fldVdr'] = !empty($nnsepa_values['sepa_panhash']) ? $nnsepa_values['fldVdr'] : '' ;
            $payment_params .= '&panhash='.$nnsepa_values['sepa_panhash'];
            $payment_params .= '&fldVdr='.$nnsepa_values['fldVdr'];
        } else {
            unset($_SESSION['novalnet']['sepa_ajax_details'], $_SESSION['novalnet']['sepa_nonajax_details']);
            $payment_params .= '&panhash=';
            $payment_params .= '&fldVdr=';
        }
        $hidden_form_values = xtc_draw_hidden_field('sepa_panhash', '') . xtc_draw_hidden_field('sepa_uniqueid', '');
        $url_base_path = (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG;
        $sepa_script = $url_base_path . 'includes/modules/payment/novalnet.js';
        $loading_image = $url_base_path . DIR_WS_IMAGES . 'icons/novalnet/novalnet-loading-icon.gif';

        if(!$this->is_digit($this->vendorid) || empty($this->authcode) || !$this->is_digit($this->productid) || !$this->payment_key || !$order->customer['country']['iso_code_2'] || !$order->customer['street_address']  || !$order->customer['postcode'] || !$order->customer['email_address'] ||  !$order->customer['city'] || !$cust_name )
             $error = NOVALNET_TEXT_JS_NN_MISSING;
        if($error) {
            $selection = array('id' => $this->code,
                         'module' => $this->public_title,
                         'fields' => array(
                                                        array('title' => '','field' =>str_replace('../','',$this->image). ($this->sepa_description )),
                                                        array('title' => '','field' => '<div style="color:red">'.$error.'</div>')
                                                )
                        );
         } else {
            $sepa_pinby = array(); 
            $sepa_additional_pinby = array(); 
            if ($this->isActivatedCallback ) {
              if (!isset($_SESSION['novalnet']['nn_tid_sepa'])) {
                switch(MODULE_NN_SEPA_PIN) {
                    case 'E-Mail' :
                            $sepa_pinby = array('title' => MODULE_PAYMENT_NOVALNET_SEPA_PIN_BY_CALLBACK_EMAIL, 'field' => xtc_draw_input_field('sepa_user_email', '', 'id="' . $this->code . '-callback" AUTOCOMPLETE=OFF' ));
                            break;
                    case  'Callback' :
                            $sepa_pinby = array('title' => MODULE_PAYMENT_NOVALNET_SEPA_PIN_BY_CALLBACK_SMS_TEL, 'field' => xtc_draw_input_field('sepa_user_tel', '', 'id="' . $this->code . '-callback" AUTOCOMPLETE=OFF' ));
                            break;
                    case 'SMS' :
                            $sepa_pinby = array('title' => MODULE_PAYMENT_NOVALNET_SEPA_PIN_BY_CALLBACK_SMS_MOB, 'field' => xtc_draw_input_field('sepa_user_tel', '', 'id="' . $this->code . '-callback" AUTOCOMPLETE=OFF' ));
                            break;
                    default :
                            break;
               }
              } else {
                switch (MODULE_NN_SEPA_PIN) {
                   case 'E-Mail' :
                        $sepa_pinby = array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_SEPA_EMAIL_INPUT_REQUEST_DESC);
                        break;
                    case 'Callback':
                    case 'SMS':
                        // Show PIN field, after first call
                        $sepa_pinby = array('title' => MODULE_PAYMENT_NOVALNET_SEPA_PIN_INPUT_REQUEST_DESC, 'field' => xtc_draw_input_field('novalnet_sepa_pin', '', 'id="' . $this->code . '-callback" AUTOCOMPLETE=OFF ' ));
                        $sepa_additional_pinby = array('title' => '', 'field' => '<a href="' . xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'new_novalnet_sepa_pin=true', 'SSL', true, false) . '">' . MODULE_PAYMENT_NOVALNET_SEPA_PIN_BY_CALLBACK_SMS_NEW_PIN . '</a>');
                        break;
                    default :
                            break;
                }
              }
            }
            $selection = array('id' => $this->code,
                           'module' => $this->public_title,
                           'fields' => array(
                                                       array('title' => '', 'field' =>str_replace('../','',$this->image). ($this->sepa_description )),
                                                       array('field' => '<span id="sepa_loading"><img src="'.$loading_image.'" alt="Novalnet AG" /></span>'),
                                                       array('field' => '<iframe id="payment_form_novalnetSEPA" width="'.$width.'" scrolling="0" height="'.$height.'" name="payment_form_novalnetSEPA"  src="' . xtc_href_link('novalnet_iframe_form.php', '', 'SSL', true, false).'?'.$payment_params . '" onload="getFormValueForSEPA()" frameBorder="0"></iframe>'
                                                       . xtc_draw_hidden_field('payment_process',$val)
                                                       . xtc_draw_hidden_field('sepa_owner', '')
                                                       . xtc_draw_hidden_field('sepa_confirm', '')
                                                       . $hidden_form_values
                                                       . xtc_draw_hidden_field('fldVdr', '')
                                                       /*DIRECT DEBIT SEPA form control*/
                                                       .xtc_draw_hidden_field('original_sepa_customstyle_css', (defined('NOVALNET_SEPA_CUSTOM_CSS') ? NOVALNET_SEPA_CUSTOM_CSS : ''))
                                                       .xtc_draw_hidden_field('original_sepa_customstyle_cssval', (defined('NOVALNET_SEPA_CUSTOM_CSS_STYLE') ? NOVALNET_SEPA_CUSTOM_CSS_STYLE : ''))
                                                       . xtc_draw_hidden_field('original_iframeparent_submit_btn','')
                                                       .'<script src="' . $sepa_script . '" type="text/javascript"></script>'),
                                                       array('title' => $sepa_pinby['title'], 'field' => $sepa_pinby['field']),
                                                       array('title' => $sepa_additional_pinby['title'], 'field' => $sepa_additional_pinby['field']),
                                                       array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_SEPA_INFO),
                                                       array('title' => '', 'field' =>$mode)
                                                ));
        }
        
        if (function_exists(get_percent)) {
            $selection['module_cost'] = $GLOBALS['ot_payment']->get_percent($this->code);
        }
        
        if ($this->isActivatedCallback && $_SESSION['novalnet']['nn_sepa_pin_max_exceed']) {
            return '';
        }
        return $selection;
    }

    /**
    * Precheck to Evaluate the Bank Datas
    * @return string
    */
    function pre_confirmation_check($vars = null) {
        global $order;
        $this->check_curl();
        $this->isActivatedCallback = ($this->isActivatedCallback && ($this->sepa_pin_amount <= (string)$_SESSION['novalnet']['sepa_pin_amount'])) ? true : false;
        $request = $_REQUEST;
        if ($this->is_ajax) {
            $request = array_merge($request, $vars);
        }
        $this->check_shipping_method();
        //storing the sepa details in the session for ajax method
        if($this->is_ajax) {
            $_SESSION['novalnet']['sepa_ajax_details'] = $request['xajaxargs'][0];
            $nnsepa_values = $this->deformatNvp($_SESSION['novalnet']['sepa_ajax_details']);
        } else {
            $nnsepa_values = $_SESSION['novalnet']['sepa_nonajax_details'] =  $request;
        }

        if($this->auto_refill || $this->isActivatedCallback) {
            $_SESSION['novalnet']['nn_auto_refill'] = $this->code;
        }
        
        if (isset($request['sepa_user_tel'])) {
            $request['sepa_user_tel'] = trim($request['sepa_user_tel']);
        }
        if (isset($request['sepa_user_email'])) {
            $request['sepa_user_email'] = trim($request['sepa_user_email']);
        }
        if (isset($request['novalnet_sepa_pin'])) {
            $request['novalnet_sepa_pin'] = trim($request['novalnet_sepa_pin']);
        }
        
        $this->validate_basic_param();
        $error = $this->validate_bank_details($nnsepa_values);
        
        if ($this->isActivatedCallback && $error == "") {
            if (isset($request['novalnet_sepa_pin']) && isset($_SESSION['novalnet']['nn_tid_sepa'])) {
                // check pin
                if (empty($request['novalnet_sepa_pin']) || !preg_match('/^[0-9]+$/', $request['novalnet_sepa_pin'])) { 
                    $this->error_redirect(MODULE_PAYMENT_NOVALNET_SEPA_PIN_BY_CALLBACK_SMS_PIN_NOTVALID);
                } else {
                    if ($request['novalnet_sepa_pin']) {
                        $_SESSION['novalnet']['novalnet_sepa_pin'] = $request['novalnet_sepa_pin'];
                    }
                }
            } else{
               //checking Email and Telephone number 
               if (isset($request['sepa_user_email']) && !xtc_validate_email($request['sepa_user_email'])) {
                   $error = utf8_decode(MODULE_PAYMENT_NOVALNET_SEPA_PIN_BY_CALLBACK_EMAIL_NOTVALID);
               } else if (isset($request['sepa_user_tel']) && (strlen($request['sepa_user_tel']) < 8 || !is_numeric($request['sepa_user_tel']))) {
                   $error = utf8_decode(MODULE_PAYMENT_NOVALNET_SEPA_PIN_BY_CALLBACK_SMS_TEL_NOTVALID);
               }
           }
        }
        
        if($error != '') {
           $this->error_redirect($error);
        } else {
            if (isset($request['sepa_user_tel'])) {
                $_SESSION['novalnet']['sepa_user_tel'] = $request['sepa_user_tel'];
            }
            if (isset($request['sepa_user_email'])) {
                $_SESSION['novalnet']['sepa_user_email'] = $request['sepa_user_email'];
            }
            if ($this->is_ajax) {
                $this->confirmation();
            }
        }
    }

    /**
    * Display Information on the Checkout Confirmation Page
    * @return array
    */
    function confirmation() {
      global $order;
         $this->isActivatedCallback = ($this->isActivatedCallback && ($this->sepa_pin_amount <= (string)$_SESSION['novalnet']['sepa_pin_amount'])) ? true : false;
         if ($this->isActivatedCallback && isset($_SESSION['novalnet']['nn_tid_sepa'])) {
             $sepa_amount = str_replace('.','',$_SESSION['novalnet']['original_amount_sepa']);
             $amount = $this->get_order_total();
             if ($sepa_amount != $amount || $_SESSION['novalnet']['shipping_name_sepa'] != $order->info['shipping_class']) {
                $error = NOVALNET_AMOUNT_VARIATION_MESSAGE_PIN;
                if(isset($_SESSION['novalnet']['sepa_user_email'])) {
                    $error = NOVALNET_AMOUNT_VARIATION_MESSAGE_EMAIL;
                }
                unset($_SESSION['novalnet']['original_amount_sepa'], $_SESSION['novalnet']['nn_tid_sepa'], $_SESSION['novalnet']['sepa_nonajax_details']);
                $this->error_redirect($error);
            }
         }
        return '';
    }

    /**
    * Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen.
    * These are hidden fields on the checkout confirmation page.
    * Get the total amount in process butto
    * @return boolean
    */
    function process_button($vars = null) {
            $_SESSION['novalnet']['novalnet_sepa_amount'] = $this->get_order_total();
        return '';
    }


    /*
    * Insert the Novalnet Transaction ID in DB
    * @return array
    */
    function before_process() {
        global $order;
        $this->assign_manual_check();
        $this->isActivatedCallback = ($this->isActivatedCallback && ($this->sepa_pin_amount <= (string)$_SESSION['novalnet']['sepa_pin_amount'])) ? true : false;
        $request = $_POST;
        if ($this->order_status) {
            $order->info['order_status'] = $this->order_status;
        }

        if($this->auto_refill) {
            $_SESSION['novalnet']['nn_auto_refill'] = $this->code;
        }
        if ($_SESSION['novalnet']['nn_tid_sepa'] && $this->isActivatedCallback) {
            $_SESSION['novalnet']['nn_sepa_pin_max_exceed'] = false;
            $aryResponse = $this->secondCall();
            if ($aryResponse) { 
                if ($aryResponse['status'] != 100) { 
                    $nnxmlsession = $this->paymentErrrorMessage($aryResponse);

                    if ($aryResponse['status'] == '0529006' ) {
                        $_SESSION['novalnet']['nn_sepa_pin_max_exceed'] = true;
                    }

                    if ($this->is_ajax) {
                        $error_message = 'payment_error=' . $this->code . '&error=' . urlencode($nnxmlsession);
                        $_SESSION['checkout_payment_error'] = $error_message;
                        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT, $error_message, 'SSL', true, false));
                    }
                    $this->error_redirect($nnxmlsession);
                } else {
                    if ($this->order_status)
                        $order->info['order_status'] = $this->order_status;
                    $this->prepare_comments($_SESSION['novalnet']);
                    $this->novalnet_response['tid']  = $_SESSION['novalnet']['nn_tid_sepa'];
                }
            }
            return;
        }
        
        if($this->is_ajax) {
            $sepa_confirm = $request['sepa_confirm'];
            $sepa_panhash = $request['sepa_panhash'];
            $uniqueid     = $request['sepa_uniqueid'];
            $fldVdr       = $request['fldVdr'];
            $_SESSION['novalnet']['sepa_ajax_details'] = $_SESSION['novalnet']['sepa_ajax_details'].'&sepa_confirm='.$sepa_confirm.'&sepa_panhash='.$sepa_panhash.'&sepa_uniqueid='.$uniqueid.'&fldVdr='.$fldVdr;
            $nnsepa_values = $this->deformatNvp($_SESSION['novalnet']['sepa_ajax_details']);

        } else {
            $nnsepa_values = $_SESSION['novalnet']['sepa_nonajax_details'];
            $sepa_confirm  = $_SESSION['novalnet']['sepa_nonajax_details']['sepa_confirm'];
            $sepa_holder   = $_SESSION['novalnet']['sepa_nonajax_details']['sepa_owner'];
            $sepa_panhash  = $_SESSION['novalnet']['sepa_nonajax_details']['sepa_panhash'];
            $uniqueid      = $_SESSION['novalnet']['sepa_nonajax_details']['sepa_uniqueid'];
            $fldVdr        = $_SESSION['novalnet']['sepa_nonajax_details']['fldVdr'];
        }
        $common_params = $this->get_common_params();
        $sepa_params   = array(
                            'bank_account'        =>  "",
                            'bank_account_holder' =>  $nnsepa_values['sepa_owner'],
                            'bank_code'           =>  "",
                            'bic'                 =>  "",
                            'iban'                =>  "",
                            'iban_bic_confirmed'  =>  $sepa_confirm,
                            'sepa_hash'           => $sepa_panhash,
                            'sepa_unique_id'      => $uniqueid
                            );

        $sepa_due_date = trim(MODULE_PAYMENT_NOVALNET_SEPA_DUE_DATE);
        if ($this->isActivatedCallback) {
          if (MODULE_NN_SEPA_PIN == 'Callback') {
            $sepa_params['pin_by_callback'] = 1;
            $sepa_params['tel'] = ($_SESSION['novalnet']['sepa_user_tel'] ) ? $_SESSION['novalnet']['sepa_user_tel']  :'';
          }
          elseif (MODULE_NN_SEPA_PIN == 'SMS') {
            $sepa_params['pin_by_sms'] = 1;
            $sepa_params['mobile'] = ($_SESSION['novalnet']['sepa_user_tel'] ) ? $_SESSION['novalnet']['sepa_user_tel']  : '';
          }
          elseif (MODULE_NN_SEPA_PIN == 'E-Mail') {
            $sepa_params['reply_email_check'] = 1;
            $sepa_params['email'] = ($_SESSION['novalnet']['sepa_user_email']) ? $_SESSION['novalnet']['sepa_user_email'] : '';
          }
        }

        if (is_numeric($sepa_due_date) ) {
            $sepa_params['sepa_due_date'] = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + $sepa_due_date, date("Y")));
        } else {
            $sepa_params['sepa_due_date'] = date('Y-m-d', strtotime("+7 days"));
        }
        $novalnet_request = array_merge($common_params,$sepa_params);
        list($errno, $errmsg, $data) = $this->perform_https_request($this->paygate_url, $novalnet_request);
        if ( $errno or $errmsg ) {
        ### Payment Gateway Error ###
            $this->error_redirect($errmsg);
        }
        parse_str($data, $this->novalnet_response);

        ### firstcall_server response ###
        if ($this->novalnet_response['status'] == 100) {
            $order->info['order_status'] = $this->order_status;
            //required data to check before the second call for pin by callback.
            $_SESSION['novalnet']['shipping_name_sepa'] = $order->info['shipping_class'];
            $_SESSION['novalnet']['amount']    = $_SESSION['novalnet']['original_amount_sepa'] = $this->novalnet_response['amount'];
            $_SESSION['novalnet']['tid']       = $_SESSION['novalnet']['nn_tid_sepa']          = $this->novalnet_response['tid'];
            $_SESSION['novalnet']['test_mode'] = $this->novalnet_response['test_mode'];
            //set session for maximum time limit to 30 minutes
            $_SESSION['novalnet']['max_time_sepa'] = time() + (30 * 60);
            if ($this->isActivatedCallback) {
              if (MODULE_NN_SEPA_PIN == 'E-Mail') {
                 $checkoutmsg = MODULE_PAYMENT_NOVALNET_SEPA_EMAIL_REPLY_CHECK_MSG;
              } else {
                 $checkoutmsg = MODULE_PAYMENT_NOVALNET_SEPA_PIN_CHECK_MSG;
              }
              if ($this->is_ajax) {
                 $error_message = 'payment_error=' . $this->code . '&error=' . $checkoutmsg;
                 $_SESSION['checkout_payment_error'] = $error_message;
                 xtc_redirect(xtc_href_link(FILENAME_CHECKOUT, $error_message, 'SSL', true, false));
              }
              $this->error_redirect($checkoutmsg);
            } else{
                $this->prepare_comments($this->novalnet_response);
            }
        } else {
            ### Passing through the Error Response from Novalnet's paygate into order-info ###
            $error = $this->novalnet_response['status_desc'];
            $this->error_redirect($error);
        }
    }

    /*
     * Send the order detail to Novalnet
     * update order details in th database
     *
     * @return boolean
     */
    function after_process() {
       $this->update_order_status($this->order_status);
       $this->send_postback_request($this->novalnet_response);
    }
    /*
    * Used to display error message details
    * function call at checkout_payment.php
    * @return array
    */
    function get_error() {
        if ($this->is_ajax) {
            unset($_SESSION['shipping']);
        }
        $error = array('title' => MODULE_PAYMENT_NOVALNET_SEPA_TEXT_ERROR, 'error' => stripslashes(html_entity_decode($_GET['error'])));
        return $error;
    }

    /*
    * Check to see whether module is installed
    * @return boolean
    */
    function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_SEPA_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

       /*
        * Install the payment module and its configuration settings
        */
    function install() {
        $this->table_alter();
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_ALLOWED', '', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_STATUS', 'False', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_NN_SEPA_PIN','False'
                , '6', '2', 'xtc_mod_select_option(array(\'Callback\' =>
                 NOVALNET_CALLBACK,\'SMS\' =>
                NOVALNET_SMS,\'E-Mail\'
                => NOVALNET_EMAIL,\'False\' =>
                NOVALNET_NOT_ACTIVE),\'MODULE_NN_SEPA_PIN\',".MODULE_NN_SEPA_PIN.",'
                ,now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_SEPA_PIN_BY_CALLBACK_MIN_LIMIT', '', '6', '3', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE', 'False', '6', '4', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_VENDOR_ID', '', '6', '5', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_AUTH_CODE', '', '6', '6', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_PRODUCT_ID', '', '6', '7', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_TARIFF_ID', '', '6', '8', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_AUTO_REFILL', 'False', '6', '9', 'xtc_cfg_select_option(array(\'True\', \'False\'),', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_MANUAL_CHECK_LIMIT', '', '6', '10', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_PRODUCT_ID2', '', '6', '11', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_TARIFF_ID2', '', '6', '12', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_INFO', '', '6', '13', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_SORT_ORDER', '0', '6', '14', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_COMPLETE_ORDER_STATUS_ID', '0', '6', '15', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_DUE_DATE', '', '6', '16', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_ZONE', '0', '6', '17', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_PROXY', '', '6', '18', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_REFERENCE1', '', '6', '19', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_REFERENCE2', '', '6', '20', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_NOVALNET_LOGO_ACTIVE_MODE', 'True', '6', '21', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_LOGO_ACTIVE_MODE', 'True', '6', '22', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_SEPA_REFERRER_ID', '', '6', '22', now())");
        $this->create_novalnet_callback_table();
    }

    /*
    * Remove the module and all its settings
    * @return boolean
    */
    function remove() {
        xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

   /*
    * Internal list of configuration keys used for configuration of the module
    * @return array
    */
    function keys() {
        return array('MODULE_PAYMENT_NOVALNET_SEPA_ALLOWED', 'MODULE_PAYMENT_NOVALNET_SEPA_STATUS', 'MODULE_NN_SEPA_PIN', 'MODULE_PAYMENT_NOVALNET_SEPA_PIN_BY_CALLBACK_MIN_LIMIT', 'MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE',  'MODULE_PAYMENT_NOVALNET_SEPA_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_SEPA_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_SEPA_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_SEPA_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_SEPA_AUTO_REFILL', 'MODULE_PAYMENT_NOVALNET_SEPA_MANUAL_CHECK_LIMIT', 'MODULE_PAYMENT_NOVALNET_SEPA_PRODUCT_ID2', 'MODULE_PAYMENT_NOVALNET_SEPA_TARIFF_ID2', 'MODULE_PAYMENT_NOVALNET_SEPA_INFO', 'MODULE_PAYMENT_NOVALNET_SEPA_SORT_ORDER','MODULE_PAYMENT_NOVALNET_SEPA_COMPLETE_ORDER_STATUS_ID','MODULE_PAYMENT_NOVALNET_SEPA_DUE_DATE','MODULE_PAYMENT_NOVALNET_SEPA_ZONE', 'MODULE_PAYMENT_NOVALNET_SEPA_PROXY', 'MODULE_PAYMENT_NOVALNET_SEPA_NOVALNET_LOGO_ACTIVE_MODE', 'MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_LOGO_ACTIVE_MODE',  'MODULE_PAYMENT_NOVALNET_SEPA_REFERENCE1','MODULE_PAYMENT_NOVALNET_SEPA_REFERENCE2', 'MODULE_PAYMENT_NOVALNET_SEPA_REFERRER_ID');
    }

    /*
    * Deformat NVP to the array
    * @return array
    */
    function deformatNvp($str) {
        $fields = array();
        $temp = explode('&', $str);
        foreach( $temp as $v ) {
            $v = explode('=', $v);
            $fields[urldecode($v[0])] = isset($v[1]) ? urldecode($v[1]) : NULL;
        }
        return $fields;
    }
    
    /**
    * This is user defined function used to send the xml request to the novalnet
    *
    * @return string
    */
    public function secondCall() {
        //If customer forgets PIN, send a new PIN
        $request_type = (MODULE_NN_SEPA_PIN == 'E-Mail') ? 'REPLY_EMAIL_STATUS' : ($_SESSION['novalnet']['new_novalnet_sepa_pin'] ?  'TRANSMIT_PIN_AGAIN' :  'PIN_STATUS');

        if ($_SESSION['novalnet']['new_novalnet_sepa_pin'])
            $_SESSION['novalnet']['new_novalnet_sepa_pin'] = false;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                   <nnxml>
                      <info_request>
                          <vendor_id>' . $this->vendorid . '</vendor_id>
                          <vendor_authcode>' . $this->authcode . '</vendor_authcode>
                          <request_type>' . $request_type . '</request_type>';
        $xml .= ($request_type != 'REPLY_EMAIL_STATUS') ? ('<pin>' . $_SESSION['novalnet']['novalnet_sepa_pin'] . '</pin>') : '';
        $xml .= '<tid>' . $_SESSION['novalnet']['nn_tid_sepa'] . '</tid>
                      </info_request>
                  </nnxml>';
                  
        ### secondcall_request ###
        if (empty($this->vendorid) || empty($this->authcode) || empty($_SESSION['novalnet']['nn_tid_sepa']) || ($request_type == 'PIN_STATUS' && empty($_SESSION['novalnet']['novalnet_sepa_pin']))) {
            //unset($_SESSION['novalnet']['nn_tid_cc']);
            $error = MODULE_PAYMENT_NOVALNET_SEPA_TEXT_JS_NN_MISSING;
            $array_param = array('status_desc' => $error);
        } else {
            ### secondcall_request ###
            list($errno, $errmsg, $xml_response) = $this->perform_https_request($this->nn_info_port_url, $xml);
            $xml_response = simplexml_load_string($xml_response); #Php function:Parse XML Response to object
            ### secondcall_request ###
            $array_param = (array) $xml_response;
        }
        ### secondcall_response ###
        return $array_param;
    }
}
/*
flow of functions:
selection              -> $order-info['total'] wrong, cause shipping_cost is net
pre_confirmation_check -> $order-info['total'] wrong, cause shipping_cost is net
confirmation           -> $order-info['total'] right, cause shipping_cost is gross
process_button         -> $order-info['total'] right, cause shipping_cost is gross
before_process         -> $order-info['total'] wrong, cause shipping_cost is net
after_process          -> $order-info['total'] right, cause shipping_cost is gross
---------------
*/
