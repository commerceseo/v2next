<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_address_label.inc.php 866 2014-03-17 12:07:35Z akausch $
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

require_once(DIR_FS_INC . 'xtc_get_address_format_id.inc.php');
require_once(DIR_FS_INC . 'xtc_address_format.inc.php');

function xtc_address_label($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {
    $address = xtc_db_fetch_array(xtc_db_query("SELECT 
										entry_firstname AS firstname, 
										entry_lastname AS lastname, 
										entry_company AS company, 
										entry_street_address AS street_address, 
										entry_suburb AS suburb, 
										entry_city AS city, 
										entry_postcode AS postcode, 
										entry_state AS state, 
										entry_zone_id AS zone_id, 
										entry_country_id AS country_id 
									FROM 
										" . TABLE_ADDRESS_BOOK . " 
									WHERE 
										customers_id = '" . (int) $customers_id . "' 
									AND 
										address_book_id = '" . (int) $address_id . "';"));

    $format_id = xtc_get_address_format_id($address['country_id']);
    return xtc_address_format($format_id, $address, $html, $boln, $eoln);
}
