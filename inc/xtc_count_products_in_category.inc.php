<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_count_products_in_category.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_count_products_in_category($category_id, $include_inactive = false) {
    $products_count = 0;
    if ($include_inactive == true) {
        $products_query = "SELECT count(*) AS total FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c WHERE p.products_id = p2c.products_id AND p2c.categories_id = '" . (int) $category_id . "';";
    } else {
        $products_query = "SELECT count(*) AS total FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c WHERE p.products_id = p2c.products_id AND p.products_status = '1' AND p2c.categories_id = '" . (int) $category_id . "';";
    }

    $products_query = xtDBquery($products_query);

    $products = xtc_db_fetch_array($products_query, true);
    $products_count += $products['total'];

    $child_categories_query = xtDBquery("SELECT categories_id FROM " . TABLE_CATEGORIES . " WHERE parent_id = '" . (int) $category_id . "';");
    if (xtc_db_num_rows($child_categories_query)) {
        while ($child_categories = xtc_db_fetch_array($child_categories_query)) {
            $products_count += xtc_count_products_in_category($child_categories['categories_id'], $include_inactive);
        }
    }

    return $products_count;
}
