<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_in_array.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_in_array($value, $array) {
    if (!$array)
        $array = array();

    if (function_exists('in_array')) {
        if (is_array($value)) {
            for ($i = 0; $i < sizeof($value); $i++) {
                if (in_array($value[$i], $array))
                    return true;
            }
            return false;
        } else {
            return in_array($value, $array);
        }
    } else {
        reset($array);
        while (list(, $key_value) = each($array)) {
            if (is_array($value)) {
                for ($i = 0; $i < sizeof($value); $i++) {
                    if ($key_value == $value[$i])
                        return true;
                }
                return false;
            } else {
                if ($key_value == $value)
                    return true;
            }
        }
    }

    return false;
}
