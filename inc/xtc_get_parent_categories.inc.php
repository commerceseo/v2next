<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_parent_categories.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Recursively go through the categories and retreive all parent categories IDs
// TABLES: categories
function xtc_get_parent_categories(&$categories, $categories_id) {
    $parent_categories_query = xtDBquery("SELECT parent_id FROM " . TABLE_CATEGORIES . " WHERE categories_id = '" . (int) $categories_id . "';");
    while ($parent_categories = xtc_db_fetch_array($parent_categories_query)) {
        if ($parent_categories['parent_id'] == 0) {
            return true;
        }
        $categories[sizeof($categories)] = $parent_categories['parent_id'];
        if ($parent_categories['parent_id'] != $categories_id) {
            xtc_get_parent_categories($categories, $parent_categories['parent_id']);
        }
    }
}
