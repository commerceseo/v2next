<?php
/* --------------------------------------------------------------
	ipayment.php 2013-02-27 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2013 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------


	based on:
	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
	(c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
	(C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
	(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

	Released under the GNU General Public License
	---------------------------------------------------------------------------------------*/

defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);

class ipayment {
	var $code = 'ipayment';
	var $title, $description, $enabled;
	//var $form_action_url;
	var $tmpOrders = true;
	var $tmpStatus = MODULE_PAYMENT_IPAYMENT_TMPORDER_STATUS_ID;
	
	function ipayment() {
		global $order;
		require_once DIR_FS_CATALOG.'/lang/'.$_SESSION['language'].'/modules/payment/'.$this->code.'.php';

		//$this->code = 'ipayment';
		$this->title = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TEXT_TITLE');
		$this->description = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TEXT_DESCRIPTION').
			'<br><br>'.@constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TEXT_DESCRIPTION_LINK').
			'<br><br>'.$this->_checkRequirements();
		$this->sort_order = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_SORT_ORDER');
		$this->enabled = ((@constant('MODULE_PAYMENT_'.strtoupper($this->code).'_STATUS') == 'True') ? true : false);
		$this->info = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TEXT_INFO');
		if ((int)@constant('MODULE_PAYMENT_'.strtoupper($this->code).'_ORDER_STATUS_ID') > 0) {
			$this->order_status = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_ORDER_STATUS_ID');
		}
		$this->tmpStatus = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TMPORDER_STATUS_ID');

		if(is_object($order)) {
			$this->update_status();
		}
	}
	
	function _checkRequirements() {
		$out = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_SYSTEM_REQUIREMENTS').':<br>';
		if(defined('DIR_WS_ADMIN') && strpos($_SERVER['REQUEST_URI'], constant('DIR_WS_ADMIN')) !== false) {
			$has_curl = in_array('curl', get_loaded_extensions());
			$out .= "cURL: ". ($has_curl ? '<span style="color:green">'.@constant('MODULE_PAYMENT_'.strtoupper($this->code).'_OK').'</span>' : '<span style="color:red">'.@constant('MODULE_PAYMENT_'.strtoupper($this->code).'_MISSING').'</span>').'<br>';
			/*
			$has_soap = in_array('soap', get_loaded_extensions());
			$out .= "SOAP: ". ($has_soap ? '<span style="color:green">'.@constant('MODULE_PAYMENT_'.strtoupper($this->code).'_OK').'</span>' : '<span style="color:red">'.@constant('MODULE_PAYMENT_'.strtoupper($this->code).'_MISSING').'</span>').'<br>';
			*/
		}
		return $out;		
	}
	
	function update_status() {
		global $order;
		
		if (($this->enabled == true) && ((int) constant('MODULE_PAYMENT_'.strtoupper($this->code).'_ZONE') > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".constant("MODULE_PAYMENT_".strtoupper($this->code)."_ZONE")."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
			while ($check = xtc_db_fetch_array($check_query)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				}
				elseif ($check['zone_id'] == $order->billing['zone_id']) {
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
		if($_SESSION['language'] != 'german') {
			return false;
		}

		$selection = array(
			'id' => $this->code,
			'module' => $this->title,
			'description' => $this->info,
			// 'fields' => array(),
		);
		
		return $selection;
	}

	function pre_confirmation_check() {
		return false;
	}

	function confirmation() {
		$confirmation = array(
			'title' => constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TEXT_DESCRIPTION'),
		);
		return $confirmation;
	}
	
	function refresh() {
	}

	function process_button() {
		global $order;
		return $pb;
	}
	
	function payment_action() {
		// $GLOBALS['order'], $_SESSION['tmp_oID'], $GLOBALS['order_totals']
		xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_ipayment.php');
	}

	function before_process() {
		return false;
	}

	function after_process() {
		global $insert_id;

		if(isset($_SESSION['ipayment_response'][$insert_id])) {
			$request = $_SESSION['ipayment_response'][$insert_id];
			$order = new order($insert_id);
			require_once 'includes/classes/class.ipayment.php';
			$ipayment = new GMIPayment($order->info['payment_method']);
			$ipayment->logResponse($insert_id, $request);

			if($request['ret_status'] == 'SUCCESS') {
				xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");
			}
			else if($request['ret_status'] == 'REDIRECT') {
				die('this should never happen'); // because we're using normal/silent mode, which handles necessary redirections on its own
			}
			else { // ERROR
				$_SESSION['ipayment_error'] = $request['ret_errormsg'];
				xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?error='.$this->code);
			}
			unset($_SESSION['ipayment_response'][$insert_id]);
		}
		else {
			die('payment failed');
		}

	}

	function get_error() {
		if(isset($_SESSION['ipayment_error'])) {
			$error = array('error' => $_SESSION['ipayment_error']);
			unset($_SESSION['ipayment_error']);
			return $error;
		}
		return false;
	}

	function check() {
		if (!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_".  strtoupper($this->code) ."_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install() {
		$config = $this->_configuration();
		$sort_order = 0;
		foreach($config as $key => $data) {
			$install_query = "insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) ".
					"values ('MODULE_PAYMENT_".strtoupper($this->code)."_".$key."', '".$data['configuration_value']."', '6', '".$sort_order."', '".addslashes($data['set_function'])."', '".addslashes($data['use_function'])."', now())";
			xtc_db_query($install_query);
			xtc_db_query('CREATE TABLE IF NOT EXISTS ipayment_log (
  ipayment_log_id int(11) NOT NULL AUTO_INCREMENT,
  orders_id int(11) NOT NULL,
  action varchar(255) NOT NULL,
  trxuser_id varchar(255) NOT NULL,
  trx_currency varchar(255) NOT NULL,
  trx_amount varchar(255) NOT NULL,
  trx_typ varchar(255) NOT NULL,
  addr_name varchar(255) NOT NULL,
  addr_email varchar(255) NOT NULL,
  shopper_id varchar(255) NOT NULL,
  client_name varchar(255) NOT NULL,
  client_version varchar(255) NOT NULL,
  addr_street varchar(255) NOT NULL,
  addr_zip varchar(255) NOT NULL,
  addr_city varchar(255) NOT NULL,
  trx_paymenttyp varchar(255) NOT NULL,
  ret_transdate varchar(255) NOT NULL,
  ret_transtime varchar(255) NOT NULL,
  ret_errorcode varchar(255) NOT NULL,
  ret_fatalerror varchar(255) NOT NULL,
  ret_errormsg varchar(255) NOT NULL,
  ret_additionalmsg varchar(255) NOT NULL,
  ret_authcode varchar(255) NOT NULL,
  ret_ip varchar(255) NOT NULL,
  ret_booknr varchar(255) NOT NULL,
  ret_trx_number varchar(255) NOT NULL,
  ret_param_checksum varchar(255) NOT NULL,
  redirect_needed varchar(255) NOT NULL,
  trx_paymentmethod varchar(255) NOT NULL,
  trx_paymentdata_country varchar(255) NOT NULL,
  trx_remoteip_country varchar(255) NOT NULL,
  ret_status varchar(255) NOT NULL,
  trx_payauth_status varchar(255) NOT NULL,
  trx_issuer_avs_response varchar(255) NOT NULL,
  ret_url_checksum varchar(255) NOT NULL,
  addr_check_result varchar(255) NOT NULL,
  paydata varchar(255) NOT NULL,
  PRIMARY KEY (ipayment_log_id),
  KEY orders_id (orders_id)
)');
			$sort_order++;
		}
	}

	function remove() {
		xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	/**
	 * Determines the module's configuration keys
	 * @return array
	 */
	function keys() {
		$ckeys = array_keys($this->_configuration());
		$keys = array();
		foreach($ckeys as $k) {
			$keys[] = 'MODULE_PAYMENT_'.strtoupper($this->code).'_'.$k;
		}
		return $keys;
	}
	
	function isInstalled() {
		foreach($this->keys() as $key) {
			if(!defined($key)) {
				return false;
			}
		}
		return true;
	}
	
	function _configuration() {
		$config = array(
			'STATUS' => array(
				'configuration_value' => 'True',
				'set_function' => 'xtc_cfg_select_option(array(\'True\', \'False\'), ',
			),
			'ALLOWED' => array(
				'configuration_value' => '',
			),
			'ACCOUNT_ID' => array(
				'configuration_value' => '99999',
			),
			'APPLICATION_ID' => array(
				'configuration_value' => '99998',
			),
			'APPLICATION_PASSWORD' => array(
				'configuration_value' => '0',
			),
			'ADMINACTION_PASSWORD' => array(
				'configuration_value' => '5cfgRT34xsdedtFLdfHxj7tfwx24fe',
			),
			'SECURITY_KEY' => array(
				'configuration_value' => 'testtest',
			),
			'AUTH_MODE' => array(
				'configuration_value' => 'auth',
				'set_function' => 'xtc_cfg_select_option(array(\'auth\', \'preauth\'), ',
			),
			'PAYMENT_MODE' => array(
				'configuration_value' => 'normal',
				'set_function' => 'xtc_cfg_select_option(array(\'normal\', \'silent\'), ',
			),
			'ZONE' => array(
				'configuration_value' => '0',
				'use_function' => 'xtc_get_zone_class_title',
				'set_function' => 'xtc_cfg_pull_down_zone_classes(',
			),
			'TMPORDER_STATUS_ID' => array(
				'configuration_value' => '',
				'set_function' => 'xtc_cfg_pull_down_order_statuses(',
				'use_function' => 'xtc_get_order_status_name',
			),
			'ORDER_STATUS_ID' => array(
				'configuration_value' => '',
				'set_function' => 'xtc_cfg_pull_down_order_statuses(',
				'use_function' => 'xtc_get_order_status_name',
			),
			'ERRORORDER_STATUS_ID' => array(
				'configuration_value' => '',
				'set_function' => 'xtc_cfg_pull_down_order_statuses(',
				'use_function' => 'xtc_get_order_status_name',
			),
			'SORT_ORDER' => array(
				'configuration_value' => '0',
			),
		);
		
		return $config;
	}
	
}

