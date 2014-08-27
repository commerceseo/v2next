<?php
/**
 * @version $Id: hst_import.class.php,v 1.1 2011-07-15 12:33:34 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 */

 	class hst_import extends vario_import {
		
		function hst_import($exp_input){
			$queryStartTime = array_sum(explode(" ",microtime()));
			_debug($exp_input, "START hst_import -- Übergeben wurde der Datensatz $exp_input um $queryStartTime.");
			$this->set_exp_source($exp_input);
			
			$this->set_fields( array(
				// TABLE_MANUFACTURES							
    	        'ID'				=> array (
	    	            					create_field_info(TABLE_MANUFACTURERS, 		'manufacturers_id'),
	    	            					create_field_info(TABLE_MANUFACTURERS_INFO, 'manufacturers_id'),
                							),
				'HERSTELLER'		=> array(create_field_info(TABLE_MANUFACTURERS, 'manufacturers_name')),
				'HERSTELLERBILD'	=> array(create_field_info(TABLE_MANUFACTURERS, 'manufacturers_image')),
  	            'ANG_AM'			=> array(create_field_info(TABLE_MANUFACTURERS, 'date_added', 	'vario_date_to_xtc_date')),
    	       	'GEA_AM'			=> array(create_field_info(TABLE_MANUFACTURERS, 'last_modified', 'vario_date_to_xtc_date')),
				// TABLE_MANUFACTURES_INFO							
				'HERSTELLERURL'		=> array(create_field_info(TABLE_MANUFACTURERS_INFO, 'manufacturers_url')),
            ));

			$this->assign_field_values();
			$this->do_SQL(array(TABLE_MANUFACTURERS=>$this->import[TABLE_MANUFACTURERS])); // Kategorien werden angelegt, permissions gesetzt
			
			/**
			 *  noch nicht vorhandene Sprachen mit Systemsprache füllen, falls Shop mehr Sprachen als VARIO hat
			 */

			$languages_ids = get_all_languages_ids();
			if (is_array($languages_ids)) {
				foreach ($languages_ids as $languages_id) {
					$this->import[TABLE_MANUFACTURERS_INFO]['languages_id'] = $languages_id;
					$this->do_SQL(array(TABLE_MANUFACTURERS_INFO=>$this->import[TABLE_MANUFACTURERS_INFO]));
				}
			}
			
			$queryEndTime = array_sum(explode(" ",microtime()));
			$processTime = $queryEndTime - $queryStartTime;
			_debug('  End hst_import: '.$queryEndTime.' Duration: '.$processTime);
		}

	}
?>
