<?php
/* -----------------------------------------------------------------
 * 	$Id: configuration.php 948 2014-04-09 11:30:00Z akausch $
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

require_once('includes/application_top.php');
$smarty = new Smarty;
$coo_text_mgr = new LanguageTextManager('configuration', $_SESSION['languages_id']);
$smarty->assign('txt', $coo_text_mgr->v_section_content_array['configuration']);

if ($_GET['action']) {
    switch ($_GET['action']) {
        case 'save':
            if ($_GET['gID'] == '31') {
                if (isset($_POST['_PAYMENT_MONEYBOOKERS_EMAILID'])) {
                    $url = 'https://www.moneybookers.com/app/email_check.pl?email=' . $_POST['_PAYMENT_MONEYBOOKERS_EMAILID'] . '&cust_id=8644877&password=1a28e429ac2fcd036aa7d789ebbfb3b0';
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    $result = curl_exec($ch);
                    if ($result == 'NOK') {
                        $messageStack->add_session(MB_ERROR_NO_MERCHANT, 'error');
                    }
                    if (strstr($result, 'OK,')) {
                        $data = explode(',', $result);
                        $_POST['_PAYMENT_MONEYBOOKERS_MERCHANTID'] = $data[1];
                        $messageStack->add_session(sprintf(MB_MERCHANT_OK, $data[1]), 'success');
                    }
                }
            }
            $configuration_query = xtc_db_query("SELECT configuration_key, configuration_id, configuration_value, use_function, set_function FROM " . TABLE_CONFIGURATION . " WHERE configuration_group_id = '" . (int) $_GET['gID'] . "' ORDER BY sort_order");
            while ($configuration = xtc_db_fetch_array($configuration_query)) {
                xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . $_POST[$configuration['configuration_key']] . "' WHERE configuration_key='" . $configuration['configuration_key'] . "'");
            }
            xtc_redirect(FILENAME_CONFIGURATION . '?gID=' . (int) $_GET['gID']);
            break;
    }
}

$cfg_group = xtc_db_fetch_array(xtc_db_query("SELECT configuration_group_title FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_id = '" . (int) $_GET['gID'] . "';"));
require_once(DIR_WS_INCLUDES . 'header.php');
$smarty->assign('FORM', xtc_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . (int) $_GET['gID'] . '&action=save'));
$smarty->assign('FORM_END', '</form>');
$smarty->assign('HEADING_TITLE', $cfg_group['configuration_group_title']);
$smarty->assign('BUTTONS', xtc_button(BUTTON_SAVE));

        switch ($_GET['gID']) {
            case 21:
            case 31:
            case 19:
            case 25:
				$smarty->assign('AFTERBUY', '<a class="btn btn-default" href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=21', 'NONSSL') . '">Afterbuy</a> ');
				$smarty->assign('SKRILL', '<a class="btn btn-default" href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=31', 'NONSSL') . '">Skrill</a> ');
				$smarty->assign('GOOGLE', '<a class="btn btn-default" href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=19', 'NONSSL') . '">Google Conversion</a> ');
				$smarty->assign('PAYPAL', '<a class="btn btn-default" href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=25', 'NONSSL') . '">PayPal</a> ');
                if ($_GET['gID'] == '31') {
                    echo MB_INFO;
				}
                break;
        }

            $configuration_query = xtc_db_query("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_group_id = '" . (int) $_GET['gID'] . "'  GROUP BY configuration_id ORDER BY sort_order");
            $i = 1;
            while ($configuration = xtc_db_fetch_array($configuration_query)) {
                if ($_GET['gID'] == 6) {
                    switch ($configuration['configuration_key']) {
                        case 'MODULE_PAYMENT_INSTALLED':
                            if ($configuration['configuration_value'] != '') {
                                $payment_installed = explode(';', $configuration['configuration_value']);
                                for ($i = 0, $n = sizeof($payment_installed); $i < $n; $i++) {
                                    include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $payment_installed[$i]);
                                }
                            }
                            break;

                        case 'MODULE_SHIPPING_INSTALLED':
                            if ($configuration['configuration_value'] != '') {
                                $shipping_installed = explode(';', $configuration['configuration_value']);
                                for ($i = 0, $n = sizeof($shipping_installed); $i < $n; $i++) {
                                    include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/' . $shipping_installed[$i]);
                                }
                            }
                            break;

                        case 'MODULE_ORDER_TOTAL_INSTALLED':
                            if ($configuration['configuration_value'] != '') {
                                $ot_installed = explode(';', $configuration['configuration_value']);
                                for ($i = 0, $n = sizeof($ot_installed); $i < $n; $i++) {
                                    include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/' . $ot_installed[$i]);
                                }
                            }
                            break;
                    }
                }
                if (xtc_not_null($configuration['use_function'])) {
                    $use_function = $configuration['use_function'];
                    if (preg_match('/->/', $use_function)) {
                        $class_method = explode('->', $use_function);
                        if (!is_object(${$class_method[0]})) {
                            include(DIR_WS_CLASSES . $class_method[0] . '.php');
                            ${$class_method[0]} = new $class_method[0]();
                        }
                        $cfgValue = xtc_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
                    } else {
                        $cfgValue = xtc_call_function($use_function, $configuration['configuration_value']);
                    }
                } else {
                    $cfgValue = $configuration['configuration_value'];
                }

                if (((!$_GET['cID']) || ($_GET['cID'] == $configuration['configuration_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
                    $cfg_extra = xtc_db_fetch_array(xtc_db_query("select configuration_key,configuration_value, date_added, last_modified, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'"));
                    $cInfo_array = xtc_array_merge($configuration, $cfg_extra);
                    $cInfo = new objectInfo($cInfo_array);
                }
                if ($configuration['set_function']) {
                    eval('$value_field = ' . $configuration['set_function'] . '"' . htmlspecialchars($configuration['configuration_value']) . '");');
                } else {
                    $value_field = xtc_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'], 'size=40');
                }
				
                if (strstr($value_field, 'configuration_value')) {
                    $value_field = str_replace('configuration_value', $configuration['configuration_key'], $value_field);
                }
				
				$configarray[] = array(
					'VALUE' => constant(strtoupper($configuration['configuration_key'] . '_TITLE')),
					'TITLE' => $value_field,
					'HELP' => constant(strtoupper($configuration['configuration_key'] . '_DESC')),
				);
                $i++;
            }
$smarty->assign('configarray', $configarray);

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
$smarty->caching = false;
$smarty->template_dir = DIR_FS_CATALOG . 'admin/templates/';
$smarty->compile_dir = DIR_FS_CATALOG . 'admin/templates_c';
$smarty->display(CURRENT_ADMIN_TEMPLATE . '/configuration.html');
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
