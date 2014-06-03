<?php

/* -----------------------------------------------------------------
 * 	$Id: ot_total.php 843 2014-02-03 13:49:21Z akausch $
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

class ot_total {

    var $title, $output;

    function ot_total() {
        $this->code = 'ot_total';
        $this->title = MODULE_ORDER_TOTAL_TOTAL_TITLE;
        $this->description = MODULE_ORDER_TOTAL_TOTAL_DESCRIPTION;
        $this->enabled = ((MODULE_ORDER_TOTAL_TOTAL_STATUS == 'true') ? true : false);
        $this->sort_order = MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER;

        $this->output = array();
    }

    function process() {
        global $order, $xtPrice;
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
			if (STORE_COUNTRY == '22' || STORE_COUNTRY == '204') {
				$total = round($order->info['total']*20, 0)/20;
			} else {
				$total = $order->info['total'];
			}
			$this->output[] = array('title' => $this->title . ':',
                'text' => $xtPrice->xtcFormat($total, true),
                'value' => $xtPrice->xtcFormat($total, false));
        }

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			if (STORE_COUNTRY == '22' || STORE_COUNTRY == '204') {
				$total = round($order->info['total']*20, 0)/20;
				$tax = round($order->info['tax']*20, 0)/20;
			} else {
				$total = $order->info['total'];
				$tax = $order->info['tax'];
			}
            $this->output[] = array('title' => MODULE_ORDER_TOTAL_TOTAL_TITLE_NO_TAX_BRUTTO . ':',
                'text' => $xtPrice->xtcFormat($tax + $total, true),
                'value' => $xtPrice->xtcFormat($total + $tax, false));
        }
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) {
			if (STORE_COUNTRY == '22' || STORE_COUNTRY == '204') {
				$total = round($order->info['total']*20, 0)/20;
			} else {
				$total = $order->info['total'];
			}
			$this->output[] = array('title' => MODULE_ORDER_TOTAL_TOTAL_TITLE_NO_TAX . ':',
                'text' => $xtPrice->xtcFormat($total, true),
                'value' => $xtPrice->xtcFormat($total, false));
        }
    }

    function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_TOTAL_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }

        return $this->_check;
    }

    function keys() {
        return array('MODULE_ORDER_TOTAL_TOTAL_STATUS', 'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER');
    }

    function install() {
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_TOTAL_STATUS', 'true', '6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER', '6','6', '2', now())");
    }

    function remove() {
        xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

}

