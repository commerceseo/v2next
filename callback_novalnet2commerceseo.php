<?php

/**
 * Novalnet Callback Script for CommerceSEO
 *
 * NOTICE
 *
 * This script is used for real time capturing of parameters passed
 * from Novalnet AG after Payment processing of customers.
 *
 * This script is only free to the use for Merchants of Novalnet AG
 *
 * If you have found this script useful a small recommendation as well
 * as a comment on merchant form would be greatly appreciated.
 *
 * Please contact sales@novalnet.de for enquiry or info
 *
 * ABSTRACT: This script is called from Novalnet, as soon as a payment
 * done for payment methods, e.g. Prepayment, Invoice, PayPal.
 * An email will be sent if an error occurs
 *
 *
 * @category   Novalnet
 * @package    Novalnet
 * @version    1.1.1
 * @copyright  Copyright (c) Novalnet AG. (https://www.novalnet.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @notice     1. This script must be placed in CommerceSEO root folder
 *                to avoid rewrite rules (mod_rewrite)
 *             2. You have to adapt the value of all the variables
 *                commented with 'adapt ...'
 *             3. Set $test/$debug to false for live system
 */

include ('includes/application_top.php');
include_once (DIR_FS_INC.'xtc_validate_email.inc.php');
//Variable Settings
$debug   = false; //false|true; adapt: set to false for go-live
$test    = false; //false|true; adapt: set to false for go-live
//Test Data Settings

$lineBreak     = empty($_SERVER['HTTP_HOST']) ? PHP_EOL : '<br />';
$requestParams = $_REQUEST;

//Reporting Email Addresses Settings
$mailHost       = ''; // adapt for Shop Mail
$mailPort       = ''; // adapt for Shop Mail
$shopInfo       = 'CommerceSEO Shop'; //manditory;adapt for your need
$emailFromAddr  = ''; //sender email addr., manditory, adapt it
$emailToAddr    = ''; //recipient email addr., manditory, adapt it
$emailSubject   = 'Novalnet Callback Script Access Report' . $shopInfo; //adapt if necessary;
$emailBody      = ''; //Email text, adapt
$emailFromName  = ''; // Sender name, adapt
$emailToName    = ''; // Recipient name, adapt
$emailBCC       = ''; //
//Parameters Settings

//Test Data Settings
if (isset($requestParams['debug_mode']) && $requestParams['debug_mode'] == 1) {
    $debug          = true;
    $test           = true;
    $mailHost       = 'mail.novalnet.de';// email host (adapt)
    $mailPort       = '25';// email port (adapt)
    $emailFromName  = "Novalnet"; // Sender name, adapt
    $emailToName    = "Novalnet"; // Recipient name, adapt
    $emailFromAddr  = 'testadmin@novalnet.de'; //manditory for test; adapt
    $emailToAddr    = 'test@novalnet.de'; //manditory for test; adapt
    $emailSubject   = $emailSubject . ' - TEST'; //adapt
    $emailBCC       = '';
}

global $lineBreak, $debug, $test;
$vendorScript = new vendorScript($requestParams);
$transHistory = (array)$vendorScript->getOrderReference();
$requestedArrayParams = $vendorScript->getRequestedParams();

$emailContent  = array(
                  'mailhost'      => $mailHost,
                  'mailport'      => $mailPort,
                  'emailfrom'     => $emailFromAddr,
                  'emailto'       => $emailToAddr,
                  'emailsubject'  => $emailSubject,
                  'emailfromname' => $emailFromName,
                  'emailtoname'   => $emailToName,
                  'emailbcc'      => $emailBCC
                     );

if (!empty($transHistory)) {

  $order_id       = $transHistory['orders_id'];
  $order_payment  = $transHistory['payment_method']; # Executed payment type for original transaction
  $order_currency = $transHistory['currency']; # Executed payment type for original transaction
  list($get_orders_status,$paymentType) = $vendorScript->getOrderStatus($order_id); 
  list($orderState, $emailfrom) = $vendorScript->getConfigOrderStatus($paymentType, $requestedArrayParams);
  $emailContent['emailfrom']    = ($emailContent['emailfrom'] == '')? $emailfrom : $emailContent['emailfrom'];
  $query   = "SELECT value FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = '".$order_id."' AND class = 'ot_total' LIMIT 1";
  $result  = xtc_db_fetch_array(xtc_db_query($query));
  $totalAmount    = str_replace(array('.', ','), array('', ''), sprintf('%0.2f', $result['value']));
  $callBackAmount = $requestedArrayParams['amount'];
  $qury           = xtc_db_fetch_array(xtc_db_query("SELECT SUM(callback_amount) AS total_amount FROM novalnet_callback WHERE ordernum = '".$order_id."'"));
  $sum            = $callBackAmount + $qury['total_amount'];
  
  if ($vendorScript->getPaymentTypeLevel() == 2) { //level 2 payments - Type of payment 
    if ($requestedArrayParams['payment_type'] == 'INVOICE_CREDIT') {
        if ($qury['total_amount'] < $totalAmount) {
          $vendorScript->insertCallbackTable(array(
                           'tid'    =>  $requestedArrayParams['shop_tid'],
                           'amount' =>  $callBackAmount,
                           'order'  =>  $order_id
                           ));
          $requestedArrayParams['success_message'] = "Novalnet Callback Script executed successfully for the TID :";
          if ($sum >= $totalAmount){
            $requestedArrayParams['order_status'] = $orderState;
            $order_update = $vendorScript->orderTableUpdate($order_id, $orderState);
            $comments = $vendorScript->emailBodyContent($requestedArrayParams, $order_id);
            $emailContent['comments'] = ($sum > $totalAmount) ? $comments . '. Customer paid amount is greater than the order amount' : $comments;
            $vendorScript->sendNotifyMail($emailContent);
            return true;
          }
          $requestedArrayParams['order_status'] = $get_orders_status;
          $emailContent['comments'] = $vendorScript->emailBodyContent($requestedArrayParams, $order_id);
          $vendorScript->sendNotifyMail($emailContent);
          return true;
        } else {
          $vendorScript->debugError('Novalnet callback received. Order already Paid');
        }
    }
  }
  elseif ($vendorScript->getPaymentTypeLevel() == 1) {  //level 1 payments - Type of payment
    $order_update = $vendorScript->orderTableUpdate($order_id, $get_orders_status);
    $requestedArrayParams['order_status'] = $get_orders_status;
    $requestedArrayParams['cancel_message'] = "Novalnet Callback received. Charge back was excetued sucessfully for the TID ";
    $vendorScript->emailBodyContent($requestedArrayParams, $order_id, $emailContent);
    return true;
  }
  elseif ($vendorScript->getPaymentTypeLevel() == 0 ) {  //level 0 payments - Type of payment

    if($requestedArrayParams['subs_billing'] == 1) { ##IF PAYMENT MADE ON SUBSCRIPTION RENEWAL

        #### Step1: THE SUBSCRIPTION IS RENEWED, PAYMENT IS MADE, SO JUST CREATE A NEW ORDER HERE WITHOUT A PAYMENT PROCESS AND SET THE ORDER STATUS AS PAID ####

        #### Step2: THIS IS OPTIONAL: UPDATE THE BOOKING REFERENCE AT NOVALNET WITH YOUR ORDER_NO BY CALLING NOVALNET GATEWAY, IF U WANT THE USER TO USE ORDER_NO AS PAYMENT REFERENCE ###

        #### Step3: ADJUST THE NEW ORDER CONFIRMATION EMAIL TO INFORM THE USER THAT THIS ORDER IS MADE ON SUBSCRIPTION RENEWAL ###

    }

    if ($requestedArrayParams['payment_type'] == 'INVOICE_START') { ##INVOICE START
      if ( $requestedArrayParams['subs_billing'] == 1 ) {
         #### Step4: ENTER THE NECESSARY REFERENCE & BANK ACCOUNT DETAILS IN THE NEW ORDER CONFIRMATION EMAIL ####
      }
    
    } elseif ($requestedArrayParams['payment_type'] == 'PAYPAL') {
      if ($qury['total_amount'] < $totalAmount) {
        $vendorScript->insertCallbackTable(array(
                           'tid'    =>  $requestedArrayParams['shop_tid'],
                           'amount' =>  $totalAmount,
                           'order'  =>  $order_id
                           ));
        $requestedArrayParams['success_message'] = "Novalnet Callback Script executed successfully for the TID :";
        $requestedArrayParams['order_status'] = $orderState;
        $order_update = $vendorScript->orderTableUpdate($order_id, $orderState);
        $vendorScript->emailBodyContent($requestedArrayParams, $order_id, $emailContent);
        return true;
      }
      $vendorScript->debugError('Novalnet callback received. Order already Paid');        
    }else {
      $vendorScript->debugError('Novalnet Callbackscript received. Payment type ( '.$requestedArrayParams['payment_type'].' ) is not applicable for this process!');
    }
  }

  //Cancellation of a Subscription
  if($requestedArrayParams['payment_type'] == 'SUBSCRIPTION_STOP') {
      ### UPDATE THE STATUS OF THE USER SUBSCRIPTION ###
  } 
} else {
    $vendorScript->debugError('Novalnet callback received. Order Reference not exist!');
}

class vendorScript {

protected $aryRequestParams = array();
/** @Array Type of payment available - Level : 0 */
public $paymentTypes   = array('CREDITCARD','INVOICE_START','DIRECT_DEBIT_SEPA','GUARANTEED_INVOICE_START','PAYPAL','ONLINE_TRANSFER','IDEAL','EPS','NOVALTEL_DE','PAYSAFECARD');

/** @Array Type of Chargebacks available - Level : 1 */
public $chargeBackPayments = array('RETURN_DEBIT_SEPA', 'REVERSAL', 'CREDITCARD_BOOKBACK', 'CREDITCARD_CHARGEBACK','REFUND_BY_BANK_TRANSFER_EU','NOVALTEL_DE_CHARGEBACK');

/** @Array Type of CreditEntry payment and Collections available - Level : 2 */
public $creditEntryPayments = array('INVOICE_CREDIT','GUARANTEED_INVOICE_CREDIT','CREDIT_ENTRY_CREDITCARD','CREDIT_ENTRY_SEPA','DEBT_COLLECTION_SEPA','DEBT_COLLECTION_CREDITCARD','NOVALTEL_DE_COLLECTION','NOVALTEL_DE_CB_REVERSAL');

public $arySubscription = array('SUBSCRIPTION_STOP');

public $allowedPayment = array('novalnet_invoice', 'novalnet_prepayment', 'novalnet_paypal', 'novalnet_instantbanktransfer','novalnet_cc', 'novalnet_ideal', 'novalnet_sepa');

public $invoiceAllowed   = array('INVOICE_CREDIT','INVOICE_START');

public $hParamsRequired  = array('vendor_id', 'tid', 'payment_type', 'status','amount');

public $aPaymentTypes    = array(
      'novalnet_invoice'                => array('INVOICE_CREDIT','INVOICE_START', 'SUBSCRIPTION_STOP'),
      'novalnet_prepayment'             => array('INVOICE_CREDIT','INVOICE_START', 'SUBSCRIPTION_STOP'),
      'novalnet_paypal'                 => array('PAYPAL'),
      'novalnet_instantbanktransfer'    => array('ONLINE_TRANSFER', 'REFUND_BY_BANK_TRANSFER_EU'),
      'novalnet_cc'                     => array('CREDITCARD', 'CREDITCARD_BOOKBACK', 'CREDITCARD_CHARGEBACK', 'CREDIT_ENTRY_CREDITCARD','SUBSCRIPTION_STOP','DEBT_COLLECTION_CREDITCARD'),
      'novalnet_sepa'                   => array('DIRECT_DEBIT_SEPA', 'RETURN_DEBIT_SEPA', 'SUBSCRIPTION_STOP','DEBT_COLLECTION_SEPA','CREDIT_ENTRY_SEPA'),
      'novalnet_ideal'                  => array('IDEAL'),
      'novalnet_telephone'              => array('NOVALTEL_DE','NOVALTEL_DE_CHARGEBACK','NOVALTEL_DE_CB_REVERSAL','NOVALTEL_DE_COLLECTION',''),
      'novalnet_eps'                    => array('EPS'), 
    ); 
public $payment_code = array(
      'novalnet_invoice'        => 'INVOICE',
      'novalnet_prepayment'     => 'PREPAYMENT',
      'novalnet_paypal'         => 'PAYPAL',
      'novalnet_instantbanktransfer'    => 'BANKTRANSFER',
      'novalnet_cc'             => 'CC',
      'novalnet_ideal'          => 'IDEAL',
      'novalnet_sepa'           => 'SEPA',
      'novalnet_telephone'      => 'TEL',
      'novalnet_eps'            => 'EPS'
        );

public $ipAllowed  = '195.143.189.210'; //Novalnet IP, is a fixed value, DO NOT CHANGE!!!!!

    public function __construct($params = array()) {
        if (empty($params)){
            self::debugError('Novalnet callback received. No params passed over!');
        }
        
        if (isset($params['subs_billing']) && $params['subs_billing'] == 1 ) {
            array_push($this->hParamsRequired, 'signup_tid');
        } elseif (in_array($params['payment_type'], array_merge($this->chargeBackPayments, $this->invoiceAllowed))) {
            array_push($this->hParamsRequired,'tid_payment');
        }
        $this->aryRequestParams = self::validateRequestParams($params);
    }

   /*
   * Perform parameter validation process
   * Set Empty value if not exist in params
   *
   * @return Array
   */
   public function validateRequestParams($params = array()) {
     global $test, $lineBreak;
     if (!empty($params)) {
         $error = '';
         //Validate Authenticated IP
         if($this->ipAllowed != self::getClientIP() && $test == false) {
             self::debugError();
         }

         if (isset($params['subs_billing']) && $params['subs_billing'] == 1 ) {
             $arySetNullvalueIfnotExist = array('reference', 'vendor_id', 'tid', 'status', 'status_messge', 'payment_type', 'signup_tid');
             foreach ($arySetNullvalueIfnotExist as $key => $value) {
                 if ( !isset($params[$value]) ) { $params[$value] = ''; }
             }
         }

         foreach ($this->hParamsRequired as $k => $v) { 
           if (empty($params[$v])) {
               $error .= 'Required param ( ' . $v . '  ) missing!' . $lineBreak;
           }
         }

         if (!empty($error)) {
             self::debugError($error);
         }

         $error = 'Novalnet callback received. ';
         if (!in_array($params['payment_type'], array_merge($this->paymentTypes, $this->chargeBackPayments, $this->creditEntryPayments, $this->arySubscription))) {
             $error .= 'Payment type ( '.$params['payment_type'].' ) is mismatched!';
             self::debugError($error);
         }

         if (isset($params['status']) && $params['status'] != 100) {
             $error .= 'Status (' . $params['status'] . ') is not valid: Only 100 is allowed';
             self::debugError($error);
         }

         if ($params['payment_type'] == 'INVOICE_CREDIT' || in_array($params['payment_type'], $this->chargeBackPayments)) {
             if (!is_numeric($params['tid_payment']) || strlen($params['tid_payment']) != 17) {
                 $error .= 'Invalid TID ['. $params['tid_payment'] . '] for Order.';
                 self::debugError($error);
             }
         }

         if (isset($params['subs_billing']) && $params['subs_billing'] == 1 && strlen($params['signup_tid']) != 17) {
            self::debugError('Novalnet callback received. Invalid TID ['. $params['signup_tid'] . '] for Order.');
         }
         
         if (strlen($params['tid']) != 17 || !is_numeric($params['tid'])) {
             $error .= 'TID [' . $params['tid'] . '] is not valid.';
             self::debugError($error);
         }

         if (!$params['amount']|| !is_numeric($params['amount']) || $params['amount'] < 0) {
              $error .= 'The requested amount ('. $params['amount'] .') is not valid';
              self::debugError($error);
         }

         if (isset($params['signup_tid']) && $params['signup_tid'] != '') { # Subscription
             $params['shop_tid'] = $params['signup_tid'];
         }
         elseif (in_array($params['payment_type'], array_merge($this->chargeBackPayments, $this->invoiceAllowed))) { 
              $params['shop_tid'] = $params['tid_payment'];
         } 
         elseif (isset($params['tid']) && $params['tid'] != '') {
              $params['shop_tid'] = $params['tid'];
         }
		 
     }
     return $params;
   }

   /*
   * Function to return the client IP Address
   *
   * @return Array
   */
   public function getClientIP() {
      $ipaddress = '';
      if ($_SERVER['HTTP_CLIENT_IP'])
          $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
      else if($_SERVER['HTTP_X_FORWARDED_FOR'])
          $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
      else if($_SERVER['HTTP_X_FORWARDED'])
          $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
      else if($_SERVER['HTTP_FORWARDED_FOR'])
          $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
      else if($_SERVER['HTTP_FORWARDED'])
          $ipaddress = $_SERVER['HTTP_FORWARDED'];
      else if($_SERVER['REMOTE_ADDR'])
          $ipaddress = $_SERVER['REMOTE_ADDR'];
      else
          $ipaddress = 'UNKNOWN';
      return $ipaddress;
    }

   /*
   * Display the error message
   *
   * @display ERROR MESSAGE
   */
   public function debugError($error_msg = 'Authentication Failed!') {
      global $debug;
      if($debug) { echo $error_msg; exit; }
    }

   /*
   * Get given payment_type level for process
   *
   * @return Integer
   */
   public function getPaymentTypeLevel() {
      if(!empty($this->aryRequestParams)) {
          if(in_array($this->aryRequestParams['payment_type'], $this->paymentTypes)) {
              return 0;
          }
          else if(in_array($this->aryRequestParams['payment_type'], $this->chargeBackPayments)) {
              return 1;
          }
          else if(in_array($this->aryRequestParams['payment_type'], $this->creditEntryPayments)) {
              return 2;
          }
      }
      return false;
    }

   /*
   * Return Requested parameters
   *
   * @return Array
   */
   public function getRequestedParams(){
      return $this->aryRequestParams;
    }

   /*
   * Get order reference from the novalnet_transaction_detail table on shop database
   *
   * @return Array
   */
   public function getOrderReference() {
      global $lineBreak;
      
      $org_tid = $this->aryRequestParams['shop_tid'];
      $error   = 'Novalnet callback received. ';
      $query   = "SELECT orders_id, payment_method, currency, comments FROM " . TABLE_ORDERS . " WHERE comments LIKE '%" . $org_tid  . "%' LIMIT 1";
      $result  =  xtc_db_fetch_array(xtc_db_query($query));

      if (empty($result)) {
          $error .= 'Transaction mapping failed';
          self::debugError($error);
      }

      $payment_method  =  $result['payment_method'];
      $order_no = (!empty($this->aryRequestParams['order_no'])) ? $this->aryRequestParams['order_no'] : (!empty($this->aryRequestParams['order_id']) ? $this->aryRequestParams['order_id'] : '');

      if (!in_array($this->aryRequestParams['payment_type'], $this->aPaymentTypes[$payment_method])) {
          $error .= 'Payment type ('.$this->aryRequestParams['payment_type'].') is mismatched!';
          self::debugError($error);
      }
          
      if (!empty($order_no) && $order_no != $result['orders_id']) {
          $error .= 'Order Number is not valid.';
          self::debugError($error);
      }      
      return $result;
    }

   /*
   * Return Callback builded comments
   *
   * @return Array / null
   */
    public function emailBodyContent($req_params , $orderid, $emailContent = array()) { 
      global $lineBreak;
      $message = isset($req_params['success_message']) ? $req_params['success_message'] : $req_params['cancel_message'] ;
      $comments = $message . $req_params['shop_tid'] . " amount " . str_replace('.',',',number_format(sprintf('%0.2f', $req_params['amount']/100), 2)) . $req_params['currency'] . " on " . date('Y-m-d H:i:s');
      if ($this->getPaymentTypeLevel() == 2)
          $comments .=' Please refer PAID transaction in our Novalnet Merchant Administration with the TID: ' . $req_params['tid']; 
      $argComments = array(
                     'comments'     => $comments,
                     'order_no'     => $orderid,
                     'order_status' => $req_params['order_status']
                       );
      $this->orderStatusHistoryUpdate($argComments);
      if (!empty($emailContent))
         $this->sendNotifyMail(array_merge($argComments, $emailContent));         
      else
         return $argComments['comments'];
    }

   /*
   * Insert the this event to callback table
   *
   * @return Boolean
   */
   public function insertCallbackTable($data = array()) { 
       $param   = array(
                   'callback_log'       => $_SERVER['REQUEST_URI'],
                   'reference_tid'      => $data['tid'],
                   'callback_amount'    => $data['amount'],
                   'ordernum'           => $data['order'],
                   'callback_datetime'  => date('Y-m-d H:i:s'),
                       );
       xtc_db_perform('novalnet_callback', $param, 'insert');
       return true;
    }
    
   /*
   * Get order status from shop
   *
   * @return Array
   */
    public function getOrderStatus($incrementId) {
        $query   = "SELECT count(*) as order_count, orders_status, payment_class FROM `" . TABLE_ORDERS . "` WHERE orders_id = '" . $incrementId . "'";
        $result  = xtc_db_fetch_array(xtc_db_query($query));
        if ($result['order_count'] > 0) {
            return array ($result['orders_status'], $result['payment_class']);
        } else {
            return array ("","");
        }
    }
    
   /*
   * Get payment configuration details
   *
   * @return Boolean
   */
   public function getConfigOrderStatus($paymentType,$request) {
       if (in_array($request['payment_type'],$this->invoiceAllowed) || ($request['payment_type'] == 'PAYPAL')) {
           $status_query =  "'MODULE_PAYMENT_NOVALNET_" . $this->payment_code[$paymentType] . "_AFTER_ORDER_STATUS_ID'";
       } else {
           $status_query =  "'MODULE_PAYMENT_NOVALNET_" . $this->payment_code[$paymentType] . "_COMPLETE_ORDER_STATUS_ID'";
       }

       $fetch_config = "SELECT configuration_value FROM " . TABLE_CONFIGURATION . "  WHERE configuration_key = " .$status_query;
       $status_query = xtc_db_fetch_array(xtc_db_query($fetch_config));

       $order_status =  $status_query['configuration_value'];
       if($order_status <= 0) {
            $sql            = xtc_db_fetch_array(xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . "  WHERE configuration_key='DEFAULT_ORDERS_STATUS_ID'"));
       $order_status = $sql['configuration_value'];
       }
       $fetch_config = "SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'EMAIL_FROM'";
       $email_addr = xtc_db_fetch_array(xtc_db_query($fetch_config));
       
       return array($order_status, $email_addr['configuration_value']);
    }

   /*
   * Insert the order status history
   *
   * @return Boolean
   */
   public function orderStatusHistoryUpdate($data = array()) { 
       $param   = array(
                 'orders_id'         => $data['order_no'],
                 'orders_status_id'  => $data['order_status'],
                 'date_added'        => 'now()',
                 'customer_notified' => '1',
                 'comments'          => $data['comments'],
                   );
       xtc_db_perform('orders_status_history', $param, 'insert');
       return true;
   }
   
   /*
   * Update the order table
   *
   * @return Boolean
   */
   public function orderTableUpdate($orderid, $order_status) {
       $param['orders_status'] = $order_status;
       xtc_db_perform('orders', $param, 'update', "orders_id = '$orderid'");
       return true;
   }

   /*
   * Send notification mail to Merchant
   *
   * @return boolean
   */
   public function sendNotifyMail($data = array()) { 
      global $debug, $lineBreak;
      $error = false;
      if (!$data['comments'] || !filter_var($data['emailto'], FILTER_VALIDATE_EMAIL)) {
        $error = true;
      }

	  if (defined(EMAIL_TRANSPORT) == 'smtp' && (defined(SMTP_MAIN_SERVER) == '' || defined(SMTP_PORT) == '')) {
	      $error = true;
	  }

      if ($error == false) {
          xtc_php_mail($data['emailfrom'],$data['emailfromname'], $data['emailto'] , $data['emailtoname'] , $data['emailbcc'], '', '', '', '', $data['emailsubject'], $data['comments']);
      }
      
      if ($debug) {
          if ($error) {
              echo "Mailing failed!" , $lineBreak;
              echo "This mail text should be sent: " , $lineBreak;
              echo $data['comments'];
              return false;
          } else {
              echo __FUNCTION__ , ': Sending Email suceeded!' , $lineBreak;
          }
          echo 'This text has been sent:' , $lineBreak , $data['comments'];
          return true;
      }
   }
}
/* 
Used modules
=============

Level 0 Payments:
-----------------
CREDITCARD
INVOICE_START
DIRECT_DEBIT_SEPA
GUARANTEED_INVOICE_START
PAYPAL
ONLINE_TRANSFER
IDEAL
EPS
PAYSAFECARD

Level 1 Payments:
-----------------
RETURN_DEBIT_SEPA
REVERSAL
CREDITCARD_BOOKBACK
CREDITCARD_CHARGEBACK
REFUND_BY_BANK_TRANSFER_EU
NOVALTEL_DE_CHARGEBACK

Level 2 Payments:
-----------------
INVOICE_CREDIT
GUARANTEED_INVOICE_CREDIT
CREDIT_ENTRY_CREDITCARD
CREDIT_ENTRY_SEPA
DEBT_COLLECTION_SEPA
DEBT_COLLECTION_CREDITCARD
 
Cancel subscription:
--------------------
SUBSCRIPTION_STOP
*/