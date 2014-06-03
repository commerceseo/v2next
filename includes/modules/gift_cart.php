<?php

/* -----------------------------------------------------------------
 * 	$Id: gift_cart.php 928 2014-03-31 13:56:47Z akausch $
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

// GUTSCHEIN EINLÖSEN AUS SESSION
if (isset($_SESSION['gv_id'])) {
    require_once (DIR_FS_INC . 'coupon_mod_functions.php');
    redeem_gv_from_session();
}

// START SMARTY
$gift_smarty = new Smarty;
$gift_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
$gift_smarty->assign('language', $_SESSION['language']);

// PRÜFEN OB SYSTEM AKTIVIERT
if (ACTIVATE_GIFT_SYSTEM == 'true') {
    $gift_smarty->assign('ACTIVATE_GIFT', 'true');
}

// RESTLICHES GUTHABEN ANZEIGEN
if (isset($_SESSION['customer_id'])) {
    require_once (DIR_FS_INC . 'coupon_mod_functions.php');
    $rest_amount = get_rest_amount();
    if ($rest_amount > 0) {
        $gift_smarty->assign('GV_AMOUNT', $xtPrice->xtcFormat($rest_amount, true, 0, true));
        $gift_smarty->assign('GV_SEND_TO_FRIEND_LINK', xtc_href_link(FILENAME_GV_SEND, '', 'SSL'));
    }
}

// WENN EIN KUPON EINGELÖST WURDE
if (isset($_SESSION['cc_id'])) {
    // INFOS ÜBER KUPON AUSLESEN
    $coupon_query = xtc_db_query("SELECT coupon_type, coupon_amount FROM " . TABLE_COUPONS . " WHERE coupon_id = '" . (int) $_SESSION['cc_id'] . "' limit 1");
    $coupon = xtc_db_fetch_array($coupon_query);
    // RABATT TYP
    if ($coupon['coupon_type'] == 'F') {
        $gift_smarty->assign('COUPON_TYPE', COUPON_TYPE_F);
    } elseif ($coupon['coupon_type'] == 'P') {
        $gift_smarty->assign('COUPON_TYPE', COUPON_TYPE_P);
    } elseif ($coupon['coupon_type'] == 'S') {
        $gift_smarty->assign('COUPON_TYPE', COUPON_TYPE_S);
    }
    // RABATT WERT
    if ($coupon['coupon_type'] == 'P') {
        $gift_smarty->assign('COUPON_AMOUNT', round($coupon['coupon_amount'], 2) . ' %');
    } elseif ($coupon['coupon_type'] == 'F') {
        $gift_smarty->assign('COUPON_AMOUNT', $xtPrice->xtcFormat($coupon['coupon_amount'], true));
    }
    // LINK ZU DETAILS
    include (DIR_WS_MODULES . FILENAME_POPUP_COUPON_HELP);
    $gift_smarty->assign('SHOW_COUPON_INFOS', $coupon_infos_popup);
}

$gift_smarty->assign('FORM_ACTION', xtc_draw_form('gift_coupon', xtc_href_link(FILENAME_SHOPPING_CART, 'action=check_gift', 'SSL')));
$gift_smarty->assign('INPUT_CODE', xtc_draw_input_field('gv_redeem_code'));
$gift_smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_redeem.gif', IMAGE_REDEEM_GIFT));
$gift_smarty->assign('FORM_END', '</form>');
$gift_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);

$gift_smarty->caching = false;
$smarty->assign('MODULE_gift_cart', $gift_smarty->fetch(cseo_get_usermod('base/module/gift_cart.html', USE_TEMPLATE_DEVMODE)));
