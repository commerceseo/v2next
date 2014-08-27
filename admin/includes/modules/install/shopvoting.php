<?php
/*-----------------------------------------------------------------
* 	$Id: shopvoting.php 497 2013-07-17 11:00:43Z akausch $
* 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
* 	http://www.commerce-seo.de
* ------------------------------------------------------------------
* 	based on:
* 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
* 	(c) 2002-2003 osCommerce - www.oscommerce.com
* 	(c) 2003     nextcommerce - www.nextcommerce.org
* 	(c) 2005     xt:Commerce - www.xt-commerce.com
* 	Released under the GNU General Public License
* ---------------------------------------------------------------*/

  class shopvoting {
    var $title, $output;

    function shopvoting() {
      $this->code = 'shopvoting';
      $this->version = '2.5';
      $this->title = 'shopvoting';
      $this->description = 'Shop-Voting Modul für commerce:SEO v2.5';
      $this->enabled = ((MODULE_CSEO_SHOPVOTING_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_CSEO_SHOPVOTING_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $xtPrice;


    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_CSEO_SHOPVOTING_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
		return array('MODULE_CSEO_SHOPVOTING_STATUS', 'MODULE_CSEO_SHOPVOTING_SORT_ORDER');
    }

    function install() {
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_CSEO_SHOPVOTING_STATUS', 'true', '6', '1','', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) VALUES ('MODULE_CSEO_SHOPVOTING_SORT_ORDER', '1','6', '2', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) VALUES ('MODULE_CSEO_SHOPVOTING', 'true','6', '3', now())");
		
			if (column_exists ('admin_access','bewertungen_verwalten')==false) {
				xtc_db_query("ALTER TABLE admin_access ADD bewertungen_verwalten INT( 1 ) NOT NULL DEFAULT 0;");
				xtc_db_query("UPDATE admin_access SET bewertungen_verwalten = '1' WHERE reviews = 1;");
				xtc_db_query("ALTER TABLE admin_access ADD bewertungen_verwalten_config INT( 1 ) NOT NULL DEFAULT 0;");
				xtc_db_query("UPDATE admin_access SET bewertungen_verwalten_config = '1' WHERE reviews = 1;");
			}
			xtc_db_query("INSERT INTO boxes_names (id, box_name, box_title, box_desc, language_id, status) VALUES ('', 'votingbox', 'Shop Voting', '', 1, 0);");
			xtc_db_query("INSERT INTO boxes_names (id, box_name, box_title, box_desc, language_id, status) VALUES ('', 'votingbox', 'Shop Bewertungen', '', 2, 0);");
			xtc_db_query("INSERT INTO boxes (id, box_name, position, sort_id, status, box_type) VALUES ('', 'votingbox', 'boxen', 2, 0, 'file');");
			if (table_exists ('bewertung')==false) {
				xtc_db_query("CREATE TABLE IF NOT EXISTS bewertung (
								bewertung_id int(11) NOT NULL auto_increment,
								bewertung_vorname varchar(32) NOT NULL,
								bewertung_nachname varchar(32) NOT NULL,
								bewertung_kundenid int(11) NOT NULL,
								bewertung_datum datetime NOT NULL,
								bewertung_shoprating int(1) NOT NULL,
								bewertung_seite int(1) NOT NULL,
								bewertung_versand int(1) NOT NULL,
								bewertung_ware int(1) NOT NULL,
								bewertung_service int(1) NOT NULL,
								bewertung_text text NOT NULL,
								bewertung_kommentar text NOT NULL,
								bewertung_sprache tinyint(1) NOT NULL,
								bewertungs_ip varchar(15) NOT NULL,
								bewertung_status int(1) NOT NULL,
								bewertungs_email varchar(96) NOT NULL,
								orders_id int(1) NOT NULL,
								PRIMARY KEY (bewertung_id)
				);");

				xtc_db_query("CREATE TABLE IF NOT EXISTS bewertung_config (
								id int(1) NOT NULL,
								admin_email varchar(96) NOT NULL,
								send_admin_email int(1) NOT NULL,
								customer_group_read varchar(255) NOT NULL,
								customer_group_write varchar(255) NOT NULL,
								customer_group_captcha varchar(255) NOT NULL,
								entry_per_page_frontend varchar(3) NOT NULL,
								voting_module_aktive varchar(1) NOT NULL,
								front_page_character varchar(5) NOT NULL,
								required_name int(1) NOT NULL,
								required_order_id int(1) NOT NULL,
								required_comment int(1) NOT NULL,
								activate_votings int(1) NOT NULL,
								required_order_id_email int(1) NOT NULL,
								PRIMARY KEY (id)
				);");

				xtc_db_query("INSERT INTO bewertung_config (id, admin_email, send_admin_email, customer_group_read, customer_group_write, customer_group_captcha, entry_per_page_frontend, voting_module_aktive, front_page_character) 
								VALUES
							(0, 'deine@email.de', 0, '', '', '', '20', '0', '350');");

			}
			xtc_db_query("INSERT INTO admin_navigation (name, title, subsite, filename, languages_id) 
							VALUES
						('bewertungen_verwalten', 'Shop-Bewertungen', 'products', 'bewertungen_verwalten.php', '2');");
						
			xtc_db_query("INSERT INTO admin_navigation (name, title, subsite, filename, languages_id) 
							VALUES
						('bewertungen_verwalten_config', 'Shop-Bewertung-Config', 'products', 'bewertungen_verwalten_config.php', '2');");
			xtc_db_query("INSERT INTO admin_navigation (name, title, subsite, filename, languages_id) 
							VALUES
						('bewertungen_verwalten', 'Shop-Voting', 'products', 'bewertungen_verwalten.php', '1');");
						
			xtc_db_query("INSERT INTO admin_navigation (name, title, subsite, filename, languages_id) 
							VALUES
						('bewertungen_verwalten_config', 'Shop-Voting-Config', 'products', 'bewertungen_verwalten_config.php', '1');");
    }

    function remove() {
      xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key in ('" . implode("', '", $this->keys()) . "')");
      xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_CSEO_SHOPVOTING'");
      xtc_db_query("DELETE FROM admin_navigation WHERE name = 'bewertungen_verwalten'");
      xtc_db_query("DELETE FROM admin_navigation WHERE name = 'bewertungen_verwalten_config'");
      xtc_db_query("DELETE FROM boxes_names WHERE box_name = 'votingbox'");
      xtc_db_query("DELETE FROM boxes WHERE box_name = 'votingbox'");
      xtc_db_query("ALTER TABLE admin_access DROP bewertungen_verwalten");
      xtc_db_query("ALTER TABLE admin_access DROP bewertungen_verwalten_config");

    }
  }
?>