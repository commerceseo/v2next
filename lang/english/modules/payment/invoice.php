<?php
/*-----------------------------------------------------------------
* 	$Id: invoice.php 420 2013-06-19 18:04:39Z akausch $
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




  define('MODULE_PAYMENT_INVOICE_TEXT_DESCRIPTION', 'Invoice');
  define('MODULE_PAYMENT_INVOICE_TEXT_TITLE', 'Invoice');
define('MODULE_PAYMENT_INVOICE_TEXT_INFO','');
  define('MODULE_PAYMENT_INVOICE_STATUS_TITLE' , 'Enable Invoices Module');
define('MODULE_PAYMENT_INVOICE_STATUS_DESC' , 'Do you want to accept Invoices as payments?');
define('MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
define('MODULE_PAYMENT_INVOICE_SORT_ORDER_TITLE' , 'Sort order of display.');
define('MODULE_PAYMENT_INVOICE_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_INVOICE_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_INVOICE_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_INVOICE_ALLOWED_TITLE' , 'Allowed zones');
define('MODULE_PAYMENT_INVOICE_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
define('MODULE_PAYMENT_INVOICE_MIN_ORDER_TITLE' , 'Minimum Orders');
define('MODULE_PAYMENT_INVOICE_MIN_ORDER_DESC' , 'Minimum orders for a Customer to view this Option.');
define('MODULE_PAYMENT_INVOICE_MIN_AMOUNT_TITLE' , 'Minimum Amount');
define('MODULE_PAYMENT_INVOICE_MIN_AMOUNT_DESC' , 'Minimum Amount for a Customer to view this Option.');
define('MODULE_PAYMENT_INVOICE_MAX_AMOUNT_TITLE' , 'Maximum Amount');
define('MODULE_PAYMENT_INVOICE_MAX_AMOUNT_DESC' , 'Maximum Amount for a Customer to view this Option.');
?>
