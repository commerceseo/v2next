<?php
/* ------------------------------------------------------------------------------ 

	$Id: gv_mail.php 420 2013-06-19 18:04:39Z akausch $Id: gv_mail.php, v. 1.76 - 09.10.2009 - jj

	Contribution for XT-Commerce http://www.xt-commerce.com
	Released under the GNU General Public License
	
------------------------------------------------------------------------------ 
	
	Web: http://www.web-looks.de
	Email: support@web-looks.de
	Copyright (c) 2008-2009 by Jens Justen

------------------------------------------------------------------------------ */


// Titel
define('HEADING_TITLE', 'Neuen Rabatt an Kunden versenden');

// Formular Felder
define('TEXT_CUSTOMER', 'Kunde:');
define('TEXT_SUBJECT', 'Betreff:');
define('TEXT_COUPON_NAME', 'Name:');
define('TEXT_FROM', 'Absender:');
define('TEXT_TO', 'Email an:');
define('TEXT_SELECT_CUSTOMER', 'Kunde ausw&auml;hlen');
define('TEXT_ALL_CUSTOMERS', 'Alle Kunden');
define('TEXT_NEWSLETTER_CUSTOMERS', 'An alle Rundschreiben-Abonnenten');
define('TEXT_AMOUNT', 'Wert:');
define('TEXT_MESSAGE', 'Nachricht:');
define('COUPON_STARTDATE', 'G&uuml;ltig ab:');
define('COUPON_FINISHDATE', 'G&uuml;ltig bis:');
define('COUPON_MIN_ORDER', 'Mindestbestellwert:');
define('COUPON_USES_COUPON', 'Verwendungen pro Kupon:');
define('COUPON_USES_USER', 'Verwendungen pro Kunde:');
define('VIEW', ' &raquo; ANZEIGEN');
define('COUPON_PRODUCTS', 'G&uuml;ltige Artikel:');
define('COUPON_CATEGORIES', 'G&uuml;ltige Kategorien:');
define('COUPON_TYPE', 'Rabatt Typ:');
define('TYPE_G', 'Gutschein');
define('TYPE_F', 'Festbetrag Kupon');
define('TYPE_P', 'Prozentualer Kupon');
define('TYPE_S', 'Versandkostenfrei Kupon');
define('TEXT_FREE_SHIPPING', 'Versandkostenfrei');

// Hilfe
define('TEXT_SINGLE_EMAIL', 'Benutzen Sie dieses Feld nur f&uuml;r einzelne Emails, ansonsten bitte das Feld ' . TEXT_CUSTOMER . ' benutzen');
define('TEXT_INFO_AMOUNT', 'Tragen Sie hier den Betrag f&uuml;r diesen Rabatt ein. Geben Sie dabei nur eine Zahl ein und keine zus&auml;tzlichen Zeichen (Bspw.: 10.5 oder 15).');
define('TEXT_INFO_COUPON_NAME', 'Eine Kurzbezeichnung f&uuml;r den Rabatt');
define('COUPON_CODE_HELP', 'Hier k&ouml;nnen Sie einen eigenen Code eintragen (max. 16 Zeichen). Lassen Sie das Feld frei, dann wird dieser Code automatisch generiert.');
define('COUPON_STARTDATE_HELP', 'Das Datum ab dem der Kupon g&uuml;ltig ist.');
define('COUPON_FINISHDATE_HELP', 'Das Datum an dem der Kupon abl&auml;uft.');
define('TYPE_S_HELP', 'Kupon f&uuml;r eine versandkostenfreie Lieferung. Wert wird bei der Bestellung berechnet und entspricht genau den vom Kunden gew&auml;hlten Versandkosten.');
define('TYPE_F_HELP', 'Kupon mit einem festen Betrag als Rabatt.');
define('TYPE_P_HELP', 'Kupon mit einem prozentualen Wert als Rabatt.');
define('TYPE_G_HELP', 'Gutschein mit einem festen Betrag als Rabatt und ohne Beschr&auml;nkungen.');
define('COUPON_MIN_ORDER_HELP', 'Mindestbestellwert ab dem dieser Kupon g&uuml;ltig ist.');
define('COUPON_USES_COUPON_HELP', 'Tragen Sie hier ein wie oft dieser Kupon insgesamt eingel&ouml;st werden darf. Lassen Sie das Feld frei, dann ist die Benutzung unlimitiert.');
define('COUPON_USES_USER_HELP', 'Tragen Sie hier ein wie oft ein einzelner Kunde diesen Kupon einl&ouml;sen darf. Lassen Sie das Feld frei, dann ist die Benutzung pro Kunde unlimitiert.');
define('COUPON_PRODUCTS_HELP', 'Eine durch Komma getrennte Liste von product_ids f&uuml;r die dieser Kupon g&uuml;ltig ist. Ein leeres Feld bedeutet keine Beschr&auml;nkung auf bestimmte Produkte. Nicht kombinierbar mit &quot;G&uuml;ltige Kategorien&quot;.');
define('COUPON_CATEGORIES_HELP', 'Eine durch Komma getrennte Liste von Kategorien (cpaths) f&uuml;r die dieser Kupon g&uuml;ltig ist. Ein leeres Feld bedeutet keine Beschr&auml;nkung auf bestimmte Kategorien. Nicht kombinierbar mit &quot;G&uuml;ltige Artikel&quot;.');

// Fehlermeldungen und Hinweise
define('NOTICE_EMAIL_SENT_TO', 'Hinweis: Email wurde versandt an: %s');
define('ERROR_NO_CUSTOMER_SELECTED', 'Fehler: Es wurde kein Kunde ausgew&auml;hlt');
define('ERROR_NO_AMOUNT_SELECTED', 'Fehler: Sie haben keinen Betrag angegeben');
define('ERROR_NO_TYPE_SELECTED', 'Fehler: Kein Rabatt Typ ausgew&auml;hlt');
define('ERROR_DOUBLE_PRODUCTS_CATS', 'Fehler: &quot;G&uuml;ltige Artikel&quot; und &quot;G&uuml;ltige Kategorien&quot; sind nicht kombinierbar! Bitte nutzen Sie nur eine der beiden Beschr&auml;nkungen.');
?>