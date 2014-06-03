<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_AMAZONCHECKOUT_TEXT_TITLE', applyTransactionId('Amazon Checkout'));
define('MODULE_PAYMENT_POSTFINANCE_AMAZONCHECKOUT_TEXT_TITLE_ADMIN', 'PostFinance: Amazon Checkout');
define('MODULE_PAYMENT_POSTFINANCE_AMAZONCHECKOUT_TEXT_DESCRIPTION', 'Zahlung mit Amazon Checkout &uuml;ber PostFinance.');
