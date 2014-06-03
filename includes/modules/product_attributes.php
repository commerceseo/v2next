<?php

/* -----------------------------------------------------------------
 * 	$Id: product_attributes.php 871 2014-03-20 09:13:14Z akausch $
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

$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');

if ($product->getAttributesCount() > 0) {
    $products_options_name_query = xtDBquery("SELECT DISTINCT 
												popt.products_options_id, 
												popt.products_options_name 
											FROM 
												" . TABLE_PRODUCTS_OPTIONS . " popt 
											INNER JOIN
												" . TABLE_PRODUCTS_ATTRIBUTES . " patrib ON(patrib.options_id = popt.products_options_id)
											WHERE 
												patrib.products_id = '" . $product->data['products_id'] . "' 
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
            'DATA' => ''
        );
		if (ATTRIBUTE_STOCK_CHECK_DISPLAY == 'true') {
			$pastockcheck = ' AND pa.attributes_stock > 0';
		}
        $products_options_query = xtDBquery("SELECT 
												pov.*,
												pa.*
											FROM 
												" . TABLE_PRODUCTS_ATTRIBUTES . " pa
											INNER JOIN
												" . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON(pa.options_values_id = pov.products_options_values_id)
											WHERE 
												pa.products_id = '" . $product->data['products_id'] . "'
											$pastockcheck
											AND 
												pa.options_id = '" . $products_options_name['products_options_id'] . "'
											AND 
												pov.language_id = '" . (int) $_SESSION['languages_id'] . "'
											ORDER BY pa.sortorder, pa.options_values_price, pa.options_values_id;");
		if (ATTRIBUTE_REQUIRED == 'true') {
			$products_options_data[$row]['DATA'][0] = array ('ID' => 0, 'TEXT' => PULL_DOWN_DEFAULT, 'STOCK' => 0, 'STOCKQTY' => 0);
			$col = 1;
		} else {
			$col = 0;
		}
        while ($products_options = xtc_db_fetch_array($products_options_query, true)) {
            $price = '';

            if ($products_options['products_options_values_image'] != '') {
                $attrubut_image = DIR_WS_IMAGES . 'product_options/' . $products_options['products_options_values_image'];
            } else {
                $attrubut_image = '';
            }
            require_once(DIR_FS_INC . 'cseo_get_stock_img.inc.php');
            // Kunde darf kein Preis sehen
            if ($_SESSION['customers_status']['customers_status_show_price'] == '0') {
                $products_options_data[$row]['DATA'][$col] = array('ID' => $products_options['products_options_values_id'],
                    'TEXT' => $products_options['products_options_values_name'],
                    'STOCK' => cseo_get_stock_img($products_options['attributes_stock']),
                    'STOCKQTY' => $products_options['attributes_stock'],
                    'IMAGE' => $attrubut_image,
                    'DESC' => $products_options['products_options_values_desc'],
                    'MODEL' => $products_options['attributes_model'],
                    'PRICE' => '',
                    'FULL_PRICE' => '',
                    'SORTORDER' => $products_options['sortorder'],
                    'PREFIX' => $products_options['price_prefix']);
            } else {
                //Jetzt schon
                if ($products_options['options_values_price'] != '0.00') {
                    $price = $xtPrice->xtcFormat($products_options['options_values_price'], false, $product->data['products_tax_class_id']);
                }

                $products_price = $xtPrice->xtcGetPrice($product->data['products_id'], $format = false, 1, $product->data['products_tax_class_id'], $product->data['products_price']);
                //VPE
                if ($products_options['attributes_vpe_status'] == '1' && $products_options['attributes_vpe_value'] != 0.00) {
                    $vpe_price = $xtPrice->xtcAddTax($products_options['options_values_price'] * (1 / $products_options['attributes_vpe_value']), $xtPrice->TAX[$product->data['products_tax_class_id']]);
                    $vpe_price = $xtPrice->xtcFormat($vpe_price, true) . TXT_PER . xtc_get_vpe_name($products_options['attributes_vpe']);
                }
                if ($_SESSION['customers_status']['customers_status_discount_attributes'] == 1 && $products_options['price_prefix'] == '+') {
                    $price -= $price / 100 * $discount;
                }
                if ($_SESSION['customers_status']['customers_status_discount_attributes'] == 1 && $products_options['price_prefix'] == '=') {
                    $price -= $price / 100 * $discount;
                }

                $attr_price = $price;
                // echo $attr_price;
                if ($products_options['price_prefix'] == "-") {
                    $attr_price = $price * (-1);
                }
				if ($products_options['price_prefix'] == "=") {
					$full = $attr_price;
				} elseif ($products_options['price_prefix'] == "-") {
					$full = $products_price - $attr_price;
				} elseif ($products_options['price_prefix'] == "+") {
					$full = $products_price + $attr_price;
				}
                if ($products_options['products_options_values_image'] != '') {
                    $attrubut_image = DIR_WS_IMAGES . 'product_options/' . $products_options['products_options_values_image'];
                } else {
                    $attrubut_image = '';
                }
                require_once(DIR_FS_INC . 'cseo_get_stock_img.inc.php');
                $products_options_data[$row]['DATA'][$col] = array('ID' => $products_options['products_options_values_id'],
                    'TEXT' => $products_options['products_options_values_name'],
                    'STOCK' => cseo_get_stock_img($products_options['attributes_stock']),
                    'STOCKQTY' => $products_options['attributes_stock'],
                    'IMAGE' => $attrubut_image,
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
            $col++;
        }
        $row++;
    }
}

if ($product->data['options_template'] == '' or $product->data['options_template'] == 'default') {
    $files = array();
    if ($dir = opendir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_options/')) {
        while (($file = readdir($dir)) !== false) {
            if (is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_options/' . $file) and ($file != "index.html") and (substr($file, 0, 1) != ".")) {
                $files[] = array('id' => $file, 'text' => $file);
            }
        }
        closedir($dir);
        asort($files);
        reset($files);
    }
    $product->data['options_template'] = $files[0]['id'];
}
$module_smarty->assign('PRODUCTS_ID', $product->data['products_id']);
$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('options', $products_options_data);
$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);

$module_smarty->caching = false;

$module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_options/' . $product->data['options_template'], USE_TEMPLATE_DEVMODE));

$info_smarty->assign('MODULE_product_options', $module);
