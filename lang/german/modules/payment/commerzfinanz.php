<?php
define('MODULE_PAYMENT_COMMERZFINANZ_VENDORNAME_TITLE', 'H&auml;ndlernummer');
define('MODULE_PAYMENT_COMMERZFINANZ_VENDORNAME_DESC', 'Ihre H&auml;ndlernummer.');

define('MODULE_PAYMENT_COMMERZFINANZ_TEMPLATE_FILE_TITLE', 'Template Datei');
define('MODULE_PAYMENT_COMMERZFINANZ_TEMPLATE_FILE_DESC', 'Geben Sie hier die Template Datei an, die Sie verwenden m&ouml;chten. Falls Sie kein Template verwenden wollen, lassen Sie dieses Feld einfach leer.');

define('MODULE_PAYMENT_COMMERZFINANZ_ENCODING_TITLE', 'Codierung');
define('MODULE_PAYMENT_COMMERZFINANZ_ENCODING_DESC', 'W&auml;hlen Sie die Codierung f&uuml;r die Transaktionen. Wichtig ist, dass Sie hier die gleichen Einstellungen machen wie im Backend der Post.');

define('MODULE_PAYMENT_COMMERZFINANZ_DB_ENCODING_TITLE', 'Datenbank Codierung');
define('MODULE_PAYMENT_COMMERZFINANZ_DB_ENCODING_DESC', 'W&auml;hlen Sie hier wie die Daten aus der Datenbank behandelt werden sollen.');


// OPTIONS
define('MODULE_PAYMENT_' . $paymentMethod . '_STATUS_TITLE', 'COMMERZ FINANZ aktivieren');
define('MODULE_PAYMENT_' . $paymentMethod . '_STATUS_DESC', 'M&ouml;chten Sie Zahlungen per COMMERZ FINANZ akzeptieren?');

define('MODULE_PAYMENT_' . $paymentMethod . '_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_' . $paymentMethod . '_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');

define('MODULE_PAYMENT_' . $paymentMethod . '_ALLOWED_TITLE' , 'Erlaubte Zonen');
define('MODULE_PAYMENT_' . $paymentMethod . '_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');

define('MODULE_PAYMENT_' . $paymentMethod . '_ORDER_STATUS_ID_TITLE', 'Bestellstatus festlegen');
define('MODULE_PAYMENT_' . $paymentMethod . '_ORDER_STATUS_ID_DESC', 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen');

define('MODULE_PAYMENT_' . $paymentMethod . '_TMPORDER_STATUS_ID_TITLE', 'Temporären Bestellstatus festlegen');
define('MODULE_PAYMENT_' . $paymentMethod . '_TMPORDER_STATUS_ID_DESC', 'Bestellung während der Eingabe der Zahlungsdaten auf diesen Status setzen');

define('MODULE_PAYMENT_' . $paymentMethod . '_ZONE_TITLE', 'Zahlungszone');
define('MODULE_PAYMENT_' . $paymentMethod . '_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');

// Define Log states:
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


define('DRESDNERFINANZ_CONTENT_ID_TITLE', 'Content-Group-ID');
define('DRESDNERFINANZ_CONTENT_ID_DESC', 'Geben Sie bitte die Content-Group-ID an, worin Ihr Finanzierungstext hinterlegt ist.');

//Define Zins
define('DRESDNERFINANZ_MINIMUM_PRICE_TITLE_TITLE', 'Mindestpreis');
define('DRESDNERFINANZ_MINIMUM_PRICE_TITLE_DESC', 'Geben Sie bitte den Artikelmindestpreis an (Beispiel: 500.00)');
define('DRESDNERFINANZ_MAXIMUM_PRICE_TITLE_TITLE', 'Maximalpreis');
define('DRESDNERFINANZ_MAXIMUM_PRICE_TITLE_DESC', 'Geben Sie bitte die maximale Finanzierungssumme an (Beispiel: 50000.00)');
define('DRESDNERFINANZ_ZINS_EFF_TITLE', 'Zinssatz');
define('DRESDNERFINANZ_ZINS_EFF_DESC', 'Bitte geben Sie den Zinssatz f&uuml;r die Beispielrechnung ohne % ein  (Beispiel: 9.9)');
define('DRESDNERFINANZ_STATUS_TITLE', 'Commerz Finanz GmbH Finanzierungsbeispiel ein / ausschalten');
define('DRESDNERFINANZ_STATUS_DESC', 'ein / ausschalten');

define('DRESDNERFINANZ_CAMPAIGN_TITLE', 'Aktionszins');
define('DRESDNERFINANZ_CAMPAIGN_DESC', '0 = Keine Kampagne, 1 = mit Aktion.');
define('DRESDNERFINANZ_CONTENT_COLOR_TITLE', 'Farbwert');
define('DRESDNERFINANZ_CONTENT_COLOR_DESC', 'Hier bitte den Farbwert f&uuml;r die Ausgabe des Hinweistextes definieren (Webfarben verwenden, z.B.: #1B6D82');
