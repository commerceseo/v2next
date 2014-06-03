<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_countries.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_countriesList($countries_id = '', $with_iso_codes = false) {
    $countries_array = array();
    if (xtc_not_null($countries_id)) {
        if ($with_iso_codes == true) {
            $countries_values = xtc_db_fetch_array(xtc_db_query("SELECT countries_name, countries_iso_code_2, countries_iso_code_3 FROM " . TABLE_COUNTRIES . " WHERE countries_id = '" . (int) $countries_id . "' AND status = '1' ORDER BY countries_name;"));
            $countries_array = array('countries_name' => $countries_values['countries_name'], 'countries_iso_code_2' => $countries_values['countries_iso_code_2'], 'countries_iso_code_3' => $countries_values['countries_iso_code_3']);
        } else {
            $countries_values = xtc_db_fetch_array(xtc_db_query("SELECT countries_name FROM " . TABLE_COUNTRIES . " WHERE countries_id = '" . (int) $countries_id . "' AND status = '1';"));
            $countries_array = array('countries_name' => $countries_values['countries_name']);
        }
    } else {
        $countries = xtc_db_query("SELECT countries_id, countries_name FROM " . TABLE_COUNTRIES . " WHERE status = '1' ORDER BY countries_name");
        while ($countries_values = xtc_db_fetch_array($countries)) {
            $countries_array[] = array('countries_id' => $countries_values['countries_id'], 'countries_name' => $countries_values['countries_name']);
        }
    }

    return $countries_array;
}
