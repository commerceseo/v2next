<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_product_path.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Construct a category path to the product
// TABLES: products_to_categories
function xtc_get_product_path($products_id) {
    $cPath = '';
    $category_query = xtDBquery("SELECT 
							p2c.categories_id 
						FROM 
							" . TABLE_PRODUCTS . " p
						INNER JOIN
							" . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON(p.products_id = p2c.products_id ) 
						WHERE 
							p.products_id = '" . (int) $products_id . "' 
						AND 
							p.products_status = '1'
						AND 
							p2c.categories_id != 0 
						LIMIT 1;");
    if (xtc_db_num_rows($category_query, true)) {
        $category = xtc_db_fetch_array($category_query);
        $categories = array();
        xtc_get_parent_categories($categories, $category['categories_id']);
        $categories = array_reverse($categories);
        $cPath = implode('_', $categories);
        if (xtc_not_null($cPath)) {
            $cPath .= '_';
        }
        $cPath .= $category['categories_id'];
    }
    return $cPath;
}
