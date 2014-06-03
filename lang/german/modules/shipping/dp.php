<?php
/*-----------------------------------------------------------------
* 	$Id: dp.php 496 2013-07-17 09:56:08Z akausch $
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

define('MODULE_SHIPPING_DP_TEXT_TITLE', 'Deutsche Post');
define('MODULE_SHIPPING_DP_TEXT_DESCRIPTION', 'Deutsche Post - Weltweites Versandmodul');
define('MODULE_SHIPPING_DP_TEXT_WAY', 'Versand nach');
define('MODULE_SHIPPING_DP_TEXT_UNITS', 'kg');
define('MODULE_SHIPPING_DP_INVALID_ZONE', 'Es ist leider kein Versand in dieses Land möglich');
define('MODULE_SHIPPING_DP_UNDEFINED_RATE', 'Die Versandkosten können im Moment nicht errechnet werden');

define('MODULE_SHIPPING_DP_STATUS_TITLE' , 'Deutsche Post WorldNet');
define('MODULE_SHIPPING_DP_STATUS_DESC' , 'Wollen Sie den Versand über die deutsche Post anbieten?');
define('MODULE_SHIPPING_DP_HANDLING_TITLE' , 'Bearbeitungsgebühr');
define('MODULE_SHIPPING_DP_HANDLING_DESC' , 'Bearbeitungsgebühr für diese Versandart in Euro');
define('MODULE_SHIPPING_DP_TAX_CLASS_TITLE' , 'Steuersatz');
define('MODULE_SHIPPING_DP_TAX_CLASS_DESC' , 'Wählen Sie den MwSt.-Satz für diese Versandart aus.');
define('MODULE_SHIPPING_DP_ZONE_TITLE' , 'Versand Zone');
define('MODULE_SHIPPING_DP_ZONE_DESC' , 'Wenn Sie eine Zone auswählen, wird diese Versandart nur in dieser Zone angeboten.');
define('MODULE_SHIPPING_DP_SORT_ORDER_TITLE' , 'Reihenfolge der Anzeige');
define('MODULE_SHIPPING_DP_SORT_ORDER_DESC' , 'Niedrigste wird zuerst angezeigt.');
define('MODULE_SHIPPING_DP_ALLOWED_TITLE' , 'Einzelne Versandzonen');
define('MODULE_SHIPPING_DP_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, in welche ein Versand möglich sein soll. zb AT,DE');
define('MODULE_SHIPPING_DP_COUNTRIES_1_TITLE' , 'DP Zone 1 Countries');
define('MODULE_SHIPPING_DP_COUNTRIES_1_DESC' , 'Komma getrennte Liste der ISO-Länder_Codes (2 Zeichen) für Zone 1');
define('MODULE_SHIPPING_DP_COST_1_TITLE' , 'DP Zone 1 Versand Tabelle');
define('MODULE_SHIPPING_DP_COST_1_DESC' , 'Versandgebühr für Zone 1 Ziele, basierend auf einem Bereich von Gewichten. Beispiel: 0-3:8.50,3-7:10.50,... Gewichte größer als 0 und kleiner als oder gleich 3 kosten würde 14.57 für Zone 1 Ziele.');
define('MODULE_SHIPPING_DP_COUNTRIES_2_TITLE' , 'DP Zone 2 Countries');
define('MODULE_SHIPPING_DP_COUNTRIES_2_DESC' , 'Komma getrennte Liste der ISO-Länder_Codes (2 Zeichen) für Zone 2');
define('MODULE_SHIPPING_DP_COST_2_TITLE' , 'DP Zone 2 Versand Tabelle');
define('MODULE_SHIPPING_DP_COST_2_DESC' , 'Versandgebühr für Zone 2 Ziele, basierend auf einem Bereich von Gewichten. Beispiel: 0-3:8.50,3-7:10.50,... Gewichte größer als 0 und kleiner als oder gleich 3 kosten würde 23.78 für Zone 2 Ziele.');
define('MODULE_SHIPPING_DP_COUNTRIES_3_TITLE' , 'DP Zone 3 Countries');
define('MODULE_SHIPPING_DP_COUNTRIES_3_DESC' , 'Komma getrennte Liste der ISO-Länder_Codes (2 Zeichen) für Zone 3');
define('MODULE_SHIPPING_DP_COST_3_TITLE' , 'DP Zone 3 Versand Tabelle');
define('MODULE_SHIPPING_DP_COST_3_DESC' , 'Versandgebühr für Zone 3 Ziele, basierend auf einem Bereich von Gewichten. Beispiel: 0-3:8.50,3-7:10.50,... Gewichte größer als 0 und kleiner als oder gleich 3 kosten würde 26.84 für Zone 3 Ziele.');
define('MODULE_SHIPPING_DP_COUNTRIES_4_TITLE' , 'DP Zone 4 Countries');
define('MODULE_SHIPPING_DP_COUNTRIES_4_DESC' , 'Komma getrennte Liste der ISO-Länder_Codes (2 Zeichen) für Zone 4');
define('MODULE_SHIPPING_DP_COST_4_TITLE' , 'DP Zone 4 Versand Tabelle');
define('MODULE_SHIPPING_DP_COST_4_DESC' , 'Versandgebühr für Zone 4 Ziele, basierend auf einem Bereich von Gewichten. Beispiel: 0-3:8.50,3-7:10.50,... Gewichte größer als 0 und kleiner als oder gleich 3 kosten würde 32.98 für Zone 4 Ziele.');
define('MODULE_SHIPPING_DP_COUNTRIES_5_TITLE' , 'DP Zone 5 Countries');
define('MODULE_SHIPPING_DP_COUNTRIES_5_DESC' , 'Komma getrennte Liste der ISO-Länder_Codes (2 Zeichen) für Zone 5');
define('MODULE_SHIPPING_DP_COST_5_TITLE' , 'DP Zone 5 Versand Tabelle');
define('MODULE_SHIPPING_DP_COST_5_DESC' , 'Versandgebühr für Zone 5 Ziele, basierend auf einem Bereich von Gewichten. Beispiel: 0-3:8.50,3-7:10.50,... Gewichte größer als 0 und kleiner als oder gleich 3 kosten würde 32.98 für Zone 5 Ziele.');
define('MODULE_SHIPPING_DP_COUNTRIES_6_TITLE' , 'DP Zone 6 Countries');
define('MODULE_SHIPPING_DP_COUNTRIES_6_DESC' , 'Komma getrennte Liste der ISO-Länder_Codes (2 Zeichen) für Zone 6');
define('MODULE_SHIPPING_DP_COST_6_TITLE' , 'DP Zone 6 Versand Tabelle');
define('MODULE_SHIPPING_DP_COST_6_DESC' , 'Versandgebühr für Zone 6 Ziele, basierend auf einem Bereich von Gewichten. Beispiel: 0-3:8.50,3-7:10.50,... Gewichte größer als 0 und kleiner als oder gleich 3 kosten würde 5.62 für Zone 6 Ziele.');
