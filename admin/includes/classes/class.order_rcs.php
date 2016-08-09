<?php

/* -----------------------------------------------------------------
 * 	$Id: order_rcs.php 420 2013-06-19 18:04:39Z akausch $
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


defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_FS_INC . 'xtc_get_tax_description.inc.php');

class order {

    var $info, $totals, $products, $customer, $delivery, $content_type;

    // function __construct() {
    // }
    function order($customer_id) {
        global $xtPrice;
        $this->info = array();
        $this->totals = array();
        $this->products = array();
        $this->customer = array();
        $this->delivery = array();

        $this->cart($customer_id);
    }

    function cart($customer_id) {
        global $currencies, $xtPrice;

        $this->content_type = $_SESSION['cart']->get_content_type();

        $customer_address_query = xtc_db_query("SELECT c.payment_unallowed,c.shipping_unallowed,c.customers_firstname,c.customers_cid, c.customers_gender,c.customers_lastname, c.customers_telephone, c.customers_email_address, c.customers_default_address_id, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, co.countries_id, co.countries_name, co.countries_iso_code_2, co.countries_iso_code_3, co.address_format_id, ab.entry_state from " . TABLE_CUSTOMERS . " c, " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) left join " . TABLE_COUNTRIES . " co on (ab.entry_country_id = co.countries_id) where c.customers_id = '" . $customer_id . "' and ab.customers_id = '" . $customer_id . "' and c.customers_default_address_id = ab.address_book_id");
        $customer_address = xtc_db_fetch_array($customer_address_query);

        $shipping_address_query = xtc_db_query("SELECT ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_country_id, c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id, ab.entry_state from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) left join " . TABLE_COUNTRIES . " c on (ab.entry_country_id = c.countries_id) where ab.customers_id = '" . $customer_id . "' and ab.address_book_id = '" . $customer_address['customers_default_address_id'] . "'");
        $shipping_address = xtc_db_fetch_array($shipping_address_query);

        $billing_address_query = xtc_db_query("SELECT ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_country_id, c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id, ab.entry_state from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) left join " . TABLE_COUNTRIES . " c on (ab.entry_country_id = c.countries_id) where ab.customers_id = '" . $customer_id . "' and ab.address_book_id = '" . $customer_address['customers_default_address_id'] . "'");
        $billing_address = xtc_db_fetch_array($billing_address_query);

        $tax_address_query = xtc_db_query("SELECT ab.entry_country_id, ab.entry_zone_id from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) where ab.customers_id = '" . $customer_id . "' and ab.address_book_id = '" . $customer_address['customers_default_address_id'] . "'");
        $tax_address = xtc_db_fetch_array($tax_address_query);

        $this->info = array('order_status' => DEFAULT_ORDERS_STATUS_ID,
            'currency' => DEFAULT_CURRENCY,
            'currency_value' => $xtPrice->currencies[$_SESSION['currency']]['value'],
            'payment_method' => $_SESSION['payment'],
            'cc_type' => (isset($_SESSION['payment']) == 'cc' && isset($_SESSION['ccard']['cc_type']) ? $_SESSION['ccard']['cc_type'] : ''),
            'cc_owner' => (isset($_SESSION['payment']) == 'cc' && isset($_SESSION['ccard']['cc_owner']) ? $_SESSION['ccard']['cc_owner'] : ''),
            'cc_number' => (isset($_SESSION['payment']) == 'cc' && isset($_SESSION['ccard']['cc_number']) ? $_SESSION['ccard']['cc_number'] : ''),
            'cc_expires' => (isset($_SESSION['payment']) == 'cc' && isset($_SESSION['ccard']['cc_expires']) ? $_SESSION['ccard']['cc_expires'] : ''),
            'cc_start' => (isset($_SESSION['payment']) == 'cc' && isset($_SESSION['ccard']['cc_start']) ? $_SESSION['ccard']['cc_start'] : ''),
            'cc_issue' => (isset($_SESSION['payment']) == 'cc' && isset($_SESSION['ccard']['cc_issue']) ? $_SESSION['ccard']['cc_issue'] : ''),
            'cc_cvv' => (isset($_SESSION['payment']) == 'cc' && isset($_SESSION['ccard']['cc_cvv']) ? $_SESSION['ccard']['cc_cvv'] : ''),
            'shipping_method' => $_SESSION['shipping']['title'],
            'shipping_cost' => $_SESSION['shipping']['cost'],
            'comments' => $_SESSION['comments'],
            'shipping_class' => $_SESSION['shipping']['id'],
            'payment_class' => $_SESSION['payment'],
        );

        if (isset($_SESSION['payment']) && is_object($_SESSION['payment'])) {
            $this->info['payment_method'] = $_SESSION['payment']->title;
            $this->info['payment_class'] = $_SESSION['payment']->title;
            if (isset($_SESSION['payment']->order_status) && is_numeric($_SESSION['payment']->order_status) && ($_SESSION['payment']->order_status > 0)) {
                $this->info['order_status'] = $_SESSION['payment']->order_status;
            }
        }

        $this->customer = array('firstname' => $customer_address['customers_firstname'],
            'lastname' => $customer_address['customers_lastname'],
            'csID' => $customer_address['customers_cid'],
            'gender' => $customer_address['customers_gender'],
            'company' => $customer_address['entry_company'],
            'street_address' => $customer_address['entry_street_address'],
            'suburb' => $customer_address['entry_suburb'],
            'city' => $customer_address['entry_city'],
            'postcode' => $customer_address['entry_postcode'],
            'state' => ((xtc_not_null($customer_address['entry_state'])) ? $customer_address['entry_state'] : $customer_address['zone_name']),
            'zone_id' => $customer_address['entry_zone_id'],
            'country' => array('id' => $customer_address['countries_id'], 'title' => $customer_address['countries_name'], 'iso_code_2' => $customer_address['countries_iso_code_2'], 'iso_code_3' => $customer_address['countries_iso_code_3']),
            'format_id' => $customer_address['address_format_id'],
            'telephone' => $customer_address['customers_telephone'],
            'payment_unallowed' => $customer_address['payment_unallowed'],
            'shipping_unallowed' => $customer_address['shipping_unallowed'],
            'email_address' => $customer_address['customers_email_address']);

        $this->delivery = array('firstname' => $shipping_address['entry_firstname'],
            'lastname' => $shipping_address['entry_lastname'],
            'company' => $shipping_address['entry_company'],
            'street_address' => $shipping_address['entry_street_address'],
            'suburb' => $shipping_address['entry_suburb'],
            'city' => $shipping_address['entry_city'],
            'postcode' => $shipping_address['entry_postcode'],
            'state' => ((xtc_not_null($shipping_address['entry_state'])) ? $shipping_address['entry_state'] : $shipping_address['zone_name']),
            'zone_id' => $shipping_address['entry_zone_id'],
            'country' => array('id' => $shipping_address['countries_id'], 'title' => $shipping_address['countries_name'], 'iso_code_2' => $shipping_address['countries_iso_code_2'], 'iso_code_3' => $shipping_address['countries_iso_code_3']),
            'country_id' => $shipping_address['entry_country_id'],
            'format_id' => $shipping_address['address_format_id']);

        $this->billing = array('firstname' => $billing_address['entry_firstname'],
            'lastname' => $billing_address['entry_lastname'],
            'company' => $billing_address['entry_company'],
            'street_address' => $billing_address['entry_street_address'],
            'suburb' => $billing_address['entry_suburb'],
            'city' => $billing_address['entry_city'],
            'postcode' => $billing_address['entry_postcode'],
            'state' => ((xtc_not_null($billing_address['entry_state'])) ? $billing_address['entry_state'] : $billing_address['zone_name']),
            'zone_id' => $billing_address['entry_zone_id'],
            'country' => array('id' => $billing_address['countries_id'], 'title' => $billing_address['countries_name'], 'iso_code_2' => $billing_address['countries_iso_code_2'], 'iso_code_3' => $billing_address['countries_iso_code_3']),
            'country_id' => $billing_address['entry_country_id'],
            'format_id' => $billing_address['address_format_id']);

        $index = 0;
        $products = $_SESSION['cart']->get_products();
        for ($i = 0, $n = sizeof($products); $i < $n; $i++) {

            $products_price = $xtPrice->xtcGetPrice($products[$i]['id'], $format = false, $products[$i]['quantity'], $products[$i]['tax_class_id'], '') +
                    $xtPrice->xtcFormat($_SESSION['cart']->attributes_price($products[$i]['id']), false, $products[$i]['tax_class_id']);

            $this->products[$index] = array('qty' => $products[$i]['quantity'],
                'name' => $products[$i]['name'],
                'model' => $products[$i]['model'],
                'tax_class_id' => $products[$i]['tax_class_id'],
                'tax' => xtc_get_tax_rate($products[$i]['tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']),
                'tax_description' => xtc_get_tax_description($products[$i]['tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']),
                'price' => $products_price,
                'final_price' => $products_price * $products[$i]['quantity'],
                'shipping_time' => $products[$i]['shipping_time'],
                'weight' => $products[$i]['weight'],
                'id' => $products[$i]['id']);

            if ($products[$i]['attributes']) {
                $subindex = 0;
                reset($products[$i]['attributes']);
                while (list($option, $value) = each($products[$i]['attributes'])) {
                    $attributes_query = xtc_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . $products[$i]['id'] . "' and pa.options_id = '" . $option . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $value . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $_SESSION['languages_id'] . "' and poval.language_id = '" . $_SESSION['languages_id'] . "'");
                    $attributes = xtc_db_fetch_array($attributes_query);

                    $this->products[$index]['attributes'][$subindex] = array('option' => $attributes['products_options_name'],
                        'value' => $attributes['products_options_values_name'],
                        'option_id' => $option,
                        'value_id' => $value,
                        'prefix' => $attributes['price_prefix'],
                        'price' => $attributes['options_values_price']);

                    $subindex++;
                }
            }

            $shown_price = $this->products[$index]['final_price'];
            $this->info['subtotal'] += $shown_price;
            if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
                $shown_price_tax = $shown_price - ($shown_price / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
            }

            $products_tax = $this->products[$index]['tax'];
            $products_tax_description = $this->products[$index]['tax_description'];
            if ($_SESSION['customers_status']['customers_status_show_price_tax'] == '1') {
                if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
                    $this->info['tax'] += $shown_price_tax - ($shown_price_tax / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
                    $this->info['tax_groups'][TAX_ADD_TAX . "$products_tax_description"] += (($shown_price_tax / (100 + $products_tax)) * $products_tax);
                } else {
                    $this->info['tax'] += $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
                    $this->info['tax_groups'][TAX_ADD_TAX . "$products_tax_description"] += (($shown_price / (100 + $products_tax)) * $products_tax);
                }
            } else {
                if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
                    $this->info['tax'] += ($shown_price_tax / 100) * ($products_tax);
                    $this->info['tax_groups'][TAX_NO_TAX . "$products_tax_description"] += ($shown_price_tax / 100) * ($products_tax);
                } else {
                    $this->info['tax'] += ($shown_price / 100) * ($products_tax);
                    $this->info['tax_groups'][TAX_NO_TAX . "$products_tax_description"] += ($shown_price / 100) * ($products_tax);
                }
            }
            $index++;
        }

        //$this->info['shipping_cost']=0;
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == '0') {
            $this->info['total'] = $this->info['subtotal'] + $xtPrice->xtcFormat($this->info['shipping_cost'], false, 0, true);
            if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1') {
                $this->info['total'] -= ($this->info['subtotal'] / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
            }
        } else {

            $this->info['total'] = $this->info['subtotal'] + $xtPrice->xtcFormat($this->info['shipping_cost'], false, 0, true);
            if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1') {
                $this->info['total'] -= ($this->info['subtotal'] / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
            }
        }
    }

}
