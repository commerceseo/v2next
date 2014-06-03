<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_check_categories_status.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_check_categories_status($categories_id) {
    if (!$categories_id) {
        return 0;
    }
    $categorie_data = xtc_db_fetch_array(xtDBquery("SELECT parent_id, categories_status FROM " . TABLE_CATEGORIES . " WHERE categories_id = '" . (int) $categories_id . "';"));
    if ($categorie_data['categories_status'] == 0) {
        return 1;
    } else {
        if ($categorie_data['parent_id'] != 0) {
            if (xtc_check_categories_status($categorie_data['parent_id']) >= 1)
                return 1;
        }
        return 0;
    }
}

function xtc_get_categoriesstatus_for_product($product_id) {
    $categorie_query = xtDBquery("SELECT categories_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id='" . (int) $product_id . "';");
    while ($categorie_data = xtc_db_fetch_array($categorie_query)) {
        if (xtc_check_categories_status($categorie_data['categories_id']) >= 1) {
            return 1;
        } else {
            return 0;
        }
        echo $categorie_data['categories_id'];
    }
}
