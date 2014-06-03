<?php
/* --------------------------------------------------------------
   billsafe_3.php 2012-11 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


define('MODULE_PAYMENT_BILLSAFE_3_TEXT_DESCRIPTION', 'Rechnung/Raten (BillSAFE)');
define('MODULE_PAYMENT_BILLSAFE_3_TEXT_DESCRIPTION_LINK', '<a target="_new" style="text-decoration: underline;" href="https://client.billsafe.de/">BillSAFE H&auml;ndlerportal</a>');
define('MODULE_PAYMENT_BILLSAFE_3_SYSTEM_REQUIREMENTS', 'Systemanforderungen');
define('MODULE_PAYMENT_BILLSAFE_3_TEXT_TITLE', 'Kauf auf Rechnung/Raten mit BillSAFE');
define('MODULE_PAYMENT_BILLSAFE_3_TEXT_INFO', 'Bezahlung mit BillSAFE');
define('MODULE_PAYMENT_BILLSAFE_3_STATUS_TITLE', 'BillSAFE-Zahlungsmodul aktivieren');
define('MODULE_PAYMENT_BILLSAFE_3_STATUS_DESC', 'M&ouml;chten Sie Zahlungen via BillSAFE akzeptieren?');
define('MODULE_PAYMENT_BILLSAFE_3_ORDER_STATUS_ID_TITLE', 'Bestellstatus festlegen');
define('MODULE_PAYMENT_BILLSAFE_3_ORDER_STATUS_ID_DESC', 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen');
define('MODULE_PAYMENT_BILLSAFE_3_TMPORDER_STATUS_ID_TITLE', 'Tempor&auml;ren Bestellstatus festlegen');
define('MODULE_PAYMENT_BILLSAFE_3_TMPORDER_STATUS_ID_DESC', 'Bestellung w&auml;hrend der Eingabe der Zahlungsdaten auf diesen Status setzen');
define('MODULE_PAYMENT_BILLSAFE_3_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_BILLSAFE_3_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
define('MODULE_PAYMENT_BILLSAFE_3_ZONE_TITLE', 'Zahlungszone');
define('MODULE_PAYMENT_BILLSAFE_3_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
define('MODULE_PAYMENT_BILLSAFE_3_ALLOWED_TITLE', 'Erlaubte Zonen');
define('MODULE_PAYMENT_BILLSAFE_3_ALLOWED_DESC', 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
define('MODULE_PAYMENT_BILLSAFE_3_MERCHANT_ID_TITLE', 'Merchant ID');
define('MODULE_PAYMENT_BILLSAFE_3_MERCHANT_ID_DESC', 'Ihre Merchant ID');
define('MODULE_PAYMENT_BILLSAFE_3_MERCHANT_LICENSE_TITLE', 'Merchant License');
define('MODULE_PAYMENT_BILLSAFE_3_MERCHANT_LICENSE_DESC', 'Ihre Merchant License');
define('MODULE_PAYMENT_BILLSAFE_3_URL_IMAGE_TITLE', 'Logo-Bild');
define('MODULE_PAYMENT_BILLSAFE_3_URL_IMAGE_DESC', 'URL einer Grafik-Datei, welche im Payment Gateway angezeigt werden soll. Die Grafik darf maximal folgende Abmessungen haben: H&ouml;he: 60 Pixel, Breite: 130 Pixel.');
define('MODULE_PAYMENT_BILLSAFE_3_IMAGE_CODE_TITLE', 'Logo-Code');
define('MODULE_PAYMENT_BILLSAFE_3_IMAGE_CODE_DESC', 'Von BillSAFE mitgeteilte Logo-URL-ID');
define('MODULE_PAYMENT_BILLSAFE_3_PREVALIDATE_TITLE', 'Vorpr&uuml;fung ausf&uuml;hren');
define('MODULE_PAYMENT_BILLSAFE_3_PREVALIDATE_DESC', 'Zahlart BillSAFE nur nach positiver Vorpr&uuml;fung des Kunden anzeigen');
define('MODULE_PAYMENT_BILLSAFE_3_SANDBOX_TITLE', 'Sandbox-Modus');
define('MODULE_PAYMENT_BILLSAFE_3_SANDBOX_DESC', 'Sandbox-Modus (Testmodus) aktivieren');
define('MODULE_PAYMENT_BILLSAFE_3_ADDRESSES_MUST_MATCH', 'F&uuml;r den Kauf auf Rechnung mit BillSAFE m&uuml;ssen Versand- und Rechnungsadresse &uuml;bereinstimmen. Mit den angegebenen Daten ist eine Nutzung von BillSAFE nicht m&ouml;glich!');
define('MODULE_PAYMENT_BILLSAFE_3_PREPAREORDER_FAILED', 'BillSAFE steht derzeit nicht zur Verfuegung');
define('MODULE_PAYMENT_BILLSAFE_3_LAYER_TITLE', 'Payment-Layer');
define('MODULE_PAYMENT_BILLSAFE_3_LAYER_DESC', 'Payment-Layer verwenden?');
define('MODULE_PAYMENT_BILLSAFE_3_OK', 'OK');
define('MODULE_PAYMENT_BILLSAFE_3_MISSING', 'FEHLT');
define('MODULE_PAYMENT_BILLSAFE_3_GERMANY_ONLY', 'BillSAFE steht nur f&uuml;r Rechnungs- und Lieferadressen in Deutschland zur Verf&uuml;gung');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENTAMOUNT', 'kleinste Rate');
define('MODULE_PAYMENT_BILLSAFE_3_INSTALLMENTCOUNT', 'Anzahl Monatsraten');
define('MODULE_PAYMENT_BILLSAFE_3_PROCESSINGFEE', 'Bearbeitungsgeb&uuml;hr');
define('MODULE_PAYMENT_BILLSAFE_3_ANNUALPERCENTAGERATE', 'eff. Jahreszins');
define('MODULE_PAYMENT_BILLSAFE_3_PLEASE_WAIT', 'bitte warten ...');

define('BILLSAFE3_ARTICLE_GROSSPRICE', 'Bruttopreis');
define('BILLSAFE3_ARTICLE_NAME', 'Bezeichnung');
define('BILLSAFE3_ARTICLE_NUMBER', 'Art.-Nr.');
define('BILLSAFE3_ARTICLE_QTYSHIPPED', 'Anzahl versandt');
define('BILLSAFE3_ARTICLE_QUANTITY', 'Anzahl');
define('BILLSAFE3_ARTICLES', 'Artikelliste');
define('BILLSAFE3_ARTICLES_UPDATED', 'Die Artikelliste wurde aktualisiert.');
define('BILLSAFE3_ARTICLE_TAX', 'USt. (%)');
define('BILLSAFE3_ARTICLE_TYPE_GOODS', 'Ware');
define('BILLSAFE3_ARTICLE_TYPE_HANDLING', 'Zahlartenaufschlag');
define('BILLSAFE3_ARTICLE_TYPE_SHIPMENT', 'Versandkosten');
define('BILLSAFE3_ARTICLE_TYPE', 'Typ');
define('BILLSAFE3_ARTICLE_TYPE_VOUCHER', 'Gutschein/Rabatt');
define('BILLSAFE3_CANNOT_RETRIEVE_PAYMENT_INSTRUCTION', 'Zahlungsinformationen k&ouml;nnen nicht abgerufen werden.');
define('BILLSAFE3_DIRECT_PAYMENT', 'Direktzahlung');
define('BILLSAFE3_DPAMOUNT', 'Betrag');
define('BILLSAFE3_DPDATE', 'Datum');
define('BILLSAFE3_GENERAL_ERROR', 'Es ist ein Fehler aufgetreten. Bitte wenden Sie sich an den Shopbetreiber.');
define('BILLSAFE3_GENERAL_INFO', 'Informationen');
define('BILLSAFE3_LABEL_DIRECT_PAYMENTS', 'Direktzahlungen');
define('BILLSAFE3_LABEL_HANDLING_CHARGES', 'Vereinbarte Aufschl&auml;ge');
define('BILLSAFE3_LABEL_ORDERS_ID', 'Bestellung Nr.');
define('BILLSAFE3_LABEL_PAYOUTS', 'Auszahlung(en)');
define('BILLSAFE3_LABEL_RETURNS', 'Retoure(n)');
define('BILLSAFE3_LABEL_TRANSACTION_ID', 'Transaktionsnr.');
define('BILLSAFE3_NO_ARTICLES', 'keine Artikel');
define('BILLSAFE3_PARCEL_SERVICE_NAME', 'anderer Paketdienst');
define('BILLSAFE3_PARCEL_SERVICE_NONE', 'keine Angabe');
define('BILLSAFE3_PARCEL_SERVICE_OTHER', 'anderer (s.u.)');
define('BILLSAFE3_PARCEL_SERVICE', 'Paketdienst');
define('BILLSAFE3_PARCEL_TRACKINGID', 'Paketnummer/Tracking-ID');
define('BILLSAFE3_PAUSE_TRANSACTION_DAYS', 'Tage');
define('BILLSAFE3_PAUSE_TRANSACTION', 'Zahlungspause');
define('BILLSAFE3_PAYMENT_ACCOUNTNO', 'Kontonummer');
define('BILLSAFE3_PAYMENT_AMOUNT', 'Betrag');
define('BILLSAFE3_PAYMENT_BANKCODE', 'Bankleitzahl');
define('BILLSAFE3_PAYMENT_BIC', 'BIC');
define('BILLSAFE3_PAYMENT_DAYS', 'Tage');
define('BILLSAFE3_PAYMENT_IBAN', 'IBAN');
define('BILLSAFE3_PAYMENT_INFO', 'Zahlungsinfo f&uuml;r den Kunden');
define('BILLSAFE3_PAYMENT_PERIOD', 'Zahlungsfrist ab Versand');
define('BILLSAFE3_PAYMENT_RECIPIENT', 'Empf&auml;nger');
define('BILLSAFE3_PAYMENT_REFERENCE', 'Verwendungszweck');
define('BILLSAFE3_SEND_DIRECTPAYMENT', 'Direktzahlung melden');
define('BILLSAFE3_SEND_PAUSETRANSACTION', 'Stundung senden');
define('BILLSAFE3_SEND_SHIPMENT', 'Versand melden');
define('BILLSAFE3_SHIPMENT_REPORTED', 'Versand wurde gemeldet.');
define('BILLSAFE3_SHIPMENT', 'Versand melden');
define('BILLSAFE3_SHIPPED_ARTICLES', 'Bisherige Versandmeldungen');
define('BILLSAFE3_SHIPPING_DATE', 'Versanddatum');
define('BILLSAFE3_SUM', 'Summe');
define('BILLSAFE3_TRANSACTION_PAUSED', 'Stundung gesendet.');
define('BILLSAFE3_UPDATE_ARTICLELIST_FAILED', 'Aktualisierung der Artikelliste ist fehlgeschlagen.');
define('BILLSAFE3_UPDATE_ARTICLES', 'Aktualisierung senden');
define('BILLSAFE3_UPTO', 'bis');
define('BILLSAFE3_REPORTSHIPMENT_FAILED', 'FEHLER beim Senden der Versandmeldung!');
define('BILLSAFE3_AND_SET_ORDERS_ID_TO', 'und Status der Bestellung setzen auf');
define('BILLSAFE3_NO_CHANGE', 'nicht &auml;ndern');
define('BILLSAFE3_ORDERS_STATUS_UPDATED', 'Bestellstatus wurde ge&auml;ndert');
define('BILLSAFE3_SHIPMENT_REPORTED', 'Versand gemeldet');
define('BILLSAFE3_PAUSETRANSACTION_FAILED', 'Stundung fehlgeschlagen');
define('BILLSAFE3_DIRECT_PAYMENT_REPORTED', 'Direktzahlung wurde gemeldet.');
define('BILLSAFE3_WARNING_UNCONFIGURED', 'WARNUNG: Die BillSAFE-Funktionen k&ouml;nnen nicht verwendet werden, da das BillSAFE-Zahlungsmodul nicht konfiguriert ist.');
define('BILLSAFE3_METHODS_GENERAL', 'Zahlung auf Rechnung oder Raten');
define('BILLSAFE3_METHODS_INVOICE_ONLY', 'Zahlung auf Rechnung');
define('BILLSAFE3_METHODS_INSTALLMENT_ONLY', 'Zahlung auf Raten');
define('BILLSAFE3_METHODS_BOTH', 'Zahlung auf Rechnung oder Raten m&ouml;glich');
define('BILLSAFE3_WARNING_ORDER_NOT_COMPLETED', 'Der Bestellvorgang wurde nicht abgeschlossen.');
define('BILLSAFE3_REPORTDIRECTPAYMENT_FAILED', 'Meldung der Direktzahlung ist FEHLGESCHLAGEN.');
define('BILLSAFE3_CREDENTIALS_ARE_VALID', 'Die eingegebenen Zugangsdaten sind <span style="color:green;font-weight:bold;">g&uuml;ltig</span>.');
define('BILLSAFE3_CREDENTIALS_ARE_INVALID', 'Die eingegebenen Zugangsdaten sind <span style="color:red;font-weight:bold;font-size:1.3em;">ung&uuml;ltig</span>.');
define('BILLSAFE3_REGET_PAYMENTINFO', 'Zahlungsinfo aktualisieren');
define('BILLSAFE3_PAYMENT_INFO_UPDATED', 'Zahlungsinfo aktualisiert');
define('BILLSAFE3_CANCEL_ARTICLES', 'alle stornieren');
define('BILLSAFE3_ARE_YOU_SURE', 'Wirklich fortfahren?');
define('BILLSAFE3_ARTICLES_CANCELLED', 'Artikel wurden storniert');
