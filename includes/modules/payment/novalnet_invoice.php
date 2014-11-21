<?php
#########################################################
#                                                       #
#  Invoice payment method class                         #
#  This module is used for real time processing of      #
#  Invoice data of customers.                           #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_invoice.php                        #
#                                                       #
#########################################################

include_once DIR_FS_CATALOG . 'includes/classes/payment/novalnet_helper.php';
class novalnet_invoice extends novalnet_helper {

        /**
        * Constructor
        *
        * @return void
        */
    function novalnet_invoice() {
        global $order;
        $this->code         = 'novalnet_invoice';
        $this->payment_key  = '27';
        $this->nninvoice_allowed_pin_country_list = array('de', 'at', 'ch');
        parent :: __construct();
        $this->logo_title   = MODULE_PAYMENT_NOVALNET_INVOICE_LOGO_TITLE;
        $this->payment_logo_title = MODULE_PAYMENT_NOVALNET_INVOICE_PAYMENT_LOGO_TITLE;
        $this->title        = MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE.'<br />'.$this->logo_title.$this->payment_logo_title;//backend
        $this->public_title = MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_PUBLIC_TITLE; //frontend
        $this->description  = MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DESCRIPTION;
        $this->sort_order   = MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER;
        $this->enabled      = ((MODULE_PAYMENT_NOVALNET_INVOICE_STATUS == 'True') ? true : false);
        $this->proxy        = trim(MODULE_PAYMENT_NOVALNET_INVOICE_PROXY);
        $this->image        = MODULE_PAYMENT_NOVALNET_INVOICE_LOGO_STATUS . MODULE_PAYMENT_NOVALNET_INVOICE_PAYMENT_LOGO_STATUS . '<br />';
        $this->check_configure();

        $this->is_ajax = (CHECKOUT_AJAX_STAT == 'true') ? true : false;
        $this->pin = trim(MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT);
        $this->pin_amount = empty($this->pin) ? 0 : $this->pin;
        //define callback types
        $this->isActivatedCallback = false;
        if (MODULE_NN_INVOICE_PIN != 'False') {
            $this->isActivatedCallback = true;
        }

        if (isset($_SESSION['novalnet']['nn_tid_invoice']) && $this->isActivatedCallback) {
           //Check the tid in session and make the second call
           //Check the time limit
           if (isset($_SESSION['novalnet']['max_time_invoice']) && time() > $_SESSION['novalnet']['max_time_invoice']) {
              unset($_SESSION['novalnet']['nn_tid_invoice'], $_SESSION['novalnet']['nn_invoice_pin_max_exceed']);
              $error_message = 'payment_error=' . $this->code . '&error=' . urlencode(utf8_encode(MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SESSION_ERROR));
              if ($this->is_ajax) {
                $_SESSION['checkout_payment_error'] = $error_message;
                return;
              } else {
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $error_message, 'SSL', true, false));
              }
           }
          if ($_GET['new_novalnet_pin_invoice'] == 'true') {
             $_SESSION['novalnet']['new_novalnet_pin_invoice'] = true;
             $response = $this->secondCall();
             if ($response['status'] != 100) {
                $_SESSION['novalnet']['xml_resp_error_invoice'] = $this->paymentErrrorMessage($response);
                $error_message = 'payment_error=' . $this->code . '&error=' . urlencode($_SESSION['novalnet']['xml_resp_error_invoice']);
                if ($response['status'] == '0529006' ) {
                    $_SESSION['novalnet']['nn_invoice_pin_max_exceed'] = TRUE;
                }
                if ($this->is_ajax) {
                    $_SESSION['novalnet']['checkout_payment_error'] = $error_message;
                } else {
                    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $error_message, 'SSL', true, false));
                }
            }
          }
       }
       if ((int) MODULE_PAYMENT_NOVALNET_INVOICE_BEFORE_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_NOVALNET_INVOICE_BEFORE_ORDER_STATUS_ID;
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
        if (($this->enabled == true) && ((int) MODULE_PAYMENT_NOVALNET_INVOICE_ZONE > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_INVOICE_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
    *
    * @return array
    */
    function selection() {
        global $order;
        $_SESSION['nn']['nn_amount_pin'] = $order->info['total'] * 100;
        $mode = ($this->testmode) ? NOVALNET_TEXT_TESTMODE_FRONT : '';
        if(($this->is_ajax || (!$this->is_ajax && !$_SESSION['novalnet']['nn_tid_invoice'])) && !isset($_SESSION['novalnet']['nn_invoice_pin_max_exceed']) ) {
            $selection = array('id' => $this->code,
                'module' => $this->public_title,
                'fields' => array(array('title' => '', 'field' => str_replace('../', '', $this->image) .$this->description))
            );

            // Display callback fields
            if ($this->isActivatedCallback && in_array(strtolower($order->customer['country']['iso_code_2']), $this->nninvoice_allowed_pin_country_list) && ($this->pin_amount == 0 ||  $_SESSION['nn']['nn_amount_pin'] >= $this->pin_amount) && !isset($_SESSION['novalnet']['nn_tid_invoice'])) {

                switch(MODULE_NN_INVOICE_PIN) {
                    case 'E-Mail' :
                            $selection['fields'][] = array('title' => MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_EMAIL, 'field' => xtc_draw_input_field('user_email_invoice', '', 'id="' . $this->code . '-callback" AUTOCOMPLETE=OFF' ));
                            break;
                    case  'Callback' :
                            $selection['fields'][] = array('title' => MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_TEL, 'field' => xtc_draw_input_field('user_tel_invoice', '', 'id="' . $this->code . '-callback" AUTOCOMPLETE=OFF' ));
                            break;
                    case 'SMS' :
                            $selection['fields'][] = array('title' => MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_MOB, 'field' => xtc_draw_input_field('user_tel_invoice', '', 'id="' . $this->code . '-callback" AUTOCOMPLETE=OFF' ));
                            break;
                    default :
                            break;
               }
            }
        }
        if ($this->isActivatedCallback && in_array(strtolower($order->customer['country']['iso_code_2']), $this->nninvoice_allowed_pin_country_list)  && ($this->pin_amount =='0' ||  $_SESSION['nn']['nn_amount_pin'] >= $this->pin_amount)  ) {
            if (($this->is_ajax || !$this->is_ajax) && (isset($_SESSION['novalnet']['nn_tid_invoice']) && !isset($_SESSION['novalnet']['nn_invoice_pin_max_exceed']))) {
                if (!$this->is_ajax) {
                                    $selection = array('id' => $this->code,
                                                        'module' => $this->public_title,
                                                        'fields' => array(array('title' => '', 'field' => str_replace('../', '', $this->image) .$this->description))
                                                                );
                }
                if (MODULE_NN_INVOICE_PIN == 'E-Mail') {
                    if ($this->is_ajax) {
                        $selection['fields'][] = array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_INVOICE_EMAIL_INFO_DESC);
                        $selection['fields'][] = array('title' => '', 'field' => xtc_draw_checkbox_field('email_replied_invoice', '1', false) . MODULE_PAYMENT_NOVALNET_INVOICE_EMAIL_REPLY_INFO);
                    } else {
                        $selection['fields'][] = array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_INVOICE_EMAIL_INPUT_REQUEST_DESC);
                    }
                } else {
                    if ($this->is_ajax) {
                        unset($_SESSION['novalnet']['email_reply_check_invoice']);
                        $selection['fields'][] = array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_INVOICE_PIN_INFO_DESC);
                        $selection['fields'][] = array('title' => MODULE_PAYMENT_NOVALNET_INVOICE_PIN_INPUT_REQUEST_DESC, 'field' => xtc_draw_input_field('novalnet_pin_invoice', '', 'id="' . $this->code . '-callback" AUTOCOMPLETE=OFF' ));
                        $selection['fields'][] = array('title' => '', 'field' => xtc_draw_checkbox_field('forgot_pin_invoice', '1', false, 'id="' . $this->code . '-forgotpin"' ) . MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_NEW_PIN);
                        $selection['fields'][] = array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_INVOICE_FORGOT_PIN_DIV);
                    } else {
                        // Show PIN field, after first call
                        $selection['fields'][] = array('title' => MODULE_PAYMENT_NOVALNET_INVOICE_PIN_INPUT_REQUEST_DESC, 'field' => xtc_draw_input_field('novalnet_pin_invoice', '', 'id="' . $this->code . '-callback" AUTOCOMPLETE=OFF ' ));
                        $selection['fields'][] = array('title' => '', 'field' => '<a href="' . xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'new_novalnet_pin_invoice=true', 'SSL', true, false) . '">' . MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_NEW_PIN . '</a>');
                    }
                }
            }
        }
        if((!$this->isActivatedCallback ) || (($this->isActivatedCallback) && $_SESSION['nn']['nn_amount_pin'] < $this->pin_amount)) {
                $selection = array('id' => $this->code,
                 'module' => $this->public_title,
                'fields' => array(array('title' => '', 'field' => str_replace('../', '', $this->image) .$this->description))
            );
            }

        $selection['fields'][] = array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_INVOICE_INFO);
        $selection['fields'][] = array('title' => '', 'field' =>$mode);
        if (function_exists(get_percent)) {
            $selection['module_cost'] = $GLOBALS['ot_payment']->get_percent($this->code);
        }
        
        return $selection;
        
    }

    /**
    * Precheck to Evaluate the Novalnet backend params
    * Precheck to Evaluate the  params
    *
    * @return void
    */
    function pre_confirmation_check($vars = null) {
        global $order;
        $this->check_curl();
        if (!xtc_validate_email($order->customer['email_address'])) {
            $error_message = 'payment_error=' . $this->code . '&error=' . urlencode(utf8_encode(MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_EMAIL_NOTVALID));
            if ($this->is_ajax) {
                $_SESSION['checkout_payment_error'] = $error_message;
            } else {
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $error_message, 'SSL', true, false));
            }
        }
        $request = $_POST;
        $this->check_shipping_method();
        if ($this->is_ajax) {
            $request = array_merge($request, $vars);
        }

        if (isset($request['user_tel_invoice'])) {
            $request['user_tel_invoice'] = trim($request['user_tel_invoice']);
        }
        if (isset($request['user_email_invoice'])) {
            $request['user_email_invoice'] = trim($request['user_email_invoice']);
        }
        if (isset($request['novalnet_pin_invoice'])) {
            $request['novalnet_pin_invoice'] = trim($request['novalnet_pin_invoice']);
        }

        if ($_SESSION['novalnet']['nn_tid_invoice'] && $this->isActivatedCallback) { #session set : check condtion's before second call.
            if ($this->is_ajax && $request['forgot_pin_invoice'] && isset($_SESSION['novalnet']['nn_tid_invoice'])) {
                $_SESSION['novalnet']['new_novalnet_pin_invoice'] = true;
                $response = $this->secondCall();
                if ($response['status'] != 100) {
                    $_SESSION['novalnet']['xml_resp_error_invoice'] = $this->paymentErrrorMessage($response);
                    if ($response['status'] == '0529006') {
                        $_SESSION['novalnet']['nn_invoice_pin_max_exceed'] = TRUE;
                    }
                    if ($this->is_ajax) {
                            $error_message = 'payment_error=' . $this->code . '&error=' . urlencode(utf8_decode($_SESSION['novalnet']['xml_resp_error_invoice']));
                        $_SESSION['novalnet']['checkout_payment_error'] = $error_message;
                    } else {
                            $error_message = 'payment_error=' . $this->code . '&error=' . urlencode(html_entity_decode($_SESSION['novalnet']['xml_resp_error_invoice']));
                        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $error_message, 'SSL', true, false));
                    }
                }
                return;
            }
            if ($this->is_ajax && MODULE_NN_INVOICE_PIN == 'E-Mail') {
                if ($this->is_ajax && !$request['email_replied_invoice']) {
                    $this->error_redirect(MODULE_PAYMENT_NOVALNET_INVOICE_EMAIL_REPLY_CHECKBOX_INFO);
                } else {
                    $_SESSION['novalnet']['email_reply_check_invoice'] = 'E-Mail';
                }
            }
            if (isset($request['novalnet_pin_invoice']) && isset($_SESSION['novalnet']['nn_tid_invoice'])) {
                // check pin
                if (empty($request['novalnet_pin_invoice']) || !preg_match('/^[0-9]+$/', $request['novalnet_pin_invoice'])) {
                    $this->error_redirect(MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_PIN_NOTVALID);
                } else {
                    if ($request['novalnet_pin_invoice']) {
                        $_SESSION['novalnet']['novalnet_pin_invoice'] = $request['novalnet_pin_invoice'];
                    }
                }
            }
        } else {
            $error = '';
            if ($this->is_ajax || (!$this->is_ajax && !isset($_SESSION['novalnet']['nn_tid_invoice']))) {
                $this->validate_basic_param();
                // Callback stuff....
                $amount_check = $_SESSION['novalnet']['novalnet_invoice_amount'];
                if ($this->isActivatedCallback && in_array(strtolower($order->customer['country']['iso_code_2']), $this->nninvoice_allowed_pin_country_list)) {
                    if ($error == '') {
                        //checking telephone number
                        if (isset($request['user_email_invoice'])) {
                            if (!xtc_validate_email($request['user_email_invoice'])) {
                                $error .= utf8_decode(MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_EMAIL_NOTVALID);
                            }
                        }else if (isset($request['user_tel_invoice'])) {
                            if (strlen($request['user_tel_invoice']) < 8 || !is_numeric($request['user_tel_invoice'])) {
                                $error .= utf8_decode(MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_TEL_NOTVALID);
                            }
                        }
                    }
                    if ($error) {
                        $this->error_redirect($error);
                    } else {
                        if (isset($request['user_tel_invoice'])) {
                            $_SESSION['novalnet']['user_tel_invoice'] = $request['user_tel_invoice'];
                        }
                        if (isset($request['user_email_invoice'])) {
                            $_SESSION['novalnet']['user_email_invoice'] = $request['user_email_invoice'];
                        }
                    }
                }
            }
            if ($error != '') {
                $this->error_redirect($error);
            } else {
                if ($this->is_ajax) {
                    $this->confirmation();
                }
            }
        }
    }

    /*
    *Display Bank Information on the Checkout Confirmation Page ###
    *
    *@return array
    */
    function confirmation() {
        global $order;
        if ($this->isActivatedCallback && isset($_SESSION['novalnet']['nn_tid_invoice'])) {
            $callback_amount = str_replace('.','',$_SESSION['novalnet']['original_amount_invoice']);
            $amount = $this->get_order_total();
            if ((in_array(strtolower($order->customer['country']['iso_code_2']), $this->nninvoice_allowed_pin_country_list)) && ($callback_amount != $amount || $_SESSION['novalnet']['shipping_name_invoice'] != $order->info['shipping_class'])) {
                $error = NOVALNET_AMOUNT_VARIATION_MESSAGE_PIN;
                if(isset($_SESSION['novalnet']['user_email_invoice'])) {
                    $error = NOVALNET_AMOUNT_VARIATION_MESSAGE_EMAIL;
                }
                unset($_SESSION['novalnet']['original_amount_invoice'], $_SESSION['novalnet']['nn_tid_invoice']);
                $this->error_redirect($error);
            }
        }
        return '';
    }

    /**
    * Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen.
    * These are hidden fields on the checkout confirmation page
    * @return string
    */
    function process_button($vars = null) {
        $this->validate_basic_param();
        $_SESSION['novalnet']['novalnet_invoice_amount'] = $this->get_order_total();
        return '';
    }

    /**
    * This sends the data to the Novalnet
    * function call in the pre_confirmation_check if pin_by callback enabled.
    * @param array vars
    * @return array
    */
    function before_process() {
        global $order;
        $amount = $_SESSION['novalnet']['novalnet_invoice_amount'];
        // First call is done, so check PIN / second call...
        if ($_SESSION['novalnet']['nn_tid_invoice'] && $this->isActivatedCallback && in_array(strtolower($order->customer['country']['iso_code_2']), $this->nninvoice_allowed_pin_country_list) && ($this->pin_amount =='0' ||  $_SESSION['nn']['nn_amount_pin'] >= $this->pin_amount)) {
            if (MODULE_NN_INVOICE_PIN == 'E-Mail')
                $_SESSION['novalnet']['email_reply_check_invoice'] = 'E-Mail';
            else
                unset($_SESSION['novalnet']['email_reply_check_invoice']);
            if ($this->is_ajax && $_POST['forgot_pin_invoice'] && !$_POST['novalnet_pin_invoice'])
                $_SESSION['novalnet']['new_novalnet_pin_invoice'] = true;
            else
                $_SESSION['novalnet']['new_novalnet_pin_invoice'] = false;

            $aryResponse = $this->secondCall();
            if ($aryResponse) {
                if ($aryResponse['status'] != 100) {
                    $nnxmlsession = $this->paymentErrrorMessage($aryResponse);
                    if ($aryResponse['status'] == '0529006' ) {
                        $_SESSION['novalnet']['nn_invoice_pin_max_exceed'] = TRUE;
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
                }
            }
            return;
        }
        $urlparam =$this->get_common_params();
        if ($this->isActivatedCallback && in_array(strtolower($order->customer['country']['iso_code_2']), $this->nninvoice_allowed_pin_country_list) && ($this->pin_amount =='0' ||  $_SESSION['nn']['nn_amount_pin'] >= $this->pin_amount)) {
            if (MODULE_NN_INVOICE_PIN == 'Callback') {
                $urlparam['pin_by_callback'] =1;
                $urlparam['tel']= ($_SESSION['novalnet']['user_tel_invoice']) ? $_SESSION['novalnet']['user_tel_invoice'] :'';
            }
            if (MODULE_NN_INVOICE_PIN == 'SMS') {
                $urlparam['pin_by_sms'] = 1;
                $urlparam['mobile']= ($_SESSION['novalnet']['user_tel_invoice']) ? $_SESSION['novalnet']['user_tel_invoice'] : '';
            }
            if (MODULE_NN_INVOICE_PIN == 'E-Mail') {
                $urlparam['reply_email_check'] = 1;
                $urlparam['email']= ($_SESSION['novalnet']['user_email_invoice']) ? $_SESSION['novalnet']['user_email_invoice'] : '';

            }
        }

        $request = urldecode(http_build_query($urlparam));
        $this->validate_basic_param();

        list($errno, $errmsg, $data) = $this->perform_https_request($this->paygate_url, $request);

        if ($errno or $errmsg) {
            ### Payment Gateway Error ###
            $this->error_redirect($errmsg);
        }
        #capture the result and message and other parameters from response data '$data' in an array
        parse_str($data, $this->aryResponse);

        ### firstcall_server response ###
        if ($this->aryResponse['status'] == 100) {
            if ($this->order_status)
                $order->info['order_status'] = $this->order_status;
            //required data to check before the second call for pin by callback.
            $_SESSION['novalnet']['shipping_name_invoice'] = $order->info['shipping_class'];
            $_SESSION['novalnet']['original_amount_invoice'] = $this->aryResponse['amount'];
            $_SESSION['novalnet']['tid'] = $this->aryResponse['tid'];
            $_SESSION['novalnet']['amount'] = $_SESSION['novalnet']['original_amount_invoice'];
            $_SESSION['novalnet']['invoice_iban'] = $this->aryResponse['invoice_iban'];
            $_SESSION['novalnet']['invoice_bic'] = $this->aryResponse['invoice_bic'];
            $_SESSION['novalnet']['invoice_bankname'] = $this->aryResponse['invoice_bankname'];
            $_SESSION['novalnet']['invoice_bankplace'] = $this->aryResponse['invoice_bankplace'];
            $_SESSION['novalnet']['test_mode'] = $this->aryResponse['test_mode'];

            $_SESSION['novalnet']['nn_tid_invoice'] = $this->aryResponse['tid']; # for postback and pin_methods
            if ($this->isActivatedCallback && in_array(strtolower($order->customer['country']['iso_code_2']), $this->nninvoice_allowed_pin_country_list) && ($this->pin_amount =='0' ||  $_SESSION['nn']['nn_amount_pin'] >= $this->pin_amount)) {
            $_SESSION['novalnet']['max_time_invoice'] = time() + (30 * 60);
            if (MODULE_NN_INVOICE_PIN == 'E-Mail') {
                $checkoutmsg = MODULE_PAYMENT_NOVALNET_INVOICE_EMAIL_REPLY_CHECK_MSG;
            } else {
                $checkoutmsg = MODULE_PAYMENT_NOVALNET_INVOICE_PIN_CHECK_MSG;
            }
            if ($this->is_ajax) {
                  $error_message = 'payment_error=' . $this->code . '&error=' . $checkoutmsg;
                  $_SESSION['checkout_payment_error'] = $error_message;
                  xtc_redirect(xtc_href_link(FILENAME_CHECKOUT, $error_message, 'SSL', true, false));
              }
              $this->error_redirect($checkoutmsg);
            }else{
                $this->prepare_comments($_SESSION['novalnet']);
            }
        } else {
            ### Passing through the Error Response from Novalnet's paygate into order-info ###
             if ($this->is_ajax) {
                if ($this->isActivatedCallback) {
                    $error_message = 'payment_error=' . $this->code . '&error=' . urlencode(utf8_decode($this->aryResponse['status_desc']));
                    $_SESSION['checkout_payment_error'] = $error_message;

                } else {
                        $error_message = 'payment_error=' . $this->code . '&error=' . urlencode($this->aryResponse['status_desc']);
                    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT, $error_message, 'SSL', true, false));
                }
            } else {
                $error_message = 'payment_error=' . $this->code . '&error=' . urlencode($this->aryResponse['status_desc']);
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $error_message, 'SSL', true, false));
            }
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
         $this->update_order_status($this->order_status);
         $this->send_postback_request($_SESSION['novalnet']['tid']);

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
        $error = array('title' => MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_ERROR, 'error' => stripslashes(html_entity_decode($_GET['error'])));
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
                        'MODULE_PAYMENT_NOVALNET_INVOICE_STATUS'");
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
        $this->table_alter();
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_INVOICE_ALLOWED', '', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)
        values ('MODULE_PAYMENT_NOVALNET_INVOICE_STATUS', 'False', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_NN_INVOICE_PIN','False'
                , '6', '2', 'xtc_mod_select_option(array(\'Callback\' =>
                 NOVALNET_CALLBACK,\'SMS\' =>
                NOVALNET_SMS,\'E-Mail\'
                => NOVALNET_EMAIL,\'False\' =>
                NOVALNET_NOT_ACTIVE),\'MODULE_NN_INVOICE_PIN\',".MODULE_NN_INVOICE_PIN.",'
                ,now())");

        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)
        values ('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE', 'False', '6', '3', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_INVOICE_VENDOR_ID', '', '6', '4', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_INVOICE_AUTH_CODE', '', '6', '5', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_INVOICE_PRODUCT_ID', '', '6', '6', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_INVOICE_TARIFF_ID', '', '6', '7', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_INVOICE_DURATION', '', '6', '8', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_INVOICE_INFO', '', '6', '9', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT', '', '6', '10', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
        ('MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER', '0', '6', '11', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_NOVALNET_INVOICE_BEFORE_ORDER_STATUS_ID', '0', '6', '12', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_NOVALNET_INVOICE_AFTER_ORDER_STATUS_ID', '0', '6', '13', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_INVOICE_ZONE', '0', '6', '14', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INVOICE_PROXY', '', '6', '15', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INVOICE_REFERENCE1', '', '6', '16', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INVOICE_REFERENCE2', '', '6', '17', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_INVOICE_REFERRER_ID', '', '6', '18', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)
        values ('MODULE_PAYMENT_NOVALNET_INVOICE_NOVALNET_LOGO_ACTIVE_MODE', 'True', '6', '19', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)
        values ('MODULE_PAYMENT_NOVALNET_INVOICE_PAYMENT_LOGO_ACTIVE_MODE', 'True', '6', '20', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
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
        return array('MODULE_PAYMENT_NOVALNET_INVOICE_ALLOWED', 'MODULE_PAYMENT_NOVALNET_INVOICE_STATUS', 'MODULE_NN_INVOICE_PIN','MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT',
                     'MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE', 'MODULE_PAYMENT_NOVALNET_INVOICE_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_INVOICE_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_INVOICE_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_INVOICE_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_INVOICE_DURATION', 'MODULE_PAYMENT_NOVALNET_INVOICE_INFO', 'MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER', 'MODULE_PAYMENT_NOVALNET_INVOICE_BEFORE_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_INVOICE_AFTER_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_INVOICE_ZONE', 'MODULE_PAYMENT_NOVALNET_INVOICE_PROXY', 'MODULE_PAYMENT_NOVALNET_INVOICE_NOVALNET_LOGO_ACTIVE_MODE', 'MODULE_PAYMENT_NOVALNET_INVOICE_PAYMENT_LOGO_ACTIVE_MODE', 'MODULE_PAYMENT_NOVALNET_INVOICE_REFERENCE1', 'MODULE_PAYMENT_NOVALNET_INVOICE_REFERENCE2', 'MODULE_PAYMENT_NOVALNET_INVOICE_REFERRER_ID');
    }

    /**
    * This is user defined function used to send the xml request to the novalnet
    *
    * @return string
    */
    public function secondCall() {
        //If customer forgets PIN, send a new PIN
        $request_type = ($_SESSION['novalnet']['email_reply_check_invoice'] == 'E-Mail') ? 'REPLY_EMAIL_STATUS' : ($_SESSION['novalnet']['new_novalnet_pin_invoice']?  'TRANSMIT_PIN_AGAIN' :  'PIN_STATUS');

        if ($_SESSION['novalnet']['new_novalnet_pin_invoice'])
            $_SESSION['novalnet']['new_novalnet_pin_invoice'] = false;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                   <nnxml>
                      <info_request>
                          <vendor_id>' . $this->vendorid . '</vendor_id>
                          <vendor_authcode>' . $this->authcode . '</vendor_authcode>
                          <request_type>' . $request_type . '</request_type>';
        $xml .= ($request_type != 'REPLY_EMAIL_STATUS') ? ('<pin>' . $_SESSION['novalnet']['novalnet_pin_invoice'] . '</pin>') : '';
        $xml .= '<tid>' . $_SESSION['novalnet']['nn_tid_invoice'] . '</tid>
                      </info_request>
                  </nnxml>';

        ### secondcall_request ###
        if ((($request_type == 'TRANSMIT_PIN_AGAIN') && (empty($this->vendorid) || empty($this->authcode) || empty($_SESSION['novalnet']['nn_tid_invoice'])))
            ||(($request_type == 'PIN_STATUS') && (empty($this->vendorid) || empty($this->authcode) || empty($_SESSION['novalnet']['nn_tid_invoice']) || empty($_SESSION['novalnet']['novalnet_pin_invoice'])))
            ||(($request_type == 'REPLY_EMAIL_STATUS') && (empty($this->vendorid) || empty($this->authcode) || empty($request_type) || empty($_SESSION['novalnet']['nn_tid_invoice'])))
          ) {
            unset($_SESSION['novalnet']['nn_tid_invoice']);
            $error = MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_JS_NN_MISSING;
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
order of functions:
selection              -> $order-info['total'] wrong, cause shipping_cost is net
pre_confirmation_check -> $order-info['total'] wrong, cause shipping_cost is net
confirmation           -> $order-info['total'] right, cause shipping_cost is gross
process_button         -> $order-info['total'] right, cause shipping_cost is gross
before_process         -> $order-info['total'] wrong, cause shipping_cost is net
after_process          -> $order-info['total'] right, cause shipping_cost is gross
*/
