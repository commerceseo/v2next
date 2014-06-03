<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_customers_statuses.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Return all customers statuses for a specified language_id and return an array(array())
// Use it to make pull_down_menu, checkbox....
function xtc_get_customers_statuses() {

    $customers_statuses_array = array(array());
    if ($_SESSION['languages_id'] == '') {
        $customers_statuses_query = xtc_db_query("SELECT * FROM " . TABLE_CUSTOMERS_STATUS . " WHERE language_id = '1' ORDER BY customers_status_id;");
    } else {
        $customers_statuses_query = xtc_db_query("SELECT * FROM " . TABLE_CUSTOMERS_STATUS . " WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY customers_status_id;");
    }

    $i = 1;
    while ($customers_statuses = xtc_db_fetch_array($customers_statuses_query)) {
        $i = $customers_statuses['customers_status_id'];
        $customers_statuses_array[] = array('id' => $customers_statuses['customers_status_id'],
            'text' => $customers_statuses['customers_status_name'],
            'csa_public' => $customers_statuses['customers_status_public'],
            'csa_show_price' => $customers_statuses['customers_status_show_price'],
            'csa_show_price_tax' => $customers_statuses['customers_status_show_price_tax'],
            'csa_image' => $customers_statuses['customers_status_image'],
            'csa_discount' => $customers_statuses['customers_status_discount'],
            'csa_ot_discount_flag' => $customers_statuses['customers_status_ot_discount_flag'],
            'csa_ot_discount' => $customers_statuses['customers_status_ot_discount'],
            'csa_graduated_prices' => $customers_statuses['customers_status_graduated_prices'],
            'csa_cod_permission' => $customers_statuses['customers_status_cod_permission'],
            'csa_cc_permission' => $customers_statuses['customers_status_cc_permission'],
            'csa_bt_permission' => $customers_statuses['customers_status_bt_permission'],
        );
    }
    return $customers_statuses_array;
}
