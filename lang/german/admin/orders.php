<?php
/*-----------------------------------------------------------------
* 	$Id: orders.php 872 2014-03-21 14:46:30Z akausch $
* 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
* 	http://www.commerce-seo.de
* ------------------------------------------------------------------
* 	based on:
* 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
* 	(c) 2002-2003 osCommerce - www.oscommerce.com
* 	(c) 2003     nextcommerce - www.nextcommerce.org
* 	(c) 2005     xt:Commerce - www.xt-commerce.com
* 	Released under the GNU General Public License
* ---------------------------------------------------------------*/

define('SUCCESS_ORDER_SEND', 'Erfolg: Die Bestellung wurde erfolgreich nochmals per E-Mail verschickt!');
define('SUCCESS_ORDER_FIRST_SEND', 'Die PDF Rechnung wurde als Email verschickt.');
define('SUCCESS_ORDER_GENEREATE', 'Die PDF Rechnung wurde erfolgreich erstellt.');
define('TEXT_CUSTOMERS_UMSATZ', 'Gesamtumsatz exkl. Versand:');
define('TEXT_CUSTOMERS_HISTORY', 'Kundenzusatzinformation');
define('HEADING_MULTI_STATUS', 'Status Verarbeitung');
define('TEXT_STATUS', 'Status');
define('HEADING_MULTI_PDF', 'PDF Rechnungen');
define('HEADING_TITLE_PDF', 'Rechnungs-Nr.:');
define('BUTTON_COMMIT', 'Ausführen');

define('TEXT_BANK', 'Bankeinzug');
define('TEXT_BANK_OWNER', 'Kontoinhaber:');
define('TEXT_BANK_NUMBER', 'Kontonummer:');
define('TEXT_BANK_BLZ', 'BLZ:');
define('TEXT_BANK_NAME', 'Bank:');
define('TEXT_BANK_FAX', 'Einzugserm&auml;chtigung wird per Fax best&auml;tigt');
define('TEXT_BANK_STATUS', 'Pr&uuml;fstatus:');
define('TEXT_BANK_PRZ', 'Pr&uuml;fverfahren:');

define('TEXT_ORDER', 'Bestellung:');
define('TEXT_SATUS', 'derzeitiger Status:');
define('TEXT_CUSTOMERS_INFO', 'Kundeninformationen');
define('TEXT_CUSTOMERS_NAME', 'Kundenname:');
define('TEXT_CUSTOMERS_STATUS', 'Kundengruppe:');
define('TEXT_ORDER_IP', 'Bestell-IP:');
define('ENTRY_SHIPPING_INFO', 'Versandinformationen');
define('PRODUCTS', 'Produkte');
define('CUSTOMER_NOTIFIED', '<em>Kunde benachrichtigt</em>');
define('CUSTOMER_NOT_NOTIFIED', '<em>Kunde nicht benachrichtigt</em>');
define('TOTAL', 'Gesamt');
define('NAME_OF_FILE', 'Name der Datei');
define('VALID_THROUGH', 'g&uuml;ltig bis');
define('OPEN_DOWNLOADS', 'noch verf&uuml;gbare downloads');
define('LAST_DOWNLOAD', 'letzter Download');
define('DOWNLOAD_IP', 'download IP');
define('REACTIVATE', 'reaktivieren?');
define('OPEN_DOWNLOAD', '<em>download noch offen<em>');
define('PDF_DELETE_SUCCESS', 'Die PDF Rechnung wurde zusammen mit dem Datenbankeintrag gel&ouml;scht.');

define('TEXT_BANK_ERROR_1', 'Kontonummer stimmt nicht mit BLZ &uuml;berein!');
define('TEXT_BANK_ERROR_2', 'F&uuml;r diese Kontonummer ist kein Pr&uuml;fverfahren definiert!');
define('TEXT_BANK_ERROR_3', 'Kontonummer nicht pr&uuml;fbar! Pr&uuml;fverfahren nicht implementiert');
define('TEXT_BANK_ERROR_4', 'Kontonummer technisch nicht pr&uuml;fbar!');
define('TEXT_BANK_ERROR_5', 'Bankleitzahl nicht gefunden!');
define('TEXT_BANK_ERROR_8', 'Keine Bankleitzahl angegeben!');
define('TEXT_BANK_ERROR_9', 'Keine Kontonummer angegeben!');
define('TEXT_BANK_ERRORCODE', 'Fehlercode:');

define('HEADING_TITLE', 'Bestellungen');
define('HEADING_TITLE_STATUS', 'Status:');

define('TABLE_HEADING_COMMENTS', 'Kommentar');
define('TABLE_HEADING_CUSTOMERS', 'Kunden');
define('TABLE_HEADING_CUSTOMERS_CID','Kdn-Nr');
define('TABLE_HEADING_ORDER_TOTAL', 'Summe');
define('TABLE_HEADING_DATE_PURCHASED', 'Datum');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Aktion');
define('TABLE_HEADING_QUANTITY', 'Anzahl');
define('TABLE_HEADING_PRODUCTS_MODEL', 'Artikel-Nr.');
define('TABLE_HEADING_PRODUCTS', 'Artikel');
define('TABLE_HEADING_TAX', 'MwSt.');
define('TABLE_HEADING_TOTAL', 'Gesamtsumme');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', 'Netto-Preis');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', 'Brutto-Preis');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', 'Brutto-Total');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', 'Netto-Total');
define('TABLE_HEADING_AFTERBUY','Afterbuy');
define('TABLE_HEADING_COUNTRY','Land');
define('TABLE_HEADING_PAYMENT','Zahlart');

define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_CUSTOMER_NOTIFIED', 'Kunde benachrichtigt');
define('TABLE_HEADING_DATE_ADDED', 'hinzugef&uuml;gt am:');

define('ENTRY_CUSTOMER', 'Kunde:');
define('ENTRY_SOLD_TO', 'Rechnungsadresse:');
define('ENTRY_STREET_ADDRESS', 'Strasse:');
define('ENTRY_SUBURB', 'zus. Anschrift:');
define('ENTRY_CITY', 'Stadt:');
define('ENTRY_POST_CODE', 'PLZ:');
define('ENTRY_STATE', 'Bundesland:');
define('ENTRY_COUNTRY', 'Land:');
define('ENTRY_TELEPHONE', 'Telefon:');
define('ENTRY_EMAIL_ADDRESS', 'Email Adresse:');
define('ENTRY_DELIVERY_TO', 'Lieferanschrift:');
define('ENTRY_SHIP_TO', 'Lieferanschrift:');
define('ENTRY_SHIPPING_ADDRESS', 'Versandadresse:');
define('ENTRY_BILLING_ADDRESS', 'Rechnungsadresse:');
define('ENTRY_PAYMENT_METHOD', 'Zahlungsweise:');
define('ENTRY_CREDIT_CARD_TYPE', 'Kreditkartentyp:');
define('ENTRY_CREDIT_CARD_OWNER', 'Kreditkarteninhaber:');
define('ENTRY_CREDIT_CARD_NUMBER', 'Kerditkartennnummer:');
define('ENTRY_CREDIT_CARD_CVV', 'Sicherheitscode (CVV)):');
define('ENTRY_CREDIT_CARD_EXPIRES', 'Kreditkarte l&auml;uft ab am:');
define('ENTRY_SUB_TOTAL', 'Zwischensumme:');
define('ENTRY_TAX', 'MwSt.:');
define('ENTRY_SHIPPING', 'Versandkosten:');
define('ENTRY_TOTAL', 'Gesamtsumme:');
define('ENTRY_DATE_PURCHASED', 'Bestelldatum:');
define('ENTRY_STATUS', 'Status:');
define('ENTRY_DATE_LAST_UPDATED', 'zuletzt aktualisiert am:');
define('ENTRY_NOTIFY_CUSTOMER', 'Kunde benachrichtigen:');
define('ENTRY_NOTIFY_COMMENTS', 'Kommentare mitsenden:');
define('ENTRY_PRINTABLE', 'Rechnung Drucken');

define('TEXT_INFO_HEADING_DELETE_ORDER', 'Bestellung l&ouml;schen');
define('TEXT_INFO_DELETE_INTRO', 'Sind Sie sicher, das Sie diese Bestellung l&ouml;schen m&ouml;chten?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', 'Artikelanzahl dem Lager gutschreiben');
define('TEXT_DATE_ORDER_CREATED', 'erstellt am:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', 'letzte &Auml;nderung:');
define('TEXT_INFO_PAYMENT_METHOD', 'Zahlungsweise:');

define('TEXT_ALL_ORDERS', 'Alle Bestellungen');
define('TEXT_NO_ORDER_HISTORY', 'Keine Bestellhistorie verf&uuml;gbar');

define('EMAIL_SEPARATOR', '------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Status&auml;nderung Ihrer Bestellung');
define('EMAIL_TEXT_ORDER_NUMBER', 'Bestell-Nr.:');
define('EMAIL_TEXT_INVOICE_URL', 'Ihre Bestellung k&ouml;nnen Sie unter folgender Adresse einsehen:');
define('EMAIL_TEXT_DATE_ORDERED', 'Bestelldatum:');
define('EMAIL_TEXT_STATUS_UPDATE', 'Der Status Ihrer Bestellung wurde aktualisiert.' . "\n\n" . 'Neuer Status: %s' . "\n\n" . 'Bei Fragen zu Ihrer Bestellung antworten Sie bitte auf diese Email.' . "\n\n" . 'Mit freundlichen Gr&uuml;ssen' . "\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', 'Anmerkungen und Kommentare zu Ihrer Bestellung:' . "\n\n%s\n\n");

define('ERROR_ORDER_DOES_NOT_EXIST', 'Fehler: Die Bestellung existiert nicht!.');
define('SUCCESS_ORDER_UPDATED', 'Erfolg: Die Bestellung wurde erfolgreich aktualisiert.');
define('WARNING_ORDER_NOT_UPDATED', 'Hinweis: Es wurde nichts ge&auml;ndert. Daher wurde diese Bestellung nicht aktualisiert.');
define('SUCCESS_HISTORY_DELETE', 'Der History Eintrag wurde aus der Datenbank gel&ouml;scht.');

define('TABLE_HEADING_DISCOUNT','Rabatt');
define('ENTRY_CUSTOMERS_GROUP','Kundengruppe:');
define('TEXT_VALIDATING','Nicht best&auml;tigt');
define('TABLE_HEADING_EDIT','alle');
define('TEXT_DO_STATUS_CHANGE','Status wirklich &auml;ndern?');
define('HEADING_MULTI_ORDER_STATUS','Bestell Schnell Bearbeitung');
define('WARNING_ORDER_NOT_UPDATED_ALL','Es wurde nicht alles korrekt ge&auml;ndert, pr&uuml;fen Sie Ihre Eingabe');
define('BUTTON_CLOSE_PRINT_PAGES','Alle Druckfenster schlie&szlig;en.');
define('TEXT_INFO_PAYPAL_DELETE', 'PayPal Transaktions Daten auch l&ouml;schen.');


// ClickandBuy
define('HEADING_CLICKANDBUY_EMS', 'ClickandBuy EMS Events');
define('TABLE_HEADING_CLICKANDBUY_EMS_TIMESTAMP', 'Zeitstempel');
define('TABLE_HEADING_CLICKANDBUY_EMS_TYPE', 'Art');
define('TABLE_HEADING_CLICKANDBUY_EMS_ACTION', 'Ereignis');
define('TEXT_CLICKANDBUY_EMS_NO_EVENTS', 'Keine EMS-Events.');
// /ClickandBuy

define('HEADING_TITLE_SEARCH', 'Bestell-Nr.:');
define('HEADING_MULTI_PDF_DELIVERY', 'PDF Lieferscheine');
define('TEXT_TRACKING_SHIPPING', 'Versender: ');
define('TEXT_TRACKING_CODE', 'Tracking-Code: ');
