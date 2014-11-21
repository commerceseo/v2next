<?php
#########################################################
#                                                       #
#  Instant Bank / Instant Bank Transfer payment method  #
#  This module is used for real time processing         #
#                                                       #
#  Copyright (c) Novalnet AG                                #
#                                                       #
#  Released under the GNU General Public License        #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_instantbanktransfer.php                    #
#                                                       #
#########################################################

include_once 'novalnet_common.php';

define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_TITLE', 'Instant Bank Transfer');
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_PUBLIC_TITLE', 'Instant Bank Transfer');

define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_LOGO_TITLE', NOVALNET_TEXT_LOGO_IMAGE . '&nbsp;');
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PAYMENT_LOGO_TITLE', '<a href="http://www.novalnet.com" target="_new"><img src="'.$img_src.'Sofortuberweisung.png" alt="Instant Bank Transfer" height= "25px;" title="Instant Bank Transfer" border="0"></a>');

if (MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_NN_LOGO_MODE == 'True') {
        define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_LOGO_STATUS',  MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_LOGO_TITLE);
} else{
        define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_LOGO_STATUS', '' );
}

if (MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PAY_LOGO_MODE == 'True') {
        define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PAY_LOGO_STATUS', MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PAYMENT_LOGO_TITLE);
} else {
        define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PAY_LOGO_STATUS', '');
}

define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_DESCRIPTION', '<span style="float:left;clear:both;">' . NOVALNET_REDIRECT_TEXT_DESCRIPTION . '</span>');
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_LANG', NOVALNET_TEXT_LANG);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_INFO', NOVALNET_TEXT_INFO);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_STATUS_TITLE', NOVALNET_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_STATUS_DESC', NOVALNET_STATUS_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_VENDOR_ID_TITLE', NOVALNET_VENDOR_ID_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_VENDOR_ID_DESC', NOVALNET_VENDOR_ID_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_AUTH_CODE_TITLE', NOVALNET_AUTH_CODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_AUTH_CODE_DESC', NOVALNET_AUTH_CODE_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PRODUCT_ID_TITLE', NOVALNET_PRODUCT_ID_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PRODUCT_ID_DESC', NOVALNET_PRODUCT_ID_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TARIFF_ID_TITLE', NOVALNET_TARIFF_ID_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TARIFF_ID_DESC', NOVALNET_TARIFF_ID_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_INFO_TITLE', NOVALNET_INFO_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_INFO_DESC', NOVALNET_INFO_DESC);
define('MODULE_PAYMENT_NOVALNET_BANKTRANSFER_COMPLETE_ORDER_STATUS_ID_TITLE', NOVALNET_COMPLETE_ORDER_STATUS_ID_TITLE);
define('MODULE_PAYMENT_NOVALNET_BANKTRANSFER_COMPLETE_ORDER_STATUS_ID_DESC', NOVALNET_ORDER_STATUS_ID_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_SORT_ORDER_TITLE', NOVALNET_SORT_ORDER_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_SORT_ORDER_DESC', NOVALNET_SORT_ORDER_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ZONE_TITLE', NOVALNET_ZONE_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ZONE_DESC', NOVALNET_ZONE_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ALLOWED_TITLE', NOVALNET_ALLOWED_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ALLOWED_DESC', NOVALNET_ALLOWED_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_JS_NN_MISSING', NOVALNET_TEXT_JS_NN_MISSING);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_ERROR', NOVALNET_ACCOUNT_TEXT_ERROR);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_CUST_INFORM', NOVALNET_TEXT_CUST_INFORM);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE', NOVALNET_TEST_MODE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_IN_TEST_MODE', NOVALNET_IN_TEST_MODE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_NOT_CONFIGURED', NOVALNET_NOT_CONFIGURED);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE_TITLE', NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE_DESC', NOVALNET_TEST_MODE_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_HASH_ERROR', NOVALNET_TEXT_HASH_ERROR);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PASSWORD_TITLE', NOVALNET_PASSWORD_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PASSWORD_DESC', NOVALNET_PASSWORD_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PROXY_TITLE', NOVALNET_PROXY_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PROXY_DESC', NOVALNET_PROXY_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_REFERENCE1_TITLE', NOVALNET_REFERENCE1_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_REFERENCE1_DESC', NOVALNET_REFERENCE1_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_REFERENCE2_TITLE', NOVALNET_REFERENCE2_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_REFERENCE2_DESC', NOVALNET_REFERENCE2_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_REFERRER_ID_TITLE', NOVALNET_REFERRER_ID_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_REFERRER_ID_DESC', NOVALNET_REFERRER_ID_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_ORDER_MESSAGE', NOVALNET_TEST_ORDER_MESSAGE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TID_MESSAGE', NOVALNET_TID_MESSAGE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_REFERRER_ID_ERROR', NOVALNET_REFERRER_ID_ERROR);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_NN_LOGO_MODE_TITLE', NOVALNET_LOGO_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_NN_LOGO_MODE_DESC', NOVALNET_LOGO_DESC);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PAY_LOGO_MODE_TITLE', NOVALNET_PAYMENT_LOGO_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PAY_LOGO_MODE_DESC', NOVALNET_PAYMENT_LOGO_DESC);