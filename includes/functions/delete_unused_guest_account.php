<?php

/* -----------------------------------------------------------------
 * 	$Id: delete_unused_guest_account.php 934 2014-04-02 15:40:06Z akausch $
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

function delete_guest_account($p_customer_id) {
    if (DELETE_GUEST_ACCOUNT == 'true') {
        $c_customer_id = (int) $p_customer_id;
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $c_customer_id . "'");
        xtc_db_query("DELETE FROM " . TABLE_ADDRESS_BOOK . " WHERE customers_id = '" . $c_customer_id . "'");
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_INFO . " WHERE customers_info_id = '" . $c_customer_id . "'");
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id = '" . $c_customer_id . "'");
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " WHERE customers_id = '" . $c_customer_id . "'");
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_IP . " WHERE customers_id = '" . $c_customer_id . "'");
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_WISHLIST . " WHERE customers_id = '" . $c_customer_id . "'");
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES . " WHERE customers_id = '" . $c_customer_id . "'");
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_STATUS_HISTORY . " WHERE customers_id = '" . $c_customer_id . "'");
        xtc_db_query("DELETE FROM " . TABLE_COUPON_GV_CUSTOMER . " WHERE customer_id = '" . $c_customer_id . "'");
        xtc_db_query("DELETE FROM " . TABLE_COUPON_GV_QUEUE . " WHERE customer_id = '" . $c_customer_id . "'");
        xtc_db_query("DELETE FROM " . TABLE_COUPON_REDEEM_TRACK . " WHERE customer_id = '" . $c_customer_id . "'");
        xtc_db_query("UPDATE " . TABLE_ORDERS . " SET customers_id = 0 WHERE customers_id = '" . $c_customer_id . "'");
        xtc_db_query("UPDATE " . TABLE_NEWSLETTER_RECIPIENTS . " SET customers_id = 0 WHERE customers_id = '" . $c_customer_id . "'");
    }
}

function delete_unused_guest_accounts() {
    if (DELETE_GUEST_ACCOUNT == 'true') {
        $t_result = xtc_db_query("SELECT 
						c.customers_id,
						w.customer_id
					FROM
						" . TABLE_CUSTOMERS . " AS c
					LEFT JOIN 
						" . TABLE_WHOS_ONLINE . " AS w ON (c.customers_id = w.customer_id)
					WHERE
						c.account_type = 1 
					AND
						w.customer_id IS NULL;");

        while ($t_result_array = xtc_db_fetch_array($t_result)) {
            delete_guest_account($t_result_array['customers_id']);
        }
    }
}
