<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_validate_vatid_status.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Return all status info values for a customer_id in catalog, need to check session registered customer or will return dafault guest customer status value !
function xtc_validate_vatid_status($customer_id) {
    $customer_status_value = xtc_db_fetch_array(xtc_db_query("SELECT customers_vat_id_status FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" . (int) $customer_id . "';"));
    if ($customer_status_value['customers_vat_id_status'] == '0') {
        $value = TEXT_VAT_FALSE;
    }
    if ($customer_status_value['customers_vat_id_status'] == '1') {
        $value = TEXT_VAT_TRUE;
    }
    if ($customer_status_value['customers_vat_id_status'] == '8') {
        $value = TEXT_VAT_UNKNOWN_COUNTRY;
    }
    if ($customer_status_value['customers_vat_id_status'] == '9') {
        $value = TEXT_VAT_UNKNOWN_ALGORITHM;
    }
    return $value;
}
