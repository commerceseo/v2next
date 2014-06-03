<?php
$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'yellowpay.php';

define('MODULE_PAYMENT_YELLOWPAY_DINERS_TEXT_TITLE', 'Diners Club');
define('MODULE_PAYMENT_YELLOWPAY_DINERS_TEXT_TITLE_ADMIN', 'yellowpay: Diners Club');
define('MODULE_PAYMENT_YELLOWPAY_DINERS_TEXT_DESCRIPTION', 'Zahlung mit Diners Club &uuml;ber yellowpay.');
