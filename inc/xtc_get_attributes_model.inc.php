<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_attributes_model.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_attributes_model($product_id, $attribute_name, $options_name, $language = '') {
    if ($language == '') {
        $language = (int) $_SESSION['languages_id'];
    }
    $options_attr_data = xtc_db_fetch_array(xtc_db_query("SELECT
										pa.attributes_model
									FROM
										" . TABLE_PRODUCTS_ATTRIBUTES . " pa
									INNER JOIN 
										" . TABLE_PRODUCTS_OPTIONS . " po ON(po.products_options_id = pa.options_id)
									INNER JOIN 
										" . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON(pa.options_values_id = pov.products_options_values_id)
									WHERE
										po.language_id = '" . (int) $language . "' 
									AND
										pa.products_id = '" . (int) $product_id . "' 
									AND	
										po.products_options_name = '" . addslashes($options_name) . "' 
									AND
										pov.language_id = '" . (int) $language . "' 
									AND
										pov.products_options_values_name = '" . addslashes($attribute_name) . "' 
									AND 
										pa.products_id='" . (int) $product_id . "';"));
    return $options_attr_data['attributes_model'];
}
