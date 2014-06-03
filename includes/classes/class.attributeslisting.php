<?php

class GetAttributesListing_ORIGINAL {

    /**
      Attribute Listing
     * */
    function GetAttributesListing($pid, $tax, $pprice) {
        global $xtPrice;
        $products_options_name_query = xtDBquery("SELECT DISTINCT 
													popt.products_options_id, 
													popt.products_options_name 
												FROM 
													" . TABLE_PRODUCTS_OPTIONS . " popt 
												LEFT JOIN
													" . TABLE_PRODUCTS_ATTRIBUTES . " patrib ON(patrib.options_id = popt.products_options_id)
												WHERE 
													patrib.products_id='" . $pid . "' 
												AND 
													popt.language_id = '" . (int) $_SESSION['languages_id'] . "' 
												ORDER BY 
													popt.products_options_sortorder, popt.products_options_id");

        $row = 0;
        $col = 0;
        $products_options_data = array();
        while ($products_options_name = xtc_db_fetch_array($products_options_name_query, true)) {
            $selected = 0;
            $products_options_array = array();

            $products_options_data[$row] = array('NAME' => $products_options_name['products_options_name'],
                'ID' => $products_options_name['products_options_id'],
                'SORTORDER' => $products_options_name['products_options_sortorder'],
                'DATA' => '');
            if (ATTRIBUTE_STOCK_CHECK_DISPLAY == 'true') {
                $pastockcheck = ' AND pa.attributes_stock > 0';
            }
            $products_options_query = xtDBquery("SELECT 
													pov.*,
													pa.*
												FROM 
													" . TABLE_PRODUCTS_ATTRIBUTES . " pa
												LEFT JOIN
													" . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON(pa.options_values_id = pov.products_options_values_id)
												WHERE 
													pa.products_id = '" . $pid . "'
												$pastockcheck
												AND 
													pa.options_id = '" . $products_options_name['products_options_id'] . "'
												AND 
													pov.language_id = '" . (int) $_SESSION['languages_id'] . "'
												ORDER BY pa.sortorder, pa.options_values_price, pa.options_values_id;");
            $col = 0;
            while ($products_options = xtc_db_fetch_array($products_options_query, true)) {
                $price = '';

                require_once(DIR_FS_INC . 'cseo_get_stock_img.inc.php');
                // Kunde darf kein Preis sehen
                if ($_SESSION['customers_status']['customers_status_show_price'] == '0') {
                    $products_options_data[$row]['DATA'][$col] = array('ID' => $products_options['products_options_values_id'],
                        'TEXT' => $products_options['products_options_values_name'],
                        'STOCK' => cseo_get_stock_img($products_options['attributes_stock']),
                        'STOCKQTY' => $products_options['attributes_stock'],
                        'DESC' => $products_options['products_options_values_desc'],
                        'MODEL' => $products_options['attributes_model'],
                        'PRICE' => '',
                        'FULL_PRICE' => '',
                        'SORTORDER' => $products_options['sortorder'],
                        'PREFIX' => $products_options['price_prefix']);
                } else {
                    //Jetzt schon
                    if ($products_options['options_values_price'] != '0.00') {
                        $price = $xtPrice->xtcFormat($products_options['options_values_price'], false, $tax);
                    }
                    $products_price = $xtPrice->xtcGetPrice($pid, $format = false, 1, $tax, $pprice);
                    //VPE
                    if ($products_options['attributes_vpe_status'] == '1' && $products_options['attributes_vpe_value'] != 0.00) {
                        $vpe_price = $xtPrice->xtcAddTax($products_options['options_values_price'] * (1 / $products_options['attributes_vpe_value']), $xtPrice->TAX[$tax]);
                        $vpe_price = $xtPrice->xtcFormat($vpe_price, true) . TXT_PER . xtc_get_vpe_name($products_options['attributes_vpe']);
                    }
                    if ($_SESSION['customers_status']['customers_status_discount_attributes'] == 1 && $products_options['price_prefix'] == '+') {
                        $price -= $price / 100 * $discount;
                    }
                    if ($_SESSION['customers_status']['customers_status_discount_attributes'] == 1 && $products_options['price_prefix'] == '=') {
                        $price -= $price / 100 * $discount;
                    }

                    $attr_price = $price;

                    if ($products_options['price_prefix'] == "-") {
                        $attr_price = $price * (-1);
                    }
                    if ($products_options['price_prefix'] == "=") {
                        $full = $attr_price;
                    } elseif ($products_options['price_prefix'] == "+") {
                        $full = $products_price + $attr_price;
                    } elseif ($products_options['price_prefix'] == "-") {
                        $full = $products_price - $attr_price;
                    }

                    require_once(DIR_FS_INC . 'cseo_get_stock_img.inc.php');
                    $products_options_data[$row]['DATA'][$col] = array('ID' => $products_options['products_options_values_id'],
                        'TEXT' => $products_options['products_options_values_name'],
                        'STOCK' => cseo_get_stock_img($products_options['attributes_stock']),
                        'STOCKQTY' => $products_options['attributes_stock'],
                        'DESC' => $products_options['products_options_values_desc'],
                        'MODEL' => $products_options['attributes_model'],
                        'VPE' => $vpe_price,
                        'PRICE' => $xtPrice->xtcFormat($price, true),
                        'FULL_PRICE' => $xtPrice->xtcFormat($full, true),
                        'SORTORDER' => $products_options['sortorder'],
                        'PREFIX' => $products_options['price_prefix'],
                        'PRICE_PLAIN' => $price);
                    //if PRICE for option is 0 we don't need to display it
                    if ($price == 0) {
                        unset($products_options_data[$row]['DATA'][$col]['PRICE']);
                        unset($products_options_data[$row]['DATA'][$col]['PREFIX']);
                    }
                }
                $col ++;
            }
            $row ++;
        }
        return $products_options_data;
    }

}
