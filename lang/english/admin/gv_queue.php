<?php
/*-----------------------------------------------------------------
* 	$Id: gv_queue.php 420 2013-06-19 18:04:39Z akausch $
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





define('HEADING_TITLE', 'Gift Voucher Release Queue');

define('TABLE_HEADING_CUSTOMERS', 'Customers');
define('TABLE_HEADING_ORDERS_ID', 'Order-No.');
define('TABLE_HEADING_VOUCHER_VALUE', 'Voucher Value');
define('TABLE_HEADING_DATE_PURCHASED', 'Date Purchased');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_REDEEM_COUPON_MESSAGE_HEADER', 'You recently purchased a Gift Voucher from our online store.' . "\n"
                                          . 'For security reasons this was not made immediately available to you.' . "\n"
                                          . 'However this amount has now been released. You can now visit our store' . "\n"
                                          . 'and send the value via eMail to someone else' . "\n\n");

define('TEXT_REDEEM_COUPON_MESSAGE_AMOUNT', 'The Gift Voucher(s) you purchased are worth %s' . "\n\n");

define('TEXT_REDEEM_COUPON_MESSAGE_BODY', '');
define('TEXT_REDEEM_COUPON_MESSAGE_FOOTER', '');
define('TEXT_REDEEM_COUPON_SUBJECT', 'Gift Voucher Purchase');
?>
