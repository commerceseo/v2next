<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_category_path.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Construct a category path
function xtc_get_category_path($cID) {
    $cPath = '';

    $category = $cID;

    $categories = array();
    xtc_get_parent_categories($categories, $cID);

    $categories = array_reverse($categories);

    $cPath = implode('_', $categories);

    if (xtc_not_null($cPath))
        $cPath .= '_';
    $cPath .= $cID;

    return $cPath;
}
