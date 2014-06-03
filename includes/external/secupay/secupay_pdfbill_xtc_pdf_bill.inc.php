<?php
if (isset($order) && $order->info['payment_method'] === 'secupay_inv_xtc') {
    
    if(isset($pdf)) {
        $invoice_transaction_query = xtc_db_query("SELECT transaction_id, DATE_FORMAT(created, '%Y%m%d') AS date FROM 
                                                        secupay_transaction_order
                                                        WHERE ordernr = " . intval($_GET['oID']) . ";"
        );
        $invoice_transaction_result = xtc_db_fetch_array($invoice_transaction_query);		

        $pdf->SetFont($pdf->fontfamily, '', 11);
        $pdf->Ln();
        //$pdf->AddPage();
        $secupay_invoice_text = utf8_decode(MODULE_PAYMENT_SPINV_INVOICE_TEXT_PDF);
        $pdf->MultiCell(0, 4, $secupay_invoice_text);
        $secupay_invoice_account_info = utf8_decode( MODULE_PAYMENT_SPINV_KONTO_NR_TITLE . ': ' . MODULE_PAYMENT_SPINV_KONTO_NR);
        $secupay_invoice_account_info .= utf8_decode(', ' . MODULE_PAYMENT_SPINV_BLZ_TITLE . ': ' . MODULE_PAYMENT_SPINV_BLZ);
        $secupay_invoice_account_info .= utf8_decode(', ' . MODULE_PAYMENT_SPINV_BANKNAME_TITLE . ': ' . MODULE_PAYMENT_SPINV_BANKNAME);
        $pdf->MultiCell(0, 4, $secupay_invoice_account_info);
        $secupay_invoice_account_iban = utf8_decode(MODULE_PAYMENT_SPINV_IBAN_TITLE . ': ' . MODULE_PAYMENT_SPINV_IBAN);
        $secupay_invoice_account_iban .= utf8_decode(', ' . MODULE_PAYMENT_SPINV_BIC_TITLE . ': ' . MODULE_PAYMENT_SPINV_BIC);
        $pdf->MultiCell(0, 4, $secupay_invoice_account_iban);

        $secupay_invoice_purpose = utf8_decode(MODULE_PAYMENT_SPINV_INVOICE_PURPOSE) . ': ';
        if(!empty($invoice_transaction_result['transaction_id'])) {
        $secupay_invoice_purpose .= 'TA ' . $invoice_transaction_result['transaction_id'] . ' ';
        }
        $secupay_invoice_purpose .= 'DT '. $invoice_transaction_result['date'];
        $pdf->SetFont($pdf->fontfamily, 'B', 11);
        $pdf->MultiCell(0, 4, $secupay_invoice_purpose);
        $pdf->SetFont($pdf->fontfamily, '', 11);
        
        $show_due_date = strcmp(MODULE_PAYMENT_SPINV_DUE_DATE, 'Ja') == 0 ? true : false;
        if($show_due_date) {
            $secupay_invoice_due_date = utf8_decode(MODULE_PAYMENT_SPINV_DUE_DATE_TEXT_PDF);
            $pdf->Ln();
            $pdf->MultiCell(0, 4, $secupay_invoice_due_date);
        }        
        

        $show_qrcode = strcmp(MODULE_PAYMENT_SPINV_SHOW_QRCODE, 'Ja') == 0 ? true : false;
        if ($show_qrcode) {
            $invoice_url_query = xtc_db_query("SELECT CONCAT(iframe_url, hash) AS url FROM 
                                                            secupay_transaction_order
                                                            INNER JOIN secupay_iframe_url ON secupay_iframe_url.iframe_url_id = secupay_transaction_order.iframe_url_id
                                                            WHERE ordernr = " . intval($_GET['oID']) . ";"
            );
            $invoice_url_result = xtc_db_fetch_array($invoice_url_query);
            if (isset($invoice_url_result['url']) && strlen($invoice_url_result['url']) > 5) {
                $secupay_invoice_hint = utf8_decode(MODULE_PAYMENT_SPINV_QRCODE_PDF_DESC);

                $qr_code = 'https://api.secupay.ag/qr?d=' . urlencode($invoice_url_result['url']);

                if (isset($qr_code)) {
                        $qr_code_size = getimagesize($qr_code);
                        $pdf->Image($qr_code, $pdf->GetX(), $pdf->GetY(),25, 25, substr(strrchr($qr_code_size['mime'], '/'),1));
                        $pdf->Ln();
                }
				$pdf->SetX($pdf->GetX() + 30);
                $pdf->MultiCell(0, 4, $secupay_invoice_hint);
				$pdf->Ln();
				$pdf->SetX($pdf->GetX() + 30);
                $pdf->MultiCell(0, 4, $invoice_url_result['url']);
            }
        }
    }
}