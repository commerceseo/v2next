<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_CREDITCARD_TEXT_TITLE', applyTransactionId('credit-card'));
define('MODULE_PAYMENT_POSTFINANCE_CREDITCARD_TEXT_TITLE_ADMIN', 'PostFinance: credit-card');
define('MODULE_PAYMENT_POSTFINANCE_CREDITCARD_TEXT_DESCRIPTION', 'Payment with credit-card over postfinance.');
