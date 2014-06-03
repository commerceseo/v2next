<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_count_customer_address_book_entries.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_count_customer_address_book_entries($id = '', $check_session = true) {
    if (is_numeric($id) == false) {
        if (isset($_SESSION['customer_id'])) {
            $id = $_SESSION['customer_id'];
        } else {
            return 0;
        }
    }
    if ($check_session == true) {
        if ((isset($_SESSION['customer_id']) == false) || ($id != $_SESSION['customer_id'])) {
            return 0;
        }
    }
    $addresses = xtc_db_fetch_array(xtc_db_query("SELECT count(*) AS total FROM " . TABLE_ADDRESS_BOOK . " WHERE customers_id = '" . (int) $id . "';"));
    return $addresses['total'];
}
