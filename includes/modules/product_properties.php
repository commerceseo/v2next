<?php

/* -----------------------------------------------------------------
 * 	$Id: product_properties.php 1244 2014-10-21 14:10:31Z akausch $
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

if ($product->getPropertiesCount() > 0) {
    $c_products_id = (int) $product->data['products_id'];
    $c_languages_id = (int) $_SESSION['languages_id'];
    $t_properties_dropdown_mode = $product->data['properties_dropdown_mode'];
    $t_properties_price_show = $product->data['properties_show_price'];
    $t_properties_weight_show = $product->data['use_properties_combis_weight'];
    $t_properties_quantity_show = $product->data['use_properties_combis_quantity'];
    $t_properties_shipping_time_show = $product->data['use_properties_combis_shipping_time'];

    $coo_properties_data = cseohookfactory::create_object('ProductPropertiesData', array($c_products_id, $c_languages_id));
    $t_properties_array = $coo_properties_data->get_properties_struct();
    $v_coo_properties_control = cseohookfactory::create_object('PropertiesControl');
	
	$t_current_combi = false;
	// $coo_properties_view = cseohookfactory::create_object('PropertiesView', array($_GET, $_POST));
	$t_properties_selection_form = $v_coo_properties_control->get_selection_form($c_products_id, $c_languages_id, false, $t_current_combi, $t_properties_dropdown_mode, $t_properties_price_show);
    
	// echo '<pre>';
    // print_r($t_properties_selection_form);
    // echo '</pre>';

// if ($product->data['options_template'] == '' or $product->data['options_template'] == 'default') {
    // $files = array();
    // if ($dir = opendir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/properties/selection_forms/')) {
    // while (($file = readdir($dir)) !== false) {
    // if (is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/properties/selection_forms/' . $file) and ($file != "index.html") and (substr($file, 0, 1) != ".")) {
    // $files[] = array('id' => $file, 'text' => $file);
    // }
    // }
    // closedir($dir);
    // asort($files);
    // reset($files);
    // }
    // $product->data['options_template'] = $files[0]['id'];
// }


    // echo '<pre>';
    // print_r($t_properties_selection_form);
    // echo '</pre>';
    $module_smarty->assign('content_data', $t_properties_selection_form);
}


$module_smarty->assign('PRODUCTS_ID', $product->data['products_id']);
$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('options', $products_options_data);
$module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);

$module_smarty->caching = false;

// $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/properties/selection_forms/' . $product->data['options_template'], USE_TEMPLATE_DEVMODE));
$module = $module_smarty->fetch(cseo_get_usermod('base/module/properties/selection_forms/dropdowns.html', USE_TEMPLATE_DEVMODE));

$info_smarty->assign('MODULE_product_properties', $module);
