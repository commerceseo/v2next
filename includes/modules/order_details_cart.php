<?php

/* -----------------------------------------------------------------
 * 	$Id: order_details_cart.php 1264 2014-11-10 09:17:15Z akausch $
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
//Wegen Versandkosten im Warenkorb neu setzen!
unset($_SESSION['shipping']);

require_once (DIR_FS_INC . 'xtc_check_stock.inc.php');
require_once (DIR_FS_INC . 'xtc_format_price.inc.php');
require_once (DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
require_once (DIR_FS_INC . 'xtc_check_stock_special.inc.php');
require_once (DIR_FS_INC . 'xtc_get_cart_description.inc.php');
require_once (DIR_FS_INC . 'xtc_get_short_description.inc.php');
require_once (DIR_FS_INC . 'xtc_get_long_description.inc.php');

$coo_lang_file_master = cseohookfactory::create_object('LanguageTextManager', array(), true);
// $coo_properties_control = cseohookfactory::create_object('PropertiesControl');

// $t_products_quantity_array = array();
// for ($i = 0, $n = sizeof($products); $i < $n; $i ++) {
    // $t_combis_id = $coo_properties_control->extract_combis_id($products[$i]['id']);
    // $t_extracted_products_id = xtc_get_prid($products[$i]['id']);
    // $coo_products = cseohookfactory::create_object('GMDataObject', array('products', array('products_id' => $t_extracted_products_id)));
    // $use_properties_combis_quantity = $coo_products->get_data_value('use_properties_combis_quantity');
    // if ($use_properties_combis_quantity == 1 || ($use_properties_combis_quantity == 0 && ATTRIBUTE_STOCK_CHECK == 'false' && STOCK_CHECK == 'true')) {
        // $t_products_quantity_array[$t_extracted_products_id] += $products[$i]['quantity'];
    // }
// }

foreach ($t_products_quantity_array as $t_product_id => $t_product_quantity) {
    $t_mark_stock = xtc_check_stock($t_product_id, $t_product_quantity);
    if ($t_mark_stock) {
        $t_products_quantity_array[$t_product_id] = $t_mark_stock;
        $_SESSION['any_out_of_stock'] = 1;
    } else {
        unset($t_products_quantity_array[$t_product_id]);
    }
}

function xtc_check_minorder($products_id, $products_quantity) {
    $query = xtc_db_query("SELECT products_minorder FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $products_id . "';");
    if (xtc_db_num_rows($query) == 0) {
        return;
    } else {
        $value = xtc_db_fetch_array($query);
        if ($value['products_minorder'] > $products_quantity) {
            return array('minorder' => $value['products_minorder'], 'mark' => '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>');
        }
    }
}

function xtc_check_maxorder($products_id, $products_quantity) {
    $query = xtc_db_query("SELECT products_maxorder FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $products_id . "' AND products_maxorder > 0;");
    if (xtc_db_num_rows($query) == 0) {
        return;
    } else {
        $value = xtc_db_fetch_array($query);
        if ($value['products_maxorder'] < $products_quantity) {
            return array('maxorder' => $value['products_maxorder'], 'mark' => '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>');
        }
    }
}

$module_content = array();
$any_out_of_stock = '';
$mark_stock = '';
$minorder = array();
$maxorder = array();

for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
    #properties
    // $t_combis_id = $coo_properties_control->extract_combis_id($products[$i]['id']);
    // if ($t_combis_id == '') {
        // if (STOCK_CHECK == 'true') {
            // $mark_stock = xtc_check_stock($products[$i]['id'], $products[$i]['quantity']);
            // if ($mark_stock) {
                // $_SESSION['any_out_of_stock'] = 1;
            // }
        // }
    // }
    // if ($t_combis_id != '') {
        // $t_properties_html = $coo_properties_control->get_properties_combis_details($t_combis_id, $_SESSION['languages_id']);
        // $coo_products = cseohookfactory::create_object('GMDataObject', array('products', array('products_id' => $products[$i]['id'])));
        // $use_properties_combis_quantity = $coo_products->get_data_value('use_properties_combis_quantity');

        // if ($use_properties_combis_quantity == 1 || ($use_properties_combis_quantity == 0 && ATTRIBUTE_STOCK_CHECK == 'false' && STOCK_CHECK == 'true')) {
            // # check article quantity
            // $mark_stock = xtc_check_stock($products[$i]['id'], $products[$i]['quantity']);
            // if ($mark_stock) {
                // $_SESSION['any_out_of_stock'] = 1;
            // }
        // } else if (($use_properties_combis_quantity == 0 && ATTRIBUTE_STOCK_CHECK == 'true' && STOCK_CHECK == 'true') || $use_properties_combis_quantity == 2) {
            // # check combis quantity
            // $t_properties_stock = $coo_properties_control->get_properties_combis_quantity($t_combis_id);
            // if ($t_properties_stock < $products[$i]['quantity']) {
                // $_SESSION['any_out_of_stock'] = 1;
                // $mark_stock = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
            // }
        // }

        // if (array_key_exists($t_extracted_products_id, $t_products_quantity_array)) {
            // $mark_stock = $t_products_quantity_array[$t_extracted_products_id];
        // }

        // if ($coo_products->get_data_value('use_properties_combis_weight') == 1) {
            // $t_products_weight = $coo_properties_control->get_properties_combis_weight($t_combis_id);
        // }

        // if ($coo_products->get_data_value('use_properties_combis_shipping_time') == 1) {
            // $t_shipping_time = $coo_properties_control->get_properties_combis_shipping_time($t_combis_id);
        // }

        // $t_combi_model = $coo_properties_control->get_properties_combis_model($t_combis_id);

        // if (APPEND_PROPERTIES_MODEL == "true") {
            // # Artikelnummer (Kombi) an Artikelnummer (Artikel) anhängen
            // if ($t_products_model != '' && $t_combi_model != '') {
                // $t_products_model = $t_products_model . '-' . $t_combi_model;
            // } else if ($t_combi_model != '') {
                // $t_products_model = $t_combi_model;
            // }
        // } else {
            // # Artikelnummer (Artikel) durch Artikelnummer (Kombi) ersetzen
            // if ($t_combi_model != '') {
                // $t_products_model = $t_combi_model;
            // }
        // }
    // } else {
        // $t_properties_html = '';
    // }
	$t_properties_html = '';
	#properties


    if (STOCK_CHECK == 'true') {
        $mark_stock = xtc_check_stock($products[$i]['id'], $products[$i]['quantity']);
        if ($mark_stock)
            $_SESSION['any_out_of_stock'] = 1;
    }
    $mark_minorder = xtc_check_minorder($products[$i]['id'], $products[$i]['quantity']);
    if ($mark_minorder['mark']) {
        $minorder[] = array('name' => $products[$i]['name'], 'minorder' => $mark_minorder['minorder']);
    }
    $mark_maxorder = xtc_check_maxorder($products[$i]['id'], $products[$i]['quantity']);
    if ($mark_maxorder['mark']) {
        $maxorder[] = array('name' => $products[$i]['name'], 'maxorder' => $mark_maxorder['maxorder']);
    }
    if (STOCK_CHECK == 'true') {
        $mark_special_stock = xtc_check_stock_special($products[$i]['id'], $products[$i]['quantity']);
        if ($mark_special_stock)
            $_SESSION['any_out_of_stock'] = 1;
    }
    $image = '';
    if ($products[$i]['image'] != '') {
        $image = DIR_WS_MINI_IMAGES . $products[$i]['image'];
    } else {
        $image = DIR_WS_THUMBNAIL_IMAGES . 'no_img.jpg';
    }

    $attributes_exist = ((isset($products[$i]['attributes'])) ? 1 : 0);
    $freitext_exist = ((isset($products[$i]['freitext'])) ? 1 : 0);

    if (xtc_get_cart_description($products[$i]['id']) != '') {
        $description = xtc_get_cart_description($products[$i]['id']);
    } elseif (CHECKOUT_SHOW_DESCRIPTION == 'true') {
        $description = cseo_truncate(strip_tags(xtc_get_short_description($products[$i]['id'])), CHECKOUT_SHOW_DESCRIPTION_LENG);
    } else {
        $description = cseo_truncate(strip_tags(xtc_get_long_description($products[$i]['id'])), CHECKOUT_SHOW_DESCRIPTION_LENG);
    }

    if ($attributes_exist == 1) {
        $product_shipping = '';
    } else {
        $main = new main($products[$i]['id']);
        $pshipping_time = xtc_db_fetch_array(xtc_db_query("SELECT products_shippingtime FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $products[$i]['id'] . "';"));
        $product_shipping = $products[$i]['shipping_time'] . $main->getShippingStatusInfoLinkActive($pshipping_time['products_shippingtime']);
    }

    $module_content[$i] = array('PRODUCTS_NAME' => $products[$i]['name'] . $mark_stock . $mark_minorder['mark'] . $mark_maxorder['mark'] . $mark_special_stock,
        'PRODUCTS_QTY' => xtc_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'size="2"') . xtc_draw_hidden_field('products_id[]', $products[$i]['id']) . xtc_draw_hidden_field('old_qty[]', $products[$i]['quantity']),
        'QTY' => $products[$i]['quantity'],
        'PRODUCTS_MODEL' => $products[$i]['model'],
        'PRODUCTS_VPE' => $products[$i]['vpe'],
        'PRODUCTS_SHIPPING_TIME' => $product_shipping,
        'PRODUCTS_TAX' => number_format($products[$i]['tax_class_id'], TAX_DECIMAL_PLACES),
        'PRODUCTS_IMAGE' => $image,
        'PRODUCTS_POS' => $i + 1,
        'IMAGE_ALT' => $products[$i]['name'],
        'BOX_DELETE' => xtc_draw_checkbox_field('cart_delete[]', $products[$i]['id']),
        'DEL_LINK' => xtc_href_link(FILENAME_SHOPPING_CART, xtc_get_all_get_params() . 'del=' . $products[$i]['id']),
        'PLUS_LINK' => xtc_href_link(FILENAME_SHOPPING_CART, xtc_get_all_get_params() . 'plus=' . $products[$i]['id']),
        'MINUS_LINK' => xtc_href_link(FILENAME_SHOPPING_CART, xtc_get_all_get_params() . 'minus=' . $products[$i]['id']),
        'PRODUCTS_LINK' => xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($products[$i]['id'], $products[$i]['name'])),
        'PRODUCTS_PRICE' => $xtPrice->xtcFormat($products[$i]['price'] * $products[$i]['quantity'], true),
        'PRODUCTS_SINGLE_PRICE' => $xtPrice->xtcFormat($products[$i]['p_single_price'], true),
        'PRODUCTS_SHORT_DESCRIPTION' => $description,
        'PROPERTIES' => $t_properties_html,
        'ATTRIBUTES' => '');
    // Product options names

    $count_attr_value = '';
    if ($attributes_exist == 1) {
        reset($products[$i]['attributes']);
        $price_sum = '';
        //Check Rabatt Attribute
        while (list ($option, $value) = each($products[$i]['attributes'])) {
            if (ATTRIBUTE_STOCK_CHECK == 'true' && STOCK_CHECK == 'true') {
                $attribute_stock_check = xtc_check_stock_attributes($products[$i][$option]['products_attributes_id'], $products[$i]['quantity']);
                if ($attribute_stock_check)
                    $_SESSION['any_out_of_stock'] = 1;
            }
            if ($products[$i][$option]['attributes_shippingtime'] > 0) {
                $main = new main($products[$i][$option]['attributes_shippingtime']);
                $attr_shipping = $main->getShippingStatusName($products[$i][$option]['attributes_shippingtime']) . $main->getShippingStatusInfoLinkActive($products[$i][$option]['attributes_shippingtime']);
            } else {
                $main = new main($products[$i]['id']);
                $pshipping_time = xtc_db_fetch_array(xtc_db_query("SELECT products_shippingtime FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $products[$i]['id'] . "';"));
                $attr_shipping = $products[$i]['shipping_time'] . $main->getShippingStatusInfoLinkActive($pshipping_time['products_shippingtime']);
            }
            $price = $products[$i][$option]['options_values_price'];

            if ($products[$i][$option]['products_options_name'] != 'Downloads') {
                if ($products[$i][$option]['options_values_scale_price'] != '') {
                    $scale_price = $xtPrice->calculate_optionscale($products[$i][$option]['options_values_price'], $products[$i][$option]['options_values_scale_price'], $products[$i]['quantity']);
                    $price_sum += $scale_price;
                    $price_sum = $xtPrice->xtcFormat($price_sum, true, $products[$i]['tax_class_id']);
                } else {
                    $price_sum = $xtPrice->xtcFormat($products[$i][$option]['options_values_price'], true, $products[$i]['tax_class_id']);
                }
            } else {
                $price_sum = '';
            }
            $module_content[$i]['ATTRIBUTES'][] = array('ID' => $products[$i][$option]['products_attributes_id'],
                'MODEL' => xtc_get_attributes_model(xtc_get_prid($products[$i]['id']), $products[$i][$option]['products_options_values_name'], $products[$i][$option]['products_options_name']),
                'NAME' => $products[$i][$option]['products_options_name'],
                'ATTR_SHIPPING' => $attr_shipping,
                'ATTR_QTY' => ($products[$i][$option]['products_options_name'] != 'Downloads') ? $products[$i]['quantity'] . 'x' : '',
                'PRICE' => $price_sum,
                'PREFIX' => ($products[$i][$option]['products_options_name'] != 'Downloads') ? $products[$i][$option]['price_prefix'] : '',
                'VALUE_NAME' => $products[$i][$option]['products_options_values_name'] . $attribute_stock_check);

            $count_attr_value += $products[$i][$option]['options_values_price'];
        }
    }

    if ($freitext_exist == 1) {
        reset($products[$i]['freitext']);
        $price_sum = '';
        //Check Rabatt Attribute
        while (list ($option, $value) = each($products[$i]['freitext'])) {
            if ($products[$i][$option]['attributes_shippingtime'] > 0) {
                $main = new main($products[$i][$option]['attributes_shippingtime']);
                $attr_shipping = $main->getShippingStatusName($products[$i][$option]['attributes_shippingtime']) . $main->getShippingStatusInfoLinkActive($products[$i][$option]['attributes_shippingtime']);
            } else {
                $main = new main($products[$i]['id']);
                $pshipping_time = xtc_db_fetch_array(xtc_db_query("SELECT products_shippingtime FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $products[$i]['id'] . "';"));
                $attr_shipping = $products[$i]['shipping_time'] . $main->getShippingStatusInfoLinkActive($pshipping_time['products_shippingtime']);
            }
            $price = $products[$i][$option]['options_values_price'];

            //Freitext
            $module_content[$i]['ATTRIBUTES'][] = array('ID' => $products[$i][$option]['products_attributes_id'],
                'MODEL' => xtc_get_attributes_model(xtc_get_prid($products[$i]['id']), $products[$i][$option]['products_options_values_name'], $_SESSION['cart_freitext'][$i_]['freitext']),
                'NAME' => $products[$i][$option]['products_options_name'],
                'ATTR_SHIPPING' => $attr_shipping,
                'ATTR_QTY' => ($products[$i][$option]['products_options_name'] != 'Downloads') ? $products[$i]['quantity'] . 'x' : '',
                'PRICE' => ($products[$i][$option]['products_options_name'] != 'Downloads') ? $xtPrice->xtcFormat($products[$i][$option]['options_values_price'] * $products[$i]['quantity'], true, $products[$i]['tax_class_id']) : '',
                'PREFIX' => ($products[$i][$option]['products_options_name'] != 'Downloads') ? $products[$i][$option]['price_prefix'] : '',
                'VALUE_NAME' => $products[$i][$option]['options_values_id']);
            $count_attr_value += $products[$i][$option]['options_values_price'];
        }
    }
}

if (sizeof($minorder) > 0) {
    $_SESSION['any_out_of_minorder_products'] = $minorder;
} else {
    unset($_SESSION['any_out_of_minorder_products']);
}

if (sizeof($maxorder) > 0) {
    $_SESSION['any_out_of_maxorder_products'] = $maxorder;
} else {
    unset($_SESSION['any_out_of_maxorder_products']);
}

$total_content = '';
$total = $_SESSION['cart']->show_total();
$total_netto = $_SESSION['cart']->show_total();

// Gratisartikel
unset($_SESSION['gratisart']);
require(DIR_FS_INC . 'specials_gratis.inc.php');

$special_gratis = array();
$special_gratis = getspecial_gratis();

if (is_array($special_gratis)) {
    $_SESSION['gratisartikel'] = getspecial_gratis();
    $module_smarty->assign('gratis_mitbestellen', xtc_draw_hidden_field('mitbestellen'));
    $checked = true;
    $module_smarty->assign('gratisart', count($_SESSION['gratisartikel']));
    $module_smarty->assign('gratis_name', $special_gratis->specials_gratis_description);
}
// Gratisartikel Ende

if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1 && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00') {
    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
        $price = $total - $_SESSION['cart']->show_tax(false);
    } else {
        $price = $total;
    }
    $discount = $xtPrice->xtcGetDC($price, $_SESSION['customers_status']['customers_status_ot_discount']);
    $total_content = '<div class="ot_total">' . $_SESSION['customers_status']['customers_status_ot_discount'] . '% ' . SUB_TITLE_OT_DISCOUNT . ' -' . xtc_format_price($discount, $price_special = 1, $calculate_currencies = false) . '</div>';
}

// Kupon-Rabatt Anzeige
if (isset($_SESSION['cc_id'])) {
    require_once (DIR_FS_INC . 'coupon_mod_functions.php');
    $coupon_deduction = calculate_deduction();
    if ($coupon_deduction[1] > '0' && $coupon_deduction[0] != '1') {
        $total_content .= SUB_TITLE_OT_COUPON . ' -' . $xtPrice->xtcFormat($coupon_deduction[1], true) . '<br />';
    }
}

if ($_SESSION['cart']->show_weight() > 0) {
    $total_content .= '<div class="ot_total_netto">' . WEIGHT . ' : ' . $_SESSION['cart']->show_weight() . ' kg</div>';
}

if ($_SESSION['customers_status']['customers_status_show_price'] == '1') {
    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) {
        $total -= $discount;
    } elseif ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
        $total -= $discount;
    } else {
        $total -= $discount;
    }
    if ($coupon_deduction[1] > '0' && $coupon_deduction[0] != '1') {
        $total -= $coupon_deduction[2];
    }
    $netto = $total_netto - round($_SESSION['cart']->show_tax(false), TAX_DECIMAL_PLACES);
    //Schweizer Rundung
    if (STORE_COUNTRY == '22' || STORE_COUNTRY == '204') {
        $netto = round($netto * 20, 0) / 20;
        $total = round($total * 20, 0) / 20;
    }

    if (MODULE_ORDER_TOTAL_UST_FREE_STATUS == 'true') {
        $module_smarty->assign('TOTAL_CONTENT_NETTO', '<div class="ot_total_netto">' . TAX_INFO_INCL . '<br>' . '</div>');
    } else {
        if (DISPLAY_TAX != 'false') {
            $module_smarty->assign('TOTAL_CONTENT_NETTO', '<div class="ot_total_netto">' . WK_NETTO . ': ' . $xtPrice->xtcFormat($netto, true) . '</div>');
        }
    }

    $total_content .= '<div class="ot_total">' . SUB_TITLE_SUB_TOTAL . $xtPrice->xtcFormat($total, true) . '</div>';
} else {
    $total_content .= '<div class="ot_total">' . NOT_ALLOWED_TO_SEE_PRICES . '</div>';
}

// display only if there is an ot_discount
if ($customer_status_value['customers_status_ot_discount'] != 0) {
    $total_content .= TEXT_CART_OT_DISCOUNT . $customer_status_value['customers_status_ot_discount'] . '%';
}

if ($_SESSION['customers_status']['customers_status_show_price'] == '1') {
    if (MODULE_ORDER_TOTAL_UST_FREE_STATUS != 'true') {
        if (DISPLAY_TAX != 'false') {
            $module_smarty->assign('UST_CONTENT', '<div class="ot_tax">' . $_SESSION['cart']->show_tax() . '</div>');
        }
    }
}

if (SHOW_SHIPPING == 'true') {
    $query = xtc_db_query("SELECT countries_id FROM " . TABLE_COUNTRIES . " WHERE status = '1';");
    if (xtc_db_num_rows($query) == 1) {
        $shipping_info = '';
    } else {
        $store_country = xtc_db_fetch_array(xtc_db_query("SELECT countries_name FROM " . TABLE_COUNTRIES . " WHERE countries_id = '" . STORE_COUNTRY . "';"));
        $shipping_info = SHIPPING_AUSLAND_CART1 . ' ' . $store_country['countries_name'] . ' <a title="' . SHIPPING_COSTS . '" class="shipping" href="' . xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . SHIPPING_INFOS, $request_type = 'SSL' ? 'SSL' : 'NONSSL') . '"> ' . SHIPPING_AUSLAND_CART2 . '</a>';
    }
    $module_smarty->assign('SHIPPING_INFO', $shipping_info);
}

// Versandkosten im Warenkorb
include DIR_FS_CATALOG . 'includes/modules/shipping_estimate.php';
if (is_array($special_gratis)) {
    $module_smarty->assign('special_gratis', $special_gratis);
}
$module_smarty->assign('TOTAL_CONTENT', $total_content);
if (file_exists(DIR_WS_INCLUDES . 'addons/order_details_cart_addon.php')) {
    include (DIR_WS_INCLUDES . 'addons/order_details_cart_addon.php');
}
$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$module_smarty->assign('module_content', $module_content);

$module_smarty->caching = false;

$module = $module_smarty->fetch(cseo_get_usermod('base/module/order_details.html', USE_TEMPLATE_DEVMODE));

$smarty->assign('MODULE_order_details', $module);
