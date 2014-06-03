<?php
/*-----------------------------------------------------------------
* 	$Id: dpd.php 496 2013-07-17 09:56:08Z akausch $
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

define('MODULE_SHIPPING_DPD_TEXT_TITLE', 'DPD');
define('MODULE_SHIPPING_DPD_TEXT_DESCRIPTION', 'Versandkosten Zonenbasierend');
define('MODULE_SHIPPING_DPD_TEXT_WAY', 'Versand nach:');
define('MODULE_SHIPPING_DPD_TEXT_UNITS', 'kg');
define('MODULE_SHIPPING_DPD_INVALID_ZONE', 'Es ist leider kein Versand in dieses Land m&ouml;glich!');
define('MODULE_SHIPPING_DPD_UNDEFINED_RATE', 'Unversicherter Versand nicht möglich. Bitte andere Versandart wählen.');

define('MODULE_SHIPPING_DPD_STATUS_TITLE' , 'Versandkosten nach Zonen Methode aktivieren');
define('MODULE_SHIPPING_DPD_STATUS_DESC' , 'M&ouml;chten Sie Versandkosten nach Zonen anbieten?');
define('MODULE_SHIPPING_DPD_ALLOWED_TITLE' , 'Erlaubte Versandzonen');
define('MODULE_SHIPPING_DPD_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, in welche ein Versand möglich sein soll. (z.B. AT,DE (lassen Sie dieses Feld leer, wenn Sie alle Zonen erlauben wollen))');
define('MODULE_SHIPPING_DPD_TAX_CLASS_TITLE' , 'Steuerklasse');
define('MODULE_SHIPPING_DPD_TAX_CLASS_DESC' , 'Folgende Steuerklasse an Versandkosten anwenden');
define('MODULE_SHIPPING_DPD_SORT_ORDER_TITLE' , 'Sortierreihenfolge');
define('MODULE_SHIPPING_DPD_SORT_ORDER_DESC' , 'Reihenfolge der Anzeige');

define('MODULE_SHIPPING_DPD_COUNTRIES_1_TITLE' , 'Zone 1 L&auml;nder');
define('MODULE_SHIPPING_DPD_COUNTRIES_1_DESC' , 'Durch Komma getrennte Liste von ISO L&auml;ndercodes (2 Zeichen), welche Teil von Zone 1 sind.');
define('MODULE_SHIPPING_DPD_COST_1_TITLE' , 'Zone 1 Versandkosten');
define('MODULE_SHIPPING_DPD_COST_1_DESC' , 'Versandkosten nach Zone 1 Bestimmungsorte, basierend auf einer Gruppe von max. Bestellgewichten. Beispiel: 3:8.50,7:10.50,... Gewicht von kleiner oder gleich 3 w&uuml;rde 8.50 für die Zone 1 Bestimmungsl&auml;nder kosten.');
define('MODULE_SHIPPING_DPD_HANDLING_1_TITLE' , 'Zone 1 Handling Geb&uuml;hr');
define('MODULE_SHIPPING_DPD_HANDLING_1_DESC' , 'Handling Geb&uuml;hr für diese Versandzone');

define('MODULE_SHIPPING_DPD_COUNTRIES_2_TITLE' , 'Zone 2 L&auml;nder');
define('MODULE_SHIPPING_DPD_COUNTRIES_2_DESC' , 'Durch Komma getrennte Liste von ISO L&auml;ndercodes (2 Zeichen), welche Teil von Zone 2 sind.');
define('MODULE_SHIPPING_DPD_COST_2_TITLE' , 'Zone 2 Versandkosten');
define('MODULE_SHIPPING_DPD_COST_2_DESC' , 'Versandkosten nach Zone 2 Bestimmungsorte, basierend auf einer Gruppe von max. Bestellgewichten. Beispiel: 3:8.50,7:10.50,... Gewicht von kleiner oder gleich 3 w&uuml;rde 8.50 für die Zone 1 Bestimmungsl&auml;nder kosten.');
define('MODULE_SHIPPING_DPD_HANDLING_2_TITLE' , 'Zone 2 Handling Geb&uuml;hr');
define('MODULE_SHIPPING_DPD_HANDLING_2_DESC' , 'Handling Geb&uuml;hr für diese Versandzone');

define('MODULE_SHIPPING_DPD_COUNTRIES_3_TITLE' , 'Zone 3 L&auml;nder');
define('MODULE_SHIPPING_DPD_COUNTRIES_3_DESC' , 'Durch Komma getrennte Liste von ISO L&auml;ndercodes (2 Zeichen), welche Teil von Zone 3 sind.');
define('MODULE_SHIPPING_DPD_COST_3_TITLE' , 'Zone 3 Versandkosten');
define('MODULE_SHIPPING_DPD_COST_3_DESC' , 'Versandkosten nach Zone 3 Bestimmungsorte, basierend auf einer Gruppe von max. Bestellgewichten. Beispiel: 3:8.50,7:10.50,... Gewicht von kleiner oder gleich 3 w&uuml;rde 8.50 für die Zone 1 Bestimmungsl&auml;nder kosten.');
define('MODULE_SHIPPING_DPD_HANDLING_3_TITLE' , 'Zone 3 Handling Geb&uuml;hr');
define('MODULE_SHIPPING_DPD_HANDLING_3_DESC' , 'Handling Geb&uuml;hr für diese Versandzone');

define('MODULE_SHIPPING_DPD_COUNTRIES_4_TITLE' , 'Zone 4 L&auml;nder');
define('MODULE_SHIPPING_DPD_COUNTRIES_4_DESC' , 'Durch Komma getrennte Liste von ISO L&auml;ndercodes (2 Zeichen), welche Teil von Zone 4 sind.');
define('MODULE_SHIPPING_DPD_COST_4_TITLE' , 'Zone 4 Versandkosten');
define('MODULE_SHIPPING_DPD_COST_4_DESC' , 'Versandkosten nach Zone 4 Bestimmungsorte, basierend auf einer Gruppe von max. Bestellgewichten. Beispiel: 3:8.50,7:10.50,... Gewicht von kleiner oder gleich 3 w&uuml;rde 8.50 für die Zone 1 Bestimmungsl&auml;nder kosten.');
define('MODULE_SHIPPING_DPD_HANDLING_4_TITLE' , 'Zone 4 Handling Geb&uuml;hr');
define('MODULE_SHIPPING_DPD_HANDLING_4_DESC' , 'Handling Geb&uuml;hr für diese Versandzone');

define('MODULE_SHIPPING_DPD_COUNTRIES_5_TITLE' , 'Zone 5 L&auml;nder');
define('MODULE_SHIPPING_DPD_COUNTRIES_5_DESC' , 'Durch Komma getrennte Liste von ISO L&auml;ndercodes (2 Zeichen), welche Teil von Zone 5 sind.');
define('MODULE_SHIPPING_DPD_COST_5_TITLE' , 'Zone 5 Versandkosten');
define('MODULE_SHIPPING_DPD_COST_5_DESC' , 'Versandkosten nach Zone 5 Bestimmungsorte, basierend auf einer Gruppe von max. Bestellgewichten. Beispiel: 3:8.50,7:10.50,... Gewicht von kleiner oder gleich 3 w&uuml;rde 8.50 für die Zone 1 Bestimmungsl&auml;nder kosten.');
define('MODULE_SHIPPING_DPD_HANDLING_5_TITLE' , 'Zone 5 Handling Geb&uuml;hr');
define('MODULE_SHIPPING_DPD_HANDLING_5_DESC' , 'Handling Geb&uuml;hr für diese Versandzone');

define('MODULE_SHIPPING_DPD_COUNTRIES_6_TITLE' , 'Zone 6 L&auml;nder');
define('MODULE_SHIPPING_DPD_COUNTRIES_6_DESC' , 'Durch Komma getrennte Liste von ISO L&auml;ndercodes (2 Zeichen), welche Teil von Zone 6 sind.');
define('MODULE_SHIPPING_DPD_COST_6_TITLE' , 'Zone 6 Versandkosten');
define('MODULE_SHIPPING_DPD_COST_6_DESC' , 'Versandkosten nach Zone 6 Bestimmungsorte, basierend auf einer Gruppe von max. Bestellgewichten. Beispiel: 3:8.50,7:10.50,... Gewicht von kleiner oder gleich 3 w&uuml;rde 8.50 für die Zone 1 Bestimmungsl&auml;nder kosten.');
define('MODULE_SHIPPING_DPD_HANDLING_6_TITLE' , 'Zone 6 Handling Geb&uuml;hr');
define('MODULE_SHIPPING_DPD_HANDLING_6_DESC' , 'Handling Geb&uuml;hr für diese Versandzone');

define('MODULE_SHIPPING_DPD_COUNTRIES_7_TITLE' , 'Zone 7 L&auml;nder');
define('MODULE_SHIPPING_DPD_COUNTRIES_7_DESC' , 'Durch Komma getrennte Liste von ISO L&auml;ndercodes (2 Zeichen), welche Teil von Zone 7 sind.');
define('MODULE_SHIPPING_DPD_COST_7_TITLE' , 'Zone 7 Versandkosten');
define('MODULE_SHIPPING_DPD_COST_7_DESC' , 'Versandkosten nach Zone 7 Bestimmungsorte, basierend auf einer Gruppe von max. Bestellgewichten. Beispiel: 3:8.50,7:10.50,... Gewicht von kleiner oder gleich 3 w&uuml;rde 8.50 für die Zone 1 Bestimmungsl&auml;nder kosten.');
define('MODULE_SHIPPING_DPD_HANDLING_7_TITLE' , 'Zone 7 Handling Geb&uuml;hr');
define('MODULE_SHIPPING_DPD_HANDLING_7_DESC' , 'Handling Geb&uuml;hr für diese Versandzone');

define('MODULE_SHIPPING_DPD_COUNTRIES_8_TITLE' , 'Zone 8 L&auml;nder');
define('MODULE_SHIPPING_DPD_COUNTRIES_8_DESC' , 'Durch Komma getrennte Liste von ISO L&auml;ndercodes (2 Zeichen), welche Teil von Zone 8 sind.');
define('MODULE_SHIPPING_DPD_COST_8_TITLE' , 'Zone 8 Versandkosten');
define('MODULE_SHIPPING_DPD_COST_8_DESC' , 'Versandkosten nach Zone 8 Bestimmungsorte, basierend auf einer Gruppe von max. Bestellgewichten. Beispiel: 3:8.50,7:10.50,... Gewicht von kleiner oder gleich 3 w&uuml;rde 8.50 für die Zone 1 Bestimmungsl&auml;nder kosten.');
define('MODULE_SHIPPING_DPD_HANDLING_8_TITLE' , 'Zone 8 Handling Geb&uuml;hr');
define('MODULE_SHIPPING_DPD_HANDLING_8_DESC' , 'Handling Geb&uuml;hr für diese Versandzone');

define('MODULE_SHIPPING_DPD_COUNTRIES_9_TITLE' , 'Zone 9 L&auml;nder');
define('MODULE_SHIPPING_DPD_COUNTRIES_9_DESC' , 'Durch Komma getrennte Liste von ISO L&auml;ndercodes (2 Zeichen), welche Teil von Zone 9 sind.');
define('MODULE_SHIPPING_DPD_COST_9_TITLE' , 'Zone 9 Versandkosten');
define('MODULE_SHIPPING_DPD_COST_9_DESC' , 'Versandkosten nach Zone 9 Bestimmungsorte, basierend auf einer Gruppe von max. Bestellgewichten. Beispiel: 3:8.50,7:10.50,... Gewicht von kleiner oder gleich 3 w&uuml;rde 8.50 für die Zone 1 Bestimmungsl&auml;nder kosten.');
define('MODULE_SHIPPING_DPD_HANDLING_9_TITLE' , 'Zone 9 Handling Geb&uuml;hr');
define('MODULE_SHIPPING_DPD_HANDLING_9_DESC' , 'Handling Geb&uuml;hr für diese Versandzone');

define('MODULE_SHIPPING_DPD_COUNTRIES_10_TITLE' , 'Zone 10 L&auml;nder');
define('MODULE_SHIPPING_DPD_COUNTRIES_10_DESC' , 'Durch Komma getrennte Liste von ISO L&auml;ndercodes (2 Zeichen), welche Teil von Zone 10 sind.');
define('MODULE_SHIPPING_DPD_COST_10_TITLE' , 'Zone 10 Versandkosten');
define('MODULE_SHIPPING_DPD_COST_10_DESC' , 'Versandkosten nach Zone 10 Bestimmungsorte, basierend auf einer Gruppe von max. Bestellgewichten. Beispiel: 3:8.50,7:10.50,... Gewicht von kleiner oder gleich 3 w&uuml;rde 8.50 für die Zone 1 Bestimmungsl&auml;nder kosten.');
define('MODULE_SHIPPING_DPD_HANDLING_10_TITLE' , 'Zone 10 Handling Geb&uuml;hr');
define('MODULE_SHIPPING_DPD_HANDLING_10_DESC' , 'Handling Geb&uuml;hr für diese Versandzone');
