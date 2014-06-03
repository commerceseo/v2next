<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_customers_country.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_customers_country($customers_id) {
    $customers = xtc_db_fetch_array(xtc_db_query("SELECT customers_default_address_id FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . (int) $customers_id . "';"));
    $address_book = xtc_db_fetch_array(xtc_db_query("SELECT entry_country_id FROM " . TABLE_ADDRESS_BOOK . " WHERE address_book_id = '" . $customers['customers_default_address_id'] . "';"));
    return $address_book['entry_country_id'];
}
