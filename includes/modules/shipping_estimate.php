<?php

/* -----------------------------------------------------------------
 * 	$Id: shipping_estimate.php 1188 2014-09-09 18:30:47Z akausch $
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

require_once (DIR_FS_INC . 'xtc_get_country_list.inc.php');
require_once (DIR_WS_CLASSES . 'class.shipping.php');

$order = new order();
$total = $_SESSION['cart']->show_total();

if (isset($_SESSION['cc_id'])) {
    require_once (DIR_FS_INC . 'coupon_mod_functions.php');
    $coupon_deduction = calculate_deduction();
    if ($coupon_deduction[1] > '0' && $coupon_deduction[0] != '1') {
        $total -= $xtPrice->xtcFormat($coupon_deduction[1], true);
    }
}

$selected = isset($_SESSION['customer_country_id']) ? $_SESSION['customer_country_id'] : STORE_COUNTRY;
if (sizeof(xtc_get_countriesList()) > 1) {
    if (isset($_SESSION['country'])) {
        $selected = $_SESSION['country'];
    } else {
        $selected = STORE_COUNTRY;
    }
    $module_smarty->assign('SELECT_COUNTRY', xtc_get_country_list(array('name' => 'country'), $selected, 'onchange="this.form.submit()"'));
}

if (!isset($order->delivery['country']['iso_code_2']) || $order->delivery['country']['iso_code_2'] == '') {
    $delivery_zone = xtc_db_fetch_array(xtc_db_query("SELECT countries_id, countries_iso_code_2, countries_name FROM " . TABLE_COUNTRIES . " WHERE countries_id = " . $selected));
    $order->delivery['country']['iso_code_2'] = $delivery_zone['countries_iso_code_2'];
    $order->delivery['country']['title'] = $delivery_zone['countries_name'];
    $order->delivery['country']['id'] = $delivery_zone['countries_id'];
}

$_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];
$shipping = new shipping;
$quotes = $shipping->quote();


$free_shipping = $free_shipping_freeamount = false;
if (defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true')) {
    switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
        case 'national' :
            if ($order->delivery['country']['id'] == STORE_COUNTRY)
                $pass = true;
            break;
        case 'international' :
            if ($order->delivery['country']['id'] != STORE_COUNTRY)
                $pass = true;
            break;
        case 'both' :
            $pass = true;
            break;
        default :
            $pass = false;
            break;
    }

    if (($pass == true) && ($total >= $xtPrice->xtcFormat(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, false, 0, true))) {
        $free_shipping = true;
    }
}
$has_freeamount = false;
foreach ($quotes as $quote) {
    if ($quote['id'] == 'freeamount') {
        $has_freeamount = true;
        if (isset($quote['methods'])) {
            $free_shipping_freeamount = true;
            break;
        }
    }
}

$isvirtual = $_SESSION['cart']->get_content_type();
if ($isvirtual == 'virtual') {
$virtualproduct = true;
}

include_once (DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/order_total/ot_shipping.php');

$shipping_content = array();
if ($free_shipping == true) {
    $shipping_content[] = array(
        'NAME' => FREE_SHIPPING_TITLE,
        'VALUE' => $xtPrice->xtcFormat(0, true, 0, true)
    );
} elseif ($virtualproduct) {
    $shipping_content[] = array(
        'NAME' => CHECKOUT_TEXT_VIRTUAL,
        'VALUE' => $xtPrice->xtcFormat(0, true, 0, true)
    );
} elseif ($free_shipping_freeamount) {
    $shipping_content[] = array(
        'NAME' => $quote['module'] . (($quote['methods'][0]['title'] == '') ? '' : ' (') . $quote['methods'][0]['title'] . (($quote['methods'][0]['title'] == '') ? '' : ')'),
        'VALUE' => $xtPrice->xtcFormat(0, true, 0, true)
    );
} else {
    if ($has_freeamount) {
        $module_smarty->assign('FREE_SHIPPING_INFO', sprintf(FREE_SHIPPING_DESCRIPTION, $xtPrice->xtcFormat(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, true, 0, true)));
    }
    $i = 0;
	//Beginn von Modul Versandsperre
	for($i = 0, $n = count($order->products);$i < $n; $i++ ) {
		$id = $order->products[$i]['id'];
		$forbidden_shipping_query = xtc_db_query("SELECT products_forbidden_shipping FROM " . TABLE_PRODUCTS . " WHERE products_id='$id' ");
		if($i == '0') {
			$forbidden_shipping_data = xtc_db_fetch_array($forbidden_shipping_query);
		} else  {
			$puffer = xtc_db_fetch_array($forbidden_shipping_query);
			if($puffer['products_forbidden_shipping'] != '') {
				$forbidden_shipping_data['products_forbidden_shipping'] .= "|";
				$forbidden_shipping_data['products_forbidden_shipping'] .= $puffer['products_forbidden_shipping'];
			}
		}
	}
	$forbidden_shipping_data = explode("|",$forbidden_shipping_data['products_forbidden_shipping']);
	$n = sizeof($quotes);
	foreach($forbidden_shipping_data AS $forbidden_shipping) {
		for ($i = 0; $i <= $n; $i++) {
			$name = explode('.', $forbidden_shipping);
			if($quotes[$i]['id'] == $name[0]) {
				unset($quotes[$i]);
			}
		}
	}

	//Ende von Modul Versandsperre
    foreach ($quotes AS $quote) {
        if ($quote['id'] != 'freeamount') {
            $total += ((isset($quote['tax']) && $quote['tax'] > 0) ? $xtPrice->xtcAddTax($quote['methods'][0]['cost'], $quote['tax']) : (!empty($quote['methods'][0]['cost']) ? $xtPrice->xtcCalculateCurr($quote['methods'][0]['cost']) : '0'));
            if ($_SESSION['customers_status']['customers_status_show_price_tax'] == '1') {
                $shipping_content[$i] = array('NAME' => $quote['module'] . (($quote['methods'][0]['title'] == '') ? '' : ' (') . $quote['methods'][0]['title'] . (($quote['methods'][0]['title'] == '') ? '' : ')'),
                    'VALUE' => $xtPrice->xtcFormat(((isset($quote['tax']) && $quote['tax'] > 0) ? $xtPrice->xtcAddTax($quote['methods'][0]['cost'], $quote['tax']) : (!empty($quote['methods'][0]['cost']) ? $xtPrice->xtcCalculateCurr($quote['methods'][0]['cost']) : '0')), true)
                );
            } else {
                $shipping_content[$i] = array('NAME' => $quote['module'] . (($quote['methods'][0]['title'] == '') ? '' : ' (') . $quote['methods'][0]['title'] . (($quote['methods'][0]['title'] == '') ? '' : ')'),
                    'VALUE' => $xtPrice->xtcFormat(((!empty($quote['methods'][0]['cost']) ? $xtPrice->xtcCalculateCurr($quote['methods'][0]['cost']) : '0')), true)
                );
            }
            $i++;
        }
    }
}

if (MODULE_ORDER_TOTAL_SPERRGUT_STATUS == 'true') {
	include_once (DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/order_total/ot_sperrgut.php');
	$sperrgut_qty = 0;
	$sperrgut_costs = 0;
	for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
		$t = xtc_db_fetch_array(xtc_db_query('SELECT products_sperrgut FROM products WHERE products_id = ' . (int) $order->products[$i]['id']));
		if ($t['products_sperrgut'] > 0) {
			$sperrgut_qty += $order->products[$i]['qty'];
			$sperrgut_costs += ($order->products[$i]['qty'] * constant('SHIPPING_SPERRGUT_' . $t['products_sperrgut']));
		}
	}
	if ($sperrgut_qty > 0) {
		$tax_class = MODULE_ORDER_TOTAL_SPERRGUT_TAX_CLASS;
		$include_tax = MODULE_ORDER_TOTAL_SPERRGUT_INC_TAX;
		$tax_rate = xtc_get_tax_rate($tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
		$tax_desc = xtc_get_tax_description($tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
		if ($include_tax == 'false') {
			$sperrgut_costs = $sperrgut_costs;
		} else {
			$sperrgut_costs = $xtPrice->xtcAddTax($sperrgut_costs, $quote['tax']);
		}
	}
	if ($sperrgut_costs > 0) {
	$shipping_content[500] = array('NAME' => $sperrgut_qty . ' x ' . MODULE_ORDER_TOTAL_SPERRGUT_TITLE,
		'VALUE' => $xtPrice->xtcFormat($sperrgut_costs, true));
	}
}


$module_smarty->assign('shipping_content', $shipping_content);
$module_smarty->assign('COUNTRY', $order->delivery['country']['title']);
$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);

if (count($shipping_content) <= 1) {
    $module_smarty->assign('total', $xtPrice->xtcFormat($total, true));
}
