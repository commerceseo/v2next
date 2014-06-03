<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_VISA_TEXT_TITLE', applyTransactionId('Visa'));
define('MODULE_PAYMENT_POSTFINANCE_VISA_TEXT_TITLE_ADMIN', 'PostFinance: Visa');
define('MODULE_PAYMENT_POSTFINANCE_VISA_TEXT_DESCRIPTION', 'Zahlung mit Visa &uuml;ber PostFinance.');
