<?php
define('MODULE_PAYMENT_YELLOWPAY_MODE_TITLE', 'Live mode');
define('MODULE_PAYMENT_YELLOWPAY_MODE_DESC', 'Do you want to run yellowpay in live mode?');

define('MODULE_PAYMENT_YELLOWPAY_PSPID_TITLE', 'Your PSPID');
define('MODULE_PAYMENT_YELLOWPAY_PSPID_DESC', 'Enter here your PSPID:');

define('MODULE_PAYMENT_YELLOWPAY_TEST_PSPID_TITLE','Your Test PSPID');
define('MODULE_PAYMENT_YELLOWPAY_TEST_PSPID_DESC','Enter here your Test PSPID. It should be send by PostFinance.');

define('MODULE_PAYMENT_YELLOWPAY_HASH_SEND_TITLE', 'SHA-1-IN Signature');
define('MODULE_PAYMENT_YELLOWPAY_HASH_SEND_DESC', 'Your SHA-1 Signature must have at least 6 chars. &raquo;<a target="_blank" href="http://shop.customweb.ch/yellowpay_sha_1_gernerator.php">Generate SHA-1 Signature</a>');

define('MODULE_PAYMENT_YELLOWPAY_HASH_BACK_TITLE', 'SHA-1-OUT Signature');
define('MODULE_PAYMENT_YELLOWPAY_HASH_BACK_DESC', 'Your SHA-1 Signature must have at least 6 chars. &raquo;<a target="_blank" href="http://shop.customweb.ch/yellowpay_sha_1_gernerator.php">Generate SHA-1 Signature</a>');

define('MODULE_PAYMENT_YELLOWPAY_SHOP_ID_TITLE', 'Shop Idendifier');
define('MODULE_PAYMENT_YELLOWPAY_SHOP_ID_DESC', 'This option is usefull to identify the shop software. This is only usefulll with the multistore module. In all other cases please leave it empty.');

define('MODULE_PAYMENT_YELLOWPAY_ORDER_PREFIX_TITLE', 'Order Prefix');
define('MODULE_PAYMENT_YELLOWPAY_ORDER_PREFIX_DESC', 'You can add here a schema for the order id. "{id}" will be replaced with the order id. (e.g. "shop_{id}"');

define('MODULE_PAYMENT_YELLOWPAY_TEMPLATE_FILE_TITLE', 'Template File');
define('MODULE_PAYMENT_YELLOWPAY_TEMPLATE_FILE_DESC', 'Enter here your template file. If you dont want using a template, then leave it empty.');

define('MODULE_PAYMENT_' . $paymentMethod . '_STATUS_TITLE', 'Activate yellowpay');
define('MODULE_PAYMENT_' . $paymentMethod . '_STATUS_DESC', 'Would you like to accept yellowpay payments?');

define('MODULE_PAYMENT_' . $paymentMethod . '_SORT_ORDER_TITLE', 'Order of display');
define('MODULE_PAYMENT_' . $paymentMethod . '_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');

define('MODULE_PAYMENT_' . $paymentMethod . '_ALLOWED_TITLE' , 'Allowed zones');
define('MODULE_PAYMENT_' . $paymentMethod . '_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');

define('MODULE_PAYMENT_' . $paymentMethod . '_ORDER_STATUS_ID_TITLE', 'Set Order Status');
define('MODULE_PAYMENT_' . $paymentMethod . '_ORDER_STATUS_ID_DESC', 'Set the status of orders made with this payment module to this value');

define('MODULE_PAYMENT_' . $paymentMethod . '_ZONE_TITLE', 'Payment Zonen');
define('MODULE_PAYMENT_' . $paymentMethod . '_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');

define('MODULE_PAYMENT_' . $paymentMethod . '_CHF_TITLE',  'Accept CHF');
define('MODULE_PAYMENT_' . $paymentMethod . '_CHF_DESC',  'Should your customers can pay with CHF?');

define('MODULE_PAYMENT_' . $paymentMethod . '_EUR_TITLE',  'Accept EURO');
define('MODULE_PAYMENT_' . $paymentMethod . '_EUR_DESC',  'Should your customers can pay with EURO?');

define('MODULE_PAYMENT_' . $paymentMethod . '_USD_TITLE',  'Accept USD');
define('MODULE_PAYMENT_' . $paymentMethod . '_USD_DESC',  'Should your customers can pay with USD?');

define('MODULE_PAYMENT_' . $paymentMethod . '_CUSTOMERCANCELLED',  'Canceled by customer');
define('MODULE_PAYMENT_' . $paymentMethod . '_REDIRECTION',  'Redirection');
define('MODULE_PAYMENT_' . $paymentMethod . '_PROCEEDCALL',  'Proceed incoming Call');
define('MODULE_PAYMENT_' . $paymentMethod . '_HACKATTEMPT',  'Hack attempt');
define('MODULE_PAYMENT_' . $paymentMethod . '_PAYMENTHASBEENAUTORISED',  'Payment autorised');
define('MODULE_PAYMENT_' . $paymentMethod . '_PAYMENTWASCAPTURED',  'Payment captured');
define('MODULE_PAYMENT_' . $paymentMethod . '_DATAVALIDATION',  'Not valid data');
define('MODULE_PAYMENT_' . $paymentMethod . '_ACQUIRERREJECTPAYMENT',  'Acquire reject payment');
define('MODULE_PAYMENT_' . $paymentMethod . '_PAYMENTISNOTACCEPTED',  'Payment failed');
define('MODULE_PAYMENT_' . $paymentMethod . '_ORDERADDED',  'Order added');

