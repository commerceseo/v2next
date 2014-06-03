<?php

/* -----------------------------------------------------------------
 * 	$Id: new_attributes_change.php 420 2013-06-19 18:04:39Z akausch $
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
require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
require_once(DIR_FS_INC . 'xtc_get_tax_class_id.inc.php');
// download function start
$delete_sql = xtc_db_query("SELECT products_attributes_id FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id = '" . $_POST['current_product_id'] . "';");
while ($delete_res = xtc_db_fetch_array($delete_sql)) {
    $delete_download_sql = xtc_db_query("SELECT products_attributes_filename FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " WHERE products_attributes_id = '" . $delete_res['prducts_attributes_id'] . "'");
    $delete_download_file = xtc_db_fetch_array($delete_download_sql);
    xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " WHERE products_attributes_id = '" . $delete_res['products_attributes_id'] . "'");
}
// download function end
xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id = '" . $_POST['current_product_id'] . "'");

for ($i = 0; $i < sizeof($_POST['optionValues']); $i++) {
    $query = "SELECT * FROM " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_values_id = '" . $_POST['optionValues'][$i] . "'";
    $result = xtc_db_query($query);
    $matches = xtc_db_num_rows($result);
    while ($line = xtc_db_fetch_array($result)) {
        $optionsID = $line['products_options_id'];
    }

    $cv_id = $_POST['optionValues'][$i];
    $value_price = $_POST[$cv_id . '_price'];

    if (PRICE_IS_BRUTTO == 'true') {
        $value_price = ($value_price / ((xtc_get_tax_rate(xtc_get_tax_class_id($_POST['current_product_id']))) + 100) * 100);
    }

    $value_price = xtc_round($value_price, PRICE_PRECISION);
    $value_prefix = $_POST[$cv_id . '_prefix'];
    $value_sortorder = $_POST[$cv_id . '_sortorder'];
    $value_weight_prefix = $_POST[$cv_id . '_weight_prefix'];
    $value_model = $_POST[$cv_id . '_model'];
    $value_ean = $_POST[$cv_id . '_ean'];
    $value_stock = $_POST[$cv_id . '_stock'];
    $value_weight = $_POST[$cv_id . '_weight'];
    $value_vpe_status = xtc_db_prepare_input($_POST[$cv_id . '_vpe_status']);
    $value_vpe = $_POST[$cv_id . '_vpe'];
    $value_vpe_value = $_POST[$cv_id . '_vpe_value'];
    $value_scale_price = $_POST[$cv_id . '_scale_price'];

    xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES . " 
					(products_id, options_id, options_values_id, options_values_price, options_values_scale_price, price_prefix, attributes_model, attributes_stock, options_values_weight, weight_prefix, sortorder, attributes_ean, attributes_vpe_status, attributes_vpe, attributes_vpe_value) 
				VALUES 
					('" . $_POST['current_product_id'] . "', 
					'" . $optionsID . "', 
					'" . $_POST['optionValues'][$i] . "', 
					'" . $value_price . "', 
					'" . $value_scale_price . "', 
					'" . $value_prefix . "', 
					'" . $value_model . "', 
					'" . $value_stock . "', 
					'" . $value_weight . "', 
					'" . $value_weight_prefix . "', 
					'" . $value_sortorder . "', 
					'" . $value_ean . "', 
					'" . $value_vpe_status . "', 
					'" . $value_vpe . "', 
					'" . $value_vpe_value . "');");

    $products_attributes_id = xtc_db_insert_id();

    if ($_POST[$cv_id . '_download_file'] != '') {
        $value_download_file = $_POST[$cv_id . '_download_file'];
        $value_download_expire = $_POST[$cv_id . '_download_expire'];
        $value_download_count = $_POST[$cv_id . '_download_count'];
        xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " (products_attributes_id, products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount) VALUES ('" . $products_attributes_id . "', '" . $value_download_file . "', '" . $value_download_expire . "', '" . $value_download_count . "');");
    }
}
