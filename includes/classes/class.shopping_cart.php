<?php

/* -----------------------------------------------------------------
 * 	$Id: class.shopping_cart.php 1466 2015-07-22 20:26:42Z akausch $
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

require_once (DIR_FS_INC . 'xtc_create_random_value.inc.php');
require_once (DIR_FS_INC . 'xtc_get_prid.inc.php');
require_once (DIR_FS_INC . 'xtc_image_submit.inc.php');
require_once (DIR_FS_INC . 'xtc_get_tax_description.inc.php');
require_once (DIR_FS_INC . 'check_allowed_amount.inc.php');

class shoppingCart_ORIGINAL {

    var $contents, $total, $weight, $cartID, $content_type;

    public function __construct() {
        $this->reset();
    }

    function shoppingCart_ORIGINAL() {
        $this->reset();
    }

    function restore_contents() {

        if (!isset($_SESSION['customer_id'])) {
            return false;
        }

        if (is_array($this->contents)) {
            reset($this->contents);
            while (list ($products_id, ) = each($this->contents)) {
                $qty = $this->contents[$products_id]['qty'];
                $product_query = xtc_db_query("SELECT products_id FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND products_id = '" . $products_id . "'");
                if (!xtc_db_num_rows($product_query)) {
                    xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) VALUES ('" . (int) $_SESSION['customer_id'] . "', '" . $products_id . "', '" . (int) $qty . "', '" . date('Ymd') . "')");
                    if (isset($this->contents[$products_id]['attributes'])) {
                        reset($this->contents[$products_id]['attributes']);
                        while (list ($option, $value) = each($this->contents[$products_id]['attributes'])) {
                            xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id) VALUES ('" . (int) $_SESSION['customer_id'] . "', '" . $products_id . "', '" . (int) $option . "', '" . (int) $value . "')");
                        }
                    }
                    // if (isset($this->contents[$products_id]['properties'])) {
                    // reset($this->contents[$products_id]['properties']);
                    // xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_BASKET_PROPERTIES . " (customers_id, products_id, properties_id) VALUES ('" . (int) $_SESSION['customer_id'] . "', '" . $products_id . "', '" . (int) $this->contents[$products_id]['properties'] . "')");
                    // }
                    if (isset($this->contents[$products_id]['freitext'])) {
                        reset($this->contents[$products_id]['freitext']);
                        while (list ($option, $value) = each($this->contents[$products_id]['freitext'])) {
                            while (list ($foption, $fvalue) = each($value)) {
                                xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id, products_option_ft) VALUES ('" . (int) $_SESSION['customer_id'] . "', '" . $products_id . "', '" . $option . "', $foption, '" . $fvalue . "')");
                            }
                        }
                    }
                } else {
                    xtc_db_query("UPDATE " . TABLE_CUSTOMERS_BASKET . " SET customers_basket_quantity = '" . $qty . "' WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND products_id = '" . $products_id . "'");
                }
            }
        }

        // reset per-session cart contents, but not the database contents
        $this->reset(false);

        $products_query = xtc_db_query("SELECT products_id, customers_basket_quantity FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "'");

        while ($products = xtc_db_fetch_array($products_query)) {
            $t_gm_products_id = xtc_get_prid($products['products_id']);
            $t_gm_check_status = xtc_db_query("SELECT products_status FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $t_gm_products_id . "'");
            if (xtc_db_num_rows($t_gm_check_status) == 1) {
                $t_gm_status = xtc_db_fetch_array($t_gm_check_status);
                if ($t_gm_status['products_status'] == 1) {
                    $this->contents[$products['products_id']] = array('qty' => $products['customers_basket_quantity']);
                    // properties
                    // $properties_query = xtc_db_fetch_array(xtc_db_query("SELECT properties_id FROM " . TABLE_CUSTOMERS_BASKET_PROPERTIES . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND products_id = '" . xtc_db_input($products['products_id']) . "'"));
                    // $this->contents[$products['products_id']]['properties'] = $properties_query['properties_id'];
                    // attributes
                    $attributes_query = xtc_db_query("SELECT * FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND products_id = '" . xtc_db_input($products['products_id']) . "'");
                    while ($attributes = xtc_db_fetch_array($attributes_query)) {
                        if ($attributes['products_option_ft'] == '') {
                            $this->contents[$products['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
                        } else {
                            $this->contents[$products['products_id']]['freitext'][$attributes['products_options_id']] = array($attributes['products_options_value_id'] => $attributes['products_option_ft']);
                        }
                    }
                    // assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
                    $this->cartID = $this->generate_cart_id();
                } else {
                    $this->remove($products['products_id']);
                }
            }
        }

        $this->cleanup();
    }

    function reset($reset_database = false) {
        $this->contents = array();
        $this->total = 0;
        $this->tax = 0;
        $this->weight = 0;
        $this->content_type = false;

        if (isset($_SESSION['customer_id']) && ($reset_database == true)) {
            xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "'");
            xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "'");
        }

        unset($this->cartID);
        if (isset($_SESSION['cartID'])) {
            unset($_SESSION['cartID']);
        }
    }

    function add_cart2($products_id, $attributes = '') {
        if (isset($_SESSION['customer_id'])) {
            xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) VALUES ('" . (int) $_SESSION['customer_id'] . "', '" . $products_id . "', '" . (int) $qty . "', '" . date('Ymd') . "')");
        }
        if (is_array($attributes)) {
            reset($attributes);
            while (list ($option, $value) = each($attributes)) {
                while (list ($foption, $fvalue) = each($value)) {
                    $this->contents[$products_id]['freitext'][$option] = array($foption => $fvalue);
                    if (isset($_SESSION['customer_id']))
                        xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id, products_option_ft) VALUES ('" . (int) $_SESSION['customer_id'] . "', '" . $products_id . "', '" . (int) $option . "', '" . $foption . "', '" . $fvalue . "')");
                }
            }
        }
    }

    function add_cart($products_id, $qty = '1', $attributes = '', $notify = true, $p_products_properties_combis_id = 0) {
        global $new_products_id_in_cart;

        $products_id = xtc_get_uprid($products_id, $attributes);
        $qty = check_allowed_amount($products_id, $qty);
        if ($notify == true) {
            $_SESSION['new_products_id_in_cart'] = $products_id;
        }

        if ($this->in_cart($products_id)) {
            $this->update_quantity($products_id, $qty, $attributes, $c_products_properties_combis_id);
        } else {
            $this->contents[] = array($products_id);
            $this->contents[$products_id] = array('qty' => $qty);
            if (isset($_SESSION['customer_id'])) {
                xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_BASKET . " 
								(customers_id, products_id, customers_basket_quantity, customers_basket_date_added) 
							VALUES 
								('" . $_SESSION['customer_id'] . "', '" . $products_id . "', '" . $qty . "', '" . date('Ymd') . "')");
            }
            if (is_array($attributes)) {
                $query_res = xtc_db_fetch_array(xtc_db_query("SELECT products_options_values_id FROM products_options_values WHERE products_options_values_name= 'Freitext' AND language_id= " . (int) $_SESSION['languages_id'] . ";"));
                $query_res1 = xtc_db_fetch_array(xtc_db_query("SELECT products_options_values_id FROM products_options_values WHERE products_options_values_name= 'Freitext1' AND language_id= " . (int) $_SESSION['languages_id'] . ";"));
                $query_res2 = xtc_db_fetch_array(xtc_db_query("SELECT products_options_values_id FROM products_options_values WHERE products_options_values_name= 'Freitext2' AND language_id= " . (int) $_SESSION['languages_id'] . ";"));
                reset($attributes);
                while (list ($option, $value) = each($attributes)) {
                    if ($query_res['products_options_values_id'] != $value && $query_res1['products_options_values_id'] != $value && $query_res2['products_options_values_id'] != $value) {
                        $this->contents[$products_id]['attributes'][$option] = $value;
                    }
                    // insert into database
                    if (isset($_SESSION['customer_id']) && $query_res['products_options_values_id'] != $value && $query_res1['products_options_values_id'] != $value && $query_res2['products_options_values_id'] != $value)
                        xtc_db_query("INSERT INTO 
						" . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " 
						(customers_id, 
						products_id, 
						products_options_id, 
						products_options_value_id) 
						VALUES 
						('" . $_SESSION['customer_id'] . "', 
						'" . $products_id . "', 
						'" . $option . "', 
						'" . $value . "')");
                }
            }
        }
        $this->cleanup();
        $this->cartID = $this->generate_cart_id();
    }

    function update_quantity($products_id, $quantity = '', $attributes = '', $c_products_properties_combis_id = 0) {
        if (empty($quantity)) {
            return true;
        }
        $this->contents[$products_id] = array('qty' => $quantity);
        if (isset($_SESSION['customer_id'])) {
            xtc_db_query("UPDATE 
								" . TABLE_CUSTOMERS_BASKET . " 
							SET 
								customers_basket_quantity = '" . $quantity . "' 
								WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' 
					AND products_id = '" . $products_id . "'");
        }

        if (is_array($attributes)) {
            reset($attributes);
            while (list ($option, $value) = each($attributes)) {
                $this->contents[$products_id]['attributes'][$option] = $value;
                if (isset($_SESSION['customer_id'])) {
                    xtc_db_query("UPDATE 
					" . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " 
					SET 
					products_options_value_id = '" . (int) $value . "' 
					WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' 
					AND products_id = '" . (int) $products_id . "' 
					AND products_options_id = '" . (int) $option . "'");
                }
            }
        }

        $this->cartID = $this->generate_cart_id();
    }

    function cleanup() {
        reset($this->contents);
        while (list ($key, ) = each($this->contents)) {
            if ($this->contents[$key]['qty'] < 1) {
                unset($this->contents[$key]);
                if (isset($_SESSION['customer_id'])) {
                    xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND products_id = '" . $key . "'");
                    xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND products_id = '" . $key . "'");
                }
            }
        }
    }

    function count_contents() {
        // get total number of items in cart
        $total_items = 0;
        if (is_array($this->contents)) {
            reset($this->contents);
            while (list ($products_id, ) = each($this->contents)) {
                $total_items += $this->get_quantity($products_id);
            }
        }

        return $total_items;
    }

    function get_quantity($products_id) {
        if (isset($this->contents[$products_id])) {
            return $this->contents[$products_id]['qty'];
        } else {
            return 0;
        }
    }

    function in_cart($products_id) {
        if (isset($this->contents[$products_id])) {
            return true;
        } else {
            return false;
        }
    }

    function remove($products_id) {
        $this->contents[$products_id] = NULL;
        if (isset($_SESSION['customer_id'])) {
            xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND products_id = '" . $products_id . "'");
            xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "' AND products_id = '" . $products_id . "'");
        }
        $this->cleanup();
        $this->cartID = $this->generate_cart_id();
    }

    function remove_all() {
        $this->reset();
    }

    function get_product_id_list() {
        $product_id_list = '';
        if (is_array($this->contents)) {
            reset($this->contents);
            while (list ($products_id, ) = each($this->contents)) {
                $product_id_list .= ', ' . $products_id;
            }
        }
        return substr($product_id_list, 2);
    }

    function calculate() {
        global $xtPrice;
        $this->total = 0;
        $this->weight = 0;
        $this->tax = array();
        $this->total_discount = array();
        if (!is_array($this->contents)) {
            return 0;
        }
        reset($this->contents);
        while (list ($products_id) = each($this->contents)) {
            $qty = $this->contents[$products_id]['qty'];
            // products price
            $product_query = xtc_db_query("SELECT products_id, 
												products_price, 
												products_discount_allowed, 
												products_tax_class_id, 
												products_weight 
												FROM " . TABLE_PRODUCTS . " 
												WHERE products_id='" . xtc_get_prid($products_id) . "'");
            if ($product = xtc_db_fetch_array($product_query)) {
                $products_price = $xtPrice->xtcGetPrice($product['products_id'], $format = false, $qty, $product['products_tax_class_id'], $product['products_price']);
                $this->total += $products_price * $qty;
                $this->weight += ($qty * $product['products_weight']);
                $attribute_price = 0;
                if (isset($this->contents[$products_id]['attributes'])) {
                    reset($this->contents[$products_id]['attributes']);
                    while (list ($option, $value) = each($this->contents[$products_id]['attributes'])) {
                        $values = $xtPrice->xtcGetOptionPrice($product['products_id'], $option, $value, $products_price);
                        $this->weight += $values['weight'] * $qty;
                        if ($values['scale_price'] != '') {
                            $scale_price = $xtPrice->calculate_optionscale($values['price'], $values['scale_price'], $qty);
                            $scale_price_tax = $xtPrice->xtcFormat($scale_price, false, $product['products_tax_class_id']);
                            $this->total += $scale_price_tax * $qty;
                            $attribute_price += $scale_price_tax;
                        } else {
                            $this->total += $values['price'] * $qty;
                            $attribute_price += $values['price'];
                        }
                    }
                }

                if ($product['products_tax_class_id'] != 0) {
                    if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1 && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00') {
                        $products_price_tax = $products_price - ($products_price / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
                        $attribute_price_tax = $attribute_price - ($attribute_price / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
                    }
                    $products_tax = $xtPrice->TAX[$product['products_tax_class_id']];
                    $products_tax_description = xtc_get_tax_description($product['products_tax_class_id']);
                    // price incl tax
                    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == '1') {
                        if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1 && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00') {
                            $this->tax[$product['products_tax_class_id']]['value'] += ((($products_price_tax + $attribute_price_tax) / (100 + $products_tax)) * $products_tax) * $qty;
                            $this->tax[$product['products_tax_class_id']]['desc'] = TAX_ADD_TAX . $products_tax_description;
                        } else {
                            $this->tax[$product['products_tax_class_id']]['value'] += ((($products_price + $attribute_price) / (100 + $products_tax)) * $products_tax) * $qty;
                            $this->tax[$product['products_tax_class_id']]['desc'] = TAX_ADD_TAX . $products_tax_description;
                        }
                    }
                    // excl tax + tax at checkout
                    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
                        if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1 && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00') {
                            $this->tax[$product['products_tax_class_id']]['value'] += (($products_price_tax + $attribute_price_tax) / 100) * ($products_tax) * $qty;
                            $this->total_discount[$product['products_tax_class_id']]+=(($products_price_tax + $attribute_price_tax) / 100) * ($products_tax) * $qty;
                            $this->tax[$product['products_tax_class_id']]['desc'] = TAX_NO_TAX . $products_tax_description;
                        } else {
                            $this->tax[$product['products_tax_class_id']]['value'] += (($products_price + $attribute_price) / 100) * ($products_tax) * $qty;
                            $this->total_discount[$product['products_tax_class_id']]+=(($products_price + $attribute_price) / 100) * ($products_tax) * $qty;
                            $this->tax[$product['products_tax_class_id']]['desc'] = TAX_NO_TAX . $products_tax_description;
                        }
                    }
                }
            }
        }
        foreach ($this->total_discount as $value) {
            $this->total+=round($value, $xtPrice->get_decimal_places($order->info['currency']));
        }
    }

    function attributes_price_scale($products_id, $att_quantity, $taxid, $products_price) {
        global $xtPrice;
        if (isset($this->contents[$products_id]['attributes'])) {
            reset($this->contents[$products_id]['attributes']);
            while (list ($option, $value) = each($this->contents[$products_id]['attributes'])) {
                $values = $xtPrice->xtcGetOptionPrice($products_id, $option, $value, $products_price);
                if ($values['scale_price'] != '') {
                    $attributes_scale_price = $xtPrice->calculate_optionscale($values['price'], $values['scale_price'], $att_quantity);
                    $attributes_scale_price = $xtPrice->xtcFormat($attributes_scale_price, false, $taxid);
                    $attributes_price += $attributes_scale_price;
                } else {
                    $attributes_price += $values['price'];
                }
            }
        }
        return $attributes_price;
    }

    function attributes_price($products_id, $products_price) {
        global $xtPrice;
        if (isset($this->contents[$products_id]['attributes'])) {
            reset($this->contents[$products_id]['attributes']);
            while (list ($option, $value) = each($this->contents[$products_id]['attributes'])) {
                $values = $xtPrice->xtcGetOptionPrice($products_id, $option, $value, $products_price);
                $attributes_price += $values['price'];
            }
        }
        return $attributes_price;
    }

    function get_products() {
        global $xtPrice, $main;
        if (!is_array($this->contents))
            return false;
        $products_array = array();
        $test = '';
        reset($this->contents);
        while (list ($products_id, ) = each($this->contents)) {
            if ($this->contents[$products_id]['qty'] != 0 || $this->contents[$products_id]['qty'] != '') {
                $products_query = xtc_db_query("SELECT 
												p.*, 
												pd.products_name,
												pd.products_description,
												pd.products_short_description
											FROM " . TABLE_PRODUCTS . " AS p
											JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON(pd.products_id = p.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
											WHERE p.products_id='" . xtc_get_prid($products_id) . "';");

                if ($products = xtc_db_fetch_array($products_query)) {
                    $prid = $products['products_id'];
                    $products_price = $xtPrice->xtcGetPrice($products['products_id'], $format = false, $this->contents[$products_id]['qty'], $products['products_tax_class_id'], $products['products_price']);
                    if ($products['products_vpe_status'] == 1 && $products['products_vpe_value'] != 0.0 && $products_price > 0) {
                        require_once (DIR_FS_INC . 'xtc_get_vpe_name.inc.php');
                        $vpe = $xtPrice->xtcFormat($products_price * (1 / $products['products_vpe_value']), true) . TXT_PER . xtc_get_vpe_name($products['products_vpe']);
                    } else {
                        $vpe = '';
                    }
                    if (isset($this->contents[$products_id]['attributes'])) {
                        reset($this->contents[$products_id]['attributes']);
                        while (list ($option, $value) = each($this->contents[$products_id]['attributes'])) {
                            $products_options = xtc_db_fetch_array(xtDBquery("SELECT 
																*
															FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
															WHERE products_id = '" . $products['products_id'] . "'
															AND options_values_id = '" . $value . "'
															AND attributes_vpe_status = '1'
															AND attributes_vpe_value > 0;"));

                            if ($products_options['attributes_vpe_status'] == '1' && $products_options['attributes_vpe_value'] != 0.00) {
                                if ($products_options['price_prefix'] == '+') {
                                    $vpe_price = $products['products_price'] + $products_options['options_values_price'];
                                } elseif ($products_options['price_prefix'] == '-') {
                                    $vpe_price = $products['products_price'] - $products_options['options_values_price'];
                                } elseif ($products_options['price_prefix'] == '=') {
                                    $vpe_price = $products_options['options_values_price'];
                                }
                                $vpe_price = $xtPrice->xtcAddTax(($vpe_price * (1 / $products_options['attributes_vpe_value'])), $xtPrice->TAX[$products['products_tax_class_id']]);
                                require_once (DIR_FS_INC . 'xtc_get_vpe_name.inc.php');
                                $vpe = $xtPrice->xtcFormat($vpe_price, true) . TXT_PER . xtc_get_vpe_name($products_options['attributes_vpe']);
                            }
                        }
                    }
                    if (isset($this->contents[$products_id]['freitext'])) {
                        reset($this->contents[$products_id]['freitext']);
                        while (list ($option, $value) = each($this->contents[$products_id]['freitext'])) {
                            $products_options = xtc_db_fetch_array(xtDBquery("SELECT 
																*
															FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
															WHERE products_id = '" . $products['products_id'] . "'
															AND options_values_id = '" . $value . "'
															AND attributes_vpe_status = '1'
															AND attributes_vpe_value > 0;"));

                            if ($products_options['attributes_vpe_status'] == '1' && $products_options['attributes_vpe_value'] != 0.00) {
                                if ($products_options['price_prefix'] == '+') {
                                    $vpe_price = $products['products_price'] + $products_options['options_values_price'];
                                } elseif ($products_options['price_prefix'] == '-') {
                                    $vpe_price = $products['products_price'] - $products_options['options_values_price'];
                                } elseif ($products_options['price_prefix'] == '=') {
                                    $vpe_price = $products_options['options_values_price'];
                                }
                                $vpe_price = $xtPrice->xtcAddTax(($vpe_price * (1 / $products_options['attributes_vpe_value'])), $xtPrice->TAX[$products['products_tax_class_id']]);
                                require_once (DIR_FS_INC . 'xtc_get_vpe_name.inc.php');
                                $vpe = $xtPrice->xtcFormat($vpe_price, true) . TXT_PER . xtc_get_vpe_name($products_options['attributes_vpe']);
                            }
                        }
                    }
                    if (ACTIVATE_SHIPPING_STATUS == 'true') {
                        $main = new main();
                        $shipping_time_active = $main->getShippingStatusName($products['products_shippingtime']);
                    }

                    $products_array[] = array('id' => $products_id,
                        'name' => $products['products_name'],
                        'shipping_time' => $shipping_time_active,
                        'products_discount_allowed' => $products['products_discount_allowed'],
                        'image' => $products['products_image'],
                        'model' => $products['products_model'],
                        'price' => ($products_price + $this->attributes_price_scale($products_id, $this->contents[$products_id]['qty'], $products['products_tax_class_id'], $products_price)),
                        'p_single_price' => ($products_price + $this->attributes_price_scale($products_id, $this->contents[$products_id]['qty'], $products['products_tax_class_id'], $products_price)),
                        'quantity' => $this->contents[$products_id]['qty'],
                        'weight' => $products['products_weight'],
                        'vpe' => $vpe,
                        'final_price' => ($products_price + $this->attributes_price_scale($products_id, $this->contents[$products_id]['qty'], $products['products_tax_class_id'], $products_price)),
                        'tax_class_id' => $products['products_tax_class_id'],
                        'attributes' => $this->contents[$products_id]['attributes'],
                        'freitext' => $this->contents[$products_id]['freitext'],
                        'product_type' => $products['product_type']
                    );
                }
            }
        }
        return $products_array;
    }

    function show_total() {
        $this->calculate();
        return $this->total;
    }

    function show_weight() {
        $this->calculate();
        return $this->weight;
    }

    function show_tax($format = true) {
        global $xtPrice;
        $this->calculate();
        $output = "";
        $val = 0;
        $gval = 0;

        foreach ($this->tax as $key => $value) {
            if ($this->tax[$key]['value'] > 0) {
                $taxvalue = $this->tax[$key]['value'];
                if (STORE_COUNTRY == '22' || STORE_COUNTRY == '204') {
                    $taxvalue = round($taxvalue * 20, 0) / 20;
                }
                $output .= $this->tax[$key]['desc'] . ": " . $xtPrice->xtcFormat($taxvalue, true) . '<br />';
                $gval+=$this->tax[$key]['value'];
            }
        }
        if ($format) {
            return $output;
        } else {
            return $gval;
        }
    }

    function generate_cart_id($length = 5) {
        return xtc_create_random_value($length, 'digits');
    }

    function get_content_type() {
        $this->content_type = false;

        if ((DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0)) {
            reset($this->contents);
            while (list ($products_id, ) = each($this->contents)) {
                if (isset($this->contents[$products_id]['attributes'])) {
                    reset($this->contents[$products_id]['attributes']);
                    while (list (, $value) = each($this->contents[$products_id]['attributes'])) {
                        $virtual_check_query = xtc_db_query("select count(*) as total from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad where pa.products_id = '" . $products_id . "' and pa.options_values_id = '" . $value . "' and pa.products_attributes_id = pad.products_attributes_id");
                        $virtual_check = xtc_db_fetch_array($virtual_check_query);

                        if ($virtual_check['total'] > 0) {
                            switch ($this->content_type) {
                                case 'physical' :
                                    $this->content_type = 'mixed';
                                    return $this->content_type;
                                    break;

                                default :
                                    $this->content_type = 'virtual';
                                    break;
                            }
                        } else {
                            switch ($this->content_type) {
                                case 'virtual' :
                                    $this->content_type = 'mixed';
                                    return $this->content_type;
                                    break;

                                default :
                                    $this->content_type = 'physical';
                                    break;
                            }
                        }
                    }
                } else {
                    switch ($this->content_type) {
                        case 'virtual' :
                            $this->content_type = 'mixed';
                            return $this->content_type;
                            break;

                        default :
                            $this->content_type = 'physical';
                            break;
                    }
                }
            }
        } else {
            $this->content_type = 'physical';
        }
        return $this->content_type;
    }

    function unserialize($broken) {
        for (reset($broken); $kv = each($broken);) {
            $key = $kv['key'];
            if (gettype($this->$key) != "user function")
                $this->$key = $kv['value'];
        }
    }

    // GV Code Start
    function count_contents_virtual() {
        // get total number of items in cart disregard gift vouchers
        $total_items = 0;
        if (is_array($this->contents)) {
            reset($this->contents);
            while (list ($products_id, ) = each($this->contents)) {
                $no_count = false;
                $gv_query = xtc_db_query("select products_model from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
                $gv_result = xtc_db_fetch_array($gv_query);
                if (preg_match('/^GIFT/', $gv_result['products_model'])) {
                    $no_count = true;
                }
                if (NO_COUNT_ZERO_WEIGHT == 1) {
                    $gv_query = xtc_db_query("select products_weight from " . TABLE_PRODUCTS . " where products_id = '" . xtc_get_prid($products_id) . "'");
                    $gv_result = xtc_db_fetch_array($gv_query);
                    if ($gv_result['products_weight'] <= MINIMUM_WEIGHT) {
                        $no_count = true;
                    }
                }
                if (!$no_count)
                    $total_items += $this->get_quantity($products_id);
            }
        }
        return $total_items;
    }

    //GV Code End
    // free shipping start - new code

    function in_cart_fs($p_id) {
        $product_id_list = ', ';
        if (is_array($this->contents)) {
            reset($this->contents);
            while (list ($products_id, ) = each($this->contents)) {
                $product_id_list = ', ' . $products_id . '{';
                $string_needle = ', ' . $p_id . '{';
                if (substr_count($product_id_list, $string_needle) != '0') {
                    return $products_id;
                }
            }
            return false;
        }
    }

    function get_quantity_fs($p_id) {
        if (is_array($this->contents)) {
            reset($this->contents);
            if (substr_count($p_id, '{') == '0') {
                return $this->get_quantity($p_id);
            } else {
                $p_quantity = 0;
                $p_root_id = substr($p_id, 0, strpos($p_id, '{'));
                while (list ($products_id, ) = each($this->contents)) {
                    $product_id_list = ', ' . $products_id . '{';
                    $string_needle = ', ' . $p_root_id . '{';
                    if (substr_count($product_id_list, $string_needle) != '0') {
                        $p_quantity += $this->get_quantity($products_id);
                    }
                }
                return $p_quantity;
            }
        }
    }

    // free shipping - end of code

    function get_attributes_from_id($var) {
        if (!is_numeric($var)) {
            $attributes = explode('{', substr($var, strpos($var, '{') + 1));
            foreach ($attributes as $element) {
                list($key, $value) = explode('}', trim($element));
                $attributes_array[$key] = $value;
            }
        } else {
            $attributes_array = "";
        }
        return $attributes_array;
    }

    function restoreCustomersCart($customers_id) {
        //Admin Function Recover Cart
        $this->reset(false);

        $products_query = xtc_db_query("SELECT products_id, customers_basket_quantity FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id = '" . (int) $customers_id . "'");
        while ($products = xtc_db_fetch_array($products_query)) {
            $this->contents[$products['products_id']] = array('qty' => $products['customers_basket_quantity']);
            $attributes_query = xtc_db_query("SELECT products_options_id, products_options_value_id FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " WHERE customers_id = '" . (int) $customers_id . "' AND products_id = '" . $products['products_id'] . "'");
            while ($attributes = xtc_db_fetch_array($attributes_query)) {
                $this->contents[$products['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
            }
        }
        $this->calculatercs();
    }

    function calculatercs() {
        $this->total = 0;
        $this->weight = 0;
        if (!is_array($this->contents))
            return 0;

        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
            $qty = $this->contents[$products_id]['qty'];
            // products price
            $product_query = xtc_db_query("SELECT products_id, products_price, products_tax_class_id, products_weight FROM " . TABLE_PRODUCTS . " WHERE products_id='" . xtc_get_prid($products_id) . "'");
            if ($product = xtc_db_fetch_array($product_query)) {
                $prid = $product['products_id'];
                $products_tax = xtc_get_tax_rate($product['products_tax_class_id']);
                $products_price = $product['products_price'];
                $products_weight = $product['products_weight'];

                $specials_query = xtc_db_query("SELECT specials_new_products_price FROM " . TABLE_SPECIALS . " WHERE products_id = '" . $prid . "' AND status = '1'");
                if (xtc_db_num_rows($specials_query)) {
                    $specials = xtc_db_fetch_array($specials_query);
                    $products_price = $specials['specials_new_products_price'];
                }

                $this->total += xtc_add_tax($products_price, $products_tax) * $qty;
                $this->weight += ($qty * $products_weight);
            }

            // Properties price
            if ($this->contents[$products_id]['properties']) {
                reset($this->contents[$products_id]['properties']);
            }
            // attributes price
            if ($this->contents[$products_id]['attributes']) {
                reset($this->contents[$products_id]['attributes']);
                while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                    $attribute_price = xtc_db_fetch_array(xtc_db_query("SELECT options_values_price, price_prefix FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id = '" . $prid . "' AND options_id = '" . $option . "' AND options_values_id = '" . $value . "';"));
                    if ($attribute_price['price_prefix'] == '+') {
                        $this->total += $qty * xtc_add_tax($attribute_price['options_values_price'], $products_tax);
                    } else {
                        $this->total -= $qty * xtc_add_tax($attribute_price['options_values_price'], $products_tax);
                    }
                }
            }
        }
    }

}
