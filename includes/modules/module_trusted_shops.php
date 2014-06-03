<?php

/* -----------------------------------------------------------------
 * 	$Id: module_trusted_shops.php 522 2013-07-24 11:44:51Z akausch $
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



// configuration for module

$shop_title = '';

// module to reach trusted shop quality (some variables in checkout prozedure!)
$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
$module_smarty->assign('language', $_SESSION['language']);
// ok, select Order Data
$order_query = xtc_db_query("SELECT
                              customers_id,
                              customers_name,
                              customers_company,
                              customers_email_address,
                              customers_street_address,
                              customers_city,
                              customers_postcode,
                              customers_country,
                              customers_telephone,
                              payment_method
                              FROM " . TABLE_ORDERS . "
                              WHERE orders_id='" . (int) $orders['orders_id'] . "'");
$order_data = xtc_db_fetch_array($order_query);


$module_smarty->assign('shop_title', $shop_title);
$module_smarty->assign('KDNR', $order_data['customers_id']);
$module_smarty->assign('ORDERNR', $orders['orders_id']);
$module_smarty->assign('email', $order_data['customers_email_address']);
$module_smarty->assign('first_name', $_SESSION['customer_first_name']);
$module_smarty->assign('last_name', $_SESSION['customer_last_name']);
$module_smarty->assign('street', $order_data['customers_street_address']);
$module_smarty->assign('city', $order_data['customers_city']);
$module_smarty->assign('zip', $order_data['customers_postcode']);
$module_smarty->assign('phone', $order_data['customers_telephone']);
$module_smarty->assign('TrID', TRUSTED_SHOP_NR);

// get ISO code for country
$country_query = xtc_db_query("SELECT
                                countries_iso_code_3
                                from " . TABLE_COUNTRIES . "
                                where countries_name = '" . $order_data['customers_country'] . "'");

$country_data = xtc_db_fetch_array($country_query);
$module_smarty->assign('country', $country_data['countries_iso_code_3']);


/*
  Zuordnung der Zahlungsweisen
  Trusted Shops / XTC

  1 Lastschrift/Bankeinzug   (banktransfer)
  2 Kreditkarte  (cc)
  4 Rechnung  (invoice)
  5 Nachnahme  (cod)
  6 Weitere Zahlungsart  (default	)
  7 Vorauskasse / Р С—РЎвЂ”Р вЂ¦berweisung (moneyorder)
  10 PayPal  (paypal)
  11 Barzahlung bei Abholung  (cash)
  16 Giropay (uos_giropay_modul)


 */


switch ($order_data['payment_method']) {



// 1 Lastschrift/Bankeinzug   (banktransfer)
    case 'banktransfer':
        $payment = 1;
        break;

// 2 Kreditkarte  (cc)
    case 'cc':
        $payment = 2;
        break;

// 4 Rechnung  (invoice)
    case 'invoice':
        $payment = 4;
        break;

// 5 Nachnahme  (cod)
    case 'cod':
        $payment = 5;
        break;

// 7 Vorauskasse / Р С—РЎвЂ”Р вЂ¦berweisung (moneyorder)
    case 'moneyorder':
        $payment = 7;
        break;

// 10 PayPal  (paypal)
    case 'paypal':
        $payment = 10;
        break;

// 11 Barzahlung bei Abholung  (cash)
    case 'cash':
        $payment = 11;
        break;

// 16 Giropay (uos_giropay_modul)
    case 'uos_giropay_modul':
        $payment = 16;
        break;


    default:
        $payment = 6;
}

$module_smarty->assign('payment', $payment);

// select total ammount
$total_query = xtc_db_query("SELECT
                              value
                              FROM " . TABLE_ORDERS_TOTAL . "
                              where
                              orders_id='" . (int) $orders['orders_id'] . "' and
                              class='ot_total'");
$total_data = xtc_db_fetch_array($total_query);
$module_smarty->assign('amount', $total_data['value']);
$module_smarty->assign('curr', $_SESSION['currency']);

$module_smarty->caching = false;
$module_trusted_shops = $module_smarty->fetch(CURRENT_TEMPLATE . '/module/module_trusted_shops.html');

if ("english" == $_SESSION['language']) {
    //$smarty->assign('MODULE_trusted_shops',$module_trusted_shops);
}
if ("french" == $_SESSION['language']) {
    //$smarty->assign('MODULE_trusted_shops',$module_trusted_shops);
} else if ("german" == $_SESSION['language']) {
    $smarty->assign('MODULE_trusted_shops', $module_trusted_shops);
}
// else $smarty->assign('MODULE_trusted_shops',$module_trusted_shops);
?>