<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_address_format_id.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_address_format_id($country_id) {
    $address_format_query = xtc_db_query("SELECT address_format_id AS format_id FROM " . TABLE_COUNTRIES . " WHERE countries_id = '" . (int) $country_id . "';");
    if (xtc_db_num_rows($address_format_query)) {
        $address_format = xtc_db_fetch_array($address_format_query);
        return $address_format['format_id'];
    } else {
        return '1';
    }
}
