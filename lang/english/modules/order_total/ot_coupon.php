<?php
/*-----------------------------------------------------------------
* 	$Id: ot_coupon.php 420 2013-06-19 18:04:39Z akausch $
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




  define('MODULE_ORDER_TOTAL_COUPON_TITLE', 'Discount Coupons');
  define('MODULE_ORDER_TOTAL_COUPON_HEADER', 'Gift Vouchers/Discount Coupons');
  define('MODULE_ORDER_TOTAL_COUPON_DESCRIPTION', 'Discount Coupon');
  define('SHIPPING_NOT_INCLUDED', ' [Shipping not included]');
  define('TAX_NOT_INCLUDED', ' [Tax not included]');
  define('MODULE_ORDER_TOTAL_COUPON_USER_PROMPT', '');
  define('ERROR_NO_INVALID_REDEEM_COUPON', 'Invalid Coupon Code');
  define('ERROR_INVALID_STARTDATE_COUPON', 'This coupon is not available yet');
  define('ERROR_INVALID_FINISDATE_COUPON', 'This coupon has expired');
  define('ERROR_INVALID_USES_COUPON', 'This coupon could only be used ');  
  define('TIMES', ' times.');
  define('ERROR_INVALID_USES_USER_COUPON', 'You have used the coupon the maximum number of times allowed per customer.'); 
  define('REDEEMED_COUPON', 'a coupon worth ');  
  define('REDEEMED_MIN_ORDER', 'on orders over ');  
  define('REDEEMED_RESTRICTIONS', ' [Product-Category restrictions apply]');  
  define('TEXT_ENTER_COUPON_CODE', 'Enter Redeem Code&nbsp;&nbsp;');
  
  define('MODULE_ORDER_TOTAL_COUPON_STATUS_TITLE', 'Display Total');
  define('MODULE_ORDER_TOTAL_COUPON_STATUS_DESC', 'Do you want to display the Discount Coupon value?');
  define('MODULE_ORDER_TOTAL_COUPON_SORT_ORDER_TITLE', 'Sort Order');
  define('MODULE_ORDER_TOTAL_COUPON_SORT_ORDER_DESC', 'Display Order.');
  define('MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING_TITLE', 'Include Shipping');
  define('MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING_DESC', 'Include Shipping in calculation');
  define('MODULE_ORDER_TOTAL_COUPON_INC_TAX_TITLE', 'Include Tax');
  define('MODULE_ORDER_TOTAL_COUPON_INC_TAX_DESC', 'Include Tax in calculation.');
  define('MODULE_ORDER_TOTAL_COUPON_CALC_TAX_TITLE', 'Re-calculate Tax');
  define('MODULE_ORDER_TOTAL_COUPON_CALC_TAX_DESC', 'Re-Calculate Tax');
  define('MODULE_ORDER_TOTAL_COUPON_TAX_CLASS_TITLE', 'Tax Class');
  define('MODULE_ORDER_TOTAL_COUPON_TAX_CLASS_DESC', 'Use the following tax class when treating Discount Coupon as Credit Note.');
?>