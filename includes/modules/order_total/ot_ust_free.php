<?php
/* --------------------------------------------------------------
   ot_tax.php 2008-09-08
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_tax.php,v 1.14 2003/02/14); www.oscommerce.com  
   (c) 2003	 nextcommerce (ot_tax.php,v 1.11 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_tax.php 1002 2005-07-10 16:11:37Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  class ot_ust_free {
    var $title, $output;
    function ot_ust_free() {
    	global $xtPrice;
		
      $this->code = 'ot_ust_free';
      $this->title = MODULE_ORDER_TOTAL_UST_FREE_TITLE;
      $this->description = MODULE_ORDER_TOTAL_UST_FREE_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_UST_FREE_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_UST_FREE_SORT_ORDER;
      $this->output = array();
    }

    function process() {
		$this->output[] = array('title' => MODULE_ORDER_TOTAL_UST_FREE_TEXT,
								'text' =>"",
								'value' => "");   
	}

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_ORDER_TOTAL_UST_FREE_STATUS';");
        $this->_check = xtc_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_UST_FREE_STATUS', 'MODULE_ORDER_TOTAL_UST_FREE_SORT_ORDER');
    }

    function install() {
      xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_ORDER_TOTAL_UST_FREE_STATUS', 'true', '6', '0','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_ORDER_TOTAL_UST_FREE_SORT_ORDER', '98', '6', '2', now())");
    }

    function remove() {
      xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
    }
  }
  
