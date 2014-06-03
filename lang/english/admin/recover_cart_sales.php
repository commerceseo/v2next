<?php
/*
  $Id: recover_cart_sales.php 420 2013-06-19 18:04:39Z akausch $
  Recover Cart Sales v2.12 for xt:Commerce GERMAN Language File

  Copyright (c) 2006 Andre Estel www.estelco.de

  Recover Cart Sales contribution: JM Ivler (c)
  Copyright (c) 2003-2005 JM Ivler / Ideas From the Deep / OSCommerce
  http://www.oscommerce.com

  Released under the GNU General Public License

  Modifed by Aalst (recover_cart_sales.php,v 1.2 .. 1.36)
  aalst@aalst.com

  Modifed by willross (recover_cart_sales.php,v 1.4)
  reply@qwest.net
  - don't forget to flush the 'scart' db table every so often

  Modifed by Lane (stats_recover_cart_sales.php,v 1.4d .. 2.11)
  lane@ifd.com www.osc-modsquad.com / www.ifd.com
*/

define('MESSAGE_STACK_CUSTOMER_ID', 'Open Baskets for Customer Number ');
define('MESSAGE_STACK_DELETE_SUCCESS', ' successfully deleted');
define('HEADING_TITLE', 'Open Baskets PLUS');
define('HEADING_EMAIL_SENT', 'e-mail Send-Report');
define('EMAIL_TEXT_SUBJECT', 'Question by '.  STORE_NAME );
define('DAYS_FIELD_PREFIX', 'Show the last ');
define('DAYS_FIELD_POSTFIX', ' Days ');
define('DAYS_FIELD_BUTTON', 'View');
define('TABLE_HEADING_DATE', 'Date');
define('TABLE_HEADING_CONTACT', 'contact?');
define('TABLE_HEADING_CUSTOMER', 'Customer Name');
define('TABLE_HEADING_EMAIL', 'e-mail');
define('TABLE_HEADING_STOPPED', 'Where finished');
define('TABLE_HEADING_PHONE', 'Phone');
define('TABLE_HEADING_MODEL', 'Article');
define('TABLE_HEADING_DESCRIPTION', 'Description');
define('TABLE_HEADING_QUANTY', 'Quantity');
define('TABLE_HEADING_PRICE', 'Price');
define('TABLE_HEADING_TOTAL', 'Total');
define('TABLE_GRAND_TOTAL', 'Total net Total: ');
define('TABLE_CART_TOTAL', 'Total net: ');
define('TABLE_GRAND_TOTAL_BRUTTO', 'Total gross Total: ');
define('TABLE_CART_TOTAL_BRUTTO', 'Total gross: ');
define('TEXT_CURRENT_CUSTOMER', 'Customer');
define('TEXT_SEND_EMAIL', 'Send e-mail');
define('TEXT_RETURN', '[Click here to go back]');
define('TEXT_NOT_CONTACTED', 'Not contacted');
define('PSMSG', 'Additional message (PS) at the end of the mail: ');

define('TEXT_CART', 'Cart');
define('TEXT_SHIPPING', 'Shipping');
define('TEXT_PAYMENT', 'Payment');
define('TEXT_CONFIRM', 'Confirmation');
define('TABLE_HEADING_OUT_DATE', 'Not in the period');
?>
