<?php

/* -----------------------------------------------------------------
 * 	$Id: transoflex.php 420 2013-06-19 18:04:39Z akausch $
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

class transoflex {

    var $code, $title, $description, $icon, $enabled, $num_dp;

    function transoflex() {
        global $order;

        $this->code = 'transoflex';
        $this->title = MODULE_SHIPPING_TRANSOFLEX_TEXT_TITLE;
        $this->description = MODULE_SHIPPING_TRANSOFLEX_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_SHIPPING_TRANSOFLEX_SORT_ORDER;
        $this->icon = DIR_WS_ICONS . 'shipping_transoflex.gif';
        $this->tax_class = MODULE_SHIPPING_TRANSOFLEX_TAX_CLASS;
        $this->enabled = ((MODULE_SHIPPING_TRANSOFLEX_STATUS == 'True') ? true : false);

        if (($this->enabled == true) && ((int) MODULE_SHIPPING_TRANSOFLEX_ZONE > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_TRANSOFLEX_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
            while ($check = xtc_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check['zone_id'] == $order->delivery['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }

            if ($check_flag == false) {
                $this->enabled = false;
            }
        }

        $this->num_dp = 6;

        #$weightcheck = $this->quote('', true);
        #if ($weightcheck === false) $this->enabled = false;      
    }

    /**
     * class methods
     */
    function quote($method = '', $return_bool = true) {
        global $order, $shipping_weight, $shipping_num_boxes;

        $dest_country = $order->delivery['country']['iso_code_2'];
        $dest_zone = 0;
        $error = false;

        for ($i = 1; $i <= $this->num_dp; $i++) {
            $countries_table = constant('MODULE_SHIPPING_TRANSOFLEX_COUNTRIES_' . $i);
            $country_zones = split("[,]", $countries_table);
            if (in_array($dest_country, $country_zones)) {
                $dest_zone = $i;
                break;
            }
        }

        if ($dest_zone == 0) {
            $error = true;
        } else {
            $shipping = -1;
            $dp_cost = constant('MODULE_SHIPPING_TRANSOFLEX_COST_' . $i);

            $dp_table = split("[:,]", $dp_cost);

            $minimumweights = array();
            for ($i = 0; $i < sizeof($dp_table); $i+=2) {
                if (strpos($dp_table[$i], "-") !== false) {
                    list($minimum_tmp, ) = explode("-", $dp_table[$i]);
                    $minimumweights[] = $minimum_tmp;
                    unset($minimum_tmp);
                }
            }

            $checkWeight = true;
            if (sizeof($minimumweights) > 0) {
                sort($minimumweights);
                $minimumweight = $minimumweights[0];
                if ($shipping_weight < $minimumweight)
                    $checkWeight = false;
            }
            if ($checkWeight) {
                for ($i = 0; $i < sizeof($dp_table); $i+=2) {
                    $checker = $dp_table[$i];
                    if (strpos($dp_table[$i], "-") !== false)
                        list(, $checker) = explode("-", $dp_table[$i]);
                    if ($shipping_weight <= $checker) {
                        $shipping = $dp_table[$i + 1];
                        $shipping_method = MODULE_SHIPPING_TRANSOFLEX_TEXT_WAY . ' ' . $dest_country . ': ';
                        break;
                    }
                }
            }

            if ($shipping == -1) {
                $shipping_cost = 0;
                $shipping_method = MODULE_SHIPPING_TRANSOFLEX_UNDEFINED_RATE;
                if ($return_bool)
                    return false;
            } else {
                $shipping_cost = ($shipping + MODULE_SHIPPING_TRANSOFLEX_HANDLING);
            }
        }

        $this->quotes = array('id' => $this->code,
            'module' => MODULE_SHIPPING_TRANSOFLEX_TEXT_TITLE,
            'methods' => array(array('id' => $this->code,
                    'title' => $shipping_method . ' (' . $shipping_num_boxes . ' x ' . $shipping_weight . ' ' . MODULE_SHIPPING_TRANSOFLEX_TEXT_UNITS . ')',
                    'cost' => $shipping_cost * $shipping_num_boxes)));

        if ($this->tax_class > 0) {
            $this->quotes['tax'] = xtc_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
        }

        if (xtc_not_null($this->icon))
            $this->quotes['icon'] = xtc_image($this->icon, $this->title);

        if ($error == true) {
            if ($return_bool)
                return false;
            $this->quotes['error'] = MODULE_SHIPPING_TRANSOFLEX_INVALID_ZONE;
        }

        return $this->quotes;
    }

    function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_TRANSOFLEX_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    function install() {
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_SHIPPING_TRANSOFLEX_STATUS', 'True', '6', '0', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_HANDLING', '0', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_ZONE', '0', '6', '0', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_SORT_ORDER', '0', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_ALLOWED', '', '6', '0', now())");

        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_COUNTRIES_1', 'DE', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_COST_1', '2.2-100:0', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_COUNTRIES_2', 'BE,DK,LU,NL,AT', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_COST_2', '2.2-100:40', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_COUNTRIES_3', 'CH', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_COST_3', '2.2-100:100', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_COUNTRIES_4', '', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_COST_4', '', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_COUNTRIES_5', '', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_COST_5', '', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_COUNTRIES_6', '', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TRANSOFLEX_COST_6', '', '6', '0', now())");
    }

    function remove() {
        xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
        $keys = array('MODULE_SHIPPING_TRANSOFLEX_STATUS', 'MODULE_SHIPPING_TRANSOFLEX_HANDLING', 'MODULE_SHIPPING_TRANSOFLEX_ALLOWED', 'MODULE_SHIPPING_TRANSOFLEX_TAX_CLASS', 'MODULE_SHIPPING_TRANSOFLEX_ZONE', 'MODULE_SHIPPING_TRANSOFLEX_SORT_ORDER');

        for ($i = 1; $i <= $this->num_dp; $i++) {
            $keys[count($keys)] = 'MODULE_SHIPPING_TRANSOFLEX_COUNTRIES_' . $i;
            $keys[count($keys)] = 'MODULE_SHIPPING_TRANSOFLEX_COST_' . $i;
        }

        return $keys;
    }

}

