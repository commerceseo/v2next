<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_oe_customer_infos.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_oe_customer_infos($customers_id) {
    $customer = xtc_db_fetch_array(xtc_db_query("SELECT a.entry_country_id, a.entry_zone_id FROM " . TABLE_CUSTOMERS . " c, " . TABLE_ADDRESS_BOOK . " a WHERE c.customers_id  = '" . (int) $customers_id . "' AND c.customers_id = a.customers_id AND c.customers_default_address_id = a.address_book_id;"));
    $customer_info_array = array('country_id' => $customer['entry_country_id'], 'zone_id' => $customer['entry_zone_id']);
    return $customer_info_array;
}
