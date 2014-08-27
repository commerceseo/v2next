<?php

/**
 * Project: xt:Commerce - eCommerce Engine
 * @version $Id
 *
 * xt:Commerce - Shopsoftware
 * (c) 2003-2007 xt:Commerce (Winger/Zanier), http://www.xt-commerce.com
 *
 * xt:Commerce ist eine geschuetzte Handelsmarke und wird vertreten durch die xt:Commerce GmbH (Austria)
 * xt:Commerce is a protected trademark and represented by the xt:Commerce GmbH (Austria)
 *
 * @copyright Copyright 2003-2007 xt:Commerce (Winger/Zanier), www.xt-commerce.com
 * @copyright based on Copyright 2002-2003 osCommerce; www.oscommerce.com
 * @copyright Porttions Copyright 2003-2007 Zen Cart Development Team
 * @copyright Porttions Copyright 2004 DevosC.com
 * @license http://www.xt-commerce.com.com/license/2_0.txt GNU Public License V2.0
 *
 * For questions, help, comments, discussion, etc., please join the
 * xt:Commerce Support Forums at www.xt-commerce.com
 *
 * ab 15.08.2008 Teile vom Hamburger-Internetdienst geaendert
 * Hamburger-Internetdienst Support Forums at www.forum.hamburger-internetdienst.de
 * Stand: 04.01.2013
 */
require_once(DIR_FS_INC . 'xtc_write_user_info.inc.php');
define('PROXY_HOST', '127.0.0.1');
define('PROXY_PORT', '808');
define('VERSION', PAYPAL_API_VERSION);

class paypal_checkout_ORIGINAL {

    var $API_UserName,
            $API_Password,
            $API_Signature,
            $API_Endpoint,
            $version,
            $location_error,
            $NOTIFY_URL,
            $EXPRESS_CANCEL_URL,
            $EXPRESS_RETURN_URL,
            $CANCEL_URL,
            $RETURN_URL,
            $GIROPAY_SUCCESS_URL,
            $GIROPAY_CANCEL_URL,
            $BANKTXN_PENDING_URL,
            $EXPRESS_URL,
            $GIROPAY_URL,
            $IPN_URL,
            $ppAPIec,
            $payPalURL;

    function paypal_checkout_ORIGINAL() {
        if (PAYPAL_MODE == 'sandbox') {
            $this->API_UserName = PAYPAL_API_SANDBOX_USER;
            $this->API_Password = PAYPAL_API_SANDBOX_PWD;
            $this->API_Signature = PAYPAL_API_SANDBOX_SIGNATURE;
            $this->API_Endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
            $this->EXPRESS_URL = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=';
            $this->GIROPAY_URL = 'https://www.sandbox.paypal.com/webscr?cmd=_complete-express-checkout&token=';
            $this->IPN_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } elseif (PAYPAL_MODE == 'live') {
            $this->API_UserName = PAYPAL_API_USER;
            $this->API_Password = PAYPAL_API_PWD;
            $this->API_Signature = PAYPAL_API_SIGNATURE;
            $this->API_Endpoint = 'https://api-3t.paypal.com/nvp';
            $this->EXPRESS_URL = 'https://www.paypal.com/webscr?cmd=_express-checkout&token=';
            $this->GIROPAY_URL = 'https://www.paypal.com/webscr?cmd=_complete-express-checkout&token=';
            $this->IPN_URL = 'https://www.paypal.com/cgi-bin/webscr';
        }
        if (defined('ENABLE_SSL') && ENABLE_SSL == true) {
            $this->NOTIFY_URL = HTTPS_SERVER . DIR_WS_CATALOG . 'callback/paypal/ipn.php';
            $this->EXPRESS_CANCEL_URL = HTTPS_SERVER . DIR_WS_CATALOG . FILENAME_SHOPPING_CART . '?cSEOid=' . xtc_session_id();
            $this->EXPRESS_RETURN_URL = HTTPS_SERVER . DIR_WS_CATALOG . FILENAME_PAYPAL_CHECKOUT . '?cSEOid=' . xtc_session_id();
            if (CHECKOUT_AJAX_STAT == 'true') {
                $this->PRE_CANCEL_URL = HTTPS_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT . '?cSEOid=' . xtc_session_id();
                $this->CANCEL_URL = HTTPS_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT . '?cSEOid=' . xtc_session_id() . '&error=true&error_message=' . PAYPAL_ERROR;
            } else {
                $this->PRE_CANCEL_URL = HTTPS_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?cSEOid=' . xtc_session_id();
                $this->CANCEL_URL = HTTPS_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?cSEOid=' . xtc_session_id() . '&error=true&error_message=' . PAYPAL_ERROR;
            }
            $this->RETURN_URL = HTTPS_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PROCESS . '?cSEOid=' . xtc_session_id();
            $this->GIROPAY_SUCCESS_URL = HTTPS_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_SUCCESS . '?cSEOid=' . xtc_session_id();
            $this->GIROPAY_CANCEL_URL = HTTPS_SERVER . DIR_WS_CATALOG . FILENAME_SHOPPING_CART . '?cSEOid=' . xtc_session_id();
            $this->BANKTXN_PENDING_URL = HTTPS_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_SUCCESS . '?cSEOid=' . xtc_session_id();
        } else {
            if (CHECKOUT_AJAX_STAT == 'true') {
                $this->CANCEL_URL = HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT . '?cSEOid=' . xtc_session_id() . '&error=true&error_message=' . PAYPAL_ERROR;
                $this->PRE_CANCEL_URL = HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT . '?cSEOid=' . xtc_session_id();
            } else {
                $this->CANCEL_URL = HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?cSEOid=' . xtc_session_id() . '&error=true&error_message=' . PAYPAL_ERROR;
                $this->PRE_CANCEL_URL = HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?cSEOid=' . xtc_session_id();
            }
            $this->NOTIFY_URL = HTTP_SERVER . DIR_WS_CATALOG . 'callback/paypal/ipn.php';
            $this->EXPRESS_CANCEL_URL = HTTP_SERVER . DIR_WS_CATALOG . FILENAME_SHOPPING_CART . '?cSEOid=' . xtc_session_id();
            $this->EXPRESS_RETURN_URL = HTTP_SERVER . DIR_WS_CATALOG . FILENAME_PAYPAL_CHECKOUT . '?cSEOid=' . xtc_session_id();
            $this->RETURN_URL = HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PROCESS . '?cSEOid=' . xtc_session_id();
            $this->GIROPAY_SUCCESS_URL = HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_SUCCESS . '?cSEOid=' . xtc_session_id();
            $this->GIROPAY_CANCEL_URL = HTTP_SERVER . DIR_WS_CATALOG . FILENAME_SHOPPING_CART . '?cSEOid=' . xtc_session_id();
            $this->BANKTXN_PENDING_URL = HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_SUCCESS . '?cSEOid=' . xtc_session_id();
        }
        $this->version = VERSION;
        $this->USE_PROXY = FALSE;
        $this->payPalURL = '';
        $this->ppAPIec = $this->buildAPIKey(PAYPAL_API_KEY, 'ec');
        if (defined('ENABLE_SSL') AND ENABLE_SSL == true) {
            $hdrImg = 'templates/' . CURRENT_TEMPLATE . '/img/' . PAYPAL_API_IMAGE;
            if (file_exists(DIR_FS_CATALOG . $hdrImg) AND PAYPAL_API_IMAGE != '') {
                $hdrSize = getimagesize(DIR_FS_CATALOG . $hdrImg);
                if ($hdrSize[0] <= 750 AND $hdrSize[1] <= 90) {
                    $this->Image = urlencode(HTTPS_SERVER . DIR_WS_CATALOG . $hdrImg);
                }
            }
        }
        if (preg_match('/^(([a-f]|[A-F]|[0-9]){6})$/', PAYPAL_API_CO_BACK)) {
            $this->BackColor = PAYPAL_API_CO_BACK;
        }
        if (preg_match('/^(([a-f]|[A-F]|[0-9]){6})$/', PAYPAL_API_CO_BORD)) {
            $this->BorderColor = PAYPAL_API_CO_BORD;
        }
    }

    function build_express_checkout_button() {
        global $PHP_SELF;
        if ($_SESSION['allow_checkout'] == 'true' AND $_SESSION['cart']->show_total() > 0 AND MODULE_PAYMENT_PAYPALEXPRESS_STATUS == 'True') {
            $unallowed_modules = explode(',', $_SESSION['customers_status']['customers_status_payment_unallowed']);
            if (!in_array('paypalexpress', $unallowed_modules)) {
                include(DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/payment/paypalexpress.php');
                $alt = ((defined('MODULE_PAYMENT_PAYPALEXPRESS_ALT_BUTTON')) ? MODULE_PAYMENT_PAYPALEXPRESS_ALT_BUTTON : 'PayPal');
                $source = ((strtoupper($_SESSION['language_code']) == 'DE') ? 'epaypal_de.gif' : 'epaypal_en.gif');
                $button .= '<a style="cursor:pointer;" onfocus="if(this.blur) this.blur();" onmouseover="window.status = ' . "''" . '; return true;" href="' . xtc_href_link(basename($_SERVER['SCRIPT_NAME']), xtc_get_all_get_params(array('action')) . 'action=paypal_express_checkout') . '"><img src="' . DIR_WS_ICONS . $source . '" alt="' . $alt . '" title="' . $alt . '" /></a>';
                return $button;
            }
        }
        return;
    }

    function build_express_fehler_button() {
        if (MODULE_PAYMENT_PAYPALEXPRESS_STATUS == 'True') {
            include(DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/payment/paypalexpress.php');
            $alt = ((defined('MODULE_PAYMENT_PAYPALEXPRESS_ALT_BUTTON')) ? MODULE_PAYMENT_PAYPALEXPRESS_ALT_BUTTON : 'PayPal');
            $source = ((strtoupper($_SESSION['language_code']) == 'DE') ? 'epaypal_de.gif' : 'epaypal_en.gif');
            $button .= '<a style="cursor:pointer;" onfocus="if(this.blur) this.blur();" onmouseover="window.status = ' . "''" . '; return true;" href="' . $this->EXPRESS_CANCEL_URL . '"><img src="' . DIR_WS_ICONS . $source . '" alt="' . $alt . '" title="' . $alt . '" /></a>';
            return $button;
        }
        return;
    }

    function paypal_auth_call() {
        // aufruf aus paypal.php NICHT fuer PP Express aus Warenkorb
        global $xtPrice, $order;

        unset($_SESSION['reshash']);
        unset($_SESSION['nvpReqArray']);
        require(DIR_WS_CLASSES . 'order_total.php');
        $order_total_modules = new order_total();
        $order_totals = $order_total_modules->process();
        $order_tax = 0;
        $order_discount = 0;
        $order_fee = 0;
        $order_gs = 0;
        $order_shipping = 0;
        for ($i = 0, $n = sizeof($order_totals); $i < $n; $i++) {
            switch ($order_totals[$i]['code']) {
                case 'ot_total':
                    $paymentAmount = $order_totals[$i]['value'];
                    break;
                case 'ot_shipping':
                    $order_shipping = $order_totals[$i]['value'];
                    break;
                case 'ot_tax':
                    $order_tax+=$order_totals[$i]['value'];
                    break;
                case 'ot_discount':
                    $order_discount+=$order_totals[$i]['value'];
                    break;
                case 'ot_coupon':
                    $order_gs+=($order_totals[$i]['value'] < 0) ? $order_totals[$i]['value'] : $order_totals[$i]['value'] *(-1);
                    break;
                case 'ot_gv':
                     $order_gs+=($order_totals[$i]['value'] < 0) ? $order_totals[$i]['value'] : $order_totals[$i]['value'] *(-1);
                    break;
                case 'ot_loyalty_discount':
                    $order_gs+=($order_totals[$i]['value'] < 0) ? $order_totals[$i]['value'] : $order_totals[$i]['value'] *(-1);
                    break;
                case 'ot_bonus_fee':
                    $order_gs+=($order_totals[$i]['value'] < 0) ? $order_totals[$i]['value'] : $order_totals[$i]['value'] *(-1);
                    break;
                case 'ot_payment':
                    if ($order_totals[$i]['value'] < 0) {
                        $order_discount+=$order_totals[$i]['value'];
                    } else {
                        $order_fee+=$order_totals[$i]['value'];
                    }
                    break;
                case 'ot_cod_fee':
                    $order_fee+=$order_totals[$i]['value'];
                    break;
                case 'ot_ps_fee':
                    $order_fee+=$order_totals[$i]['value'];
                    break;
                case 'ot_loworderfee':
                    $order_fee+=$order_totals[$i]['value'];
            }
        }

        $paymentAmount = round($paymentAmount, $xtPrice->get_decimal_places($order->info['currency']));

        $order_tax = round($order_tax, $xtPrice->get_decimal_places($order->info['currency']));
        $order_discount = round($order_discount, $xtPrice->get_decimal_places($order->info['currency']));
        $order_gs = round($order_gs, $xtPrice->get_decimal_places($order->info['currency']));
        $order_fee = round($order_fee, $xtPrice->get_decimal_places($order->info['currency']));
        $order_shipping = round($order_shipping, $xtPrice->get_decimal_places($order->info['currency']));
        $nvp_products = $this->paypal_get_products($paymentAmount, $order_tax, $order_discount, $order_fee, $order_shipping, $order_gs);
        $paymentAmount = urlencode(number_format($paymentAmount, $xtPrice->get_decimal_places($order->info['currency']), '.', ','));
        $currencyCodeType = urlencode($order->info['currency']);
        // Payment Type
        $paymentType = PAYPAL_PAYMENT_MODE;
        // The returnURL is the location where buyers return when a
        // payment has been succesfully authorized.
        $returnURL = urlencode($this->RETURN_URL);
        $cancelURL = urlencode($this->CANCEL_URL);
        $gpsucssesURL = urlencode($this->GIROPAY_SUCCESS_URL);
        $gpcancelURL = urlencode($this->GIROPAY_CANCEL_URL);
        $bankpending = urlencode($this->BANKTXN_PENDING_URL);

        $sh_name = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['firstname'] . ' ' . $order->delivery['lastname']));
        $sh_street = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['street_address']));
        $sh_street_2 = '';
        $sh_city = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['city']));
        $sh_zip = urlencode($order->delivery['postcode']);
        $sh_state = urlencode($this->state_code($order->delivery['state']));
        $sh_countrycode = urlencode($order->delivery['country']['iso_code_2']);
        $sh_countryname = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['country']['title']));
        $sh_phonenum = urlencode($order->customer['telephone']);

        $nvpstr = "&AMT=" . $paymentAmount .
                "&CURRENCYCODE=" . $currencyCodeType .
                "&PAYMENTACTION=" . $paymentType .
                "&LOCALECODE=" . $_SESSION['language_code'] .
                "&RETURNURL=" . $returnURL .
                "&CANCELURL=" . $cancelURL .
                "&GIROPAYSUCCESSURL=" . $gpsucssesURL .
                "&GIROPAYCANCELURL=" . $gpcancelURL .
                "&BANKTXNPENDINGURL=" . $bankpending .
                "&HDRIMG=" . $this->Image .
                "&HDRBORDERCOLOR=" . $this->BorderColor .
                "&HDRBACKCOLOR=" . $this->BackColor .
                "&CUSTOM=" . '' .
                "&SHIPTONAME=" . $sh_name .
                "&SHIPTOSTREET=" . $sh_street .
                "&SHIPTOSTREET2=" . $sh_street2 .
                "&SHIPTOCITY=" . $sh_city .
                "&SHIPTOZIP=" . $sh_zip .
                "&SHIPTOSTATE=" . $sh_state .
                "&SHIPTOCOUNTRYCODE=" . $sh_countrycode .
                "&SHIPTOCOUNTRYNAME=" . $sh_countryname .
                "&PHONENUM=" . $sh_phonenum .
                "&ALLOWNOTE=0" .
                "&ADDROVERRIDE=1";

        $nvpstr.=$nvp_products;
        $resArray = $this->hash_call("SetExpressCheckout", $nvpstr);
        $_SESSION['reshash'] = $resArray;
        $ack = strtoupper($resArray["ACK"]);
        if ($ack != "SUCCESS") {
            if (PAYPAL_ERROR_DEBUG == 'true') {
                $this->build_error_message($_SESSION['reshash']);
            } else {
                $_SESSION['reshash']['FORMATED_ERRORS'] = PAYPAL_NOT_AVIABLE;
            }
            xtc_redirect($this->PRE_CANCEL_URL);
        }
        if ($ack == "SUCCESS") {
            $token = urldecode($resArray["TOKEN"]);
            $this->payPalURL = $this->EXPRESS_URL . '' . $token;
            return $this->payPalURL;
        }
    }

    function paypal_express_auth_call() {
        // aufruf aus cart_actions.php
        // 1. Call um die Token ID zu bekommen
        global $xtPrice, $order;
        unset($_SESSION['reshash']);
        unset($_SESSION['nvpReqArray']);

        if (!isset($_SESSION['sendto'])) {
            $_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
        } else {
            $check_address = xtc_db_fetch_array(xtc_db_query("SELECT count(*) AS total FROM " . TABLE_ADDRESS_BOOK . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND address_book_id = '" . (int) $_SESSION['sendto'] . "';"));
            if ($check_address['total'] != '1') {
                $_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
            }
        }
        if (isset($_SESSION['shipping'])) {
            unset($_SESSION['shipping']);
        }
        require_once(DIR_WS_CLASSES . 'class.order.php');
        $order = new order();
        require_once(DIR_WS_CLASSES . 'class.order_total.php');
        $order_total_modules = new order_total();
        $order_totals = $order_total_modules->process();
        $order_tax = 0;
        $order_discount = 0;
        $order_gs = 0;
        $order_fee = 0;
        $order_shipping = 0;
        for ($i = 0, $n = sizeof($order_totals); $i < $n; $i++) {
            switch ($order_totals[$i]['code']) {
                case 'ot_discount':
                    $order_discount+=$order_totals[$i]['value'];
                    break;
                case 'ot_coupon':
                    $order_gs+=($order_totals[$i]['value'] < 0) ? $order_totals[$i]['value'] : $order_totals[$i]['value'] *(-1);
                    break;
                case 'ot_gv':
                    $order_gs+=($order_totals[$i]['value'] < 0) ? $order_totals[$i]['value'] : $order_totals[$i]['value'] *(-1);
                    break;
                case 'ot_bonus_fee':
                    $order_gs+=($order_totals[$i]['value'] < 0) ? $order_totals[$i]['value'] : $order_totals[$i]['value'] *(-1);
                    break;
                case 'ot_loyalty_discount':
                    $order_gs+=($order_totals[$i]['value'] < 0) ? $order_totals[$i]['value'] : $order_totals[$i]['value'] *(-1);
                    break;
                case 'ot_payment':
                    if ($order_totals[$i]['value'] < 0) {
                        $order_discount+=$order_totals[$i]['value'];
                    } else {
                        $order_fee+=$order_totals[$i]['value'];
                    }
                    break;
                case 'ot_cod_fee':
                    $order_fee+=$order_totals[$i]['value'];
                    break;
                case 'ot_ps_fee':
                    $order_fee+=$order_totals[$i]['value'];
                    break;
                case 'ot_loworderfee':
                    $order_fee+=$order_totals[$i]['value'];
            }
        }

        $paymentAmount = $_SESSION['cart']->show_total() + $order_discount + $order_gs + $order_fee;
        // Durch Kupon oder irgendwas auf unter 0 -> Kein PP Express sinnvoll
        if ($paymentAmount <= 0) {
            $_SESSION['reshash']['FORMATED_ERRORS'] = PAYPAL_AMMOUNT_NULL;
            $this->payPalURL = $this->EXPRESS_CANCEL_URL;
            return $this->payPalURL;
        }
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $order_tax = $_SESSION['cart']->show_tax(false);
        }
        // Vorlaeufige Versandkosten
        if (PAYPAL_EXP_VORL != '' AND PAYPAL_EXP_VERS != 0) {
            $paymentAmount+=PAYPAL_EXP_VERS;
        }

        $paymentAmount = round($paymentAmount, $xtPrice->get_decimal_places($order->info['currency']));
        $order_tax = round($order_tax, $xtPrice->get_decimal_places($order->info['currency']));
        $order_discount = round($order_discount, $xtPrice->get_decimal_places($order->info['currency']));
        $order_gs = round($order_gs, $xtPrice->get_decimal_places($order->info['currency']));
        $order_fee = round($order_fee, $xtPrice->get_decimal_places($order->info['currency']));
        $nvp_products = $this->paypal_get_products($paymentAmount, $order_tax, $order_discount, $order_fee, $order_shipping, $order_gs, True);
        $paymentAmount = urlencode(number_format($paymentAmount, $xtPrice->get_decimal_places($order->info['currency']), '.', ','));
        $currencyCodeType = urlencode($order->info['currency']);
        $paymentType = PAYPAL_PAYMENT_MODE;
        $returnURL = urlencode($this->EXPRESS_RETURN_URL);
        $cancelURL = urlencode($this->EXPRESS_CANCEL_URL);
        $gpsucssesURL = urlencode($this->GIROPAY_SUCCESS_URL);
        $gpcancelURL = urlencode($this->EXPRESS_CANCEL_URL);
        $bankpending = urlencode($this->BANKTXN_PENDING_URL);
        if (isset($_SESSION['sendto']) AND isset($_SESSION['customer_id'])) {
            // User eingeloggt
            $sh_name = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['firstname'] . ' ' . $order->delivery['lastname']));
            $sh_street = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['street_address']));
            $sh_street_2 = '';
            $sh_city = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['city']));
            $sh_zip = urlencode($order->delivery['postcode']);
            $sh_state = urlencode($this->state_code($order->delivery['state']));
            $sh_countrycode = urlencode($order->delivery['country']['iso_code_2']);
            $sh_countryname = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['country']['title']));
            $sh_phonenum = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->customer['telephone']));
            if ($_SESSION['paypal_express_new_customer'] != 'true') {
                $address = "&SHIPTONAME=" . $sh_name . "&SHIPTOSTREET=" . $sh_street . "&SHIPTOSTREET2=" . $sh_street2 . "&SHIPTOCITY=" . $sh_city . "&SHIPTOZIP=" . $sh_zip . "&SHIPTOSTATE=" . $sh_state . "&SHIPTOCOUNTRYCODE=" . $sh_countrycode . "&SHIPTOCOUNTRYNAME=" . $sh_countryname . "&PHONENUM=" . $sh_phonenum;
            }
        }
        $nvpstr = "&AMT=" . $paymentAmount .
                "&CURRENCYCODE=" . $currencyCodeType .
                "&PAYMENTACTION=" . $paymentType .
                "&LOCALECODE=" . $_SESSION['language_code'] .
                "&RETURNURL=" . $returnURL .
                "&CANCELURL=" . $cancelURL .
                "&GIROPAYSUCCESSURL=" . $gpsucssesURL .
                "&GIROPAYCANCELURL=" . $gpcancelURL .
                "&BANKTXNPENDINGURL=" . $bankpending .
                "&HDRIMG=" . $this->Image .
                "&HDRBORDERCOLOR=" . $this->BorderColor .
                "&HDRBACKCOLOR=" . $this->BackColor .
                "&CUSTOM=" . '' .
                $address .
                "&ALLOWNOTE=0" .
                "&ADDROVERRIDE=0";
        $nvpstr.=$nvp_products;
        $resArray = $this->hash_call("SetExpressCheckout", $nvpstr);
        $_SESSION['reshash'] = $resArray;
        $ack = strtoupper($resArray["ACK"]);
        if ($ack == "SUCCESS") {
            $token = urldecode($resArray["TOKEN"]);
            $this->payPalURL = $this->EXPRESS_URL . '' . $token;
            return $this->payPalURL;
        } else {
            if (PAYPAL_ERROR_DEBUG == 'true') {
                $this->build_error_message($_SESSION['reshash']);
            } else {
                $_SESSION['reshash']['FORMATED_ERRORS'] = PAYPAL_NOT_AVIABLE;
            }
            $this->payPalURL = $this->EXPRESS_CANCEL_URL;
            return $this->payPalURL;
        }
    }

    function paypal_second_auth_call($insert_id) {
        global $xtPrice, $order;
        unset($_SESSION['reshash']);
        unset($_SESSION['nvpReqArray']);
        require(DIR_WS_CLASSES . 'class.order.php');
        $order = new order($insert_id);
        $paymentAmount = round($order->info['pp_total'], $xtPrice->get_decimal_places($order->info['currency']));
        $order_tax = round($order->info['pp_tax'], $xtPrice->get_decimal_places($order->info['currency']));
        $order_discount = round($order->info['pp_disc'], $xtPrice->get_decimal_places($order->info['currency']));
        $order_gs = round($order->info['pp_gs'], $xtPrice->get_decimal_places($order->info['currency']));
        $order_fee = round($order->info['pp_fee'], $xtPrice->get_decimal_places($order->info['currency']));
        $order_shipping = round($order->info['pp_shipping'], $xtPrice->get_decimal_places($order->info['currency']));
        $nvp_products = $this->paypal_get_products($paymentAmount, $order_tax, $order_discount, $order_fee, $order_shipping, $order_gs);
        $paymentAmount = urlencode(number_format($paymentAmount, $xtPrice->get_decimal_places($order->info['currency']), '.', ','));
        $currencyCodeType = urlencode($order->info['currency']);
        $paymentType = PAYPAL_PAYMENT_MODE;
        $returnURL = urlencode($this->EXPRESS_CANCEL_URL);
        $cancelURL = urlencode($this->EXPRESS_CANCEL_URL);
        $gpsucssesURL = urlencode($this->GIROPAY_SUCCESS_URL);
        $gpcancelURL = urlencode($this->EXPRESS_CANCEL_URL);
        $bankpending = urlencode($this->BANKTXN_PENDING_URL);
        $notify_url = urlencode($this->NOTIFY_URL);
        $inv_num = urlencode(PAYPAL_INVOICE . $insert_id);
        $sh_name = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['firstname'] . ' ' . $order->delivery['lastname']));
        $sh_street = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['street_address']));
        $sh_street_2 = '';
        $sh_city = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['city']));
        $sh_state = urlencode($this->state_code($order->delivery['state']));
        if (is_array($order->delivery['country'])) {
            $sh_countrycode = urlencode($order->delivery['country']['iso_code_2']);
            $sh_countryname = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['country']['title']));
        } else {
            $sh_countrycode = urlencode($order->delivery['country_iso_2']);
            $sh_countryname = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['country']));
        }
        $sh_phonenum = urlencode($order->customer['telephone']);
        $sh_zip = urlencode($order->delivery['postcode']);
        $address = "&SHIPTONAME=" . $sh_name . "&SHIPTOSTREET=" . $sh_street . "&SHIPTOSTREET2=" . $sh_street2 . "&SHIPTOCITY=" . $sh_city . "&SHIPTOZIP=" . $sh_zip . "&SHIPTOSTATE=" . $sh_state . "&SHIPTOCOUNTRYCODE=" . $sh_countrycode . "&SHIPTOCOUNTRYNAME=" . $sh_countryname . "&PHONENUM=" . $sh_phonenum;

        $nvpstr = "&AMT=" . $paymentAmount .
                "&CURRENCYCODE=" . $currencyCodeType .
                "&PAYMENTACTION=" . $paymentType .
                "&NOTIFYURL=" . $notify_url .
                "&INVNUM=" . $inv_num . $adress .
                "&LOCALECODE=" . $_SESSION['language_code'] .
                "&RETURNURL=" . $returnURL .
                "&CANCELURL=" . $cancelURL .
                "&GIROPAYSUCCESSURL=" . $gpsucssesURL .
                "&GIROPAYCANCELURL=" . $gpcancelURL .
                "&BANKTXNPENDINGURL=" . $bankpending .
                "&HDRIMG=" . $this->Image .
                "&HDRBORDERCOLOR=" . $this->BorderColor .
                "&HDRBACKCOLOR=" . $this->BackColor .
                "&CUSTOM=" . '' .
                $address .
                "&ALLOWNOTE=0" .
                "&ADDROVERRIDE=1";
        $nvpstr.=$nvp_products;
        $resArray = $this->hash_call("SetExpressCheckout", $nvpstr);
        $_SESSION['reshash'] = $resArray;
        $ack = strtoupper($resArray["ACK"]);
        if ($ack == "SUCCESS") {
            $token = urldecode($resArray["TOKEN"]);
            $this->payPalURL = $this->EXPRESS_URL . '' . $token;
            return $this->payPalURL;
        } else {
            $this->build_error_message($_SESSION['reshash']);
            if (PAYPAL_ERROR_DEBUG == 'true') {
                $_SESSION['reshash']['FORMATED_ERRORS'] = PAYPAL_NOT_AVIABLE;
            } else {
                $this->payPalURL = $this->EXPRESS_CANCEL_URL;
            }
            return $this->payPalURL;
        }
    }

    function complete_ceckout($insert_id, $data = '') {

        global $xtPrice, $order;
        $order = new order($insert_id);

        if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
            $customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $customers_ip = $_SERVER["REMOTE_ADDR"];
        }
        $paymentAmount = round($order->info['pp_total'], $xtPrice->get_decimal_places($order->info['currency']));
        $order_tax = round($order->info['pp_tax'], $xtPrice->get_decimal_places($order->info['currency']));
        $order_discount = round($order->info['pp_disc'], $xtPrice->get_decimal_places($order->info['currency']));
        $order_gs = round($order->info['pp_gs'], $xtPrice->get_decimal_places($order->info['currency']));
        $order_fee = round($order->info['pp_fee'], $xtPrice->get_decimal_places($order->info['currency']));
        $order_shipping = round($order->info['pp_shipping'], $xtPrice->get_decimal_places($order->info['currency']));
        $nvp_products = $this->paypal_get_products($paymentAmount, $order_tax, $order_discount, $order_fee, $order_shipping, $order_gs);
        $paymentAmount = urlencode(number_format($paymentAmount, $xtPrice->get_decimal_places($order->info['currency']), '.', ','));
        $currencyCodeType = urlencode($order->info['currency']);
        $tkn = (($data['token'] != '') ? $data['token'] : $_SESSION['nvpReqArray']['TOKEN']);
        $payer = (($data['PayerID'] != '') ? $data['PayerID'] : $_SESSION['reshash']['PAYERID']);
        $token = urlencode($tkn);
        $payerID = urlencode($payer);
        $paymentType = PAYPAL_PAYMENT_MODE;
        $notify_url = urlencode($this->NOTIFY_URL);
        $inv_num = urlencode(PAYPAL_INVOICE . $insert_id);
        $button_source = urlencode($this->ppAPIec);
        $sh_name = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['firstname'] . ' ' . $order->delivery['lastname']));
        $sh_street = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['street_address']));
        $sh_street_2 = '';
        $sh_city = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['city']));
        $sh_state = urlencode($this->state_code($order->delivery['state']));
        if (is_array($order->delivery['country'])) {
            $sh_countrycode = urlencode($order->delivery['country']['iso_code_2']);
            $sh_countryname = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['country']['title']));
        } else {
            $sh_countrycode = urlencode($order->delivery['country_iso_2']);
            $sh_countryname = urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", $order->delivery['country']));
        }
        $sh_phonenum = urlencode($order->customer['telephone']);
        $sh_zip = urlencode($order->delivery['postcode']);
        $address = "&SHIPTONAME=" . $sh_name . "&SHIPTOSTREET=" . $sh_street . "&SHIPTOSTREET2=" . $sh_street2 . "&SHIPTOCITY=" . $sh_city . "&SHIPTOZIP=" . $sh_zip . "&SHIPTOSTATE=" . $sh_state . "&SHIPTOCOUNTRYCODE=" . $sh_countrycode . "&SHIPTOCOUNTRYNAME=" . $sh_countryname . "&PHONENUM=" . $sh_phonenum;

        $nvpstr = '&TOKEN=' . $token .
                '&PAYERID=' . $payerID .
                '&PAYMENTACTION=' . $paymentType .
                '&AMT=' . $paymentAmount .
                '&CURRENCYCODE=' . $currencyCodeType .
                '&IPADDRESS=' . $customers_ip .
                '&NOTIFYURL=' . $notify_url .
                '&INVNUM=' . $inv_num . $adress .
                '&BUTTONSOURCE=' . $button_source .
                $address;

        $nvpstr.=$nvp_products;
        $resArray = $this->hash_call("DoExpressCheckoutPayment", $nvpstr);
        $_SESSION['reshash'] = array_merge($_SESSION['reshash'], $resArray);
        $ack = strtoupper($resArray["ACK"]);
        if ($ack != "SUCCESS" && $ack != "SUCCESSWITHWARNING") {
            $this->build_error_message($_SESSION['reshash'], 'DoEx');
        }
    }

    function paypal_get_customer_data() {
        $nvpstr = "&TOKEN=" . $_SESSION['reshash']['TOKEN'];
        $resArray = $this->hash_call("GetExpressCheckoutDetails", $nvpstr);
        $_SESSION['reshash'] = array_merge($_SESSION['reshash'], $resArray);
        $ack = strtoupper($resArray["ACK"]);
        if ($ack == "SUCCESS") {
            $_SESSION['paypal_express_checkout'] = true;
            $_SESSION['paypal_express_payment_modules'] = 'paypalexpress.php';
            if (!$this->check_customer()) {
                $_SESSION['reshash']['FORMATED_ERRORS'] = PAYPAL_ADRESSE . $_SESSION['reshash']['SHIPTOCOUNTRYCODE'];
                xtc_redirect($this->EXPRESS_CANCEL_URL);
            }
        } else {
            $this->build_error_message($_SESSION['reshash']);
            $this->payPalURL = $this->EXPRESS_CANCEL_URL;
            return $this->payPalURL;
        }
    }

    function check_customer() {
        if ($_SESSION['reshash']['SHIPTOCOUNTRYCODE']) {
            $tmp_country = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_COUNTRIES . " WHERE countries_iso_code_2 = '" . xtc_db_input($_SESSION['reshash']['SHIPTOCOUNTRYCODE']) . "';"));
            if ($tmp_country['status'] != 1) {
                return false;
            }
        }
        if (!isset($_SESSION['customer_id'])) {
            $check_customer_query = xtc_db_query("select * from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($_SESSION['reshash']['EMAIL']) . "' and account_type = '0'");
            if (!xtc_db_num_rows($check_customer_query)) {
                $this->create_account();
            } else {
                $check_customer = xtc_db_fetch_array($check_customer_query);
                $this->login_customer($check_customer);
                if (PAYPAL_EXPRESS_ADDRESS_OVERRIDE == 'true' && $_SESSION['pp_allow_address_change'] != 'true')
                    $this->create_shipping_address();
            }
        } else {
            if (PAYPAL_EXPRESS_ADDRESS_OVERRIDE == 'true' && $_SESSION['pp_allow_address_change'] != 'true') {
                $check_customer_query = xtc_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = '" . xtc_db_input($_SESSION['customer_id']) . "' and account_type = '0'");
                $check_customer = xtc_db_fetch_array($check_customer_query);
                $this->create_shipping_address();
            }
        }
        return true;
    }

    function create_account() {
        global $xtPrice;
        $firstname = xtc_db_prepare_input($this->UTF8decode($_SESSION['reshash']['FIRSTNAME']));
        $lastname = xtc_db_prepare_input($this->UTF8decode($_SESSION['reshash']['LASTNAME']));
        $email_address = xtc_db_prepare_input($_SESSION['reshash']['EMAIL']);
        $company = xtc_db_prepare_input($this->UTF8decode($_SESSION['reshash']['BUSINESS']));
        $street_address = xtc_db_prepare_input($this->UTF8decode($_SESSION['reshash']['SHIPTOSTREET'] . $_SESSION['reshash']['SHIPTOSTREET_2']));
        $postcode = xtc_db_prepare_input($_SESSION['reshash']['SHIPTOZIP']);
        $city = xtc_db_prepare_input($this->UTF8decode($_SESSION['reshash']['SHIPTOCITY']));
        $state = xtc_db_prepare_input($_SESSION['reshash']['SHIPTOSTATE']);
        $telephone = xtc_db_prepare_input($_SESSION['reshash']['PHONENUM']);
        $country_query = xtc_db_query("select * from " . TABLE_COUNTRIES . " where countries_iso_code_2 = '" . xtc_db_input($_SESSION['reshash']['SHIPTOCOUNTRYCODE']) . "' ");
        $tmp_country = xtc_db_fetch_array($country_query);
        $country = xtc_db_prepare_input($tmp_country['countries_id']);
        $customers_status = DEFAULT_CUSTOMERS_STATUS_ID;
        $sql_data_array = array('customers_status' => $customers_status,
            'customers_firstname' => $firstname,
            'customers_lastname' => $lastname,
            'customers_email_address' => $email_address,
            'customers_telephone' => $telephone,
            'customers_date_added' => 'now()',
            'customers_last_modified' => 'now()');

        xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array);

        $_SESSION['paypal_express_new_customer'] = 'true';
        $_SESSION['customer_id'] = xtc_db_insert_id();
        $user_id = xtc_db_insert_id();
        xtc_write_user_info($user_id);

        if (CUSTOMER_CID_FORM == 'date') {
            $new_cid = '';
            $day = date("d");
            $mon = date("m");
            $year = date("y");
            $ccid = $day . $mon . $year . '-' . ($_SESSION['customer_id'] + 1000);
        } elseif (CUSTOMER_CID_FORM == 'custom' && CUSTOMER_CID_FORM_CUSTOM != '') {
            $ccid = CUSTOMER_CID_FORM_CUSTOM . '-' . $_SESSION['customer_id'];
        } else {
            $ccid = $_SESSION['customer_id'];
        }

        $sql_data_array = array('customers_id' => $_SESSION['customer_id'],
            'entry_firstname' => $firstname,
            'entry_lastname' => $lastname,
            'entry_street_address' => $street_address,
            'entry_postcode' => $postcode,
            'entry_city' => $city,
            'entry_country_id' => $country,
            'entry_company' => $company,
            'entry_zone_id' => '0',
            'entry_state' => $state,
            'address_date_added' => 'now()',
            'address_last_modified' => 'now()');
        xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
        $address_id = xtc_db_insert_id();
        $_SESSION['sendto'] = $address_id;
        xtc_db_query("UPDATE " . TABLE_CUSTOMERS . " SET customers_default_address_id = '" . $address_id . "' WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "'");
        xtc_db_query("UPDATE " . TABLE_CUSTOMERS . " SET customers_cid = '" . $ccid . "' WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "'");
        xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) VALUES ('" . (int) $_SESSION['customer_id'] . "', '0', now())");
        if (isset($_SESSION['tracking']['refID'])) {
            $rows = xtc_db_query("SHOW COLUMNS FROM " . TABLE_CUSTOMERS);
            $feld_ist_original = 0;
            while ($row = xtc_db_fetch_array($rows)) {
                if ($row['Field'] == 'refferers_id') {
                    if (substr($row['Type'], 0, 3) == 'int') {
                        $feld_ist_original = 1;
                    }
                }
            }
            if ($feld_ist_original == 1) {
                $campaign_check_query_raw = "SELECT * FROM " . TABLE_CAMPAIGNS . " WHERE campaigns_refID = '" . $_SESSION[tracking][refID] . "'";
                $campaign_check_query = xtc_db_query($campaign_check_query_raw);
                if (xtc_db_num_rows($campaign_check_query) > 0) {
                    $campaign = xtc_db_fetch_array($campaign_check_query);
                    $refID = $campaign['campaigns_id'];
                } else {
                    $refID = 0;
                }
                xtc_db_query("UPDATE " . TABLE_CUSTOMERS . " SET refferers_id = '" . $refID . "' WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "'");
                $leads = $campaign['campaigns_leads'] + 1;
                xtc_db_query("UPDATE " . TABLE_CAMPAIGNS . " SET campaigns_leads = '" . $leads . "' WHERE campaigns_id = '" . $refID . "'");
            } else {
                xtc_db_query("UPDATE " . TABLE_CUSTOMERS . " SET refferers_id = '" . $_SESSION['tracking']['refID'] . "' WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "'");
            }
        }
        if (ACTIVATE_GIFT_SYSTEM == 'true') {
            // GV Code Start
            if (NEW_SIGNUP_GIFT_VOUCHER_AMOUNT > 0) {
                $coupon_code = create_coupon_code();
                $insert_query = xtc_db_query("INSERT INTO " . TABLE_COUPONS . " (coupon_code, coupon_type, coupon_amount, date_created) VALUES ('" . $coupon_code . "', 'G', '" . NEW_SIGNUP_GIFT_VOUCHER_AMOUNT . "', now())");
                $insert_id = xtc_db_insert_id($insert_query);
                $insert_query = xtc_db_query("INSERT INTO " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) VALUES ('" . $insert_id . "', '0', 'Admin', '" . $email_address . "', now() )");
                $_SESSION['reshash']['SEND_GIFT'] = 'true';
                $_SESSION['reshash']['GIFT_AMMOUNT'] = $xtPrice->xtcFormat(NEW_SIGNUP_GIFT_VOUCHER_AMOUNT, true);
                $_SESSION['reshash']['GIFT_CODE'] = $coupon_code;
                $_SESSION['reshash']['GIFT_LINK'] = xtc_href_link(FILENAME_GV_REDEEM, 'gv_no=' . $coupon_code, 'NONSSL', false);
            }
            if (NEW_SIGNUP_DISCOUNT_COUPON != '') {
                $coupon_code = NEW_SIGNUP_DISCOUNT_COUPON;
                $coupon_query = xtc_db_query("SELECT * FROM " . TABLE_COUPONS . " WHERE coupon_code = '" . $coupon_code . "'");
                $coupon = xtc_db_fetch_array($coupon_query);
                $coupon_id = $coupon['coupon_id'];
                $coupon_desc_query = xtc_db_query("SELECT * FROM " . TABLE_COUPONS_DESCRIPTION . " WHERE coupon_id = '" . $coupon_id . "' AND language_id = '" . (int) $_SESSION['language_id'] . "'");
                $coupon_desc = xtc_db_fetch_array($coupon_desc_query);
                $insert_query = xtc_db_query("INSERT INTO " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) VALUES ('" . $coupon_id . "', '0', 'Admin', '" . $email_address . "', now() )");
                $_SESSION['reshash']['SEND_COUPON'] = 'true';
                $_SESSION['reshash']['COUPON_DESC'] = $coupon_desc['coupon_description'];
                $_SESSION['reshash']['COUPON_CODE'] = $coupon['coupon_code'];
            }
            // GV Code End
        }
        $_SESSION['ACCOUNT_PASSWORD'] = 'true';
        $check_customer = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_CUSTOMERS . " WHERE customers_email_address = '" . xtc_db_input($email_address) . "' AND account_type = '0'"));
        $this->login_customer($check_customer);
        if (PAYPAL_EXPRESS_ADDRESS_OVERRIDE == 'true') {
            if ($firstname . ' ' . $lastname != $this->UTF8decode($_SESSION['reshash']['SHIPTONAME'])) {
                $this->create_shipping_address();
            }
        }
    }

    function login_customer($check_customer) {
        global $main, $xtPrice, $econda;
        if (SESSION_RECREATE == 'true') {
            xtc_session_recreate();
        }
        $check_country = xtc_db_fetch_array(xtc_db_query("SELECT entry_country_id, entry_zone_id from " . TABLE_ADDRESS_BOOK . " WHERE customers_id = '" . (int) $check_customer['customers_id'] . "' AND address_book_id = '" . $check_customer['customers_default_address_id'] . "'"));
        $_SESSION['customer_gender'] = $check_customer['customers_gender'];
        $_SESSION['customer_first_name'] = $check_customer['customers_firstname'];
        $_SESSION['customer_last_name'] = $check_customer['customers_lastname'];
        $_SESSION['customer_id'] = $check_customer['customers_id'];
        $_SESSION['customer_vat_id'] = $check_customer['customers_vat_id'];
        $_SESSION['customer_default_address_id'] = $check_customer['customers_default_address_id'];
        $_SESSION['customer_country_id'] = $check_country['entry_country_id'];
        $_SESSION['customer_zone_id'] = $check_country['entry_zone_id'];
        $_SESSION['customer_email_address'] = $check_customer['customers_email_address'];
        $date_now = date('Ymd');
        xtc_db_query("UPDATE " . TABLE_CUSTOMERS_INFO . " SET customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 WHERE customers_info_id = '" . (int) $_SESSION['customer_id'] . "'");
        xtc_write_user_info((int) $_SESSION['customer_id']);
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "'");
        xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "'");
        $_SESSION['cart']->restore_contents();
        require(DIR_WS_INCLUDES . 'write_customers_status.php');
        $xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
    }

    function create_shipping_address() {
        if (!$_SESSION['reshash']['SHIPTOCITY'])
            return;
        $pos = strrpos($_SESSION['reshash']['SHIPTONAME'], ' ');
        $lenght = strlen($_SESSION['reshash']['SHIPTONAME']);
        $firstname = $this->UTF8decode(substr($_SESSION['reshash']['SHIPTONAME'], 0, $pos));
        $lastname = $this->UTF8decode(substr($_SESSION['reshash']['SHIPTONAME'], ($pos + 1), $lenght));
        $email_address = xtc_db_prepare_input($_SESSION['reshash']['EMAIL']);
        $company = xtc_db_prepare_input($_SESSION['reshash']['BUSINESS']);
        $street_address = xtc_db_prepare_input($this->UTF8decode($_SESSION['reshash']['SHIPTOSTREET'] . $_SESSION['reshash']['SHIPTOSTREET_2']));
        $postcode = xtc_db_prepare_input($_SESSION['reshash']['SHIPTOZIP']);
        $city = xtc_db_prepare_input($this->UTF8decode($_SESSION['reshash']['SHIPTOCITY']));
        $state = xtc_db_prepare_input($_SESSION['reshash']['SHIPTOSTATE']);
        $telephone = xtc_db_prepare_input($_SESSION['reshash']['PHONENUM']);
        $country_query = xtc_db_query("select * from " . TABLE_COUNTRIES . " where countries_iso_code_2 = '" . xtc_db_input($_SESSION['reshash']['SHIPTOCOUNTRYCODE']) . "' ");
        $tmp_country = xtc_db_fetch_array($country_query);
        $country = xtc_db_prepare_input($tmp_country['countries_id']);
        $sql_data_array = array('customers_id' => $_SESSION['customer_id'],
            'entry_firstname' => $firstname,
            'entry_lastname' => $lastname,
            'entry_street_address' => $street_address,
            'entry_postcode' => $postcode,
            'entry_city' => $city,
            'entry_country_id' => $country,
            'entry_company' => $company,
            'entry_zone_id' => '0',
            'entry_state' => $state,
            'address_date_added' => 'now()',
            'address_last_modified' => 'now()',
            'address_class' => 'paypal');
        $check_address_query = xtc_db_query("SELECT address_book_id FROM " . TABLE_ADDRESS_BOOK . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND address_class = 'paypal'");
        $check_address = xtc_db_fetch_array($check_address_query);
        if ($check_address['address_book_id'] != '') {
            xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "address_book_id = '" . (int) $check_address['address_book_id'] . "' and customers_id ='" . (int) $_SESSION['customer_id'] . "'");
            $send_to = $check_address['address_book_id'];
        } else {
            xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
            $send_to = xtc_db_insert_id();
        }
        $_SESSION['sendto'] = $send_to;
    }

    function hash_call($methodName, $nvpStr, $pp_token = '') {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->API_Endpoint . $pp_token);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($this->USE_PROXY) {
                curl_setopt($ch, CURLOPT_PROXY, PROXY_HOST . ":" . PROXY_PORT);
            }
            $nvpreq = "METHOD=" . urlencode($methodName) . "&VERSION=" . urlencode($this->version) . "&PWD=" . urlencode($this->API_Password) . "&USER=" . urlencode($this->API_UserName) . "&SIGNATURE=" . urlencode($this->API_Signature) . $nvpStr;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
            $response = curl_exec($ch);
            $nvpResArray = $this->deformatNVP($response);
            $nvpReqArray = $this->deformatNVP($nvpreq);
            $_SESSION['nvpReqArray'] = $nvpReqArray;
            $curl_fehler = curl_errno($ch);
            curl_close($ch);
            if (!$curl_fehler) {
                return $nvpResArray;
            }
        }
        /// Falls cURL nicht da oder Fehlerhaft
        global $API_Endpoint, $version, $API_UserName, $API_Password, $API_Signature, $nvp_Header;
        $nvpreq = "METHOD=" . urlencode($methodName) . "&VERSION=" . urlencode($this->version) . "&PWD=" . urlencode($this->API_Password) . "&USER=" . urlencode($this->API_UserName) . "&SIGNATURE=" . urlencode($this->API_Signature) . $nvpStr;
        $request_post = array('http' => array('method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => $nvpreq));
        $request = stream_context_create($request_post);
        $response = file_get_contents($this->API_Endpoint . $pp_token, false, $request);
        $nvpResArray = $this->deformatNVP($response);
        $nvpReqArray = $this->deformatNVP($nvpreq);
        $_SESSION['nvpReqArray'] = $nvpReqArray;
        return $nvpResArray;
    }

    function deformatNVP($nvpstr) {
        $intial = 0;
        $nvpArray = array();
        while (strlen($nvpstr)) {
            $keypos = strpos($nvpstr, '=');
            $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);
            $keyval = substr($nvpstr, $intial, $keypos);
            $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
            $nvpArray[urldecode($keyval)] = urldecode($valval);
            $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
        }
        return $nvpArray;
    }

    function build_error_message($resArray = '', $Aufruf = '') {
        global $messageStack;
        if (isset($_SESSION['curl_error_no'])) {
            $errorCode = $_SESSION['curl_error_no'];
            $errorMessage = $_SESSION['curl_error_msg'];
            $error .= 'Error Number: ' . $errorCode . '<br />';
            $error .= 'Error Message: ' . $errorMessage . '<br />';
        } else {
            $error .= 'Ack: ' . $resArray['ACK'] . '<br />';
            $error .= 'Correlation ID: ' . $resArray['CORRELATIONID'] . '<br />';
            $error .= 'Version:' . $resArray['VERSION'] . '<br />';
            $count = 0;
            $redirect = 0;
            while (isset($resArray["L_SHORTMESSAGE" . $count])) {
                $errorCode = $resArray["L_ERRORCODE" . $count];
                $shortMessage = $resArray["L_SHORTMESSAGE" . $count];
                $longMessage = $resArray["L_LONGMESSAGE" . $count];
                if ($Aufruf == 'DoEx' AND ($errorCode == '10422' OR $errorCode == '10417'))
                    $redirect = 1;
                $count = $count + 1;
                $error .= 'Error Number:' . $errorCode . '<br />';
                $error .= 'Error Short Message: ' . $shortMessage . '<br />';
                $error .= 'Error Long Message: ' . $longMessage . '<br />';
            }
            if ($redirect == 1)
                $_SESSION['reshash']['REDIRECTREQUIRED'] = "TRUE";
        }
        $_SESSION['reshash']['FORMATED_ERRORS'] = $error;
    }

    function paypal_get_products($paymentAmount, $order_tax, $order_discount, $order_fee, $order_shipping, $order_gs, $express_call = False) {
        global $xtPrice, $order;
        require_once(DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
        $products_sum_amt = 0;
        $tmp_products = '';
        for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
            $products_price = round($order->products[$i]['price'], $xtPrice->get_decimal_places($order->info['currency']));
            $products_sum_amt+=$products_price * $order->products[$i]['qty'];
            $attributes_data = '';
            $attributes_model = '';
            if ((isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0)) {
                for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j++) {
                    $attributes_data .= ' - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
                    $attributes_model .= '-' . xtc_get_attributes_model($order->products[$i]['id'], $order->products[$i]['attributes'][$j]['value'], $order->products[$i]['attributes'][$j]['option']);
                }
            }
            $tmp_products .='&L_NAME' . $i . '=' . urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", substr($order->products[$i]['name'] . $attributes_data, 0, 127))) .
                    '&L_NUMBER' . $i . '=' . urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", substr($order->products[$i]['model'] . $attributes_model, 0, 127))) .
                    '&L_QTY' . $i . '=' . urlencode($order->products[$i]['qty']) .
                    '&L_AMT' . $i . '=' . urlencode(number_format($products_price, $xtPrice->get_decimal_places($order->info['currency']), '.', ','));
        }
        if ($order_discount != 0) {
            $products_sum_amt+=$order_discount;
            $tmp_products .='&L_NAME' . $i . '=' . urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", substr(SUB_TITLE_OT_DISCOUNT, 0, 127))) .
                    '&L_NUMBER' . $i . '=' .
                    '&L_QTY' . $i . '=1' .
                    '&L_AMT' . $i . '=' . urlencode(number_format($order_discount, $xtPrice->get_decimal_places($order->info['currency']), '.', ','));
            $i++;
        }
        if ($order_gs != 0) {
            $products_sum_amt+=$order_gs;
            $tmp_products .='&L_NAME' . $i . '=' . urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", substr(PAYPAL_GS, 0, 127))) .
                    '&L_NUMBER' . $i . '=' .
                    '&L_QTY' . $i . '=1' .
                    '&L_AMT' . $i . '=' . urlencode(number_format($order_gs, $xtPrice->get_decimal_places($order->info['currency']), '.', ','));
            $i++;
        }
        if ($order_fee != 0) {
            $products_sum_amt+=$order_fee;
            $tmp_products .='&L_NAME' . $i . '=' . urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", "Handling")) .
                    '&L_NUMBER' . $i . '=' .
                    '&L_QTY' . $i . '=1' .
                    '&L_AMT' . $i . '=' . urlencode(number_format($order_fee, $xtPrice->get_decimal_places($order->info['currency']), '.', ','));
            $i++;
        }
        if ($order_shipping != 0) {
            $products_sum_amt+=$order_shipping;
            $tmp_products .='&L_NAME' . $i . '=' . urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", substr(SHIPPING_COSTS, 0, 127))) .
                    '&L_NUMBER' . $i . '=' .
                    '&L_QTY' . $i . '=1' .
                    '&L_AMT' . $i . '=' . urlencode(number_format($order_shipping, $xtPrice->get_decimal_places($order->info['currency']), '.', ','));
            $i++;
        }
        $products_sum_amt = round($products_sum_amt, $xtPrice->get_decimal_places($order->info['currency']));
        if ($order_tax != 0 && trim($paymentAmount - $products_sum_amt) >= $order_tax) {
            $products_sum_amt+=$order_tax;
            $tmp_products .='&L_NAME' . $i . '=' . urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", substr(PAYPAL_TAX, 0, 127))) .
                    '&L_NUMBER' . $i . '=' .
                    '&L_QTY' . $i . '=1' .
                    '&L_AMT' . $i . '=' . urlencode(number_format($order_tax, $xtPrice->get_decimal_places($order->info['currency']), '.', ','));
            $i++;
        }
        if ($express_call && PAYPAL_EXP_WARN != '') {
            $tmp_products .='&L_NAME' . $i . '=' . urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", substr(PAYPAL_EXP_WARN, 0, 127))) .
                    '&L_NUMBER' . $i . '=' .
                    '&L_QTY' . $i . '=0' .
                    '&L_AMT' . $i . '=0';
            $i++;
        }
        if ($express_call && PAYPAL_EXP_VORL != '' && PAYPAL_EXP_VERS != 0) {
            $products_sum_amt+=PAYPAL_EXP_VERS;
            $tmp_products .='&L_NAME' . $i . '=' . urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", substr(PAYPAL_EXP_VORL, 0, 127))) .
                    '&L_NUMBER' . $i . '=' .
                    '&L_QTY' . $i . '=1' .
                    '&L_AMT' . $i . '=' . urlencode(number_format(PAYPAL_EXP_VERS, $xtPrice->get_decimal_places($order->info['currency']), '.', ','));
            $i++;
        }
        $products_sum_amt = round($products_sum_amt, $xtPrice->get_decimal_places($order->info['currency']));
        if (trim($paymentAmount) != trim($products_sum_amt)) {
            $order_diff = round($paymentAmount - $products_sum_amt, $xtPrice->get_decimal_places($order->info['currency']));
            $products_sum_amt+=$order_diff;
            $tmp_products .='&L_NAME' . $i . '=' . urlencode($this->mn_iconv($_SESSION['language_charset'], "UTF-8", "Differenz")) .
                    '&L_NUMBER' . $i . '=' .
                    '&L_QTY' . $i . '=1' .
                    '&L_AMT' . $i . '=' . urlencode(number_format($order_diff, $xtPrice->get_decimal_places($order->info['currency']), '.', ','));
        }
        $tmp_products.="&ITEMAMT=" . urlencode(number_format($products_sum_amt, $xtPrice->get_decimal_places($order->info['currency']), '.', ','));
        return($tmp_products);
    }

    function write_status_history($o_id) {
        if (empty($o_id))
            return false;
        $ack = strtoupper($_SESSION['reshash']["ACK"]);
        if ($ack == "SUCCESS" OR $ack == "SUCCESSWITHWARNING") {
            $o_status = PAYPAL_ORDER_STATUS_PENDING_ID;
        } else {
            $o_status = PAYPAL_ORDER_STATUS_REJECTED_ID;
        }
        if (!($ack == "SUCCESS" OR $ack == "SUCCESSWITHWARNING")) {
            $crlf = "\n";
            while (list($key, $value) = each($_SESSION['reshash'])) {
                $comment .= $key . '=' . $value . $crlf;
            }
        }
        $order_history_data = array('orders_id' => $o_id,
            'orders_status_id' => $o_status,
            'date_added' => 'now()',
            'customer_notified' => '0',
            'comments' => $comment);
        xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $order_history_data);
        xtc_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status = '" . $o_status . "', last_modified = now() WHERE orders_id = '" . xtc_db_prepare_input($o_id) . "'");
        return true;
    }

    function logging_status($o_id) {
        $data = array_merge($_SESSION['nvpReqArray'], $_SESSION['reshash']);
        if (!$data['TRANSACTIONID'] OR $data['TRANSACTIONID'] == '') {
            $data['TRANSACTIONID'] = 'PayPal Fehler!<br>' . date("d.m.Y - H:i:s");
        }
        $data_array = array('xtc_order_id' => $o_id,
            'txn_type' => $data['TRANSACTIONTYPE'],
            'reason_code' => $data['REASONCODE'],
            'payment_type' => $data['PAYMENTTYPE'],
            'payment_status' => $data['PAYMENTSTATUS'],
            'pending_reason' => $data['PENDINGREASON'],
            'invoice' => $data['INVNUM'],
            'mc_currency' => $data['CURRENCYCODE'],
            'first_name' => $_SESSION['customer_first_name'],
            'last_name' => $_SESSION['customer_last_name'],
            'payer_business_name' => $this->UTF8decode($data['BUSINESS']),
            'address_name' => $this->UTF8decode($data['SHIPTONAME']),
            'address_street' => $this->UTF8decode($data['SHIPTOSTREET']),
            'address_city' => $this->UTF8decode($data['SHIPTOCITY']),
            'address_state' => $this->UTF8decode($data['SHIPTOSTATE']),
            'address_zip' => $data['SHIPTOZIP'],
            'address_country' => $this->UTF8decode($data['SHIPTOCOUNTRYNAME']),
            'address_status' => $data['ADDRESSSTATUS'],
            'payer_email' => $data['EMAIL'],
            'payer_id' => $data['PAYERID'],
            'payer_status' => $data['PAYERSTATUS'],
            'payment_date' => $data['TIMESTAMP'],
            'business' => '',
            'receiver_email' => '',
            'receiver_id' => '',
            'txn_id' => $data['TRANSACTIONID'],
            'parent_txn_id' => '',
            'num_cart_items' => '',
            'mc_gross' => $data['AMT'],
            'mc_fee' => $data['FEEAMT'],
            'mc_authorization' => $data['AMT'],
            'payment_gross' => '',
            'payment_fee' => '',
            'settle_amount' => $data['SETTLEAMT'],
            'settle_currency' => '',
            'exchange_rate' => $data['EXCHANGERATE'],
            'notify_version' => $data['VERSION'],
            'verify_sign' => '',
            'last_modified' => '',
            'date_added' => 'now()',
            'memo' => $data['DESC']);
        xtc_db_perform(TABLE_PAYPAL, $data_array);
        return true;
    }

    function giropay_confirm($data = '') {
        // Giropay transaction
        $tkn = (($data['token'] != '') ? $data['token'] : $_SESSION['nvpReqArray']['TOKEN']);
        unset($_SESSION['payment']);
        unset($_SESSION['nvpReqArray']);
        unset($_SESSION['reshash']);
        xtc_redirect($this->GIROPAY_URL . '' . urlencode($tkn));
    }

    function callback_process($data, $charset) {
        global $_GET;
        $this->data = $data;
        require_once(DIR_WS_CLASSES . 'class.phpmailer.php');
        if (EMAIL_TRANSPORT == 'smtp') {
            require_once(DIR_WS_CLASSES . 'class.smtp.php');
        }
        $xtc_order_id = (int) substr($this->data['invoice'], strlen(PAYPAL_INVOICE));
        if (isset($xtc_order_id) && is_numeric($xtc_order_id) && ($xtc_order_id > 0)) {
            // order suchen
            $order_query = xtc_db_query("SELECT currency, currency_value FROM " . TABLE_ORDERS . " WHERE orders_id = '" . xtc_db_prepare_input($xtc_order_id) . "'");
            if (xtc_db_num_rows($order_query) > 0) {
                $ipn_charset = xtc_db_prepare_input($this->data['charset']);
                $ipn_data = array();
                $ipn_data['reason_code'] = xtc_db_prepare_input($this->data['reason_code']);
                $ipn_data['xtc_order_id'] = xtc_db_prepare_input($xtc_order_id);
                $ipn_data['payment_type'] = xtc_db_prepare_input($this->data['payment_type']);
                $ipn_data['payment_status'] = xtc_db_prepare_input($this->data['payment_status']);
                $ipn_data['pending_reason'] = xtc_db_prepare_input($this->data['pending_reason']);
                $ipn_data['invoice'] = xtc_db_prepare_input($this->data['invoice']);
                $ipn_data['mc_currency'] = xtc_db_prepare_input($this->data['mc_currency']);
                $ipn_data['first_name'] = xtc_db_prepare_input($this->IPNdecode($this->data['first_name'], $ipn_charset, $charset));
                $ipn_data['last_name'] = xtc_db_prepare_input($this->IPNdecode($this->data['last_name'], $ipn_charset, $charset));
                $ipn_data['address_name'] = xtc_db_prepare_input($this->IPNdecode($this->data['address_name'], $ipn_charset, $charset));
                $ipn_data['address_street'] = xtc_db_prepare_input($this->IPNdecode($this->data['address_street'], $ipn_charset, $charset));
                $ipn_data['address_city'] = xtc_db_prepare_input($this->IPNdecode($this->data['address_city'], $ipn_charset, $charset));
                $ipn_data['address_state'] = xtc_db_prepare_input($this->IPNdecode($this->data['address_state'], $ipn_charset, $charset));
                $ipn_data['address_zip'] = xtc_db_prepare_input($this->data['address_zip']);
                $ipn_data['address_country'] = xtc_db_prepare_input($this->IPNdecode($this->data['address_country'], $ipn_charset, $charset));
                $ipn_data['address_status'] = xtc_db_prepare_input($this->data['address_status']);
                $ipn_data['payer_email'] = xtc_db_prepare_input($this->data['payer_email']);
                $ipn_data['payer_id'] = xtc_db_prepare_input($this->data['payer_id']);
                $ipn_data['payer_status'] = xtc_db_prepare_input($this->data['payer_status']);
                $ipn_data['payment_date'] = xtc_db_prepare_input($this->datetime_to_sql_format($this->data['payment_date']));
                $ipn_data['business'] = xtc_db_prepare_input($this->IPNdecode($this->data['business'], $ipn_charset, $charset));
                $ipn_data['receiver_email'] = xtc_db_prepare_input($this->data['receiver_email']);
                $ipn_data['receiver_id'] = xtc_db_prepare_input($this->data['receiver_id']);
                $ipn_data['txn_id'] = xtc_db_prepare_input($this->data['txn_id']);
                $ipn_data['txn_type'] = $this->ipn_determine_txn_type($this->data['txn_type']);
                $ipn_data['parent_txn_id'] = xtc_db_prepare_input($this->data['parent_txn_id']);
                $ipn_data['mc_gross'] = xtc_db_prepare_input($this->data['mc_gross']);
                $ipn_data['mc_fee'] = xtc_db_prepare_input($this->data['mc_fee']);
                $ipn_data['mc_shipping'] = xtc_db_prepare_input($this->data['mc_shipping']);
                $ipn_data['payment_gross'] = xtc_db_prepare_input($this->data['payment_gross']);
               
                $ipn_data['payment_fee'] = xtc_db_prepare_input($this->data['payment_fee']);
                $ipn_data['notify_version'] = xtc_db_prepare_input($this->data['notify_version']);
                $ipn_data['verify_sign'] = xtc_db_prepare_input($this->data['verify_sign']);
                $ipn_data['num_cart_items'] = xtc_db_prepare_input($this->data['num_cart_items']);
                if ($ipn_data['num_cart_items'] > 1) {
                    $verspos = $ipn_data['num_cart_items'];
                    for ($p = 1; $p <= $verspos; $p++) {
                        if ($this->data['item_name' . $p] == substr(SUB_TITLE_OT_DISCOUNT, 0, 127) || $this->data['item_name' . $p] == substr(PAYPAL_GS, 0, 127) || $this->data['item_name' . $p] == "Handling" || $this->data['item_name' . $p] == substr(PAYPAL_TAX, 0, 127) || $this->data['item_name' . $p] == "Differenz") {
                            $ipn_data['num_cart_items']--;
                        }
                        if ($this->data['item_name' . $p] == substr(SHIPPING_COSTS, 0, 127)) {
                            $ipn_data['mc_shipping'] = $this->data['mc_gross_' . $p];
                            $ipn_data['num_cart_items']--;
                        }
                    }
                }
                $_transQuery = xtc_db_fetch_array(xtc_db_query("SELECT paypal_ipn_id FROM " . TABLE_PAYPAL . " WHERE txn_id = '" . $ipn_data['txn_id'] . "'"));
                if ($_transQuery['paypal_ipn_id'] != '') {
                    $insert_id = $_transQuery['paypal_ipn_id'];
                    $sql_data_array = array('payment_status' => $ipn_data['payment_status'],
                        'pending_reason' => $ipn_data['pending_reason'],
                        'payer_email' => $ipn_data['payer_email'],
                        'num_cart_items' => $ipn_data['num_cart_items'],
                        'mc_fee' => $ipn_data['mc_fee'],
                        'mc_shipping' => $ipn_data['mc_shipping'],
                        'address_name' => $ipn_data['address_name'],
                        'address_street' => $ipn_data['address_street'],
                        'address_city' => $ipn_data['address_city'],
                        'address_state' => $ipn_data['address_state'],
                        'address_zip' => $ipn_data['address_zip'],
                        'address_country' => $ipn_data['address_country'],
                        'address_status' => $ipn_data['address_status'],
                        'payer_status' => $ipn_data['payer_status'],
                        'receiver_email' => $ipn_data['receiver_email'],
                        'last_modified ' => 'now()');
                    xtc_db_perform(TABLE_PAYPAL, $sql_data_array, 'update', "paypal_ipn_id = '" . (int) $insert_id . "'");
                } else {
                    $ipn_data['date_added'] = 'now()';
                    $ipn_data['last_modified'] = 'now()';
                    xtc_db_perform(TABLE_PAYPAL, $ipn_data);
                    $insert_id = xtc_db_insert_id();
                }
                $paypal_order_history = array('paypal_ipn_id' => $insert_id,
                    'txn_id' => $ipn_data['txn_id'],
                    'parent_txn_id' => $ipn_data['parent_txn_id'],
                    'payment_status' => $ipn_data['payment_status'],
                    'pending_reason' => $ipn_data['pending_reason'],
                    'mc_amount' => $ipn_data['mc_gross'],
                    'date_added' => 'now()');
                xtc_db_perform(TABLE_PAYPAL_STATUS_HISTORY, $paypal_order_history);
                $crlf = "\n";
                $comment_status = xtc_db_prepare_input($this->data['payment_status']) . ' ' . xtc_db_prepare_input($this->data['mc_gross']) . xtc_db_prepare_input($this->data['mc_currency']) . $crlf;
                $comment_status .= ' ' . xtc_db_prepare_input($this->data['first_name']) . ' ' . xtc_db_prepare_input($this->data['last_name']) . ' ' . xtc_db_prepare_input($this->data['payer_email']);
                if (isset($this->data['payer_status']))
                    $comment_status .= ' is ' . xtc_db_prepare_input($this->data['payer_status']);
                $comment_status .= '.' . $crlf;
                if (isset($this->data['test_ipn']) && is_numeric($this->data['test_ipn']) && ($_POST['test_ipn'] > 0))
                    $comment_status .='(Sandbox-Test Mode)' . $crlf;
                $comment_status .= 'Total=' . xtc_db_prepare_input($this->data['mc_gross']) . xtc_db_prepare_input($this->data['mc_currency']);
                if (isset($this->data['pending_reason']))
                    $comment_status .= $crlf . ' Pending Reason=' . xtc_db_prepare_input($this->data['pending_reason']);
                if (isset($this->data['reason_code']))
                    $comment_status .= $crlf . ' Reason Code=' . xtc_db_prepare_input($this->data['reason_code']);
                $comment_status .= $crlf . ' Payment=' . xtc_db_prepare_input($this->data['payment_type']);
                $comment_status .= $crlf . ' Date=' . xtc_db_prepare_input($this->data['payment_date']);
                if (isset($this->data['parent_txn_id']))
                    $comment_status .= $crlf . ' ParentID=' . xtc_db_prepare_input($this->data['parent_txn_id']);
                $comment_status .= $crlf . ' ID=' . xtc_db_prepare_input($_POST['txn_id']);
                //Set status for default (Pending)
                $order_status_id = PAYPAL_ORDER_STATUS_PENDING_ID;
                $parameters = 'cmd=_notify-validate';
                foreach ($this->data as $key => $value) {
                    $parameters .= '&' . $key . '=' . urlencode(stripslashes($value));
                }
                $mit_curl = 0;
                if (function_exists('curl_init')) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $this->IPN_URL);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    $result = curl_exec($ch);
                    if (!curl_errno($ch))
                        $mit_curl = 1;
                    curl_close($ch);
                }
                // cURL fehlt oder ist fehlgeschlagen
                if ($mit_curl == 0) {
                    $request_post = array(
                        'http' => array(
                            'method' => 'POST',
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'content' => $parameters));
                    $request = stream_context_create($request_post);
                    $result = file_get_contents($this->IPN_URL, false, $request);
                }
                if (strtoupper($result) == 'VERIFIED' or $result == '1') {
                    // Steht auf Warten
                    if (strtolower($this->data['payment_status']) == 'completed') {
                        if (PAYPAL_ORDER_STATUS_SUCCESS_ID > 0) {
                            $order_status_id = PAYPAL_ORDER_STATUS_SUCCESS_ID;
                        }
                        //Set status for Denied, Failed
                    } elseif ((strtolower($this->data['payment_status']) == 'denied') OR (strtolower($this->data['payment_status']) == 'failed')) {
                        $order_status_id = PAYPAL_ORDER_STATUS_REJECTED_ID;
                        //Set status for Reversed
                    } elseif (strtolower($this->data['payment_status']) == 'reversed') {
                        $order_status_id = PAYPAL_ORDER_STATUS_PENDING_ID;
                        //Set status for Canceled-Reversal
                    } elseif (strtolower($this->data['payment_status']) == 'canceled-reversal') {
                        $order_status_id = PAYPAL_ORDER_STATUS_SUCCESS_ID;
                        //Set status for Refunded
                    } elseif (strtolower($this->data['payment_status']) == 'refunded') {
                        $order_status_id = DEFAULT_ORDERS_STATUS_ID;
                        //Set status for Pendign - eigentlich nicht nötig?
                    } elseif (strtolower($this->data['payment_status']) == 'pending') {
                        $order_status_id = PAYPAL_ORDER_STATUS_PENDING_ID;
                        //Set status for Processed - wann kommt das ?
                    } elseif (strtolower($this->data['payment_status']) == 'processed') {
                        if (PAYPAL_ORDER_STATUS_SUCCESS_ID > 0) {
                            $order_status_id = PAYPAL_ORDER_STATUS_SUCCESS_ID;
                        }
                    }
                } else {
                    $order_status_id = PAYPAL_ORDER_STATUS_REJECTED_ID;
                    $error_reason = 'Received INVALID responce but invoice and Customer matched.';
                }
                $xtc_order_id = (int) substr($this->data['invoice'], strlen(PAYPAL_INVOICE));
                xtc_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status = '" . $order_status_id . "', last_modified = now() WHERE orders_id = '" . xtc_db_prepare_input($xtc_order_id) . "'");
                $sql_data_array = array('orders_id' => xtc_db_prepare_input($xtc_order_id),
                    'orders_status_id' => $order_status_id,
                    'date_added' => 'now()',
                    'customer_notified' => '0',
                    'comments' => 'PayPal IPN ' . $comment_status . '');
                xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
            } else {
                $error_reason = 'IPN-Fehler: Keine Order Nr.=' . xtc_db_prepare_input($this->data['invoice']) . ' mit Kunden=' . (int) $this->data['custom'] . ' gefunden.';
            }
        } else {
            $error_reason = 'IPN-Fehler: Keine Order gefunden zu den empfangenen Daten.';
        }
        if (xtc_not_null(EMAIL_SUPPORT_ADDRESS) && strlen($error_reason)) {
            $email_body = $error_reason . "\n\n" . '<br>';
            $email_body .= $_SERVER["REQUEST_METHOD"] . " - " . $_SERVER["REMOTE_ADDR"] . " - " . $_SERVER["HTTP_REFERER"] . " - " . $_SERVER["HTTP_ACCEPT"] . "\n\n" . '<br>';
            $email_body .= '$_POST:' . "\n\n" . '<br>';
            foreach ($this->data as $key => $value) {
                $email_body .= $key . '=' . $value . "\n" . '<br>';
            }
            $email_body .= "\n" . '$_GET:' . "\n\n" . '<br>';
            foreach ($_GET as $key => $value) {
                $email_body .= $key . '=' . $value . "\n" . '<br>';
            }
            xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_ADDRESS, '', EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, false, false, 'PayPal IPN Invalid Process', $email_body, $email_body);
        }
    }

    function datetime_to_sql_format($paypalDateTime) {
        //Copyright (c) 2004 DevosC.com
        $months = array('Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04', 'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08', 'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12');
        $hour = substr($paypalDateTime, 0, 2);
        $minute = substr($paypalDateTime, 3, 2);
        $second = substr($paypalDateTime, 6, 2);
        $month = $months[substr($paypalDateTime, 9, 3)];
        $day = (strlen($day = preg_replace("/,/", '', substr($paypalDateTime, 13, 2))) < 2) ? '0' . $day : $day;
        $year = substr($paypalDateTime, -8, 4);
        if (strlen($day) < 2)
            $day = '0' . $day;
        return($year . "-" . $month . "-" . $day . " " . $hour . ":" . $minute . ":" . $second);
    }

    function buildAPIKey($key, $pay) {
        $key_arr = explode(',', $key);
        $k = '';
        for ($i = 0; $i < count($key_arr); $i++)
            $k.=chr($key_arr[$i]);
        if ($pay == 'ec') {
            return $k . 'EC_MN_53';
        }
    }

    function ipn_determine_txn_type($txn_type = 'unknown') {
        if (substr($txn_type, 0, 8) == 'cleared-')
            return $txn_type;
        if ($txn_type == 'send_money')
            return $txn_type;
        if ($txn_type == 'express_checkout' || $txn_type == 'cart')
            $txn_type = $txn_type;
        if ($this->data['payment_status'] == 'Completed' && $txn_type == 'express_checkout' && $this->data['payment_type'] == 'echeck') {
            $txn_type = 'express-checkout-cleared';
            return $txn_type;
        }
        if ($this->data['payment_status'] == 'Completed' && $this->data['payment_type'] == 'echeck') {
            $txn_type = 'echeck-cleared';
            return $txn_type;
        }
        if (($this->data['payment_status'] == 'Denied' || $this->data['payment_status'] == 'Failed') && $this->data['payment_type'] == 'echeck') {
            $txn_type = 'echeck-denied';
            return $txn_type;
        }
        if ($this->data['payment_status'] == 'Denied') {
            $txn_type = 'denied';
            return $txn_type;
        }
        if (($this->data['payment_status'] == 'Pending') && $this->data['pending_reason'] == 'echeck') {
            $txn_type = 'pending-echeck';
            return $txn_type;
        }
        if (($this->data['payment_status'] == 'Pending') && $this->data['pending_reason'] == 'address') {
            $txn_type = 'pending-address';
            return $txn_type;
        }
        if (($this->data['payment_status'] == 'Pending') && $this->data['pending_reason'] == 'intl') {
            $txn_type = 'pending-intl';
            return $txn_type;
        }
        if (($this->data['payment_status'] == 'Pending') && $this->data['pending_reason'] == 'multi-currency') {
            $txn_type = 'pending-multicurrency';
            return $txn_type;
        }
        if (($this->data['payment_status'] == 'Pending') && $this->data['pending_reason'] == 'verify') {
            $txn_type = 'pending-verify';
            return $txn_type;
        }
        return $txn_type;
    }

    function IPNdecode($string, $ipncharset = 'windows-1252', $charset) {
        if ($ipncharset != $charset)
            $string = $this->mn_iconv($ipncharset, $charset, $string);
        return $string;
    }

    function UTF8decode($string) {
        if ($this->detectUTF8($string))
            $string = $this->mn_iconv('UTF-8', $_SESSION['language_charset'], $string);
        return($string);
    }

    function detectUTF8($string) {
        return preg_match('%(?:
				[\xC2-\xDF][\x80-\xBF]
				|\xE0[\xA0-\xBF][\x80-\xBF]
				|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
				|\xED[\x80-\x9F][\x80-\xBF]
				|\xF0[\x90-\xBF][\x80-\xBF]{2}
				|[\xF1-\xF3][\x80-\xBF]{3}
				|\xF4[\x80-\x8F][\x80-\xBF]{2}
				)+%xs', $string);
    }

    function state_code($string) {
        $zone_query = xtc_db_query("SELECT zone_code FROM " . TABLE_ZONES . " WHERE zone_name = '" . $string . "'");
        if (xtc_db_num_rows($zone_query)) {
            $zone = xtc_db_fetch_array($zone_query);
            return $zone['zone_code'];
        } else {
            return $string;
        }
    }

    function mn_iconv($t1, $t2, $string) {
        if (function_exists('iconv')) {
            return iconv($t1, $t2, $string);
        }
        if ($t2 == "UTF-8") {
            if (function_exists('utf8_encode')) {
                return utf8_encode($string);
            } else {
                $string = preg_replace("/([\x80-\xFF])/e", "chr(0xC0|ord('\\1')>>6).chr(0x80|ord('\\1')&0x3F)", $string);
                return($string);
            }
        } elseif ($t1 == "UTF-8") {
            if (function_exists('utf8_decode')) {
                return utf8_decode($string);
            } else {
                $string = preg_replace("/([\xC2\xC3])([\x80-\xBF])/e", "chr(ord('\\1')<<6&0xC0|ord('\\2')&0x3F)", $string);
                return($string);
            }
        } else {
            // keine Konvertierung moeglich
            return($string);
        }
    }
//Admin Functions

    function GetTransactionDetails($txn_id) {
        // Stand: 29.04.2009
        $nvpstr = '&TRANSACTIONID=' . urlencode($txn_id);
        $resArray = $this->hash_call("gettransactionDetails", $nvpstr);
        $ack = strtoupper($resArray["ACK"]);
        if ($ack != "SUCCESS")
            $this->build_error_message($resArray);
        return $resArray;
    }

    /*     * ********************************************************** */

    function RefundTransaction($txn_id, $curr, $amount, $refund, $note = '') {
        // Stand: 29.04.2009
        // full refund ?
        if ($note != '')
            $note = '&NOTE=' . urlencode($note);
        if ($amount != $refund) {
            $refund = str_replace(',', '.', $refund);
            $nvpstr = '&TRANSACTIONID=' . urlencode($txn_id) . '&REFUNDTYPE=Partial&CURRENCYCODE=' . $curr . '&AMT=' . $refund . $note;
        } else {
            $nvpstr = '&TRANSACTIONID=' . urlencode($txn_id) . '&REFUNDTYPE=Full' . $note;
        }
        $resArray = $this->hash_call("RefundTransaction", $nvpstr);
        $ack = strtoupper($resArray["ACK"]);
        if ($ack != "SUCCESS")
            $this->build_error_message($resArray);
        return $resArray;
    }

    /*     * ********************************************************** */

    function DoCapture($txn_id, $curr, $amount, $capture_amount, $note = '') {
        // Stand: 29.04.2009
        if ($note != '')
            $note = '&NOTE=' . urlencode($note);
        if ($amount != $capture_amount) {
            $capture_amount = str_replace(',', '.', $capture_amount);
            $nvpstr = '&AUTHORIZATIONID=' . urlencode($txn_id) . '&COMPLETETYPE=NotComplete&CURRENCYCODE=' . $curr . '&AMT=' . $capture_amount . $note;
        } else {
            $nvpstr = '&AUTHORIZATIONID=' . urlencode($txn_id) . '&COMPLETETYPE=Complete' . $note;
        }
        $resArray = $this->hash_call("DoCapture", $nvpstr);
        $ack = strtoupper($resArray["ACK"]);
        if ($ack != "SUCCESS")
            $this->build_error_message($resArray);
        return $resArray;
    }

    /*     * ********************************************************** */

    function TransactionSearch($data) {
        // Stand: 29.04.2009
        global $date;
        // date range
        if ($data['span'] == 'narrow') {
            // show range
            $startdate = (int) $data['from_y'] . '-' . (int) $data['from_m'] . '-' . (int) $data['from_t'] . 'T00:00:00Z';
            $enddate = (int) $data['to_y'] . '-' . (int) $data['to_m'] . '-' . (int) $data['to_t'] . 'T24:00:00Z';
        } else {
            /*
             * 1 = last day
             * 2 = last week
             * 3 = last month
             * 4 = last year
             */
            switch ($data['for']) {
                case '1' :
                    $cal_date = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));
                    $_date = array();
                    $_date['tt'] = date('d', $cal_date);
                    $_date['mm'] = date('m', $cal_date);
                    $_date['yyyy'] = date('Y', $cal_date);
                    $startdate = (int) $_date['yyyy'] . '-' . (int) $_date['mm'] . '-' . (int) $_date['tt'] . 'T00:00:00Z';
                    $enddate = $date['actual']['yyyy'] . '-' . $date['actual']['mm'] . '-' . $date['actual']['tt'] . 'T24:00:00Z';
                    break;
                case '2' :
                    $cal_date = mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"));
                    $_date = array();
                    $_date['tt'] = date('d', $cal_date);
                    $_date['mm'] = date('m', $cal_date);
                    $_date['yyyy'] = date('Y', $cal_date);
                    $startdate = (int) $_date['yyyy'] . '-' . (int) $_date['mm'] . '-' . (int) $_date['tt'] . 'T00:00:00Z';
                    $enddate = $date['actual']['yyyy'] . '-' . $date['actual']['mm'] . '-' . $date['actual']['tt'] . 'T24:00:00Z';
                    break;
                case '3' :
                    $cal_date = mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
                    $_date = array();
                    $_date['tt'] = date('d', $cal_date);
                    $_date['mm'] = date('m', $cal_date);
                    $_date['yyyy'] = date('Y', $cal_date);
                    $startdate = (int) $_date['yyyy'] . '-' . (int) $_date['mm'] . '-' . (int) $_date['tt'] . 'T00:00:00Z';
                    $enddate = $date['actual']['yyyy'] . '-' . $date['actual']['mm'] . '-' . $date['actual']['tt'] . 'T24:00:00Z';
                    break;
                case '4' :
                    $cal_date = mktime(0, 0, 0, date("m"), date("d"), date("Y") - 1);
                    $_date = array();
                    $_date['tt'] = date('d', $cal_date);
                    $_date['mm'] = date('m', $cal_date);
                    $_date['yyyy'] = date('Y', $cal_date);
                    $startdate = (int) $_date['yyyy'] . '-' . (int) $_date['mm'] . '-' . (int) $_date['tt'] . 'T00:00:00Z';
                    $enddate = $date['actual']['yyyy'] . '-' . $date['actual']['mm'] . '-' . $date['actual']['tt'] . 'T24:00:00Z';
                    break;
            }
        }
        // search in details
        $detail_search = '';
        if ($data['search_type'] != '') {
            switch ($data['search_first_type']) {
                case 'email_alias' :
                    $detail_search = '&EMAIL=' . urlencode($data['search_type']);
                    break;
                case 'trans_id' :
                    $detail_search = '&TRANSACTIONID=' . urlencode($data['search_type']);
                    break;
                case 'last_name_only' :
                    $detail_search = '&LASTNAME=' . urlencode($data['search_type']);
                    break;
                case 'last_name' :
                    $search = explode(',', $data['search_type']);
                    $detail_search = '&LASTNAME=' . urlencode(trim($search['0'])) . '&FIRSTNAME=' . urlencode(trim($search['1']));
                    break;
                case 'invoice_id' :
                    $detail_search = '&INVNUM=' . urlencode($data['search_type']);
                    break;
            }
        }
        $nvpstr = '&STARTDATE=' . $startdate . '&ENDDATE=' . $enddate . '&CURRENCYCODE=EUR' . $detail_search;
        $resArray = $this->hash_call("TransactionSearch", $nvpstr);
        if ($resArray['ACK'] == 'Success') {
            $result = $this->createResultArray($resArray);
        } elseif ($resArray['ACK'] == 'SuccessWithWarning') {
            $this->SearchError['code'] = $resArray['L_ERRORCODE0'];
            $this->SearchError['shortmessage'] = $resArray['L_SHORTMESSAGE0'];
            $this->SearchError['longmessage'] = $resArray['L_LONGMESSAGE0'];
            $result = $this->createResultArray($resArray);
        } else {
            $this->SearchError['code'] = $resArray['L_ERRORCODE0'];
            $this->SearchError['shortmessage'] = $resArray['L_SHORTMESSAGE0'];
            $this->SearchError['longmessage'] = $resArray['L_LONGMESSAGE0'];
            $result = -1;
        }
        return $result;
    }

    /*     * ********************************************************** */

    function createResultArray($response) {
        // Stand: 29.04.2009
        $result = array();
        $n = 0;
        $flag = true;
        while ($flag) {
            if (!isset($response['L_TIMESTAMP' . $n])) {
                $flag = false;
                return -1;
            }
            $result[$n]['TIMESTAMP'] = $response['L_TIMESTAMP' . $n];
            $result[$n]['TYPE'] = $response['L_TYPE' . $n];
            $result[$n]['NAME'] = $response['L_NAME' . $n];
            $result[$n]['TXNID'] = $response['L_TRANSACTIONID' . $n];
            $result[$n]['STATUS'] = $response['L_STATUS' . $n];
            $result[$n]['AMT'] = $response['L_AMT' . $n];
            $result[$n]['FEEAMT'] = $response['L_FEEAMT' . $n];
            $result[$n]['NETAMT'] = $response['L_NETAMT' . $n];
            if (!isset($response['L_TIMESTAMP' . ($n + 1)]))
                $flag = false;
            $n++;
        }
        return $result;
    }

    /*     * ********************************************************** */

    function getStatusSymbol($status, $type = '', $reason = '') {
        // Stand: 29.04.2009
        switch ($status) {
            case 'Reversed' :
            case 'Refunded' :
                $symbol = xtc_image(DIR_WS_ICONS . 'action_refresh_blue.gif');
                break;
            case 'Completed' :
            case 'verified' :
            case 'confirmed' :
                $symbol = xtc_image(DIR_WS_ICONS . 'icon_accept.gif');
                break;
            case 'Pending' :
                $symbol = xtc_image(DIR_WS_ICONS . 'icon_clock.gif');
                if ($reason == 'authorization')
                    $symbol = xtc_image(DIR_WS_ICONS . 'icon_capture.gif');
                if ($reason == 'partial-capture')
                    $symbol = xtc_image(DIR_WS_ICONS . 'icon_partcapture.png');
                if ($reason == 'completed-capture')
                    $symbol = xtc_image(DIR_WS_ICONS . 'icon_capture.gif');
                break;
            case 'Denied' :
            case 'unverified' :
            case 'unconfirmed' :
                $symbol = xtc_image(DIR_WS_ICONS . 'exclamation.png');
                break;
            case 'Unconfirmed' :
                $symbol = xtc_image(DIR_WS_ICONS . 'exclamation.png');
                break;
            case 'Payment' :
            case 'Refund';
                switch ($type) {
                    case 'Completed' :
                        $symbol = xtc_image(DIR_WS_ICONS . 'icon_accept.gif');
                        break;
                    case 'Pending' :
                        $symbol = xtc_image(DIR_WS_ICONS . 'icon_clock.gif');
                        break;
                    case 'Refunded' :
                    case 'Partially Refunded';
                        $symbol = xtc_image(DIR_WS_ICONS . 'action_refresh_blue.gif');
                        break;
                    case 'Cancelled' :
                        $symbol = xtc_image(DIR_WS_ICONS . 'icon_cancel.png');
                        break;
                }
                break;
            case 'Transfer' :
                switch ($type) {
                    case 'Completed' :
                        $symbol = xtc_image(DIR_WS_ICONS . 'icon_arrow_right.gif');
                        break;
                }
            case '' :
                if ($type == 'new_case')
                    $symbol = xtc_image(DIR_WS_ICONS . 'exclamation.png');
                break;
        }
        return $symbol;
    }

    /*     * ********************************************************** */

    function mapResponse($data) {
        // Stand: 29.04.2009
        $data_array = array(
            'xtc_order_id' => $data['INVNUM'],
            'txn_type' => $data['TRANSACTIONTYPE'],
            'reason_code' => $data['REASONCODE'],
            'payment_type' => $data['PAYMENTTYPE'],
            'payment_status' => $data['PAYMENTSTATUS'],
            'pending_reason' => $data['PENDINGREASON'],
            'invoice' => $data['INVNUM'],
            'mc_currency' => $data['CURRENCYCODE'],
            'first_name' => $this->UTF8decode($data['FIRSTNAME']),
            'last_name' => $this->UTF8decode($data['LASTNAME']),
            'payer_business_name' => $this->UTF8decode($data['BUSINESS']),
            'address_name' => $this->UTF8decode($data['SHIPTONAME']),
            'address_street' => $this->UTF8decode($data['SHIPTOSTREET']),
            'address_city' => $this->UTF8decode($data['SHIPTOCITY']),
            'address_state' => $this->UTF8decode($data['SHIPTOSTATE']),
            'address_zip' => $data['SHIPTOZIP'],
            'address_country' => $this->UTF8decode($data['SHIPTOCOUNTRYNAME']),
            'address_status' => $data['ADDRESSSTATUS'],
            'payer_email' => $data['EMAIL'],
            'payer_id' => $data['PAYERID'],
            'payer_status' => $data['PAYERSTATUS'],
            'payment_date' => $data['TIMESTAMP'],
            'business' => '',
            'receiver_email' => $data['RECEIVEREMAIL'],
            'receiver_id' => $data['RECEIVERID'],
            'txn_id' => $data['TRANSACTIONID'],
            'parent_txn_id' => '',
            'num_cart_items' => '',
            'mc_gross' => $data['AMT'],
            'mc_fee' => $data['FEEAMT'],
            'mc_authorization' => $data['AMT'],
            'payment_gross' => '',
            'payment_fee' => '',
            'settle_amount' => $data['SETTLEAMT'],
            'settle_currency' => '',
            'exchange_rate' => $data['EXCHANGERATE'],
            'notify_version' => $data['VERSION'],
            'verify_sign' => '',
            'last_modified' => '',
            'date_added' => 'now()',
            'memo' => $data['DESC']
        );
        return $data_array;
    }

    /*     * ********************************************************** */

    function getPaymentType($type) {
        // Stand: 29.04.2009
        if ($type == '' OR strtoupper($type) == 'NONE')
            return;
        if (defined(TYPE_ . strtoupper($type))):
            return constant(TYPE_ . strtoupper($type));
        else:
            return(ucfirst($type));
        endif;
    }

    /*     * ********************************************************** */

    function getStatusName($status, $type = '') {
        // Stand: 29.04.2009
        if ($type == 'new_case')
            return STATUS_CASE;
        if (defined(STATUS_ . strtoupper($status))):
            return constant(STATUS_ . strtoupper($status));
        else:
            return(ucfirst($status));
        endif;
    }

    /*     * ********************************************************** */

    function admin_notification($orders_id) {
        // Stand: 29.04.2009
        global $_GET;
        include(DIR_FS_CATALOG . 'lang/' . $_SESSION['language'] . '/admin/paypal.php');
        $db_installed = false;
        $tables = mysql_list_tables(DB_DATABASE);
        while ($row = mysql_fetch_row($tables)) {
            if ($row[0] == TABLE_PAYPAL)
                $db_installed = true;
        }
        if ($db_installed == false)
            return;
        $query = "SELECT * FROM " . TABLE_PAYPAL . " WHERE xtc_order_id = '" . $orders_id . "'ORDER BY paypal_ipn_id DESC LIMIT 1";
        $query = xtc_db_query($query);
        if (xtc_db_num_rows($query)) {
            $data = xtc_db_fetch_array($query);
            if (substr($data['txn_id'], 0, 6) != "PayPal"):
                $response = $this->GetTransactionDetails($data['txn_id']);
            else:
                $response = array('ACK' => 'PFailure', 'ERROR' => $data['txn_id']);
            endif;
            // show transaction status
            $output = '<tr>
								<td class="main" valign="top"><b>' . TEXT_PAYPAL_PAYMENT . ':</b><br /></td>
								<td class="main" style="border: 1px solid; border-color: #003366; background: #fff;">';
            // show INFO
            if ($response['ACK'] == 'Failure') {
                $output .= '<table width="300">
									<tr>
										<td class="main" colspan="2">' . $this->getErrorDescription($response['L_ERRORCODE0']) . '</td>
									</tr>';
            } elseif ($response['ACK'] == 'PFailure') {
                $output .= '<table width="300">
										<tr>
											<td class="main" colspan="2">' . $response['ERROR'] . '</td>
										</tr>';
            } else {
                // authorization ?
                if ($response['PAYMENTSTATUS'] == 'None' && $response['PENDINGREASON'] == 'other') {
                    $response['PAYMENTSTATUS'] = 'Pending';
                    $response['PENDINGREASON'] = 'authorization';
                    $response['AMT'] = $response['AMT'] . ' ( ' . $data['mc_captured'] . ' Captured) ';
                }
                $output .= '<table width="300">
										<tr>
											<td width="10">' . $this->getStatusSymbol($response['PAYMENTSTATUS'], $response['TRANSACTIONTYPE'], $response['PENDINGREASON']) . '</dt>
											<td class="main">' . $this->getStatusName($response['PAYMENTSTATUS'], $response['TRANSACTIONTYPE']) . ' Total: ' . $response['AMT'] . ' ' . $response['CURRENCYCODE'] . '</td>
										</tr>
										<tr>
											<td width="10">' . $this->getStatusSymbol($response['PAYERSTATUS']) . '</dt>
											<td class="main">' . $response['PAYERSTATUS'] . '(' . $response['EMAIL'] . ')' . '</td>
										</tr>
										<tr>
											<td width="10" valign="top">' . $this->getStatusSymbol($response['ADDRESSSTATUS']) . '</dt>
											<td class="main">(' . $response['ADDRESSSTATUS'] . ')<br>' . $this->mn_iconv("UTF-8", $_SESSION['language_charset'], $response['SHIPTONAME']) . '<br>' . $this->mn_iconv("UTF-8", $_SESSION['language_charset'], $response['SHIPTOSTREET']) . '<br>' . $response['SHIPTOZIP'] . ' ' . $this->mn_iconv("UTF-8", $_SESSION['language_charset'], $response['SHIPTOCITY']) . '<br>' . $this->mn_iconv("UTF-8", $_SESSION['language_charset'], $response['SHIPTOCOUNTRYNAME']) . '</td>
										</tr>
										<tr>
											<td width="10" valign="top">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif') . '</dt>
											<td class="main"><a href="' . xtc_href_link(FILENAME_PAYPAL, 'view=detail&paypal_ipn_id=' . $data['paypal_ipn_id']) . '" target="_blank">' . TEXT_PAYPAL_DETAIL . '</td>
										</tr>';
            }
            $output .= '</table></td>
									</tr>';
            echo $output;
        }
    }

    /*     * ********************************************************** */

    function getErrorDescription($err) {
        // Stand: 29.04.2009
        //return constant(strtoupper($err));
        $err = $_SESSION['reshash']['FORMATED_ERRORS'];
        unset($_SESSION['reshash']['FORMATED_ERRORS']);
        return strtoupper($err);
    }

    /*     * ********************************************************** */

	
}
