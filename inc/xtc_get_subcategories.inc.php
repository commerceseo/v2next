<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_subcategories.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_subcategories(&$subcategories_array, $parent_id = 0) {
    $subcategories_query = xtDBquery("SELECT categories_id FROM " . TABLE_CATEGORIES . " WHERE parent_id = '" . (int) $parent_id . "';");
    while ($subcategories = xtc_db_fetch_array($subcategories_query)) {
        $subcategories_array[sizeof($subcategories_array)] = $subcategories['categories_id'];
        if ($subcategories['categories_id'] != $parent_id) {
            xtc_get_subcategories($subcategories_array, $subcategories['categories_id']);
        }
    }
}
