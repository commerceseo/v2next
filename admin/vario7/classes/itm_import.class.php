<?php
/**
 * @version $Id: itm_import.class.php,v 1.1 2011-07-15 12:33:34 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2005 VARIO Software GmbH
 * 
 * 10.07.2012 SV
 *   Prüfung vor Insert ob Kombination aus products_id und categories_id bereits existiert
 * 28.09.2010 AB
 *   Sortierung in die products schrteiben
 * 
 */
class itm_import extends vario_import {

	var $action, $products_id, $categories_id, $products_sort, $sql;
			
	function itm_import($exp_input) {
		_debug($exp_input, 'START itm_import -- Übergeben wurde der Datensatz $exp_input');
		$this->set_exp_source($exp_input);

		$this->action 			= trim($this->get_exp_value('AKTION'));	// geht nur so, da der auto-SQL kein zus.gesetzten Key kann
		$this->categories_id 	= trim($this->get_exp_value('KAT_ID'));
		$this->products_id 		= trim($this->get_exp_value('WEBSHOP_ID'));
		$this->products_sort	= trim($this->get_exp_value('SORTIERUNG'));
		if ($this->action <> 'D') {						
			_debug($this->action,'      itm_import: INSERT/UPDATE');

			if ($this->categories_id > 0 && $this->products_id > 0) { 

				if( vDB::num_rows(vDB::query( "SELECT * 
												 FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " 
												WHERE categories_id = ".$this->categories_id." 
												  AND products_id   = ".$this->products_id ) ) == 0 ){

					$this->sql = "insert into ".TABLE_PRODUCTS_TO_CATEGORIES." (products_id, categories_id) values (".$this->products_id.", ".$this->categories_id.")";
					$GLOBALS['affected_rows'][TABLE_PRODUCTS_TO_CATEGORIES]['INSERT'] += 1;
				} else {

					_debug( "Kombination aus Categories_id " . $this->categories_id . " und Products_id " . $this->products_id . " existiert bereits." , 'Achtung');
				}
			}
			$qry = vDB::query($this->sql);
			_debug($this->sql, ' ENDE itm_import --');
			
			$this->sql = "update ".TABLE_PRODUCTS." set products_sort = $this->products_sort where products_id = $this->products_id"; 
			$GLOBALS['affected_rows'][TABLE_PRODUCTS]['UPDATE'] += 1;
			$qry = vDB::query($this->sql);
			_debug($this->sql, ' ENDE itm_import --');
		
		} else {
			_debug($this->action,'      itm_import: DELETE');			
			if ($this->categories_id == -1) {		
				// nicht gesetzte KAT_ID sorgt für Löschung aller products_ids vor Neu-Einfügung
				$this->sql = "delete from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id = ".$this->products_id;
			} else {
				$this->sql = "delete from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id = ".$this->products_id." and categories_id = ".$this->categories_id;
			}
			$qry = vDB::query($this->sql);
			$affected_rows = 1;
			if (is_array($qry)) { 
				$affected_rows = vDB::num_rows($qry);
			}
			$GLOBALS['affected_rows'][TABLE_PRODUCTS_TO_CATEGORIES]['DELETE'] += $affected_rows;			
			_debug($this->sql,'      itm_import: DELETE');
		}
	}	
}
?>
