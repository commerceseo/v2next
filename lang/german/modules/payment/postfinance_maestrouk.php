<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_MAESTROUK_TEXT_TITLE', applyTransactionId('Maestro UK'));
define('MODULE_PAYMENT_POSTFINANCE_MAESTROUK_TEXT_TITLE_ADMIN', 'PostFinance: Maestro UK');
define('MODULE_PAYMENT_POSTFINANCE_MAESTROUK_TEXT_DESCRIPTION', 'Zahlung mit Maestro UK &uuml;ber PostFinance.');
