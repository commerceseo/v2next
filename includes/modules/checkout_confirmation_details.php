<?php

/* -----------------------------------------------------------------
 * 	$Id: checkout_confirmation_details.php 964 2014-04-10 13:16:54Z akausch $
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

$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
require_once (DIR_FS_INC . 'xtc_get_attributes_model.inc.php');

$module_content = array();
$any_out_of_stock = '';
$mark_stock = '';

for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
    $image = '';
    $products_image_query = xtc_db_query("SELECT products_image FROM products WHERE products_id = '" . $order->products[$i]['id'] . "'");
    $products_image = xtc_db_fetch_array($products_image_query);

    if ((isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0)) {
        for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j++) {
			// Freitext_module
			if($order->products[$i]['attributes'][$j]['value'] == 'Freitext') {
				for($i_=0; $i_ < sizeof($_SESSION['cart_freitext'][$order->products[$i]['id']]);$i_++) {
					if($order->products[$i]['id'] == $_SESSION['cart_freitext'][$order->products[$i]['id']][$i_]['product_id']) {
						$module_content[$i]['ATTRIBUTES'][] .= '<div>&nbsp;<em> - ' .$order->products[$i]['attributes'][$j]['option'].': '.$_SESSION['cart_freitext'][$order->products[$i]['id']][$i_]['freitext'].'</em></div>';
					}
				}
			} elseif($order->products[$i]['attributes'][$j]['value'] == 'Freitext1') {
				for($i_=0; $i_ < sizeof($_SESSION['cart_freitext'][$order->products[$i]['id']]);$i_++) {
					if($order->products[$i]['id'] == $_SESSION['cart_freitext'][$order->products[$i]['id']][$i_]['product_id']) {
						$module_content[$i]['ATTRIBUTES'][] .= '<div>&nbsp;<em> - ' .$order->products[$i]['attributes'][$j]['option'].': '.$_SESSION['cart_freitext'][$order->products[$i]['id']][$i_]['freitext1'].'</em></div>';
					}
				}
			} elseif($order->products[$i]['attributes'][$j]['value'] == 'Freitext2') {
				for($i_=0; $i_ < sizeof($_SESSION['cart_freitext'][$order->products[$i]['id']]);$i_++) {
					if($order->products[$i]['id'] == $_SESSION['cart_freitext'][$order->products[$i]['id']][$i_]['product_id']) {
						$module_content[$i]['ATTRIBUTES'][] .= '<div>&nbsp;<em> - ' .$order->products[$i]['attributes'][$j]['option'].': '.$_SESSION['cart_freitext'][$order->products[$i]['id']][$i_]['freitext2'].'</em></div>';
					}
				}
			} else {		
            $module_content[$i]['ATTRIBUTES'][] .= '<div>&nbsp;<em> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</em></div>';
			}
		
		 }
	}

    if ($products_image['products_image'] != '') {
        $img = DIR_WS_MINI_IMAGES . $products_image['products_image'];
    } else {
        $img = DIR_WS_MINI_IMAGES . 'no_img.jpg';
    }

    if ((isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0)) {
        for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j++) {
            $shipping_time = $main->getShippingStatusName($order->products[$i]['attributes'][$j]['attributes_shippingtime']);
        }
    } else {
        $shipping_time = $order->products[$i]['shipping_time'];
    }

    $module_content[$i] = array(
        'PRODUCTS_NAME' => '<a href="' . xtc_href_link('checkout_product_info.php', xtc_product_link($order->products[$i]['id'], $order->products[$i]['name'])) . '" class="shipping">' . $order->products[$i]['name'] . '</a>' . $mark_stock,
        'PRODUCTS_QTY' => $order->products[$i]['qty'] . 'x',
        'PRODUCTS_VPE' => $order->products[$i]['vpe'],
        'PRODUCTS_SHORT_DESCRIPTION' => $order->products[$i]['products_short_description'],
        'PRODUCTS_MODEL' => $order->products[$i]['model'],
        'PRODUCTS_SHIPPING_TIME' => $shipping_time,
        'PRODUCTS_IMAGE' => $img,
        'IMAGE_ALT' => $order->products[$i]['name'],
        'ATTRIBUTES' => array(),
        'PRODUCTS_PRICE' => $xtPrice->xtcFormat($order->products[$i]['final_price'], true),
        'PRODUCTS_SINGLE_PRICE' => $order->products[$i]['price_formated']);

    if ((isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0)) {
        for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j++) {
			if($order->products[$i]['attributes'][$j]['value'] == 'Freitext') {
				$vals .= array ('ID' => $order->products[$i][$option]['products_attributes_id'], 
								'MODEL' => xtc_get_attributes_model(xtc_get_prid($order->products[$i]['id']), $order->products[$i][$option]['products_options_values_name'],$order->products[$i][$option]['products_options_name']), 
								'NAME' => $order->products[$i]['attributes'][$j]['option'], 
								'VALUE_NAME' => $_SESSION['cart_freitext'][$order->products[$i]['id']][$i]['freitext']);
			} elseif($order->products[$i]['attributes'][$j]['value'] == 'Freitext1') {
				$vals .= array ('ID' => $order->products[$i][$option]['products_attributes_id'], 
								'MODEL' => xtc_get_attributes_model(xtc_get_prid($order->products[$i]['id']), $order->products[$i][$option]['products_options_values_name'],$order->products[$i][$option]['products_options_name']), 
								'NAME' => $order->products[$i]['attributes'][$j]['option'], 
								'VALUE_NAME' => $_SESSION['cart_freitext'][$order->products[$i]['id']][$i]['freitext1']);
			} elseif($order->products[$i]['attributes'][$j]['value'] == 'Freitext2') {
				$vals .= array ('ID' => $order->products[$i][$option]['products_attributes_id'], 
								'MODEL' => xtc_get_attributes_model(xtc_get_prid($order->products[$i]['id']), $order->products[$i][$option]['products_options_values_name'],$order->products[$i][$option]['products_options_name']), 
								'NAME' => $order->products[$i]['attributes'][$j]['option'], 
								'VALUE_NAME' => $_SESSION['cart_freitext'][$order->products[$i]['id']][$i]['freitext2']);
			} else {
				$vals = array ('ID' => $order->products[$i][$option]['products_attributes_id'], 
								'MODEL' => xtc_get_attributes_model(xtc_get_prid($order->products[$i]['id']), $order->products[$i][$option]['products_options_values_name'],$order->products[$i][$option]['products_options_name']), 
								'NAME' => $order->products[$i]['attributes'][$j]['option'], 
								'VALUE_NAME' => $order->products[$i]['attributes'][$j]['value']);
			}
			$module_content[$i]['ATTRIBUTES'][] = $vals;
        }
    }
}

$module_smarty->assign('module_content', $module_content);
$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$module_smarty->caching = false;
$module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/checkout_confirmation_details.html', USE_TEMPLATE_DEVMODE));

$smarty->assign('MODULE_checkout_confirmation_details', $module);
