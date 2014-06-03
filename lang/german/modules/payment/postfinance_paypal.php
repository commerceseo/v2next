<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_PAYPAL_TEXT_TITLE', applyTransactionId('PayPal'));
define('MODULE_PAYMENT_POSTFINANCE_PAYPAL_TEXT_TITLE_ADMIN', 'PostFinance: PayPal');
define('MODULE_PAYMENT_POSTFINANCE_PAYPAL_TEXT_DESCRIPTION', 'Zahlung mit PayPal &uuml;ber PostFinance.');
