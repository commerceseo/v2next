<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_db_connect_installer.inc.php 987 2014-04-22 10:40:42Z akausch $
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

function xtc_db_connect_installer($server, $username, $password, $link = 'db_link') {
    global $$link, $db_error;

    $db_error = false;

    if (!$server) {
        $db_error = 'No Server selected.';
        return false;
    }

    $$link = @mysql_connect($server, $username, $password) or $db_error = mysql_error();

    if (function_exists('mysql_set_charset') == true) {
        mysql_set_charset('utf8');
    } else {
        mysql_query('set names utf8');
    }

    return $$link;
}
