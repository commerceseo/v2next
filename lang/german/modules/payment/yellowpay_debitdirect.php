<?php
$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'yellowpay.php';

define('MODULE_PAYMENT_YELLOWPAY_DEBITDIRECT_TEXT_TITLE', 'PostFinance Card');
define('MODULE_PAYMENT_YELLOWPAY_DEBITDIRECT_TEXT_TITLE_ADMIN', 'yellowpay: PostFinance Card (DebitDirect)');
define('MODULE_PAYMENT_YELLOWPAY_DEBITDIRECT_TEXT_DESCRIPTION', 'Zahlung mit PostFinance Card über yellowpay.');
