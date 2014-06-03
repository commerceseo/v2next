<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_DINERS_TEXT_TITLE', applyTransactionId('Diners Club'));
define('MODULE_PAYMENT_POSTFINANCE_DINERS_TEXT_TITLE_ADMIN', 'PostFinance: Diners Club');
define('MODULE_PAYMENT_POSTFINANCE_DINERS_TEXT_DESCRIPTION', 'Zahlung mit Diners Club &uuml;ber PostFinance.');
