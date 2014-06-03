<?php
$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'yellowpay.php';

define('MODULE_PAYMENT_YELLOWPAY_VISA_TEXT_TITLE', 'VISA Card');
define('MODULE_PAYMENT_YELLOWPAY_VISA_TEXT_TITLE_ADMIN', 'yellowpay: Visa');
define('MODULE_PAYMENT_YELLOWPAY_VISA_TEXT_DESCRIPTION', 'Zahlung with Visa over yellowpay.');
