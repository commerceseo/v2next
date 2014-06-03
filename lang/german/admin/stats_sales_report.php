<?php
/*-----------------------------------------------------------------
* 	$Id: stats_sales_report.php 420 2013-06-19 18:04:39Z akausch $
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



 

define('REPORT_DATE_FORMAT', 'd. m. Y');

define('HEADING_TITLE', 'Verkaufs Report');

define('REPORT_TYPE_YEARLY', 'J&auml;hrlich');
define('REPORT_TYPE_MONTHLY', 'Monatlich');
define('REPORT_TYPE_WEEKLY', 'W&ouml;chentlich');
define('REPORT_TYPE_DAILY', 'T&auml;glich');
define('REPORT_START_DATE', 'von Datum');
define('REPORT_END_DATE', 'bis Datum (inklusive)');
define('REPORT_DETAIL', 'Detail');
define('REPORT_MAX', 'zeige Beste');
define('REPORT_ALL', 'Alle');
define('REPORT_SORT', 'Sortierung');
define('REPORT_EXP', 'Export');
define('REPORT_SEND', 'Senden');
define('EXP_NORMAL', 'Normal');
define('EXP_HTML', 'HTML only');
define('EXP_CSV', 'CSV');

define('TABLE_HEADING_DATE', 'Datum');
define('TABLE_HEADING_ORDERS', '# Bestellungen');
define('TABLE_HEADING_ITEMS', '# Artikel');
define('TABLE_HEADING_REVENUE', 'Umsatz');
define('TABLE_HEADING_SHIPPING', 'Versand');

define('DET_HEAD_ONLY', 'keine Details');
define('DET_DETAIL', 'Details anzeigen');
define('DET_DETAIL_ONLY', 'Details mit Betrag');

define('SORT_VAL0', 'Standard');
define('SORT_VAL1', 'Beschreibung');
define('SORT_VAL2', 'Beschreibung ab');
define('SORT_VAL3', '# Artikel');
define('SORT_VAL4', '# Artikel ab');
define('SORT_VAL5', 'Umsatz');
define('SORT_VAL6', 'Umsatz ab');

define('REPORT_STATUS_FILTER', 'Status');
define('REPORT_PAYMENT_FILTER','Zahlungsweise');

define('SR_SEPARATOR1', ';');
define('SR_SEPARATOR2', ';');
define('SR_NEWLINE', '<br>');
?>