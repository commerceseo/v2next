<?php

/* -----------------------------------------------------------------
 * 	$Id: mailbeez.php 844 2014-02-04 15:26:56Z akausch $
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

class mailbeez {

    var $title, $output;

    function mailbeez() {
        $this->code = 'mailbeez';
        $this->version = '2.5';
        $this->title = 'mailbeez';
        $this->description = 'Mailbeez für commerce:SEO v2.5';
        $this->enabled = ((MODULE_CSEO_MAILBEEZ_STATUS == 'true') ? true : false);
        $this->sort_order = MODULE_CSEO_MAILBEEZ_SORT_ORDER;

        $this->output = array();
    }

    function process() {
        global $order, $xtPrice;
    }

    function check() {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_CSEO_MAILBEEZ_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }

        return $this->_check;
    }

    function keys() {
        return array('MODULE_CSEO_MAILBEEZ_STATUS', 'MODULE_CSEO_MAILBEEZ_SORT_ORDER');
    }

    function install() {
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_CSEO_MAILBEEZ_STATUS', 'true', '6', '1','', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) VALUES ('MODULE_CSEO_MAILBEEZ_SORT_ORDER', '1','6', '2', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) VALUES ('MODULE_CSEO_MAILBEEZ', 'true','6', '3', now())");

        if (column_exists('admin_access', 'mailbeez') == false) {
            xtc_db_query("ALTER TABLE admin_access ADD mailbeez INT( 1 ) NOT NULL DEFAULT 0;");
            xtc_db_query("UPDATE admin_access SET mailbeez = '1' WHERE module_newsletter = 1;");
        }

        xtc_db_query("INSERT INTO admin_navigation (name, title, subsite, filename, languages_id) 
						VALUES
					('mailbeez', 'Mailbeez', 'tools', 'mailbeez.php', '2');");

        xtc_db_query("INSERT INTO admin_navigation (name, title, subsite, filename, languages_id) 
						VALUES
					('mailbeez', 'Mailbeez', 'tools', 'mailbeez.php', '1');");
    }

    function remove() {
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key in ('" . implode("', '", $this->keys()) . "')");
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_CSEO_MAILBEEZ'");
        xtc_db_query("DELETE FROM admin_navigation WHERE name = 'mailbeez'");
        xtc_db_query("ALTER TABLE admin_access DROP mailbeez");
    }

}
