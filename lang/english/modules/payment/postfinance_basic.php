<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_BASIC_TEXT_TITLE', applyTransactionId('PostFinance'));
define('MODULE_PAYMENT_POSTFINANCE_BASIC_TEXT_TITLE_ADMIN', 'PostFinance: main module');
define('MODULE_PAYMENT_POSTFINANCE_BASIC_TEXT_DESCRIPTION', 'If you wish to use PostFinance, you must install this module. This module is mandatory!');
