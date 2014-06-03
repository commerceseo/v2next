<?php

/* -----------------------------------------------------------------
 * 	ID:						xtc_db_query.inc.php
 * 	Letzter Stand:			v2.3
 * 	zuletzt geaendert von:	cseoak
 * 	Datum:					2012/11/19
 *
 * 	Copyright (c) since 2010 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

// include needed functions
include_once(DIR_FS_INC . 'xtc_db_error.inc.php');

function xtc_db_query($query, $link = 'db_link') {
    global $$link;

    // mysql_query("SET names 'utf8'");
    // mysql_query("SET CHARACTER SET 'utf8'");

    if (STORE_DB_TRANSACTIONS == 'true') {
        error_log('QUERY ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    $result = @mysql_query($query, $$link) or sqlerrorhandler("(" . mysql_errno() . ") " . mysql_error(), $query, ($_REQUEST['linkurl'] != '' ? $_REQUEST['linkurl'] : $_SERVER['PHP_SELF']), __LINE__);

    if (STORE_DB_TRANSACTIONS == 'true') {
        $result_error = mysql_error();
        error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }
    return $result;
}
