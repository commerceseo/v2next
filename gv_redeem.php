<?php

/* -----------------------------------------------------------------
 * 	$Id: gv_redeem.php 420 2013-06-19 18:04:39Z akausch $
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

require ('includes/application_top.php');

// WENN GUTSCHEIN_SYSTEM DEAKTIVIERT, DANN WEITERLEITEN
if (ACTIVATE_GIFT_SYSTEM != 'true') {
    xtc_redirect(FILENAME_DEFAULT);
}

// ERROR : KEINEN CODE EINGEGEBEN
if ($_GET['gv_no'] == '') {
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_NO_REDEEM_CODE), 'SSL'));
    $error = true;
}


//////////////////////////////////////////////////////////////////
// KONTROLLE VOR EINLÖSEN
//////////////////////////////////////////////////////////////////
if (isset($_GET['gv_no'])) {

    // INFOS KUPON / GUTSCHEIN AUSLESEN
    $coupon_query = xtc_db_query("select * from " . TABLE_COUPONS . " where coupon_code = '" . xtc_db_input($_GET['gv_no']) . "'");
    $coupon = xtc_db_fetch_array($coupon_query);

    // ERROR : CODE EXISTIERT NICHT
    if (xtc_db_num_rows($coupon_query) == 0) {
        xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_NO_INVALID_REDEEM_GV), 'SSL'));
    }

    //////////////////////////////////////////////////////////////////
    // BEREICH GUTSCHEINE
    //////////////////////////////////////////////////////////////////
    if ($coupon['coupon_type'] == 'G') {

        // ERROR : GUTSCHEIN BEREITS 
        $redeem_query = xtc_db_query("select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $coupon['coupon_id'] . "'");
        if (xtc_db_num_rows($redeem_query) != 0) {
            xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_NO_INVALID_REDEEM_GV), 'SSL'));
        }

        // GUTSCHEIN ID IN SESSION SPEICHERN
        $_SESSION['gv_id'] = $coupon['coupon_id'];
        if (!isset($_SESSION['gv_id'])) {
            $_SESSION['gv_id'] = 'gv_id';
        }

        // ERROR : KUNDE IST NICHT EINGELOGGT, BZW. HAT KEIN KUNDENKONTO
        $customers_query = xtc_db_query("select customers_id from " . TABLE_CUSTOMERS . " where customers_id = '" . (int) $_SESSION['customer_id'] . "'");
        $customers = xtc_db_fetch_array($customers_query);
        if (xtc_db_num_rows($customers_query) == 0) {
            xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_GV_LOGIN), 'SSL'));
        }

        // WEITERLEITUNG ZUM WARENKORB WENN KEINE FEHLER 
        xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
    }


    //////////////////////////////////////////////////////////////////
    // BEREICH FÜR KUPONS
    //////////////////////////////////////////////////////////////////
    if ($coupon['coupon_type'] != 'G') {

        // KUPON ID IN SESSION SPEICHERN
        $_SESSION['cc_id'] = $coupon['coupon_id'];
        if (!isset($_SESSION['cc_id'])) {
            $_SESSION['cc_id'] = 'cc_id';
        }

        // ERROR : LAUFZEIT HAT NOCH NICHT BEGONNEN
        if ($coupon['coupon_start_date'] > date('Y-m-d H:i:s')) {
            xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_STARTDATE_COUPON), 'SSL'));
        }

        // ERROR : LAUFZEIT BEENDET
        if ($coupon['coupon_expire_date'] < date('Y-m-d H:i:s')) {
            xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_FINISDATE_COUPON), 'SSL'));
        }

        // ERROR : MINDESTBESTELLWERT NICHT ERREICHT
        if ($coupon['coupon_minimum_order'] > $_SESSION['cart']->show_total()) {
            xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_MINIMUM_ORDER_COUPON_1 . ' ' . $xtPrice->xtcFormat($coupon['coupon_minimum_order'], true) . ' ' . ERROR_MINIMUM_ORDER_COUPON_2), 'SSL'));
        }

        // ERROR : GESAMTES VERWENDUNGSLIMIT			
        $coupon_count = xtc_db_query("SELECT coupon_id FROM " . TABLE_COUPON_REDEEM_TRACK . " WHERE coupon_id = '" . (int) $coupon['coupon_id'] . "'");
        if (xtc_db_num_rows($coupon_count) >= $coupon['uses_per_coupon'] && $coupon['uses_per_coupon'] > 0) {
            xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_USES_COUPON . $coupon['uses_per_coupon'] . TIMES), 'SSL'));
        }

        // ERROR : VERWENDUNGSLIMIT FÜR EINZELNEN KUNDEN	
        $coupon_count_customer = xtc_db_query("SELECT coupon_id FROM " . TABLE_COUPON_REDEEM_TRACK . " WHERE coupon_id = '" . (int) $coupon['coupon_id'] . "' AND customer_id = '" . (int) $_SESSION['customer_id'] . "'");
        if (xtc_db_num_rows($coupon_count_customer) >= $coupon['uses_per_user'] && $coupon['uses_per_user'] > 0) {
            xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_USES_USER_COUPON . $coupon['uses_per_user'] . TIMES2), 'SSL'));
        }

        if ($coupon['restrict_to_products'] != '') {
            $products = $_SESSION['cart']->get_products();
            for ($i = 0; $i < sizeof($products); $i++) {
                $product_found = false;
                $t_prid = xtc_get_prid($products[$i]['id']);
                $pr_ids = explode(",", $coupon['restrict_to_products']);
                for ($ii = 0; $ii < sizeof($pr_ids); $ii++) {
                    if ($t_prid == $pr_ids[$ii]) {
                        $product_found = true;
                    } else {
                        if ($ii + 1 == sizeof($pr_ids) && $product_found == false) {
                            xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_PRODUCT_COUPON), 'SSL'));
                        }
                    }
                }
            }
        }

        // ERROR : KATEGORIEN
        if ($coupon['restrict_to_categories'] != '') {
            $products = $_SESSION['cart']->get_products();
            for ($i = 0; $i < sizeof($products); $i++) {
                $product_found = false;
                $t_prid = xtc_get_prid($products[$i]['id']);
                $cat_ids = explode(",", $coupon['restrict_to_categories']);
                for ($ii = 0; $ii < sizeof($cat_ids); $ii++) {
                    // NACH UNTERKATEGORIEN SUCHEN
                    $subcategories_array = array();
                    require_once (DIR_FS_INC . 'xtc_get_subcategories.inc.php');
                    xtc_get_subcategories($subcategories_array, $cat_ids[$ii]);
                    // WENN UNTERKATEGORIEN EXISTIEREN
                    if (sizeof($subcategories_array) > 0) {
                        for ($iii = 0; $iii < sizeof($subcategories_array); $iii++) {
                            $cat_query = xtc_db_query("SELECT products_id FROM products_to_categories WHERE products_id = '" . $t_prid . "' AND (categories_id = '" . $cat_ids[$ii] . "' OR categories_id = '" . $subcat_ids[$iii] . "')");
                            // PASSENDER ARTIKEL GEFUNDEN
                            if (xtc_db_num_rows($cat_query) != 0) {
                                $product_found = true;
                                // KEINEN PASSENDEN ARTIKEL GEFUNDEN
                            } else {
                                if ($ii + 1 == sizeof($cat_ids) && $product_found == false) {
                                    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_CATEGORIE_COUPON), 'SSL'));
                                }
                            }
                        }
                        // KEINE UNTERKATEGORIEN VORHANDEN
                    } else {
                        $cat_query = xtc_db_query("SELECT products_id FROM products_to_categories WHERE products_id = '" . $t_prid . "' AND categories_id = '" . $cat_ids[$ii] . "'");
                        if (xtc_db_num_rows($cat_query) != 0) {
                            $product_found = true;
                        } else {
                            if ($ii + 1 == sizeof($cat_ids) && $product_found == false) {
                                xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_CATEGORIE_COUPON), 'SSL'));
                            }
                        }
                    }
                }
            }
        }

        xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(REDEEMED_COUPON), 'SSL'));
    }
}
