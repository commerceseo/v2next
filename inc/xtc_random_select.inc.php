<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_random_select.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_random_select($query) {
    $random_product = '';
    $random_query = xtc_db_query($query);
    $num_rows = xtc_db_num_rows($random_query);
    if ($num_rows > 0) {
        $random_row = xtc_rand(0, ($num_rows - 1));
        xtc_db_data_seek($random_query, $random_row);
        $random_product = xtc_db_fetch_array($random_query);
    }

    return $random_product;
}
