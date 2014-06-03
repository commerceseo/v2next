<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_input_validation.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_input_validation($var, $type, $replace_char) {

    switch ($type) {
        case 'cPath':
            $replace_param = '/[^0-9_]/';
            break;
        case 'int':
            $replace_param = '/[^0-9]/';
            break;
        case 'char':
            $replace_param = '/[^a-zA-Z]/';
            break;
    }

    $val = preg_replace($replace_param, $replace_char, $var);

    return $val;
}
