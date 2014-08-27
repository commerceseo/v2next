<?php
/**
 * @version $Id: ap1_import.class.php,v 1.1 2011-07-15 12:33:33 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 * 
 * TODO: 
 * 
 *	1. Artikel "mengenabhängige VK's"/AP1 mit xtc.personal_offers_by_customers_status_{ALLE} Tabellen mappen, quantity=MENGE{X}.
 *
 * 20.12.2010 AB: v7-1-1-002 VKxBRUTTO Preisfeld falsch gebaut
 * 19.09.2010 AB: netto/brutto bei Runden aus der Gruppe beachten
 * 27.03.2010 AB: Umstellung auf WEBSHOP_ID
 *
 */

	class ap1_import extends vario_import {

		var $products_id, $quantity, $personal_offer;
		
		function ap1_import($exp_input){
			_debug($exp_input, 'START ap1_import -- Übergeben wurde der Datensatz $exp_input');
			$this->set_exp_source($exp_input);
			
			$customers_status_ids = get_all_customers_status_ids();
			if (is_array($customers_status_ids)) {
			  // AP1: Preise für alle Gruppen gleich, Preis deshalb für alle gleich eintragen  
			  foreach ($customers_status_ids as $customer_status_id) {
				
				// Tabellennamen für diese Kundengruppe
			  	$TABLE_PO_BY_CS = TABLE_PERSONAL_OFFERS_BY.$customer_status_id;
				
				$this->products_id = $this->get_exp_value("WEBSHOP_ID");
				
				// Da die Tabelle TABLE_PERSONAL_OFFERS_BY einen Autowert hat, alle vorher löschen
				$sql = "DELETE FROM `$TABLE_PO_BY_CS` WHERE `products_id`='{$this->products_id}'";
				vDB::query($sql);
				_debug($sql, '      ap1_import -- SQL');
				
				// Entscheiden, ob Brutto oder netto gerundet werden soll, 1: Preis ist Bruttogruppe
				$brutto2netto = vDB::fetchone("select customers_status_show_price_tax from ".TABLE_CUSTOMERS_STATUS." where customers_status_id = $customer_status_id");
						
				for ($preisgruppe=2; $preisgruppe<=17; $preisgruppe++) {	// 16 Mengen
					
					$this->quantity = $this->get_exp_value('MENGE'.$preisgruppe);
					//_debug($this->$quantity, '      ap1_import -- quantity');
					if ($this->quantity > 0) {
						
						if ($brutto2netto == 0) {	
							$this->personal_offer = $this->get_exp_value('VK'.$preisgruppe);
						} else {
	    		    		$this->personal_offer 	= $this->calculate_price_with_vario_preiseinheit(
														$this->getNettoPrice($this->get_exp_value('VK'.$preisgruppe.'BRUTTO'), 
														$this->get_exp_value('MWSTSATZ')));
						}
						$this->setField($TABLE_PO_BY_CS, 'products_id', 	$this->products_id);
						$this->setField($TABLE_PO_BY_CS, 'quantity', 		$this->quantity);
						$this->setField($TABLE_PO_BY_CS, 'personal_offer', 	$this->personal_offer);

						$this->assign_field_values();
						$this->do_SQL(array($TABLE_PO_BY_CS=>$structures[$TABLE_PO_BY_CS]));
					} // if
				} // for
			  } // foreach
			} // if
			_debug('', ' ENDE ap1_import --');
		} // function
		
	}
?>
