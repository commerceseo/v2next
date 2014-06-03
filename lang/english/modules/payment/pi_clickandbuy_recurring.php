<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category  PayIntelligent
 * @package   PayIntelligent_ClickandBuy
 * @copyright (C) 2010 PayIntelligent GmbH  <http://www.payintelligent.de/>
 * @license   GPLv2
 */

define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_STATUS_TITLE', 'Activate ClickandBuy Billing Agreement');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_STATUS_DESC', 'Do you accept ClickandBuy Billing Agreement?');

define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT', 'ClickandBuy Billing Agreement');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_TITLE', 'ClickandBuy Billing Agreement');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION', 'Test');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION_HEAD', 'Offer and accept all relevant payment methods with ClickandBuy - worldwide!');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION_TEXT_1', 'No registration fee, no monthly fee, no fixed costs!!!');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION_TEXT_2', 'Commission: 1.9 % + 0.35 Euro per transaction');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_DESCRIPTION_SUBHEAD', 'Sign up now with ClickandBuy as a commercial merchant');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_TEXT_INFO', 'Offer more ways to pay and increase your revenue!<br />Safe, secure and easy payment for your customers!<br />One-time free online registration, no monthly fee!!<br />');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_MERCHANT_ID_TITLE', 'Merchant ID');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_MERCHANT_ID_DESC', 'Number identifying the merchant');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_PROJECT_ID_TITLE', 'Project ID');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_PROJECT_ID_DESC', 'Number representing the API-key used for this payment calls');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY_TITLE', 'Project Secret Key');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY_DESC', 'Your ClickandBuy project shared secret key for token generation');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY_MMS_TITLE', 'MMS Secret Key');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SECRET_KEY_MMS_DESC', 'Your ClickandBuy MMS shared secret key for the XML event verification');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SANDBOX_TITLE', 'Sandbox');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SANDBOX_DESC', 'Activate sandbox?');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_MERCHANT_REGISTRATION_BUTTON', 'ClickandBuy Registration');

define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DESCRIPTION_TITLE', 'Description');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DESCRIPTION_DESC', 'Description of the recurring model.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_NUMBER_LIMIT_TITLE', 'Number Limit');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_NUMBER_LIMIT_DESC', 'Defines the maximum number of successful recurring payments. Absent means no limit.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_AMOUNT_TITLE', 'Amount');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_AMOUNT_DESC', 'Defines the maximum total amount of successful recurring payments. If no value is entered the shop will automatically use the shopping cart value (final price) as maximum amount.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_CURRENCY_TITLE', 'Currency');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_CURRENCY_DESC', 'Defines the currency for the recurring payments. If no value is enetered the shop will automatically use the shopping cart currency.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DATE_LIMIT_TITLE', 'Date Limit');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_DATE_LIMIT_DESC', 'Defines the expiration date until when this recurring payment is valid (23:59:59 UTC on that day). If no value is entered the Billing Agreement PaymentAuthorisation will not expire automatically. (yyyy-mm-dd).');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_REVOKABLE_TITLE', 'Revokable By Consumer');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_REVOKABLE_DESC', 'Defines whether the recurring payment authorisation is revokable by the customer. Check the box for allowing him to revoke. A recurring payment authorisation can always be cancelled by ClickandBuy.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_INITIAL_AMOUNT_TITLE', 'Initial Amount Zero');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_INITIAL_AMOUNT_DESC', 'Defines if the total basket amount should be debited at the same time an authorisation is given. If the box is checked the seller has to debit the amounts manually in order administration.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SPECIAL_CASE_TITLE', 'Special use cases');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SPECIAL_CASE_DESC', 'Fast Checkout<br />If the buyer has once given an authorisation (Billing Agreements) he will not be redirected to the ClickandBuy payment pages for future payments (easy checkout for registered customers).<br /><br />Partial Delivery<br />If some of a carts products are not in stock the amount of these will not debited in the ClickandBuy payment checkout process. The shop owner can debit the amount later in order administration.<br /><br />Note: Partial Delivery has default settings which override all other settings configured above.');

define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SORT_ORDER_TITLE', 'Sort order of display');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ZONE_TITLE', 'Payment Zone');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ORDER_STATUS_ID_TITLE', 'Set Order Status');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ORDER_STATUS_ID_DESC', 'Set the status of orders made with this payment module to this value');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ALLOWED_TITLE', 'Allowed Zones');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_ALLOWED_DESC', 'Please enter the zones separately which should be allowed to use this modul (e. g. DE,AT (leave empty if you want to allow all zones))');

define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_CHECKOUT_TEXT_INFO', 'Easy and secure payments with Credit/Debit Card, Direct Debit,<br />Online Bank Transfer and cash funding.');
define('MODULE_PAYMENT_PI_CLICKANDBUY_RECURRING_CHECKOUT_MORE_INFO_LINK_TITLE', 'More');

define('CLICKANDBUY_ERROR_MESSAGE_1', 'Invalid shash in pi_clickandbuy_do_trans.php%20');
define('CLICKANDBUY_ERROR_MESSAGE_2', 'An error has occurred. Probably, this is a temporary error. Error message: ');
define('CLICKANDBUY_ERROR_MESSAGE_3', 'Please try again. If the problem persists, contact our support.');
define('CLICKANDBUY_ERROR_MESSAGE_4', 'Wrong shash in before_process.');
define('CLICKANDBUY_ERROR_MESSAGE_5', 'Handshake error in before_process.');
define('CLICKANDBUY_ERROR_MESSAGE_6', 'Unkown');
define('CLICKANDBUY_ERROR_MESSAGE_7', 'Handshake error in pi_clickandbuy_trans.php');
define('CLICKANDBUY_ERROR_MESSAGE_8', 'Invalid shash-1 in pi_clickandbuy_trans.php');
define('CLICKANDBUY_ERROR_MESSAGE_9', 'Invalid shash-2 in pi_clickandbuy_trans.php');

define('CLICKANDBUY_LANG_CODE', 'GB_en');

?>