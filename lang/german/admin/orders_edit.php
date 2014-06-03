<?php
/*-----------------------------------------------------------------
* 	$Id: orders_edit.php 420 2013-06-19 18:04:39Z akausch $
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




// Allgemeine Texte
define('MODULE_SHIPPING_FREE_TAX_CLASS', 'Steuerfreie Versandkosten');
define('TABLE_HEADING', 'Bestelldaten bearbeiten');
define('TABLE_HEADING_ORDER', 'Bestellung Nr:&nbsp;');
define('TEXT_SAVE_ORDER', 'Bestellungsbearbeitung beenden und Bestellung neu berechnen.');

define('TEXT_EDIT_ADDRESS', 'Adressdaten und Kundendaten bearbeiten und einf&uuml;gen.');
define('TEXT_EDIT_PRODUCTS', 'Artikel und Artikeloptionen bearbeiten und einf&uuml;gen.');
define('TEXT_EDIT_OTHER', 'Versandkosten, Zahlungsweisen, W&auml;hrungen, Sprachen usw bearbeiten und einf&uuml;gen.');

define('IMAGE_EDIT_ADDRESS', 'Adressen bearbeiten oder einf&uuml;gen');
define('IMAGE_EDIT_PRODUCTS', 'Artikel und Optionen bearbeiten oder einf&uuml;gen');
define('IMAGE_EDIT_OTHER', 'Versandkosten Zahlung Gutscheine usw. bearbeiten oder einf&uuml;gen');

// AdressР Т‘nderung
define('TEXT_INVOICE_ADDRESS', 'Kundenadresse');
define('TEXT_SHIPPING_ADDRESS', 'Versandadresse');
define('TEXT_BILLING_ADDRESS', 'Rechnungsadresse');


define('TEXT_COMPANY', 'Firma:');
define('TEXT_NAME', 'Name:');
define('TEXT_STREET', 'Strasse:');
define('TEXT_SUBURB', 'Zusatz:');
define('TEXT_ZIP', 'Plz:');
define('TEXT_CITY', 'Stadt:');
define('TEXT_COUNTRY', 'Land:');
define('TEXT_CUSTOMER_GROUP', 'Kundengruppe in der Bestellung');
define('TEXT_CUSTOMER_EMAIL', 'eMail:');
define('TEXT_CUSTOMER_TELEPHONE', 'Telefon:');
define('TEXT_CUSTOMER_UST', 'UstID:');

// Artikelbearbeitung

define('TEXT_EDIT_GIFT', 'Gutscheine und Rabatt bearbeiten oder einf&uuml;gen');
define('TEXT_EDIT_ADDRESS_SUCCESS', 'Adress&auml;nderung wurde gespeichert.');
define('TEXT_SMALL_NETTO', '(Netto)');
define('TEXT_PRODUCT_ID', 'pID:');
define('TEXT_PRODUCTS_MODEL', 'Art.Nr:');
define('TEXT_QUANTITY', 'Anzahl:');
define('TEXT_PRODUCT', 'Artikel:');
define('TEXT_TAX', 'MWSt.:');
define('TEXT_PRICE', 'Preis:');
define('TEXT_FINAL', 'Gesamt:');
define('TEXT_PRODUCT_SEARCH', 'Artikelsuche:');

define('TEXT_PRODUCT_OPTION', 'Artikelmerkmale:');
define('TEXT_PRODUCT_OPTION_VALUE', 'Optionswert:');
define('TEXT_PRICE', 'Preis:');
define('TEXT_PRICE_PREFIX', 'Price Prefix:');

// Sonstiges

define('TEXT_PAYMENT', 'Zahlungsweise:');
define('TEXT_SHIPPING', 'Versandart:');
define('TEXT_LANGUAGE', 'Sprache:');
define('TEXT_CURRENCIES', 'W&auml;hrungen:');
define('TEXT_ORDER_TOTAL', 'Zusammenfassung:');
define('TEXT_SAVE', 'Speichern');
define('TEXT_ACTUAL', 'Aktuell: ');
define('TEXT_NEW', 'Neu: ');
define('TEXT_PRICE', 'Kosten: ');
define('TEXT_ACTUAL', 'aktuell:');
define('TEXT_NEW', 'neu:');


define('TEXT_ADD_TAX','enthaltene ');
define('TEXT_NO_TAX','zzgl. ');

define('TEXT_ORDERS_EDIT_INFO', '<b>Wichtige Hinweise:</b><br>
Bitte bei den Adress/Kundendaten die richtige Kundengruppe w&auml;hlen <br>
Bei einem Wechsel der Kundengruppe sind alle Einzelposten der Rechnung neu abzuspeichern!<br>
Versandkosten m&uuml;ssen manuell ge&auml;ndert werden!<br>
Hierbei sind je nach Kundengruppe die Versandkosten brutto oder netto einzutragen!<br>
');

define('TEXT_CUSTOMER_GROUP_INFO', ' <span class="myerrorlog">Bei einem Wechsel der Kundengruppe sind alle Einzelposten der Rechnung neu abzuspeichern!</span>');

define('TEXT_ORDER_TITLE', 'Titel:');
define('TEXT_ORDER_VALUE', 'Wert:');
define('ERROR_INPUT_TITLE', 'Keine Eingabe bei Titel');
define('ERROR_INPUT_EMPTY', 'Keine Eingabe bei Titel und Wert');
define('ERROR_INPUT_SHIPPING_TITLE', 'Es wurde noch kein Versandkostenmodul ausgew&auml;hlt!');


define('TEXT_ORDERS_PRODUCT_EDIT_INFO', '<b>Hinweis:</b> Bei Staffelpreisen muss der Einzelpreis manuell angepasst werden!');


define('TEXT_FIRSTNAME', 'Vorname:');
define('TEXT_LASTNAME', 'Nachname:');

define('TEXT_SAVE_CUSTOMERS_DATA', 'Kundendaten speichern');
?>