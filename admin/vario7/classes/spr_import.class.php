<?php
/**
 * @version $Id: spr_import.class.php,v 1.1 2011-07-15 12:33:34 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2005 VARIO Software GmbH
 * 
 * VARIO.SPR:			ID	SPRACHKUERZEL	SPRACHE
 * xtc.languages:		languages_id  name  code  image  directory  sort_order  language_charset  
 * xtc.configuration 	configuration_id  configuration_key  configuration_value  configuration_group_id  sort_order  last_modified  date_added  use_function  set_function 
 * 
 * Aufgabe: VARIO-Sprachen in configuration eintragen
 *   Bsp.:	VARIO_SPRACH_ID=12 -> en
 * 
 */
	class spr_import extends vario_import{
		
		function spr_import($exp_input){
			_debug($exp_input);
			
			$this->set_exp_source($exp_input);
			
			$configuration_key 		= 'VARIO_SPRACH_ID='.$this->get_exp_value('ID');		// VARIO_SPRACH_ID=12
			$configuration_value 	= strtolower($this->get_exp_value('SPRACHKUERZEL'));	// der SHOP will Kleinbuchstaben
			
			$sql = "select configuration_id from ".TABLE_CONFIGURATION." where configuration_value = '".$configuration_value."'";
			_debug($sql,'      spr_import - select');			
			$configuration_id = vDB::fetchone($sql);
			//_debug($configuration_id,'      spr_import - return');			
			
			$this->setField(TABLE_CONFIGURATION, 'configuration_id', $configuration_id);
			$this->setField(TABLE_CONFIGURATION, 'configuration_key', $configuration_key);
			$this->setField(TABLE_CONFIGURATION, 'configuration_value', $configuration_value);
			$this->setField(TABLE_CONFIGURATION, 'configuration_group_id', 91);
			
			$this->assign_field_values();
			$this->do_SQL(array(TABLE_CONFIGURATION=>$this->import[TABLE_CONFIGURATION])); // Kategorien werden angelegt, permissions gesetzt
		}
		
	}
?>
