<?php

/* -----------------------------------------------------------------
 * 	ID:						xtc_db_connect.inc.php
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

function xtc_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link') {
    global $$link;

    if (USE_PCONNECT == 'true') {
        $$link = mysql_pconnect($server, $username, $password);
    } else {
        $$link = mysql_connect($server, $username, $password);
    }

    if ($$link)
        mysql_select_db($database) or die('Datenbank nicht erreichbar!');

    if (!defined('DB_SERVER_CHARSET')) {
        define('DB_SERVER_CHARSET', 'utf8');
    }

    if (function_exists('mysql_set_charset') == true) {
        mysql_set_charset('utf8');
    } else {
        mysql_query('set names utf8');
    }

    return $$link;
}
