<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_products_stock.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_products_stock($products_id) {
    $products_id = xtc_get_prid($products_id);
    $stock_values = xtc_db_fetch_array(xtDBquery("SELECT products_quantity FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . (int) $products_id . "';"));
    return $stock_values['products_quantity'];
}
