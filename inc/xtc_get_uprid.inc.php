<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_uprid.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Return a product ID with attributes

function xtc_get_uprid($prid, $params) {
    if (is_numeric($prid)) {
        $uprid = $prid;

        if (is_array($params) && (sizeof($params) > 0)) {
            $attributes_check = true;
            $attributes_ids = '';

            reset($params);
            while (list($option, $value) = each($params)) {
                if (is_numeric($option) && is_numeric($value)) {
                    $attributes_ids .= '{' . (int) $option . '}' . (int) $value;
                } else {
                    $attributes_check = false;
                    break;
                }
            }

            if ($attributes_check == true) {
                $uprid .= $attributes_ids;
            }
        }
    } else {
        $uprid = xtc_get_prid($prid);

        if (is_numeric($uprid)) {
            if (strpos($prid, '{') !== false) {
                $attributes_check = true;
                $attributes_ids = '';

                $attributes = explode('{', substr($prid, strpos($prid, '{') + 1));

                for ($i = 0, $n = sizeof($attributes); $i < $n; $i++) {
                    $pair = explode('}', $attributes[$i]);

                    if (is_numeric($pair[0]) && is_numeric($pair[1])) {
                        $attributes_ids .= '{' . (int) $pair[0] . '}' . (int) $pair[1];
                    } else {
                        $attributes_check = false;
                        break;
                    }
                }

                if ($attributes_check == true) {
                    $uprid .= $attributes_ids;
                }
            }
        } else {
            return false;
        }
    }

    return $uprid;
}
