<?php
/**
 * @version $Id: ap5_import.class.php,v 1.1 2011-07-15 12:33:33 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 * 
 * 25.11.2010 AB: Bruttofeldnamen bei AP5 falsch gebildet
 * 19.09.2010 AB: netto/brutto bei Runden aus der Gruppe beachten
 * 27.03.2010 AB: Umstellung auf WEBSHOP_ID
 *
 */
	class ap5_import extends vario_import {

		var $products_id, $quantity, $price_id, $personal_offer, $preisgruppe;
		
		function ap5_import($exp_input){
			_debug($exp_input, 'START ap5_import -- Übergeben wurde der Datensatz $exp_input');
			$this->set_exp_source($exp_input);
			
			$customers_status_ids = get_all_customers_status_ids();
			_debug($customers_status_ids, '      ap5_import -- customers_status_ids');
			if (is_array($customers_status_ids)) {
				foreach ($customers_status_ids as $customer_status_id) {
				
					$TABLE_PO_BY_CS = TABLE_PERSONAL_OFFERS_BY.$customer_status_id;
				
					$this->price_id 	= $this->get_exp_value("ID");
					$this->products_id 	= $this->get_exp_value("WEBSHOP_ID");
					$this->preisgruppe 	= $this->get_exp_value("PREISGRUPPE");
					
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
										
					if ($preisgruppe == $this->preisgruppe) {
						
						// Entscheiden, ob Brutto oder netto gerundet werden soll, 1: Preis ist Bruttogruppe
						$brutto2netto = vDB::fetchone("select customers_status_show_price_tax from ".TABLE_CUSTOMERS_STATUS." where customers_status_id = $customer_status_id");
						
						$sql = "DELETE FROM `$TABLE_PO_BY_CS` WHERE `products_id`='{$this->products_id}'";
						vDB::query($sql);
						_debug($sql, '      ap5_import -- SQL');
						
						for ($i=2; $i<=17; $i++) {	// 16 Mengen für diese Preisgruppe
							
							$this->quantity = $this->get_exp_value('MENGE'.$i);
							//_debug($this->$quantity, '      ap5_import -- quantity');
							if ($this->quantity > 0) {
						
								if ($brutto2netto == 0) {	
									$this->personal_offer = $this->get_exp_value('VK'.$i);
								} else {
			    		    		$this->personal_offer 	= $this->calculate_price_with_vario_preiseinheit(
																$this->getNettoPrice($this->get_exp_value('VK'.$i.'BRUTTO'), 
																$this->get_exp_value('MWSTSATZ')));
								}
								
								$this->setField($TABLE_PO_BY_CS, 'products_id', 	$this->products_id);
								$this->setField($TABLE_PO_BY_CS, 'quantity', 		$this->quantity);
								$this->setField($TABLE_PO_BY_CS, 'personal_offer', 	$this->personal_offer);

								$this->assign_field_values();
								$this->do_SQL(array($TABLE_PO_BY_CS=>$structures[$TABLE_PO_BY_CS]));
							} // if
						} // for
					} // end if
				} // if
			} // if
			_debug('', ' ENDE ap5_import --');
		} // function
		
	}
?>
