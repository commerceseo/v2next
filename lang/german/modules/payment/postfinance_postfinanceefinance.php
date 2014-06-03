<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_POSTFINANCEEFINANCE_TEXT_TITLE', applyTransactionId('PostFinance E-Finance'));
define('MODULE_PAYMENT_POSTFINANCE_POSTFINANCEEFINANCE_TEXT_TITLE_ADMIN', 'PostFinance: PostFinance E-Finance');
define('MODULE_PAYMENT_POSTFINANCE_POSTFINANCEEFINANCE_TEXT_DESCRIPTION', 'Zahlung mit PostFinance E-Finance &uuml;ber PostFinance.');
