<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_products_image.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_products_image($products_id = '') {
    $products_image = xtc_db_fetch_array(xtDBquery("SELECT products_image FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . (int) $products_id . "';"));
    return $products_image['products_image'];
}
