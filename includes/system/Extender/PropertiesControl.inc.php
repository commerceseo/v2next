<?php

/* --------------------------------------------------------------
  PropertiesControl.inc.php 2014-02-03 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

#TODO: create language files

require_once(DIR_FS_INC . 'gm_prepare_number.inc.php');
require_once(DIR_FS_INC . 'gm_convert_qty.inc.php');
require_once(DIR_FS_INC . 'xtc_get_vpe_name.inc.php');

class PropertiesControl {

    var $v_id_seperator = 'x';
    protected $coo_lang_manager;

    function PropertiesControl() {
        $this->coo_lang_manager = cseohookfactory::create_object('LanguageTextManager', array('properties_dropdown', $_SESSION['languages_id']));
    }

    function get_combis_id_by_value_ids_array($p_products_id, $p_value_ids_array) {
        $t_accepted_combis_id = 0;
        $coo_properties_data_agent = cseohookfactory::create_object('PropertiesDataAgent');
        $t_accepted_combis_ids_array = $coo_properties_data_agent->get_available_combis_ids_by_values($p_products_id, $p_value_ids_array, false);
        if (is_array($t_accepted_combis_ids_array) && count($t_accepted_combis_ids_array) == 1) {
            $t_accepted_combis_id = $t_accepted_combis_ids_array[0];
        }
        return $t_accepted_combis_id;
    }

    function get_orders_products_properties($p_orders_products_id) {
        $t_properties_output_array = array();

        $coo_data_object_group = new GMDataObjectGroup('orders_products_properties', array('orders_products_id' => $p_orders_products_id));
        $t_data_object_array = $coo_data_object_group->get_data_objects_array();

        foreach ($t_data_object_array as $t_data_object_item) {
            $t_properties_output_array[] = array(
                'orders_products_properties_id' => $t_data_object_item->get_data_value('orders_products_properties_id'),
                'orders_products_id' => $t_data_object_item->get_data_value('orders_products_id'),
                'properties_name' => $t_data_object_item->get_data_value('properties_name'),
                'values_name' => $t_data_object_item->get_data_value('values_name'),
                'properties_price_type' => $t_data_object_item->get_data_value('properties_price_type'),
                'properties_price' => $t_data_object_item->get_data_value('properties_price')
            );
        }
        return $t_properties_output_array;
    }

    function get_properties_combis_price($p_properties_combis_id) {
        $coo_data_object = new GMDataObject('products_properties_combis', array('products_properties_combis_id' => $p_properties_combis_id));

        $t_products_id = $coo_data_object->get_data_value('products_id');
        $t_combi_price = $coo_data_object->get_data_value('combi_price');

        return $t_combi_price;
    }

    function get_properties_combis_details($p_properties_combis_id, $p_language_id) {
        $c_properties_combis_id = (int) $p_properties_combis_id;
        $c_language_id = (int) $p_language_id;

        #check parameters
        if ($c_properties_combis_id == 0)
            trigger_error('combis_id=0', E_USER_ERROR);
        if ($c_language_id == 0)
            trigger_error('language_id=0', E_USER_ERROR);

        $coo_data_agent = cseohookfactory::create_object('PropertiesDataAgent');
        $t_details_array = $coo_data_agent->get_properties_combis_details($c_properties_combis_id, $c_language_id);

        #check result arrays
        //if(sizeof($t_details_array) == 0) trigger_error('sizeof($t_details_array)=0 pid-lid'. $c_properties_combis_id .'-'. $c_language_id, E_USER_ERROR);
        if (sizeof($t_details_array) == 0) {
            # given combis_id doesnt exist. clean up!
            $coo_control = cseohookfactory::create_object('PropertiesCombisAdminControl');
            $coo_control->clear_baskets_combis($c_properties_combis_id);
        }
        return $t_details_array;
    }

    function get_properties_combis_quantity($p_properties_combis_id) {
        $coo_data_object = new GMDataObject('products_properties_combis', array('products_properties_combis_id' => $p_properties_combis_id));
        $t_quantity = $coo_data_object->get_data_value('combi_quantity');
        return $t_quantity;
    }

    function get_properties_combis_shipping_time($p_properties_combis_id) {
        $coo_data_object = new GMDataObject('products_properties_combis', array('products_properties_combis_id' => $p_properties_combis_id));
        $t_shipping_time_id = $coo_data_object->get_data_value('combi_shipping_status_id');
        $coo_data_object = new GMDataObject('shipping_status', array('shipping_status_id' => $t_shipping_time_id, 'language_id' => $_SESSION['languages_id']));
        $t_shipping_time_value = $coo_data_object->get_data_value('shipping_status_name');

        return $t_shipping_time_value;
    }

    function get_properties_combis_weight($p_properties_combis_id) {
        $coo_data_object = new GMDataObject('products_properties_combis', array('products_properties_combis_id' => $p_properties_combis_id));
        $t_weight = $coo_data_object->get_data_value('combi_weight');

        return $t_weight;
    }

    function get_properties_combis_model($p_properties_combis_id) {
        $coo_data_object = new GMDataObject('products_properties_combis', array('products_properties_combis_id' => $p_properties_combis_id));
        $t_model = $coo_data_object->get_data_value('combi_model');

        return $t_model;
    }

    function add_properties_combi_to_orders_product($p_properties_combis_id, $p_orders_products_id) {
        $t_output_counter = 0; #count of added properties

        $coo_xtc_price = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);

        $c_properties_combis_id = (int) $p_properties_combis_id;
        $c_orders_products_id = (int) $p_orders_products_id;

        #check parameters
        if ($c_properties_combis_id == 0)
            trigger_error('combis_id=0 c-p ' . $c_properties_combis_id . '-' . $p_orders_products_id, E_USER_ERROR);
        if ($c_orders_products_id == 0)
            trigger_error('orders_products_id=0 c-p ' . $c_orders_products_id . '-' . $p_orders_products_id, E_USER_ERROR);

        $coo_data_agent = cseohookfactory::create_object('PropertiesDataAgent');
        $t_details_array = $coo_data_agent->get_properties_combis_details($c_properties_combis_id, $_SESSION['languages_id']);
        #check result arrays
        if (sizeof($t_details_array) == 0)
            trigger_error('sizeof($t_details_array)=0 cid-pid' . $p_properties_combis_id . '-' . $p_orders_products_id, E_USER_ERROR);

        # data object for combis_model
        $coo_data_object = new GMDataObject('products_properties_combis', array('products_properties_combis_id' => $c_properties_combis_id));
        $t_combi_model = $coo_data_object->get_data_value('combi_model');
        $t_combi_price = (double) $coo_data_object->get_data_value('combi_price');
        $t_combi_price_type = $coo_data_object->get_data_value('combi_price_type');

        if ($t_combi_model != '') {
            $t_combi_shipping_status = '';
            $coo_orders_products = new GMDataObject('orders_products', array('orders_products_id' => $c_orders_products_id));
            $t_products_id = $coo_orders_products->get_data_value('products_id');
            // $coo_properties_combis_admin = cseohookfactory::create_object('PropertiesCombisAdminControl');
            $t_use_properties_combis_shipping_time = $coo_properties_combis_admin->get_use_properties_combis_shipping_time($t_products_id);
            if ($t_use_properties_combis_shipping_time == 1) {
                $t_combi_shipping_status_id = $coo_data_object->get_data_value('combi_shipping_status_id');
                $t_main = new main();
                $t_combi_shipping_status = $t_main->getShippingStatusName($t_combi_shipping_status_id);
            }
            # data object for updating products_model
            $coo_data_object = new GMDataObject('orders_products', array('orders_products_id' => $c_orders_products_id), false, false);
            $t_products_model = $coo_data_object->get_data_value('products_model');

            if (APPEND_PROPERTIES_MODEL == "true" && $t_products_model != '') {
                $t_products_model = $t_products_model . '-' . $t_combi_model;
            } else {
                $t_products_model = $t_combi_model;
            }
            if ($t_products_model == '') {
                $t_products_model = '&nbsp;';
            }
            if ($t_combi_shipping_status != '') {
                $coo_data_object->set_data_value('products_shipping_time', $t_combi_shipping_status);
            }
            $coo_data_object->set_data_value('products_model', $t_products_model);
            $coo_data_object->set_data_value('properties_combi_model', $t_combi_model);
            $coo_data_object->save_body_data();
        }

        $coo_data_object = new GMDataObject('orders_products', array('orders_products_id' => $c_orders_products_id), false, false);

        if ($coo_data_object->get_data_value('allow_tax') == '1') {
            $t_combi_price = $t_combi_price * (1 + (double) $coo_data_object->get_data_value('products_tax') / 100);
        }

        $t_combi_price = $coo_xtc_price->xtcCalculateCurr($t_combi_price);
        $t_combi_price = round($t_combi_price, (int) PRICE_PRECISION);
        $coo_data_object->set_data_value('properties_combi_price', $t_combi_price);
        $coo_data_object->save_body_data();

        # data object for inserts
        $coo_data_object = new GMDataObject('orders_products_properties');

        foreach ($t_details_array as $t_item_array) {
            # reset data object for insert
            $coo_data_object->set_keys(array('orders_products_properties_id' => false));

            $coo_data_object->set_data_value('orders_products_id', $c_orders_products_id);
            $coo_data_object->set_data_value('properties_name', $t_item_array['properties_name']);
            $coo_data_object->set_data_value('values_name', $t_item_array['values_name']);
            $coo_data_object->set_data_value('properties_price_type', $t_combi_price_type);
            $coo_data_object->set_data_value('properties_price', $t_item_array['value_price']);

            # VARIO
            $coo_data_object->set_data_value('products_properties_combis_id', $c_properties_combis_id);

            # do insert
            $coo_data_object->save_body_data();
            $t_output_counter++;
        }
        return $t_output_counter;
    }

    function change_combis_quantity($p_properties_combis_id, $p_quantity_change) {
        $coo_data_object = new GMDataObject('products_properties_combis', array('products_properties_combis_id' => $p_properties_combis_id));

        $t_old_qty = $coo_data_object->get_data_value('combi_quantity');
        $t_new_qty = $t_old_qty + $p_quantity_change;

        $coo_data_object->set_data_value('combi_quantity', $t_new_qty);
        $coo_data_object->save_body_data();
        return $t_new_qty;
    }

    # compose products_id and combis_id for customers_basket-table and order-object

    function get_baskets_products_id($p_products_id, $p_combis_id) {
        # sample: 1{2}6{1}3x56
        $t_output_id = $p_products_id . $this->v_id_seperator . $p_combis_id;

        $t_output_id = strip_tags($t_output_id);
        return $t_output_id;
    }

    function extract_combis_id($p_baskets_products_id) {
        // $t_output_combis_id = '';
        // # sample: 1{2}6{1}3x56
        // $t_parts_array = explode($this->v_id_seperator, $p_baskets_products_id);
        // if (sizeof($t_parts_array) > 1) {
        // $t_output_combis_id = $t_parts_array[1];
        // }
        $t_output_combis_id = xtc_db_fetch_array(xtc_db_query("SELECT properties_id FROM " . TABLE_CUSTOMERS_BASKET . " WHERE products_id = '" . $p_baskets_products_id . "';"));

        return $t_output_combis_id; #empty, if no id found
    }

    function extract_products_id($p_baskets_products_id) {
        # sample: 1{2}6{1}3x56
        $t_parts_array = explode($this->v_id_seperator, $p_baskets_products_id);
        $t_cleared_output = $t_parts_array[0];

        return $t_cleared_output;
    }

    # DEPRECATED

    function clear_baskets_products_id($p_baskets_products_id) {
        $t_products_id = $this->extract_products_id($p_baskets_products_id);
        return $t_products_id;
    }

    function get_shop_languages_data() {
        $coo_properties_data_agent = cseohookfactory::create_object('PropertiesDataAgent');
        $t_languages_array = $coo_properties_data_agent->get_shop_languages_data();

        return $t_languages_array;
    }

    function get_products_properties_data($p_products_id, $p_language_id) {
        $coo_properties_data = cseohookfactory::create_object('ProductPropertiesData', array($p_products_id, $p_language_id));
        $t_properties_data_array = $coo_properties_data->get_properties_struct();

        return $t_properties_data_array;
    }

    function get_use_properties_combis_quantity($p_products_id) {

        $t_products_id = (int) $p_products_id;

        $coo_data_object = new GMDataObject('products', array('products_id' => $t_products_id));

        $t_quantity = $coo_data_object->get_data_value('use_properties_combis_quantity');

        return $t_quantity;
    }

    public function get_cheapest_combi($p_products_id, $p_language_id) {
        $coo_properties_data = cseohookfactory::create_object('PropertiesDataAgent');
        return $coo_properties_data->get_cheapest_combi($p_products_id, $p_language_id);
    }

    public function get_available_combis_ids_by_values($p_products_id, $p_selected_values, $p_check_quantity = true) {
        $coo_properties_data = cseohookfactory::create_object('PropertiesDataAgent');
        return $coo_properties_data->get_available_combis_ids_by_values($p_products_id, $p_selected_values, $p_check_quantity);
    }

    public function get_available_properties_values_by_values($p_products_id, $p_selected_values) {
        $coo_properties_data = cseohookfactory::create_object('PropertiesDataAgent');
        $t_values_ids_array = array();
        if (is_array($p_selected_values) && count($p_selected_values) > 0) {
            $t_combis_ids = $coo_properties_data->get_available_combis_ids_by_values($p_products_id, $p_selected_values);
            $t_values_ids_array = array_merge($t_values_ids_array, $coo_properties_data->get_values_by_combis_ids($t_combis_ids));
            $t_combis_ids = array();
            if (count($p_selected_values) > 1) {
                for ($i = 0; $i < count($p_selected_values); $i++) {
                    $t_tmp_selected_values = $p_selected_values;
                    array_splice($t_tmp_selected_values, $i, 1);
                    $t_combis_ids = array_merge($t_combis_ids, $coo_properties_data->get_available_combis_ids_by_values($p_products_id, $t_tmp_selected_values));
                }
            }
            $t_combis_ids = array_unique($t_combis_ids);
            if (count($t_combis_ids) > 0) {
                $t_combis_ids = array_chunk($t_combis_ids, 100);
                foreach ($t_combis_ids as $t_combis_ids_chunk) {
                    $t_values_ids_array = array_merge($t_values_ids_array, $coo_properties_data->get_values_by_combis_ids($t_combis_ids_chunk, $p_selected_values));
                }
                $t_values_ids_array = array_unique($t_values_ids_array);
            }
        }
        return $t_values_ids_array;
    }

    function get_combis_full_struct($p_properties_combis_id, $p_language_id) {
        $coo_properties_data_agent = cseohookfactory::create_object('PropertiesDataAgent');
        $t_combis_struct = $coo_properties_data_agent->get_combis_full_struct($p_properties_combis_id, $p_language_id);

        return $t_combis_struct;
    }

    public function split_properties_values_string($p_properties_values_string) {
        $coo_properties_data_agent = cseohookfactory::create_object('PropertiesDataAgent');
        $t_properties_values_array = $coo_properties_data_agent->split_properties_values_string($p_properties_values_string);

        return $t_properties_values_array;
    }

    public function count_properties_to_product($p_products_id) {
        $coo_properties_data_agent = cseohookfactory::create_object('PropertiesDataAgent');
        return $coo_properties_data_agent->count_properties_to_product($p_products_id);
    }

    public function get_selected_combi($p_products_id, $p_languages_id, $p_properties_values, $p_check_quantity = true) {
        $t_selected_values = array();
        $t_selected_combi = false;

        $coo_properties_data_agent = cseohookfactory::create_object('PropertiesDataAgent');

        if (is_string($p_properties_values) && trim($p_properties_values) != '') {
            $t_selected_values = $coo_properties_data_agent->split_properties_values_string($p_properties_values);
        } else if (is_array($p_properties_values) && count($p_properties_values) > 0) {
            $t_selected_values = $p_properties_values;
        }

        if (is_array($t_selected_values) && count($t_selected_values) > 0) {
            // GET PROPERTIES COUNT IN PRODUCT
            $t_properties_to_product_count = $coo_properties_data_agent->count_properties_to_product($p_products_id);

            // GET SELECTED COMBI BY VALUES
            $t_available_combis_ids = $coo_properties_data_agent->get_available_combis_ids_by_values($p_products_id, $t_selected_values, $p_check_quantity);
            if (is_array($t_available_combis_ids) && count($t_available_combis_ids) == 1 && count($t_selected_values) == $t_properties_to_product_count) {
                $t_selected_combi = $coo_properties_data_agent->get_combis_full_struct($t_available_combis_ids[0], $p_languages_id);
            }
        }
        return $t_selected_combi;
    }

    public function get_selection_data($p_products_id, $p_languages_id, $p_quantity, $p_properties_values_string, $p_currency, $p_customers_status_id) {
        $t_output_array = array();

        $c_products_id = (int) $p_products_id;
        if ($c_products_id == 0) {
            trigger_error('$p_products_id is null: PropertiesControl->get_selection_data()');
        }

        $c_languages_id = (int) $p_languages_id;
        if ($c_languages_id == 0) {
            trigger_error('$p_languages_id is null: PropertiesControl->get_selection_data()');
        }

        // GET PRODUCT
        $coo_product_object = cseohookfactory::create_object("GMDataObject", array("products", array("products_id" => $c_products_id)));

        $c_user_quantity = $p_quantity;
        if (is_numeric($p_quantity) == false) {
            trigger_error('$p_quantity is not a number: PropertiesControl->get_selection_data()');
        }
        if ($p_quantity < $coo_product_object->get_data_value('gm_min_order')) {
            $c_user_quantity = $coo_product_object->get_data_value('gm_min_order');
        }

        if (is_string($p_properties_values_string) == false || trim($p_properties_values_string) == '') {
            trigger_error('$p_properties_values_string is not a string: PropertiesControl->get_selection_data()');
        }

        $c_properties_values_string = $p_properties_values_string;

        $c_currency = $p_currency;
        if (trim($p_currency) == '') {
            trigger_error('$p_currency is empty: PropertiesControl->get_selection_data()');
        }

        $c_customers_status_id = (int) $p_customers_status_id;
        if ($c_customers_status_id == 0 && $p_customers_status_id != 0) {
            trigger_error('$p_customers_status_id is not an integer: PropertiesControl->get_selection_data()');
        }

        $xtPrice = new xtcPrice($c_currency, $c_customers_status_id);
        $xtPrice->showFrom_Attributes = true;

        $coo_properties_data_agent = cseohookfactory::create_object('PropertiesDataAgent');

        $t_properties_values_array = $coo_properties_data_agent->split_properties_values_string($c_properties_values_string);
        $t_properties_values_count = count($t_properties_values_array);

        $t_properties_to_products_count = $coo_properties_data_agent->count_properties_to_product($c_products_id);

        if ($t_properties_values_count == $t_properties_to_products_count) {
            $t_selected_combi = $this->get_selected_combi($c_products_id, $c_languages_id, $t_properties_values_array, false);

            if ($t_selected_combi != false) {
                $xtPrice->showFrom_Attributes = false;

                $t_output_array = $this->quantity_check($coo_product_object, $t_selected_combi, $c_user_quantity);

                $t_products_shipping_time = $coo_product_object->get_data_value('products_shippingtime');
                $t_products_weight = $coo_product_object->get_data_value('products_weight');
                $t_products_model = $coo_product_object->get_data_value('products_model');
                $t_products_quantity = $coo_product_object->get_data_value('products_quantity');
                $t_use_properties_combis_weight = $coo_product_object->get_data_value('use_properties_combis_weight');
                $t_use_properties_combis_shipping_time = $coo_product_object->get_data_value('use_properties_combis_shipping_time');
                $t_use_properties_combis_quantity = $coo_product_object->get_data_value('use_properties_combis_quantity');

                if ($t_use_properties_combis_weight == 0) {
                    $t_output_array['weight'] = gm_prepare_number($t_selected_combi['combi_weight'] + $t_products_weight, $xtPrice->currencies[$xtPrice->actualCurr]['decimal_point']);
                } else {
                    $t_output_array['weight'] = gm_prepare_number($t_selected_combi['combi_weight'], $xtPrice->currencies[$xtPrice->actualCurr]['decimal_point']);
                }

                $coo_shipping_time = cseohookfactory::create_object('ProductsShippingStatusSource');
                if ($t_use_properties_combis_shipping_time == 0) {
                    if ($t_products_shipping_time != 0) {
                        $t_shipping_status = $coo_shipping_time->get_shipping_status($t_products_shipping_time, $c_languages_id);
                    }
                } else {
                    if ($t_selected_combi['combi_shipping_status_id'] != 0) {
                        $t_shipping_status = $coo_shipping_time->get_shipping_status($t_selected_combi['combi_shipping_status_id'], $c_languages_id);
                    }
                }
                $t_output_array['shipping_status_image'] = $t_shipping_status['shipping_status_image'];
                $t_output_array['shipping_status_name'] = $t_shipping_status['shipping_status_name'];

                $t_output_array['model'] = $t_selected_combi['combi_model'];
                if (APPEND_PROPERTIES_MODEL == "true" && trim($t_products_model) != '' && trim($t_selected_combi['combi_model']) != '') {
                    $t_output_array['model'] = $t_products_model . "-" . $t_selected_combi['combi_model'];
                } else if (APPEND_PROPERTIES_MODEL == "true") {
                    $t_output_array['model'] = $t_products_model . $t_selected_combi['combi_model'];
                }
                if ($t_output_array['model'] == '') {
                    $t_output_array['model'] = '&nbsp;';
                }

                if (($t_use_properties_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_properties_combis_quantity == 2) {
                    $t_output_array['quantity'] = gm_convert_qty($t_selected_combi['combi_quantity'], false);
                } else if (($t_use_properties_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'false') || $t_use_properties_combis_quantity == 1) {
                    $t_output_array['quantity'] = gm_convert_qty($t_products_quantity, false);
                }

                $t_total_price = $xtPrice->xtcGetPrice($c_products_id, true, $c_user_quantity, $coo_product_object->get_data_value('products_tax_class_id'), 0, 1, 0, true, true, $t_selected_combi['products_properties_combis_id']);

                $t_output_array['price'] = $t_total_price['formated'];

                if ($coo_product_object->get_data_value('products_vpe_status') == 1 && $t_selected_combi['products_vpe_id'] != 0 && $t_selected_combi['vpe_value'] != 0 && $t_total_price['plain'] > 0) {
                    $t_output_array['price'] .= '<br /><span class="tax-shipping-text gm_products_vpe">' . $xtPrice->xtcFormat($t_total_price['plain'] * (1 / $t_selected_combi['vpe_value']), true) . TXT_PER . $t_selected_combi['products_vpe_name'] . '</span><br />';
                } else if ($coo_product_object->get_data_value('products_vpe_status') == 1) {
                    $t_output_array['price'] .= '<br />';
                }
            } else {
                $t_output_array['status'] = 'combi_not_exists';
                $t_output_array['message'] = $this->coo_lang_manager->get_text('COMBI_NOT_EXIST');
                ;
            }
        } else {
            $t_output_array['status'] = 'no_combi_selected';
            $t_output_array['message'] = '';
        }

        if (isset($t_output_array['shipping_status_image']) == false) {
            $t_output_array['shipping_status_image'] = 'gray.png';
            $t_output_array['shipping_status_name'] = '';
        }

        if (isset($t_output_array['weight']) == false) {
            $t_output_array['weight'] = '-';
        }

        if (isset($t_output_array['model']) == false) {
            $t_output_array['model'] = '-';
        }

        if (isset($t_output_array['quantity']) == false) {
            if (($coo_product_object->get_data_value('use_properties_combis_quantity') == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'false') || $coo_product_object->get_data_value('use_properties_combis_quantity') == 1) {
                $t_output_array['quantity'] = gm_convert_qty($coo_product_object->get_data_value('products_quantity'), false);
            } else {
                $t_output_array['quantity'] = '-';
            }
        }

        if (isset($t_output_array['price']) == false) {
            $t_cheapest_combi = $this->get_cheapest_combi($c_products_id, $c_languages_id);
            $t_total_price = $xtPrice->xtcGetPrice($c_products_id, true, $c_user_quantity, $coo_product_object->get_data_value('products_tax_class_id'), $coo_product_object->get_data_value('products_price'), 1, 0, true, true, $t_cheapest_combi['products_properties_combis_id']);

            $t_output_array['price'] = $t_total_price['formated'];

            if ($coo_product_object->get_data_value('products_vpe_status') == 1 && $t_cheapest_combi['vpe_value'] != 0.0 && $t_cheapest_combi['products_vpe_id'] != 0 && $t_total_price['plain'] > 0) {
                $t_output_array['price'] .= '<br /><span class="tax-shipping-text gm_products_vpe">' . $xtPrice->xtcFormat($t_total_price['plain'] * (1 / $t_cheapest_combi['vpe_value']), true) . TXT_PER . xtc_get_vpe_name($t_cheapest_combi['products_vpe_id']) . '</span><br />';
            }
        }

        $coo_properties_view = cseohookfactory::create_object('PropertiesView');
        $t_output_array['html'] = $coo_properties_view->get_selection_form($c_products_id, $c_languages_id, $t_properties_values_array, $t_selected_combi, $c_user_quantity);

        return $t_output_array;
    }

    public function check_combis_quantity($p_products_id, $p_languages_id, $p_quantity, $p_properties_values_string) {
        $c_products_id = (int) $p_products_id;
        if ($c_products_id == 0) {
            trigger_error('$p_products_id is null: PropertiesControl->check_combis_quantity()');
        }

        $c_languages_id = (int) $p_languages_id;
        if ($c_languages_id == 0) {
            trigger_error('$p_languages_id is null: PropertiesControl->check_combis_quantity()');
        }

        // GET PRODUCT
        $coo_product_object = cseohookfactory::create_object("GMDataObject", array("products", array("products_id" => $c_products_id)));

        $c_user_quantity = $p_quantity;
        if (is_numeric($p_quantity) == false) {
            trigger_error('$p_quantity is not a number: PropertiesControl->check_combis_quantity()');
        }
        if ($p_quantity < $coo_product_object->get_data_value('gm_min_order')) {
            $c_user_quantity = $coo_product_object->get_data_value('gm_min_order');
        }

        if (is_string($p_properties_values_string) == false || trim($p_properties_values_string) == '') {
            trigger_error('$p_properties_values_string is not a string: PropertiesControl->check_combis_quantity()');
        }

        $c_properties_values_string = $p_properties_values_string;

        $coo_properties_data_agent = cseohookfactory::create_object('PropertiesDataAgent');
        $t_output_array = array();

        $t_properties_values_array = $coo_properties_data_agent->split_properties_values_string($c_properties_values_string);
        $t_properties_values_count = count($t_properties_values_array);

        $t_properties_to_products_count = $coo_properties_data_agent->count_properties_to_product($c_products_id);

        if ($t_properties_values_count > 0 && $t_properties_to_products_count > 0) {
            if ($t_properties_values_count != $t_properties_to_products_count) {
                // NO COMBI SELECTED
                $t_output_array['status'] = 'no_combi_selected';
                $t_output_array['message'] = '';
            } else {
                $t_available_combis_ids = $coo_properties_data_agent->get_available_combis_ids_by_values($p_products_id, $t_properties_values_array);
                if (is_array($t_available_combis_ids) && count($t_available_combis_ids) == 1) {
                    // VALID COMBI
                    $t_selected_combi = $coo_properties_data_agent->get_combis_full_struct($t_available_combis_ids[0], $p_languages_id);

                    $t_output_array = $this->quantity_check($coo_product_object, $t_selected_combi, $c_user_quantity);
                } else {
                    // NO VALID COMBI
                    $t_output_array['status'] = 'combi_not_exists';
                    $t_output_array['message'] = $this->coo_lang_manager->get_text('COMBI_NOT_EXIST');
                }
            }
        } else {
            // NO COMBI SELECTED
            $t_output_array['status'] = 'no_combi_selected';
            $t_output_array['message'] = '';
        }

        return $t_output_array;
    }

    public function quantity_check($p_products_object, $p_selected_combi, $p_quantity) {
        $coo_products_object = $p_products_object;
        if (is_object($p_products_object) == false) {
            trigger_error('$p_products_object is not an object: PropertiesControl->quantity_check()');
        }

        $c_selected_combi = $p_selected_combi;
        if (is_array($p_selected_combi) == false || isset($p_selected_combi['combi_quantity']) == false) {
            trigger_error('$p_selected_combi is not an array: PropertiesControl->quantity_check()');
        }

        $c_user_quantity = $p_quantity;
        if (is_numeric($p_quantity) == false) {
            trigger_error('$p_quantity is not a number: PropertiesControl->check_combis_quantity()');
        }
        if ($p_quantity < $coo_products_object->get_data_value('gm_min_order')) {
            $c_user_quantity = $coo_products_object->get_data_value('gm_min_order');
        }

        $t_use_properties_combis_quantity = $coo_products_object->get_data_value('use_properties_combis_quantity');
        $t_products_quantity = 0;
        if (($t_use_properties_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'false') || $t_use_properties_combis_quantity == 1) {
            // CHECK PRODUCTS QUANTITY
            $t_products_quantity = $coo_products_object->get_data_value('products_quantity');
        } else if (($t_use_properties_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_properties_combis_quantity == 2) {
            // CHECK COMBI QUANTITY
            $t_products_quantity = $c_selected_combi['combi_quantity'];
        } else {
            // NO QUANTITY CHECK
            $t_products_quantity = $c_user_quantity = 0;
        }

        if ($t_products_quantity >= $c_user_quantity) {
            // VALID QUANTITY
            $t_output_array['status'] = 'valid_quantity';
            $t_output_array['message'] = '';
        } else if ($t_products_quantity < $c_user_quantity && STOCK_ALLOW_CHECKOUT == 'true') {
            // INVALID QUANTITY BUT STOCK ALLOWED
            $t_output_array['status'] = 'stock_allowed';
            $t_output_array['message'] = $this->coo_lang_manager->get_text('COMBI_NOT_AVAILABLE_BUT_ALLOWED');
        } else {
            // INVALID QUANTITY
            $t_output_array['status'] = 'invalid_quantity';
            $t_output_array['message'] = $this->coo_lang_manager->get_text('COMBI_NOT_AVAILABLE');
        }
        return $t_output_array;
    }

    public function available_combi_exists($p_products_id, $p_combi_id = 0) {
        $t_available_combi_exists = false;
        $c_products_id = (int) $p_products_id;
        $c_combi_id = (int) $p_combi_id;

        $t_query = 'SELECT
						COUNT(*) as count
					FROM
						products_properties_combis
					WHERE
						products_id = "' . $c_products_id . '" AND
						products_properties_combis_id != "' . $c_combi_id . '" AND
						combi_quantity > 0';

        $t_result = xtc_db_query($t_query);
        if (xtc_db_num_rows($t_result) == 1) {
            $t_row = xtc_db_fetch_array($t_result);
            if ($t_row['count'] > 0) {
                $t_available_combi_exists = true;
            }
        }

        return $t_available_combi_exists;
    }

    function get_selection_form($p_products_id, $p_language_id, $p_selected_ids = false, $p_selected_combi = false, $p_quantity = false, $t_properties_dropdown_mode, $t_properties_price_show) {
        $c_products_id = (int) $p_products_id;
        $c_language_id = (int) $p_language_id;
        $c_quantity = (int) $p_quantity;
        if ($c_quantity == 0) {
            $c_quantity = 1;
        }

        $t_selection_form_type = 'dropdowns';

        switch ($t_selection_form_type) {
            case 'dropdowns':
                // GET ALL PROPERTIES DATA
                $t_properties_array = $this->get_products_properties_data($c_products_id, $c_language_id);

                if (is_string($p_selected_ids) && trim($p_selected_ids) != '') {
                    $p_selected_ids = $this->split_properties_values_string($p_selected_ids);
                }
                $t_error = '';
                $t_image = '';
                $t_selected_combi = false;
                $t_selected_values = array();
                if ($p_selected_combi != false) {
                    // GET SELECTED COMBI
                    $t_selected_combi = $p_selected_combi;
                    if (trim($t_selected_combi['combi_image']) != '') {
                        $t_image = '<img src="images/product_images/properties_combis_images/' . $t_selected_combi['combi_image'] . '" alt="" />';
                    }
                }

                if ($t_selected_combi != false) {
                    $t_valid_quantity = $this->quantity_check($coo_product_object, $t_selected_combi, $c_quantity);
                    $t_error = $t_valid_quantity['message'];

                    // GET VALUES FROM SELECTED COMBI
                    foreach ($t_selected_combi['COMBIS_VALUES'] as $t_value) {
                        $t_selected_values[$t_value['properties_id']] = $t_value['properties_values_id'];
                    }
                } else if (is_array($p_selected_ids) && count($p_selected_ids) > 0) {
                    $t_selected_combi = $this->get_selected_combi($c_products_id, $c_language_id, $p_selected_ids);
                    $t_selected_values = $p_selected_ids;
                }

                if ($t_properties_dropdown_mode == '' && $t_selected_combi == false && is_array($p_selected_ids) && count($p_selected_ids) == count($t_properties_array)) {
                    $t_error = $this->coo_lang_manager->get_text('COMBI_NOT_EXIST');
                }

                $t_single_propertie = 0;
                if (count($t_selected_values) == 1) {
                    $t_single_propertie = key($t_selected_values);
                }

                $t_available_properties_values = $this->get_available_properties_values_by_values($c_products_id, $t_selected_values);

                $t_visible_properties = array();
                if ($t_selected_combi != false || $t_properties_dropdown_mode != 'dropdown_mode_2') {
                    foreach ($t_properties_array as $t_propertie) {
                        $t_visible_properties[] = $t_propertie['properties_id'];
                    }
                } else {
                    if (count($t_selected_values) > 0) {
                        $t_append_next = false;
                        foreach ($t_properties_array as $t_propertie) {
                            if (count(array_intersect($t_selected_values, array_keys($t_propertie['values_array']))) > 0) {
                                $t_visible_properties[] = $t_propertie['properties_id'];
                            } else {
                                $t_append_next = true;
                            }
                            if ($t_append_next == true) {
                                $t_visible_properties[] = $t_propertie['properties_id'];
                                break;
                            }
                        }
                    }
                }

                $t_index = 0;
                foreach ($t_properties_array as $t_propertie) {
                    $t_properties_id = $t_propertie['properties_id'];
                    $t_visible = false;
                    if (in_array($t_properties_id, $t_visible_properties) == true || $t_index == 0) {
                        $t_visible = true;
                    }
                    foreach ($t_properties_array[$t_properties_id]['values_array'] as $t_value) {
                        $t_value_id = $t_value['properties_values_id'];
                        $t_disabled = true;
                        $t_selected = false;
                        if (in_array($t_value_id, $t_available_properties_values) == true || $t_single_propertie == $t_properties_id || count($t_selected_values) == 0) {
                            $t_disabled = false;
                        }
                        if (in_array($t_value_id, $t_selected_values) == true && $t_visible == true) {
                            $t_selected = true;
                        }
                        $t_properties_array[$t_properties_id]['values_array'][$t_value_id]['selected'] = $t_selected;
                        $t_properties_array[$t_properties_id]['values_array'][$t_value_id]['disabled'] = $t_disabled;
                    }
                    $t_properties_array[$t_properties_id]['visible'] = $t_visible;
                    $t_index++;
                }

                $t_content_data_array = array(
                    'products_id' => $c_products_id,
                    'properties_dropdown_mode' => $t_properties_dropdown_mode,
                    'properties_price_show' => $t_properties_price_show,
                    'properties_currency' => $_SESSION['currency'],
                    'PROPERTIES_DATA' => $t_properties_array,
                    'PROPERTIES_ERROR' => $t_error,
                    'PROPERTIES_IMAGE' => $t_image
                );
                $t_content_template = 'selection_forms/dropdowns.html';
                break;

            default:
                break;
        }
        // echo '<pre>';
        // print_r($t_content_data_array);
        // echo '</pre>';
        // $t_html_output = $this->build_html($t_content_data_array, $t_content_template);
        // return $t_html_output;
        return $t_content_data_array;
    }

}
