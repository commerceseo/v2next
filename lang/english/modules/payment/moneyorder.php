<?php
/*-----------------------------------------------------------------
* 	$Id: moneyorder.php 420 2013-06-19 18:04:39Z akausch $
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




  define('MODULE_PAYMENT_MONEYORDER_TEXT_TITLE', 'Check/Money Order');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION', 'Make payable to:&nbsp;' . MODULE_PAYMENT_MONEYORDER_PAYTO . '<br />Send to:<br /><br />' . nl2br(STORE_NAME_ADDRESS) . '<br /><br />' . 'Your order will not ship until we receive payment!');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER', "Make payable to: ". MODULE_PAYMENT_MONEYORDER_PAYTO . "\n\nSend to:\n" . STORE_NAME_ADDRESS . "\n\n" . 'Your order will not ship until we receive payment');
define('MODULE_PAYMENT_MONEYORDER_TEXT_INFO','');
  define('MODULE_PAYMENT_MONEYORDER_STATUS_TITLE' , 'Enable Check/Money Order Module');
define('MODULE_PAYMENT_MONEYORDER_STATUS_DESC' , 'Do you want to accept Check/Money Order payments?');
define('MODULE_PAYMENT_MONEYORDER_ALLOWED_TITLE' , 'Allowed zones');
define('MODULE_PAYMENT_MONEYORDER_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
define('MODULE_PAYMENT_MONEYORDER_PAYTO_TITLE' , 'Make Payable to:');
define('MODULE_PAYMENT_MONEYORDER_PAYTO_DESC' , 'Who should payments be made payable to?');
define('MODULE_PAYMENT_MONEYORDER_SORT_ORDER_TITLE' , 'Sort order of display.');
define('MODULE_PAYMENT_MONEYORDER_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_MONEYORDER_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_MONEYORDER_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
?>
