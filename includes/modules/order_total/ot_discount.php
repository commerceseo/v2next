<?php

/* -----------------------------------------------------------------
 * 	$Id: ot_discount.php 791 2014-01-02 11:54:43Z akausch $
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

class ot_discount {

    var $title, $output;

    function ot_discount() {
        global $xtPrice;
        $this->code = 'ot_discount';
        $this->title = MODULE_ORDER_TOTAL_DISCOUNT_TITLE;
        $this->description = MODULE_ORDER_TOTAL_DISCOUNT_DESCRIPTION;
        $this->enabled = ((MODULE_ORDER_TOTAL_DISCOUNT_STATUS == 'true') ? true : false);
        $this->sort_order = MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER;

        $this->output = array();
    }

    function process() {
        global $order, $xtPrice;
        $this->title = $_SESSION['customers_status']['customers_status_ot_discount'] . ' % ' . SUB_TITLE_OT_DISCOUNT;
        if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1 && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00') {
            $discount_price = $xtPrice->xtcFormat($order->info['subtotal'], false) / 100 * $_SESSION['customers_status']['customers_status_ot_discount'] * -1;
            $this->output[] = array('title' => $this->title . ':', 'text' => $xtPrice->xtcFormat($discount_price, true), 'value' => $discount_price);
        }
    }

    function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_DISCOUNT_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    function keys() {
        return array('MODULE_ORDER_TOTAL_DISCOUNT_STATUS', 'MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER');
    }

    function install() {
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_DISCOUNT_STATUS', 'true','6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER', '2', '7', '2', now())");
    }

    function remove() {
        xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

}

