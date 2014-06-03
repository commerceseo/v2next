<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_oe_get_options_name.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_oe_get_options_name($products_options_id, $language = '') {
    if (empty($language)) {
        $language = (int) $_SESSION['languages_id'];
    }
    $product = xtc_db_fetch_array(xtDBquery("SELECT products_options_name FROM " . TABLE_PRODUCTS_OPTIONS . " WHERE products_options_id = '" . (int) $products_options_id . "' AND language_id = '" . (int) $language . "';"));
    return $product['products_options_name'];
}
