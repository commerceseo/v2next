<?php
/* --------------------------------------------------------------
   sepa.php 2014-01-23 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

define('MODULE_PAYMENT_TYPE_PERMISSION', 'sepa');
define('MODULE_PAYMENT_SEPA_TEXT_TITLE', 'SEPA-Lastschriftverfahren');
define('MODULE_PAYMENT_SEPA_TEXT_DESCRIPTION', 'SEPA');
define('MODULE_PAYMENT_SEPA_TEXT_INFO', '');
define('MODULE_PAYMENT_SEPA_TEXT_BANK', 'SEPA');
define('MODULE_PAYMENT_SEPA_TEXT_EMAIL_FOOTER', 'Hinweis: Sie k&ouml;nnen sich unser Faxformular unter \' . HTTP_SERVER . DIR_WS_CATALOG . MODULE_PAYMENT_SEPA_URL_NOTE . \' herunterladen und es ausgef&uuml;llt an uns zur&uuml;cksenden.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_INFO', '');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_OWNER', 'Kontoinhaber:');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_IBAN', 'IBAN:');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_BIC', 'BIC:');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_NAME', 'Bank:');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_FAX', 'Einzugserm&auml;chtigung wird per Fax best&auml;tigt');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR', 'FEHLER: ');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_1', 'IBAN und BLZ stimmen nicht überein, bitte korrigieren Sie Ihre Angabe.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_2', 'Diese IBAN ist nicht prüfbar, bitte kontrollieren zur Sicherheit Sie Ihre Eingabe nochmals.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_3', 'Diese IBAN ist nicht prüfbar, bitte kontrollieren zur Sicherheit Sie Ihre Eingabe nochmals.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_4', 'Diese IBAN ist nicht prüfbar, bitte kontrollieren zur Sicherheit Sie Ihre Eingabe nochmals.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_5', 'Die aus der IBAN resultierende BLZ existiert nicht, bitte kontrollieren zur Sicherheit Sie Ihre Eingabe nochmals.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_10', 'Sie haben keinen Kontoinhaber angegeben.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_11', 'Sie haben keine IBAN angegeben.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_12', 'Die angegebene IBAN enthält keine Prüfziffer. Bitte kontrollieren Sie Ihre Eingabe nochmals.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_13', 'Sie haben keine korrekte IBAN eingegeben.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_14', 'Sie haben keine BIC angegeben.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_15', 'Sie haben keine korrekte BIC eingegeben.');
define('MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_16', 'Sie haben keinen Banknamen angegeben.');
define('MODULE_PAYMENT_SEPA_TEXT_NOTE', 'Hinweis:');
define('MODULE_PAYMENT_SEPA_TEXT_NOTE2', 'Wenn Sie aus Sicherheitsbedenken keine Bankdaten &uuml;ber das Internet<br />&uuml;bertragen wollen, k&ouml;nnen Sie sich unser ');
define('MODULE_PAYMENT_SEPA_TEXT_NOTE3', 'Faxformular');
define('MODULE_PAYMENT_SEPA_TEXT_NOTE4', ' herunterladen und uns ausgef&uuml;llt zusenden.');
define('JS_BANK_BIC', '* Bitte geben Sie die BIC Ihrer Bank ein!\n\n');
define('JS_BANK_NAME', '* Bitte geben Sie den Namen Ihrer Bank ein!\n\n');
define('JS_BANK_IBAN', '* Bitte geben Sie Ihre IBAN ein!\n\n');
define('JS_BANK_OWNER', '* Bitte geben Sie den Namen des Kontobesitzers ein!\n\n');
define('MODULE_PAYMENT_SEPA_DATABASE_BLZ_TITLE', 'Datenbanksuche f&uuml;r die BLZ verwenden?');
define('MODULE_PAYMENT_SEPA_DATABASE_BLZ_DESC', 'M&ouml;chten Sie die Datenbanksuche f&uuml;r die BLZ verwenden?');
define('MODULE_PAYMENT_SEPA_URL_NOTE_TITLE', 'Fax-URL');
define('MODULE_PAYMENT_SEPA_URL_NOTE_DESC', 'Die Fax-Best&auml;tigungsdatei. Diese muss im Catalog-Verzeichnis liegen');
define('MODULE_PAYMENT_SEPA_FAX_CONFIRMATION_TITLE', 'Fax Best&auml;tigung erlauben');
define('MODULE_PAYMENT_SEPA_FAX_CONFIRMATION_DESC', 'M&ouml;chten Sie die Fax Best&auml;tigung erlauben?');
define('MODULE_PAYMENT_SEPA_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_SEPA_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
define('MODULE_PAYMENT_SEPA_ORDER_STATUS_ID_TITLE', 'Bestellstatus festlegen');
define('MODULE_PAYMENT_SEPA_ORDER_STATUS_ID_DESC', 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen');
define('MODULE_PAYMENT_SEPA_ZONE_TITLE', 'Zahlungszone');
define('MODULE_PAYMENT_SEPA_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
define('MODULE_PAYMENT_SEPA_ALLOWED_TITLE', 'Erlaubte Zonen');
define('MODULE_PAYMENT_SEPA_ALLOWED_DESC', 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
define('MODULE_PAYMENT_SEPA_STATUS_TITLE', 'Sepa Zahlungen erlauben');
define('MODULE_PAYMENT_SEPA_STATUS_DESC', 'M&ouml;chten Sepa Zahlungen erlauben?');
define('MODULE_PAYMENT_SEPA_MIN_ORDER_TITLE', 'Notwendige Bestellungen');
define('MODULE_PAYMENT_SEPA_MIN_ORDER_DESC', 'Die Mindestanzahl an Bestellungen die ein Kunden haben muss damit die Option zur Verf&uuml;gung steht.');
define('MODULE_PAYMENT_SEPA_DATACHECK_TITLE', 'Bankdaten pr&uuml;fen?');
define('MODULE_PAYMENT_SEPA_DATACHECK_DESC', 'Sollen die eingegebenen Bankdaten &uuml;berpr&uuml;ft werden?');
