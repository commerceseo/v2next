<?php

/* -----------------------------------------------------------------
 * 	ID:						xtc_db_num_rows.inc.php
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

function xtc_db_num_rows($db_query, $cq = false) {
    if (DB_CACHE == 'true' && $cq) {
        if (!count($db_query))
            return false;
        return count($db_query);
    } else {
        if (!is_array($db_query))
            return mysql_num_rows($db_query);
    }
}
