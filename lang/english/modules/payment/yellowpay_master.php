<?php
$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'yellowpay.php';

define('MODULE_PAYMENT_YELLOWPAY_MASTER_TEXT_TITLE', 'Master Card');
define('MODULE_PAYMENT_YELLOWPAY_MASTER_TEXT_TITLE_ADMIN', 'yellowpay: Master Card');
define('MODULE_PAYMENT_YELLOWPAY_MASTER_TEXT_DESCRIPTION', 'Payment with Master Card over yellowpay.');
