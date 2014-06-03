<?php

/* -----------------------------------------------------------------
 * 	$Id: ot_loyalty_discount.php 420 2013-06-19 18:04:39Z akausch $
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

class ot_loyalty_discount {

    var $title, $output;

    function ot_loyalty_discount() {
        $this->code = 'ot_loyalty_discount';
        $this->title = MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_TITLE;
        $this->description = MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_DESCRIPTION;
        $this->enabled = MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_STATUS;
        $this->sort_order = MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_SORT_ORDER;
        $this->include_shipping = MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_SHIPPING;
        $this->include_tax = MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_TAX;
        $this->calculate_tax = MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CALC_TAX;
        $this->table = MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_TABLE;
        $this->cum_order_period = MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CUMORDER_PERIOD;
        $this->output = array();
    }

    function process() {
        global $order, $ot_subtotal, $xtPrice;
        $od_amount = $this->calculate_credit($this->get_order_total(), $this->get_cum_order_total());
        if ($od_amount > 0) {
            $this->deduction = $od_amount;
            $this->output[] = array('title' => $this->title . ':<br>' . MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_SPENT . $xtPrice->xtcFormat($this->cum_order_total, true) . $this->period_string . MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_QUALIFY . $this->od_pc . '%:',
                'text' => '<b>' . $xtPrice->xtcFormat($od_amount, true) . '</b>',
//                                'text' => '<b>' . $currencies->format($od_amount) . '</b>',
                'value' => $od_amount);
            $order->info['total'] = $order->info['total'] - $od_amount;
            if ($this->sort_order < $ot_subtotal->sort_order) {
                $order->info['subtotal'] = $order->info['subtotal'] - $od_amount;
            }
        }
    }

    function calculate_credit($amount_order, $amount_cum_order) {
        global $order;
        $od_amount = 0;
        #$table_cost = split("[:,]" , MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_TABLE);
        $table_cost = preg_split("/[:,]/", MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_TABLE);
        for ($i = 0; $i < count($table_cost); $i+=2) {
            if ($amount_cum_order >= $table_cost[$i]) {
                $od_pc = $table_cost[$i + 1];
                $this->od_pc = $od_pc;
            }
        }
// Calculate tax reduction if necessary
        if ($this->calculate_tax == 'true') {
// Calculate main tax reduction
            $tod_amount = round($order->info['tax'] * 10) / 10 * $od_pc / 100;
            $order->info['tax'] = $order->info['tax'] - $tod_amount;
// Calculate tax group deductions
            reset($order->info['tax_groups']);
            while (list($key, $value) = each($order->info['tax_groups'])) {
                $god_amount = round($value * 10) / 10 * $od_pc / 100;
                $order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] - $god_amount;
            }
        }
        $od_amount = round($amount_order * 10) / 10 * $od_pc / 100;
        $od_amount = $od_amount + $tod_amount;
        return $od_amount;
    }

    function get_order_total() {
        global $order;
        $order_total = $order->info['total'];
// Check if gift voucher is in cart and adjust total
        $products = $_SESSION['cart']->get_products();
        for ($i = 0; $i < sizeof($products); $i++) {
            $t_prid = xtc_get_prid($products[$i]['id']);
            $gv_query = xtc_db_query("select 
                                  products_price, 
                                  products_tax_class_id, 
                                  products_model 
                                from " .
                    TABLE_PRODUCTS . " 
                                where 
                                  products_id = '" . $t_prid . "'");
            $gv_result = xtc_db_fetch_array($gv_query);
            if (strpos(addslashes($gv_result['products_model']), "GIFT") !== false) {
                #if (ereg('^GIFT', addslashes($gv_result['products_model']))) { 
                $qty = $_SESSION['cart']->get_quantity($t_prid);
                $products_tax = xtc_get_tax_rate($gv_result['products_tax_class_id']);
                if ($this->include_tax == 'false') {
                    $gv_amount = $gv_result['products_price'] * $qty;
                } else {
                    $gv_amount = ($gv_result['products_price'] + xtc_calculate_tax($gv_result['products_price'], $products_tax)) * $qty;
                }
                $order_total = $order_total - $gv_amount;
            }
        }
        if ($this->include_tax == 'false')
            $order_total = $order_total - $order->info['tax'];
        if ($this->include_shipping == 'false')
            $order_total = $order_total - $order->info['shipping_cost'];
        return $order_total;
    }

    function get_cum_order_total() {
        global $order;

        if (MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_ORDER_STATUS_CONSIDER != '') {
            $st_list = explode(',', MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_ORDER_STATUS_CONSIDER);
            $st_list = "o.orders_status in ('" . implode("','", $st_list) . "') and ";
        }

        $customer_id = $_SESSION['customer_id'];
        $history_query_raw = "select 
                            o.date_purchased,
                            ot.value as order_total 
                          from " .
                TABLE_ORDERS . " o left join " .
                TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id) 
                          where 
                            o.customers_id = '" . $customer_id . "' and
                            " . $st_list . " 
                            ot.class = 'ot_total' 
                          order by date_purchased DESC";
        $history_query = xtc_db_query($history_query_raw);

        if (xtc_db_num_rows($history_query)) {
            $cum_order_total = 0;
            $cutoff_date = $this->get_cutoff_date();
            while ($history = xtc_db_fetch_array($history_query)) {
                if ($this->get_date_in_period($cutoff_date, $history['date_purchased']) == true) {
                    $cum_order_total = $cum_order_total + $history['order_total'];
                }
            }
            $this->cum_order_total = $cum_order_total;
            return $cum_order_total;
        } else {
            $cum_order_total = 0;
            $this->cum_order_total = $cum_order_total;
            return $cum_order_total;
        }
    }

    function get_cutoff_date() {
        $rightnow = time();
        switch ($this->cum_order_period) {
            case alltime:
                $this->period_string = MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_WITHUS;
                $cutoff_date = 0;
                return $cutoff_date;
                break;
            case year:
                $this->period_string = MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_LAST . MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_YEAR;
                $cutoff_date = $rightnow - (60 * 60 * 24 * 365);
                return $cutoff_date;
                break;
            case quarter:
                $this->period_string = MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_LAST . MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_QUARTER;
                $cutoff_date = $rightnow - (60 * 60 * 24 * 92);
                return $cutoff_date;
                break;
            case month:
                $this->period_string = MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_LAST . MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_MONTH;
                $cutoff_date = $rightnow - (60 * 60 * 24 * 31);
                return $cutoff_date;
                break;
            default:
                $cutoff_date = $rightnow;
                return $cutoff_date;
        }
    }

    function get_date_in_period($cutoff_date, $raw_date) {
        if (($raw_date == '0000-00-00 00:00:00') || ($raw_date == ''))
            return false;

        $year = (int) substr($raw_date, 0, 4);
        $month = (int) substr($raw_date, 5, 2);
        $day = (int) substr($raw_date, 8, 2);
        $hour = (int) substr($raw_date, 11, 2);
        $minute = (int) substr($raw_date, 14, 2);
        $second = (int) substr($raw_date, 17, 2);

        $order_date_purchased = mktime($hour, $minute, $second, $month, $day, $year);
        if ($order_date_purchased >= $cutoff_date) {
            return true;
        } else {
            return false;
        }
    }

    function check() {
        if (!isset($this->check)) {
            $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_STATUS'");
            $this->check = xtc_db_num_rows($check_query);
        }

        return $this->check;
    }

    function keys() {
        return array('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_STATUS',
            'MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_SORT_ORDER',
            'MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CUMORDER_PERIOD',
            'MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_TABLE',
            'MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_SHIPPING',
            'MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_TAX',
            'MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CALC_TAX',
            'MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_ORDER_STATUS_CONSIDER'
        );
    }

    function install() {
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_STATUS', 'true', '6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_SORT_ORDER', '999', '6', '2', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_SHIPPING', 'true', '6', '3', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_INC_TAX', 'true', '6', '4','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CALC_TAX', 'false', '6', '5','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_CUMORDER_PERIOD', 'year', '6', '6','xtc_cfg_select_option(array(\'alltime\', \'year\', \'quarter\', \'month\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_TABLE', '1000:5,1500:7.5,2000:10,3000:12.5,5000:15', '6', '7', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_LOYALTY_DISCOUNT_ORDER_STATUS_CONSIDER', '" . xtc_order_statuses_defaultlist() . "', '6', '8', now())");
    }

    function remove() {
        $keys = '';
        $keys_array = $this->keys();
        for ($i = 0; $i < sizeof($keys_array); $i++) {
            $keys .= "'" . $keys_array[$i] . "',";
        }
        $keys = substr($keys, 0, -1);

        xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in (" . $keys . ")");
    }

}

function xtc_order_statuses_defaultlist() {

    $orders_status_arr = xtc_get_orders_status();

    $ret = '';
    $sep = '';
    foreach ($orders_status_arr as $orders_status) {
        $ret .= $sep . $orders_status['id'];
        $sep = ',';
    }

    return $ret;
}

