<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_address_summary.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_address_summary($customers_id, $address_id) {
    $customers_id = xtc_db_prepare_input($customers_id);
    $address_id = xtc_db_prepare_input($address_id);

    $address = xtc_db_fetch_array(xtc_db_query("SELECT ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_state, ab.entry_country_id, ab.entry_zone_id, c.countries_name, c.address_format_id FROM " . TABLE_ADDRESS_BOOK . " ab, " . TABLE_COUNTRIES . " c WHERE ab.address_book_id = '" . xtc_db_input($address_id) . "' AND ab.customers_id = '" . xtc_db_input($customers_id) . "' AND ab.entry_country_id = c.countries_id;"));

    $street_address = $address['entry_street_address'];
    $suburb = $address['entry_suburb'];
    $postcode = $address['entry_postcode'];
    $city = $address['entry_city'];
    $state = xtc_get_zone_code($address['entry_country_id'], $address['entry_zone_id'], $address['entry_state']);
    $country = $address['countries_name'];

    $address_format = xtc_db_fetch_array(xtc_db_query("SELECT address_summary FROM " . TABLE_ADDRESS_FORMAT . " WHERE address_format_id = '" . $address['address_format_id'] . "';"));

    $address_summary = $address_format['address_summary'];
    eval("\$address = \"$address_summary\";");

    return $address;
}
