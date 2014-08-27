<?php

/* -----------------------------------------------------------------
 * 	$Id: brief.php 420 2013-06-19 18:04:39Z akausch $
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

class brief {
    var $code, $title, $description, $icon, $enabled, $num_ap;
    function brief() {
        global $order;
        $this->code = 'brief';
        $this->title = MODULE_SHIPPING_BRIEF_TEXT_TITLE;
        $this->description = MODULE_SHIPPING_BRIEF_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_SHIPPING_BRIEF_SORT_ORDER;
        $this->icon = DIR_WS_ICONS . 'shipping_brief.gif';
        $this->tax_class = MODULE_SHIPPING_BRIEF_TAX_CLASS;
        $this->enabled = ((MODULE_SHIPPING_BRIEF_STATUS == 'True') ? true : false);

        if (($this->enabled == true) && ((int) MODULE_SHIPPING_BRIEF_ZONE > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("SELECT zone_id FROM " . TABLE_ZONES_TO_GEO_ZONES . " WHERE geo_zone_id = '" . MODULE_SHIPPING_BRIEF_ZONE . "' AND zone_country_id = '" . $order->delivery['country']['id'] . "' ORDER BY zone_id;");
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
        $this->num_ap = 4;
    }

    function quote($method = '') {
        global $order, $shipping_weight, $shipping_num_boxes;
        $dest_country = $order->delivery['country']['iso_code_2'];
        $dest_zone = 0;
        $error = false;

        for ($i = 1; $i <= $this->num_ap; $i++) {
            $countries_table = constant('MODULE_SHIPPING_BRIEF_COUNTRIES_' . $i);
            $country_zones = preg_split("/[,]/", $countries_table);
            if (in_array($dest_country, $country_zones)) {
                $dest_zone = $i;
                break;
            }
        }

        if ($dest_zone == 0) {
            $error = true;
        } else {
            $shipping = -1;
            $ap_cost = constant('MODULE_SHIPPING_BRIEF_COST_' . $i);

            $ap_table = preg_split("/[:,]/", $ap_cost);
            for ($i = 0; $i < sizeof($ap_table); $i+=2) {
                if ($shipping_weight <= $ap_table[$i]) {
                    $shipping = $ap_table[$i + 1];
                    $shipping_method = MODULE_SHIPPING_BRIEF_TEXT_WAY . ' ' . $dest_country;
                    break;
                }
            }

            if ($shipping == -1) {
                $shipping_cost = 0;
				$error = true;
            } else {
                $shipping_cost = ($shipping + MODULE_SHIPPING_BRIEF_HANDLING);
            }
        }
		$this->quotes = array('id' => $this->code,
			'module' => MODULE_SHIPPING_BRIEF_TEXT_TITLE,
			'methods' => array(array('id' => $this->code,
					'title' => $shipping_method . ' (' . $shipping_num_boxes . ' x ' . $shipping_weight . ' ' . MODULE_SHIPPING_BRIEF_TEXT_UNITS . ')',
					'cost' => $shipping_cost * $shipping_num_boxes)));
		if ($this->tax_class > 0) {
			$this->quotes['tax'] = xtc_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
		}

		if (xtc_not_null($this->icon)) {
			$this->quotes['icon'] = xtc_image($this->icon, $this->title);
		}

		if ($error == true) {
			$this->quotes['error'] = MODULE_SHIPPING_BRIEF_INVALID_ZONE;
		}
		return $this->quotes;
    }

    function check() {
        if (!isset($this->_check)) {
            $this->_check = xtc_db_num_rows(xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_BRIEF_STATUS';"));
        }
        return $this->_check;
    }

    function install() {
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_SHIPPING_BRIEF_STATUS', 'True', '6', '0', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_BRIEF_HANDLING', '0', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_BRIEF_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_BRIEF_ZONE', '0', '6', '0', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_BRIEF_SORT_ORDER', '0', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_BRIEF_ALLOWED', '', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_BRIEF_COUNTRIES_1', 'DE', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_BRIEF_COST_1', '0.002:0.60,0.005:0.90,0.5:1.45,1.00:2.40', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_BRIEF_COUNTRIES_2', 'AT', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_BRIEF_COST_2', '0.002:0.75,0.005:1.50,0.5:3.45,1.00:7.00', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_BRIEF_COUNTRIES_3', 'CH', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_BRIEF_COST_3', '0.002:0.75,0.005:1.50,0.5:3.45,1.00:7.00', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_BRIEF_COUNTRIES_4', 'NL', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_BRIEF_COST_4', '0.002:0.75,0.005:1.50,0.5:3.45,1.00:7.00', '6', '0', now())");
    }

    function remove() {
        xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
        $keys = array('MODULE_SHIPPING_BRIEF_STATUS', 'MODULE_SHIPPING_BRIEF_HANDLING', 'MODULE_SHIPPING_BRIEF_ALLOWED', 'MODULE_SHIPPING_BRIEF_TAX_CLASS', 'MODULE_SHIPPING_BRIEF_ZONE', 'MODULE_SHIPPING_BRIEF_SORT_ORDER');
        for ($i = 1; $i <= $this->num_ap; $i++) {
            $keys[count($keys)] = 'MODULE_SHIPPING_BRIEF_COUNTRIES_' . $i;
            $keys[count($keys)] = 'MODULE_SHIPPING_BRIEF_COST_' . $i;
        }
        return $keys;
    }
}

