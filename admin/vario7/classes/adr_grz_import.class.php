<?php
/**
 * @version $Id: adr_grz_import.class.php,v 1.1 2011-07-15 12:33:33 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 */

	class adr_grz_import extends vario_import {
		
		function adr_grz_import($exp_input){
			_debug($exp_input, 'START adr_grz_import -- Übergeben wurde der Datensatz $exp_input');
			$this->set_exp_source($exp_input);
			
			$this->setAction('U');	// DELETE übersteuern! Hintergrund: Feldliste!
			
			$this->set_fields(array(
				'ABC_KENNUNG'		=> array(create_field_info(TABLE_CUSTOMERS_STATUS, 'customers_status_id', 'customers_status_name_to_customers_status_id')),
				'R_CSV_FELDLISTE' 	=> array(create_field_info(TABLE_CUSTOMERS_STATUS, 'customers_status_payment_unallowed')),
			));

			$this->assign_field_values();
			// $this->do_SQL(array(TABLE_CUSTOMERS_STATUS=>$this->import[TABLE_CUSTOMERS_STATUS])); 
			// für alle Sprachen gleicher Eintrag			
			$languages_ids = get_all_languages_ids();
			if (is_array($languages_ids)) {
				foreach ($languages_ids as $languages_id) {
					$this->import[TABLE_CUSTOMERS_STATUS]['language_id'] = $languages_id;
					$this->do_SQL(array(TABLE_CUSTOMERS_STATUS=>$this->import[TABLE_CUSTOMERS_STATUS]));
				}
			}
			_debug('', ' ENDE adr_grz_import --');
		}

	}
?>
