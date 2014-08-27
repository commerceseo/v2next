<?php

/* -----------------------------------------------------------------
 * 	$Id: order_deleteconfirm.php 420 2013-06-19 18:04:39Z akausch $
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
defined("_VALID_XTC") or die("Direct access to this location isn't allowed.");

$oID = xtc_db_prepare_input($_GET['oID']);

xtc_remove_order($oID, $_POST['restock']);
if ($_POST['paypaldelete']):
    $query = xtc_db_query("SELECT * FROM " . TABLE_PAYPAL . " WHERE xtc_order_id = '" . $oID . "'");
    while ($values = xtc_db_fetch_array($query)) {
        xtc_db_query("delete from " . TABLE_PAYPAL_STATUS_HISTORY . " WHERE paypal_ipn_id = '" . $values['paypal_ipn_id'] . "'");
    }
    xtc_db_query("delete from " . TABLE_PAYPAL . " WHERE xtc_order_id = '" . $oID . "'");
endif;
?>