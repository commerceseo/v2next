<?php

if (isset($order) && $order->info['payment_method'] == 'secupay_inv_xtc') {

    include_once(DIR_FS_CATALOG . 'lang/' . $order->info['language'] . '/modules/payment/secupay_inv_xtc.php');
    include_once ("../" . DIR_WS_CLASSES . 'payment/class.secupay_api.php');
    
    $invoice_hash_query = xtc_db_query("SELECT hash AS hash FROM secupay_transaction_order WHERE ordernr = " . intval($_GET['oID']) . ";");
    $invoice_hash_result = xtc_db_fetch_array($invoice_hash_query);
    if (isset($invoice_hash_result['hash'])) {
        $secupay_capture_url = SECUPAY_URL . $invoice_hash_result['hash'] . '/capture/' . MODULE_PAYMENT_SECUPAY_APIKEY;
        echo '<tr>
                <td class="button">
                <a href=" ' . $secupay_capture_url . '" target="_blank">' . MODULE_PAYMENT_SPINV_CONFIRMATION_URL . '</a>
                </td>
              </tr>';
    }
}
if (isset($order) && $order->info['payment_method'] == 'secupay_ls_xtc') {

    include_once ("../" . DIR_WS_CLASSES . 'payment/class.secupay_api.php');
    
    $debit_hash_query = xtc_db_query("SELECT hash AS hash FROM secupay_transaction_order WHERE ordernr = " . intval($_GET['oID']) . ";");
    $debit_hash_result = xtc_db_fetch_array($debit_hash_query);
    if (isset($debit_hash_result['hash'])) {
        $secupay_capture_url = SECUPAY_URL . $debit_hash_result['hash'] . '/capture/' . MODULE_PAYMENT_SECUPAY_APIKEY;
        echo '<tr>
                <td class="button">
                <a href=" ' . $secupay_capture_url . '" target="_blank">Capture Lastschrift</a>
                </td>
              </tr>';
    }
}
if (isset($order) && $order->info['payment_method'] == 'secupay_kk_xtc') {

    include_once ("../" . DIR_WS_CLASSES . 'payment/class.secupay_api.php');
    
    $debit_hash_query = xtc_db_query("SELECT hash AS hash FROM secupay_transaction_order WHERE ordernr = " . intval($_GET['oID']) . ";");
    $debit_hash_result = xtc_db_fetch_array($debit_hash_query);
    if (isset($debit_hash_result['hash'])) {
        $secupay_capture_url = SECUPAY_URL . $debit_hash_result['hash'] . '/capture/' . MODULE_PAYMENT_SECUPAY_APIKEY;
        echo '<tr>
                <td class="button">
                <a href=" ' . $secupay_capture_url . '" target="_blank">Capture Kreditkarte</a>
                </td>
              </tr>';
    }
}