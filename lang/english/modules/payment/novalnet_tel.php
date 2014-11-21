<?php
#########################################################
#                                                       #
#  Telephone payment text creator script                #
#  This script is used for translating the text for     #
#  real time processing of Telephone Payment of customer#
#                                                       #
#  Copyright (c) Novalnet AG                            #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_tel module Created By Dixon Rajdaniel       #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_tel.php                            #
#                                                       #
#########################################################

include_once 'novalnet_common.php';
define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_TITLE', 'Telephone Payment');
define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_PUBLIC_TITLE', 'Telephone Payment');
define('MODULE_PAYMENT_NOVALNET_TEL_LOGO_TITLE', NOVALNET_TEXT_LOGO_IMAGE . '&nbsp;');
define('MODULE_PAYMENT_NOVALNET_TEL_PAYMENT_LOGO_TITLE', '&nbsp;&nbsp;<a href="http://www.novalnet.com" target="_new"><img src="'.$img_src.'Telefonpayment.png" alt="Telephone Payment" title="Telephone Payment" height = "25px" border="0"></a>');

if (MODULE_PAYMENT_NOVALNET_TEL_NOVALNET_LOGO_ACTIVE_MODE == 'True') {
        define('MODULE_PAYMENT_NOVALNET_TEL_LOGO_STATUS',  MODULE_PAYMENT_NOVALNET_TEL_LOGO_TITLE);
} else {
        define('MODULE_PAYMENT_NOVALNET_TEL_LOGO_STATUS', '' );
}

if (MODULE_PAYMENT_NOVALNET_TEL_PAYMENT_LOGO_ACTIVE_MODE == 'True') {
        define('MODULE_PAYMENT_NOVALNET_TEL_PAYMENT_LOGO_STATUS', MODULE_PAYMENT_NOVALNET_TEL_PAYMENT_LOGO_TITLE);
} else {
        define('MODULE_PAYMENT_NOVALNET_TEL_PAYMENT_LOGO_STATUS', '');
}

define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_DESCRIPTION', 'Your amount will be added in your telephone bill when you place the order ');
define('MODULE_PAYMENT_NOVALNET_TEXT_LANG', 'EN');
define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_INFO', '');

define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP_INFO', '<B>Following steps are required to complete your payment:');
define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP1', '<B>Step 1:</B>');
define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP2', '<B>Step 2:</B>');
define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP1_DESC', 'Please call the telephone number displayed:');
define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP2_DESC', 'Please wait for the beep and then hang up the listeners. <br />After your successful call, please proceed with the payment.');
define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_COST_INFO', '* This call will cost <B>');
define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_TAX_INFO', 'EUR</B> (including VAT) and it is possible only for German landline connection! *');
define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_AMOUNT_ERROR1', 'Amounts below 0,99 Euros and above 10,00 Euros cannot be processed and are not accepted! ');
define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_AMOUNT_ERROR2', 'Amounts below 0,99 Euros and above 10,00 Euros cannot be processed and are not accepted! ');

define('MODULE_PAYMENT_NOVALNET_TEL_TID_MESSAGE', 'Novalnet Transaction ID : ');
define('MODULE_PAYMENT_NOVALNET_TEL_AMOUNT_ERROR', 'You have changed the order amount after receiving telephone number, please try again with a new call');
define('MODULE_PAYMENT_NOVALNET_TEXT_JS_NN_MISSING', '* Basic Parameter Missing!');

define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_DESCRIPTION', NOVALNET_TEL_TEXT_DESCRIPTION);
define('MODULE_PAYMENT_NOVALNET_TEXT_LANG', NOVALNET_TEXT_LANG);
define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_INFO', NOVALNET_TEXT_INFO);
define('MODULE_PAYMENT_NOVALNET_TEL_STATUS_TITLE', NOVALNET_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_STATUS_DESC', NOVALNET_STATUS_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID_TITLE', NOVALNET_VENDOR_ID_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID_DESC', NOVALNET_VENDOR_ID_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE_TITLE', NOVALNET_AUTH_CODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE_DESC', NOVALNET_AUTH_CODE_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_PRODUCT_ID_TITLE', NOVALNET_PRODUCT_ID_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_PRODUCT_ID_DESC', NOVALNET_PRODUCT_ID_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_TARIFF_ID_TITLE', NOVALNET_TARIFF_ID_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_TARIFF_ID_DESC', NOVALNET_TARIFF_ID_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_INFO_TITLE', NOVALNET_INFO_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_INFO_DESC', NOVALNET_INFO_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_COMPLETE_ORDER_STATUS_ID_TITLE', NOVALNET_COMPLETE_ORDER_STATUS_ID_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_COMPLETE_ORDER_STATUS_ID_DESC', NOVALNET_ORDER_STATUS_ID_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_SORT_ORDER_TITLE', NOVALNET_SORT_ORDER_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_SORT_ORDER_DESC', NOVALNET_SORT_ORDER_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_ZONE_TITLE', NOVALNET_ZONE_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_ZONE_DESC', NOVALNET_ZONE_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_ALLOWED_TITLE', NOVALNET_ALLOWED_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_ALLOWED_DESC', NOVALNET_ALLOWED_DESC);
define('MODULE_PAYMENT_NOVALNET_TEXT_JS_NN_MISSING', NOVALNET_TEXT_JS_NN_MISSING);
define('MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE', NOVALNET_TEST_MODE);
define('MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE_TITLE', NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE_DESC', NOVALNET_TEST_MODE_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_PROXY_TITLE', NOVALNET_PROXY_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_PROXY_DESC', NOVALNET_PROXY_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_REFERENCE1_TITLE', NOVALNET_REFERENCE1_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_REFERENCE1_DESC', NOVALNET_REFERENCE1_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_REFERENCE2_TITLE', NOVALNET_REFERENCE2_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_REFERENCE2_DESC', NOVALNET_REFERENCE2_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_REFERRER_ID_TITLE', NOVALNET_REFERRER_ID_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_REFERRER_ID_DESC', NOVALNET_REFERRER_ID_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_IN_TEST_MODE', NOVALNET_IN_TEST_MODE);
define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_REFERRER_ID_ERROR', NOVALNET_REFERRER_ID_ERROR);
define('MODULE_PAYMENT_NOVALNET_TEST_ORDER_MESSAGE', NOVALNET_TEST_ORDER_MESSAGE);
define('MODULE_PAYMENT_NOVALNET_TEL_NOT_CONFIGURED', NOVALNET_NOT_CONFIGURED);
define('MODULE_PAYMENT_NOVALNET_TEL_REQUEST_FOR_CHOOSE_SHIPPING_METHOD', 'Please choose a shipping method');
define('MODULE_PAYMENT_NOVALNET_TEL_FIRST_CALL_NOTIFY', 'Refer the steps which is mentioned in the telephone payment to complete the process');
define('MODULE_PAYMENT_NOVALNET_TEL_NOVALNET_LOGO_ACTIVE_MODE_TITLE', NOVALNET_LOGO_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_NOVALNET_LOGO_ACTIVE_MODE_DESC', NOVALNET_LOGO_DESC);
define('MODULE_PAYMENT_NOVALNET_TEL_PAYMENT_LOGO_ACTIVE_MODE_TITLE', NOVALNET_PAYMENT_LOGO_TITLE);
define('MODULE_PAYMENT_NOVALNET_TEL_PAYMENT_LOGO_ACTIVE_MODE_DESC', NOVALNET_PAYMENT_LOGO_DESC);
?>
