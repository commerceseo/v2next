<?php
/*-----------------------------------------------------------------
* 	$Id: ap.php 496 2013-07-17 09:56:08Z akausch $
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

define('MODULE_SHIPPING_AP_TEXT_TITLE', 'Austrian Post AG');
define('MODULE_SHIPPING_AP_TEXT_DESCRIPTION', 'Austrian Post AG - Worldwide Dispatch');
define('MODULE_SHIPPING_AP_TEXT_WAY', 'Dispatch to');
define('MODULE_SHIPPING_AP_TEXT_UNITS', 'kg');
define('MODULE_SHIPPING_AP_INVALID_ZONE', 'Unfortunately it is not possible to dispatch into this country');
define('MODULE_SHIPPING_AP_UNDEFINED_RATE', 'Forwarding expenses cannot be calculated for the moment');

define('MODULE_SHIPPING_AP_STATUS_TITLE' , 'Oesterreichische Post AG');
define('MODULE_SHIPPING_AP_STATUS_DESC' , 'Wollen Sie den Versand for die Oesterreichische Post AG anbieten?');
define('MODULE_SHIPPING_AP_HANDLING_TITLE' , 'Handling Fee');
define('MODULE_SHIPPING_AP_HANDLING_DESC' , 'Bearbeitungsgebuehr for diese Versandart in Euro');
define('MODULE_SHIPPING_AP_TAX_CLASS_TITLE' , 'Steuersatz');
define('MODULE_SHIPPING_AP_TAX_CLASS_DESC' , 'WР Т‘hlen Sie den MwSt.-Satz for diese Versandart aus.');
define('MODULE_SHIPPING_AP_ZONE_TITLE' , 'Versand Zone');
define('MODULE_SHIPPING_AP_ZONE_DESC' , 'Wenn Sie eine Zone select, wird diese Versandart nur in dieser Zone angeboten.');
define('MODULE_SHIPPING_AP_SORT_ORDER_TITLE' , 'Reihenfolge der Anzeige');
define('MODULE_SHIPPING_AP_SORT_ORDER_DESC' , 'Niedrigste wird zuerst angezeigt.');
define('MODULE_SHIPPING_AP_ALLOWED_TITLE' , 'Einzelne Versandzonen');
define('MODULE_SHIPPING_AP_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, in welche ein Versand mРЎвЂ glich sein soll. zb AT,DE');
define('MODULE_SHIPPING_AP_COUNTRIES_1_TITLE' , 'Zone 1a Countries');
define('MODULE_SHIPPING_AP_COUNTRIES_1_DESC' , 'Durch Komma getrennt Liste der Countries als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 1a sind.');
define('MODULE_SHIPPING_AP_COST_1_TITLE' , 'Zone 1a Tarif Tabelle bis 20 kg');
define('MODULE_SHIPPING_AP_COST_1_DESC' , 'Tarif Tabelle for die Zone 1a, basiered auf <b>\'Schnelles Paket\'</b> bis 20 kg Versandgewicht.');
define('MODULE_SHIPPING_AP_COUNTRIES_2_TITLE' , 'Zone 1b Countries');
define('MODULE_SHIPPING_AP_COUNTRIES_2_DESC' , 'Durch Komma getrennt Liste der Countries als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 1b sind.');
define('MODULE_SHIPPING_AP_COST_2_TITLE' , 'Zone 1b Tarif Tabelle bis 20 kg');
define('MODULE_SHIPPING_AP_COST_2_DESC' , 'Tarif Tabelle for die Zone 1b, basiered auf <b>\'Schnelles Paket\'</b> bis 20 kg Versandgewicht.');
define('MODULE_SHIPPING_AP_COUNTRIES_3_TITLE' , 'Zone 2 Countries');
define('MODULE_SHIPPING_AP_COUNTRIES_3_DESC' , 'Durch Komma getrennt Liste der Countries als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 2 sind.');
define('MODULE_SHIPPING_AP_COST_3_TITLE' , 'Zone 2 Tarif Tabelle bis 20 kg');
define('MODULE_SHIPPING_AP_COST_3_DESC' , 'Tarif Tabelle for die Zone 2, basiered auf <b>\'Schnelles Paket\'</b> bis 20 kg Versandgewicht.');
define('MODULE_SHIPPING_AP_COUNTRIES_4_TITLE' , 'Zone 3 Countries');
define('MODULE_SHIPPING_AP_COUNTRIES_4_DESC' , 'Durch Komma getrennt Liste der Countries als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 3 sind.');
define('MODULE_SHIPPING_AP_COST_4_TITLE' , 'Zone 3 Tarif Tabelle bis 20 kg');
define('MODULE_SHIPPING_AP_COST_4_DESC' , 'Tarif Tabelle for die Zone 3, basiered auf <b>\'Schnelles Paket\'</b> bis 20 kg Versandgewicht.');
define('MODULE_SHIPPING_AP_COUNTRIES_5_TITLE' , 'Zone 4 Countries');
define('MODULE_SHIPPING_AP_COUNTRIES_5_DESC' , 'Durch Komma getrennt Liste der Countries als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 4 sind.');
define('MODULE_SHIPPING_AP_COST_5_TITLE' , 'Zone 4 Tarif Tabelle bis 20 kg');
define('MODULE_SHIPPING_AP_COST_5_DESC' , 'Tarif Tabelle for die Zone 4, basiered auf <b>\'Schnelles Paket\'</b> bis 20 kg Versandgewicht.');
define('MODULE_SHIPPING_AP_COUNTRIES_6_TITLE' , 'Zone 4 Countries');
define('MODULE_SHIPPING_AP_COUNTRIES_6_DESC' , 'Durch Komma getrennt Liste der Countries als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 4 sind.');
define('MODULE_SHIPPING_AP_COST_6_TITLE' , 'Zone 4 Tarif Tabelle bis 20 kg');
define('MODULE_SHIPPING_AP_COST_6_DESC' , 'Tarif Tabelle for die Zone 4, basiered auf <b>\'Schnelles Paket\'</b> bis 20 kg Versandgewicht.');
define('MODULE_SHIPPING_AP_COUNTRIES_7_TITLE' , 'Zone 5 Countries');
define('MODULE_SHIPPING_AP_COUNTRIES_7_DESC' , 'Durch Komma getrennt Liste der Countries als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 5 sind.');
define('MODULE_SHIPPING_AP_COST_7_TITLE' , 'Zone 5 Tarif Tabelle bis 20 kg');
define('MODULE_SHIPPING_AP_COST_7_DESC' , 'Tarif Tabelle for die Zone 5, basiered auf <b>\'Schnelles Paket\'</b> bis 20 kg Versandgewicht.');
define('MODULE_SHIPPING_AP_COUNTRIES_8_TITLE' , 'Zone Inland');
define('MODULE_SHIPPING_AP_COUNTRIES_8_DESC' , 'Inlandszone');
define('MODULE_SHIPPING_AP_COST_8_TITLE' , 'Zone Tarif Tabelle bis 31.5 kg');
define('MODULE_SHIPPING_AP_COST_8_DESC' , 'Tarif Tabelle for die Inlandszone, bis 31.5 kg Versandgewicht.');
