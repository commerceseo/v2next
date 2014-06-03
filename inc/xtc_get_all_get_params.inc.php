<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_all_get_params.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_all_get_params($exclude_array = '') {
    global $InputFilter;

    if (!is_array($exclude_array))
        $exclude_array = array();
    $get_url = '';
    if (is_array($_GET) && (sizeof($_GET) > 0)) {
        reset($_GET);
        while (list($key, $value) = each($_GET)) {
            if ((strlen($value) > 0) && ($key != xtc_session_name()) && ($key != 'error') && (!in_array($key, $exclude_array)) && ($key != 'x') && ($key != 'y')) {
                $get_url .= rawurlencode(stripslashes($key)) . '=' . rawurlencode(stripslashes($value)) . '&';
            }
        }
    }

    return $get_url;
}
