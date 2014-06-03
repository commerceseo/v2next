<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_customer_status_value.inc.php 866 2014-03-17 12:07:35Z akausch $
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
function xtc_get_customer_status_value($customer_id) {

    if (isset($_SESSION['customer_id'])) {
        $customer_status_query = xtc_db_query("SELECT c.customers_status, c.member_flag, cs.customers_status_id, cs.customers_status_name, cs.customers_status_public, cs.customers_status_show_price, cs_customers_status_min_order, cs.customers_status_max_order, cs.customers_status_show_price_tax, cs.customers_status_image, cs.customers_status_discount, cs.customers_status_ot_discount_flag, cs.customers_status_ot_discount, cs.customers_status_graduated_prices, cs.customers_status_cod_permission, cs.customers_status_cc_permission, cs.customers_status_bt_permission FROM " . TABLE_CUSTOMERS . " AS c LEFT JOIN " . TABLE_CUSTOMERS_STATUS . " AS cs on customers_status = customers_status_id WHERE c.customers_id='" . (int) $_SESSION['customer_id'] . "' AND cs.language_id = '" . (int) $_SESSION['languages_id'] . "';");
    } else {
        $customer_status_query = xtc_db_query("SELECT cs.customers_status_id, cs.customers_status_name, cs.customers_status_public, cs.customers_status_show_price, cs_customers_status_min_order, cs.customers_status_max_order, cs.customers_status_show_price_tax, cs.customers_status_image, cs.customers_status_discount, cs.customers_status_ot_discount_flag, cs.customers_status_ot_discount, cs.customers_status_graduated_prices  FROM " . TABLE_CUSTOMERS_STATUS . " AS cs WHERE cs.customers_status_id='" . DEFAULT_CUSTOMERS_STATUS_ID_GUEST . "' AND cs.language_id = '" . (int) $_SESSION['languages_id'] . "';");
        $customer_status_value['customers_status'] = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
    }

    $customer_status_value = xtc_db_fetch_array($customer_status_query);

    session_register('customer_status_value');
    return $customer_status_value;
}
