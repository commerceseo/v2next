<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_array_to_string.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_array_to_string($array, $exclude = '', $equals = '=', $separator = '&') {
    if (!is_array($exclude))
        $exclude = array();

    $get_string = '';
    if (sizeof($array) > 0) {
        while (list($key, $value) = each($array)) {
            if ((!in_array($key, $exclude)) && ($key != 'x') && ($key != 'y')) {
                $get_string .= $key . $equals . $value . $separator;
            }
        }
        $remove_chars = strlen($separator);
        $get_string = substr($get_string, 0, -$remove_chars);
    }

    return $get_string;
}
