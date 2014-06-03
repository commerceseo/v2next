<?php
$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require_once 'sagepay.php';

define('MODULE_PAYMENT_SAGEPAY_BASIC_TEXT_TITLE', applyTransactionId('<img src="images/icons/sagepay.png" /> SagePay'));
define('MODULE_PAYMENT_SAGEPAY_BASIC_TEXT_TITLE_ADMIN', '<img src="../images/icons/sagepay.png" /> Basiseinstellungen');
define('MODULE_PAYMENT_SAGEPAY_BASIC_TEXT_DESCRIPTION', 'Um sagepay zu verwenden, müssen Sie dieses Modul installieren. Hier wird die Hauptkonfiguration vorgenommen. Falls in den anderen Modulen nicht die selben Optionen erscheinen, gelten diese Optionen global!');
