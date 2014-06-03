<?php
/**
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 * 
 * 27.07.2012 SV: ap6 Import (Kopie von AP5)
 *
 */
	class ap6_import extends vario_import {

		var $products_id, $quantity, $price_id, $personal_offer, $preisgruppe;
		
		function ap6_import($exp_input){
			_debug($exp_input, 'START ap6_import -- Übergeben wurde der Datensatz $exp_input');
			$this->set_exp_source($exp_input);
			
			$customers_status_ids = get_all_customers_status_ids();
			_debug($customers_status_ids, '      ap6_import -- customers_status_ids');
			if (is_array($customers_status_ids)) {
				foreach ($customers_status_ids as $customer_status_id) {
				
					$TABLE_PO_BY_CS = TABLE_PERSONAL_OFFERS_BY.$customer_status_id;
				
					$this->price_id 	= $this->get_exp_value("ID");
					$this->products_id 	= $this->get_exp_value("WEBSHOP_ID");
					$this->preisgruppe 	= $this->get_exp_value("PREISGRUPPE");
					
					// i == VARIO PREISGRUPPE: in der EXP stehen bis zu 16 Preise 
					// VARIO_PG=2 -> Händler 
					// Eintrag aus der configure: xtc-Gruppe=3 wird der VARIO-Preisgruppe=2 zugeordnet

					// In $configuration_key[1] steht die zugeordnete VARIO Preisgruppe
					$sql = 
						"select " 
						."  SUBSTRING(c.configuration_key, 10) as PREISGRUPPE "
						." from customers_status s "
						." left join configuration c on c.configuration_value = s.customers_status_name and c.configuration_key LIKE 'VARIO_PG=%' "
						." where s.customers_status_id = ".$customer_status_id." and s.language_id = 2 ";
					
					$preisgruppe = vDB::fetchone($sql);
										
					if ($preisgruppe == $this->preisgruppe) {
						

						$sql = "DELETE FROM `$TABLE_PO_BY_CS` WHERE `products_id`='{$this->products_id}'";
						vDB::query($sql);
						_debug($sql, '      ap6_import -- SQL');
						
						for ($i=2; $i<=17; $i++) {	// 16 Mengen für diese Preisgruppe
							
							$this->quantity = $this->get_exp_value('MENGE'.$i);
							//_debug($this->$quantity, '      ap6_import -- quantity');
							if ($this->quantity > 0) {

			    		    		$this->personal_offer 	= $this->calculate_price_with_vario_preiseinheit(
																$this->getNettoPrice($this->get_exp_value('VK'.$i), 
																$this->get_exp_value('MWSTSATZ')));

								
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
			_debug('', ' ENDE ap6_import --');
		} // function
		
	}
?>
