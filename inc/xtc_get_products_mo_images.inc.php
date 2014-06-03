<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_products_mo_images.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_products_mo_images($pID = '') {
    $products_mo_images_query = xtDBquery("SELECT image_id, image_nr, image_name, alt_langID_" . (int) $_SESSION['languages_id'] . " FROM " . TABLE_PRODUCTS_IMAGES . " WHERE products_id = '" . (int) $pID . "' ORDER BY image_nr;");
    while ($row = xtc_db_fetch_array($products_mo_images_query)) {
        $results[($row['image_nr'] - 1)] = $row;
    }
    if (is_array($results)) {
        return $results;
    } else {
        return false;
    }
}
