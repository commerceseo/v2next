<?php
/* --------------------------------------------------------------
   CheckoutByAmazon PaymentModul V1.2.1
   checkout_amazon.php 2011-10-28

   Ruhrmedia GmbH & Co. KG
   http://www.RuhrMedia.de
   Copyright (c) 2011 Ruhrmedia GmbH & Co. KG

   alkim media
   http://www.alkim.de

   Released under the GNU General Public License
   --------------------------------------------------------------
*/
DEFINE ('BOX_CONFIGURATION_777', 'Amazon Payments options');

DEFINE('MODULE_PAYMENT_RMAMAZON_STATUS_TITLE', 'activate modul?');
DEFINE('MODULE_PAYMENT_RMAMAZON_STATUS_DESC', '"True" = enable, "False" = disable');
DEFINE('MODULE_PAYMENT_RMAMAZON_SORT_ORDER_TITLE', 'order');
DEFINE('MODULE_PAYMENT_RMAMAZON_SORT_ORDER_DESC', '');
DEFINE('MODULE_PAYMENT_RMAMAZON_TEXT_TITLE', 'AmazonPayments');
DEFINE('MODULE_PAYMENT_RMAMAZON_MERCHANTID_TITLE', 'Amazon merchant ID');
DEFINE('MODULE_PAYMENT_RMAMAZON_MERCHANTID_DESC', 'Your Amazon merchant ID');

DEFINE('MODULE_PAYMENT_RMAMAZON_MARKETPLACEID_TITLE', 'Amazon Marketplace ID');
DEFINE('MODULE_PAYMENT_RMAMAZON_MARKETPLACEID_DESC', 'Your Amazon Marketplace ID');
DEFINE('MODULE_PAYMENT_RMAMAZON_MERCHANTTOKEN_TITLE', 'Amazon Merchant Token');
DEFINE('MODULE_PAYMENT_RMAMAZON_MERCHANTTOKEN_DESC', 'Your Amazon Merchant Token. Your Seller Central Merchant Token, see Options -> merchenttoken');

DEFINE('MODULE_PAYMENT_RMAMAZON_ACCESKEY_TITLE', 'Access ID');
DEFINE('MODULE_PAYMENT_RMAMAZON_ACCESKEY_DESC', 'Used to communicate and authentication with Amazon Payments');
DEFINE('MODULE_PAYMENT_RMAMAZON_SECRETKEY_TITLE', 'Secret Acces Key');
DEFINE('MODULE_PAYMENT_RMAMAZON_SECRETKEY_DESC', 'Used to communicate and authentication with Amazon Payments');

define('MODULE_PAYMENT_RMAMAZON_ALLOWED_TITLE', 'allowed zones');
define('MODULE_PAYMENT_RMAMAZON_ALLOWED_DESC', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');

DEFINE('MODULE_PAYMENT_RMAMAZON_MODE_TITLE', 'operation mode');
DEFINE('MODULE_PAYMENT_RMAMAZON_MODE_DESC', 'Live = "live" or test mode = "sandbox"');

DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_NOTIFIED_TITLE', 'status for new orders');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_NOTIFIED_DESC', 'This status is set when the order is sent to Amazon.');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_OK_TITLE', 'Order status for confirmid orders');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_OK_DESC', 'This status is set after Amazon has confirmed the order.');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_PAYED_TITLE', 'Order status for payment orders');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_PAYED_DESC', 'This status is set when Amazon marked the order as paid.');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_SHIPPED_TITLE', 'Order status for paid and shipped orders');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_SHIPPED_DESC', 'This status is set or should be set as soon as the item was shipped');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_STORNO_TITLE', 'Order status for canceled orders');
DEFINE('MODULE_PAYMENT_RMAMAZON_ORDER_STATUS_STORNO_DESC', 'This status is set or should be set as soon as the order was canceled.');

DEFINE('MODULE_PAYMENT_RMAMAZON_SENDMAILS_TITLE', 'activate notifications');
DEFINE('MODULE_PAYMENT_RMAMAZON_SENDMAILS_DESC', 'If enabled, you can get with any change in status, the Amazon to the shop sends an e-mail to the gegistered address.');
DEFINE ('MODULE_PAYMENT_RMAMAZON_MAIL_TITLE', 'e-mail address');
DEFINE ('MODULE_PAYMENT_RMAMAZON_MAIL_DESC', 'e-mail address for notifications');

DEFINE ('MODULE_PAYMENT_RMAMAZON_SENDUSERMAIL_TITLE','Additional confirmation of the shop to send email?');
DEFINE ('MODULE_PAYMENT_RMAMAZON_SENDUSERMAIL_DESC','Amazon sent a separate confirmation email to the customer. If you also want to send the mail system of the shop, please enable this option. NOTE: Only shop-customers.');

DEFINE ('MODULE_PAYMENT_RMAMAZON_ALLOW_GUESTS_TITLE','Guest orders allow?');
DEFINE ('MODULE_PAYMENT_RMAMAZON_ALLOW_GUESTS_DESC','If enabled, customers can also pay by Amazon Payments, if they did not have an account in your shop.');
DEFINE ('MODULE_PAYMENT_RMAMAZON_SHOW_ORDERID_TITLE','Amazon order number show?');
DEFINE ('MODULE_PAYMENT_RMAMAZON_SHOW_ORDERID_DESC','If enalbed, appears on the order confirmation page of your store, Amazons order number.');

?>