<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_GIROPAY_TEXT_TITLE', applyTransactionId('giropay'));
define('MODULE_PAYMENT_POSTFINANCE_GIROPAY_TEXT_TITLE_ADMIN', 'PostFinance: giropay');
define('MODULE_PAYMENT_POSTFINANCE_GIROPAY_TEXT_DESCRIPTION', 'Zahlung mit giropay &uuml;ber PostFinance.');
