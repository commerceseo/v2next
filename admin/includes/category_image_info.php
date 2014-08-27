<?php

/* -----------------------------------------------------------------
 * 	$Id: category_image_info.php 420 2013-06-19 18:04:39Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

$a = new image_manipulation(DIR_FS_CATALOG_IMAGES . 'categories_org/' . $categories_image_name, CATEGORY_INFO_IMAGE_WIDTH, CATEGORY_INFO_IMAGE_HEIGHT, DIR_FS_CATALOG_IMAGES . 'categories_info/' . $categories_image_name, IMAGE_QUALITY, '');

$string = str_replace("'", '', CATEGORY_INFO_IMAGE_MERGE);
$string = str_replace(')', '', $string);
$string = str_replace('(', DIR_FS_CATALOG_IMAGES, $string);
$array = explode(',', $string);

if (CATEGORY_INFO_IMAGE_MERGE != '') {
    $a->merge($array[0], $array[1], $array[2], $array[3], $array[4]);
}

$a->create();
