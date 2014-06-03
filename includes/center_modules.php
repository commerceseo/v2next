<?php
/*-----------------------------------------------------------------
* 	$Id: center_modules.php 722 2013-11-07 10:35:39Z akausch $
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

require(DIR_WS_MODULES . FILENAME_NEW_PRODUCTS);

if(CATEGORY_LISTING_START == 'true' && file_exists(DIR_WS_MODULES . FILENAME_MAIN_CATEGORIES_LIST)) {
	require(DIR_WS_MODULES . FILENAME_MAIN_CATEGORIES_LIST);
}

if (UPCOMING_PRODUCTS_START == 'true' && file_exists(DIR_WS_MODULES . FILENAME_MAIN_UPCOMING_PRODUCTS)) {
	require(DIR_WS_MODULES . FILENAME_MAIN_UPCOMING_PRODUCTS);
}

if (RANDOM_PRODUCTS_START == 'true' && file_exists(DIR_WS_MODULES . FILENAME_MAIN_RANDOM_PRODUCTS)) {
	require(DIR_WS_MODULES . FILENAME_MAIN_RANDOM_PRODUCTS);
}

if (RANDOM_SPECIALS_START == 'true' && file_exists(DIR_WS_MODULES . FILENAME_MAIN_RANDOM_SPECIALS)) {
	require(DIR_WS_MODULES . FILENAME_MAIN_RANDOM_SPECIALS);
}

if (BLOG_START == 'true' && file_exists(DIR_WS_MODULES . FILENAME_MAIN_BLOG)) {
	require(DIR_WS_MODULES . FILENAME_MAIN_BLOG);
}

if (MODULE_PRODUCT_PROMOTION_STATUS == 'true' && file_exists(DIR_WS_MODULES . FILENAME_MAIN_PRODUCTS_PROMOTION)) {
	require(DIR_WS_MODULES . FILENAME_MAIN_PRODUCTS_PROMOTION);
}
if (BESTSELLER_START == 'true' && file_exists(DIR_WS_MODULES . FILENAME_MAIN_BESTSELLER)) {
	require(DIR_WS_MODULES . FILENAME_MAIN_BESTSELLER);
}
// if (BESTSELLER_START == 'true' && file_exists(DIR_WS_MODULES . 'rss_reader.php')) {
// if (file_exists(DIR_WS_MODULES . 'rss_reader.php')) {
	// require(DIR_WS_MODULES . 'rss_reader.php');
// }

return $module;
