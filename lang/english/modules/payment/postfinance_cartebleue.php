<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_CARTEBLEUE_TEXT_TITLE', applyTransactionId('Carte Bleue'));
define('MODULE_PAYMENT_POSTFINANCE_CARTEBLEUE_TEXT_TITLE_ADMIN', 'PostFinance: Carte Bleue');
define('MODULE_PAYMENT_POSTFINANCE_CARTEBLEUE_TEXT_DESCRIPTION', 'Payment with Carte Bleue over postfinance.');
