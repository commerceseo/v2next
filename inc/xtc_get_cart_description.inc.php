<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_cart_description.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_cart_description($product_id, $language = '') {
    if (empty($language)) {
        $language = (int) $_SESSION['languages_id'];
    }
    $product = xtc_db_fetch_array(xtDBquery("SELECT products_cart_description FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_id = '" . (int) $product_id . "' AND language_id = '" . (int) $language . "';"));
    return $product['products_cart_description'];
}
