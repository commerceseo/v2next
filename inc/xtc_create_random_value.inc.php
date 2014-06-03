<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_create_random_value.inc.php 866 2014-03-17 12:07:35Z akausch $
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

require_once(DIR_FS_INC . 'xtc_rand.inc.php');

function xtc_create_random_value($length, $type = 'mixed') {
    if (($type != 'mixed') && ($type != 'chars') && ($type != 'digits'))
        return false;

    $rand_value = '';
    while (strlen($rand_value) < $length) {
        if ($type == 'digits') {
            $char = xtc_rand(0, 9);
        } else {
            $char = chr(xtc_rand(0, 255));
        }
        if ($type == 'mixed') {
            if (preg_match('/^[a-z0-9]$/i', $char))
                $rand_value .= $char;
        } elseif ($type == 'chars') {
            if (preg_match('/^[a-z]$/i', $char))
                $rand_value .= $char;
        } elseif ($type == 'digits') {
            if (preg_match('/^[0-9]$/', $char))
                $rand_value .= $char;
        }
    }

    return $rand_value;
}
