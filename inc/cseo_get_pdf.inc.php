<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_get_pdf.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function cseo_get_pdf($oID, $createName = false, $checkPDF = false, $getNameShort = false, $getNameLong = false, $CheckMail = false) {
    if ($createName) {
        include_once(DIR_WS_CLASSES . 'class.order.php');
        $order = new order($oID);

        $name = str_replace('#bn#', $oID, TEXT_PDF_FILE_NAME);
        $name = str_replace('#rn#', $_POST['pdf_bill_nr'], $name);
        $name = str_replace('#vn#', $order->customer['firstname'], $name);
        $name = str_replace('#nn#', $order->customer['lastname'], $name);
        $name = str_replace('#d#', date("d-m-Y"), $name);

        $pfad = 'pdf_rechnungen/' . $name;
        return $pfad;
    } elseif ($checkPDF) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT bill_name FROM orders_pdf WHERE order_id = '" . (int) $oID . "' "));
        if (file_exists($pdf['bill_name']))
            return true;
        else
            return false;
    } elseif ($getNameShort) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT bill_name FROM orders_pdf WHERE order_id = '" . (int) $oID . "' "));
        $pdf = explode('/', $pdf['bill_name']);
        return $pdf[1];
    } elseif ($getNameLong) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT bill_name FROM orders_pdf WHERE order_id = '" . (int) $oID . "' "));
        return $pdf['bill_name'];
    } elseif ($CheckMail) {
        $pdf_query = xtc_db_query("SELECT customer_notified, notified_date FROM orders_pdf WHERE order_id = '" . (int) $oID . "' ");
        if (xtc_db_num_rows($pdf_query, true) > 0) {
            $pdf = xtc_db_fetch_array($pdf_query);
            if ($pdf['customer_notified'] == '1')
                return xtc_date_short($pdf['notified_date']);
            else
                return false;
        } else
            return false;
    }
}

function cseo_get_pdf_multistatus($oID, $createName = false, $checkPDF = false, $getNameShort = false, $getNameLong = false, $CheckMail = false, $multistatusrechnungsnr) {
    if ($createName) {
        include_once(DIR_WS_CLASSES . 'class.order.php');
        $order = new order($oID);
        $name = str_replace('#bn#', $oID, TEXT_PDF_FILE_NAME);
        $name = str_replace('#rn#', $multistatusrechnungsnr, $name);
        $name = str_replace('#vn#', $order->customer['firstname'], $name);
        $name = str_replace('#nn#', $order->customer['lastname'], $name);
        $name = str_replace('#d#', date("d-m-Y"), $name);

        $pfad = 'pdf_rechnungen/' . $name;
        return $pfad;
    } elseif ($checkPDF) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT bill_name FROM orders_pdf WHERE order_id = '" . (int) $oID . "' "));
        if (file_exists($pdf['bill_name']))
            return true;
        else
            return false;
    } elseif ($getNameShort) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT bill_name FROM orders_pdf WHERE order_id = '" . (int) $oID . "' "));
        $pdf = explode('/', $pdf['bill_name']);
        return $pdf[1];
    } elseif ($getNameLong) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT bill_name FROM orders_pdf WHERE order_id = '" . (int) $oID . "' "));
        return $pdf['bill_name'];
    } elseif ($CheckMail) {
        $pdf_query = xtc_db_query("SELECT customer_notified, notified_date FROM orders_pdf WHERE order_id = '" . (int) $oID . "' ");
        if (xtc_db_num_rows($pdf_query, true) > 0) {
            $pdf = xtc_db_fetch_array($pdf_query);
            if ($pdf['customer_notified'] == '1')
                return xtc_date_short($pdf['notified_date']);
            else
                return false;
        } else
            return false;
    }
}

function cseo_get_pdf_delivery($oID, $createName = false, $checkPDF = false, $getNameShort = false, $getNameLong = false, $CheckMail = false) {
    if ($createName) {
        include_once(DIR_WS_CLASSES . 'class.order.php');
        $order = new order($oID);

        $name = str_replace('#bn#', $oID, TEXT_PDF_DELIVERY_FILE_NAME);
        $name = str_replace('#rn#', $_POST['pdf_bill_nr'], $name);
        $name = str_replace('#vn#', $order->customer['firstname'], $name);
        $name = str_replace('#nn#', $order->customer['lastname'], $name);
        $name = str_replace('#d#', date("d-m-Y"), $name);

        $pfad = 'pdf_lieferscheine/' . $name;
        return $pfad;
    } elseif ($checkPDF) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT delivery_name FROM orders_pdf_delivery WHERE order_id = '" . (int) $oID . "' "));
        if (file_exists($pdf['delivery_name']))
            return true;
        else
            return false;
    } elseif ($getNameShort) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT delivery_name FROM orders_pdf_delivery WHERE order_id = '" . (int) $oID . "' "));
        $pdf = explode('/', $pdf['delivery_name']);
        return $pdf[1];
    } elseif ($getNameLong) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT delivery_name FROM orders_pdf_delivery WHERE order_id = '" . (int) $oID . "' "));
        return $pdf['delivery_name'];
    } elseif ($CheckMail) {
        $pdf_query = xtc_db_query("SELECT customer_notified, notified_date FROM orders_pdf_delivery WHERE order_id = '" . (int) $oID . "' ");
        if (xtc_db_num_rows($pdf_query, true) > 0) {
            $pdf = xtc_db_fetch_array($pdf_query);
            if ($pdf['customer_notified'] == '1')
                return xtc_date_short($pdf['notified_date']);
            else
                return false;
        } else
            return false;
    }
}

function cseo_get_pdf_multistatus_delivery($oID, $createName = false, $checkPDF = false, $getNameShort = false, $getNameLong = false, $CheckMail = false, $multistatusrechnungsnr) {
    if ($createName) {
        include_once(DIR_WS_CLASSES . 'class.order.php');
        $order = new order($oID);
        $name = str_replace('#bn#', $oID, TEXT_PDF_DELIVERY_FILE_NAME);
        $name = str_replace('#rn#', $multistatusrechnungsnr, $name);
        $name = str_replace('#vn#', $order->customer['firstname'], $name);
        $name = str_replace('#nn#', $order->customer['lastname'], $name);
        $name = str_replace('#d#', date("d-m-Y"), $name);

        $pfad = 'pdf_lieferscheine/' . $name;
        return $pfad;
    } elseif ($checkPDF) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT delivery_name FROM orders_pdf_delivery WHERE order_id = '" . (int) $oID . "' "));
        if (file_exists($pdf['delivery_name']))
            return true;
        else
            return false;
    } elseif ($getNameShort) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT delivery_name FROM orders_pdf_delivery WHERE order_id = '" . (int) $oID . "' "));
        $pdf = explode('/', $pdf['delivery_name']);
        return $pdf[1];
    } elseif ($getNameLong) {
        $pdf = xtc_db_fetch_array(xtc_db_query("SELECT delivery_name FROM orders_pdf_delivery WHERE order_id = '" . (int) $oID . "' "));
        return $pdf['delivery_name'];
    } elseif ($CheckMail) {
        $pdf_query = xtc_db_query("SELECT customer_notified, notified_date FROM orders_pdf_delivery WHERE order_id = '" . (int) $oID . "' ");
        if (xtc_db_num_rows($pdf_query, true) > 0) {
            $pdf = xtc_db_fetch_array($pdf_query);
            if ($pdf['customer_notified'] == '1')
                return xtc_date_short($pdf['notified_date']);
            else
                return false;
        } else
            return false;
    }
}
