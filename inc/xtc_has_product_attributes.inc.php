<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_has_product_attributes.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Check if product has attributes
function xtc_has_product_attributes($products_id) {
    $attributes = xtc_db_fetch_array(xtDBquery("SELECT COUNT(*) AS count FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id = '" . (int) $products_id . "';"));
    if ($attributes['count'] > 0) {
        return true;
    } else {
        return false;
    }
}
