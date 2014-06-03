<?php
$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'yellowpay.php';

define('MODULE_PAYMENT_YELLOWPAY_YELLOWNET_TEXT_TITLE', 'E-Finance');
define('MODULE_PAYMENT_YELLOWPAY_YELLOWNET_TEXT_TITLE_ADMIN', 'yellowpay: E-Finance (yellownet)');
define('MODULE_PAYMENT_YELLOWPAY_YELLOWNET_TEXT_DESCRIPTION', 'Zahlung mit E-Finance.');
