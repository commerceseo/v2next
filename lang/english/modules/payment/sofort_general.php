<?php
/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2013-06-18 18:16:57 +0200 (Di, 18 Jun 2013) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: sofort_general.php 420 2013-06-19 18:04:39Z akausch $
 */

define('MODULE_PAYMENT_SOFORT_MULTIPAY_JS_LIBS', '
	<script type="text/javascript" src="'.DIR_WS_CATALOG.'callback/sofort/ressources/javascript/sofortbox.js"></script>
');

define('MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY_TITLE', 'configuration key');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY_DESC', 'Assigned configuration key by SOFORT AG');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_AUTH_TITLE', 'test configuration key');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_AUTH_DESC', '<noscript>Please activate Javascript</noscript>
	<script type="text/javascript" src="'.DIR_WS_CATALOG.'callback/sofort/ressources/javascript/testAuth.js"></script>
');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_ZONE_TITLE', 'Payment zone');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_ZONE_DESC', 'When a zone is selected, the payment method applies only to this zone.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_1_TITLE', 'Reason 1');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_1_DESC', 'For purpose 1 the following options can be selected');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_2_TITLE', 'Reason 2');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_2_DESC', 'Following placeholders will be replaced inside the reason (max 27 characters):<br />{{order_date}}<br />{{customer_id}}<br />{{customer_name}}<br />{{customer_company}}<br />{{customer_email}}');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_TEXT_ERROR_HEADING', 'Following error occurred during the process:');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_TEXT_ERROR_MESSAGE', 'Payment is unfortunately not possible or has been cancelled by the customer. Please select another payment method.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_IMAGE_TITLE', 'Banner or text in the selection of payment methods');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_IMAGE_DESC', 'Banner or text in the selection of payment methods');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_PROF_SETTINGS_TITLE', '<span style="font-weight:bold; text-decoration:underline; font-size:1.4em;"><br />Professional settings</span> ');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_PROF_SETTINGS_DESC', 'The following settings usually require no adjustment and should be already filled with the correct values.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED_TITLE', 'Enable logging');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED_DESC', 'Enable logging to record errors, warnings and requests to and responses from the SOFORT servers. For privacy reasons, logging should be turned on only for trouble shooting. For more information, see the manual.');

define('MODULE_PAYMENT_SOFORT_MULTIPAY_TEMP_STATUS_ID_TITLE', 'Temporary order status');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_TEMP_STATUS_ID_DESC', 'Order state for non-completed transactions. The order has been created but the transaction has not yet been confirmed by SOFORT AG.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_ABORTED_STATUS_ID_TITLE', 'Order state at aborted payment'); //Bestellstatus bei abgebrochener/erfolgloser Zahlung
define('MODULE_PAYMENT_SOFORT_MULTIPAY_ABORTED_STATUS_ID_DESC', 'Order status for orders that were cancelled during the checkout process.'); //Bestellstatus bei Bestellungen, die whrend des Bestellvorgangs oder im Wizard abgebrochen wurden.
define('MODULE_PAYMENT_SOFORT_MULTIPAY_ORDER_CANCELED', 'The order has been cancelled.'); //Die Bestellung wurde abgebrochen.

define('MODULE_PAYMENT_SOFORT_MULTIPAY_TRANSACTION_ID', 'transaction ID');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_UPDATE_NOTICE', 'The module was updated. To ensure correct operation, all(!) remaining SOFORT payment methods have to be uninstalled and then reinstalled (if required). Until then, the payment methods can not be offered to the buyer. Please note all the module settings before you uninstall them!');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_VERSIONNUMBER', 'Version number');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_FORWARDING', 'Your request is being checked, please wait a moment and do not cancel.<br />The process can take up to 30 seconds.');

define('MODULE_PAYMENT_SOFORT_KEYTEST_SUCCESS', 'API-Key successfully validated');
define('MODULE_PAYMENT_SOFORT_KEYTEST_SUCCESS_DESC', 'Test OK on');
define('MODULE_PAYMENT_SOFORT_KEYTEST_ERROR', 'Could not validate API key!');
define('MODULE_PAYMENT_SOFORT_KEYTEST_ERROR_DESC', 'Note: API key error');
define('MODULE_PAYMENT_SOFORT_KEYTEST_DEFAULT', 'API key not tested yet');

define('MODULE_PAYMENT_SOFORT_REFRESH_INFO', 'If you have just confirmed, adjusted, cancelled or credited this order, you may need to {{refresh}} this page to see all changes.');
define('MODULE_PAYMENT_SOFORT_REFRESH_PAGE', 'Click here to reload the page');
define('MODULE_PAYMENT_SOFORT_TRANSLATE_TIME', 'time');

//definition of error-codes that can resolve by calling the SOFORT-API
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_0',		'An unknown error occured.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8002',		'Validation error occurred.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010',		'The data is incomplete oder incorrect. Please correct or contact the online merchant.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8011',		'Not in the range of valid values.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8012',		'Value must be positive.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8013',		'Only orders in Euro are supported at the moment. Please correct this and try again.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8015',		'The total amount is too large or too small, please correct this or contact the shop owner.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8017',		'Unknown characters.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8018',		'Maximum number of characters exceeded (max. 27).');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8019',		'The order cannot be completed due to an incorrect e-mail address. Please correct this and try again.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8020',		'The order cannot be completed due to an incorrect telephone number. Please correct this and try again.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8021',		'The county code ist not supported, please contact your merchant.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8022',		'The provided BIC is invalid.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8023',		'The order cannot be completed due to an incorrect BIC (Bank Identifier Code). Please correct this and try again.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8024',		'The order cannot be completed due to an incorrect country code. The delivery/billing address must be in Germany. Please correct this and try again.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8029',		'We can only support German accounts. Please correct this or try an alternative method of payment.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8033',		'The total amount is too high. please correct this and try again.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8034',		'The total amount is too low. Please correct this and try again.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8041',		'Value for VAT is incorrect. Valid values: 0, 7,19.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8046',		'The validation of bank account and bank routing number failed.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8047',		'The maximum number of 255 characters has been exceeded.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8051',		'The query contained invalid cart positions. Please correct this or contact the shop owner.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8058',		'Please enter, at least, the account holder and try again.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8061',		'A transaction with the information you have submitted already exists.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8068',		'Buy on account is only available to private customers at the moment.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_10001', 	'Please complete the fields account number, sort code and account holder in full.'); //LS: holder and bankdata missing
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_10002',	'Please accept the privacy policy.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_10003',	'Unfortunately, the chosen payment method can not be used for payment with items such as downloads or gift vouchers.');  //RBS and virtual content is not allowed
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_10007',	'An unknown error occured.');  //Rechnung by SOFORT: check of shopamount against invoiceamount failed (more than one percent difference found)
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_10008',	'An error has occured during the synchronisation with the SOFORT servers. Please use the options in the SOFORT merchant menu to process the invoice or the cart.');  //Rechnung by SOFORT: Sync of Articles failed

//check for empty fields failed (code 8010 = 'must not be empty')
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.EMAIL_CUSTOMER',				'The e-mail address can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.PHONE_CUSTOMER',				'The telephone number can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.INVOICE_ADDRESS.FIRSTNAME',	'The firstname of the invoice address can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.SHIPPING_ADDRESS.FIRSTNAME',	'The firstname of the shipping address can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.INVOICE_ADDRESS.LASTNAME',	'The lastname of the invoice address can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.SHIPPING_ADDRESS.LASTNAME',	'The lastname of the shipping address can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.INVOICE_ADDRESS.STREET',		'Street and house number must be separated by a space.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.SHIPPING_ADDRESS.STREET',		'Street and house number of the shipping address can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.INVOICE_ADDRESS.STREET_NUMBER',	'Street and house number of the shipping address can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.SHIPPING_ADDRESS.STREET_NUMBER',	'Street and house number of the shipping address can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.INVOICE_ADDRESS.ZIPCODE',		'The zipcode of the invoice address can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.SHIPPING_ADDRESS.ZIPCODE',	'The zipcode of the shipping address can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.INVOICE_ADDRESS.CITY',		'The city name of the invoice address can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.SHIPPING_ADDRESS.CITY',		'The city name of the shipping address can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.INVOICE_ADDRESS.COUNTRY_CODE',	'The country code of the invoice address can not be empty.');
define('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_8010.SHIPPING_ADDRESS.COUNTRY_CODE',	'The country code of the shipping address can not be emtpy.');