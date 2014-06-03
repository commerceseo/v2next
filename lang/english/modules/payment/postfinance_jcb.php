<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_JCB_TEXT_TITLE', applyTransactionId('JCB'));
define('MODULE_PAYMENT_POSTFINANCE_JCB_TEXT_TITLE_ADMIN', 'PostFinance: JCB');
define('MODULE_PAYMENT_POSTFINANCE_JCB_TEXT_DESCRIPTION', 'Payment with JCB over postfinance.');
