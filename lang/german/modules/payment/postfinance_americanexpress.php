<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_AMERICANEXPRESS_TEXT_TITLE', applyTransactionId('American Express'));
define('MODULE_PAYMENT_POSTFINANCE_AMERICANEXPRESS_TEXT_TITLE_ADMIN', 'PostFinance: American Express');
define('MODULE_PAYMENT_POSTFINANCE_AMERICANEXPRESS_TEXT_DESCRIPTION', 'Zahlung mit American Express &uuml;ber PostFinance.');
