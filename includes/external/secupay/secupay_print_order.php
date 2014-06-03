<?php

//add iFrame URL to invoice
if (isset($order) && $order->info['payment_method'] === 'secupay_inv_xtc') {

    $invoice_transaction_query = xtc_db_query("SELECT transaction_id, DATE_FORMAT(created, '%Y%m%d') AS date FROM 
							secupay_transaction_order
							WHERE ordernr = " . intval($_GET['oID']) . ";"
    );
    $invoice_transaction_result = xtc_db_fetch_array($invoice_transaction_query);
    
    $show_due_date = strcmp(MODULE_PAYMENT_SPINV_DUE_DATE, 'Ja') == 0 ? true : false;
    if($show_due_date) {
        $secupay_payment_info .= '<br>'.MODULE_PAYMENT_SPINV_DUE_DATE_TEXT.'<br>';
    }
    $secupay_payment_info .= '<br>' . MODULE_PAYMENT_SPINV_INVOICE_TEXT;
    $secupay_payment_info .= '<br>' . MODULE_PAYMENT_SPINV_KONTO_NR_TITLE . ': ' . MODULE_PAYMENT_SPINV_KONTO_NR;
    $secupay_payment_info .= ', ' . MODULE_PAYMENT_SPINV_BLZ_TITLE . ': ' . MODULE_PAYMENT_SPINV_BLZ;
    $secupay_payment_info .= ', ' . MODULE_PAYMENT_SPINV_BANKNAME_TITLE . ': ' . MODULE_PAYMENT_SPINV_BANKNAME;
    $secupay_payment_info .= '<br>' . MODULE_PAYMENT_SPINV_IBAN_TITLE . ': ' . MODULE_PAYMENT_SPINV_IBAN;
    $secupay_payment_info .= ', ' . MODULE_PAYMENT_SPINV_BIC_TITLE . ': ' . MODULE_PAYMENT_SPINV_BIC;
    $secupay_payment_info .= '<br><br>' . MODULE_PAYMENT_SPINV_INVOICE_PURPOSE . ': ';
    if(!empty($invoice_transaction_result['transaction_id'])) {
        $secupay_payment_info .= '<b>TA ' . $invoice_transaction_result['transaction_id'] . ' ';
    }
    $secupay_payment_info .= 'DT ' . $invoice_transaction_result['date'];
    $secupay_payment_info .= '</b><br>';

    $show_qrcode = strcmp(MODULE_PAYMENT_SPINV_SHOW_QRCODE, 'Ja') == 0 ? true : false;
    if ($show_qrcode) {
        $invoice_url_query = xtc_db_query("SELECT CONCAT(iframe_url, hash) AS url FROM 
							secupay_transaction_order
							INNER JOIN secupay_iframe_url ON secupay_iframe_url.iframe_url_id = secupay_transaction_order.iframe_url_id
							WHERE ordernr = " . intval($_GET['oID']) . ";"
        );
        $invoice_url_result = xtc_db_fetch_array($invoice_url_query);
        if (isset($invoice_url_result['url']) && strlen($invoice_url_result['url']) > 5) {
            $secupay_payment_info .= '<br>' . MODULE_PAYMENT_SPINV_QRCODE_DESC . '<br>' . $invoice_url_result['url'];

            $qr_code = 'https://api.secupay.ag/qr?d=' . urlencode($invoice_url_result['url']);
            $secupay_payment_info_qr_code .= '<br><img alt="" style="border:0;" src="' . $qr_code . '"/>';
        }
    }
}