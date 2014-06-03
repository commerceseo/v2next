<?php
define('MODULE_PAYMENT_YELLOWPAY_MODE_TITLE', 'Produktiver Betrieb');
define('MODULE_PAYMENT_YELLOWPAY_MODE_DESC', 'Wollen Sie das yellowpay Modul im produktiven Betrieb laufen lassen?');

define('MODULE_PAYMENT_YELLOWPAY_PSPID_TITLE', 'Ihre PSPID');
define('MODULE_PAYMENT_YELLOWPAY_PSPID_DESC', 'Geben Sie hier Ihre PSPID ein:');

define('MODULE_PAYMENT_YELLOWPAY_TEST_PSPID_TITLE','Ihre Test PSPID');
define('MODULE_PAYMENT_YELLOWPAY_TEST_PSPID_DESC','Geben Sie hier Ihre Test PSPID ein. Diese sollte ebenfalls durch PostFinance zugestellt worden sein.');

define('MODULE_PAYMENT_YELLOWPAY_HASH_SEND_TITLE', 'SHA-1-IN Signatur');
define('MODULE_PAYMENT_YELLOWPAY_HASH_SEND_DESC', 'Wichtig verwenden Sie einen mindestens 6 Zeichen langen String. &raquo;<a target="_blank" href="http://shop.customweb.ch/yellowpay_sha_1_gernerator.php">SHA-1 Signature erzeugen</a>');

define('MODULE_PAYMENT_YELLOWPAY_HASH_BACK_TITLE', 'SHA-1-OUT Signatur');
define('MODULE_PAYMENT_YELLOWPAY_HASH_BACK_DESC', 'Wichtig verwenden Sie einen mindestens 6 Zeichen langen String. &raquo;<a target="_blank" href="http://shop.customweb.ch/yellowpay_sha_1_gernerator.php">SHA-1 Signature erzeugen</a>');

define('MODULE_PAYMENT_YELLOWPAY_SHOP_ID_TITLE', 'Shop Idendifier');
define('MODULE_PAYMENT_YELLOWPAY_SHOP_ID_DESC', 'Diese Option erm&ouml;glich Ihnen diesen Shop seperat zu kennzeichnen. Dies wird mit dem Multistore Modul wichtig. Falls Sie dies nicht nutzen k&ouml;nnen, das Feld einfach leer lassen.');

define('MODULE_PAYMENT_YELLOWPAY_ORDER_PREFIX_TITLE', 'Bestellungsprefix');
define('MODULE_PAYMENT_YELLOWPAY_ORDER_PREFIX_DESC', 'Hier k&ouml;nnen Sie einen Schema f&uuml;r die Bestellnr hinterlegen. "{id}" wird mit der Bestellnummer ersetzt. (z.B. "shop_{id}"');

define('MODULE_PAYMENT_YELLOWPAY_TEMPLATE_FILE_TITLE', 'Template Datei');
define('MODULE_PAYMENT_YELLOWPAY_TEMPLATE_FILE_DESC', 'Geben Sie hier die Template Datei an, die Sie verwenden m&ouml;chten. Falls Sie kein Template verwenden wollen, lassen Sie dieses Feld einfach leer.');

define('MODULE_PAYMENT_' . $paymentMethod . '_STATUS_TITLE', 'Kreditkartenmodul aktivieren');
define('MODULE_PAYMENT_' . $paymentMethod . '_STATUS_DESC', 'M&ouml;chten Sie Zahlungen per yellowpay akzeptieren?');

define('MODULE_PAYMENT_' . $paymentMethod . '_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_' . $paymentMethod . '_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');

define('MODULE_PAYMENT_' . $paymentMethod . '_ALLOWED_TITLE' , 'Erlaubte Zonen');
define('MODULE_PAYMENT_' . $paymentMethod . '_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');

define('MODULE_PAYMENT_' . $paymentMethod . '_ORDER_STATUS_ID_TITLE', 'Bestellstatus festlegen');
define('MODULE_PAYMENT_' . $paymentMethod . '_ORDER_STATUS_ID_DESC', 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen');

define('MODULE_PAYMENT_' . $paymentMethod . '_ZONE_TITLE', 'Zahlungszone');
define('MODULE_PAYMENT_' . $paymentMethod . '_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');

define('MODULE_PAYMENT_' . $paymentMethod . '_CHF_TITLE',  'CHF akzeptieren');
define('MODULE_PAYMENT_' . $paymentMethod . '_CHF_DESC',  'Sollen Ihre Kunden mit CHF zahlen dürfen?');

define('MODULE_PAYMENT_' . $paymentMethod . '_EUR_TITLE',  'EURO akzeptieren');
define('MODULE_PAYMENT_' . $paymentMethod . '_EUR_DESC',  'Sollen Ihre Kunden mit EURO zahlen dürfen?');

define('MODULE_PAYMENT_' . $paymentMethod . '_USD_TITLE',  'USD akzeptieren');
define('MODULE_PAYMENT_' . $paymentMethod . '_USD_DESC',  'Sollen Ihre Kunden mit USD zahlen dürfen?');

define('MODULE_PAYMENT_' . $paymentMethod . '_CUSTOMERCANCELLED',  'Abbruch durch Kunde');
define('MODULE_PAYMENT_' . $paymentMethod . '_REDIRECTION',  'Weiterleitung');
define('MODULE_PAYMENT_' . $paymentMethod . '_PROCEEDCALL',  'Starte Verarbeitung Callback');
define('MODULE_PAYMENT_' . $paymentMethod . '_HACKATTEMPT',  'Hack endeckt');
define('MODULE_PAYMENT_' . $paymentMethod . '_PAYMENTHASBEENAUTORISED',  'Zahlung autorisiert');
define('MODULE_PAYMENT_' . $paymentMethod . '_PAYMENTWASCAPTURED',  'Zahlung empfangen');
define('MODULE_PAYMENT_' . $paymentMethod . '_DATAVALIDATION',  'Nicht valide Daten');
define('MODULE_PAYMENT_' . $paymentMethod . '_ACQUIRERREJECTPAYMENT',  'Acquire akzeptiert Zahlung nicht');
define('MODULE_PAYMENT_' . $paymentMethod . '_PAYMENTISNOTACCEPTED',  'Zahlung fehlgeschlagen');
define('MODULE_PAYMENT_' . $paymentMethod . '_ORDERADDED',  'Bestellung hinzugef&uuml;gt');
