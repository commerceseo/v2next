<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_MASTERCARD_TEXT_TITLE', applyTransactionId('MasterCard'));
define('MODULE_PAYMENT_POSTFINANCE_MASTERCARD_TEXT_TITLE_ADMIN', 'PostFinance: MasterCard');
define('MODULE_PAYMENT_POSTFINANCE_MASTERCARD_TEXT_DESCRIPTION', 'Payment with MasterCard over postfinance.');
