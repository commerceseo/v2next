<?php


  class dpd {
    var $code, $title, $description, $enabled, $num_zones;

/**
 * class constructor
 */
    function dpd() {
      $this->code = 'dpd';
      $this->title = MODULE_SHIPPING_DPD_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_DPD_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_DPD_SORT_ORDER;
      $this->icon = '';
      $this->tax_class = MODULE_SHIPPING_DPD_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_DPD_STATUS == 'True') ? true : false);

/**
 * CUSTOMIZE THIS SETTING FOR THE NUMBER OF ZONES NEEDED
 */
      $this->num_zones = 10;
    }

/**
 * class methods
 */
    function quote($method = '') {
      global $order, $shipping_weight, $shipping_num_boxes;

      $dest_country = $order->delivery['country']['iso_code_2'];
      $dest_zone = 0;
      $error = false;

      for ($i=1; $i<=$this->num_zones; $i++) {
        $countries_table = constant('MODULE_SHIPPING_DPD_COUNTRIES_' . $i);
        $country_zones = explode(',', $countries_table);
        if (in_array($dest_country, $country_zones)) {
          $dest_zone = $i;
          break;
        }
      }

      if ($dest_zone == 0) {
        $error = true;
      } else {
        $shipping = -1;
        $zones_cost = constant('MODULE_SHIPPING_DPD_COST_' . $dest_zone);

        $zones_table = preg_split('/[:,]/', $zones_cost);
        $size = sizeof($zones_table);
        for ($i=0; $i<$size; $i+=2) {
          if ($shipping_weight <= $zones_table[$i]) {
            $shipping = $zones_table[$i+1];
            // BOF GM_MOD:
            $shipping_method = MODULE_SHIPPING_DPD_TEXT_WAY . ' ' . $dest_country . ': (' . $shipping_num_boxes . ' x ' . $shipping_weight . ' ' . MODULE_SHIPPING_DPD_TEXT_UNITS . ')';
            break;
          }
        }

        if ($shipping == -1) {
          $error = true;
          $shipping_cost = 0;
          $shipping_method = MODULE_SHIPPING_DPD_UNDEFINED_RATE;
        } else {
          $shipping_cost = ($shipping + constant('MODULE_SHIPPING_DPD_HANDLING_' . $dest_zone));
        }
      }

      $this->quotes = array('id' => $this->code,
                            'module' => MODULE_SHIPPING_DPD_TEXT_TITLE,
                            'methods' => array(array('id' => $this->code,
                                                     'title' => $shipping_method,
                                                     'cost' => $shipping_cost * $shipping_num_boxes)));

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = xtc_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
      }

      if (xtc_not_null($this->icon)) $this->quotes['icon'] = xtc_image($this->icon, $this->title);

      if ($error == true) $this->quotes['error'] = MODULE_SHIPPING_DPD_UNDEFINED_RATE;

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_DPD_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_SHIPPING_DPD_STATUS', 'True', '6', '0', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DPD_ALLOWED', '', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_DPD_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DPD_SORT_ORDER', '0', '6', '0', now())");
      for ($i = 1; $i <= $this->num_zones; $i++) {
        $default_countries = '';
        if ($i == 1) {
          $default_countries = 'DE';
        }
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DPD_COUNTRIES_" . $i ."', '" . $default_countries . "', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DPD_COST_" . $i ."', '3:8.50,7:10.50,99:20.00', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DPD_HANDLING_" . $i."', '0', '6', '0', now())");
      }
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      $keys = array('MODULE_SHIPPING_DPD_STATUS','MODULE_SHIPPING_DPD_ALLOWED', 'MODULE_SHIPPING_DPD_TAX_CLASS', 'MODULE_SHIPPING_DPD_SORT_ORDER');

      for ($i=1; $i<=$this->num_zones; $i++) {
        $keys[] = 'MODULE_SHIPPING_DPD_COUNTRIES_' . $i;
        $keys[] = 'MODULE_SHIPPING_DPD_COST_' . $i;
        $keys[] = 'MODULE_SHIPPING_DPD_HANDLING_' . $i;
      }

      return $keys;
    }
  }

?>