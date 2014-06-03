<?php
/*-----------------------------------------------------------------
* 	$Id: shipping_status.php 420 2013-06-19 18:04:39Z akausch $
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




define('HEADING_TITLE', 'Shipping Status');

define('TABLE_HEADING_SHIPPING_STATUS', 'Shipping Status');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_INFO_EDIT_INTRO', 'Please make any necessary changes');
define('TEXT_INFO_SHIPPING_STATUS_NAME', 'Shipping Status:');
define('TEXT_INFO_INSERT_INTRO', 'Please enter the new Shipping status with its related data');
define('TEXT_INFO_DELETE_INTRO', 'Are you sure you want to delete this Shipping status?');
define('TEXT_INFO_HEADING_NEW_SHIPPING_STATUS', 'New Shipping Status');
define('TEXT_INFO_HEADING_EDIT_SHIPPING_STATUS', 'Edit Shipping Status');
define('TEXT_INFO_SHIPPING_STATUS_IMAGE', 'Image:');
define('TEXT_INFO_HEADING_DELETE_SHIPPING_STATUS', 'Delete Shipping Status');

define('ERROR_REMOVE_DEFAULT_SHIPPING_STATUS', 'Error: The default Shipping status can not be removed. Please set another Shipping status as default, and try again.');
define('ERROR_STATUS_USED_IN_ORDERS', 'Error: This Shipping status is currently used in Products.');
define('ERROR_STATUS_USED_IN_HISTORY', 'Error: This Shipping status is currently used in Products.');
?>