<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'yellowpay.php';

define('MODULE_PAYMENT_YELLOWPAY_BASIC_TEXT_TITLE', 'Credit cards and yellownet');
define('MODULE_PAYMENT_YELLOWPAY_BASIC_TEXT_TITLE_ADMIN', 'yellowpay: main module');
define('MODULE_PAYMENT_YELLOWPAY_BASIC_TEXT_DESCRIPTION', 'If you like to use yellowpay, you should install this module. This module is mandatory!');
