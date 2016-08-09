<?php
/*-----------------------------------------------------------------
* 	$Id: ot_cod_fee.php 1447 2015-02-17 18:16:53Z akausch $
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
define('MODULE_ORDER_TOTAL_COD_FEE_FLEX_TITLE','Flexible Versandkosten');
define('MODULE_ORDER_TOTAL_COD_FEE_FLEX_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br />
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_INTRASHIP_FLAT_TITLE','Intraship Versandkosten');
define('MODULE_ORDER_TOTAL_COD_FEE_TITLE', 'Nachnahmegeb&uuml;hr');
define('MODULE_ORDER_TOTAL_COD_FEE_DESCRIPTION', 'Berechnung der Nachnahmegeb&uuml;hr');

define('MODULE_ORDER_TOTAL_COD_FEE_STATUS_TITLE','Nachnahmegeb&uuml;hr');
define('MODULE_ORDER_TOTAL_COD_FEE_STATUS_DESC','Berechnung der Nachnahmegeb&uuml;hr');

define('MODULE_ORDER_TOTAL_COD_FEE_SORT_ORDER_TITLE','Sortierreihenfolge');
define('MODULE_ORDER_TOTAL_COD_FEE_SORT_ORDER_DESC','Anzeigereihenfolge');

define('MODULE_ORDER_TOTAL_COD_FEE_FLAT_TITLE','Pauschale Versandkosten');
define('MODULE_ORDER_TOTAL_COD_FEE_FLAT_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_FLAT_DE_TITLE','Pauschale Versandkosten DE');
define('MODULE_ORDER_TOTAL_COD_FEE_FLAT_DE_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_FLAT_AT_TITLE','Pauschale Versandkosten AT');
define('MODULE_ORDER_TOTAL_COD_FEE_FLAT_AT_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_FLAT_CH_TITLE','Pauschale Versandkosten CH');
define('MODULE_ORDER_TOTAL_COD_FEE_FLAT_CH_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_ITEM_TITLE','Versandkosten pro St&uuml;ck');
define('MODULE_ORDER_TOTAL_COD_FEE_ITEM_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_TABLE_TITLE','Tabellarische Versandkosten');
define('MODULE_ORDER_TOTAL_COD_FEE_TABLE_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_TABLE_DE_TITLE','Tabellarische Versandkosten DE');
define('MODULE_ORDER_TOTAL_COD_FEE_TABLE_DE_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_TABLE_AT_TITLE','Tabellarische Versandkosten AT');
define('MODULE_ORDER_TOTAL_COD_FEE_TABLE_AT_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_TABLE_CH_TITLE','Tabellarische Versandkosten CH');
define('MODULE_ORDER_TOTAL_COD_FEE_TABLE_CH_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_ZONES_TITLE','Versandkosten nach Zonen');
define('MODULE_ORDER_TOTAL_COD_FEE_ZONES_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_AP_TITLE','&Ouml;sterreichische Post AG');
define('MODULE_ORDER_TOTAL_COD_FEE_AP_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_CHP_TITLE','Schweizerische Post');
define('MODULE_ORDER_TOTAL_COD_FEE_CHP_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_CHRONOPOST_TITLE','Chronopost Zone Rates');
define('MODULE_ORDER_TOTAL_COD_FEE_CHRONOPOST_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_DHL_TITLE','DHL &Ouml;sterreich');
define('MODULE_ORDER_TOTAL_COD_FEE_DHL_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_DP_TITLE','Deutsche Post AG');
define('MODULE_ORDER_TOTAL_COD_FEE_DP_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_UPS_TITLE','UPS');
define('MODULE_ORDER_TOTAL_COD_FEE_UPS_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_COD_FEE_UPSE_TITLE','UPS Express');
define('MODULE_ORDER_TOTAL_COD_FEE_UPSE_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet
(nicht m&ouml;glich).');


define('MODULE_ORDER_TOTAL_COD_FEE_FREE_TITLE','Versandkostenfrei (Modul Versandkosten in Zusammenfassung)');
define('MODULE_ORDER_TOTAL_COD_FEE_FREE_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet
(nicht m&ouml;glich).');

define('MODULE_ORDER_TOTAL_FREEAMOUNT_FREE_TITLE','Versandkostenfrei (Modul Versankosten in Versandkosten)');
define('MODULE_ORDER_TOTAL_FREEAMOUNT_FREE_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet
(nicht m&ouml;glich).');  

define('MODULE_ORDER_TOTAL_COD_FEE_HERMES_TITLE','Hermes Nachnahme');
define('MODULE_ORDER_TOTAL_COD_FEE_HERMES_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet
(nicht m&ouml;glich).');  

define('MODULE_ORDER_TOTAL_COD_FEE_HERMESPROPS_TITLE','HermesPro Nachnahme');
define('MODULE_ORDER_TOTAL_COD_FEE_HERMESPROPS_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet
(nicht m&ouml;glich).');  

define('MODULE_ORDER_TOTAL_COD_FEE_DPD_TITLE','DPD Nachnahme');
define('MODULE_ORDER_TOTAL_COD_FEE_DPD_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn
00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn
kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet
(nicht m&ouml;glich).');  

define('MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS_TITLE','Steuerklasse');
define('MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS_DESC','W&auml;hlen Sie eine Steuerklasse.');
