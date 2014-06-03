<?php
$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'yellowpay.php';

define('MODULE_PAYMENT_YELLOWPAY_YELLOWBILL_TEXT_TITLE', 'yellowbill');
define('MODULE_PAYMENT_YELLOWPAY_YELLOWBILL_TEXT_TITLE_ADMIN', 'yellowpay: yellowbill');
define('MODULE_PAYMENT_YELLOWPAY_YELLOWBILL_TEXT_DESCRIPTION', 'Zahlung mit yellowbill über yellowpay.');
