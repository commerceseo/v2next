<?php

/* -----------------------------------------------------------------
 * 	$Id: class.commerzfinanz.php 795 2014-01-09 14:38:23Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	xt:Commerce v 3.x PostFinance  Zahlungs-Modul by customweb GmbH
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

class commerzfinanz {

    var $code = NULL;
    var $title = NULL;
    var $description = false;
    var $enabled = false;
    var $paymentMethod = NULL;
    var $paymentMethodBrand = NULL;
    var $arrCurrencies = NULL;

    function commerzfinanz() {
        global $order;
        $this->upperCode = strtoupper($this->code);
        $this->sort_order = @constant('MODULE_PAYMENT_' . $this->upperCode . '_SORT_ORDER');
        $this->zone = @constant('MODULE_PAYMENT_' . $this->upperCode . '_ZONE');
        $this->immediate = @constant('MODULE_PAYMENT_' . $this->upperCode . '_IMMEDIATE');
        $this->enabled = @constant('MODULE_PAYMENT_' . $this->upperCode . '_STATUS') == 'True' ? true : false;

        if ($this->backendCheck()) {
            $this->title = @constant('MODULE_PAYMENT_' . $this->upperCode . '_TEXT_TITLE_ADMIN');
        } else {
            $this->title = @constant('MODULE_PAYMENT_' . $this->upperCode . '_TEXT_TITLE');
        }
        $this->description = @constant('MODULE_PAYMENT_' . $this->upperCode . '_TEXT_DESCRIPTION');

        if ((int) @constant('MODULE_PAYMENT_' . $this->upperCode . '_ORDER_STATUS_ID') > 0) {
            $this->order_status = constant('MODULE_PAYMENT_' . $this->upperCode . '_ORDER_STATUS_ID');
        }
        if ((int) @constant('MODULE_PAYMENT_' . $this->upperCode . '_TMPORDER_STATUS_ID') > 0) {
            $this->order_status = constant('MODULE_PAYMENT_' . $this->upperCode . '_TMPORDER_STATUS_ID');
        }

        // Change order status
        if (is_object($order)) {
            $this->update_status();
        }
    }

    function currencyCheck() {
        if (in_array($_SESSION['currency'], $this->arrCurrencies) && @constant('MODULE_PAYMENT_' . $this->upperCode . '_' . $_SESSION['currency']) == 'True') {
            return true;
        } else {
            return false;
        }
    }

    function backendCheck() {
        global $order;
        if (preg_match('/.*\/modules.*/i', $_SERVER['REQUEST_URI'])) {
            return true;
        } else {
            return false;
        }
    }

    function findeModules() {
        $query = xtc_db_query("SELECT configuration_value, configuration_key FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE_PAYMENT_COMMERZFINANZ_%_STATUS'");
        while ($row = xtc_db_fetch_array($query)) {
            if ($row['configuration_key'] != 'MODULE_PAYMENT_COMMERZFINANZ_BASIC_STATUS' && $row['configuration_value'] == 'True') {
                return true;
            }
        }
        return false;
    }

    function update_status() {
        global $order;

        if ($this->enabled == true && $this->code == 'commerzfinanz_basic' && $this->findeModules() != false) {
            $this->enabled = false;
        }

        if ($this->enabled == true && MODULE_PAYMENT_COMMERZFINANZ_BASIC_STATUS == 'False') {
            $this->enabled = false;
        }

        if (($this->enabled == true) && ((int) $this->zone > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . $this->zone . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
            while ($check = xtc_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }

            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    function javascript_validation() {
        return false;
    }

    function selection() {
        global $xtPrice, $order;

        $_SESSION['customerIsRedirected'] = false;

        $selection = array('id' => $this->code, 'module' => $this->title, 'description' => $this->info);

        if (defined(MODULE_ORDER_TOTAL_PAYMENT_STATUS) && MODULE_ORDER_TOTAL_PAYMENT_STATUS == 'True' && class_exists('ot_payment')) {
            $arrCosts = ot_payment::getPaymentCosts($this->code);
            $selection['module_cost'] = $arrCosts['text'];
        }
        if (defined(MODULE_ORDER_TOTAL_PAYFEE_STATUS) && MODULE_ORDER_TOTAL_PAYFEE_STATUS == 'True' && class_exists('ot_payfee')) {
            $arrCosts = ot_payfee::getPaymentCosts($this->code);
            $selection['module_cost'] = $arrCosts['text'];
        }
        return $selection;
    }

    function pre_confirmation_check() {
        return false;
    }

    function confirmation() {
        return array('title' => $this->description);
    }

    function process_button() {
        return false;
    }

    function before_process() {
		return false;
    }

    function generateRedirectUrl($orderId, $callbackId) {
        global $customer_id, $order, $sidretour, $customers_id, $language, $xtPrice;
       
		$parameters = array();
        //Base Paramenter
		
		$url = 'https://finanzierung.commerzfinanz.com/ecommerce/entry';
       
	   $parameters['vendorid'] = MODULE_PAYMENT_COMMERZFINANZ_VENDORNAME;
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $total = $order->info['total'] + $order->info['tax'];
        } else {
            $total = $order->info['total'];
        }
		$total = str_replace(".", "", $total);
		$total_neu = number_format($total,2,",","");
		$amount = $total_neu;
		
		$parameters['order_amount'] = $amount;
		$parameters['order_id'] = $orderId;
		
		if ($_SESSION['customer_gender'] == 'm') {
			$dresdner_gender = 'HERR';
		} else {
			$dresdner_gender = 'FRAU';
		}
		
		$parameters['salutation'] = $dresdner_gender;
		$parameters['firstname'] = $order->billing['firstname'];
		$parameters['lastname'] = $order->billing['lastname'];

		$dresdnercust_query_check = xtc_db_query("SELECT customers_dob FROM ".TABLE_CUSTOMERS." WHERE customers_id='".(int)$_SESSION['customer_id']."'");
		if (xtc_db_num_rows($dresdnercust_query_check) > 0) {
			$dresdnercust_query_check = xtc_db_fetch_array($dresdnercust_query_check);
			require_once(DIR_FS_INC . 'xtc_date_short.inc.php');
			$parameters['birthdate'] = xtc_date_short($dresdnercust_query_check['customers_dob']);
		}
		if ($order->customer['telephone'] != '') {
			$parameters['phone'] = $order->customer['telephone'];
		}
		$parameters['email'] = $order->customer['email_address'];
		$parameters['street'] = $order->billing['street_address'];
		$parameters['zip'] = $order->billing['postcode'];
		$parameters['city'] = $order->billing['city'];
		
		if (CHECKOUT_AJAX_STAT == 'true') {
			$parameters['cancelURL'] = xtc_href_link(FILENAME_CHECKOUT, '', 'SSL', true);
			$parameters['failureURL'] = xtc_href_link(FILENAME_CHECKOUT, 'payment=' . $this->paymentCode, 'SSL', true);
			$parameters['successURL'] = xtc_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL', true);
			$parameters['notifyURL'] = xtc_href_link(DIR_WS_CATALOG . 'callback/commerzfinanz/ipn.php', '', 'SSL', true);
		} else {
			$parameters['cancelURL'] = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true);
			$parameters['failureURL'] = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment=' . $this->paymentCode, 'SSL', true);
			$parameters['successURL'] = xtc_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL', true);
			$parameters['notifyURL'] = xtc_href_link(DIR_WS_CATALOG . 'callback/commerzfinanz/ipn.php', '', 'SSL', true);
		}
		

        $parameters['paramplus'] .= 'callback_id=' . $callbackId . '&';
        $parameters['paramplus'] .= xtc_session_name() . '=' . xtc_session_id() . '&';

        // Set Paymentmethod
        if ($this->code != 'commerzfinanz_basic') {
            $parameters['PM'] = $this->paymentMethod;
            $parameters['BRAND'] = $this->paymentMethodBrand;
        }

        // Add Template URL
        if (defined('MODULE_PAYMENT_COMMERZFINANZ_TEMPLATE_FILE') && MODULE_PAYMENT_COMMERZFINANZ_TEMPLATE_FILE != '') {
            $parameters['TP'] = xtc_href_link(MODULE_PAYMENT_COMMERZFINANZ_TEMPLATE_FILE, xtc_session_name() . '=' . xtc_session_id(), 'SSL', true);
        }

        $url = $url . '?';
		
        foreach ($parameters as $key => $value) {
            $url .= $key . '=' . $value . '&';
        }
        $url = $this->encodeString($url);
        $url = $this->encodeDatabaseString($url);

        return $url;
    }

    function executeRedirect() {
        global $order;
		xtc_db_query('INSERT INTO payment_callbacks (customers_id, module_name) VALUES (\'' . (int) $_SESSION['customer_id'] . '\', \'' . $this->code . '\')');
        $callbackId = xtc_db_insert_id();
		$lastoid = xtc_db_fetch_array(xtc_db_query("SELECT orders_id FROM orders ORDER BY orders_id DESC LIMIT 1"));
		$orderId = $lastoid['orders_id'];

        $redirectUrl = $this->generateRedirectUrl($orderId, $callbackId);

        xtc_db_query('UPDATE payment_callbacks SET external_order_id = \'' . $orderId . '\' WHERE callback_id = \'' . $callbackId . '\'');
        xtc_db_query('UPDATE payment_callbacks SET orders_id = \'' . $orderId . '\' WHERE callback_id = \'' . $callbackId . '\'');

        $this->writeCallbackLog($callbackId, 'Redirection', 'info');

        $_SESSION['customerIsRedirected'] = true;
        $_SESSION['callbackId'] = $callbackId;
		$_SESSION['cart']->reset(true);
		unset($_SESSION['sendto']);
		unset($_SESSION['billto']);
		unset($_SESSION['shipping']);
		unset($_SESSION['payment']);
		unset($_SESSION['comments']);
		unset($_SESSION['last_order']);
		unset($_SESSION['tmp_oID']);
        xtc_redirect($redirectUrl);
    }

	
    function proceedInComingCall() {
		global $order, $callbackId;
		
		$callbackId = $_SESSION['callbackId'];

		switch ($_GET['status']) {
			// Payment was successfull
			case 'success':
				$this->writeCallbackLog($callbackId, 'OrderHasBeenStored', 'info');
				xtc_db_query('UPDATE payment_callbacks SET external_payment_id = \'' . $_GET['transaction_id'] . '\' WHERE orders_id = \'' . $_GET['order_id'] . '\'');
				xtc_db_query('UPDATE orders_status_history SET comments = \'' . $_GET['transaction_id'] . '\' WHERE orders_id = \'' . $_GET['order_id'] . '\'');
				xtc_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status = '" . MODULE_PAYMENT_COMMERZFINANZ_BASIC_ORDER_STATUS_ID . "' WHERE orders_id = '" . $_GET['order_id'] . "'");
				return true;
				break;

			// Payment failed:
			case 'error':
				$this->writeCallbackLog($_POST['callback_id'], 'DataValidation', 'error');
				return false;
				break;
			default:
				$this->writeCallbackLog($_POST['callback_id'], 'PaymentIsNotAccepted', 'error');
				return false;
				break;
		}
    }

    function writeCallbackLog($callbackId, $status, $type = 'error') {
        global $order;
		switch (strtolower($type)) {
            default:
            case 'error':
                $type = 'error';
                break;

            case 'info':
            case 'information':
                $type = 'info';
                break;

            case 'warning':
                $type = 'waring';
                break;
        }

        xtc_db_query('INSERT INTO payment_callbacks_log (info, added, callback_id, type) VALUES (\'' . $status . '\', NOW(), \'' . $callbackId . '\', \'' . $type . '\')');
    }
	
	function payment_action () {
		return false;
	}

    function after_process() {
        global $order, $insert_id;
        if ($this->order_status) {
            xtc_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status='" . $this->order_status . "' WHERE orders_id='" . $insert_id . "'");
        }
		
        $this->writeCallbackLog($_POST['callback_id'], 'OrderAdded', 'info');
        xtc_db_query('UPDATE payment_callbacks SET orders_id = \'' . $insert_id . '\' WHERE callback_id = \'' . $_POST['callback_id'] . '\'');

        // reset the callback flag
        $_SESSION['customerIsRedirected'] = false;

        // Update payment method
        if ($this->code == 'commerzfinanz_basic') {
            $modules = glob(DIR_WS_MODULES . 'payment/commerzfinanz_*.php');
            $brand = preg_replace('/[^a-z0-9]/i', '', strtolower($_POST['BRAND']));
            $method = preg_replace('/[^a-z0-9]/i', '', strtolower($_POST['PM']));
            foreach ($modules as $filepath) {
                $name = substr(basename($filepath), 0, -4);
                require_once DIR_FS_CATALOG . 'lang/' . $_SESSION['language'] . '/modules/payment/' . $name . '.php';
                require_once $filepath;

                $module = new $name();

                if (preg_replace('/[^a-z0-9]/i', '', strtolower($module->paymentMethod)) == $brand && preg_replace('/[^a-z0-9]/i', '', strtolower($module->paymentMethodBrand)) == $method) {
                    xtc_db_query("UPDATE " . TABLE_ORDERS . " SET payment_method='" . $module->code . "', payment_class = '" . $module->code . "' WHERE orders_id='" . $insert_id . "'");
                    break;
                }
            }
        }

        if (isset($insert_id)) {
            $this->executeRedirect();
        }

    }

    function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_PAYMENT_" . $this->upperCode . "_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    function normalInstallation() {
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_" . $this->upperCode . "_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_" . $this->upperCode . "_ALLOWED', 'DE', '6', '3', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_" . $this->upperCode . "_ORDER_STATUS_ID', '1', '6', '8', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_" . $this->upperCode . "_TMPORDER_STATUS_ID', '1', '6', '9', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('MODULE_PAYMENT_" . $this->upperCode . "_ZONE', '0', '6', '4', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_" . $this->upperCode . "_SORT_ORDER', '0', '6', '7', now())");
    }

    function remove() {
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
        $keys = array('MODULE_PAYMENT_' . $this->upperCode . '_STATUS',
            'MODULE_PAYMENT_' . $this->upperCode . '_ALLOWED',
            'MODULE_PAYMENT_' . $this->upperCode . '_ORDER_STATUS_ID',
            'MODULE_PAYMENT_' . $this->upperCode . '_TMPORDER_STATUS_ID',
            'MODULE_PAYMENT_' . $this->upperCode . '_ZONE',
            'MODULE_PAYMENT_' . $this->upperCode . '_SORT_ORDER'
        );
        return $keys;
    }


    function encodeString($string) {
        if (MODULE_PAYMENT_COMMERZFINANZ_ENCODING != 'UTF-8') {
            return utf8_decode($string);
        } else {
            return utf8_encode($string);
        }
    }

    function encodeDatabaseString($string) {
        if (MODULE_PAYMENT_COMMERZFINANZ_DB_ENCODING == 'UTF-8 encode') {
            return utf8_encode($string);
        } elseif (MODULE_PAYMENT_COMMERZFINANZ_DB_ENCODING == 'UTF-8 decode') {
            return utf8_decode($string);
        } else {
            return $string;
        }
    }

    function substrUtf8($string, $start, $end) {
        return utf8_encode(substr(utf8_decode($string), $start, $end));
    }
	
	
	
    function GetSollzinz($zinssatz_value) {
		
		/*
		8,9		0,10396345	
		9,9		0,10439501	
		10,9	0,10482394	
		11,9	0,10525026	
		12,9	0,10567403	
		13,9	0,10609526	
		14,9	0,10651400
		*/
		if ($zinssatz_value == 8.9) {
			$sollzinssatz = round($zinssatz_value/0.10396345/10,2);
		} elseif ($zinssatz_value == 9.9) {
			$sollzinssatz = round($zinssatz_value/0.10439501/10,2);
		} elseif ($zinssatz_value == 10.9) {
			$sollzinssatz = round($zinssatz_value/0.10482394/10,2);
		} elseif ($zinssatz_value == 11.9) {
			$sollzinssatz = round($zinssatz_value/0.10482394/10,2);
		} elseif ($zinssatz_value == 12.9) {
			$sollzinssatz = round($zinssatz_value/0.10567403/10,2);
		} elseif ($zinssatz_value == 13.9) {
			$sollzinssatz = round($zinssatz_value/0.10609526/10,2);
		} elseif ($zinssatz_value == 14.9) {
			$sollzinssatz = round($zinssatz_value/0.10651400/10,2);
		}
		// echo $sollzinssatz;
		return $sollzinssatz;
    }
	
    function CommerzOrderDetails($value) {
		$mindest_price = DRESDNERFINANZ_MINIMUM_PRICE_TITLE;
		$maximum_price = DRESDNERFINANZ_MAXIMUM_PRICE_TITLE;
		$kredit_wert = $value;
		if ($kredit_wert >= $mindest_price && $kredit_wert <= $maximum_price) {
			$zinssatz_value = DRESDNERFINANZ_ZINS_EFF;
			$sollzinssatz = $this->GetSollzinz($zinssatz_value);
			$zinssatz_value_formated = number_format($zinssatz_value, 2, ",", ".");
			$kredit_wert_formated = number_format($kredit_wert, 2, ",", "");
			$zins_satz = $zinssatz_value / 100;

			$monate6 = 6;
			$monate10 = 10;
			$monate12 = 12;
			$monate18 = 18;
			$monate24 = 24;
			$monate30 = 30;
			$monate36 = 36;
			$monate48 = 48;
			$monate60 = 60;
			$monate72 = 72;

			$rate6 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate6 / 12));
			$rate10 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate10 / 12));
			$rate12 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate12 / 12));
			$rate18 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate18 / 12));
			$rate24 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate24 / 12));
			$rate30 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate30 / 12));
			$rate36 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate36 / 12));
			$rate48 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate48 / 12));
			$rate60 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate60 / 12));
			$rate72 = $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$monate72 / 12));

			if ($rate72 >= 9) {
				$dresdner_rate = $monate72;
			} elseif ($rate60 >= 9) {
				$dresdner_rate = $monate60;
			} elseif ($rate48 >= 9) {
				$dresdner_rate = $monate48;
			} elseif ($rate36 >= 9) {
				$dresdner_rate = $monate36;
			} elseif ($rate30 >= 9) {
				$dresdner_rate = $monate30;
			} elseif ($rate24 >= 9) {
				$dresdner_rate = $monate24;
			} elseif ($rate18 >= 9) {
				$dresdner_rate = $monate18;
			} elseif ($rate12 >= 9) {
				$dresdner_rate = $monate12;
			} elseif ($rate12 >= 9) {
				$dresdner_rate = $monate10;
			} elseif ($rate6 >= 9) {
				$dresdner_rate = $monate6;
			}
			$text_schonab = TEXT_DRESDNER_SCHONAB;
			$text_waehrung = TEXT_DRESDNER_WAEHRUNG;
			$text_oder = TEXT_DRESDNER_ODER;

			if ($rate72 >= 9) {
				$CommerzReturn = $text_schonab . number_format($rate72, 2, ",", ".") . $text_waehrung;
			} elseif ($rate60 >= 9) {
				$CommerzReturn = $text_schonab . number_format($rate60, 2, ",", ".") . $text_waehrung;
			} elseif ($rate48 >= 9) {
				$CommerzReturn = $text_schonab . number_format($rate48, 2, ",", ".") . $text_waehrung;
			} elseif ($rate36 >= 9) {
				$CommerzReturn = $text_schonab . number_format($rate36, 2, ",", ".") . $text_waehrung;
			} elseif ($rate30 >= 9) {
				$CommerzReturn = $text_schonab . number_format($rate30, 2, ",", ".") . $text_waehrung;
			} elseif ($rate24 >= 9) {
				$CommerzReturn = $text_schonab . number_format($rate24, 2, ",", ".") . $text_waehrung;
			} elseif ($rate18 >= 9) {
				$CommerzReturn = $text_schonab . number_format($rate18, 2, ",", ".") . $text_waehrung;
			} elseif ($rate12 >= 9) {
				$CommerzReturn = $text_schonab . number_format($rate12, 2, ",", ".") . $text_waehrung;
			} elseif ($rate10 >= 9) {
				$CommerzReturn = $text_schonab . number_format($rate10, 2, ",", ".") . $text_waehrung;
			} elseif ($rate6 >= 9) {
				$CommerzReturn = $text_schonab . number_format($rate6, 2, ",", ".") . $text_waehrung;
			}
			
			$CommerzReturn .=  '<br>'.TEXT_DRESDNER_PRODUCT_1 . $dresdner_rate . TEXT_DRESDNER_PRODUCT_2 . number_format($dresdner_rate * $kredit_wert * (pow(1 + $zins_satz, 1 / 12) - 1) / (1 - pow(1 + $zins_satz, -$dresdner_rate / 12)), 2, ",", ".") . TEXT_DRESDNER_PRODUCT_2_1 . $sollzinssatz . TEXT_DRESDNER_PRODUCT_3 . $zinssatz_value . TEXT_DRESDNER_PRODUCT_4_O;
			return $CommerzReturn;
		}
    }

}

class CommerzException extends Exception {
    
}
