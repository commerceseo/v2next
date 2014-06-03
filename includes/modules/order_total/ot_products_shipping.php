<?php

/* -----------------------------------------------------------------
 * 	$Id: ot_products_shipping.php 420 2013-06-19 18:04:39Z akausch $
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

class ot_products_shipping {

    var $title, $output;

    function ot_products_shipping() {
        global $xtPrice;
        $this->code = 'ot_products_shipping';
        $this->title = MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_TITLE;
        $this->description = MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_DESCRIPTION;
        $this->enabled = ((MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_STATUS == 'true') ? true : false);
        $this->sort_order = MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_SORT_ORDER;

        $this->output = array();
    }

    function nc_get_product_shipping_costs() {
        global $xtPrice;

        $products = $_SESSION['cart']->get_products();
        $costs = 0;
        $infos = array();

        for ($i = 0; $i < sizeof($products); $i++) {
            $result = xtc_db_query("SELECT 
										p.products_shipping_costs AS costs,
										pd.products_name AS products_name
									FROM 	
										products p
										LEFT JOIN products_description AS pd USING (products_id)
									WHERE
										p.products_shipping_costs != 0
									AND
										p.products_id = '" . $products[$i]['id'] . "'
									AND
										pd.language_id = '" . $_SESSION['languages_id'] . "'");

            if (xtc_db_num_rows($result) > 0) {
                while ($row = xtc_db_fetch_array($result)) {
                    $costs += $xtPrice->xtcFormat($row['costs'] * $products[$i]['quantity'], false, 0, true);
                    $infos[] = array('title' => $products[$i]['quantity'] . 'x ' . $row['products_name'],
                        'price' => $xtPrice->xtcFormat($row['costs'] * $products[$i]['quantity'], true, MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_TAX_CLASS, true),
                        'price_plain' => $xtPrice->xtcFormat($row['costs'] * $products[$i]['quantity'], false, 0, true));
                }
            }
        }
        $output = array('costs' => $costs,
            'infos' => $infos);
        return $output;
    }

    function process() {
        global $order, $xtPrice;
        require_once(DIR_FS_INC . 'xtc_calculate_tax.inc.php');

        if (MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_STATUS == 'true') {
            switch (MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_DESTINATION) {
                case 'national':
                    if ($order->delivery['country_id'] == STORE_COUNTRY)
                        $pass = true; break;
                case 'international':
                    if ($order->delivery['country_id'] != STORE_COUNTRY)
                        $pass = true; break;
                case 'both':
                    $pass = true;
                    break;
                default:
                    $pass = false;
                    break;
            }

            if ($pass == true) {
                $products_data = $this->nc_get_product_shipping_costs();

                $tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
                $tax_description = xtc_get_tax_description(MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);

                if (MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_DETAILS == 'false') {
                    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
                        $order->info['tax'] += xtc_calculate_tax($products_data['costs'], $tax);
                        $order->info['tax_groups'][TAX_ADD_TAX . "$tax_description"] += xtc_calculate_tax($products_data['costs'], $tax);
                        $order->info['total'] += $products_data['costs'] + xtc_calculate_tax($products_data['costs'], $tax);
                        $products_shipping_fee = xtc_add_tax($products_data['costs'], $tax);
                    }
                    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
                        $products_shipping_fee = $products_data['costs'];
                        $order->info['tax'] += xtc_calculate_tax($products_data['costs'], $tax);
                        $order->info['tax_groups'][TAX_NO_TAX . "$tax_description"] += xtc_calculate_tax($products_data['costs'], $tax);
                        $order->info['subtotal'] += $products_shipping_fee;
                        $order->info['total'] += $products_shipping_fee;
                    }
                    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] != 1) {
                        $products_shipping_fee = $products_data['costs'];
                        $order->info['subtotal'] += $products_shipping_fee;
                        $order->info['total'] += $products_shipping_fee;
                    }
                    $output_title = MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_OUTPUT_NAME . ':';
                    $this->output[] = array('title' => $output_title,
                        'text' => $xtPrice->xtcFormat($products_shipping_fee, true),
                        'value' => $products_shipping_fee);
                } else {
                    foreach ($products_data['infos'] as $info) {
                        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
                            $order->info['tax'] += xtc_calculate_tax($info['price_plain'], $tax);
                            $order->info['tax_groups'][TAX_ADD_TAX . "$tax_description"] += xtc_calculate_tax($info['price_plain'], $tax);
                            $order->info['total'] += $info['price_plain'] + xtc_calculate_tax($info['price_plain'], $tax);
                            $products_shipping_fee = xtc_add_tax($info['price_plain'], $tax);
                        }
                        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
                            $products_shipping_fee = $info['price_plain'];
                            $order->info['tax'] += xtc_calculate_tax($info['price_plain'], $tax);
                            $order->info['tax_groups'][TAX_NO_TAX . "$tax_description"] += xtc_calculate_tax($info['price_plain'], $tax);
                            $order->info['subtotal'] += $products_shipping_fee;
                            $order->info['total'] += $products_shipping_fee;
                        }
                        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] != 1) {
                            $products_shipping_fee = $info['price_plain'];
                            $order->info['subtotal'] += $products_shipping_fee;
                            $order->info['total'] += $products_shipping_fee;
                        }
                        $output_title = MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_OUTPUT_NAME . ': ';
                        $this->output[] = array('title' => $output_title . ' ' . $info['title'] . ': ',
                            'text' => $xtPrice->xtcFormat($products_shipping_fee, true),
                            'value' => $products_shipping_fee);
                    }
                }
            }
        }
    }

    function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    function keys() {
        return array('MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_STATUS', 'MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_SORT_ORDER', 'MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_OUTPUT_NAME', 'MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_DETAILS', 'MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_DESTINATION', 'MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_TAX_CLASS');
    }

    function install() {
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_STATUS', 'true', '6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_SORT_ORDER', '31', '6', '2', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_OUTPUT_NAME', 'Versandzuschlag', '6', '2', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_DETAILS', 'true', '6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_DESTINATION', 'both','6', '6', 'xtc_cfg_select_option(array(\'national\', \'international\', \'both\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_ORDER_TOTAL_PRODUCTS_SHIPPING_TAX_CLASS', '0','6', '7', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
    }

    function remove() {
        xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

}

