<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_db_test_connection.inc.php 420 2013-06-19 18:04:39Z akausch $
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

function xtc_db_test_connection($database) {
    global $db_error;

    $db_error = false;

    if (!$db_error) {
        if (!@xtc_db_select_db($database)) {
            $db_error = mysql_error();
        } else {
            if (!@xtc_db_query_installer('select count(*) from configuration')) {
                $db_error = mysql_error();
            }
        }
    }

    if ($db_error) {
        return false;
    } else {
        return true;
    }
}
