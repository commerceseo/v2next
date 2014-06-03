<?php
require_once DIR_FS_CATALOG . 'includes/classes/class.commerzfinanz.php';
class commerzfinanz_basic extends commerzfinanz {
	var $code = 'commerzfinanz_basic';
	var $paymentMethod = NULL;
	var $paymentMethodBrand = NULL;
	// var $arrCurrencies = array('EUR');
	
	function commerzfinanz_basic() {
		global $order;
		$this->commerzfinanz();
		$this->info = MODULE_PAYMENT_COMMERZFINANZ_BASIC_TEXT_INFO;
		if(preg_match('/.*\/admin.*/i', $_SERVER['REQUEST_URI']) && $this->enabled) {
			// $this->updateLanguageVariable();
		}

        if ($_SESSION['cart']->total < DRESDNERFINANZ_MINIMUM_PRICE_TITLE) {
            $check_flag = false;
            $this->enabled = false;
        }
        if ($_SESSION['cart']->total > DRESDNERFINANZ_MAXIMUM_PRICE_TITLE) {
            $check_flag = false;
            $this->enabled = false;
        }
		
	}
	
	function install() {
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_COMMERZFINANZ_VENDORNAME', '8403', '6', '20', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_COMMERZFINANZ_ENCODING', 'UTF-8', '6', '65', 'xtc_cfg_select_option(array(\'UTF-8\', \'ISO-8859-1\'), ', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_COMMERZFINANZ_DB_ENCODING', 'do not change encoding', '6', '65', 'xtc_cfg_select_option(array(\'UTF-8 encode\', \'UTF-8 decode\', \'do not change encoding\'), ', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('DRESDNERFINANZ_ZINS_EFF', '9.90', '6', '65', 'xtc_cfg_select_option(array(\'8.90\', \'9.90\', \'10.90\', \'11.90\', \'12.90\', \'13.90\', \'14.90\'), ', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_COMMERZFINANZ_TEMPLATE_FILE', 'commerzfinanz_template.php', '80', '', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('DRESDNERFINANZ_MINIMUM_PRICE_TITLE', '100.00', '6', '0', now());");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('DRESDNERFINANZ_STATUS', 'true', '6', '3', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now());");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('DRESDNERFINANZ_MAXIMUM_PRICE_TITLE', '50000.00', '6', '0', now());");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('DRESDNERFINANZ_CAMPAIGN', '0', '6', '0', now());");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('DRESDNERFINANZ_CONTENT_ID', '12', '6', '0', now());");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('DRESDNERFINANZ_CONTENT_COLOR', '#1B6D82', '6', '0', now());");

		
		xtc_db_query("CREATE TABLE IF NOT EXISTS payment_callbacks_log (
  			log_id int(11) unsigned NOT NULL auto_increment,
  			callback_id int(11) unsigned NOT NULL,
  			info varchar(255) NOT NULL,
  			added datetime NOT NULL,
  			type enum('warning','error','info') NOT NULL,
  			PRIMARY KEY  (log_id)
		) ");
		
		xtc_db_query("CREATE TABLE IF NOT EXISTS payment_callbacks (
		  callback_id int(11) unsigned NOT NULL auto_increment,
		  customers_id int(11) unsigned NOT NULL,
		  module_name varchar(255) NOT NULL,
		  orders_id int(11) unsigned default NULL,
		  external_order_id varchar(255) default NULL,
		  external_payment_id varchar(255) NOT NULL,
		  PRIMARY KEY  (callback_id)
		) ");
		$this->normalInstallation();
	}

	function keys() {
		$keys = parent::keys();
		$keys[] = 'MODULE_PAYMENT_COMMERZFINANZ_VENDORNAME';
		$keys[] = 'MODULE_PAYMENT_COMMERZFINANZ_TEMPLATE_FILE';
		$keys[] = 'MODULE_PAYMENT_COMMERZFINANZ_ENCODING';
		$keys[] = 'MODULE_PAYMENT_COMMERZFINANZ_DB_ENCODING';
		$keys[] = 'DRESDNERFINANZ_STATUS';
		$keys[] = 'DRESDNERFINANZ_MINIMUM_PRICE_TITLE';
		$keys[] = 'DRESDNERFINANZ_MAXIMUM_PRICE_TITLE';
		$keys[] = 'DRESDNERFINANZ_ZINS_EFF';
		$keys[] = 'DRESDNERFINANZ_CONTENT_ID';
		$keys[] = 'DRESDNERFINANZ_CONTENT_COLOR';
		$keys[] = 'DRESDNERFINANZ_CAMPAIGN';
		return $keys;
	}	
}
