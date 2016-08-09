<?php

/* -----------------------------------------------------------------
 * 	$Id: class.checkout.php 1592 2016-07-12 18:26:21Z akausch $
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

class Checkout_ORIGINAL {

    public function __construct() {
        
    }

    function getDSG($which) {
        switch ($which) {
            case 'text' :
                if (DISPLAY_DATENSCHUTZ_ON_CHECKOUT == 'true') {
                    if (GROUP_CHECK == 'true') {
                        $group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
                    }
                    $shop_content_query = xtc_db_query("SELECT
															content_title,
															content_heading,
															content_text,
															content_file
														FROM
															" . TABLE_CONTENT_MANAGER . "
														WHERE
															content_group='2' " . $group_check . "
														AND
															languages_id='" . $_SESSION['languages_id'] . "'");
                    $shop_content_data = xtc_db_fetch_array($shop_content_query);
                    if ($shop_content_data['content_file'] != '') {
                        if ($shop_content_data['content_file'] == 'janolaw_datenschutz.php') {
                            include_once(DIR_FS_INC . 'janolaw.inc.php');
                            $conditions = JanolawContent('datenschutzerklaerung', 'txt');
                        } elseif ($shop_content_data['content_file'] == 'protected_shops_datenschutz.php') {
                            $conditions = '<div class="agbframe">' . file_get_contents(DIR_FS_DOCUMENT_ROOT . 'media/content/ps_datenschutz.html') . '</div>';
                        } else {
                            $conditions = '<div class="agbframe">' . file_get_contents(DIR_FS_DOCUMENT_ROOT . 'media/content/' . $shop_content_data['content_file']) . '</div>';
                        }
                    } else {
                        $conditions = '<div class="agbframe">' . $shop_content_data['content_text'] . '</div>';
                    }
                    return $conditions;
                }
                break;


            case 'link' :
                if (DISPLAY_DATENSCHUTZ_ON_CHECKOUT == 'true') {
                    return xtc_href_link('shop_content.php', 'coID=2');
                }
                break;


            case 'checkbox' :
                if (DISPLAY_DATENSCHUTZ_ON_CHECKOUT == 'true') {
                    return '<input type="checkbox" value="dsg" name="dsg" id="dsg" class="radiobox" onclick="hitDSG()" />';
                }
                break;


            case 'stat' :
                if (DISPLAY_DATENSCHUTZ_ON_CHECKOUT == 'true') {
                    return 1;
                } else {
                    return 0;
                }
                break;
        }
    }

    function getAGB($which) {
        switch ($which) {
            case 'text' :
                if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
                    if (GROUP_CHECK == 'true') {
                        $group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
                    }
                    $shop_content_query = xtc_db_query("SELECT
																content_title,
																content_heading,
																content_text,
																content_file
																FROM " . TABLE_CONTENT_MANAGER . "
																WHERE content_group='3' " . $group_check . "
																AND languages_id='" . $_SESSION['languages_id'] . "'");
                    $shop_content_data = xtc_db_fetch_array($shop_content_query);
                    if ($shop_content_data['content_file'] != '') {
                        if ($shop_content_data['content_file'] == 'janolaw_agb.php') {
                            include_once(DIR_FS_INC . 'janolaw.inc.php');
                            $conditions = JanolawContent('agb', 'txt');
                        } elseif ($shop_content_data['content_file'] == 'protected_shops_agb.php') {
                            $conditions = '<div class="agbframe">' . file_get_contents(DIR_FS_DOCUMENT_ROOT . 'media/content/ps_agb.html') . '</div>';
                        } else {
                            $conditions = '<div class="agbframe">' . file_get_contents(DIR_FS_DOCUMENT_ROOT . 'media/content/' . $shop_content_data['content_file']) . '</div>';
                        }
                    } else {
                        $conditions = '<div class="agbframe">' . $shop_content_data['content_text'] . '</div>';
                    }
                    return $conditions;
                }
                break;


            case 'link' :
                if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
                    return xtc_href_link('shop_content.php', 'coID=3');
                }
                break;


            case 'checkbox' :
                if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
                    return '<input type="checkbox" value="conditions" name="conditions" id="conditions" class="radiobox" onclick="hitAGB()"  />';
                }
                break;


            case 'stat' :
                if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
                    return 1;
                } else {
                    return 0;
                }
                break;
        }
    }

    function getSZI($which) {
        switch ($which) {
            case 'text' :
                if (CHECKOUT_SHOW_SHIPPING == 'true') {
                    if (GROUP_CHECK == 'true') {
                        $group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
                    }
                    $shop_content_query = xtc_db_query("SELECT
																content_title,
																content_heading,
																content_text,
																content_file
																FROM " . TABLE_CONTENT_MANAGER . "
																WHERE content_group='" . CHECKOUT_SHOW_SHIPPING_ID . "' " . $group_check . "
																AND languages_id='" . $_SESSION['languages_id'] . "'");
                    $shop_content_data = xtc_db_fetch_array($shop_content_query);
                    $conditions = $shop_content_data['content_text'];
                    return $conditions;
                }
                break;
        }
    }

    function getFormUrl() {
        if (isset($GLOBALS[$_SESSION['payment']]->form_action_url) && !$GLOBALS[$_SESSION['payment']]->tmpOrders) {
            $form_action_url = $GLOBALS[$_SESSION['payment']]->form_action_url;
        } else {
            $form_action_url = xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
        }
        $form_action_url = utf8_encode($form_action_url);
        return $form_action_url;
    }

    function getRevocation($which) {
        switch ($which) {
            case 'text' :
                if (DISPLAY_REVOCATION_ON_CHECKOUT == 'true') {
                    if (GROUP_CHECK == 'true') {
                        $group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
                    }
                    $shop_content_query = "SELECT
                                                content_title,
                                                content_heading,
                                                content_text,
                                                content_file
		                                   FROM
		                                   		" . TABLE_CONTENT_MANAGER . "
		                                   WHERE
		                                   		content_group='" . REVOCATION_ID . "' " . $group_check . "
		                                   AND
		                                   		languages_id='" . $_SESSION['languages_id'] . "'";

                    $shop_content_query = xtc_db_query($shop_content_query);
                    $shop_content_data = xtc_db_fetch_array($shop_content_query);

                    if ($shop_content_data['content_file'] != '') {
                        if ($shop_content_data['content_file'] == 'janolaw_widerruf.php') {
                            include_once(DIR_FS_INC . 'janolaw.inc.php');
                            $conditions = JanolawContent('widerrufsbelehrung', 'txt');
                        } elseif ($shop_content_data['content_file'] == 'protected_shops_widerruf.php') {
                            $conditions = '<div class="agbframe">' . file_get_contents(DIR_FS_DOCUMENT_ROOT . 'media/content/ps_widerruf.html') . '</div>';
                        } else {
                            $conditions = '<div class="agbframe">' . file_get_contents(DIR_FS_DOCUMENT_ROOT . 'media/content/' . $shop_content_data['content_file']) . '</div>';
                        }
                    } else {
                        $conditions = '<div class="agbframe">' . $shop_content_data['content_text'] . '</div>';
                    }
                    return $conditions;
                }
                break;


            case 'link' :
                if (DISPLAY_REVOCATION_ON_CHECKOUT == 'true') {
                    return xtc_href_link('shop_content.php', 'coID=' . REVOCATION_ID);
                }
                break;

            case 'checkbox' :
                if (DISPLAY_REVOCATION_ON_CHECKOUT == 'true') {
                    return '<input type="checkbox" value="revocation" name="revocation" id="revocation" class="radiobox" onclick="hitRevocation()" />';
                }
                break;

            case 'stat' :
                if (DISPLAY_REVOCATION_ON_CHECKOUT == 'true') {
                    return 1;
                } else {
                    return 2;
                }
                break;
        }
    }

    function getRevocationDownload($which) {
        switch ($which) {
            case 'checkbox' :
                if (DISPLAY_REVOCATION_ON_CHECKOUT == 'true') {
                    return '<input type="checkbox" value="revocationdownload" name="revocationdownload" id="revocationdownload" class="radiobox" onclick="hitRevocationDownload()" />';
                }
                break;
        }
    }

    function getRevocationService($which) {
        switch ($which) {
            case 'checkbox' :
                if (DISPLAY_REVOCATION_ON_CHECKOUT == 'true') {
                    return '<input type="checkbox" value="revocationservice" name="revocationservice" id="revocationservice" class="radiobox" onclick="hitRevocationService()" />';
                }
                break;
        }
    }

    function getIp() {
        if (SHOW_IP_LOG == 'true') {
            if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
                $customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else {
                $customers_ip = $_SERVER["REMOTE_ADDR"];
            }
            return $customers_ip;
        } else {
            return '';
        }
    }

    function getProducts() {
        global $order;
        global $xtPrice;
        $module_smarty = new Smarty;
        $data_products = array();

        for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
            $html_update_qty = '';
            if (CHECKOUT_AJAX_PRODUCTS == 'true') {
                $html_update_qty = '<a href="javascript:void(0);" onclick="updateProducts(\'' . $order->products[$i]['id'] . '\',\'minus\');">[-]</a> 
									<a href="javascript:void(0);" onclick="updateProducts(\'' . $order->products[$i]['id'] . '\',\'plus\');">[+]</a> 
									<a href="javascript:void(0);" onclick="removeProduct(\'' . $order->products[$i]['id'] . '\',\'minus\');"><span style="color:#CC0000">[x]</span> </a>';
            }
            $img = xtc_db_fetch_array(xtc_db_query("SELECT products_image FROM products WHERE products_id = '" . $order->products[$i]['id'] . "'"));
            if ($img['products_image'] != '') {
                $products_image = xtc_image(DIR_WS_MINI_IMAGES . $img['products_image'], $order->products[$i]['name']);
            } else {
                $products_image = xtc_image(DIR_WS_MINI_IMAGES . 'no_img.jpg', $order->products[$i]['name']);
            }

            $attribut_array = array();
            if (((isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0)) || ((isset($order->products[$i]['freitext'])) && (sizeof($order->products[$i]['freitext']) > 0))) {
                for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j++) {
                    $attribut_value = '';
                    $attribut_name = '';
                    $attribut_edit = '';
                    $attribut_hidden = '';
                    $attribut_products_shipping = '';
                    $attribut_name = $order->products[$i]['attributes'][$j]['option'];
                    $attribut_value = $order->products[$i]['attributes'][$j]['value'];
                    if (CHECKOUT_AJAX_PRODUCTS == 'true') {
                        $attribut_edit = '<a class="fl mr10" href="javascript:void(0);" onclick="showSelect(\'' . $i . $j . '\')"><img src="images/icons/pencil--arrow.png" style="vertical-align:middle;" alt="" /></a> ';
                        $attribut_hidden = $this->getAllPossibleAttributes($order->products[$i]['id'], $order->products[$i]['attributes'][$j]['option_id'], $order->products[$i]['attributes'][$j]['value'], $i . $j);
                    }


                    if ($order->products[$i]['attributes'][$j]['attributes_shippingtime'] > 0) {
                        $main = new main($order->products[$i]['id']);
                        $attribut_products_shipping = $main->getShippingStatusName($order->products[$i]['attributes'][$j]['attributes_shippingtime']) . $main->getShippingStatusInfoLinkActive($order->products[$i]['attributes'][$j]['attributes_shippingtime']);
                    } else {
                        $main = new main($order->products[$i]['id']);
                        $pshipping_time = xtc_db_fetch_array(xtc_db_query("SELECT products_shippingtime FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $order->products[$i]['id'] . "';"));
                        $attribut_products_shipping = $order->products[$i]['shipping_time'] . $main->getShippingStatusInfoLinkActive($pshipping_time['products_shippingtime']);
                    }

                    if ($order->products[$i]['attributes'][$j]['products_options_name'] != 'Downloads') {
                        $price_sum = $xtPrice->xtcFormat($order->products[$i]['attributes'][$j]['price'], true, $order->products[$i]['tax_class_id']);
                    } else {
                        $price_sum = '';
                    }

                    $attribut_array[] = array('ATTRIBUTE_ID' => $i . $j,
                        'NAME' => $attribut_name,
                        'VALUE_NAME' => $attribut_value,
                        'ATTRIBUTE_EDIT' => $attribut_edit,
                        'ATTRIBUTE_SHIPPING' => $attribut_products_shipping,
                        'PRICE' => $price_sum,
                        'PREFIX' => ($order->products[$i]['attributes'][$j]['products_options_name'] != 'Downloads') ? $order->products[$i]['attributes'][$j]['prefix'] : '',
                        'ATTRIBUTE_HIDDEN' => $attribut_hidden);
                }
                for ($j = 0, $n2 = sizeof($order->products[$i]['freitext']); $j < $n2; $j++) {
                    $attribut_value = '';
                    $attribut_name = '';
                    $attribut_edit = '';
                    $attribut_hidden = '';
                    $attribut_products_shipping = '';

                    $attribut_name = $order->products[$i]['freitext'][$j]['option'];
                    $attribut_value = $order->products[$i]['freitext'][$j]['value_id'];
                    if (CHECKOUT_AJAX_PRODUCTS == 'true') {
                        $attribut_edit = '<a class="fl mr10" href="javascript:void(0);" onclick="showSelect(\'' . $i . $j . '\')"><img src="images/icons/pencil--arrow.png" style="vertical-align:middle;" alt="" /></a> ';
                        $attribut_hidden = $this->getAllPossibleAttributes($order->products[$i]['id'], $order->products[$i]['freitext'][$j]['option_id'], $order->products[$i]['freitext'][$j]['value_id'], $i . $j);
                    }


                    if ($order->products[$i]['attributes'][$j]['attributes_shippingtime'] > 0) {
                        $main = new main($order->products[$i]['id']);
                        $attribut_products_shipping = $main->getShippingStatusName($order->products[$i]['attributes'][$j]['attributes_shippingtime']) . $main->getShippingStatusInfoLinkActive($order->products[$i]['attributes'][$j]['attributes_shippingtime']);
                    } else {
                        $main = new main($order->products[$i]['id']);
                        $pshipping_time = xtc_db_fetch_array(xtc_db_query("SELECT products_shippingtime FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $order->products[$i]['id'] . "';"));
                        $attribut_products_shipping = $order->products[$i]['shipping_time'] . $main->getShippingStatusInfoLinkActive($pshipping_time['products_shippingtime']);
                    }
                    if ($order->products[$i]['attributes'][$j]['products_options_name'] != 'Downloads') {
                        $price_sum = $xtPrice->xtcFormat($order->products[$i]['attributes'][$j]['price'], true, $order->products[$i]['tax_class_id']);
                    } else {
                        $price_sum = '';
                    }

                    $attribut_array[] = array('ATTRIBUTE_ID' => $i . $j,
                        'NAME' => $attribut_name,
                        'VALUE_NAME' => $attribut_value,
                        'ATTRIBUTE_EDIT' => $attribut_edit,
                        'ATTRIBUTE_SHIPPING' => $attribut_products_shipping,
                        'PRICE' => $price_sum,
                        'PREFIX' => ($order->products[$i]['attributes'][$j]['products_options_name'] != 'Downloads') ? $order->products[$i]['attributes'][$j]['prefix'] : '',
                        'ATTRIBUTE_HIDDEN' => $attribut_hidden);
                }
            } else {
                $main = new main($order->products[$i]['id']);
                $pshipping_time = xtc_db_fetch_array(xtc_db_query("SELECT products_shippingtime FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $order->products[$i]['id'] . "';"));
                $attribut_products_shipping = $order->products[$i]['shipping_time'] . $main->getShippingStatusInfoLinkActive($pshipping_time['products_shippingtime']);
            }
            if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
                require_once (DIR_FS_INC . 'xtc_display_tax_value.inc.php');
                $zzglmwst = sprintf(TAX_INFO_ADD, xtc_display_tax_value($order->products[$i]['tax']) . ' %');
            } else {
                require_once (DIR_FS_INC . 'xtc_display_tax_value.inc.php');
                $zzglmwst = sprintf(TAX_INFO_INCL, xtc_display_tax_value($order->products[$i]['tax']) . ' %');
            }

            if (DISPLAY_TAX == 'false') {
                $zzglmwst = '';
            }
            $request_type = (getenv('HTTPS') == '1' || getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';
            $cechkout_products_array[] = array('PRODUCTS_ID' => $order->products[$i]['id'],
                'PRODUCTS_IMAGE' => $products_image,
                'PRODUCTS_NAME' => $order->products[$i]['name'],
                'PRODUCTS_LINK' => (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . 'checkout_product_info.php?products_id=' . $order->products[$i]['id'],
                'PRODUCTS_SHORT_DESCRIPTION' => ($order->products[$i]['products_short_description'] != '' ? strip_tags($order->products[$i]['products_short_description']) . '<br>' : ''),
                'PRODUCTS_SHIPPING_TIME' => (ACTIVATE_SHIPPING_STATUS == 'true' && $order->products[$i]['shipping_time'] == $attribut_products_shipping ? $order->products[$i]['shipping_time'] : $attribut_products_shipping),
                'ATTRIBUTES' => $attribut_array,
                'PRODUCTS_QTY' => '<span id="qty_' . $order->products[$i]['id'] . '">' . $order->products[$i]['qty'] . ' x</span>',
                'PRODUCTS_UPDATE' => $html_update_qty,
                'PRODUCTS_SINGLE_PRICE' => $xtPrice->xtcFormat($order->products[$i]['price'], true).'<br>'.$order->products[$i]['vpe'],
                'PRODUCTS_PRICE' => $xtPrice->xtcFormat($order->products[$i]['final_price'], true),
                'PRODUCTS_MWST' => $zzglmwst);
        }
        require_once(DIR_FS_INC . 'specials_gratis_active.inc.php');
        $gratis_gratis = array();
        $gratis_gratis = getspecial_gratis_active();
        if (is_array($gratis_gratis)) {
            $_SESSION['gratis_artikel'] = getspecial_gratis_active();
        }
        $module_smarty->assign('gratis_art', $gratiscount);
        $module_smarty->assign('specials_gratis_new_products_price', $xtPrice->xtcFormat($gratis_gratis[specials_gratis_new_products_price], true));
        unset($_SESSION['gratisartikel']);
        if (is_array($gratis_gratis)) {
            $module_smarty->assign('gratis_gratis', $gratis_gratis);
        }
        $module_smarty->assign('module_content', $cechkout_products_array);
        $module_smarty->caching = false;
        $return_products = $module_smarty->fetch(cseo_get_usermod('base/module/order_details_checkout.html', USE_TEMPLATE_DEVMODE));
        return $return_products;
    }

    function getAllPossibleAttributes($pid, $oid, $selected, $aid) {
        global $xtPrice;
        $dropdown = '<select id="att_' . $pid . $oid . '" name="att_' . $pid . $oid . '" size="1" onchange="updateAttributes(this.id, \'' . $aid . '\')">';
        $query1 = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id = '" . $pid . "' AND options_id = '" . $oid . "'");
        $product_info = xtc_db_fetch_array(xtDBquery("SELECT * FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . (int) $pid . "';"));

        if (xtc_db_num_rows($query1) <= 1) {
            // Only one Attribut
            $query1_array = xtc_db_fetch_array($query1);
            $query2_array = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " WHERE language_id = '" . $_SESSION['languages_id'] . "' AND products_options_values_id = '" . $query1_array['options_values_id'] . "';"));
            return $query2_array['products_options_values_name'];
        } else {
            // More than one attribute
            while ($query1_array = xtc_db_fetch_array($query1)) {
                $query2_array = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " WHERE language_id = '" . $_SESSION['languages_id'] . "' AND products_options_values_id = '" . $query1_array['options_values_id'] . "';"));
                // Check if selected
                $html_selected = '';
                if (html_entity_decode($query2_array['products_options_values_name']) == html_entity_decode($selected)) {
                    $html_selected = ' selected';
                }
                $oprice = '';
                if ($query1_array['options_values_price'] > 0) {
                    $oprice = ' ' . $query1_array['price_prefix'] . ' ' . $xtPrice->xtcFormat($query1_array['options_values_price'], true, $product_info['products_tax_class_id'], true);
                }

                $dropdown .= '<option value="' . $pid . '|' . $oid . '|' . $query2_array['products_options_values_id'] . '"' . $html_selected . '>' . $query2_array['products_options_values_name'] . $oprice . '</option>';
            }
        }
        $dropdown .= '</select>';
        return $dropdown;
    }

    function getStat($option) {
        $option = strtoupper($option);
        $query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'CHECKOUT_SHOW_" . $option . "'");
        $query_array = xtc_db_fetch_array($query);
        if ($query_array['configuration_value'] == 'true')
            return 1;
        else
            return 0;
    }

    function getAddresses($which) {
        if ($which == 'shipping')
            $sess = 'sendto';
        elseif ($which == 'payment')
            $sess = 'billto';

        $dropdown = '<select id="select_shipping_address" name="select_shipping_address" size="1" onchange="updateAddressBySelect(\'' . $which . '\', this.value);">';
        $addresses_query = xtc_db_query("select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $_SESSION['customer_id'] . "'");
        while ($addresses = xtc_db_fetch_array($addresses_query)) {
            $format_id = xtc_get_address_format_id($address['country_id']);
            $selected = '';
            if ($addresses['address_book_id'] == $_SESSION[$sess]) {
                $selected = ' selected';
            }
            $short_address = xtc_address_format($format_id, $addresses, true, ' ', ', ');
            $strpos = strpos($short_address, ',', 25);
            if ($strpos != false) {
                $short_address = substr($short_address, 0, $strpos + 1) . ' ...';
            }

            $dropdown .= '<option value="' . $addresses['address_book_id'] . '"' . $selected . '>' . $short_address . '</option>';
        }
        $dropdown .= '</select>';
        return $dropdown;
    }

    function getEditAddressButton($which) {
        return '<a href="javascript:void(0);" onclick="editAddress(\'' . $which . '\')">' . xtc_image_button('button_checkout_edit.gif', IMAGE_BUTTON_EDIT) . '</a>';
    }

    function getSaveAddressButton($which) {
        return '<a href="javascript:void(0);" onclick="updateAddress(\'' . $which . '\')">' . xtc_image_button('button_checkout_save.gif', IMAGE_BUTTON_SAVE) . '</a>';
    }

    function getBackButton($which) {
        return '<a href="javascript:void(0);" onclick="cancelAddress(\'' . $which . '\')">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>';
    }

    function getSaveModuleButton($which) {
        if ($which == 'payment') {
            return '<a href="javascript:void(0);" onclick="updatePaymentModule()">' . xtc_image_button('button_checkout_save.gif', IMAGE_BUTTON_SAVE, 'id="button_save_payment"') . '</a>';
        } elseif ($which == 'shipping') {
            return '<a href="javascript:void(0);" onclick="updateShippingModule()">' . xtc_image_button('button_checkout_save.gif', IMAGE_BUTTON_SAVE, 'id="button_save_shipping"') . '</a>';
        }
    }

    function newAddressPossible() {
        $addresses_query = xtc_db_query("select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $_SESSION['customer_id'] . "'");
        $count = xtc_db_num_rows($addresses_query);
        if ($count >= MAX_ADDRESS_BOOK_ENTRIES) {
            return 0;
        } else {
            return 1;
        }
    }

    function isVirtual() {
        global $order;
        $virtual = 0;
        if ($order->content_type == 'virtual' || ($order->content_type == 'virtual_weight') || ($_SESSION['cart']->count_contents_virtual() == 0)) {
            unset($_SESSION['shipping']);
            #unset($_SESSION['payment']);
            $virtual = 1;
        }
        return $virtual;
    }

    function getStates($country_id) {
        if (ACCOUNT_STATE == 'true') {
            $zone_id = 0;
            $check_query = xtc_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . $country_id . "'");
            $check = xtc_db_fetch_array($check_query);
            $entry_state_has_zones = ($check['total'] > 0);
            if ($entry_state_has_zones) {
                $dropdown = '<select size="1">';
                $dropdown .= '</select>';
            }
        } else {
            return false;
        }
    }

    function convertToVars($query_string) {
        $vars = array();
        if (strpos($query_string, '&') === false) {
            $string = str_replace('"', '', stripslashes($query_string));
            $string = str_replace('{', '', $string);
            $string = str_replace('}', '', $string);

            $exploded = explode(',', $string);

            for ($i = 0; sizeof($exploded) > $i; $i++) {
                $trennen = explode(':', $exploded[$i]);
                if ($trennen[0] != '' && $trennen[1] != '')
                    $vars[$trennen[0]] = $trennen[1];
            }
        } else {
            if (!is_array($query_string)) {
                $arr_vars = explode('&', $query_string);
                foreach ($arr_vars as $value) {
                    $arr_vars2 = explode('=', $value);
                    $vars[$arr_vars2[0]] = utf8_decode(urldecode($arr_vars2[1]));
                }
            } else {
                reset($query_string);
                while (list($key, $value) = each($query_string))
                    $vars[$key] = utf8_decode($value);
            }
        }
        return $vars;
    }

    function isFreeShipping($order, $xtPrice) {
        if (defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true')) {
            switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
                case 'national' :
                    if ($order->delivery['country_id'] == STORE_COUNTRY)
                        $pass = true;
                    break;
                case 'international' :
                    if ($order->delivery['country_id'] != STORE_COUNTRY)
                        $pass = true;
                    break;
                case 'both' :
                    $pass = true;
                    break;
                default :
                    $pass = false;
                    break;
            }

            $free_shipping = false;
            if (($pass == true) && ($order->info['total'] - $order->info['shipping_cost'] >= $xtPrice->xtcFormat(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, false, 0, true))) {
                $free_shipping = true;
            }
        } else {
            $free_shipping = false;
        }


// free shipping start - new code
if ((STORE_COUNTRY == $order->delivery['country']['id'] && FREE_SHIPPING_LOCAL_ONLY == 'true') || FREE_SHIPPING_LOCAL_ONLY == 'false') {
        $free_shipping_products_query = xtDBquery("SELECT products_id, max_free_shipping_amount FROM " . TABLE_PRODUCTS . " WHERE free_shipping ='1';");
        $free_amount = true;
        $free_contents = 0;
        while ($free_shipping_products = xtc_db_fetch_array($free_shipping_products_query)) {
            $products_id_fs = $_SESSION['cart']->in_cart_fs($free_shipping_products['products_id']);
            if ($products_id_fs) {
                $free_contents += $_SESSION['cart']->get_quantity_fs($products_id_fs);
                if (($free_shipping_products['max_free_shipping_amount'] > 0) && ($_SESSION['cart']->get_quantity_fs($products_id_fs) > $free_shipping_products['max_free_shipping_amount'])) {
                    $free_amount = false;
                }
            }
        }
        if (($free_contents > 0) && ($free_contents == $_SESSION['cart']->count_contents()) && ($free_amount == true)) {
            $free_shipping = true;
        }
        }
        // free shipping - end of code	
        return $free_shipping;
    }

    function getTaxID($id) {
        $cart_products = $_SESSION['cart']->get_products();
        for ($i = 0; $i < count($cart_products); $i++) {
            if ($cart_products[$i]['id'] == $id) {
                $tax_class_id = $cart_products[$i]['tax_class_id'];
                break;
            }
        }
        return $tax_class_id;
    }

    function getShippingBlock($xtPrice) {
        global $shipping_modules;
        global $order;
        global $total_weight;
        global $total_count;
        global $shipping_compatible;

        if (!is_object($shipping_modules)) {
            $shipping_modules = new shipping;
            $free_shipping = $this->isFreeShipping($order, $xtPrice);
            if ($free_shipping)
                include (DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/order_total/ot_shipping.php');
        }
        $quotes = $shipping_modules->quote();

        // SHIPPING STUFF

        $module_smarty = new Smarty;
        if (xtc_count_shipping_modules() > 0) {
            $showtax = $_SESSION['customers_status']['customers_status_show_price_tax'];

            $radio_buttons = 0;
            //Beginn von Modul Versandsperre
            for ($i = 0, $n = count($order->products); $i < $n; $i++) {
                $id = $order->products[$i]['id'];
                $forbidden_shipping_query = xtc_db_query("SELECT products_forbidden_shipping FROM " . TABLE_PRODUCTS . " WHERE products_id='$id' ");
                if ($i == '0') {
                    $forbidden_shipping_data = xtc_db_fetch_array($forbidden_shipping_query);
                } else {
                    $puffer = xtc_db_fetch_array($forbidden_shipping_query);
                    if ($puffer['products_forbidden_shipping'] != '') {
                        $forbidden_shipping_data['products_forbidden_shipping'] .= "|";
                        $forbidden_shipping_data['products_forbidden_shipping'] .= $puffer['products_forbidden_shipping'];
                    }
                }
            }
            $forbidden_shipping_data = explode("|", $forbidden_shipping_data['products_forbidden_shipping']);
            $n = sizeof($quotes);
            foreach ($forbidden_shipping_data AS $forbidden_shipping) {
                for ($i = 0; $i <= $n; $i++) {
                    $name = explode('.', $forbidden_shipping);
                    if ($quotes[$i]['id'] == $name[0]) {
                        unset($quotes[$i]);
                    }
                }
            }

            //Ende von Modul Versandsperre
            #loop through installed shipping methods...
            $something_checked = false;

            // for ($i = 0, $n = sizeof($quotes); $i < $n; $i++) {
            for ($i = 0; $i < $n; $i ++) {
                if (!isset($quotes[$i]['error'])) {
                    for ($j = 0, $n2 = sizeof($quotes[$i]['methods']); $j < $n2; $j++) {
                        # set the radio button to be checked if it is the method chosen
                        $quotes[$i]['methods'][$j]['radio_buttons'] = $radio_buttons;
                        $checked = (($quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $_SESSION['shipping']['id'] || $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $_SESSION['shipping']) ? true : false);
                        if (($checked == true) || ($n == 1 && $n2 == 1)) {
                            $quotes[$i]['methods'][$j]['checked'] = 1;
                            $something_checked = true;
                        }

                        if (($n > 1) || ($n2 > 1)) {
                            if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
                                $quotes[$i]['tax'] = '';
                            if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
                                $quotes[$i]['tax'] = 0;

                            $quotes[$i]['methods'][$j]['price'] = $xtPrice->xtcFormat(xtc_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax']), true, 0, true);
                            $quotes[$i]['methods'][$j]['radio_field'] = xtc_draw_radio_field('shipping', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'], $checked, 'onclick="if (this.checked) { shippingShowOrHide(\'smodule' . $radio_buttons . '\'); this.checked=true; return true; }"') . xtc_draw_hidden_field($quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] . '_num', $radio_buttons + 1);
                            $quotes[$i]['methods'][$j]['value_id'] = $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'];
                        } else {
                            if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0) {
                                $quotes[$i]['tax'] = 0;
                            }
                            if ((isset($quotes[$i]['methods'][$j]['title'])) && (isset($quotes[$i]['methods'][$j]['cost']))) {
                                $_SESSION['shipping'] = array('id' => $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'], 'title' => (($this->isFreeShipping($order, $xtPrice) == true) ? $quotes[$i]['methods'][$j]['title'] : $quotes[$i]['module'] . (($quotes[$i]['methods'][$j]['title'] == '') ? '' : ' (') . $quotes[$i]['methods'][$j]['title'] . (($quotes[$i]['methods'][$j]['title'] == '') ? '' : ')')), 'cost' => $quotes[$i]['methods'][$j]['cost']);
                            }
                            $quotes[$i]['methods'][$j]['value_id'] = $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'];
                            $quotes[$i]['methods'][$j]['price'] = $xtPrice->xtcFormat(xtc_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax']), true, 0, true) . xtc_draw_hidden_field('shipping', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id']) . xtc_draw_hidden_field($quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] . '_num', $radio_buttons + 1);
                        }
                        $radio_buttons++;
                    }
                }
            }

            if ($something_checked == false && !empty($_SESSION['shipping']['id'])) {
                unset($_SESSION['shipping']);
                $shipping_compatible = false;
            }
            $module_smarty->assign('module_content', $quotes);
            $module_smarty->assign('module_choose', CHECKOUT_SHIPPING_CHOOSE);
            $module_smarty->caching = false;
            $shipping_block = $module_smarty->fetch(cseo_get_usermod('base/module/checkout_shipping_block.html', USE_TEMPLATE_DEVMODE));

            return $shipping_block;
        }
    }

    function getPaymentBlock($xtPrice, $payment_modules) {
        global $payment_compatible;
        global $order;
        $module_smarty = new Smarty;

        $something_checked = false;
        $selection = $payment_modules->selection();
        for ($i = 0, $n = count($order->products); $i < $n; $i++) {
            $pID = xtc_get_prid($order->products[$i]['id']);
            $sperre_query = xtc_db_query("SELECT products_forbidden_payment FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $pID . "';");

            if (xtc_db_num_rows($sperre_query)) {
                $sperre_values = xtc_db_fetch_array($sperre_query);
                if (!empty($sperre_values['products_forbidden_payment'])) {
                    if (strpos($sperre_values['products_forbidden_payment'], '|')) {
                        $value = explode('|', $sperre_values['products_forbidden_payment']);
                        for ($v = 0; $v <= sizeof($value); $v++)
                            $tmp[] = $value[$v];
                    } else
                        $tmp[] = $sperre_values['products_forbidden_payment'];
                }
            }
        }

        $n = sizeof($selection);
        if (is_array($tmp)) {
            foreach ($tmp AS $paymentmodule => $value) {
                for ($i = 0; $i <= $n; $i++) {
                    $name = explode('.', $value);
                    if ($selection[$i]['id'] == $name[0]) {
                        unset($selection[$i]);
                    }
                }
            }
        }
        $radio_buttons = 0;
        for ($i = 0; $i < $n; $i++) {
            $selection[$i]['radio_buttons'] = $radio_buttons;
            if (($selection[$i]['id'] == $_SESSION['payment']) || ($n == 1)) {
                $selection[$i]['checked'] = 1;
                $something_checked = true;
            }
            if (empty($selection[$i]['module_cost']) && isset($GLOBALS[$selection[$i]['id']]->cost)) {
                $selection[$i]['module_cost'] = CHECKOUT_PAYMENT_DUE;
            }

            if (sizeof($selection) > 1) {
                //Radio Buttons zusammenbauen Payment
                $selection[$i]['selection'] = xtc_draw_radio_field('payment', $selection[$i]['id'], ($selection[$i]['id'] == $_SESSION['payment']), 'data-ajax="false" id="' . $selection[$i]['id'] . '" onclick="if (this.checked) { paymentShowOrHide(\'pmodule_' . $selection[$i]['id'] . '\'); this.checked=true; return true; }"') . xtc_draw_hidden_field($selection[$i]['id'] . '_num', $i + 1);
                $selection[$i]['label'] = $selection[$i]['id'];
            } else {
                $only_one = 1;
                $selection[$i]['selection'] = xtc_draw_hidden_field('payment', $selection[$i]['id']) . xtc_draw_hidden_field($selection[$i]['id'] . '_num', $i + 1);

                if (!is_array($selection[$i]['fields']) && !isset($selection[$i]['fields'])) {
                    if ($_SESSION['payment'] != 'no_payment') {
                        $_SESSION['payment'] = $selection[$i]['id'];
                    }
                } else {
                    unset($_SESSION['payment']);
                    $something_checked = false;
                    $selection[$i]['checked'] = 0;
                }
            }
            $selection[$i]['value_id'] = $selection[$i]['id'];

            if (isset($selection[$i]['error'])) {
                
            } else {
                $radio_buttons++;
            }
        }

        if ($something_checked == false && !empty($_SESSION['payment'])) {
            unset($_SESSION['payment']);
            $payment_compatible = false;
        }

        $module_smarty->assign('module_content', $selection);
        $module_smarty->assign('module_choose', CHECKOUT_PAYMENT_CHOOSE);
        $module_smarty->assign('xajax', 1);
        $module_smarty->assign('only_one', $only_one);
        $module_smarty->caching = false;
        $payment_block = $module_smarty->fetch(cseo_get_usermod('base/module/checkout_payment_block.html', USE_TEMPLATE_DEVMODE));

        return $payment_block;
    }

    function getTotalBlock($order, $order_total_modules, $xtPrice) {
        if (MODULE_ORDER_TOTAL_INSTALLED)
            $total_block = $order_total_modules->output();
        return $total_block;
    }

    function getSorting() {
        $row = xtc_db_fetch_array(xtc_db_query("SELECT configuration_value FROM configuration WHERE configuration_key='CHECKOUT_BOX_ORDER' LIMIT 1"));
        $sorting = explode('|', $row['configuration_value']);
        return $sorting;
    }

}
