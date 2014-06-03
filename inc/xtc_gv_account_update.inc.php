<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_gv_account_update.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Update the Customers GV account
function xtc_gv_account_update($customer_id, $gv_id) {
    $customer_gv_query = xtc_db_query("SELECT amount FROM " . TABLE_COUPON_GV_CUSTOMER . " WHERE customer_id = '" . (int) $customer_id . "';");
    $coupon_gv = xtc_db_fetch_array(xtc_db_query("SELECT coupon_amount FROM " . TABLE_COUPONS . " WHERE coupon_id = '" . (int) $gv_id . "';"));
    if (xtc_db_num_rows($customer_gv_query) > 0) {
        $customer_gv = xtc_db_fetch_array($customer_gv_query);
        $new_gv_amount = $customer_gv['amount'] + $coupon_gv['coupon_amount'];
        //prepare for DB insert
        $new_gv_amount = str_replace(",", ".", $new_gv_amount);
        $gv_query = xtc_db_query("UPDATE " . TABLE_COUPON_GV_CUSTOMER . " SET amount = '" . $new_gv_amount . "' WHERE customer_id = '" . (int) $customer_id . "';");
    } else {
        $gv_query = xtc_db_query("INSERT INTO " . TABLE_COUPON_GV_CUSTOMER . " (customer_id, amount) VALUES ('" . (int) $customer_id . "', '" . $coupon_gv['coupon_amount'] . "');");
    }
}
