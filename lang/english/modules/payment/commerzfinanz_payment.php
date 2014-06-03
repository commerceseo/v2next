<?php
$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require_once 'commerzfinanz.php';

define('MODULE_PAYMENT_COMMERZFINANZ_PAYMENT_TEXT_TITLE', '<img src="images/icons/logo_commerz_finanz.gif" />');
define('MODULE_PAYMENT_COMMERZFINANZ_PAYMENT_TEXT_TITLE_ADMIN', '<img src="../images/icons/logo_commerz_finanz.gif" />');
define('MODULE_PAYMENT_COMMERZFINANZ_PAYMENT_TEXT_DESCRIPTION', 'Um COMMERZ FINANZ zu verwenden, müssen Sie dieses Modul installieren. Hier wird die Hauptkonfiguration vorgenommen. Falls in den anderen Modulen nicht die selben Optionen erscheinen, gelten diese Optionen global!');
