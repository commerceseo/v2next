<?php

/* -----------------------------------------------------------------
 * 	$Id: create_coupon_code.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Create a Coupon Code. length may be between 1 and 16 Characters
// $salt needs some thought.

function create_coupon_code($salt = "secret", $length = SECURITY_CODE_LENGTH) {
    $ccid = md5(uniqid("", "salt"));
    $ccid .= md5(uniqid("", "salt"));
    $ccid .= md5(uniqid("", "salt"));
    $ccid .= md5(uniqid("", "salt"));
    srand((double) microtime() * 1000000); // seed the random number generator
    $random_start = @rand(0, (128 - $length));
    $good_result = 0;
    while ($good_result == 0) {
        $id1 = substr($ccid, $random_start, $length);
        $query = xtc_db_query("SELECT coupon_code FROM " . TABLE_COUPONS . " WHERE coupon_code = '" . $id1 . "';");
        if (xtc_db_num_rows($query) == 0) {
            $good_result = 1;
        }
    }
    return $id1;
}
