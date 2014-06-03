<?php

/* -----------------------------------------------------------------
 * 	$Id: class.wish_list.php 791 2014-01-02 11:54:43Z akausch $
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

require_once(DIR_FS_INC . 'xtc_create_random_value.inc.php');
require_once(DIR_FS_INC . 'xtc_get_prid.inc.php');
require_once(DIR_FS_INC . 'xtc_image_submit.inc.php');
require_once(DIR_FS_INC . 'xtc_get_tax_description.inc.php');

class wishList_ORIGINAL {

    var $contents, $total, $weight, $cartID, $content_type;

    function wishList_ORIGINAL() {
        // $this->reset();
    }

    function all_to_cart($id = '', $qty = '', $attributes) {
        if ($id == '' && $associated_array == '' && $attributes == '') {
            $contents = $this->get_products();
            if (is_array($contents)) {
                foreach ($contents AS $content) {

                    if ($content['quantity'] > MAX_PRODUCTS_QTY)
                        $content['quantity'] = MAX_PRODUCTS_QTY;

                    $attributes = ($content['attributes']) ? $content['attributes'] : '';
                    $old_qty = 0;
                    if ($_SESSION['cart']->in_cart($content['id'])) {
                        $old_qty = $_SESSION['cart']->get_quantity($content['id']);
                    }
                    $_SESSION['cart']->add_cart($content['id'], xtc_remove_non_numeric($content['quantity'] + $old_qty), $attributes, false);
                    $_SESSION['wishList']->remove($content['id']); //REMOVE FROM WISHLIST
                }
            }
        } else {
            $content['id'] = $id;
            $content['quantity'] = $qty;
            $content['attributes'] = $attributes;

            if ($content['quantity'] > MAX_PRODUCTS_QTY)
                $content['quantity'] = MAX_PRODUCTS_QTY;

            $attributes = ($content['attributes']) ? $content['attributes'] : '';
            $_SESSION['cart']->add_cart($content['id'], xtc_remove_non_numeric($content['quantity']), $attributes, false);
            $_SESSION['wishList']->remove($content['id']); //REMOVE FROM WISHLIST
        }
    }

    function restore_contents() {

        if (!isset($_SESSION['customer_id']))
            return false;

        // insert current cart contents in database
        if (is_array($this->contents)) {
            reset($this->contents);
            while (list($products_id, ) = each($this->contents)) {
                $qty = $this->contents[$products_id]['qty'];
                $product_query = xtc_db_query("select products_id from " . TABLE_CUSTOMERS_WISHLIST . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products_id . "' ORDER BY customers_basket_date_added DESC");
                if (!xtc_db_num_rows($product_query)) {
                    xtc_db_query("insert into " . TABLE_CUSTOMERS_WISHLIST . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . $_SESSION['customer_id'] . "', '" . $products_id . "', '" . $qty . "', '" . date('Ymd') . "')");
                    if (isset($this->contents[$products_id]['attributes'])) {
                        reset($this->contents[$products_id]['attributes']);
                        while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                            xtc_db_query("insert into " . TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id) values ('" . $_SESSION['customer_id'] . "', '" . $products_id . "', '" . $option . "', '" . $value . "')");
                        }
                    }
                } else {
                    xtc_db_query("UPDATE " . TABLE_CUSTOMERS_WISHLIST . " SET customers_basket_quantity = '" . $qty . "' WHERE customers_id = '" . $_SESSION['customer_id'] . "' AND products_id = '" . $products_id . "'");
                }
            }
        }

        // reset per-session cart contents, but not the database contents
        $this->reset(false);

        $products_query = xtc_db_query("select products_id, customers_basket_quantity from " . TABLE_CUSTOMERS_WISHLIST . " where customers_id = '" . $_SESSION['customer_id'] . "' ORDER BY customers_basket_date_added DESC");
        while ($products = xtc_db_fetch_array($products_query)) {
            $this->contents[$products['products_id']] = array('qty' => $products['customers_basket_quantity']);
            // attributes
            $attributes_query = xtc_db_query("select products_options_id, products_options_value_id from " . TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products['products_id'] . "'");
            while ($attributes = xtc_db_fetch_array($attributes_query)) {
                $this->contents[$products['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
            }
        }

        $this->cleanup();
    }

    function reset($reset_database = false) {

        $this->contents = array();
        $this->total = 0;
        $this->weight = 0;
        $this->content_type = false;

        if (isset($_SESSION['customer_id']) && ($reset_database == true)) {
            xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_WISHLIST . " WHERE customers_id = '" . $_SESSION['customer_id'] . "'");
            xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES . " WHERE customers_id = '" . $_SESSION['customer_id'] . "'");
        }

        unset($this->cartID);
        if (isset($_SESSION['cartID']))
            unset($_SESSION['cartID']);
    }

    function add_cart($products_id, $qty = '1', $attributes = '', $notify = true) {
        global $new_products_id_in_cart;

        $products_id = xtc_get_uprid($products_id, $attributes);
        if ($notify == true) {
            $_SESSION['new_products_id_in_wish'] = $products_id;
        }

        if ($this->in_cart($products_id)) {
            $this->update_quantity($products_id, $qty, $attributes);
        } else {
            $this->contents[] = array($products_id);
            $this->contents[$products_id] = array('qty' => $qty);
            if (isset($_SESSION['customer_id']))
                xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_WISHLIST . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) VALUES ('" . $_SESSION['customer_id'] . "', '" . $products_id . "', '" . $qty . "', '" . date('Ymd') . "')");

            if (is_array($attributes)) {
                reset($attributes);
                while (list($option, $value) = each($attributes)) {
                    $this->contents[$products_id]['attributes'][$option] = $value; //FIX-WISHLIST OHNE ATTRIBUTES
                    if (isset($_SESSION['customer_id']))
                        xtc_db_query("INSERT INTO " . TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id) VALUES ('" . $_SESSION['customer_id'] . "', '" . $products_id . "', '" . $option . "', '" . $value . "')");
                }
            }
        }
        $this->cleanup();

        $this->cartID = $this->generate_cart_id();
    }

    function update_quantity($products_id, $quantity = '', $attributes = '') {

        if (empty($quantity))
            return true; // nothing needs to be updated if theres no quantity, so we return true..

        $this->contents[$products_id] = array('qty' => $quantity);
        // update database
        if (isset($_SESSION['customer_id']))
            xtc_db_query("update " . TABLE_CUSTOMERS_WISHLIST . " set customers_basket_quantity = '" . $quantity . "' where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products_id . "'");

        if (is_array($attributes)) {
            reset($attributes);
            while (list($option, $value) = each($attributes)) {
                $this->contents[$products_id]['attributes'][$option] = $value;
                // update database
                if (isset($_SESSION['customer_id']))
                    xtc_db_query("update " . TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES . " set products_options_value_id = '" . $value . "' where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products_id . "' and products_options_id = '" . $option . "'");
            }
        }
    }

    function cleanup() {

        reset($this->contents);
        while (list($key, ) = each($this->contents)) {
            if ($this->contents[$key]['qty'] < 1) {
                unset($this->contents[$key]);
                // remove from database
                if (isset($_SESSION['customer_id'])) {
                    xtc_db_query("delete from " . TABLE_CUSTOMERS_WISHLIST . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $key . "'");
                    xtc_db_query("delete from " . TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $key . "'");
                }
            }
        }
    }

    function count_contents() {
        $total_items = 0;
        if (is_array($this->contents)) {
            reset($this->contents);
            while (list($products_id, ) = each($this->contents)) {
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

        unset($this->contents[$products_id]);
        // remove from database
        if (isset($_SESSION['customer_id'])) {
            xtc_db_query("delete from " . TABLE_CUSTOMERS_WISHLIST . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products_id . "'");
            xtc_db_query("delete from " . TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products_id . "'");
        }

        // assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
        $this->cartID = $this->generate_cart_id();
    }

    function remove_all() {
        $this->reset();
    }

    function get_product_id_list() {
        $product_id_list = '';
        if (is_array($this->contents)) {
            reset($this->contents);
            while (list($products_id, ) = each($this->contents)) {
                $product_id_list .= ', ' . $products_id;
            }
        }

        return substr($product_id_list, 2);
    }

    function calculate() {
        global $xtPrice;
        $this->total = 0;
        $this->weight = 0;
        if (!is_array($this->contents))
            return 0;

        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
            $qty = $this->contents[$products_id]['qty'];



            // products price
            $product_query = xtc_db_query("select products_id, products_price, products_discount_allowed, products_tax_class_id, products_weight from " . TABLE_PRODUCTS . " where products_id='" . xtc_get_prid($products_id) . "'");
            if ($product = xtc_db_fetch_array($product_query)) {

                $products_price = $xtPrice->xtcGetPrice($product['products_id'], $format = false, $qty, $product['products_tax_class_id'], $product['products_price']);
                $this->total += $products_price * $qty;
                $this->weight += ($qty * $product['products_weight']);
            }

            // attributes price
            if (isset($this->contents[$products_id]['attributes'])) {
                reset($this->contents[$products_id]['attributes']);
                while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                    $attribute_price_query = xtc_db_query("select pd.products_tax_class_id, p.options_values_price, p.price_prefix, p.options_values_weight, p.weight_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " p, " . TABLE_PRODUCTS . " pd where p.products_id = '" . $product['products_id'] . "' and p.options_id = '" . $option . "' and pd.products_id = p.products_id and p.options_values_id = '" . $value . "'");
                    $attribute_price = xtc_db_fetch_array($attribute_price_query);

                    $attribute_weight = $attribute_price['options_values_weight'];
                    if ($attribute_price['weight_prefix'] == '+') {
                        $this->weight += ($qty * $attribute_price['options_values_weight']);
                    } else {
                        $this->weight -= ($qty * $attribute_price['options_values_weight']);
                    }

                    if ($attribute_price['price_prefix'] == '+') {
                        $this->total +=$xtPrice->xtcFormat($attribute_price['options_values_price'], false, $attribute_price['products_tax_class_id']) * $qty;
                    } else {
                        $this->total -=$xtPrice->xtcFormat($attribute_price['options_values_price'], false, $attribute_price['products_tax_class_id']) * $qty;
                    }
                }
            }
        }
        if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] != 0 && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00') {
            $this->total -= $this->total / 100 * $_SESSION['customers_status']['customers_status_ot_discount'];
        }
    }

    function attributes_price($products_id, $products_price) {
        global $xtPrice;
        if (isset($this->contents[$products_id]['attributes'])) {
            reset($this->contents[$products_id]['attributes']);
            while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                $attribute_price_query = xtc_db_query("select pd.products_tax_class_id, p.options_values_price, p.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " p, " . TABLE_PRODUCTS . " pd where p.products_id = '" . $products_id . "' and p.options_id = '" . $option . "' and pd.products_id = p.products_id and p.options_values_id = '" . $value . "'");
                $attribute_price = xtc_db_fetch_array($attribute_price_query);
                if ($attribute_price['price_prefix'] == '+') {
                    $attributes_price += $xtPrice->xtcFormat($attribute_price['options_values_price'], false, $attribute_price['products_tax_class_id']);
                } elseif ($attribute_price['price_prefix'] == '=') {
                    $attributes_price = $xtPrice->xtcFormat($attribute_price['options_values_price'], false, $attribute_price['products_tax_class_id']) - $products_price;
                } else {
                    $attributes_price -= $xtPrice->xtcFormat($attribute_price['options_values_price'], false, $attribute_price['products_tax_class_id']);
                }
            }
        }
        return $attributes_price;
    }

    function get_products() {
        global $xtPrice, $main;
        if (!is_array($this->contents))
            return false;

        $products_array = array();
        reset($this->contents);
        while (list($products_id) = each($this->contents)) {
            $products_query = xtc_db_query("SELECT 
											p.products_id, 
											pd.products_name,
											p.products_shippingtime,
											p.products_image, 
											p.products_model, 
											p.products_price, 
											p.products_discount_allowed, 
											p.products_weight,
											p.products_vpe_status,
											p.products_vpe,
											p.products_vpe_value,
											p.products_tax_class_id 
										FROM 
											products p, 
											products_description pd 
										WHERE 
											p.products_id = '" . xtc_get_prid($products_id) . "' 
										AND 
											pd.products_id = p.products_id 
										AND 
											pd.language_id = '" . $_SESSION['languages_id'] . "'");
            if ($products = xtc_db_fetch_array($products_query)) {
                $prid = $products['products_id'];

                $products_price = $xtPrice->xtcGetPrice($products['products_id'], $format = false, $this->contents[$products_id]['qty'], $products['products_tax_class_id'], $products['products_price']);


                if ($products['products_vpe_status'] == 1 && $products['products_vpe_value'] != 0.0 && $products_price > 0) {
                    require_once (DIR_FS_INC . 'xtc_get_vpe_name.inc.php');
                    $vpe = $xtPrice->xtcFormat($products_price * (1 / $products['products_vpe_value']), true) . TXT_PER . xtc_get_vpe_name($products['products_vpe']);
                } else {
                    $vpe = '';
                }
                $products_array[] = array('id' => $products_id,
                    'name' => $products['products_name'],
                    'model' => $products['products_model'],
                    'image' => $products['products_image'],
                    'vpe' => $vpe,
                    'price' => ($products_price + $this->attributes_price($products_id, $products_price)),
                    'quantity' => $this->contents[$products_id]['qty'],
                    'weight' => $products['products_weight'],
                    'shipping_time' => $main->getShippingStatusName($products['products_shippingtime']),
                    'final_price' => ($products_price + $this->attributes_price($products_id, $products_price)),
                    'tax_class_id' => $products['products_tax_class_id'],
                    'attributes' => $this->contents[$products_id]['attributes']);
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

    function generate_cart_id($length = 5) {
        return xtc_create_random_value($length, 'digits');
    }

    function get_content_type() {
        $this->content_type = false;


        if ((DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0)) {
            reset($this->contents);
            while (list($products_id, ) = each($this->contents)) {
                if (isset($this->contents[$products_id]['attributes'])) {
                    reset($this->contents[$products_id]['attributes']);
                    while (list(, $value) = each($this->contents[$products_id]['attributes'])) {
                        $virtual_check_query = xtc_db_query("select count(*) as total from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad where pa.products_id = '" . $products_id . "' and pa.options_values_id = '" . $value . "' and pa.products_attributes_id = pad.products_attributes_id");
                        $virtual_check = xtc_db_fetch_array($virtual_check_query);

                        if ($virtual_check['total'] > 0) {
                            switch ($this->content_type) {
                                case 'physical':
                                    $this->content_type = 'mixed';
                                    return $this->content_type;
                                    break;

                                default:
                                    $this->content_type = 'virtual';
                                    break;
                            }
                        } else {
                            switch ($this->content_type) {
                                case 'virtual':
                                    $this->content_type = 'mixed';
                                    return $this->content_type;
                                    break;

                                default:
                                    $this->content_type = 'physical';
                                    break;
                            }
                        }
                    }
                } else {
                    switch ($this->content_type) {
                        case 'virtual':
                            $this->content_type = 'mixed';
                            return $this->content_type;
                            break;

                        default:
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

    function count_contents_virtual() {
        $total_items = 0;
        if (is_array($this->contents)) {
            reset($this->contents);
            while (list($products_id, ) = each($this->contents)) {
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

}
