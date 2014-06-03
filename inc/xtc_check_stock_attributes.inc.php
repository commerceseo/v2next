<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_check_stock_attributes.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_check_stock_attributes($attribute_id, $products_quantity) {
    $stock_data = xtc_db_fetch_array(xtDBquery("SELECT attributes_stock FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_attributes_id='" . (int) $attribute_id . "';"));
    $stock_left = $stock_data['attributes_stock'] - $products_quantity;
    $out_of_stock = '';
    if ($stock_left < 0) {
        $out_of_stock = '<span class="markProductOutOfStock"> ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
    }
    return $out_of_stock;
}
