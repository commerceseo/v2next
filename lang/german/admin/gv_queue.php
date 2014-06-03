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





define('HEADING_TITLE', 'Gutschein Freigabe Warteschlange');

define('TABLE_HEADING_CUSTOMERS', 'Kunden');
define('TABLE_HEADING_ORDERS_ID', 'Bestell-Nr.');
define('TABLE_HEADING_VOUCHER_VALUE', 'Gutscheinwert');
define('TABLE_HEADING_DATE_PURCHASED', 'Bestelldatum');
define('TABLE_HEADING_ACTION', 'Aktion');

define('TEXT_REDEEM_COUPON_MESSAGE_HEADER', 'Sie haben k&uuml;rzlich in unserem Online-Shop einen Gutschein bestellt, ' . "\n"
                                          . 'welcher aus Sicherheitsgr&uuml;nden nicht sofort freigeschaltet wurde.' . "\n"
                                          . 'Dieses Guthaben steht Ihnen nun zur Verf&uuml;gung. Sie k&ouml;nnen nun auch unseren Online Shop besuchen' . "\n"
                                          . 'und einen Teilbetrag Ihres Gutschens per eMail an jemanden versenden' . "\n\n");

define('TEXT_REDEEM_COUPON_MESSAGE_AMOUNT', 'Der von Ihnen bestellte Gutschein hat einen Wert von %s' . "\n\n");

define('TEXT_REDEEM_COUPON_MESSAGE_BODY', '');
define('TEXT_REDEEM_COUPON_MESSAGE_FOOTER', '');
define('TEXT_REDEEM_COUPON_SUBJECT', 'Gutschein kaufen');?>