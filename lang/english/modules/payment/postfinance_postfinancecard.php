<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_POSTFINANCECARD_TEXT_TITLE', applyTransactionId('PostFinance Card'));
define('MODULE_PAYMENT_POSTFINANCE_POSTFINANCECARD_TEXT_TITLE_ADMIN', 'PostFinance: PostFinance Card');
define('MODULE_PAYMENT_POSTFINANCE_POSTFINANCECARD_TEXT_DESCRIPTION', 'Payment with PostFinance Card over postfinance.');
