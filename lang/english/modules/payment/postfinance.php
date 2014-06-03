<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/postfinance_language_functions.php';

define('MODULE_PAYMENT_POSTFINANCE_MODE_TITLE', 'Live mode');
define('MODULE_PAYMENT_POSTFINANCE_MODE_DESC', 'Do you want to run postfinance in live mode?');

define('MODULE_PAYMENT_POSTFINANCE_PSPID_TITLE', 'Your PSPID');
define('MODULE_PAYMENT_POSTFINANCE_PSPID_DESC', 'Enter your PSPID:');

define('MODULE_PAYMENT_POSTFINANCE_TEST_PSPID_TITLE','Your Test PSPID');
define('MODULE_PAYMENT_POSTFINANCE_TEST_PSPID_DESC','Enter your Test PSPID. It should have been sent to you by PostFinance.');

define('MODULE_PAYMENT_POSTFINANCE_HASH_SEND_TITLE', 'SHA-1-IN pass phrase');
define('MODULE_PAYMENT_POSTFINANCE_HASH_SEND_DESC', 'Your SHA-1 pass phrase must have at least 6 chars. &raquo;<a target="_blank" href="http://www.customweb.ch/signature_gernerator.php">Generate SHA-1 Signature</a>');

define('MODULE_PAYMENT_POSTFINANCE_HASH_BACK_TITLE', 'SHA-1-OUT pass phrase');
define('MODULE_PAYMENT_POSTFINANCE_HASH_BACK_DESC', 'Your SHA-1 pass phrase must have at least 6 chars. &raquo;<a target="_blank" href="http://www.customweb.ch/signature_gernerator.php">Generate SHA-1 Signature</a>');

define('MODULE_PAYMENT_POSTFINANCE_SHOP_ID_TITLE', 'Shop Identifier');
define('MODULE_PAYMENT_POSTFINANCE_SHOP_ID_DESC', 'This option is useful to identify the shop software. This is only useful with the multistore module. In all other cases please leave it empty.');

define('MODULE_PAYMENT_POSTFINANCE_ORDER_PREFIX_TITLE', 'Order Prefix');
define('MODULE_PAYMENT_POSTFINANCE_ORDER_PREFIX_DESC', 'Here you can an add order prefix. "{id}" will be replaced with the order id. (e.g. "shop_{id}"');

define('MODULE_PAYMENT_POSTFINANCE_TEMPLATE_FILE_TITLE', 'Template File');
define('MODULE_PAYMENT_POSTFINANCE_TEMPLATE_FILE_DESC', 'Here you can enter a template URL. This template file must contain the tag "$$$PAYMENT ZONE$$$"');

define('MODULE_PAYMENT_POSTFINANCE_HASH_CALCULATION_TITLE', 'Hash Calculation');
define('MODULE_PAYMENT_POSTFINANCE_HASH_CALCULATION_DESC', 'Here you can choose if all parameters or only the main parameters should be secured. It is important that you choose the same option as in the postfinance backend.');

define('MODULE_PAYMENT_POSTFINANCE_HASH_METHOD_TITLE', 'Hash Calculation Method');
define('MODULE_PAYMENT_POSTFINANCE_HASH_METHOD_DESC', 'Select the hash algorithm. It is important that you choose here same option as in the PostFinance backend.');

define('MODULE_PAYMENT_POSTFINANCE_ENCODING_TITLE', 'Encoding');
define('MODULE_PAYMENT_POSTFINANCE_ENCODING_DESC', 'Select the encoding for the transaction. It is important that you choose same option as in the postfinance backend.');

define('MODULE_PAYMENT_POSTFINANCE_DB_ENCODING_TITLE', 'Database Encoding');
define('MODULE_PAYMENT_POSTFINANCE_DB_ENCODING_DESC', 'Select the encoding of your database.');


// OPTIONS
define('MODULE_PAYMENT_' . $paymentMethod . '_STATUS_TITLE', 'Activate PostFinance');
define('MODULE_PAYMENT_' . $paymentMethod . '_STATUS_DESC', 'Would you like to accept payments by PostFinance?');

define('MODULE_PAYMENT_' . $paymentMethod . '_SORT_ORDER_TITLE', 'Order of display');
define('MODULE_PAYMENT_' . $paymentMethod . '_SORT_ORDER_DESC', 'Define order of display. Lowest is displayed first.');

define('MODULE_PAYMENT_' . $paymentMethod . '_ALLOWED_TITLE' , 'Allowed zones');
define('MODULE_PAYMENT_' . $paymentMethod . '_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed for modul (e. g. AT,DE (leave empty if you want to allow all zones))');

define('MODULE_PAYMENT_' . $paymentMethod . '_ORDER_STATUS_ID_TITLE', 'Set Order Status');
define('MODULE_PAYMENT_' . $paymentMethod . '_ORDER_STATUS_ID_DESC', 'Set the status of orders made with this payment module.');

define('MODULE_PAYMENT_' . $paymentMethod . '_ZONE_TITLE', 'Payment Zonen');
define('MODULE_PAYMENT_' . $paymentMethod . '_ZONE_DESC', 'If a zone is selected, only enable this payment method for the zone in question.');


// currencies
define('MODULE_PAYMENT_' . $paymentMethod . '_CHF_TITLE',  'Accept CHF');
define('MODULE_PAYMENT_' . $paymentMethod . '_CHF_DESC',  'Should your customers be able to pay with CHF?');

define('MODULE_PAYMENT_' . $paymentMethod . '_EUR_TITLE',  'Accept EURO');
define('MODULE_PAYMENT_' . $paymentMethod . '_EUR_DESC',  'Should your customers be able to pay with EURO?');

define('MODULE_PAYMENT_' . $paymentMethod . '_USD_TITLE',  'Accept USD');
define('MODULE_PAYMENT_' . $paymentMethod . '_USD_DESC',  'Should your customers be able to pay with USD?');

define('MODULE_PAYMENT_' . $paymentMethod . '_GBP_TITLE',  'Accept GBP');
define('MODULE_PAYMENT_' . $paymentMethod . '_GBP_DESC',  'Should your customers be able to pay with GBP?');

define('MODULE_PAYMENT_' . $paymentMethod . '_TRY_TITLE',  'Accept TRY');
define('MODULE_PAYMENT_' . $paymentMethod . '_TRY_DESC',  'Should your customers be able to pay with TRY?');


// Define Log states:
define('MODULE_PAYMENT_' . $paymentMethod . '_CUSTOMERCANCELLED',  'Cancelled by customer');
define('MODULE_PAYMENT_' . $paymentMethod . '_REDIRECTION',  'Redirection');
define('MODULE_PAYMENT_' . $paymentMethod . '_PROCEEDCALL',  'Proceed incoming call');
define('MODULE_PAYMENT_' . $paymentMethod . '_HACKATTEMPT',  'Hack attempt');
define('MODULE_PAYMENT_' . $paymentMethod . '_PAYMENTHASBEENAUTORISED',  'Payment autorised');
define('MODULE_PAYMENT_' . $paymentMethod . '_PAYMENTWASCAPTURED',  'Payment captured');
define('MODULE_PAYMENT_' . $paymentMethod . '_DATAVALIDATION',  'Data not valid');
define('MODULE_PAYMENT_' . $paymentMethod . '_ACQUIRERREJECTPAYMENT',  'Acquirer has rejected the payment');
define('MODULE_PAYMENT_' . $paymentMethod . '_PAYMENTISNOTACCEPTED',  'Payment failed');
define('MODULE_PAYMENT_' . $paymentMethod . '_ORDERADDED',  'Order added');


define('MODULE_PAYMENT_POSTFINANCE_ALIAS_TITLE', 'Alias Manager');
define('MODULE_PAYMENT_POSTFINANCE_ALIAS_DESC', 'Should the Alias Manager activated? This feature must be activated in your PostFinance account.');

$aliasUsageDescription = 'Define here the message, which is shown to the customer during the checkout process. (%s)';
$aliasUsageTitle = 'Alias Message (%s)';

foreach (getLanguages() as $languageCode => $languageName) {
	define('MODULE_PAYMENT_POSTFINANCE_ALIAS_USAGE_' . strtoupper($languageCode) . '_TITLE', sprintf($aliasUsageTitle, $languageCode));
	define('MODULE_PAYMENT_POSTFINANCE_ALIAS_USAGE_' . strtoupper($languageCode) . '_DESC', sprintf($aliasUsageDescription, $languageCode));
}

