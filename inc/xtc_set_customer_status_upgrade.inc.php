<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_set_customer_status_upgrade.inc.php 866 2014-03-17 12:07:35Z akausch $
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

//set customer satus to new customer for upgrade account
function xtc_set_customer_status_upgrade($customer_id) {

    if (($_SESSION['customer_status_value']['customers_status_id'] == "' . DEFAULT_CUSTOMERS_STATUS_ID_NEWSLETTER .'" ) && ($_SESSION['customer_status_value']['customers_is_newsletter'] == 0 )) {
        xtc_db_query("UPDATE " . TABLE_CUSTOMERS . " SET customers_status = '" . DEFAULT_CUSTOMERS_STATUS_ID . "' WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "';");
        xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_STATUS_HISTORY . " (customers_id, new_value, old_value, date_added, customer_notified) VALUES ('" . (int) $_SESSION['customer_id'] . "', '" . DEFAULT_CUSTOMERS_STATUS_ID . "', '" . DEFAULT_CUSTOMERS_STATUS_ID_NEWSLETTER . "', now(), '" . $customer_notified . "');");
    }
    return 1;
}
