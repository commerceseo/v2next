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



  

define('REPORT_DATE_FORMAT', 'm. d. Y');

define('HEADING_TITLE', 'Sales Report');

define('REPORT_TYPE_YEARLY', 'Yearly');
define('REPORT_TYPE_MONTHLY', 'Monthly');
define('REPORT_TYPE_WEEKLY', 'Weekly');
define('REPORT_TYPE_DAILY', 'Daily');
define('REPORT_START_DATE', 'from date');
define('REPORT_END_DATE', 'to date (inclusive)');
define('REPORT_DETAIL', 'detail');
define('REPORT_MAX', 'show top');
define('REPORT_ALL', 'all');
define('REPORT_SORT', 'sort');
define('REPORT_EXP', 'export');
define('REPORT_SEND', 'send');
define('EXP_NORMAL', 'normal');
define('EXP_HTML', 'HTML only');
define('EXP_CSV', 'CSV');

define('TABLE_HEADING_DATE', 'Date');
define('TABLE_HEADING_ORDERS', '# Orders');
define('TABLE_HEADING_ITEMS', '# Products');
define('TABLE_HEADING_REVENUE', 'Revenue');
define('TABLE_HEADING_SHIPPING', 'Shipping');

define('DET_HEAD_ONLY', 'no details');
define('DET_DETAIL', 'show details');
define('DET_DETAIL_ONLY', 'details with amount');

define('SORT_VAL0', 'standard');
define('SORT_VAL1', 'description');
define('SORT_VAL2', 'description desc');
define('SORT_VAL3', '# Products');
define('SORT_VAL4', '#Items desc');
define('SORT_VAL5', 'Revenue');
define('SORT_VAL6', 'Revenue desc');

define('REPORT_STATUS_FILTER', 'Status');
define('REPORT_PAYMENT_FILTER','Paymenttype');

define('SR_SEPARATOR1', ';');
define('SR_SEPARATOR2', ';');
define('SR_NEWLINE', '<br>');
?>
