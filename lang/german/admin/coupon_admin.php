<?php
/* ------------------------------------------------------------------------------ 

	$Id: coupon_admin.php, v. 1.80 - 30.10.2009 - jj

	Contribution for XT-Commerce http://www.xt-commerce.com
	Released under the GNU General Public License
	
------------------------------------------------------------------------------ 
	
	Web: http://www.web-looks.de
	Email: support@web-looks.de
	Copyright (c) 2008-2009 by Jens Justen

------------------------------------------------------------------------------ */


// Titel
define('HEADING_TITLE', 'Übersicht Kupons und Gutscheine');
define('HEADING_EMAIL_TITLE', 'Bestehenden Rabatt an Kunden versenden');
define('TEXT_HEADING_NEW_COUPON', 'Neuen Rabatt erstellen');

// Funktionen der Tabelle
define('TEXT_STATUS', 'Status: ');
define('TEXT_COUPON_ACTIVE', 'Aktive');
define('TEXT_COUPON_INACTIVE', 'Beendete');
define('TEXT_COUPON_ALL', 'Alle');
define('TEXT_TYPE', 'Typ: ');
define('TEXT_SHOWN_NUMBER', 'Anzahl pro Seite: ');
define('SHOW_PAGE_STANDARD', 20); // Standardwert
$pages_array = array();
$pages_array[] = array('id' => '20', 'text' => '20');
$pages_array[] = array('id' => '50', 'text' => '50');
$pages_array[] = array('id' => '100', 'text' => '100');
$pages_array[] = array('id' => '500', 'text' => '500');
$pages_array[] = array('id' => '1000', 'text' => '1000');
define('TEXT_SEARCH', 'Suche: ');

// Tabellen Spalten
define('STATUS', 'Status');
define('STATUS_ACTIVE', 'AKTIV');
define('STATUS_INACTIVE', 'BEENDET');
define('COUPON_NAME', 'Name');
define('COUPON_AMOUNT', 'Wert');
define('COUPON_CODE', 'Code');
define('DATE_CREATED', 'Erstellt am');
define('TABLE_HEADING_ACTION', 'Aktion');

// Email
define('TEXT_COUPON', 'Name: ');
define('TEXT_SUBJECT', 'Betreff:');
define('TEXT_FROM', 'Von:');
define('TEXT_MESSAGE', 'Nachricht:');
define('TEXT_CUSTOMER', 'Kunde:');
define('TEXT_SELECT_CUSTOMER', 'Kunde ausw&auml;hlen');
define('TEXT_ALL_CUSTOMERS', 'Alle Kunden');
define('TEXT_NEWSLETTER_CUSTOMERS', 'Alle Newsletter Abonnenten');
define('NOTICE_EMAIL_SENT_TO', 'Notiz: eMail versendet an: %s');
define('ERROR_NO_CUSTOMER_SELECTED', 'Fehler: Kein Kunde ausgew&auml;hlt');

// Neu erstellen und Box Rechte Seite
define('TEXT_FREE_SHIPPING', 'Versandkostenfrei');
define('TEXT_NO_FREE_SHIPPING', 'Nicht Versandkostenfrei');
define('COUPON_STARTDATE', 'G&uuml;ltig ab');
define('COUPON_FINISHDATE', 'G&uuml;ltig bis');
define('COUPON_FREE_SHIP', 'Versandkostenfrei');
define('COUPON_DESC', 'Beschreibung');
define('COUPON_TYPE', 'Rabatt Typ');
define('COUPON_MIN_ORDER', 'Mindestbestellwert');
define('COUPON_USES_COUPON', 'Verwendungen pro Kupon');
define('COUPON_USES_USER', 'Verwendungen pro Kunde');
define('VIEW', ' &raquo; ANZEIGEN');
define('COUPON_PRODUCTS', 'G&uuml;ltige Artikel');
define('COUPON_CATEGORIES', 'G&uuml;ltige Kategorien');
define('DATE_MODIFIED', 'ge&auml;ndert am');
define('TEXT_NEW_INTRO', 'Bitte geben Sie die folgenden Informationen f&uuml;r den neuen Rabatt an.<br />');
define('TYPE_G', 'Gutschein');
define('TYPE_F', 'Festbetrag Kupon');
define('TYPE_P', 'Prozentualer Kupon');
define('TYPE_S', 'Versandkostenfrei Kupon');
define('ERROR_NO_COUPON_AMOUNT', 'Fehler: Kein Wert angegeben');
define('ERROR_NO_COUPON_TYPE', 'Fehler: Kein Rabatt Typ ausgew&auml;hlt');
define('ERROR_DOUBLE_PRODUCTS_CATS', 'Fehler: &quot;G&uuml;ltige Artikel&quot; und &quot;G&uuml;ltige Kategorien&quot; sind nicht kombinierbar! Bitte nutzen Sie nur eine der beiden Beschr&auml;nkungen.');
define('DELETE_NOW', 'Rabatt jetzt l&ouml;schen');
define('TEXT_CONFIRM_DELETE', 'Sind Sie sicher, dass Sie diesen Rabatt l&ouml;schen wollen?');
define('BUTTON_DELETE_ALL_INAVTIVE', 'Alle mit Status &quot;Beendet&quot; l&ouml;schen');
define('TEXT_CONFIRM_DELETE_INACTIVE', 'Sind Sie sicher, dass Sie alle inaktiven Rabatte (Status &quot;Beendet&quot;) l&ouml;schen wollen?');
define('NONE', 'keine Beschr&auml;nkung');
define('ERROR', 'FEHLER');
define('ERROR_NO_COUPONS_EXIST', 'Es wurden keine Gutscheine gefunden');

// Hilfe
define('COUPON_NAME_HELP', 'Eine Kurzbezeichnung f&uuml;r den Rabatt');
define('COUPON_DESC_HELP', 'Beschreibung des Rabatts f&uuml;r den Kunden');
define('COUPON_AMOUNT_HELP', 'Tragen Sie hier den Betrag f&uuml;r diesen Rabatt ein. Geben Sie dabei nur eine Zahl ein und keine zus&auml;tzlichen Zeichen (Bspw.: 10.5 oder 15).');
define('COUPON_CODE_HELP', 'Hier k&ouml;nnen Sie einen eigenen Code eintragen (max. 16 Zeichen). Lassen Sie das Feld frei, dann wird dieser Code automatisch generiert.');
define('COUPON_STARTDATE_HELP', 'Das Datum ab dem der Kupon g&uuml;ltig ist.');
define('COUPON_FINISHDATE_HELP', 'Das Datum an dem der Kupon abl&auml;uft.');
define('TYPE_S_HELP', 'Kupon f&uuml;r eine versandkostenfreie Lieferung. Wert wird bei der Bestellung berechnet und entspricht genau den vom Kunden gew&auml;hlten Versandkosten.');
define('TYPE_F_HELP', 'Kupon mit einem festen Betrag als Rabatt.');
define('TYPE_P_HELP', 'Kupon mit einem prozentualen Wert als Rabatt.');
define('TYPE_G_HELP', 'Gutschein mit einem festen Betrag als Rabatt und ohne Beschränkungen.');
define('COUPON_MIN_ORDER_HELP', 'Mindestbestellwert ab dem dieser Kupon g&uuml;ltig ist.');
define('COUPON_USES_COUPON_HELP', 'Tragen Sie hier ein wie oft dieser Kupon insgesamt eingel&ouml;st werden darf. Lassen Sie das Feld frei, dann ist die Benutzung unlimitiert.');
define('COUPON_USES_USER_HELP', 'Tragen Sie hier ein wie oft ein einzelner Kunde diesen Kupon einl&ouml;sen darf. Lassen Sie das Feld frei, dann ist die Benutzung pro Kunde unlimitiert.');
define('COUPON_PRODUCTS_HELP', 'Eine durch Komma getrennte Liste von product_ids f&uuml;r die dieser Kupon g&uuml;ltig ist. Ein leeres Feld bedeutet keine Beschr&auml;nkung auf bestimmte Produkte. Nicht kombinierbar mit &quot;G&uuml;ltige Kategorien&quot;.');
define('COUPON_CATEGORIES_HELP', 'Eine durch Komma getrennte Liste von Kategorien (cpaths) f&uuml;r die dieser Kupon g&uuml;ltig ist. Ein leeres Feld bedeutet keine Beschr&auml;nkung auf bestimmte Kategorien. Nicht kombinierbar mit &quot;G&uuml;ltige Artikel&quot;.');

// Statistik
define('CUSTOMER_NAME', 'Kunden Name');
define('CUSTOMER_ID', 'Kunden Nr.');
define('REDEEM_DATE', 'eingel&ouml;st am');
define('IP_ADDRESS', 'IP Adresse');
define('TEXT_REDEMPTIONS', 'Einl&ouml;sung');
define('TEXT_REDEMPTIONS_TOTAL', 'Insgesamt');
define('TEXT_REDEMPTIONS_CUSTOMER', 'F&uuml;r diesen Kunden');
?>