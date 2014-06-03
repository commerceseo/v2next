<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/postfinance_language_functions.php';

define('MODULE_PAYMENT_SAGEPAY_MODE_TITLE', 'Produktiver Betrieb');
define('MODULE_PAYMENT_SAGEPAY_MODE_DESC', 'Wollen Sie das PostFinance Modul im produktiven Betrieb laufen lassen?');

define('MODULE_PAYMENT_SAGEPAY_VENDORNAME_TITLE', 'Händlername');
define('MODULE_PAYMENT_SAGEPAY_VENDORNAME_DESC', 'Ihr Händlername.');

define('MODULE_PAYMENT_SAGEPAY_USERNAME_TITLE','Benutzername');
define('MODULE_PAYMENT_SAGEPAY_USERNAME_DESC','Ihr Benutzername.');

define('MODULE_PAYMENT_SAGEPAY_HASH_SEND_TITLE', 'SHA-1-IN Signatur');
define('MODULE_PAYMENT_SAGEPAY_HASH_SEND_DESC', 'Wichtig verwenden Sie einen mindestens 6 Zeichen langen String.');

define('MODULE_PAYMENT_SAGEPAY_HASH_BACK_TITLE', 'SHA-1-OUT Signatur');
define('MODULE_PAYMENT_SAGEPAY_HASH_BACK_DESC', 'Wichtig verwenden Sie einen mindestens 6 Zeichen langen String.');

define('MODULE_PAYMENT_SAGEPAY_SHOP_ID_TITLE', 'Shop Idendifier');
define('MODULE_PAYMENT_SAGEPAY_SHOP_ID_DESC', 'Diese Option erm&ouml;glich Ihnen diesen Shop seperat zu kennzeichnen. Dies wird mit dem Multistore Modul wichtig. Falls Sie dies nicht nutzen k&ouml;nnen, das Feld einfach leer lassen.');

define('MODULE_PAYMENT_SAGEPAY_ORDER_PREFIX_TITLE', 'Bestellungsprefix');
define('MODULE_PAYMENT_SAGEPAY_ORDER_PREFIX_DESC', 'Hier k&ouml;nnen Sie einen Schema f&uuml;r die Bestellnr hinterlegen. "{id}" wird mit der Bestellnummer ersetzt. (z.B. "shop_{id}"');

define('MODULE_PAYMENT_SAGEPAY_TEMPLATE_FILE_TITLE', 'Template Datei');
define('MODULE_PAYMENT_SAGEPAY_TEMPLATE_FILE_DESC', 'Geben Sie hier die Template Datei an, die Sie verwenden m&ouml;chten. Falls Sie kein Template verwenden wollen, lassen Sie dieses Feld einfach leer.');

define('MODULE_PAYMENT_SAGEPAY_HASH_CALCULATION_TITLE', 'Hash Berechnung');
define('MODULE_PAYMENT_SAGEPAY_HASH_CALCULATION_DESC', 'W&auml;hlen Sie die Variante mit der die Hash Berechnung erfolgt. all = Alle Parameter werden abgesichter; main = Nur die wichtigen Parameter werden abgesichter. Wichtig ist, dass Sie hier die gleichen Einstellungen machen wie im Backend der Post.');

define('MODULE_PAYMENT_SAGEPAY_HASH_METHOD_TITLE', 'Berechnungsmethode');
define('MODULE_PAYMENT_SAGEPAY_HASH_METHOD_DESC', 'W&auml;hlen Sie die Methode f&uuml;r die Berechnung des Hashs. Wichtig ist, dass Sie hier die gleichen Einstellungen machen wie im Backend der Post.');

define('MODULE_PAYMENT_SAGEPAY_ENCODING_TITLE', 'Codierung');
define('MODULE_PAYMENT_SAGEPAY_ENCODING_DESC', 'W&auml;hlen Sie die Codierung f&uuml;r die Transaktionen. Wichtig ist, dass Sie hier die gleichen Einstellungen machen wie im Backend der Post.');

define('MODULE_PAYMENT_SAGEPAY_DB_ENCODING_TITLE', 'Datenbank Codierung');
define('MODULE_PAYMENT_SAGEPAY_DB_ENCODING_DESC', 'W&auml;hlen Sie hier wie die Daten aus der Datenbank behandelt werden sollen.');


// OPTIONS
define('MODULE_PAYMENT_' . $paymentMethod . '_STATUS_TITLE', 'Sagepay aktivieren');
define('MODULE_PAYMENT_' . $paymentMethod . '_STATUS_DESC', 'M&ouml;chten Sie Zahlungen per Sagepay akzeptieren?');

define('MODULE_PAYMENT_' . $paymentMethod . '_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_' . $paymentMethod . '_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');

define('MODULE_PAYMENT_' . $paymentMethod . '_ALLOWED_TITLE' , 'Erlaubte Zonen');
define('MODULE_PAYMENT_' . $paymentMethod . '_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');

define('MODULE_PAYMENT_' . $paymentMethod . '_ORDER_STATUS_ID_TITLE', 'Bestellstatus festlegen');
define('MODULE_PAYMENT_' . $paymentMethod . '_ORDER_STATUS_ID_DESC', 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen');

define('MODULE_PAYMENT_' . $paymentMethod . '_ZONE_TITLE', 'Zahlungszone');
define('MODULE_PAYMENT_' . $paymentMethod . '_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');


// currencies
define('MODULE_PAYMENT_' . $paymentMethod . '_CHF_TITLE',  'CHF akzeptieren');
define('MODULE_PAYMENT_' . $paymentMethod . '_CHF_DESC',  'Sollen Ihre Kunden mit CHF zahlen d&uuml;rfen?');

define('MODULE_PAYMENT_' . $paymentMethod . '_EUR_TITLE',  'EURO akzeptieren');
define('MODULE_PAYMENT_' . $paymentMethod . '_EUR_DESC',  'Sollen Ihre Kunden mit EURO zahlen d&uuml;rfen?');

define('MODULE_PAYMENT_' . $paymentMethod . '_USD_TITLE',  'USD akzeptieren');
define('MODULE_PAYMENT_' . $paymentMethod . '_USD_DESC',  'Sollen Ihre Kunden mit USD zahlen d&uuml;rfen?');

define('MODULE_PAYMENT_' . $paymentMethod . '_GBP_TITLE',  'GBP akzeptieren');
define('MODULE_PAYMENT_' . $paymentMethod . '_GBP_DESC',  'Sollen Ihre Kunden mit GBP zahlen d&uuml;rfen?');

define('MODULE_PAYMENT_' . $paymentMethod . '_TRY_TITLE',  'TRY akzeptieren');
define('MODULE_PAYMENT_' . $paymentMethod . '_TRY_DESC',  'Sollen Ihre Kunden mit TRY zahlen d&uuml;rfen?');


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


define('MODULE_PAYMENT_SAGEPAY_ALIAS_TITLE', 'Alias Manager');
define('MODULE_PAYMENT_SAGEPAY_ALIAS_DESC', 'Soll der Alias Manager aktiviert werden? Dieses Feature muss in Ihrem PostFinance Account aktiviert werden.');

$aliasUsageDescription = 'Setzen Sie hier die Nachricht, die dem Kunden angezeigt wird. (%s)';
$aliasUsageTitle = 'Alias Nachricht (%s)';

foreach (getLanguages() as $languageCode => $languageName) {
	define('MODULE_PAYMENT_SAGEPAY_ALIAS_USAGE_' . strtoupper($languageCode) . '_TITLE', sprintf($aliasUsageTitle, $languageCode));
	define('MODULE_PAYMENT_SAGEPAY_ALIAS_USAGE_' . strtoupper($languageCode) . '_DESC', sprintf($aliasUsageDescription, $languageCode));
}
