<?php
/*-----------------------------------------------------------------
* 	ID:						cseov25_qf5.php
* 	Letzter Stand:			v2.5
* 	zuletzt geaendert von:	akausch
* 	Datum:					2014/04/08
*
* 	Copyright (c) since 2010 commerce:SEO by Webdesign Erfurt
* 	http://www.commerce-seo.de
* ---------------------------------------------------------------*/

class cseov25_qf5 {
var $title, $output;

function cseov25_qf5() {
  $this->code = 'cseov25_qf5';
  $this->version = '2.5.05';
  $this->title = 'cseov25_qf5';
  $this->description = 'Fix5.5 für commerce:SEO v2next 2.5.4';
  $this->enabled = ((MODULE_CSEO_V251QF5_STATUS == 'true') ? true : false);
  $this->sort_order = MODULE_CSEO_V251QF5_SORT_ORDER;

  $this->output = array();
}

function process() {
  global $order, $xtPrice;


}

function check() {
  if (!isset($this->_check)) {
	$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_CSEO_V251QF5_STATUS'");
	$this->_check = xtc_db_num_rows($check_query);
  }

  return $this->_check;
}

function keys() {
	return array('MODULE_CSEO_V251QF5_STATUS', 'MODULE_CSEO_V251QF5_SORT_ORDER');
}

function install() {
	xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_CSEO_V251QF5_STATUS', 'true', '6', '1','', now())");
	xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) VALUES ('MODULE_CSEO_V251QF5_SORT_ORDER', '1','6', '2', now())");
	xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) VALUES ('MODULE_CSEO_V251QF5', 'true','6', '3', now())");

	
	//Fehlende Navi
	$check_query = xtc_db_query("select name from admin_navigation where name = 'cseo_rma';");
	if (xtc_db_num_rows($check_query) == 0) {
		xtc_db_query("INSERT INTO admin_navigation VALUES (NULL, 'cseo_rma', 'Retouren Anfragen', 'customers', 'cseo_rma.php', NULL, 2, NULL, 5);");
		xtc_db_query("INSERT INTO admin_navigation VALUES (NULL, 'cseo_rma', 'RMA', 'customers', 'cseo_rma.php', NULL, 1, NULL, 5);");
	} else {
		xtc_db_query("UPDATE admin_navigation SET title = 'Retouren Anfragen' WHERE name = 'cseo_rma';");
	}
	
	if (!column_exists ('admin_access','cseo_rma')) {
		xtc_db_query("ALTER TABLE admin_access ADD COLUMN cseo_rma INT(1) NOT NULL DEFAULT '0';");
		xtc_db_query("UPDATE admin_access SET cseo_rma = 1;");
	}

	
	xtc_db_query("UPDATE database_version SET version = 'commerce:SEO v2next 2.5.5 CE'");
	

}

function remove() {
  xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key in ('" . implode("', '", $this->keys()) . "')");
  xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_CSEO_V251QF5'");
}

}
