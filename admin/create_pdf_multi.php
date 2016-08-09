<?php

/* -----------------------------------------------------------------
 * 	$Id: create_pdf_multi.php 1457 2015-04-21 09:38:44Z akausch $
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

require_once('includes/application_top.php');
define('FPDF_FONTPATH', DIR_FS_ADMIN . 'pdf/font/');
$pdf_query = xtc_db_query("SELECT pdf_key, pdf_value FROM orders_pdf_profile WHERE languages_id = '0';");

while ($pdf = xtc_db_fetch_array($pdf_query)) {
    define($pdf['pdf_key'], $pdf['pdf_value']);
}
require_once(DIR_FS_INC . 'xtc_get_order_data.inc.php');
require_once(DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
require_once(DIR_FS_INC . 'xtc_format_price_order.inc.php');
include_once(DIR_WS_CLASSES . 'class.order.php');
require_once(DIR_FS_INC . 'cseo_get_pdf_nr.inc.php');

function custom_multistatusGenPDF($myorderid, $mytype, $rechnungsnummernext) {

    if ($mytype == "rechnung") {
        define('PDF_LIEFERSCHEIN', false);
    } else {
        define('PDF_LIEFERSCHEIN', true);
    }

    $order = new order($myorderid);
    $type = $mytype;

    if (PDF_RECHNUNG_OID == 'true') {
        $mybillno = $myorderid;
    } else {
        $mybillno = $rechnungsnummernext;
    }

    $sprach_id = $_POST['pdf_language_id'];
    $sprache = $order->info['language'];

    require_once('pdf/pdf_bill.php');
    $pdf = new PDF_Bill();
    $pdf->Init(($type == 'rechnung' ? FILENAME_BILL : FILENAME_PACKINSLIP));

    // Kunden ID abfragen
	if (PDF_LIEFERSCHEIN) {
		$kundenadresse = xtc_address_format($order->customer['format_id'], $order->delivery, 1, '', '<br>');
	} else {
		$kundenadresse = xtc_address_format($order->customer['format_id'], $order->billing, 1, '', '<br>');
	}
    // $kundenadresse = xtc_address_format($order->customer['format_id'], $order->billing, 1, '', '<br>');
    $pdf->Adresse(utf8_decode(str_replace("<br>", "\n", $kundenadresse)), TEXT_PDF_SHOPADRESSEKLEIN);
    $logo = DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/' . LAYOUT_LOGO_FILE;

    if (file_exists($logo) && LAYOUT_LOGO_FILE != '') {
        $pdf->Logo($logo);
    }

    if (PDF_RECHNUNG_DATE_ACT == 'true') {
        $date_purchased = time();
        $date_purchased = date("d.m.Y", $date_purchased);
    } else {
        $date_purchased = xtc_date_short($order->info['date_purchased']);
    }

    if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') {
        include(DIR_FS_CATALOG . 'lang/' . $sprache . '/modules/payment/' . $order->info['payment_method'] . '.php');
        $payment_method = constant(strtoupper('MODULE_PAYMENT_' . $order->info['payment_method'] . '_TEXT_TITLE'));
        $payment_method = strip_tags($payment_method);
        $payment_method = html_entity_decode($payment_method);
        $payment_method = utf8_decode($payment_method);
    } else {
        $payment_method = '';
    }

    $order_check = xtc_db_fetch_array(xtc_db_query("SELECT customers_id FROM " . TABLE_ORDERS . " WHERE orders_id='" . $myorderid . "';"));
    $customer_gender = xtc_db_fetch_array(xtc_db_query("SELECT customers_gender FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" . $order_check['customers_id'] . "';"));
    $pdf->Rechnungsdaten($order->customer['csID'], $myorderid, $order->customer['vat_id'], $mybillno, $date_purchased, $payment_method, PDF_LIEFERSCHEIN);
    $pdf->RechnungStart($mybillno, $myorderid, utf8_decode($order->customer['lastname']), $customer_gender['customers_gender'], PDF_LIEFERSCHEIN);
    $pdf->ListeKopf(PDF_LIEFERSCHEIN);

    // Produktinfos
    $order_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id='" . (int) $myorderid . "'");
    $order_data = array();

    // Ausgabe der Produkte
    while ($order_data_values = xtc_db_fetch_array($order_query)) {
        $attributes_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_products_id='" . $order_data_values['orders_products_id'] . "' ORDER BY orders_products_attributes_id ASC;");
        $attributes_data = '';
        $attributes_model = '';

        while ($attributes_data_values = xtc_db_fetch_array($attributes_query)) {
            $attributes_data .= $attributes_data_values['products_options'] . ': ' . $attributes_data_values['products_options_values'] . "\n";
            $attributes_model .= xtc_get_attributes_model($order_data_values['products_id'], $attributes_data_values['products_options_values'], $attributes_data_values['products_options']) . "\n";
        }

        $orderinfosingleprice = str_replace('€', 'EUR', xtc_format_price_order($order_data_values['products_price'], 1, $order->info['currency']));
        $orderinfosingleprice = str_replace('&euro;', 'EUR', $orderinfosingleprice);
        $orderinfosumleprice = str_replace('€', 'EUR', xtc_format_price_order($order_data_values['final_price'], 1, $order->info['currency']));
        $orderinfosumleprice = str_replace('&euro;', 'EUR', $orderinfosumleprice);
        $orderinfocurreny = str_replace('&euro;', 'EUR', $orderinfocurreny);
        $pdf->ListeProduktHinzu($order_data_values['products_quantity'], utf8_decode(strip_tags($order_data_values['products_name'])), utf8_decode(trim($attributes_data)), $order_data_values['products_model'], trim($attributes_model, $type), $orderinfosingleprice, $orderinfosumleprice, $type);
    }
	if (!PDF_LIEFERSCHEIN) {
		$oder_total_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id='" . $myorderid . "' ORDER BY sort_order ASC;");
		$order_data = array();

		while ($oder_total_values = xtc_db_fetch_array($oder_total_query)) {
			$ordervaluetext = str_replace('€', 'EUR', $oder_total_values['text']);
			$ordervaluetext = str_replace('&euro;', 'EUR', $ordervaluetext);
			$order_data[] = array('title' => $oder_total_values['title'], 'class' => $oder_total_values['class'], 'value' => $oder_total_values['value'], 'text' => $ordervaluetext);
		}
		$pdf->Betrag($order_data, PDF_LIEFERSCHEIN);
	}
	/** BEGIN BILLPAY CHANGED * */
	if ($order->info['payment_method'] == 'billpay' || $order->info['payment_method'] == 'billpaydebit' || $order->info['payment_method'] == 'billpaytransactioncredit') {
		require_once(DIR_FS_CATALOG . DIR_WS_INCLUDES . '/billpay/utils/billpay_display_pdf_data.php');
	}
	/** EOF BILLPAY CHANGED * */
    $pdf->RechnungEnde($order->customer['vat_id'], $order->info['shipping_method'], PDF_LIEFERSCHEIN);
    if (MODULE_CUSTOMERS_PDF_INVOICE_PRINT_COMMENT == 'true') {
		$pdf->Kommentar($order->info['comments'], $order->info['shipping_method'], PDF_LIEFERSCHEIN);
	}
    if ($order->info['payment_method'] == 'invoice' || $order->info['payment_method'] == 'moneyorder') {
        $invoiceinfo = TEXT_PDF_INVOICE_TEXT;
        $pdf->Invoice($invoiceinfo);
    }

    $lieferadresse = xtc_address_format($order->customer['format_id'], $order->delivery, 1, '', ', ');

    $pdf->LieferAdresse(utf8_decode($lieferadresse));

    if ($type == 'rechnung') {
        $pdf_name = cseo_get_pdf_multistatus($myorderid, true, false, false, false, false, $mybillno) . '.pdf';
    } else {
        $pdf_name = cseo_get_pdf_multistatus_delivery($myorderid, true, false, false, false, false, $mybillno) . '.pdf';
    }

    $pdf->Output($pdf_name, 'F');

    if ($type == 'rechnung') {
        $check_pdf = xtc_db_query("SELECT bill_name FROM orders_pdf WHERE order_id = '" . $myorderid . "'");
        if (xtc_db_num_rows($check_pdf) > 0) {
            xtc_db_query("UPDATE orders_pdf SET bill_name = '" . $pdf_name . "', pdf_bill_nr = '" . $mybillno . "', pdf_generate_date = NOW() WHERE order_id = '" . $myorderid . "' ");
        } else {
            xtc_db_query("INSERT INTO orders_pdf (order_id, bill_name, pdf_bill_nr, pdf_generate_date) VALUES ('" . $myorderid . "', '" . $pdf_name . "', '" . $mybillno . "', NOW());");
        }
    } else {
        $check_pdf = xtc_db_query("SELECT delivery_name FROM orders_pdf_delivery WHERE order_id = '" . $myorderid . "'");
        if (xtc_db_num_rows($check_pdf) > 0) {
            xtc_db_query("UPDATE orders_pdf_delivery SET delivery_name = '" . $pdf_name . "', pdf_delivery_nr = '" . $mybillno . "', pdf_generate_date = NOW() WHERE order_id = '" . $myorderid . "' ");
        } else {
            xtc_db_query("INSERT INTO orders_pdf_delivery (order_id, delivery_name, pdf_delivery_nr, pdf_generate_date) VALUES ('" . $myorderid . "', '" . $pdf_name . "', '" . $mybillno . "', NOW());");
        }
    }
}
