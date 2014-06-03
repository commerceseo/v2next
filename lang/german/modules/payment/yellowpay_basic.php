<?php
$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require 'yellowpay.php';

define('MODULE_PAYMENT_YELLOWPAY_BASIC_TEXT_TITLE', 'yellownet, Postcard oder Kreditkarten');
define('MODULE_PAYMENT_YELLOWPAY_BASIC_TEXT_TITLE_ADMIN', 'yellowpay: Hauptmodul');
define('MODULE_PAYMENT_YELLOWPAY_BASIC_TEXT_DESCRIPTION', 'Um yellowpay zu verwenden, müssen Sie dieses Modul installieren. Hier wird die Hauptkonfiguration vorgenommen. Falls in den anderen Modulen nicht die selben Optionen erscheinen, gelten diese Optionen global!');
