<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'yellowpay.php';

define('MODULE_PAYMENT_YELLOWPAY_DEBITDIRECT_TEXT_TITLE', 'Postcard (Debit Direct)');
define('MODULE_PAYMENT_YELLOWPAY_DEBITDIRECT_TEXT_TITLE_ADMIN', 'yellowpay: DebitDirect');
define('MODULE_PAYMENT_YELLOWPAY_DEBITDIRECT_TEXT_DESCRIPTION', 'Payment with DebitDirect.');
