<?php
$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'yellowpay.php';

define('MODULE_PAYMENT_YELLOWPAY_AMEX_TEXT_TITLE', 'American Express');
define('MODULE_PAYMENT_YELLOWPAY_AMEX_TEXT_TITLE_ADMIN', 'yellowpay: American Express');
define('MODULE_PAYMENT_YELLOWPAY_AMEX_TEXT_DESCRIPTION', 'Zahlung mit American Express &uuml;ber yellowpay.');
