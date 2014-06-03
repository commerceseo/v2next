<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_is_leap_year.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_is_leap_year($year) {
    if ($year % 100 == 0) {
        if ($year % 400 == 0)
            return true;
    } else {
        if (($year % 4) == 0)
            return true;
    }

    return false;
}
