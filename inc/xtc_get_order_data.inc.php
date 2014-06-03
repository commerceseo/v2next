<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_order_data.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_order_data($order_id) {
    $order_data = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_ORDERS . " WHERE orders_id='" . (int) $_GET['oID'] . "';"));
    // get order status name	
    $order_status_data = xtc_db_fetch_array(xtc_db_query("SELECT orders_status_name FROM " . TABLE_ORDERS_STATUS . " WHERE orders_status_id='" . (int) $order_data['orders_status'] . "' AND language_id='" . (int) $_SESSION['languages_id'] . "';"));
    $order_data['orders_status'] = $order_status_data['orders_status_name'];
    // get language name for payment method
    include(DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $order_data['payment_method'] . '.php');
    $order_data['payment_method'] = constant(strtoupper('MODULE_PAYMENT_' . $order_data['payment_method'] . '_TEXT_TITLE'));
    return $order_data;
}
