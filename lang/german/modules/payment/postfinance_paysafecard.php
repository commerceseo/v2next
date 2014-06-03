<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_PAYSAFECARD_TEXT_TITLE', applyTransactionId('paysafecard'));
define('MODULE_PAYMENT_POSTFINANCE_PAYSAFECARD_TEXT_TITLE_ADMIN', 'PostFinance: paysafecard');
define('MODULE_PAYMENT_POSTFINANCE_PAYSAFECARD_TEXT_DESCRIPTION', 'Zahlung mit paysafecard &uuml;ber PostFinance.');
