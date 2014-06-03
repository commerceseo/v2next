<?php
/* --------------------------------------------------------------
   sepa.php 2014-01-23 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

define('MODULE_PAYMENT_TYPE_PERMISSION', 'sepa');
define('MODULE_PAYMENT_SEPA_TEXT_TITLE', 'SEPA Bank Transfer');
define('MODULE_PAYMENT_SEPA_TEXT_DESCRIPTION', 'Payments via SEPA');
define('MODULE_PAYMENT_SEPA_TEXT_INFO','');
define('MODULE_PAYMENT_SEPA_TEXT_BANK', 'SEPA');
define('MODULE_PAYMENT_SEPA_TEXT_EMAIL_FOOTER', 'Note: You can download our fax confirmation form here: \' . HTTP_SERVER . DIR_WS_CATALOG . MODULE_PAYMENT_SEPA_URL_NOTE . \'');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_INFO', '');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_OWNER', 'Account holder:');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_IBAN', 'IBAN:');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_BIC', 'BIC:');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_NAME', 'Bank:');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_FAX', 'Bank transfer payment will be confirmed by fax');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR', 'ERROR:');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_1', 'Account number and bank code does not match. Please check again.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_2', 'IBAN cannot be verified. Please check again.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_3', 'IBAN cannot be verified. Please check again.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_4', 'IBAN cannot be verified. Please check again.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_5', 'Bank code not found! Please check again.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_10', 'No account holder entered. Please check again.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_11', 'No iban number entered. Please check again.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_12', 'There was no check digit specified in the IBAN. Please check again.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_13', 'There was no valid iban number entered. Please check again.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_14', 'No bic number entered. Please check again.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_15', 'There was no valid bic number entered. Please check again.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_16', 'No bankname entered. Please check again.');
define('MODULE_PAYMENT_SEPA_TEXT_NOTE', 'Note:');
define('MODULE_PAYMENT_SEPA_TEXT_NOTE2', 'If you do not want to send your<br />account details over the Internet, you can download our ');
define('MODULE_PAYMENT_SEPA_TEXT_NOTE3', 'fax form');
define('MODULE_PAYMENT_SEPA_TEXT_NOTE4', ' and return it to us.');
define('JS_BANK_BLZ', 'Please enter the BIC of your bank!\n');
define('JS_BANK_NAME', 'Please enter the name of your bank!\n');
define('JS_BANK_NUMBER', 'Please enter your IBAN!\n');
define('JS_BANK_OWNER', 'Please enter the name of the account holder!\n');
define('MODULE_PAYMENT_SEPA_DATABASE_BLZ_TITLE' , 'Use database lookup for the bank code?');
define('MODULE_PAYMENT_SEPA_DATABASE_BLZ_DESC' , 'Would you like to use database lookup for the bank code?');
define('MODULE_PAYMENT_SEPA_URL_NOTE_TITLE' , 'Fax Url');
define('MODULE_PAYMENT_SEPA_URL_NOTE_DESC' , 'The fax confirmation file; this should be located in the catalog dir');
define('MODULE_PAYMENT_SEPA_FAX_CONFIRMATION_TITLE' , 'Allow Fax Confirmation');
define('MODULE_PAYMENT_SEPA_FAX_CONFIRMATION_DESC' , 'Do you want to allow fax confirmation?');
define('MODULE_PAYMENT_SEPA_SORT_ORDER_TITLE' , 'Display Sort Order');
define('MODULE_PAYMENT_SEPA_SORT_ORDER_DESC' , 'Display sort order; the lowest value is displayed first.');
define('MODULE_PAYMENT_SEPA_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_SEPA_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module');
define('MODULE_PAYMENT_SEPA_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_SEPA_ZONE_DESC' , 'When a zone is selected, this payment method will be enabled for that zone only.');
define('MODULE_PAYMENT_SEPA_ALLOWED_TITLE' , 'Allowed Zones');
define('MODULE_PAYMENT_SEPA_ALLOWED_DESC' , 'Please enter the zones <b>individually</b> that should be allowed to use this module (e.g. US, UK (leave blank to allow all zones))');
define('MODULE_PAYMENT_SEPA_STATUS_TITLE' , 'Allow Sepa Payments');
define('MODULE_PAYMENT_SEPA_STATUS_DESC' , 'Do you want to accept Sepa payments?');
define('MODULE_PAYMENT_SEPA_MIN_ORDER_TITLE' , 'Minimum Orders');
define('MODULE_PAYMENT_SEPA_MIN_ORDER_DESC' , 'Minimum orders for a customer to view this option.');
define('MODULE_PAYMENT_SEPA_DATACHECK_TITLE', 'Check bankdata?');
define('MODULE_PAYMENT_SEPA_DATACHECK_DESC', 'Shall the entered bank data be checked?');
