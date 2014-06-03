<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_random_charcode.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// build to generate a random charcode
function xtc_random_charcode($length) {
    $arraysize = 28;
    $chars = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'K', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '2', '3', '4', '5', '6', '8', '9');

    $code = '';
    for ($i = 1; $i <= $length; $i++) {
        $j = floor(xtc_rand(0, $arraysize));
        $code .= $chars[$j];
    }
    return $code;
}
