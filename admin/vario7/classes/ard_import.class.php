<?php
/**
 * @version $Id: ard_import.class.php,v 1.1 2011-07-15 12:33:33 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2005 VARIO Software GmbH
 */
 
// 24.10.2012 sv: Beim Gambio GX2 muss man nun angeben, dass eine Datei für alle Kundengruppen Sichtbar ist.

	class ard_import extends vario_import{
		
		var $action, $products_id, $products_attributes_filename, $sql, $content_id;
		
		// content_id  products_id  group_ids  content_name  content_file  content_link  languages_id  content_read  file_comment  
		
		function ard_import($exp_input){
			_debug($exp_input);
			$this->set_exp_source($exp_input);

			$this->action 		= $this->get_exp_value('AKTION');
			$this->content_id	= $this->get_exp_value('ID');
			//if ($this->action <> 'D') {						
				_debug($this->action,'      ard_import: INSERT/UPDATE');			
				$this->set_fields( array(
					// 'ID'				=> array(create_field_info(TABLE_PRODUCTS_CONTENT, 'content_id')),
					'WEBSHOP_ID'		=> array(create_field_info(TABLE_PRODUCTS_CONTENT, 'products_id')),
					'LINK_TEXT'			=> array(create_field_info(TABLE_PRODUCTS_CONTENT, 'content_name')),
					'DATEINAME_KURZ'	=> array(create_field_info(TABLE_PRODUCTS_CONTENT, 'content_file')),
					'BEMERKUNG'			=> array(create_field_info(TABLE_PRODUCTS_CONTENT, 'file_comment')),
				));

				$this->setField(TABLE_PRODUCTS_CONTENT, 'group_ids', 'c_all_group'); // Erlaubt für alle Kundengruppen

				$this->assign_field_values();
				$languages_ids = get_all_languages_ids();
				if (is_array($languages_ids)) {
					foreach ($languages_ids as $languages_id) {
						$this->import[TABLE_PRODUCTS_CONTENT]['content_id'] 	= $languages_id * 100 + $this->content_id;
						$this->import[TABLE_PRODUCTS_CONTENT]['languages_id'] 	= $languages_id;
						$this->do_SQL(array(TABLE_PRODUCTS_CONTENT=>$this->import[TABLE_PRODUCTS_CONTENT]));
					}
				}
				_debug('', ' ENDE ard_import --');
		
			//} else {
			//	$this->products_id 					= $this->get_exp_value('WEBSHOP_ID');
			//	$this->products_attributes_filename = $this->get_exp_value('DATEINAME_KURZ');		
			//	$this->sql = "delete from ".TABLE_PRODUCTS_CONTENT." where products_id = ".$this->products_id." and products_attributes_filename = '".$this->products_attributes_filename."'";
			//	_debug($this->sql,'      ard_import: DELETE');			
			//	xtc_db_query($this->sql);
			//}
		}
		
	}

?>
