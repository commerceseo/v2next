<?php

//add iFrame URL to invoice
if (isset($order) && $order->info['payment_method'] === 'secupay_inv_xtc') {
    if (isset($order_info)) {
        $invoice_transaction_query = xtc_db_query("SELECT transaction_id, DATE_FORMAT(created, '%Y%m%d') AS date FROM 
							secupay_transaction_order
							WHERE ordernr = " . intval($_GET['oID']) . ";"
        );
        $invoice_transaction_result = xtc_db_fetch_array($invoice_transaction_query);
        
        $show_due_date = strcmp(MODULE_PAYMENT_SPINV_DUE_DATE, 'Ja') == 0 ? true : false;
        if($show_due_date) {
            $order_info['PAYMENT_METHOD'][1] .= utf8_decode("\n".MODULE_PAYMENT_SPINV_DUE_DATE_TEXT_PDF);
        }
        $order_info['PAYMENT_METHOD'][1] .= utf8_decode("\n" . MODULE_PAYMENT_SPINV_INVOICE_TEXT_PDF_HINT);
        $order_info['PAYMENT_METHOD'][1] .= utf8_decode("\n" . MODULE_PAYMENT_SPINV_KONTO_NR_TITLE) . ': ' . MODULE_PAYMENT_SPINV_KONTO_NR;
        $order_info['PAYMENT_METHOD'][1] .= ', ' . utf8_decode(MODULE_PAYMENT_SPINV_BLZ_TITLE) . ': ' . MODULE_PAYMENT_SPINV_BLZ;
        $order_info['PAYMENT_METHOD'][1] .= ', ' . utf8_decode(MODULE_PAYMENT_SPINV_BANKNAME_TITLE) . ': ' . MODULE_PAYMENT_SPINV_BANKNAME;
        $order_info['PAYMENT_METHOD'][1] .= utf8_decode("\n" . MODULE_PAYMENT_SPINV_IBAN_TITLE) . ': ' . MODULE_PAYMENT_SPINV_IBAN;
        $order_info['PAYMENT_METHOD'][1] .= ', ' . utf8_decode(MODULE_PAYMENT_SPINV_BIC_TITLE) . ': ' . MODULE_PAYMENT_SPINV_BIC;
        $order_info['PAYMENT_METHOD'][1] .= utf8_decode("\n" . MODULE_PAYMENT_SPINV_INVOICE_PURPOSE) . ': ';
        if(!empty($invoice_transaction_result['transaction_id'])) {
            $order_info['PAYMENT_METHOD'][1] .= 'TA ' . $invoice_transaction_result['transaction_id'] . ' ';
        }
        $order_info['PAYMENT_METHOD'][1] .= 'DT ' . $invoice_transaction_result['date'];
        $order_info['PAYMENT_METHOD'][1] .= "\n";

        $invoice_url_query = xtc_db_query("SELECT CONCAT(iframe_url, hash) AS url FROM 
                                                            secupay_transaction_order
                                                            INNER JOIN secupay_iframe_url ON secupay_iframe_url.iframe_url_id = secupay_transaction_order.iframe_url_id
                                                            WHERE ordernr = " . intval($_GET['oID']) . ";"
        );
        $invoice_url_result = xtc_db_fetch_array($invoice_url_query);
        $order_info['PAYMENT_METHOD'][1] .= utf8_decode(MODULE_PAYMENT_SPINV_INVOICE_URL_HINT) .' '. $invoice_url_result['url'];

        $order_info['PAYMENT_METHOD'][1] .= "\n" . utf8_decode(MODULE_PAYMENT_SPINV_QRCODE_PDF_HINT);
    }
}