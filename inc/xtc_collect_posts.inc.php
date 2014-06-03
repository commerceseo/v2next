<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_collect_posts.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_collect_posts() {
    global $REMOTE_ADDR, $xtPrice;
    if ($_POST['gv_redeem_code']) {
        // ERROR : KEINEN CODE EINGEGEBEN
        if ($_POST['gv_redeem_code'] == '') {
            xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_NO_REDEEM_CODE), 'SSL'));
        }
        // INFOS ZUM GUTSCHEIN / KUPON AUSLESEN
        $gv_query = xtc_db_query("SELECT * FROM " . TABLE_COUPONS . " WHERE coupon_code = '" . $_POST['gv_redeem_code'] . "' AND coupon_active = 'Y' LIMIT 1;");
        $gv_result = xtc_db_fetch_array($gv_query);
        // ERROR : CODE EXISTIERT NICHT
        if (xtc_db_num_rows($gv_query) == 0) {
            xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_NO_INVALID_REDEEM_GV), 'SSL'));
        }

        // BEREICH FUER GUTSCHEINE
        if ($gv_result['coupon_type'] == 'G') {
            // ERROR : GUTSCHEIN BEREITS EINGELOEST
            $redeem_query = xtc_db_query("SELECT * FROM " . TABLE_COUPON_REDEEM_TRACK . " WHERE coupon_id = '" . $gv_result['coupon_id'] . "' LIMIT 1;");
            if (xtc_db_num_rows($redeem_query) != 0) {
                xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_NO_INVALID_REDEEM_GV), 'SSL'));
            }
            // GUTSCHEIN ID IN SESSION SPEICHERN
            $_SESSION['gv_id'] = $gv_result['coupon_id'];
            if (!$_SESSION['gv_id']) {
                $_SESSION['gv_id'] = 'gv_id';
            }
            // ERROR : KUNDE IST NICHT EINGELOGGT, BZW. HAT KEIN KUNDENKONTO
            $customers_query = xtc_db_query("SELECT customers_id FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' LIMIT 1;");
            // $customers = xtc_db_fetch_array($customers_query);
            if (xtc_db_num_rows($customers_query) == 0) {
                xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_GV_LOGIN), 'SSL'));
            }
            // GUTSCHEIN EINLOESEN
            require_once (DIR_FS_INC . 'coupon_mod_functions.php');
            redeem_gv_from_session();
        }
        // BEREICH FUER KUPONS
        if ($gv_result['coupon_type'] != 'G') {
            $teq = 0;
            // KUPON ID IN SESSION SPEICHERN
            $_SESSION['cc_id'] = $gv_result['coupon_id'];
            if (!$_SESSION['cc_id']) {
                $_SESSION['cc_id'] = 'cc_id';
            }
            // ERROR : LAUFZEIT HAT NOCH NICHT BEGONNEN
            if ($gv_result['coupon_start_date'] > date('Y-m-d H:i:s')) {
                unset($_SESSION['cc_id']);
                xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_STARTDATE_COUPON), 'SSL'));
            }
            // ERROR : LAUFZEIT BEENDET
            if ($gv_result['coupon_expire_date'] < date('Y-m-d H:i:s')) {
                unset($_SESSION['cc_id']);
                xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_FINISDATE_COUPON), 'SSL'));
            }
            // ERROR : MINDESTBESTELLWERT NICHT ERREICHT
            if ($gv_result['coupon_minimum_order'] > $_SESSION['cart']->show_total()) {
                xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_MINIMUM_ORDER_COUPON_1 . ' ' . $xtPrice->xtcFormat($gv_result['coupon_minimum_order'], true) . ' ' . ERROR_MINIMUM_ORDER_COUPON_2), 'SSL'));
            }
            // ERROR : GESAMTES VERWENDUNGSLIMIT UEBERSCHRITTEN				
            $coupon_count = xtc_db_query("SELECT coupon_id FROM " . TABLE_COUPON_REDEEM_TRACK . " WHERE coupon_id = '" . $gv_result['coupon_id'] . "'");
            if (xtc_db_num_rows($coupon_count) >= $gv_result['uses_per_coupon'] && $gv_result['uses_per_coupon'] > 0) {
                unset($_SESSION['cc_id']);
                xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_USES_COUPON . $gv_result['uses_per_coupon'] . TIMES), 'SSL'));
            }
            // ERROR : VERWENDUNGSLIMIT FUER EINZELNEN KUNDEN UEBERSCHRITTEN		
            $coupon_count_customer = xtc_db_query("SELECT coupon_id FROM " . TABLE_COUPON_REDEEM_TRACK . " WHERE coupon_id = '" . $gv_result['coupon_id'] . "' AND customer_id = '" . (int) $_SESSION['customer_id'] . "'");
            if (xtc_db_num_rows($coupon_count_customer) >= $gv_result['uses_per_user'] && $gv_result['uses_per_user'] > 0) {
                unset($_SESSION['cc_id']);
                xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_USES_USER_COUPON . $gv_result['uses_per_user'] . TIMES2), 'SSL'));
            }
            // WEITERLEITUNG ZUM WARENKORB NACH ERFOLGREICHEM EINLOESEN DES KUPONS
            xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(REDEEMED_COUPON), 'SSL'));
        }

        // ERROR : KEINEN CODE EINGEGEBEN
    } elseif (!$_POST['gv_redeem_code']) {
        xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_NO_REDEEM_CODE), 'SSL'));
    }
}
