<?php
$paymentMethod = strtoupper( substr( basename(__FILE__) , 0, -4) );
require_once 'commerzfinanz.php';

define('MODULE_PAYMENT_COMMERZFINANZ_BASIC_TEXT_TITLE', '<img src="'.(($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG. 'images/icons/logo_commerz_finanz.gif" />');
define('MODULE_PAYMENT_COMMERZFINANZ_BASIC_TEXT_TITLE_ADMIN', '<img src="../images/icons/logo_commerz_finanz.gif" />');
define('MODULE_PAYMENT_COMMERZFINANZ_BASIC_ADMIN_TEXT_DESCRIPTION', 'Um COMMERZ FINANZ zu verwenden, m&uuml;ssen Sie dieses Modul installieren. Hier wird die Hauptkonfiguration vorgenommen.');
define('MODULE_PAYMENT_COMMERZFINANZ_BASIC_TEXT_DESCRIPTION', 'Jetzt kaufen - und in kleinen monatlichen Raten zahlen!');
define('MODULE_PAYMENT_COMMERZFINANZ_BASIC_TEXT_INFO', 'Jetzt kaufen - und in kleinen monatlichen Raten zahlen!');
