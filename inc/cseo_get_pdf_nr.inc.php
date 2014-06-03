<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_get_pdf_nr.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function cseo_get_pdf_nr($oID, $CheckNr = false, $GetNextNr = false, $is_send = false) {
    if ($CheckNr) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT pdf_bill_nr AS nr FROM orders_pdf WHERE order_id = '" . (int) $oID . "';"));
        return $pdf['nr'];
    } elseif ($GetNextNr) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT MAX(pdf_bill_nr) AS next_nr FROM orders_pdf;"));
        return $pdf['next_nr'] + 1;
    } elseif ($is_send) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT customer_notified FROM orders_pdf WHERE order_id = '" . (int) $oID . "';"));
        if ($pdf['customer_notified'] > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function cseo_get_pdf_delivery_nr($oID, $CheckNr = false, $GetNextNr = false, $is_send = false) {
    if ($CheckNr) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT pdf_delivery_nr AS nr FROM orders_pdf_delivery WHERE order_id = '" . (int) $oID . "';"));
        return $pdf['nr'];
    } elseif ($GetNextNr) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT MAX(pdf_delivery_nr) AS next_nr FROM orders_pdf_delivery;"));
        return $pdf['next_nr'] + 1;
    } elseif ($is_send) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT customer_notified FROM orders_pdf_delivery WHERE order_id = '" . (int) $oID . "';"));
        if ($pdf['customer_notified'] > 0) {
            return true;
        } else {
            return false;
        }
    }
}
