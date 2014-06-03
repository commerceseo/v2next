<?php

$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'postfinance.php';

define('MODULE_PAYMENT_POSTFINANCE_BASIC_TEXT_TITLE', applyTransactionId('PostFinance'));
define('MODULE_PAYMENT_POSTFINANCE_BASIC_TEXT_TITLE_ADMIN', 'PostFinance: Hauptmodul');
define('MODULE_PAYMENT_POSTFINANCE_BASIC_TEXT_DESCRIPTION', 'Um PostFinance zu verwenden, m&uuml;ssen Sie dieses Modul installieren. Hier wird die Hauptkonfiguration vorgenommen. Falls in den anderen Modulen nicht die selben Optionen erscheinen, gelten diese Optionen global!');

