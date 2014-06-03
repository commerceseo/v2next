<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_db_cache.inc.php 866 2014-03-17 12:07:35Z akausch $
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

//! Get data from the cache or the database.
//  get_db_cache checks the cache for cached SQL data in $filename
//  or retreives it from the database is the cache is not present.
//  $SQL      -  The SQL query to exectue if needed.
//  $filename -  The name of the cache file.
//  $var      -  The variable to be filled.
//  $refresh  -  Optional.  If true, do not read from the cache.
function get_db_cache($sql, &$var, $filename, $refresh = false) {
    $var = array();
    if (($refresh == true) || !read_cache($var, $filename)) {
        $res = xtc_db_query($sql);
        while ($rec = xtc_db_fetch_array($res)) {
            $var[] = $rec;
        }
        write_cache($var, $filename);
    }
}
