<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_db_query_installer.inc.php 987 2014-04-22 10:40:42Z akausch $
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

function xtc_db_query_installer($query, $link = 'db_link') {
    global $$link;

    mysql_query("SET names 'utf8'");
    mysql_query("SET CHARACTER SET 'utf8'");

    return mysql_query($query, $$link);
}
