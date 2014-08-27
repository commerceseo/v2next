<?php
/**
 * @version $Id: art_xse_import.class.php,v 1.1 2011-07-15 12:33:33 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 * 
 * 09.01.2013 SV: XSE_GRP_ID hinzugeüft.
 *  * 
 * 29.06.2012 SV: VARIO übergibt TABELLE_ID und nicht ID, daher hat die Sortierung nicht funktioniert.
 * 
 * 27.03.2010 AB: Umstellung auf WEBSHOP_ID
 * 
 */
	class art_xse_import extends vario_import{

		var $products_id, $xsell_id, $sort_order, $products_xsell_grp_name_id;
		
		function art_xse_import ($exp_input) {
			_debug($exp_input, 'START art_xse__import -- Übergeben wurde der Datensatz $exp_input');
			$this->set_exp_source($exp_input);
			
			$this->products_id 	= $this->get_exp_value('WEBSHOP_ID');
			$this->xsell_id 	= $this->get_exp_value('XSE_WEBSHOP_ID');
			$this->sort_order 	= $this->get_exp_value('TABELLE_ID');
			$this->grp_id 		= $this->get_exp_value('XSE_GRP_ID');
			// $products_xsell_grp_name_id 
			
			$this->sql = "delete from ".TABLE_PRODUCTS_XSELL." where products_id = ".$this->products_id." and xsell_id = ".$this->xsell_id;
			_debug($this->sql,'      art_xse_import: DELETE');			
			xtc_db_query($this->sql);

			// *** BUG? Warum klappt das hier nicht ? ***
			//$this->set_fields( array(
			//	'WEBSHPOP_ID'		=> array(create_field_info(TABLE_PRODUCTS_XSELL, 'products_id')),
			//	'XSE_WEBSHOP_ID'	=> array(create_field_info(TABLE_PRODUCTS_XSELL, 'xsell_id')),
			//	'ID'				=> array(create_field_info(TABLE_PRODUCTS_XSELL, 'sort_order'))		// noch keine Sortorder da, ID nutzen
			//));

			$this->setField(TABLE_PRODUCTS_XSELL, 'products_id', 	$this->products_id);
			$this->setField(TABLE_PRODUCTS_XSELL, 'xsell_id', 		$this->xsell_id);
			$this->setField(TABLE_PRODUCTS_XSELL, 'sort_order', 	$this->sort_order);
			$this->setField(TABLE_PRODUCTS_XSELL, 'products_xsell_grp_name_id', $this->grp_id);
			// $products_xsell_grp_name_id 
			
			$this->assign_field_values();
			//_debug($this->import[TABLE_PRODUCTS_XSELL], '      art_xse__import -- ');
			//_debug($this->constant_fields, '      art_xse__import -- cf');
			
			$this->do_SQL(array(TABLE_PRODUCTS_XSELL=>$this->import[TABLE_PRODUCTS_XSELL])); 
			_debug('', ' ENDE art_xse_import --');
		}
					
	}
?>
