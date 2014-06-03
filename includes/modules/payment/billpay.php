<?php

require_once DIR_FS_CATALOG . 'includes/billpay/base/billpayBase.php';

class billpay extends billpayBase {
	var $_paymentIdentifier = 'BILLPAY';

	function _getPaymentType() {
		return IPL_CORE_PAYMENT_TYPE_INVOICE;
	}

	function _getStaticLimit($config) {
		if ($this->b2b_active == 'BOTH') {
			return max($config['static_limit_invoice'], $config['static_limit_invoicebusiness']);
		}

		if ($this->b2b_active == 'B2C') {
			return $config['static_limit_invoice'];
		}
		else {
			return $config['static_limit_invoicebusiness'];
		}
		return 0;
	}

	function _extendSeoLayout($selection, $input) {
		$selection['fields'][] = array('title' => '</dt>',
										'field' => $input);
		return $selection;
	}
	
	function _extendSeoEula($selection, $eulaText, $onClickAction) {
		$selection['fields'][] = array(
            'title' => '</dt>',
            'field' => '<input type="checkbox" name="'.$this->_getDataIdentifier('eula').'" '.$onClickAction.' style="display:block;float:left;">' . $eulaText);
		return $selection;
	}
	
	function _is_b2b_allowed($config) {
		return ($config['static_limit_invoicebusiness'] > 0);
	}

	function _is_b2c_allowed($config) {
		return ($config['static_limit_invoice'] > 0);
	}

	function _install_b2b_option() {
		xtc_db_query('INSERT INTO ' . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_".$this->_paymentIdentifier."_B2BCONFIG', 'B2C', '6', '0', 'xtc_cfg_select_option(array(\'B2C\', \'B2B\', \'BOTH\'), ', now())");
	}

	function _checkBuildFeeTitleExtension($paymentIdentifier) {
		$config = $this->getModuleConfig();

		if ($this->b2b_active == 'BOTH' && $this->_is_b2b_allowed($config) && $this->_is_b2c_allowed($config)) {
			return false;
		}
		else if (in_array($this->b2b_active, array('B2C', 'BOTH'))) {
			return parent::_buildFeeTitleExtension('BILLPAY');
		}
		else if (in_array($this->b2b_active, array('B2B', 'BOTH'))) {
			return parent::_buildFeeTitleExtension('BILLPAYBUSINESS');
		}

		return false;
	}
}
