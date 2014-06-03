<?php

/* -----------------------------------------------------------------
 * 	$Id: product_parameters.php 522 2013-07-24 11:44:51Z akausch $
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
$module_content_display = false;
if ($product->data['products_id']) {

    $get_parameter_groups = xtc_db_query("SELECT DISTINCT g.group_id, g.sort_order, gd.group_name
                                        FROM " . TABLE_PRODUCTS_PARAMETERS_GROUPS . " g,
                                        " . TABLE_PRODUCTS_PARAMETERS_GROUPS_DESCRIPTION . " gd,
                                        " . TABLE_PRODUCTS_PARAMETERS . " p
                                        WHERE g.group_id=gd.group_id AND p.group_id=g.group_id
                                        AND p.products_id=" . $product->data['products_id'] . "
                                        AND gd.language_id=" . $_SESSION['languages_id'] . "
                                        ORDER BY g.sort_order");

    if (xtc_db_num_rows($get_parameter_groups)) {
        $module_content_display = true;
        $module_content_group = array();
        $module_content_value = array();

        while ($groups = xtc_db_fetch_array($get_parameter_groups)) {
            $get_parameters = xtc_db_query("SELECT pd.parameters_name, pd.parameters_value
                                      FROM " . TABLE_PRODUCTS_PARAMETERS . " p, " . TABLE_PRODUCTS_PARAMETERS_DESCRIPTION . " pd
                                      WHERE p.parameters_id=pd.parameters_id
                                      AND p.group_id=" . $groups['group_id'] . "
                                      AND p.products_id=" . $product->data['products_id'] . "
                                      AND pd.language_id=" . $_SESSION['languages_id'] . "
                                      ORDER BY p.sort_order");

            $module_content_group[] = array('GROUPS_NAME' => $groups['group_name']);

            $ii = 0;
            while ($parameters = xtc_db_fetch_array($get_parameters)) {
                $module_content_value[$groups['group_name']][$ii++][$parameters['parameters_name']] = $parameters['parameters_value'];
            }

            $module_smarty->assign('language', $_SESSION['language']);
            $module_smarty->assign('module_content_display', $module_content_display);
            $module_smarty->assign('module_content_group', $module_content_group);
            $module_smarty->assign('module_content_value', $module_content_value);
            $module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
            $module_smarty->caching = false;
            $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/product_parameters.html', USE_TEMPLATE_DEVMODE));
            $info_smarty->assign('MODULE_products_parameters', $module);
        }
        unset($ii);
    }
}
