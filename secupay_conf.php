<?php

define('SECUPAY_FRONTEND_URL', 'https://connect.secupay.ag');
define('SECUPAY_INFO_IFRAME_URL', 'https://connect.secupay.ag/shopiframe.php');
define('SECUPAY_PUSH_LOG', FALSE);

/*
 * configuration for secupay payment modules
 */
if (!function_exists("get_sp_conf")) {

    function get_sp_conf() {
        $conf = '{
            "general": {
                "apikey": "",
                "modulversion": "3.09.5",
                "shop": "xtc"
            },
            "secupay_ls_xtc": {
                "modus": "demo",
                "debug": false,
                "modul": "lastschrift xtc",
                "payment_type": "debit",
                "payment_action": "sale",
                "delivery_differs_disable": true
            },
            "secupay_kk_xtc": {
                "modus": "demo",
                "debug": false,
                "modul": "kreditkarte xtc",
                "payment_type": "creditcard",
                "payment_action": "sale",
                "delivery_differs_disable": false
            },
            "secupay_inv_xtc": {
                "modus": "demo",
                "debug": false,
                "modul": "rechnungskauf xtc",
                "payment_type": "invoice",
                "payment_action": "sale",
                "delivery_differs_disable": true
            }
        }';
        return json_decode($conf);
    }

}   
