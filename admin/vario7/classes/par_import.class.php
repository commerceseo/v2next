<?php
/**
 * @version $Id: par_import.class.php,v 1.1 2011-07-15 12:33:34 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2005 VARIO Software GmbH
 */
	class par_import extends vario_import {
		
		function par_import($exp_input){
			_debug($exp_input, 'START par_import -- Übergeben wurde der Datensatz $exp_input');
			$this->set_exp_source($exp_input);
	
			// KAPITEL: Preisgruppennamen START
			for ($i=2; $i<=17; $i++) {	// 16 Mengen
				$configuration_key 		= 'VARIO_PG='.$i;						// VARIO_PG=2
				$configuration_value 	= $this->get_exp_value('BESCHRIFTUNGP'.$i);	// z.B. Händler
			
				$sql = "select configuration_id from ".TABLE_CONFIGURATION." where configuration_key = '".$configuration_key."'";
				_debug($sql,'      par_import - select');			
				$configuration_id = vDB::fetchone($sql);
				//_debug($configuration_id,'      par_import - return');			
				
				$this->setField(TABLE_CONFIGURATION, 'configuration_id', $configuration_id);
				$this->setField(TABLE_CONFIGURATION, 'configuration_key', $configuration_key);
				$this->setField(TABLE_CONFIGURATION, 'configuration_value', $configuration_value);
				$this->setField(TABLE_CONFIGURATION, 'configuration_group_id', 91);
				
				$this->assign_field_values();
				$this->do_SQL(array(TABLE_CONFIGURATION=>$this->import[TABLE_CONFIGURATION]));
			
			}
			// KAPITEL: Preisgruppennamen ENDE
			
			_debug('', ' ENDE par_import --');
		} 
	}
?>
