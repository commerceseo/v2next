<?php

/* -----------------------------------------------------------------
 * 	$Id: invoice.php 420 2013-06-19 18:04:39Z akausch $
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

class invoice {

    var $code, $title, $description, $enabled;

    function invoice() {
        global $order;

        $this->code = 'invoice';
        $this->title = MODULE_PAYMENT_INVOICE_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_INVOICE_TEXT_DESCRIPTION;
        $this->min_order = MODULE_PAYMENT_INVOICE_MIN_ORDER;
        $this->min_amount = MODULE_PAYMENT_INVOICE_MIN_AMOUNT;
        $this->max_amount = MODULE_PAYMENT_INVOICE_MAX_AMOUNT;
        $this->sort_order = MODULE_PAYMENT_INVOICE_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_INVOICE_STATUS == 'True') ? true : false);
        $this->info = MODULE_PAYMENT_INVOICE_TEXT_INFO;
        if ((int) MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID;
        }

        if (is_object($order))
            $this->update_status();
    }

    function update_status() {
        global $order;

        $check_order_query = xtc_db_query("select count(*) as count from " . TABLE_ORDERS . " where customers_id = '" . (int) $_SESSION['customer_id'] . "'");
        $order_check = xtc_db_fetch_array($check_order_query);
        if ($_SESSION['cart']->total < MODULE_PAYMENT_INVOICE_MIN_AMOUNT) {
            $check_flag = false;
            $this->enabled = false;
        }
        if ($_SESSION['cart']->total > MODULE_PAYMENT_INVOICE_MAX_AMOUNT) {
            $check_flag = false;
            $this->enabled = false;
        }


        if ($order_check['count'] < MODULE_PAYMENT_INVOICE_MIN_ORDER) {
            $check_flag = false;
            $this->enabled = false;
        } else {
            $check_flag = true;

            if (($this->enabled == true) && ((int) MODULE_PAYMENT_INVOICE_ZONE > 0)) {
                $check_flag = false;
                $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_INVOICE_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");

                while ($check = xtc_db_fetch_array($check_query)) {
                    if ($check['zone_id'] < 1) {
                        $check_flag = true;
                        break;
                    } elseif ($check['zone_id'] == $order->delivery['zone_id']) {
                        $check_flag = true;
                        break;
                    }
                }
            }

            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    function javascript_validation() {
        return false;
    }

    function selection() {
        return array('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
    }

    function pre_confirmation_check() {
        return false;
    }

    function confirmation() {
        return false;
    }

    function process_button() {
        return false;
    }

    function before_process() {
        return false;
    }

    function after_process() {
        global $insert_id;
        if ($this->order_status)
            xtc_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status='" . $this->order_status . "' WHERE orders_id='" . $insert_id . "'");
    }

    function get_error() {
        return false;
    }

    function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_INVOICE_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    function install() {
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_INVOICE_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_INVOICE_ALLOWED', '', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_INVOICE_ZONE', '0',  '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_INVOICE_SORT_ORDER', '0',  '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_INVOICE_MIN_ORDER', '0',  '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID', '0',  '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_INVOICE_MIN_AMOUNT', '0',  '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_INVOICE_MAX_AMOUNT', '10000',  '6', '0', now())");
    }

    function remove() {
        xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
        return array('MODULE_PAYMENT_INVOICE_STATUS', 'MODULE_PAYMENT_INVOICE_ALLOWED', 'MODULE_PAYMENT_INVOICE_ZONE', 'MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID', 'MODULE_PAYMENT_INVOICE_MIN_ORDER', 'MODULE_PAYMENT_INVOICE_SORT_ORDER', 'MODULE_PAYMENT_INVOICE_MIN_AMOUNT', 'MODULE_PAYMENT_INVOICE_MAX_AMOUNT');
    }

}

