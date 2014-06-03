<?php
/**
 * @version $Id: ap3_import.class.php,v 1.1 2011-07-15 12:33:33 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 *
 * 16.03.2011 AB: Alle Werte auch in neuer vario_ap3 speichern, damit SALVEs diese nutzen können
 * 25.11.2010 AB: Bruttofeldnamen bei AP3 falsch gebildet
 * 19.09.2010 AB: netto/brutto bei Runden aus der Gruppe beachten
 * 27.03.2010 AB: Umstellung auf WEBSHOP_ID
 * 
 */
	class ap3_import extends vario_import {

		var $products_id, $quantity, $personal_offer;
		
		function ap3_import($exp_input){
			_debug($exp_input, 'START ap3_import -- Übergeben wurde der Datensatz $exp_input');
			$this->set_exp_source($exp_input);
			
			$this->products_id = $this->get_exp_value("WEBSHOP_ID");
			
			$customers_status_ids = get_all_customers_status_ids();
			if (is_array($customers_status_ids)) {
				foreach ($customers_status_ids as $customer_status_id) {
					
					// Tabellennamen für diese Kundengruppe
					$TABLE_PO_BY_CS = TABLE_PERSONAL_OFFERS_BY.$customer_status_id;
				
					// $this->products_id = $this->get_exp_value("WEBSHOP_ID");

					// Da die Tabelle TABLE_PERSONAL_OFFERS_BY eine Autowert hat, alle vorher löschen
					$sql = "DELETE FROM `$TABLE_PO_BY_CS` WHERE `products_id`='{$this->products_id}'";
					vDB::query($sql);
				
					// i == VARIO PREISGRUPPE: in der EXP stehen bis zu 16 Preise 
					// VARIO_PG=2 -> Händler 
					// Eintrag aus der configure: xtc-Gruppe=3 wird der VARIO-Preisgruppe=2 zugeordnet

					// Welcher Preis aus der AP3 ist dieser Kundengruppe zugeordnet?
					// In $configuration_key[1] steht die zugeordnete VARIO Preisgruppe
					$sql = 
						"select " 
						."  SUBSTRING(c.configuration_key, 10) as PREISGRUPPE "
						." from customers_status s "
						." left join configuration c on c.configuration_value = s.customers_status_name and c.configuration_key LIKE 'VARIO_PG=%' "
						." where s.customers_status_id = ".$customer_status_id." and s.language_id = 2 ";
					
					$preisgruppe = vDB::fetchone($sql);
					
					if ($preisgruppe > 1 and $preisgruppe <= 17 ) {
						
						// Entscheiden, ob Brutto oder netto gerundet werden soll, 1: Preis ist Bruttogruppe
						$brutto2netto = vDB::fetchone("select customers_status_show_price_tax from ".TABLE_CUSTOMERS_STATUS." where customers_status_id = $customer_status_id");
						
						if ($brutto2netto == 0) {	
							$this->personal_offer = $this->get_exp_value('VK'.$preisgruppe);
						} else {
	    		    		$this->personal_offer 	= $this->calculate_price_with_vario_preiseinheit(
														$this->getNettoPrice($this->get_exp_value('VK'.$preisgruppe.'BRUTTO'), 
														$this->get_exp_value('MWSTSATZ')));
						}
						_debug($this->personal_offer, '      ap3_import -- this->personal_offer');
						
						if ($this->personal_offer) {	// Ist da überhaupt was drin? 
						
							$this->setField($TABLE_PO_BY_CS, 'products_id', $this->products_id);
							$this->setField($TABLE_PO_BY_CS, 'quantity', 	1);
							$this->setField($TABLE_PO_BY_CS, 'personal_offer', $this->personal_offer);

							$this->assign_field_values();
							$this->do_SQL(array($TABLE_PO_BY_CS=>$structures[$TABLE_PO_BY_CS]));
							
						} // end if
					} // end if
					
				} // end foreach
			} // end if

    		
			_debug('', ' ENDE ap3_import --');
		} // function
		
	}
?>
