<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_products.inc.php 866 2014-03-17 12:07:35Z akausch $
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

require(DIR_FS_CATALOG . 'includes/classes/class.xtcprice.php');

function unserialize_session_data($session_data) {
    $variables = array();
    $a = preg_split("/(\w+)\|/", $session_data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    for ($i = 0; $i < count($a); $i = $i + 2) {
        $variables[$a[$i]] = unserialize($a[$i + 1]);
    }
    return( $variables );
}

function xtc_get_products($session) {
    if (!is_array($session)) {
        return false;
    }

    $products_array = array();
    reset($session);
    if (is_array($session['cart']->contents)) {
        while (list($products_id, ) = each($session['cart']->contents)) {
            $products_query = xtDBquery("SELECT p.products_id, pd.products_name, p.products_image, p.products_model, p.products_price, p.products_discount_allowed, p.products_weight, p.products_tax_class_id FROM " . TABLE_PRODUCTS . " AS p, " . TABLE_PRODUCTS_DESCRIPTION . " AS pd WHERE p.products_id='" . xtc_get_prid($products_id) . "' AND pd.products_id = p.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "';");
            if ($products = xtc_db_fetch_array($products_query)) {
                $prid = $products['products_id'];
                $xtPrice = new xtcPrice($session['currency'], $session['customers_status']['customers_status_id']);
                $products_price = $xtPrice->xtcGetPrice($products['products_id'], $format = false, $session['cart']->contents[$products_id]['qty'], $products['products_tax_class_id'], $products['products_price']);

                $products_array[] = array('id' => $products_id,
                    'name' => $products['products_name'],
                    'model' => $products['products_model'],
                    'image' => $products['products_image'],
                    'price' => $products_price + attributes_price($products_id, $session),
                    'quantity' => $session['cart']->contents[$products_id]['qty'],
                    'weight' => $products['products_weight'],
                    'final_price' => ($products_price + attributes_price($products_id, $session)),
                    'tax_class_id' => $products['products_tax_class_id'],
                    'attributes' => $session['contents'][$products_id]['attributes']);
            }
        }
        return $products_array;
    }
    return false;
}

function attributes_price($products_id, $session) {
    $xtPrice = new xtcPrice($session['currency'], $session['customers_status']['customers_status_id']);
    if (isset($session['contents'][$products_id]['attributes'])) {
        reset($session['contents'][$products_id]['attributes']);
        while (list($option, $value) = each($session['contents'][$products_id]['attributes'])) {
            $attribute_price = xtc_db_fetch_array(xtDBquery("SELECT pd.products_tax_class_id, p.options_values_price, p.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " p, " . TABLE_PRODUCTS . " pd WHERE p.products_id = '" . $products_id . "' AND p.options_id = '" . $option . "' AND pd.products_id = p.products_id AND p.options_values_id = '" . $value . "';"));
            if ($attribute_price['price_prefix'] == '+') {
                $attributes_price += $xtPrice->xtcFormat($attribute_price['options_values_price'], false, $attribute_price['products_tax_class_id']);
            } elseif ($attribute_price['price_prefix'] == '=') {
                $attributes_price = $xtPrice->xtcFormat($attribute_price['options_values_price'], false, $attribute_price['products_tax_class_id']);
            } else {
                $attributes_price -= $xtPrice->xtcFormat($attribute_price['options_values_price'], false, $attribute_price['products_tax_class_id']);
            }
        }
    }
    return $attributes_price;
}
