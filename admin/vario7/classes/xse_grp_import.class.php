<?php
/**
 * @copyright Copyright (c) 2013 VARIO Software AG  */

 	class xse_grp_import extends vario_import {
		
		function xse_grp_import($exp_input){

			$queryStartTime = array_sum(explode(" ",microtime()));
			_debug($exp_input, "START xse_grp_import -- Übergeben wurde der Datensatz $exp_input um $queryStartTime.");
			$this->set_exp_source($exp_input);

			$SprachID = $this->get_exp_value('SPRACH_ID');

			if( empty($SprachID) ){

				$this->setField(TABLE_PRODUCTS_XSELL_GROUPS, 'language_id', vario_sprach_id_to_xtc_languages_id( get_vario_default_language_id() ));
			} else {

				$this->setField(TABLE_PRODUCTS_XSELL_GROUPS, 'language_id', vario_sprach_id_to_xtc_languages_id( $this->get_exp_value('SPRACH_ID') ));
			}

			$this->set_fields( array(
    	        'WEBSHOP_ID'		=> array(create_field_info(TABLE_PRODUCTS_XSELL_GROUPS, 'products_xsell_grp_name_id'	)),
				'SORTIERUNG'		=> array(create_field_info(TABLE_PRODUCTS_XSELL_GROUPS, 'xsell_sort_order'				)),
  	            'BEZEICHNUNG'		=> array(create_field_info(TABLE_PRODUCTS_XSELL_GROUPS, 'groupname'						))
            ));

			$this->assign_field_values();

			$this->do_SQL(array(TABLE_PRODUCTS_XSELL_GROUPS=>$this->import[TABLE_PRODUCTS_XSELL_GROUPS]));

			$queryEndTime = array_sum(explode(" ",microtime()));
			$processTime = $queryEndTime - $queryStartTime;
			_debug('  End xse_grp_import: '.$queryEndTime.' Duration: '.$processTime);
		}

	}
?>
