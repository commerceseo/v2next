<?php

/* -----------------------------------------------------------------
 * 	$Id: klarna2_partpay.php 36 2013-11-29 13:21:27Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 *      Gambio GmbH
 *      http://www.gambio.de
 *      Copyright (c) 2013 Gambio GmbH
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

defined('CSEO_HTTP_SERVER') or define('CSEO_HTTP_SERVER', HTTP_SERVER);
defined('INVALID_ORDERS_STATUS') or define('INVALID_ORDERS_STATUS', -1);
require_once DIR_FS_CATALOG . 'includes/classes/class.klarna.php';

class klarna2_partpay {

    public $code;
    public $title;
    public $description;
    public $enabled;
    public $tmpOrders = true;
    public $tmpStatus;

    public function __construct() {
        $config_url = CSEO_HTTP_SERVER . DIR_WS_ADMIN . 'klarna_config.php';
        $this->code = 'klarna2_partpay';
        $this->title = MODULE_PAYMENT_KLARNA2_PARTPAY_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_KLARNA2_PARTPAY_TEXT_DESCRIPTION;
        if (strpos($_SERVER['REQUEST_URI'], 'admin/modules.php') !== false) {
            $this->description .= '<a href="' . $config_url . '" class="button" style="margin: 1ex auto 1.5em;">Konfiguration</a>';
        }
        $this->sort_order = MODULE_PAYMENT_KLARNA2_PARTPAY_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_KLARNA2_PARTPAY_STATUS == 'True') ? true : false);
        $this->info = MODULE_PAYMENT_KLARNA2_PARTPAY_TEXT_INFO;
        $this->tmpStatus = MODULE_PAYMENT_KLARNA2_PARTPAY_TMPSTATUS;
        $this->order_status = MODULE_PAYMENT_KLARNA2_PARTPAY_ORDERSTATUS;

        if (is_object($GLOBALS['order'])) {
            $this->update_status();
        }
		
        if ($_SESSION['cart']->total < MODULE_PAYMENT_KLARNA2_PARTPAY_MIN_ORDER) {
            $check_flag = false;
            $this->enabled = false;
        }
        if ($_SESSION['cart']->total > MODULE_PAYMENT_KLARNA2_PARTPAY_MAX_ORDER) {
            $check_flag = false;
            $this->enabled = false;
        }
    }

    public function klarna2_partpay() {
        $this->__construct();
    }

    public function update_status() {
        if (($this->enabled == true) && ((int) constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_ZONE') > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_ZONE') . "' and zone_country_id = '" . $GLOBALS['order']->billing['country']['id'] . "' order by zone_id");
            while ($check = xtc_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check['zone_id'] == $GLOBALS['order']->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }

            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    public function javascript_validation() {
        return false;
    }

    public function selection() {
        $klarna = new GMKlarna();
        if ($_SESSION['currency'] != $klarna->getCurrencyString()) {
            $klarna->_log("no partpay: currency mismatch");
            return false;
        }
        $order = $GLOBALS['order'];
        $total_amount = $GLOBALS['order']->info['total'];
        if (!is_numeric($total_amount)) {
            $total_amount = $_SESSION['klarna_total_amount'];
        } else {
            $_SESSION['klarna_total_amount'] = $total_amount;
        }
        $max_amount = $klarna->getMaximumPartpayAmount();
        if ($total_amount > $max_amount) {
            $klarna->_log("partpay: maximum amount of $max_amount exceeded.");
            return false;
        }
        $pclasses = $klarna->getPClasses($total_amount);
        $check = $klarna->paymentSelectionCheck($GLOBALS['order']);
        if ($check['allow_select'] == true && $pclasses !== false) {
            $minfee = $klarna->formatAmount($klarna->getMinimumMonthlyCost($total_amount));
            $country = $order->delivery['country']['iso_code_2'];
            $logo_width = 150;
            $description .= '<img style="float: left; margin-right: 1em;" src="' . $klarna->getCDNLogoURL('account', $logo_width, $country) . '">';
            $description .= '<div style="float:left; width: 400px;">';
            $description .= strtr($this->info, array('#minfee' => $minfee, '#url' => 'http://www.klarna.com/'));
            $description .= ' <span id="klarna_acc_cond"></span><br><br>';
            $description .= '<div class="klarna_phone">';
            $description .= YOUR_PHONE_NUMBER;
            $description .= ': <input name="klarna_ppay_phone" type="text" value="' . $order->customer['telephone'] . '">';
            $description .= '</div>';
            $dob = $this->_getDateOfBirth();
            $description .= '<div class="klarna_dob">' . YOUR_DATE_OF_BIRTH . '<br>';
            $description .= '<select name="klarna_ppay_dob_year">';
            for ($year = date('Y') - 10; $year >= 1900; $year--) {
                $description .= '<option value="' . $year . '" ' . ($year == $dob['year'] ? 'selected="selected"' : '') . '>' . $year . '</option>';
            }
            $description .= '</select>';
            $description .= '<select name="klarna_ppay_dob_month">';
            for ($month = 1; $month <= 12; $month++) {
                $description .= '<option value="' . $month . '" ' . ($month == $dob['month'] ? 'selected="selected"' : '') . '>' . sprintf('%02d', $month) . '</option>';
            }
            $description .= '</select>';
            $description .= '<select name="klarna_ppay_dob_day">';
            for ($day = 1; $day <= 31; $day++) {
                $description .= '<option value="' . $day . '" ' . ($day == $dob['day'] ? 'selected="selected"' : '') . '>' . sprintf('%02d', $day) . '</option>';
            }
            $description .= '</select>';
            $description .= '</div>';
            $num_pno_digits = $klarna->getNumPNODigits();
            if ($num_pno_digits > 0) {
                $description .= '<br>' . ENTER_PNO . ': ';
                $description .= '<input type="text" name="klarna_ppay_pno" maxlength="' . $num_pno_digits . '" size="' . $num_pno_digits . '" placeholder="' . str_repeat('N', $num_pno_digits) . '">';
            }
            $description .= '<br>';
            $description .= '<select name="klarna_pclass_id">';
            foreach ($pclasses as $pclass) {
                $description .= '<option value="' . $pclass->getId() . '">';
                $description .= $pclass->getDescription() . ' (' . STARTING_AT . ' ' .
                        $klarna->formatAmount(KlarnaCalc::calc_monthly_cost($total_amount, $pclass, KlarnaFlags::CHECKOUT_PAGE)) . ')';
                $description .= '</option>';
            }
            $description .= '</select>';
            $description .= $klarna->getCreditWarning();
            if (empty($_SESSION['customer_gender'])) {
                $description .= '<br>' . ENTER_GENDER . ': ';
                $description .= '<select name="klarna_ppay_gender">';
                $description .= '<option value="0">' . GENDER_FEMALE . '</option>';
                $description .= '<option value="1">' . GENDER_MALE . '</option>';
                $description .= '</select>';
            }

            $description .= $klarna->getAccountConditionsLink('klarna_acc_cond');

            if ($klarna->consentRequired()) {
                $description .= '<div class="klarna_consent">';
                $description .= '<input name="klarna_ppay_consent" type="checkbox" value="1" id="klarna_ppay_consent">';
                $description .= '<label for="klarna_ppay_consent">' . $klarna->getConsentboxText() . '</label>';
                $description .= '</div>';
            }

            $description .= '</div>';


            $selection = array(
                'id' => $this->code,
                'module' => $this->title,
                'description' => $description,
            );
        } else {
            if ($check['is_b2b'] == true) {
                $no_b2b_description = NO_B2B_DESCRIPTION;
                $no_b2b_description .= '<input type="hidden" name="' . $this->code . '_no_b2b" value="1">';
                $selection = array(
                    'id' => $this->code,
                    'module' => $this->title,
                    'description' => $no_b2b_description,
                );
            } else {
                $selection = false;
            }
            if ($pclasses == false) {
                $klarna->_log("no partpay: no pclass");
            }
        }
        return $selection;
    }

    protected function _getDateOfBirth() {
        if (isset($_SESSION['klarna_ppay_dob'])) {
            list($session_year, $session_month, $session_day) = explode('-', $_SESSION['klarna_ppay_dob']);
            $dob = array(
                'year' => $session_year,
                'month' => $session_month,
                'day' => $session_day,
            );
        } else {
            $query = "SELECT YEAR(`customers_dob`) AS year, MONTH(`customers_dob`) AS month, DAY(`customers_dob`) AS day FROM customers WHERE customers_id = :cid";
            $query = strtr($query, array(':cid' => (int) $_SESSION['customer_id']));
            $result = xtc_db_query($query, 'db_link', false);
            $dob = array('year' => '0000', 'month' => '00', 'day' => '00');
            while ($row = xtc_db_fetch_array($result)) {
                $dob = $row;
            }
        }
        return $dob;
    }

    protected function _setDateOfBirth($year, $month, $day, $customers_id = null) {
        if ($customers_id === null) {
            $customers_id = $_SESSION['customer_id'];
        }
        $dob = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $query = "UPDATE customers SET customers_dob = ':dob' WHERE customers_id = :cid";
        $query = strtr($query, array(':dob' => $dob, ':cid' => (int) $customers_id));
        xtc_db_query($query);
    }

    protected function _setPhoneNumber($phone) {
        $query = "UPDATE customers SET customers_telephone = '" . xtc_db_input($phone) . "' WHERE customers_id = " . (int) $_SESSION['customer_id'];
        xtc_db_query($query);
    }

    public function pre_confirmation_check($vars = '') {
        if (is_array($vars) && !empty($vars)) {
            $data_arr = $vars;
            $is_ajax = true;
        } else {
            $data_arr = $_POST;
        }
        $klarna = new GMKlarna();

        if (isset($data_arr[$this->code . '_no_b2b'])) {
            if (CHECKOUT_AJAX_STAT == 'true') {
                $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_KLARNA2_PARTPAY_ERROR_NO_B2B);
                $_SESSION['checkout_payment_error'] = $payment_error_return;
            } else {
                xtc_redirect(CSEO_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?' . http_build_query(array('payment_error' => $this->code, 'error_reason' => 'no_b2b')));
            }
        }

        // check for dob
        if (isset($data_arr['klarna_ppay_dob_year']) && isset($data_arr['klarna_ppay_dob_month']) && isset($data_arr['klarna_ppay_dob_day'])) {
            $_SESSION['klarna_ppay_dob'] = $data_arr['klarna_ppay_dob_year'] . '-' . $data_arr['klarna_ppay_dob_month'] . '-' . $data_arr['klarna_ppay_dob_day'];
            $this->_setDateOfBirth($data_arr['klarna_ppay_dob_year'], $data_arr['klarna_ppay_dob_month'], $data_arr['klarna_ppay_dob_day']);
        }
        if (!$this->_checkCustomerAge()) {
            if (CHECKOUT_AJAX_STAT == 'true') {
                $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_KLARNA2_PARTPAY_ERROR_TOO_YOUNG);
                $_SESSION['checkout_payment_error'] = $payment_error_return;
            } else {
                xtc_redirect(CSEO_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?' . http_build_query(array('payment_error' => $this->code, 'error_reason' => 'too_young')));
            }
        }
        if (!empty($data_arr['klarna_ppay_pno'])) {
            $_SESSION['klarna_pno'] = $data_arr['klarna_ppay_pno'];
        } else {
            $_SESSION['klarna_pno'] = '';
        }

        $_SESSION['klarna_ppay_consent'] = isset($data_arr['klarna_ppay_consent']);
        if ($klarna->consentRequired() && $_SESSION['klarna_ppay_consent'] == false) {
            if (CHECKOUT_AJAX_STAT == 'true') {
                $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_KLARNA2_PARTPAY_ERROR_NO_CONSENT);
                $_SESSION['checkout_payment_error'] = $payment_error_return;
            } else {
                xtc_redirect(CSEO_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?' . http_build_query(array('payment_error' => $this->code, 'error_reason' => 'no_consent')));
            }
        }

        if (isset($data_arr['klarna_ppay_phone'])) {
            $phone = trim($data_arr['klarna_ppay_phone']);
            $this->_setPhoneNumber($phone);
        } else {
            $phone = $GLOBALS['order']->customer['telephone'];
        }
        if (empty($phone)) {
            if (CHECKOUT_AJAX_STAT == 'true') {
                $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_KLARNA2_INVOICE_ERROR_NO_PHONE);
                $_SESSION['checkout_payment_error'] = $payment_error_return;
            } else {
                xtc_redirect(CSEO_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?' . http_build_query(array('payment_error' => $this->code, 'error_reason' => 'no_phone')));
            }
        }

        if (empty($_SESSION['klarna_pclass_id']) && !isset($data_arr['klarna_pclass_id'])) {
            if (CHECKOUT_AJAX_STAT == 'true') {
                xtc_redirect(CSEO_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT . '?' . http_build_query(array('payment_error' => $this->code, 'error_reason' => 'select_pclass')));
            } else {
                xtc_redirect(CSEO_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?' . http_build_query(array('payment_error' => $this->code, 'error_reason' => 'select_pclass')));
            }
        }
        $_SESSION['klarna_pclass_id'] = $data_arr['klarna_pclass_id'];

        if (isset($data_arr['klarna_ppay_gender'])) {
            $gender = $data_arr['klarna_ppay_gender'] == '1' ? 'm' : 'f';
            xtc_db_query("UPDATE customers SET customers_gender = '" . $gender . "' WHERE customers_id = " . $_SESSION['customer_id']);
            $_SESSION['customer_gender'] = $gender;
        }
        return false;
    }

    protected function _checkCustomerAge() {
        $dob = $this->_getDateOfBirth();
        $dob_time = strtotime(sprintf('%04d-%02d-%02d', $dob['year'], $dob['month'], $dob['day']));
        $dob_max = strtotime('18 years ago');
        $old_enough = $dob_time < $dob_max;
        return $old_enough;
    }

    public function confirmation() {
        $klarna = new GMKlarna();
        $cw = '<div class="klarna_creditwarning_big">' . $klarna->getCreditWarning() . '</div>';

        $confirmation = array(
            'title' => constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_TEXT_DESCRIPTION') . $cw,
        );
        return $confirmation;
    }

    public function refresh() {
        
    }

    public function process_button() {
        $order = $GLOBALS['order'];
        $pb = '';
        return $pb;
    }

    public function payment_action() {
        $order = $GLOBALS['order'];
        $orders_id = $GLOBALS['insert_id'];
        $klarna = new GMKlarna();
        try {
            $result = $klarna->reserveInvoiceAmount($order, $orders_id, $_SESSION['klarna_pclass_id']);
        } catch (Exception $e) {
            $this->_invalidateTempOrder($orders_id);
            $_SESSION['klarna2_error'] = $e->getMessage();
            if (CHECKOUT_AJAX_STAT == 'true') {
                xtc_redirect(CSEO_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT . '?' . http_build_query(array('payment_error' => $this->code)));
            } else {
                xtc_redirect(CSEO_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?' . http_build_query(array('payment_error' => $this->code)));
            }
        }
        if ($result[1] == KlarnaFlags::PENDING || $result[1] == KlarnaFlags::ACCEPTED) {
            $query = "UPDATE orders SET orders_status = " . MODULE_PAYMENT_KLARNA2_PARTPAY_ORDERSTATUS . " WHERE orders_id = " . (int) $orders_id;
            xtc_db_query($query);
            xtc_redirect(CSEO_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PROCESS);
        } else { // KlarnaFlags::DENIED
            $this->_invalidateTempOrder($orders_id);
            if (CHECKOUT_AJAX_STAT == 'true') {
                xtc_redirect(CSEO_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT . '?' . http_build_query(array('payment_error' => $this->code, 'error_reason' => 'denied')));
            } else {
                xtc_redirect(CSEO_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?' . http_build_query(array('payment_error' => $this->code, 'error_reason' => 'denied')));
            }
        }
    }

    private function _invalidateTempOrder($orders_id) {
        unset($_SESSION['tmp_oID']);
        unset($_SESSION['payment']);
        $klarna = new GMKlarna();
        $klarna->removeOrder($orders_id);
    }

    public function before_process() {
        return false;
    }

    public function after_process() {
        $insert_id = $GLOBALS['insert_id'];
    }

    public function get_error() {
        if (isset($_GET['error_reason'])) {
            $error_msg = @constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_ERROR_' . strtoupper($_GET['error_reason']));
            if (empty($error_msg)) {
                $error_msg = constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_ERROR_UNSPECIFIED');
            }
            $error = array('error' => $error_msg);
            return $error;
        }
        if (isset($_SESSION['klarna2_error'])) {
            $error = array('error' => $_SESSION['klarna2_error']);
            unset($_SESSION['klarna2_error']);
            return $error;
        }
        return false;
    }

    public function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_" . strtoupper($this->code) . "_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    public function install() {
        $config = $this->_configuration();
        $sort_order = 0;
        foreach ($config as $key => $data) {
            $install_query = "insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) " .
                    "values ('MODULE_PAYMENT_" . strtoupper($this->code) . "_" . $key . "', '" . $data['configuration_value'] . "', '6', '" . $sort_order . "', '" . addslashes($data['set_function']) . "', '" . addslashes($data['use_function']) . "', now())";
            xtc_db_query($install_query);
            $sort_order++;
        }


        if (!column_exists('admin_access', 'klarna_config')) {
            xtc_db_query("ALTER TABLE admin_access ADD klarna_config INT( 1 ) NOT NULL DEFAULT '0';");
            xtc_db_query("UPDATE admin_access SET klarna_config = 1 WHERE module_export = 1;");
        }

        if (table_exists('orders_klarna') == false) {
            xtc_db_query("CREATE TABLE IF NOT EXISTS orders_klarna (
						  orders_id int(10) unsigned NOT NULL,
						  rno varchar(255) NOT NULL,
						  status varchar(255) NOT NULL,
						  risk_status varchar(255) NOT NULL,
						  inv_rno varchar(255) NOT NULL,
						  PRIMARY KEY (orders_id),
						  KEY rno (rno)
						);");
        }
        if (table_exists('orders_klarna_returnamount') == false) {
            xtc_db_query("CREATE TABLE IF NOT EXISTS orders_klarna_returnamount (
						  ok_returnamount_id int(10) unsigned NOT NULL AUTO_INCREMENT,
						  orders_id int(10) unsigned NOT NULL,
						  amount decimal(15,4) NOT NULL,
						  vat decimal(15,4) NOT NULL,
						  description varchar(255) NOT NULL,
						  sent_time datetime NOT NULL,
						  PRIMARY KEY (ok_returnamount_id),
						  KEY orders_id (orders_id)
						);");
        }
        if (table_exists('orders_klarna_creditpart') == false) {
            xtc_db_query("CREATE TABLE IF NOT EXISTS orders_klarna_creditpart (
						  ok_creditpart_id int(10) unsigned NOT NULL AUTO_INCREMENT,
						  orders_id int(10) unsigned NOT NULL,
						  products_model varchar(255) NOT NULL,
						  quantity int(11) NOT NULL,
						  sent_time datetime NOT NULL,
						  PRIMARY KEY (ok_creditpart_id),
						  KEY orders_id (orders_id)
						);");
        }
    }

    public function remove() {
        xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    public function keys() {
        $ckeys = array_keys($this->_configuration());
        $keys = array();
        foreach ($ckeys as $k) {
            $keys[] = 'MODULE_PAYMENT_' . strtoupper($this->code) . '_' . $k;
        }
        return $keys;
    }

    protected function _configuration() {
        $config = array(
            'STATUS' => array(
                'configuration_value' => 'True',
                'set_function' => 'xtc_cfg_select_option(array(\'True\', \'False\'), ',
            ),
            'ALLOWED' => array(
                'configuration_value' => 'AT,DK,FI,DE,NO,SE,NL',
            ),
            'ZONE' => array(
                'configuration_value' => '0',
                'use_function' => 'xtc_get_zone_class_title',
                'set_function' => 'xtc_cfg_pull_down_zone_classes(',
            ),
            'SORT_ORDER' => array(
                'configuration_value' => '0',
            ),
            'MIN_ORDER' => array(
                'configuration_value' => '0',
            ),
            'MAX_ORDER' => array(
                'configuration_value' => '5000',
            ),
            'TMPSTATUS' => array(
                'configuration_value' => '1',
                'set_function' => 'xtc_cfg_pull_down_order_statuses(',
                'use_function' => 'xtc_get_order_status_name',
            ),
            'ORDERSTATUS' => array(
                'configuration_value' => '2',
                'set_function' => 'xtc_cfg_pull_down_order_statuses(',
                'use_function' => 'xtc_get_order_status_name',
            ),
        );
        return $config;
    }
}
