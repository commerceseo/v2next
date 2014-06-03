<?php
// Copyright (c) 2014 VARIO Software AG

include_once('../inc/xtc_db_query.inc.php');
include_once('vario7/configure_import.inc.php');

class bep_export extends vario_export {

	var $pos = int;
	
 	function bep_export($orders_products_id, $pos, $orders_id) {
		_debug($orders_id.'-'.$pos.'('.$orders_products_id.')', 'START bep_export');
 		$this->set_class_name('BEP');
		$this->set_file_name('BEP_o' . $orders_id);
		$this->pos = $pos;

		// fetch the data!
		$this->ds 	= $this->select_orders_products_data($orders_products_id);


		
		
		
		
		$fa = $this->getFakturierungsart( $orders_id );
		
		// AB: 	bei Brutto Bruttofelder füllen, bei Netto Nettofelder füllen
		// 		der Preis bei xtc ist der in der aktuellen Fakturierungsart
		
		// evtl. noch MWSTSATZ, GEWICHT, PE, GESAMTPREIS, EINZELPREIS, ME, STAFFEL, BEZEICHNUNG
		
		if ($fa != 0) {	// <> 0 ist nicht Netto, als Brutto
			$this->set_fields(
				array('export_view' =>
					array('fields' =>
						array(	
								create_field_info('orders_id', 				'BEK_EXT_REFERENZ'),
								create_field_info('orders_products_id',		'EXT_REFERENZ'),
								create_field_info('orders_id', 				'SCHLUESSEL', 		'', '$this->get_bep_schluessel'),
								create_field_info('orders_id', 				'BELEGSCHLUESSEL', 	'', 'get_belegschluessel'),
								create_field_info('orders_id', 				'BELEGNR', 			'', 'get_belegnr'),
								create_field_info('', 						'BELEGART', 		'00'),
								create_field_info('', 						'POART', 			'A'),
								create_field_info('', 						'PO', 				$pos),
								create_field_info('', 						'PE', 				'1'),
								create_field_info('products_model',			'ARTIKELNR'),
								create_field_info('products_name',			'BEZEICHNUNG', ''),
								create_field_info('LANGTEXT',				'LANGTEXT'),
								create_field_info('products_quantity',	 	'MENGE', 				'',	 	'replace_dot_to_comma'),
								create_field_info('products_price', 		'EINZELPREISBRUTTO', 	'',	 	'xtc_to_vario_float'), 							// EINZELPREIS des Artikels
								create_field_info('products_discount_made', 'RABATT', 				'0', 	'$this->select_orders_product_discount'), 		// RABATT %e, z.B. 20
								create_field_info('final_price', 			'GESAMTPREISBRUTTO',	'',		'$this->select_orders_product_final_price'), 	// GESAMTPREIS ist MENGE * (EINZELPREIS - (EINZELPREIS * (1-RABATT/100)))
								create_field_info('', 						'VERSION', 		$this->getVARIOCfgKey('VARIO_VERSION_TAG')),
								create_field_info('', 						'VERSION_INFO', VARIO_SHOP_USED),
							),
					)
				)
			);
		} else {
			$this->set_fields(
				array('export_view' =>
					array('fields' =>
						array(	
								create_field_info('orders_id', 				'BEK_EXT_REFERENZ'),
								create_field_info('orders_products_id',		'EXT_REFERENZ'),
								create_field_info('orders_id', 				'SCHLUESSEL', 		'', '$this->get_bep_schluessel'),
								create_field_info('orders_id', 				'BELEGSCHLUESSEL', 	'', 'get_belegschluessel'),
								create_field_info('orders_id', 				'BELEGNR', 			'', 'get_belegnr'),
								create_field_info('', 						'BELEGART', 		'00'),
								create_field_info('', 						'POART', 			'A'),
								create_field_info('', 						'PO', 				$pos),
								create_field_info('', 						'PE', 				'1'),
								create_field_info('products_model',			'ARTIKELNR'),
								create_field_info('products_name',			'BEZEICHNUNG'),
								create_field_info('LANGTEXT',				'LANGTEXT'),
								create_field_info('products_quantity',	 	'MENGE', 			'', 	'replace_dot_to_comma'), 					// EINZELPREIS des Artikels
								create_field_info('products_price', 		'EINZELPREIS', 		'', 	'xtc_to_vario_float'), 						// RABATT %e, z.B. 20
								create_field_info('products_discount_made', 'RABATT', 			'0', 	'$this->select_orders_product_discount'), 	// GESAMTPREIS ist MENGE * (EINZELPREIS - (EINZELPREIS * (1-RABATT/100)))
								create_field_info('final_price', 			'GESAMTPREIS', 		'', 	'$this->select_orders_product_final_price'),
								create_field_info('', 						'VERSION', 	$this->getVARIOCfgKey('VARIO_VERSION_TAG')),
								create_field_info('', 						'VERSION_INFO', VARIO_SHOP_USED),
							),
					)
				)
			);
		} // endif fa

		$this->assign_field_values($orders_id);
		$this->write_exp();
		_debug('', ' ENDE bep_export');
 	}

	
	function select_orders_product_final_price() {

		// Gesamtpreis abzüglich evtl. Rabatt
		$discount = $this->select_orders_product_discount();

		return xtc_to_vario_float( $this->ds['final_price'] * ( 1-$discount/100 ) );
	}

	
	function select_orders_product_discount() {

		/*
			Hier könnte eine mögliche Rabattierung pro Position vorgenommen werden
		*/

		return xtc_to_vario_float( 0 );
	}

	
    function select_orders_products_data($orders_products_id) {
    	// Neu: checkout_process doch unangetastet lassen und hier umständlich die Werte suchen 
    	// Es reicht hier, das erste komplette Attribut - falls vorhanden - zu holen.
    	// Durch das Ausmultiplizieren steht in jedem die richtige Artikelnummer drin
    	// in op.products_model steht bereits die korrekte Artikelnummer, case entfernt
    	//
    	_debug($orders_products_id, '--> START select_orders_products_data');

		$query = "
		select distinct
			op.orders_products_id
		  , op.orders_id
		  , op.products_id
		  , coalesce(ppc.combi_model, op.products_model) as products_model
		  , op.products_name
		  , op.products_quantity
		  , op.products_price
		  , op.products_discount_made
		  , op.final_price  
		  , op.products_tax
		  , op.allow_tax

		  , cs.customers_status_show_price_tax

		  from ".TABLE_ORDERS_PRODUCTS." op
		  left join ".TABLE_ORDERS_PRODUCTS_PROPERTIES." opp on opp.orders_products_id = op.orders_products_id
		  left join ".TABLE_PRODUCTS_PROPERTIES_COMBIS." ppc on ppc.products_properties_combis_id = opp.products_properties_combis_id

		  left join orders o on op.orders_id = o.orders_id
		  left join customers_status cs on o.customers_status = cs.customers_status_id

		 where op.orders_products_id = $orders_products_id limit 0,1";

    		
// Fehler in xtc:  In orders_products_attributes stehen evtl. Aufschläge drin, 
// die aber in orders_products schon addiert wurden
    	_debug($query, '         select_orders_products_data - sql');
    	$result = xtc_db_query($query);
    	$data = mysql_fetch_assoc($result);
    		
    		
    	// bei unmodifiziertem Shop mit nicht gepflegten Attribut-Artikelnummern 
    	// diese in das LANGTEXT-Feld der Belegposition schreiben
    	if ( VARIO_FUELLE_BEP_LANGTEXT == 1 ) {
    	
		    $query = "
select opa.products_options, opa.products_options_values
  from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa 
 where opa.orders_products_id = $orders_products_id";
	    	
	    	_debug($query, '         select_orders_products_data - att');
		    $result = xtc_db_query($query);
		    $langtext = '';
			while ($att_data = xtc_db_fetch_array($result, true)) { 
				$zeile  = $att_data['products_options'].': ';
				$zeile .= $att_data['products_options_values'].' ';
				$zeile .= chr(13) . chr(10);
				$langtext .= $zeile; 
			}   	
			$data['LANGTEXT'] = $langtext; 	
	    		
    	}
    	
    	_debug($data, '--> ENDE select_orders_products_data');
        return $data;
    }
	
    function get_bep_schluessel($nr) {
		$return = get_belegschluessel($nr) . sprintf("%06d", $this->pos);
		return $return;
	}
	
}
?>
