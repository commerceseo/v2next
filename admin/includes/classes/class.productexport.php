<?php

/* -----------------------------------------------------------------
 * 	$Id: class.productexport.php 420 2013-06-19 18:04:39Z akausch $
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

/*
 * some server configurations for better performance
 */
@ini_set('max_execution_time', '3600'); # 1h max runtine
@ini_set('memory_limit', '256M');       # 256 MB max ram
// defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

function no_html($p_text) {
    $t_text = strip_tags($p_text, '');
    $t_text = html_entity_decode($t_text);

    return($t_text);
}

class CSEOProductExport {

    var $coo_export = false;
    var $v_modules = array();
    var $v_module_name = '';
    var $v_module_logo = '';
    var $v_module_website = '';
    var $v_picker = array();
    var $v_selected_module = '';
    var $v_module_content = '';
    var $v_module_data_array = array();
    var $v_config_array = array();

    function CSEOProductExport() {
        $this->define_missing_path_names();
    }

    function set_missing_flags($p_module = '') {
        if (empty($p_module)) {
            return false;
        }
        $t_module_name = basename($p_module, ".php");
        $t_options_array = array('CRONJOB' => '0',
            'CHECKBOXES' => '',
            'STOCK' => '0'
        );
        foreach ($t_options_array as $t_option_key => $t_option_value) {
            $t_module_query = xtc_db_query("SELECT * FROM cseo_configuration WHERE cseo_key = 'CSEO_" . strtoupper($t_module_name) . "_" . strtoupper($t_option_key) . "'");
            $t_module_data_array = xtc_db_fetch_array($t_module_query);
            if (empty($t_module_data_array['cseo_key'])) {
                $t_group_id = $t_module_data_array['cseo_group_id'];
                $t_sort_order = $t_module_data_array['cseo_sort_order'];
                $t_cronjob_query = xtc_db_query("INSERT INTO cseo_configuration (cseo_key, cseo_value, cseo_group_id, cseo_sort_order) VALUES ('CSEO_" . strtoupper($t_module_name) . "_" . strtoupper($t_option_key) . "', '" . $t_option_value . "', '" . $t_group_id . "', '" . $t_sord_order . "')");
            }
        }
        return true;
    }

    function module_installed($p_module = '') {
        if (empty($p_module)) {
            return false;
        }
        $t_module_name = basename($p_module, ".php");
        $t_module_query = xtc_db_query("SELECT cseo_key, cseo_value FROM cseo_configuration WHERE cseo_key = 'CSEO_" . strtoupper($t_module_name) . "_FILE'");
        $t_module_data_array = xtc_db_fetch_array($t_module_query);
        if (!is_array($t_module_data_array) || empty($t_module_data_array))
            return false;
        return true;
    }

    function has_cronjob_flag($p_module = '') {
        if (empty($p_module)) {
            return false;
        }
        $t_module_name = basename($p_module, ".php");
        $t_module_query = xtc_db_query("SELECT cseo_key, cseo_value FROM cseo_configuration WHERE cseo_key = 'CSEO_" . strtoupper($t_module_name) . "_CRONJOB'");
        $t_module_data_array = xtc_db_fetch_array($t_module_query);
        return (bool) $t_module_data_array['cseo_value'];
    }

    function set_module($p_module = '') {
        if (empty($p_module)) {
            return false;
        }
        require(DIR_FS_CATALOG . 'admin/cseo_product_export/' . $p_module);
        $t_class_name = basename($p_module, ".php");
        $this->coo_export = new $t_class_name();
        $v_result = $this->set_configuration_array();
        return true;
    }

    function set_selected_module($p_module = '') {
        if (empty($p_module)) {
            return false;
        }
        if (!empty($p_module)) {
            $this->v_selected_module = $p_module;
        }
        return true;
    }

    function set_module_data($p_module = '') {
        if (empty($_POST['filename'])) {
            $t_module_name = basename($p_module, ".php");
            $t_module_query = xtc_db_query("SELECT cseo_key, cseo_value FROM cseo_configuration WHERE cseo_key LIKE 'CSEO_" . strtoupper($t_module_name) . "%'");
            $t_data_array = array();
            while ($t_module_data_array = xtc_db_fetch_array($t_module_query)) {
                if (!is_array($t_module_data_array) || empty($t_module_data_array))
                    return false;
                $t_key = $t_module_data_array['cseo_key'];
                $t_value = $t_module_data_array['cseo_value'];
                $t_key_new = strtolower(str_replace("CSEO_" . strtoupper($t_module_name) . "_", "", $t_key));
                $t_data_array[$t_key_new] = $t_value;
            }
            $this->v_module_data_array = array('filename' => $t_data_array['file'],
                'currency' => $t_data_array['currency'],
                'shipping_costs' => $t_data_array['shipping_costs'],
                'shipping_costs_free' => $t_data_array['shipping_costs_free'],
                'attributes' => $t_data_array['attributes'],
                'campaign' => $t_data_array['campaign'],
                'export' => 'no',
                'cronjob' => $t_data_array['cronjob'],
                'stock' => $t_data_array['stock'],
                'create_csv' => $p_module,
                'status' => $t_data_array['status'],
                'customers_groups' => $t_data_array['customers_groups'],
                'action' => 'save'
            );
        } else {
            if (defined('_VALID_XTC') == false)
                return false;
            $this->v_module_data_array = array('filename' => $_POST['filename'],
                'currency' => $_POST['currency'],
                'shipping_costs' => $_POST['shipping_costs'],
                'shipping_costs_free' => $_POST['shipping_costs_free'],
                'attributes' => $_POST['attributes'],
                'campaign' => $_POST['campaign'],
                'export' => $_POST['export'],
                'cronjob' => $_POST['cronjob'],
                'stock' => (double) $_POST['stock'],
                'create_csv' => $_POST['create_csv'],
                'action' => $_POST['action'],
                'status' => $_POST['status'],
                'customers_groups' => $_POST['customers_groups']
            );
        }
        return true;
    }

    function new_fputcsv($p_file, $p_csv_fields_array, $p_delimiter, $p_enclosure) {
// using the slower version because the fputcsv is creating an error if there
// is no enclosure transmitted.
        $t_schema = '';
        $t_element_count = 0;
        foreach ($p_csv_fields_array as $t_value) {
            if ($t_element_count == count($p_csv_fields_array) - 1) {
                $p_delimiter = '';
            }
            $t_schema .= $p_enclosure . $t_value . $p_enclosure . $p_delimiter;
            $t_element_count++;
        }

        fputs($p_file, $t_schema . "\n");
// all good
        return true;
    }

    function set_configuration_array() {
        $t_categorie_file_path = '';
        if ($this->coo_export->v_keyname == 'GOOGLE_SHOPPING') {
            $t_categorie_file_path = $this->coo_export->v_category_file_path;
        }
        $v_param_array = array('filename' => array('FILE', $this->coo_export->v_module_export_filename, '6', '1'),
            'status' => array('STATUS', '1', '6', '1'),
            'customers_groups' => array('CUSTOMERS_GROUP', '', '6', '1'),
            'currency' => array('CURRENCY', 'EUR', '6', '1'),
            'shipping_costs' => array('SHIPPING_COSTS', '', '6', '1'),
            'shipping_costs_free' => array('SHIPPING_COSTS_FREE', '', '6', '1'),
            'attributes' => array('ATTRIBUTES', '', '6', '1'),
            'campaign' => array('CAMPAIGN', '', '6', '1'),
            'cronjob' => array('CRONJOB', '0', '6', '1'),
            'stock' => array('STOCK', '0', '6', '1'),
            'checkboxes' => array('CHECKBOXES', '', '6', '1'),
            'categorie_file_path' => array('CATEGORY_FILE_PATH', $t_categorie_file_path, '6', '1'),
            'add_vpe_to_name' => array('ADD_VPE_TO_NAME', 'no', '6', '1')
        );
// set array
        return $this->v_config_array = $v_param_array;
// all good
        return true;
    }

    function save_configuration() {
        switch ($this->coo_export->v_keyname) {
            case 'IDEALO':
                $t_setup_array = array();
                foreach ($this->coo_export->v_params as $t_name) {
                    $t_value = (isset($_POST['products_shipping_costs_' . $t_name])) ? 1 : 0;
                    if (is_numeric($_POST['products_shipping_costs_' . $t_name])) {
                        $t_value = (float) $_POST['products_shipping_costs_' . $t_name];
                    }
                    $t_set = $t_name . ':' . $t_value;
                    $t_setup_array[] = $t_set;
                }
                $_POST['checkboxes'] = implode(";", $t_setup_array);
                break;
        }
// save setup
        foreach ($this->v_config_array as $v_post => $v_db_keys) {
            $t_value = '';
            if (!empty($_POST[$v_post]))
                $t_value = $_POST[$v_post];
            xtc_db_query("UPDATE cseo_configuration SET cseo_value = '" . xtc_db_input($t_value) . "' WHERE cseo_key = 'CSEO_" . xtc_db_input($this->coo_export->v_keyname) . "_" . $v_db_keys[0] . "'");
        }
// all good
        return true;
    }

    function module_remove() {
        xtc_db_query("DELETE FROM cseo_configuration WHERE cseo_key LIKE 'CSEO_" . xtc_db_input($this->coo_export->v_keyname) . "%'");
        xtc_redirect(xtc_href_link(FILENAME_CSEO_PRODUCT_EXPORT));
        break;
    }

    function module_install() {
        foreach ($this->v_config_array as $v_post => $v_db_keys) {
            xtc_db_query("INSERT INTO cseo_configuration (cseo_key, cseo_value,  cseo_group_id, cseo_sort_order)
VALUES ('CSEO_" . $this->coo_export->v_keyname . "_" . $v_db_keys[0] . "', '" . $v_db_keys[1] . "', '" . $v_db_keys[2] . "', '" . $v_db_keys[3] . "')");
        }
        $f_module = $_GET['module'];
        xtc_redirect(xtc_href_link(FILENAME_CSEO_PRODUCT_EXPORT, 'module=' . $f_module));
        break;
    }

    function get_modules() {
        $t_modules = array();
        $t_handle = opendir(DIR_FS_CATALOG . 'admin/cseo_product_export/');
        while ($t_module = readdir($t_handle)) {
            $t_info = pathinfo($t_module);
            if (strlen($t_module) > 4 && $t_info['extension'] == 'php') {
                $t_modules[] = $t_module;
            }
        }
        sort($t_modules);
        closedir($t_handle);
        $this->v_modules = $t_modules;
        return $this->v_modules;
    }

    function module_picker() {
        $this->v_picker = $this->v_modules;
        return true;
    }

    function display_options() {
// starting up if no module selected (show list)
        if ($_GET['module'] == null) {
            $t_modules = array();
            foreach ($this->v_modules as $t_module) {
                require_once(DIR_FS_CATALOG . 'admin/cseo_product_export/' . $t_module);
                $c_modul_name = basename($t_module, ".php");
                $coo_temp_class = new $c_modul_name();
                $t_modules[$coo_temp_class->v_export_type][] = $t_module;
            }
            $t_content = '<table border="0" width="100%" cellspacing="0" cellpadding="2">';
            foreach ($t_modules as $t_key => $t_value) {
                $t_content .= '<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent" width="70%">' . CSEO_PRODUCT_EXPORT_OFFERER . '</td>
<td class="dataTableHeadingContent" width="15%">Installtion</td>
<td class="dataTableHeadingContent" width="15%" style="border:0px">Export</td></tr>';

                switch ($t_key) {
                    case 'comparison':
                        $t_content .= '<tr><td colspan="3">' . CSEO_PRODUCT_EXPORT_COMPARISON . '</td></tr>';
                        break;
                    case 'selling':
                        $t_content .= '<tr><td colspan="3">' . CSEO_PRODUCT_EXPORT_SHOPPING_PORTALS . '</td></tr>';
                        break;
                    case 'affiliate':
                        $t_content .= '<tr><td colspan="3">Affiliate</td></tr>';
                        break;
                    default:
                        $t_content .= '<tr><td colspan="3">' . CSEO_PRODUCT_EXPORT_OFFERER . '</td></tr>';
                }
                foreach ($t_value as $t_module) {
                    require_once(DIR_FS_CATALOG . 'admin/cseo_product_export/' . $t_module);
                    $c_modul_name = basename($t_module, ".php");
                    $coo_temp_class = new $c_modul_name();
                    $t_key_value_query = xtc_db_query("SELECT cseo_key, cseo_value FROM cseo_configuration WHERE cseo_key = 'CSEO_" . xtc_db_input($coo_temp_class->v_keyname) . "_STATUS'");
                    $t_key_value = xtc_db_fetch_array($t_key_value_query);
                    $t_content .= '<tr>
<td class="dataTableContent"><a target="_blank" href="http://' . $coo_temp_class->v_module_homepage . '">' . $coo_temp_class->v_module_name . '</a></td>';

                    if (empty($t_key_value['cseo_value'])) {
                        $t_content .= '<td class="dataTableContent"><a class="button" href="' . xtc_href_link(FILENAME_CSEO_PRODUCT_EXPORT, 'set=' . $_GET['set'] . '&module=' . $coo_temp_class->v_filename . '&action=install') . '">' . BUTTON_MODULE_INSTALL . '</a></td>';
                        $t_content .= '<td class="dataTableContent"><a>&nbsp;</a></td>';
                    } else {
                        $t_content .= '<td class="dataTableContent"><a class="button" href="' . xtc_href_link(FILENAME_CSEO_PRODUCT_EXPORT, 'set=' . $_GET['set'] . '&module=' . $coo_temp_class->v_filename . '&action=remove') . '">' . BUTTON_MODULE_REMOVE . '</a></td>';
                        $t_content .= '<td class="dataTableContent"><a class="button" href="' . xtc_href_link(FILENAME_CSEO_PRODUCT_EXPORT, 'set=' . $_GET['set'] . '&module=' . $coo_temp_class->v_filename . '&action=edit') . '">' . BUTTON_START . '</a></td>';
                    }
                    $t_content .= '</tr>';
                }
                $t_content .= '</tr>';
            }
            $t_content .= '</table>';
            $this->v_module_content = $t_content;
        } else {
// get_configuration (with dynamic param naming)
            foreach ($this->v_config_array as $v_post => $v_db_keys) {
                $t_query = xtc_db_query("SELECT cseo_key, cseo_value FROM cseo_configuration WHERE cseo_key = 'CSEO_" . $this->coo_export->v_keyname . "_" . $v_db_keys[0] . "'");
                $t_var_name = 't_export_' . $v_post;
                $$t_var_name = xtc_db_fetch_array($t_query);
            }
// with attributes?
            $t_attributes_yes = false;
            $t_attributes_no = true;
            if ($t_export_attributes[cseo_value] == 'yes') {
                $t_attributes_yes = true;
                $t_attributes_no = false;
            }
            $t_customers_statuses_array = xtc_get_customers_statuses();
// build Currency Select
            $t_curr = '';
            $t_currencies = xtc_db_query("SELECT code FROM " . TABLE_CURRENCIES);
            while ($t_currencies_data = xtc_db_fetch_array($t_currencies)) {
                if ($t_export_currency[cseo_value] != null) {
                    $t_checked = '';
                    if ($t_export_currency[cseo_value] == $t_currencies_data['code']) {
                        $t_checked = ' checked="checked"';
                    }
                } else {
                    $t_checked = '';
                    if ($t_currencies_data['code'] == $_SESSION['currency']) {
                        $t_checked = ' checked="checked"';
                    }
                }
                $t_curr .= '<input name="currency" value="' . $t_currencies_data['code'] . '"' . $t_checked . ' type="radio">' . $t_currencies_data['code'] . '<br />';
            }
            $t_campaign_array = array(array('id' => '', 'text' => TEXT_NONE));
            $t_campaign_query = xtc_db_query("SELECT campaigns_name, campaigns_refID FROM " . TABLE_CAMPAIGNS . " ORDER BY campaigns_id");
            while ($t_campaign = xtc_db_fetch_array($t_campaign_query)) {
                $t_campaign_array[] = array('id' => 'refID=' . $t_campaign['campaigns_refID'], 'text' => $t_campaign['campaigns_name']);
            }
// information (SAVED, EXPORT)
            if (isset($_POST['do_export'])) {
                $t_file_link = '<br /><div class="mysuccesslog">' . CSEO_PRODUCT_EXPORT_SUCCESS . '<a href="../export/' . $t_export_filename['cseo_value'] . '" target="_blank">' . HTTP_SERVER . DIR_WS_CATALOG . 'export/' . $t_export_filename['cseo_value'] . '</a></div>';
            }
            if (isset($_POST['do_save'])) {
                $t_file_link = '<br /><div class="mysuccesslog">' . CSEO_PRODUCT_SAVE_SUCCESS . '</a></div>';
            }
// customer group?
            $t_customers_groups = '1';
            if (cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_CUSTOMERS_GROUP', 'ASSOC', true) !== false) {
                $t_customers_groups = cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_CUSTOMERS_GROUP', 'ASSOC', true);
            }
// form
            $this->v_module_content = '<form method="post" action="#" name="csv_export"/>
<input type="hidden" name="status" value="1" />
<div style="clear:both;">
</div>
' . $t_file_link . '
<table style="width:100%;" cellpadding="4" cellspacing="2">';
// module info
            if ($this->coo_export->v_module_homepage) {
                $this->v_module_content .= '<tr>
<td class="dataTableContent">' . MODULE_NAME . '</td>
<td class="dataTableContent">
<a href="http://' . $this->coo_export->v_module_homepage . '" target="_blank">' . $this->coo_export->v_module_name . '</a></td>
</tr>';
            } else {
                $this->v_module_content .= '<tr>
<td class="dataTableContent">' . MODULE_NAME . '</td>
<td class="dataTableContent">' . $this->coo_export->v_module_name . '</td>
</tr>';
            }
// export type
            switch ($this->coo_export->v_export_type) {
                case 'comparison':
                    $this->v_module_content .= '<tr>
<td class="dataTableContent">' . MODULE_TYPE . '</td>
<td class="dataTableContent">' . CSEO_PRODUCT_EXPORT_COMPARISON . '</td>
</tr>';
                    break;
                case 'selling':
                    $this->v_module_content .= '<tr>
<td class="dataTableContent">' . MODULE_TYPE . '</td>
<td class="dataTableContent">' . CSEO_PRODUCT_EXPORT_SHOPPING_PORTALS . '</td>
</tr>';
                    break;
                case 'affiliate':
                    $this->v_module_content .= '<tr>
<td class="dataTableContent">' . MODULE_TYPE . '</td>
<td class="dataTableContent">Affiliate</td>
</tr>';
                    break;
            }
// info
            if ($this->coo_export->v_partnerlink) {
                $this->v_module_content .= '<tr>
<td class="dataTableContent">' . MODULE_INFO . '</td>
<td class="dataTableContent">
<a style="font-family: Arial,sans-serif; text-decoration: underline; font-size: 11px; color: #444444" href="http://' . $this->coo_export->v_partnerlink . '" target="_blank">' . CSEO_PRODUCT_EXPORT_MORE_INFORMATION . '</a></td>
</tr>';
            }
// filename
            if ($this->coo_export->v_field_filename) {
                $this->v_module_content .= '<tr>
<td class="dataTableContent">' . MODULE_FILE_TITLE . '</td>
<td class="dataTableContent">' . xtc_draw_input_field('filename', $t_export_filename['cseo_value']) . '<br />' . MODULE_FILE_DESC . '</td>
</tr>';
            }
// customer groups
            if ($this->coo_export->v_field_customers_groups) {
                $this->v_module_content.= '<tr>
<td class="dataTableContent">' . EXPORT_STATUS_TYPE . '</td>
<td class="dataTableContent">' . xtc_draw_pull_down_menu('customers_groups', $t_customers_statuses_array, $t_customers_groups) . '<br />' . EXPORT_STATUS . '</td>
</tr>';
            }
// currency
            if ($this->coo_export->v_field_currency) {
                $this->v_module_content.= '<tr>
<td class="dataTableContent">' . CURRENCY . '</td>
<td class="dataTableContent">' . $t_curr . '<br />' . CURRENCY_DESC . '</td>
</tr>';
            }
// shipping cost
            if ($this->coo_export->v_field_shipping_costs) {
                $this->v_module_content .= '<tr>
<td class="dataTableContent">' . SHIPPING_COSTS_TITLE . '</td>
<td class="dataTableContent">' . xtc_draw_input_field('shipping_costs', $t_export_shipping_costs['cseo_value']) . '<br />' . SHIPPING_COSTS_DESC . '</td>
</tr>';
            }
// shipping cost free
            if ($this->coo_export->v_field_shipping_costs_free) {
                $this->v_module_content.= '<tr>
<td class="dataTableContent">' . SHIPPING_COSTS_FREE_TITLE . '</td>
<td class="dataTableContent">' . xtc_draw_input_field('shipping_costs_free', $t_export_shipping_costs_free['cseo_value']) . '<br />' . SHIPPING_COSTS_FREE_DESC . '</td>
</tr>';
            }
// attribut export yes/no
            if ($this->coo_export->v_field_attributes) {
                $this->v_module_content.= '<tr>
<td class="dataTableContent">' . EXPORT_ATTRIBUTES . '</td>
<td class="dataTableContent">' . xtc_draw_radio_field('attributes', 'no', $t_attributes_no) . NO . '<br />' .
                        xtc_draw_radio_field('attributes', 'yes', $t_attributes_yes) . YES . '<br />' . EXPORT_ATTRIBUTES_DESC . '</td>
</tr>';
            }
// campaign
            if ($this->coo_export->v_field_campaign) {
                $this->v_module_content.= '<tr>
<td class="dataTableContent">' . CAMPAIGNS . '</td>
<td class="dataTableContent">' . xtc_draw_pull_down_menu('campaign', $t_campaign_array, $t_export_campaign['cseo_value']) . '<br />' . CAMPAIGNS_DESC . '</td>
</tr>';
            }
// formAddOn
            $this->v_module_content.=$this->coo_export->formAddOn();
            if ($this->coo_export->v_field_export) {
                $this->v_module_content .= '<tr>
<td class="dataTableContent">' . EXPORT_TYPE . '</td>
<td class="dataTableContent">' . xtc_draw_radio_field('export', 'no', true) . EXPORT_NO . '<br />' .
                        xtc_draw_radio_field('export', 'yes', false) . EXPORT_YES . '<br />' . EXPORT . '</td>
</tr>';
            }
// (stock > 0) switch
            $this->v_module_content .= '<tr>
<td class="dataTableContent">' . STOCK . '</td>
<td class="dataTableContent">
' . xtc_draw_input_field('stock', cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_STOCK', 'ASSOC', true), 'style="width:50px;"') . '&nbsp;' . STOCK_DESC . '<br />
</td>
</tr>';

            $t_add_vpe_to_name = cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_ADD_VPE_TO_NAME');

// VPE
            $this->v_module_content .= '<tr>
<td class="dataTableContent"><strong>' . ADD_VPE_TO_NAME . '</strong></td>
<td class="dataTableContent">' . xtc_draw_radio_field('add_vpe_to_name', 'no', (($t_add_vpe_to_name == 'no') ? true : false)) . ADD_VPE_TO_NAME_NO . '<br />' .
                    xtc_draw_radio_field('add_vpe_to_name', 'prefix', (($t_add_vpe_to_name == 'prefix') ? true : false)) . ADD_VPE_TO_NAME_PREFIX . '<br />' .
                    xtc_draw_radio_field('add_vpe_to_name', 'suffix', (($t_add_vpe_to_name == 'suffix') ? true : false)) . ADD_VPE_TO_NAME_SUFFIX . '<br />' . ADD_VPE_TO_NAME_DESC . '</td>
</tr>';

// cronjob
            $t_export_cronjob_query = xtc_db_query("SELECT cseo_key, cseo_value FROM cseo_configuration WHERE cseo_key = 'CSEO_" . $this->coo_export->v_keyname . "_CRONJOB'");
            $t_export_cronjob = xtc_db_fetch_array($t_export_cronjob_query);
            $t_export_cronjob_url = HTTP_SERVER . DIR_WS_CATALOG . 'cseo_product_export_cron.php?token=' . FileLog::get_secure_token();
            $this->v_module_content .= '<tr>
<td class="dataTableContent">' . CRONJOB . '</td>
<td class="dataTableContent">
' . xtc_draw_checkbox_field('cronjob', '1', (bool) $t_export_cronjob['cseo_value']) . CRONJOB_DESC . '<br />
Cronjob-URL: ' . $t_export_cronjob_url . '
</td>
</tr>';
// end table
            $this->v_module_content .= '</table>' .
// buttons
                    xtc_draw_hidden_field('create_csv', $_GET['module']) . '
<div>
<input type="submit" class="button" name="do_export" value="' . BUTTON_EXPORT . '" />
<input type="submit" class="button" name="do_save" value="' . BUTTON_SAVE . '" />
<a href="cseo_product_export.php" class="button">' . BUTTON_BACK . '<a/>
<a href="cseo_product_export.php?module=' . $_GET['module'] . '" class="button">' . BUTTON_RESET . '<a/>
</div>' .
                    xtc_draw_hidden_field('action', 'save') . '</form>';
        }
        return true;
    }

    function buildCAT($catID) {
        if (isset($this->CAT[$catID])) {
            return $this->CAT[$catID];
        } else {
            $cat = array();
            $tmpID = $catID;
            while ($this->getParent($catID) != 0 || $catID != 0) {
                $cat_select = xtc_db_query("SELECT categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id = '" . $catID . "' AND language_id='" . (int) $_SESSION['languages_id'] . "'");
                $cat_data = xtc_db_fetch_array($cat_select);
                $catID = $this->getParent($catID);
                $cat[] = $cat_data['categories_name'];
            }
            $catStr = '';
            for ($i = count($cat); $i > 0; $i--) {
                $catStr .= $cat[$i - 1] . ' > ';
            }
            $this->CAT[$tmpID] = $catStr;
            return $this->CAT[$tmpID];
        }
        return true;
    }

    function getParent($catID) {
        if (isset($this->PARENT[$catID])) {
            return $this->PARENT[$catID];
        } else {
            $parent_query = xtc_db_query("SELECT parent_id FROM " . TABLE_CATEGORIES . " WHERE categories_id = '" . $catID . "'");
            $parent_data = xtc_db_fetch_array($parent_query);
            $this->PARENT[$catID] = $parent_data['parent_id'];
            return $parent_data['parent_id'];
        }
        return true;
    }

    function do_export() {
// do export without filter
        $cat_filter_all = array();
        $prod_id_array = $this->get_product_ids($cat_filter_all, false);
        $this->create_csv($prod_id_array, $this->v_module_data_array['filename']);
        return true;
    }

    function get_product_ids($p_cat_array = array(), $p_only = false) {
// setup
        $t_id_list = implode(",", $p_cat_array);
        $t_cat_filter = '';
// if filter, use filter
        if (!empty($p_cat_array)) {
            $t_cat_filter = 'AND ptc.categories_id NOT IN (' . $t_id_list . ')';
            if ($p_only) {
                $t_cat_filter = 'AND ptc.categories_id IN (' . $t_id_list . ')';
            }
        }
// if STOCK filter is in use
        $t_stock_switch = (double) cseo_get_conf("CSEO_" . $this->coo_export->v_keyname . "_STOCK", 'ASSOC', true);
        $t_stock_filter = '';
        if (!empty($t_stock_switch)) {
            $t_stock_filter = "AND p.products_quantity >= " . $t_stock_switch;
        }

        if (empty($t_cat_filter) && empty($t_stock_filter)) {
            return array();
        }

// sql
        $t_query = "SELECT DISTINCT p.products_id 
						FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc
						LEFT JOIN " . TABLE_CATEGORIES . " c ON (c.categories_id = ptc.categories_id AND c.categories_status = 1)
						WHERE  p.products_status = 1
						AND ( (p.products_id = ptc.products_id) OR (p.products_id = ptc.products_id AND ptc.categories_id = 0) )
						" . $t_cat_filter . "
						" . $t_stock_filter . "
						ORDER BY p.products_id ASC";
        $t_export_query = xtc_db_query($t_query);
// create prod_id array
        $t_prod_id_array = array();
        while ($t_products = xtc_db_fetch_array($t_export_query)) {
            $t_prod_id_array[] = $t_products['products_id'];
        }
// return array
        return $t_prod_id_array;
    }

    function create_csv($p_prod_id_array = array(), $p_filename = '') {
// check for needed params
        if (empty($p_filename)) {
            return false;
        }
// needed class for pricing
        require_once(DIR_FS_CATALOG . 'includes/classes/xtcPrice.php');
        $coo_xtPrice = new xtcPrice($this->v_module_data_array['currency'], $this->v_module_data_array['customers_groups']);
// create export (XML or FILE) -> first of all HEADER line
        $t_xml_export_array = array();
        if ($this->coo_export->v_module_format == 'xml') {
            $t_xml_export_array[] = $this->coo_export->exportScheme();
        } else {
            $t_file = fopen(DIR_FS_CATALOG . 'export/' . $p_filename, 'w');
            $this->new_fputcsv($t_file, $this->coo_export->exportScheme(), $this->coo_export->v_delimiter, $this->coo_export->v_enclosure);
        }

        $t_group_check = '';
        if (GROUP_CHECK == 'true') {
            $t_customers_group = (int) cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_CUSTOMERS_GROUP', 'ASSOC', true);
            $t_group_check = " AND p.group_permission_" . $t_customers_group . " = '1' ";
        }

        $t_products_check = '';
        if (!empty($p_prod_id_array)) {
            $t_products_check = ' AND p.products_id IN (' . implode(',', $p_prod_id_array) . ') ';
        }

// get data
        $t_query = "SELECT DISTINCT
						p.*,
						pd.*,
						m.manufacturers_name,
						sp.specials_new_products_price
						FROM
						" . TABLE_PRODUCTS . " p
						LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
						LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id AND pd.language_id = '" . (int) $_SESSION['languages_id'] . "')
						LEFT JOIN " . TABLE_SPECIALS . " sp ON (p.products_id = sp.products_id)
						WHERE
						p.products_status = 1 
						" . $t_products_check . "
						" . $t_group_check . "
						ORDER BY 
						p.products_date_added DESC,
						pd.products_name";
        $t_export_query = xtc_db_query($t_query);
        $t_product_info = array();
        while ($t_products = xtc_db_fetch_array($t_export_query)) {
//UTF8 Decode
            $t_products['products_name'] = $t_products['products_name'];
            $t_products['products_description'] = $t_products['products_description'];
            $t_products['products_short_description'] = $t_products['products_short_description'];
            $t_products['products_model'] = $t_products['products_model'];
            $t_products['products_meta_keywords'] = $t_products['products_meta_keywords'];
            $t_products['manufacturers_name'] = $t_products['manufacturers_name'];
// tax
            $t_products['products_tax'] = $coo_xtPrice->TAX[$t_products['products_tax_class_id']];

// categories
            $t_categories = 0;
            $t_categorie_query = xtc_db_query("SELECT categories_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = '" . $t_products['products_id'] . "' AND categories_id != '0'");
            while ($t_categorie_data = xtc_db_fetch_array($t_categorie_query)) {
                $t_categories = $t_categorie_data['categories_id'];
            }
            $t_cat = $this->buildCAT($t_categories);
            $t_products['products_categories'] = substr($t_cat, 0, strlen($t_cat) - 2);
            $t_kat_array = explode(">", $t_products['products_categories']);
            $t_products['products_categories_last'] = trim($t_kat_array[count($t_kat_array) - 1]);
// availability
            $t_products['products_availability'] = '0000-00-00 00:00:00';
            if (!empty($t_products['products_date_available'])) {
                $t_products['products_availability'] = $t_products['products_date_available'];
            }
// short_description
            $t_products_short_description = trim($t_products['products_short_description']);
            $t_products_short_description = str_replace("\n", '', $t_products_short_description);
            $t_products['products_short_description'] = str_replace("\r", '', $t_products_short_description);
// description
            $t_products_description = preg_replace('!(.*?)\[TAB:(.*?)\](.*?)!is', "$1$3", $t_products['products_description']);
            $t_products_description = trim($t_products_description);
            $t_products_description = str_replace("\n", '', $t_products_description);
            $t_products['products_description'] = str_replace("\r", '', $t_products_description);
// products_image
            if ($t_products['products_image'] != '') {
                $t_products['products_image_1_small'] = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_THUMBNAIL_IMAGES . $t_products['products_image'];
                $t_products['products_image_1'] = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_POPUP_IMAGES . $t_products['products_image'];
            }
// product_images 2-6
            $t_images_query = xtc_db_query("SELECT image_nr, image_name FROM " . TABLE_PRODUCTS_IMAGES . " WHERE products_id = '" . $t_products['products_id'] . "'");
            while ($t_images_data = xtc_db_fetch_array($t_images_query)) {
                if (!empty($t_images_data['image_name'])) {
                    $t_img_nr = (int) $t_images_data['image_nr'] + 1;
                    $t_products['products_image_' . $t_img_nr] = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_POPUP_IMAGES . $t_images_data['image_name'];
                }
            }
// products_shippingtime
            $t_cseo_get_shippingtime = xtc_db_query("SELECT shipping_status_name FROM shipping_status WHERE shipping_status_id = '" . $t_products['products_shippingtime'] . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "'");
            if (xtc_db_num_rows($t_cseo_get_shippingtime) == 1) {
                $t_cseo_shippingtime = xtc_db_fetch_array($t_cseo_get_shippingtime);
                $t_products['products_shippingtime'] = $t_cseo_shippingtime['shipping_status_name'];
            }
// products_link
            require_once(DIR_FS_CATALOG . 'inc/xtc_href_link_from_admin.inc.php');
            $t_products['products_link'] = xtc_href_link_from_admin('product_info.php', xtc_product_link($t_products['products_id'], $t_products['products_name'])) . '?' . $this->v_module_data_array['campaign'];

// currencies
            $t_products['products_currency'] = $this->v_module_data_array['currency'];
// retail price
            $t_special_price = $coo_xtPrice->xtcCheckSpecial($t_products['products_id']);
            if ($t_special_price != null) {
                $t_products['retail_price'] = $t_products['products_price'] / 100 * $t_products['products_tax'] + $t_products['products_price'];
                $t_products['products_price'] = $coo_xtPrice->xtcGetPrice($t_products['products_id'], $format = false, 1, $t_products['products_tax_class_id'], '');
            } else {
                $t_products['products_price'] = $coo_xtPrice->xtcGetPrice($t_products['products_id'], $format = false, 1, $t_products['products_tax_class_id'], '');
            }

// shipping_costs
            if ($t_products['products_shipping_costs'] > 0) {
                $t_shipping_costs = $t_products['products_shipping_costs'];
                if (strpos(MODULE_SHIPPING_INSTALLED, 'gambioultra') > 0) {
                    require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
                    $t_tax_rate = xtc_get_tax_rate(MODULE_SHIPPING_GAMBIOULTRA_TAX_CLASS);
                    $t_shipping_costs = $coo_xtPrice->xtcAddTax($t_shipping_costs, $t_tax_rate);
                }
            } else {
                $t_shipping_costs = $this->v_module_data_array['shipping_costs'];
            }
            if ($t_products['products_price'] > $this->v_module_data_array['shipping_costs_free'] && $this->v_module_data_array['shipping_costs_free'] != null) {
                $t_shipping_costs = '';
            }

// VPE
            if ($t_products['products_vpe_value'] > 0) {
                $t_products['products_vpe_compare'] = 1;
                $t_vpe_query = xtc_db_query("SELECT products_vpe_name FROM " . TABLE_PRODUCTS_VPE . " WHERE products_vpe_id = '" . $t_products['products_vpe'] . "' AND language_id = '" . $_SESSION['languages_id'] . "'");
                $t_vpe_array = xtc_db_fetch_array($t_vpe_query);
                $t_products['products_vpe_name'] = $t_vpe_array['products_vpe_name'];
                $t_products['packing_unit_name'] = $t_vpe_array['products_vpe_name'];
                $t_baseprice = $t_products['products_price'] / $t_products['products_vpe_value'];
                $t_products['baseprice'] = number_format($t_baseprice, 2, $t_products_price_format, '');
            } else {
                $t_products['products_vpe_compare'] = '';
                $t_products['products_vpe_value'] = '';
                $t_products['products_vpe_name'] = '';
                $t_products['packing_unit_name'] = '';
            }

            $t_products['products_shipping_costs'] = $t_shipping_costs;
            $t_attributes_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id='" . $t_products['products_id'] . "'");
            $t_num_rows = xtc_db_num_rows($t_attributes_query);
// tax-free price
            $t_products['products_price_taxfree'] = $t_products['products_price'] - ($t_products['products_price'] * $t_products['products_tax'] / 100);
            $t_products['products_price_taxfree'] = number_format($t_products['products_price_taxfree'], 2, $t_products_price_format, '');
//  if Attributes
            if ($this->v_module_data_array['attributes'] == 'yes' && $t_num_rows != 0) {
                $t_products_info = $t_products;
                while ($t_products_attributes_array = xtc_db_fetch_array($t_attributes_query)) {
                    $t_products_options_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_OPTIONS . " WHERE products_options_id = '" . $t_products_attributes_array['options_id'] . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "'");
                    $t_products_options = xtc_db_fetch_array($t_products_options_query);
                    $t_products_options_values_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " WHERE products_options_values_id = '" . $t_products_attributes_array['options_values_id'] . "' AND language_id = '" . (int) $_SESSION['languages_id'] . "'");
                    $t_products_options_values = xtc_db_fetch_array($t_products_options_values_query);
                    $t_products_attributes_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id = '" . $t_products['products_id'] . "' AND options_id = '" . $t_products_attributes_array['options_id'] . "' AND options_values_id='" . $t_products_attributes_array['options_values_id'] . "'");
                    $t_products_attributes = xtc_db_fetch_array($t_products_attributes_query);
                    $t_products_tax = xtc_get_tax_rate($t_products['products_tax_class_id']);
                    $t_products_attribut_price = $coo_xtPrice->xtcGetOptionPrice($t_products['products_id'], $t_products_attributes_array['options_id'], $t_products_attributes_array['options_values_id']);
                    $t_new_products_attribut_price = $t_products['products_price'] + $t_products_attribut_price['price'];
                    if ($t_products['retail_price'] != null) {
                        $t_products['retail_price'] = $t_products['retail_price'] + $t_products_attribut_price['price'];
                    }
                    $t_products_info['products_price'] = number_format($t_new_products_attribut_price, 2, $t_products_price_format, '');
                    if ($t_products_info['products_price'] > $this->v_module_data_array['shipping_costs_free'] && $this->v_module_data_array['shipping_costs_free'] != null) {
                        $t_products_info['products_shipping_costs'] = number_format('0', 2, $t_products_shipping_costs_format, '');
                    } else {
                        if ($t_products_info['products_shipping_costs'] > 0) {
                            $t_shipping_costs = $t_products_info['products_shipping_costs'];
                        } else {
                            $t_shipping_costs = $this->v_module_data_array['shipping_costs'];
                        }
                        $t_products_info['products_shipping_costs'] = $t_shipping_costs;
                    }
                    $t_products_info['products_model'] = $t_products['products_model'] . $t_products_attributes['attributes_model'];
                    $t_products_info['attributes_model'] = $t_products_attributes['attributes_model'];
                    $t_products_info['products_id_copy'] = $t_products['products_id'];
                    $t_products_info['products_attributes_id'] = $t_products_attributes['products_attributes_id'];
                    $t_products_info['products_id'] = $t_products['products_id'] . $t_products_attributes['products_attributes_id'];
                    $t_products_info['products_name'] = $t_products['products_name'] . ' ' . $t_products_options_values['products_options_values_name'];
                    $t_products_info['products_link'] = $t_products['products_link'] . '#' . $t_products_attributes_array['options_id'] . '-' . $t_products_attributes_array['options_values_id'];
// EAN
                    if (!empty($t_products_attributes['attributes_ean'])) {
                        $t_products_info['products_ean'] = $t_products_attributes['attributes_ean'];
                    }
// VPE
                    $t_products_info['products_options_values_name'] = $t_products_options_values['products_options_values_name'];
                    if ((int) $t_products_attributes['products_vpe_id'] > 0) {
                        $t_vpe_query = xtc_db_query("SELECT products_vpe_name FROM " . TABLE_PRODUCTS_VPE . " WHERE products_vpe_id = '" . $t_products_attributes['products_vpe_id'] . "' AND language_id = '" . $_SESSION['languages_id'] . "'");
                        $t_vpe_array = xtc_db_fetch_array($t_vpe_query);
                        $t_products_info['packing_unit_name'] = $t_vpe_array['products_vpe_name'];
                        $t_products_info['products_vpe_name'] = $t_vpe_array['products_vpe_name'];
                    }
                    $t_products_info['packing_unit_value'] = str_replace('.', $t_products_price_format, $t_products_attributes['attributes_vpe_value']);
                    if ($t_products_attributes['attributes_vpe_value'] != 0) {
                        $t_baseprice = $t_new_products_attribut_price / $t_products_attributes['attributes_vpe_value'];
                        $t_products_info['baseprice'] = number_format($t_baseprice, 2, $t_products_price_format, '');
                    }

// add VPE to name
                    if ((int) $t_products_attributes['products_vpe_id'] > 0) {
                        $t_add_vpe_to_name = cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_ADD_VPE_TO_NAME');
                        switch ($t_add_vpe_to_name) {
                            case 'prefix':
                                $t_products_info['products_name'] = '(' . number_format((double) $t_products_info['baseprice'], 2, ',', '') . ' ' . cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products_info['packing_unit_name'] . ') ' . $t_products_info['products_name'];
                                break;
                            case 'suffix':
                                $t_products_info['products_name'] .= ' (' . number_format((double) $t_products_info['baseprice'], 2, ',', '') . ' ' . cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products_info['packing_unit_name'] . ')';
                                break;
                        }
                    } elseif ($t_products['products_vpe_value'] > 0) {
                        $t_add_vpe_to_name = cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_ADD_VPE_TO_NAME');
                        switch ($t_add_vpe_to_name) {
                            case 'prefix':
                                $t_products_info['products_name'] = '(' . number_format((double) $t_products['baseprice'], 2, ',', '') . ' ' . cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products['packing_unit_name'] . ') ' . $t_products_info['products_name'];
                                break;
                            case 'suffix':
                                $t_products_info['products_name'] .= ' (' . number_format((double) $t_products['baseprice'], 2, ',', '') . ' ' . cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products['packing_unit_name'] . ')';
                                break;
                        }
                    }

                    $t_products_results_array = $this->coo_export->formatResults($t_products_info);
                    $t_exportScheme = $this->coo_export->exportScheme();
                    $i = '0';
                    $t_exportScheme = array_flip($t_exportScheme);
                    foreach ($t_exportScheme as $t_csv_field) {
                        $t_product_output[$i] = $t_products_results_array[$t_csv_field];
                        $i++;
                    }
// XML or CSV
                    if ($this->coo_export->v_module_format == 'xml') {
                        $t_xml_export_array[] = $t_product_output;
                    } else {
                        $this->new_fputcsv($t_file, $t_product_output, $this->coo_export->v_delimiter, $this->coo_export->v_enclosure);
                    }
                }
            } else {

// add VPE to name
                if ($t_products['products_vpe_value'] > 0) {
                    $t_add_vpe_to_name = cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_ADD_VPE_TO_NAME');
                    switch ($t_add_vpe_to_name) {
                        case 'prefix':
                            $t_products['products_name'] = '(' . number_format((double) $t_products['baseprice'], 2, ',', '') . ' ' . cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products['packing_unit_name'] . ') ' . $t_products['products_name'];
                            break;
                        case 'suffix':
                            $t_products['products_name'] .= ' (' . number_format((double) $t_products['baseprice'], 2, ',', '') . ' ' . cseo_get_conf('CSEO_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products['packing_unit_name'] . ')';
                            break;
                    }
                }

// if no attributes
                $t_products_results_array = $this->coo_export->formatResults($t_products);
                $t_exportScheme = $this->coo_export->exportScheme();
                $i = '0';
                $t_exportScheme = array_flip($t_exportScheme);
                foreach ($t_exportScheme as $t_csv_field) {
                    $t_product_output[$i] = $t_products_results_array[$t_csv_field];
                    $i++;
                }
// XML or CSV
                if ($this->coo_export->v_module_format == 'xml') {
                    $t_xml_export_array[] = $t_product_output;
                } else {
                    $this->new_fputcsv($t_file, $t_product_output, $this->coo_export->v_delimiter, $this->coo_export->v_enclosure);
                }
            }
        }
// XML or CSV
        if ($this->coo_export->v_module_format == 'xml') {
            $this->coo_export->create_xml($p_filename, $t_xml_export_array);
        } else {
            fclose($t_file);
        }
// if DOWNLOAD
        switch ($this->v_module_data_array['export']) {
            case 'yes':
// send file to browser
                $extension = substr($file, -3);
                $fp = fopen(DIR_FS_DOCUMENT_ROOT . 'export/' . $p_filename, "rb");
                $buffer = fread($fp, filesize(DIR_FS_DOCUMENT_ROOT . 'export/' . $p_filename));
                fclose($fp);
                header('Content-type: application/x-octet-stream');
                header('Content-disposition: attachment; filename=' . $p_filename);
                echo $buffer;
                exit;
                break;
        }
        return true;
    }

    function define_missing_path_names() {
        if (!defined('HTTPS_CATALOG_SERVER'))
            define('HTTPS_CATALOG_SERVER', HTTPS_SERVER);
        if (!defined('HTTP_CATALOG_SERVER'))
            define('HTTP_CATALOG_SERVER', HTTP_SERVER);

        if (!defined('DIR_WS_IMAGES'))
            define('DIR_WS_IMAGES', 'images/');

        if (!defined('DIR_FS_CATALOG_IMAGES'))
            define('DIR_FS_CATALOG_IMAGES', DIR_FS_CATALOG . 'images/');
        if (!defined('DIR_FS_CATALOG_ORIGINAL_IMAGES'))
            define('DIR_FS_CATALOG_ORIGINAL_IMAGES', DIR_FS_CATALOG_IMAGES . 'product_images/original_images/');
        if (!defined('DIR_FS_CATALOG_THUMBNAIL_IMAGES'))
            define('DIR_FS_CATALOG_THUMBNAIL_IMAGES', DIR_FS_CATALOG_IMAGES . 'product_images/thumbnail_images/');
        if (!defined('DIR_FS_CATALOG_INFO_IMAGES'))
            define('DIR_FS_CATALOG_INFO_IMAGES', DIR_FS_CATALOG_IMAGES . 'product_images/info_images/');
        if (!defined('DIR_FS_CATALOG_POPUP_IMAGES'))
            define('DIR_FS_CATALOG_POPUP_IMAGES', DIR_FS_CATALOG_IMAGES . 'product_images/popup_images/');

        if (!defined('DIR_WS_CATALOG_IMAGES'))
            define('DIR_WS_CATALOG_IMAGES', DIR_WS_CATALOG . 'images/');
        if (!defined('DIR_WS_CATALOG_ORIGINAL_IMAGES'))
            define('DIR_WS_CATALOG_ORIGINAL_IMAGES', DIR_WS_CATALOG_IMAGES . 'product_images/original_images/');
        if (!defined('DIR_WS_CATALOG_THUMBNAIL_IMAGES'))
            define('DIR_WS_CATALOG_THUMBNAIL_IMAGES', DIR_WS_CATALOG_IMAGES . 'product_images/thumbnail_images/');
        if (!defined('DIR_WS_CATALOG_INFO_IMAGES'))
            define('DIR_WS_CATALOG_INFO_IMAGES', DIR_WS_CATALOG_IMAGES . 'product_images/info_images/');
        if (!defined('DIR_WS_CATALOG_POPUP_IMAGES'))
            define('DIR_WS_CATALOG_POPUP_IMAGES', DIR_WS_CATALOG_IMAGES . 'product_images/popup_images/');

        return true;
    }

}
