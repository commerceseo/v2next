<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_parse_category_path.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// include needed function
require_once(DIR_FS_INC . 'xtc_string_to_int.inc.php');

// Parse and secure the cPath parameter values
function xtc_parse_category_path($cPath) {
    // make sure the category IDs are integers
    $cPath_array = array_map('xtc_string_to_int', explode('_', $cPath));

    // make sure no duplicate category IDs exist which could lock the server in a loop
    return array_unique($cPath_array);
}
