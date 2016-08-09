<?php

/* --------------------------------------------------------------
  PropertiesDataAgent.inc.php 2014-01-25 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class PropertiesDataAgent {

    function PropertiesDataAgent() {
        
    }

    function rebuild_properties_index($p_products_id) {
        $c_products_id = (int) $p_products_id;
        if (empty($c_products_id))
            trigger_error('rebuild_properties_index: typeof($p_products_id) != integer', E_USER_ERROR);

        # remove old index content
        xtc_db_query('DELETE FROM products_properties_index WHERE products_id = "' . $c_products_id . '";');

        # get shop languages
        $t_result = xtc_db_query('SELECT languages_id FROM languages;');

        # build index content
        while (($t_row = xtc_db_fetch_array($t_result))) {
            $c_language_id = (int) $t_row['languages_id'];
            xtc_db_query('
                INSERT INTO products_properties_index
                SELECT
                    ppc.products_id AS products_id,
                    pvd.language_id AS language_id,
                    pd.properties_id AS properties_id,
                    ppc.products_properties_combis_id AS products_properties_combis_id,
                    pvd.properties_values_id AS properties_values_id,
                    pd.properties_name AS properties_name,
                    pd.properties_admin_name AS properties_admin_name,
                    p.sort_order AS properties_sort_order,
                    pvd.values_name AS values_name,
                    pv.value_price AS values_price,
                    pv.sort_order AS value_sort_order
                FROM
                    properties AS p
                        LEFT JOIN properties_values AS pv USING (properties_id)
                        LEFT JOIN products_properties_combis_values AS ppcv USING (properties_values_id)
                        LEFT JOIN products_properties_combis AS ppc USING (products_properties_combis_id)
                        LEFT JOIN properties_description AS pd ON (pv.properties_id = pd.properties_id)
                        LEFT JOIN properties_values_description AS pvd ON (pv.properties_values_id = pvd.properties_values_id)
                WHERE
                    ppc.products_id = "' . $c_products_id . '" AND
                    pd.language_id 	= "' . $c_language_id . '" AND
                    pvd.language_id = "' . $c_language_id . '";');
        }
        return true;
    }

    function get_shop_languages_data() {
        $languages_query = xtc_db_query("SELECT languages_id, name, code, image, directory FROM " . TABLE_LANGUAGES . " ORDER BY sort_order;");
        while ($languages = xtc_db_fetch_array($languages_query)) {
            $languages_array[] = array(
                'id' => $languages['languages_id'],
                'name' => $languages['name'],
                'code' => $languages['code'],
                'image' => $languages['image'],
                'directory' => $languages['directory']
            );
        }
        return $languages_array;
    }

    /* ########## properties ########## */

    function get_properties_name($p_properties_id, $p_language_id) {
        $c_properties_id = (int) $p_properties_id;
        $c_language_id = (int) $p_language_id;

        $coo_data = new GMDataObject(
                'properties_description', array(
            'properties_id' => $c_properties_id,
            'language_id' => $c_language_id
                )
        );

        $t_name = $coo_data->get_data_value('properties_name');
        return $t_name;
    }

    function get_properties_admin_name($p_properties_id, $p_language_id) {
        $c_properties_id = (int) $p_properties_id;
        $c_language_id = (int) $p_language_id;

        $coo_data = new GMDataObject(
                'properties_description', array(
            'properties_id' => $c_properties_id,
            'language_id' => $c_language_id
                )
        );

        $t_name = $coo_data->get_data_value('properties_admin_name');
        return $t_name;
    }

    /* ########## products_properties_combis ########## */

    function get_properties_combis_details($p_combis_id, $p_language_id) {
        $t_details_array = array();

        $c_combis_id = (int) $p_combis_id;
        $c_language_id = (int) $p_language_id;

        $t_result = xtc_db_query('
			SELECT *
			FROM products_properties_combis_values AS cv
				LEFT JOIN properties_values AS pv USING (properties_values_id)
				LEFT JOIN properties_description AS pd USING (properties_id)
				LEFT JOIN properties_values_description AS pvd ON (pv.properties_values_id = pvd.properties_values_id)
			WHERE
				cv.products_properties_combis_id 	= "' . $c_combis_id . '" AND
				pd.language_id 						= "' . $c_language_id . '" AND
				pvd.language_id 					= "' . $c_language_id . '";');

        while (($t_row = xtc_db_fetch_array($t_result))) {
            $t_details_array[] = $t_row;
        }
        return $t_details_array;
    }

    function get_properties_combis_vpe_details($p_combis_id, $p_language_id) {
        $t_details_array = array();

        $c_combis_id = (int) $p_combis_id;
        $c_language_id = (int) $p_language_id;

        # get combis details
        $t_result = xtc_db_query('
			SELECT
				ppc.combi_price_type AS combi_price_type,
				ppc.combi_price AS combi_price,
				ppc.vpe_value AS vpe_value,
				ppc.products_vpe_id AS products_vpe_id,
				ppc.combi_weight AS combi_weight
			FROM
				products_properties_combis AS ppc
			WHERE
				ppc.products_properties_combis_id	= "' . $c_combis_id . '";');

        if (xtc_db_num_rows($t_result) == 0) {
            if (is_object($GLOBALS['coo_debugger']))
                $GLOBALS['coo_debugger']->log('get_properties_combis_vpe_details() given combis_id not found: ' . $c_combis_id, 'warning');
            return false;
        }
        $t_details_array = xtc_db_fetch_array($t_result);
        $t_details_array['products_vpe_name'] = '';

        # get vpe details
        if ($t_details_array['products_vpe_id'] > 0) {
            $t_result = xtc_db_query('
				SELECT
					pvpe.products_vpe_name AS products_vpe_name
				FROM
					products_vpe AS pvpe
				WHERE
					pvpe.products_vpe_id	= "' . $t_details_array['products_vpe_id'] . '" AND
					pvpe.language_id		= "' . $c_language_id . '";');

            if (xtc_db_num_rows($t_result) == 0) {
                if (is_object($GLOBALS['coo_debugger']))
                    $GLOBALS['coo_debugger']->log('get_properties_combis_vpe_details() needed products_vpe_id/language_id not found: ' . $t_details_array['products_vpe_id'] . '/' . $c_language_id, 'warning');
            } else {
                # attach products_vpe_name
                $t_details_array['products_vpe_name'] = mysql_result($t_result, 0, 'products_vpe_name');
            }
        }
        return $t_details_array;
    }

    function get_combis_full_struct($p_properties_combis_id, $p_language_id) {
        $t_combis_struct = array();

        $c_properties_combis_id = (int) $p_properties_combis_id;
        $c_language_id = (int) $p_language_id;

        # main data, properties_combis
        $coo_data = new GMDataObject('products_properties_combis', array('products_properties_combis_id' => $c_properties_combis_id));

        if ((int) $coo_data->get_data_value('products_id') == 0) {
            return false;
        }

        $t_combis_struct['products_properties_combis_id'] = $coo_data->get_data_value('products_properties_combis_id');
        $t_combis_struct['products_id'] = $coo_data->get_data_value('products_id');
        $t_combis_struct['sort_order'] = $coo_data->get_data_value('sort_order');
        $t_combis_struct['combi_model'] = $coo_data->get_data_value('combi_model');
        $t_combis_struct['combi_ean'] = $coo_data->get_data_value('combi_ean');
        $t_combis_struct['combi_quantity'] = (float) $coo_data->get_data_value('combi_quantity');
        $t_combis_struct['combi_shipping_status_id'] = (int) $coo_data->get_data_value('combi_shipping_status_id');
        $t_combis_struct['combi_shipping_status_name'] = '';
        $t_combis_struct['combi_shipping_status_image'] = '';
        $t_combis_struct['combi_weight'] = $coo_data->get_data_value('combi_weight');
        $t_combis_struct['combi_price_type'] = $coo_data->get_data_value('combi_price_type');
        $t_combis_struct['combi_price'] = $coo_data->get_data_value('combi_price');
        $t_combis_struct['combi_image'] = $coo_data->get_data_value('combi_image');
        $t_combis_struct['vpe_value'] = (float) $coo_data->get_data_value('vpe_value');
        $t_combis_struct['products_vpe_id'] = $coo_data->get_data_value('products_vpe_id');
        $t_combis_struct['products_vpe_name'] = '';

        if ($coo_data->get_data_value('combi_shipping_status_id') > 0) {
            $t_parameters_array = array(
                'shipping_status_id' => $coo_data->get_data_value('combi_shipping_status_id'),
                'language_id' => $_SESSION['languages_id']
            );
            $coo_shipping_status_do = new GMDataObject('shipping_status', $t_parameters_array);
            $t_combis_struct['combi_shipping_status_name'] = $coo_shipping_status_do->get_data_value('shipping_status_name');
            $t_combis_struct['combi_shipping_status_image'] = $coo_shipping_status_do->get_data_value('shipping_status_image');
        }

        if ($coo_data->get_data_value('products_vpe_id') > 0) {
            $t_parameters_array = array(
                'products_vpe_id' => $coo_data->get_data_value('products_vpe_id'),
                'language_id' => $_SESSION['languages_id']
            );
            $coo_vpe_do = new GMDataObject('products_vpe', $t_parameters_array);
            $t_combis_struct['products_vpe_name'] = $coo_vpe_do->get_data_value('products_vpe_name');
        }


        $coo_combis_struct_supplier = cseohookfactory::create_object('PropertiesCombisStructSupplier');
        $t_combis_values_struct = $coo_combis_struct_supplier->get_properties_combis_struct($c_properties_combis_id, $c_language_id);

        for ($i = 0; $i < sizeof($t_combis_values_struct); $i++) {
            $t_properties_name = $this->get_properties_name(
                    $t_combis_values_struct[$i]['properties_id'], $_SESSION['languages_id']
            );

            $t_properties_admin_name = $this->get_properties_admin_name(
                    $t_combis_values_struct[$i]['properties_id'], $_SESSION['languages_id']
            );
            $t_combis_values_struct[$i] = array_merge(
                    $t_combis_values_struct[$i], array('properties_name' => $t_properties_name, 'properties_admin_name' => $t_properties_admin_name)
            );
        }
        $t_combis_struct['COMBIS_VALUES'] = $t_combis_values_struct;

        return $t_combis_struct;
    }

    public function get_cheapest_combi($p_products_id, $p_language_id) {
        $t_result = xtc_db_query('SELECT 
						products_properties_combis_id
					FROM 
						products_properties_combis
					WHERE
						products_id = "' . $p_products_id . '"
					ORDER BY
						combi_price, products_properties_combis_id
					LIMIT 1;');

        if (xtc_db_num_rows($t_result) != 1) {
            return false;
        }
        $t_row = xtc_db_fetch_array($t_result);
        $t_cheapest_combi = $this->get_combis_full_struct($t_row['products_properties_combis_id'], $p_language_id);

        return $t_cheapest_combi;
    }

    public function get_available_combis_ids_by_values($p_products_id, $p_selected_values, $p_check_quantity = true) {

        $c_products_id = (int) $p_products_id;
        if ($c_products_id == 0) {
            xtc_db_close();
            trigger_error('$p_products_id is null: PropertiesDataAgent->get_available_combis_ids_by_values()');
        }
        $c_check_quantity = true;
        if ($p_check_quantity == false) {
            $c_check_quantity = false;
        }
        // GET PRODUCTS DATA
        $coo_products = cseohookfactory::create_object('GMDataObject', array('products', array('products_id' => $p_products_id)));
        $t_products_min_quantity = gm_convert_qty($coo_products->get_data_value('products_minorder'));
        $t_use_properties_combis_quantity = $coo_products->get_data_value('use_properties_combis_quantity');

        $t_min_quantity_string = '';
        if (($t_use_properties_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_properties_combis_quantity == 2) {
            $t_min_quantity_string = 'AND ppc.combi_quantity >= ' . $t_products_min_quantity;
        }
        if (STOCK_ALLOW_CHECKOUT == 'true' || $c_check_quantity == false) {
            $t_min_quantity_string = '';
        }

        $t_available_combis_ids = array();
        $t_result = xtc_db_query('SELECT 
						ppc.products_properties_combis_id
					FROM 
						products_properties_combis_values AS ppcv
					LEFT JOIN 
						products_properties_combis AS ppc ON ppc.products_properties_combis_id = ppcv.products_properties_combis_id
					WHERE 
						ppcv.properties_values_id IN (' . implode(',', $p_selected_values) . ')
						AND ppc.products_id = "' . $c_products_id . '"
						' . $t_min_quantity_string . '
					GROUP BY
						ppc.products_properties_combis_id
					HAVING
						COUNT(ppcv.products_properties_combis_values_id) = ' . count($p_selected_values) . ';');

        while ($t_row = xtc_db_fetch_array($t_result)) {
            $t_available_combis_ids[] = $t_row['products_properties_combis_id'];
        }
        return $t_available_combis_ids;
    }

    public function get_values_by_combis_ids($p_combis_is_array, $p_select_properties = false) {
        $t_values_ids_array = array();
        if (is_array($p_combis_is_array) == false || count($p_combis_is_array) == 0) {
            return $t_values_ids_array;
        }

        if (is_array($p_select_properties) && count($p_select_properties) > 0) {
            $t_query = 'SELECT
							ppcv.properties_values_id
						FROM
							products_properties_combis_values AS ppcv
						LEFT JOIN
							properties_values AS pv USING (properties_values_id)
						WHERE
							pv.properties_id IN (' . implode(',', array_keys($p_select_properties)) . ')
							AND products_properties_combis_id IN (' . implode(',', $p_combis_is_array) . ')';
        } else {
            $t_query = 'SELECT
							properties_values_id
						FROM
							products_properties_combis_values
						WHERE
							products_properties_combis_id IN (' . implode(',', $p_combis_is_array) . ')';
        }
        $t_result = xtc_db_query($t_query);

        while ($t_row = xtc_db_fetch_array($t_result)) {
            $t_values_ids_array[] = $t_row['properties_values_id'];
        }
        $t_values_ids_array = array_unique($t_values_ids_array);
        return $t_values_ids_array;
    }

    public function split_properties_values_string($p_properties_values_string) {
        $t_properties_values_array = array();

        $t_properties = explode('&', $p_properties_values_string);
        foreach ($t_properties as $t_propertie) {
            $t_split_array = explode(':', $t_propertie);
            if ($t_split_array[1] != 0) {
                $t_properties_values_array[$t_split_array[0]] = $t_split_array[1];
            }
        }

        return $t_properties_values_array;
    }

    public function count_properties_to_product($p_products_id) {
        $c_products_id = (int) $p_products_id;
        if ($c_products_id == 0) {
            trigger_error('$p_products_id is null: PropertiesDataAgent->count_properties_to_product()');
        }

        $t_properties_count = 0;
        $t_result = xtc_db_query('SELECT
						count(*) AS total
					FROM
						products_properties_combis AS ppc
					LEFT JOIN
						products_properties_combis_values AS ppcv USING ( products_properties_combis_id )
					WHERE
						ppc.products_id = "' . $c_products_id . '"
					GROUP BY
						products_properties_combis_id
					LIMIT 1;');

        if (xtc_db_num_rows($t_result) == 1) {
            $t_row = xtc_db_fetch_array($t_result);
            $t_properties_count = $t_row['total'];
        }

        return $t_properties_count;
    }

}
