<?php
// opyright (c) 2014 VARIO Software AG

include_once('../includes/classes/xtcPrice.php');

class art_import extends vario_import {

	var	$products_id;
	var $parent_id;
	var $languages_ids = array();
	
	function art_import($exp_input) {

		$this->products_id	= -1;
		$this->parent_id	= -1;
		$aktion				= 'U';
		
		$queryStartTime = array_sum(explode(" ",microtime()));
		//_debug($exp_input, "START art_import -- Übergeben wurde der Datensatz exp_input um $queryStartTime.");
		
		$this->set_exp_source($exp_input);
		_debug($this->exp_source, '      art_import -- $exp_source');
		
		// ein paar Initialwerte
		$this->products_id 	= $this->get_exp_value('WEBSHOP_ID');	// 
		$this->parent_id 	= $this->get_exp_value('MASTER_WEBSHOP_ID');	// PARENT_ID? falls > 0, dann Attribut-Werte setzen
		$action 			= $this->get_exp_value('AKTION');		// für später mal
		$this->artikelart	= $this->get_exp_value('ARTIKELART');	// 
		$internetjn			= $this->get_exp_value('INTERNETJN');
		$products_price 	= $this->calculate_price_with_vario_preiseinheit(
											art_import::getNettoPrice($this->get_exp_value('VKBRUTTO'), 
											$this->get_exp_value('MWSTSATZ')));
		$products_quantity	= $this->get_exp_value(VARIO_ARIKELANZAHL_FELD);
		$products_vpe_id	= $this->get_products_vpe_id($this->get_exp_value('GRUNDEINHEIT'));
		$this->languages_ids = get_all_languages_ids();		// die werden evtl. noch gebraucht

		// Highlight: Je Nach Version 1/0 oder J/N
		$highlight = $this->get_exp_value('HIGHLIGHT');
		if ( VARIO_PRODUCT_USED >= 'VARIO7.1' ) {
			$highlight = vario_bool_to_xtc_bool($highlight);	// J/N --> 1/0
  		}
		
		// Über die Artikelart 1,2,3,P,V und parent_id  TABLE_PRODUCTS_ATTRIBUTES ansteuern
		// falls = V, dann Attributnamen eintragen

		if ($action <> 'D') {						// für später mal
			if (!$this->parent_id || VARIO_KEINE_SLAVE_ATTRIBUTE == 1) {
				// MASTER, Einzelartikel 	
						
				_debug(VARIO_KEINE_SLAVE_ATTRIBUTE, '      art_import -- Feldwerte setzen ..');
				$this->set_fields( array (
			
    	        	'WEBSHOP_ID'			=> array(
	    	            							create_field_info(TABLE_PRODUCTS, 				'products_id'),
    	    	        							create_field_info(TABLE_SPECIALS, 				'products_id'),
            		    							create_field_info(TABLE_PRODUCTS_ATTRIBUTES, 	'products_id') ),
		            'EANNR'					=> array(create_field_info(TABLE_PRODUCTS, 				'products_ean')),
					VARIO_ARIKELANZAHL_FELD	=> array(create_field_info(TABLE_PRODUCTS, 				'products_quantity', '$this->getProductsQuantity')),
	        	    'WEBSHOP_STATUS'		=> array(create_field_info(TABLE_PRODUCTS, 				'products_shippingtime')),
    	        	'ARTIKELNR'				=> array(create_field_info(TABLE_PRODUCTS, 				'products_model')),
    		        'GEA_AM'				=> array(create_field_info(TABLE_PRODUCTS, 				'products_last_modified',	'vario_date_to_xtc_date') ),
		            'AKTION_RABATT' 		=> array(create_field_info(TABLE_PRODUCTS, 				'products_discount_allowed') ),
    		        'GEWICHT'				=> array(create_field_info(TABLE_PRODUCTS, 				'products_weight')),
    	    	    'INTERNETJN' 			=> array(create_field_info(TABLE_PRODUCTS, 				'products_status', 			'vario_bool_to_xtc_bool') ),
        	    	'MWSTSATZ'				=> array(create_field_info(TABLE_PRODUCTS, 				'products_tax_class_id')),
		            'ARTIKELDETAILS' 		=> array(create_field_info(TABLE_PRODUCTS, 				'product_template')),
    		        'ARTIKELOPTIONEN'		=> array(create_field_info(TABLE_PRODUCTS, 				'options_template') ),
        		    'FSK18'					=> array(create_field_info(TABLE_PRODUCTS, 				'products_fsk18', 			'vario_bool_to_xtc_bool') ),
	    	        'VPE_ANZEIGEN' 			=> array(create_field_info(TABLE_PRODUCTS, 				'products_vpe_status', 		'vario_bool_to_xtc_bool')),
		            'GRUNDEINHEIT_FAKTOR'   => array(create_field_info(TABLE_PRODUCTS, 				'products_vpe_value', 		'$this->getVpeValue')),
        		    'HIGHLIGHT_POSITION'	=> array(create_field_info(TABLE_PRODUCTS, 				'products_startpage_sort') ),

                ));

		        $this->setField(TABLE_PRODUCTS, 'products_price', 	  $products_price);		// korrekt berechneter Preis
		        $this->setField(TABLE_PRODUCTS, 'products_vpe', 	  $products_vpe_id);	// Mengeneinheit
		        $this->setField(TABLE_PRODUCTS, 'products_startpage', $highlight);
		        
				// Neue Herstellerbehandlung ab VARIO7.1
				if ( VARIO_PRODUCT_USED >= 'VARIO7.1' ) {
					// neu: Hersteller-ID jetzt auch ab VARIO 7.1
					$hersteler_id = $this->get_exp_value('HST_WEBSHOP_ID');
  				} else {
  					// bisher: Hersteller dynamisch anlegen 
					$hersteller   = $this->get_exp_value('HERSTELLER');
					$hersteler_id = $this->getManufacturersId($hersteler);
  				}
  				// die ID ist für alle gleich
  				$this->setField(TABLE_PRODUCTS, 'manufacturers_id', $hersteler_id);
		        
  				//_debug($this->fields, '      art_import -- $this->fields');
	    	    // Ein bisschen Mist von osCommerce abfangen: Dynamsiche Felder für Kunde/Preisgruppen
				// VARIO hat noch keine Verwaltung dafür, so dass alle Kunden alle Artikel sehen dürfen
				$customers_status_ids = get_all_customers_status_ids();

				// Alle erstmal auf 1 setzen
				if (is_array($customers_status_ids)) {
					foreach ($customers_status_ids as $group_id) {
						$this->setField(TABLE_PRODUCTS, "group_permission_" . $group_id, 1);
					}
				}
				
				// WORKAROUND group_permission_ START: Altes Verfahren der IDs im Sxx-Feld
		        if ( $this->get_exp_value('ALLE_KUNDENGRUPPEN_JN') == 'N' ) {

		            $showFor = explode(';', $this->get_exp_value('KUNDENGRUPPEN'));

					foreach( $showFor as $Key => $PreisgruppenID ){

						$customers_status_name = vDB::fetchone("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'VARIO_PG=".$PreisgruppenID."'");
						$customers_status_id = vDB::fetchone("select customers_status_id from ".TABLE_CUSTOMERS_STATUS." where customers_status_name = '".$customers_status_name."'");
			
						$showFor[$Key] = $customers_status_id;
					}

		            if (!empty($showFor) && is_array($customers_status_ids)) {

						foreach ($customers_status_ids as $group_id ) {

							if ( !in_array( $group_id, $showFor ) ) {

								$this->setField(TABLE_PRODUCTS, 'group_permission_' . $group_id, 0);
							}
						}
        	    	}
    		    }

				
		        // Neues Verhalten ANG_AM
		        $ang_am = date('Y-m-d');	// So steht's in der MySQL
		        if (VARIO_ARTIKEL_ANG_AM == 'VARIO') {
		        	$ang_am = vario_date_to_xtc_date($this->get_exp_value('ANG_AM'));
		        }
				$this->setField(TABLE_PRODUCTS, 'products_date_added', $ang_am, array('INSERT'));	// nur INSERT

				$this->assign_field_values();
    		    $this->do_SQL(array(TABLE_PRODUCTS=>$this->import[TABLE_PRODUCTS]));		// Artikel schon mal eintragen
    		    
    		    // VARIO-Erweiterung: Tabelle VARIO_ART mit weiteren Vario-Feldern vorhanden.

    		    if (!$this->parent_id || VARIO_KEINE_SLAVE_ATTRIBUTE == 1) {													
    		    	
    		    	// Sonderpreise bei Nicht-Salve-Artikeln oder 
    		    	// bei VARIO_KEINE_SLAVE_ATTRIBUTE == 1 
    		        		    
	    		    // **** TABLE_SPECIALS ***
	    		    // $specials_price 	= $this->get_exp_value('AKTION_PREIS');
	    		    $specials_price 	= $this->calculate_price_with_vario_preiseinheit(
											art_import::getNettoPrice($this->get_exp_value('AKTION_PREISBRUTTO'), 
											$this->get_exp_value('MWSTSATZ')));
    		    	
    			    $specials_discount 	= $this->get_exp_value('AKTION_RABATT');

    		    	// Aktionspreis und(!) Aktionsrabatt, deaktiviert!
	    		    //if ($specials_discount && $specials_price) {							// Sind beide Felder gesetzt, dann wird der Rabatt  
    		    	//  $specials_price = $specials_price * (1-($specials_discount/100));		// auch noch auf den Aktionspreis angewendet
    		    	//}
    	    
		    	    // nur Aktionsrabatt
    			    if ($specials_discount && !$specials_price) {
    			      $specials_price = $products_price * (1-($specials_discount/100));
    	    		}
					
					// Aktionsrabatt Menge
					if ( VARIO_AKTIONSPREIS_MENGE_BERUECKSICHTIGEN == 0 ) {
					  $specials_products_quantity = 9999;
					} else {
					  $specials_products_quantity = $this->getProductsQuantity($products_quantity);
					}
    	    
    	    		// wird immer durch Auto-ID neu eingefügt, deshalb hier erst mal alle löschen
					vDB::query("DELETE FROM ".TABLE_SPECIALS." WHERE products_id = $this->products_id");
    	    		$expires_date = $this->get_exp_value('AKTION_BIS');
					// if ($specials_price || $expires_date) {		// 0,00 EUR - Falle
    	    		if ($specials_price > 0.00) {					// 06.12.2012 AB: Annahme: 0,00 - EUR - Aktionen nicht sinnvoll	
				        $this->setField(TABLE_SPECIALS, 'specials_quantity', $specials_products_quantity);
    				    $this->setField(TABLE_SPECIALS, 'specials_new_products_price', $specials_price);
        				$this->setField(TABLE_SPECIALS, 'specials_date_added', date('Y-m-d H:i:s'));
		    	    	$this->setField(TABLE_SPECIALS, 'specials_last_modified', date('Y-m-d H:i:s'));
		    		    $this->setField(TABLE_SPECIALS, 'expires_date', vario_date_to_xtc_date($expires_date, 'd.m.Y', 'Y-m-d'));
    		    		$this->setField(TABLE_SPECIALS, 'status', art_import::getSpecialsStatus($expires_date));
						$this->do_SQL(array(TABLE_SPECIALS=>$this->import[TABLE_SPECIALS]));
					}
    		    					
    	    	}
    	    
    	    	// 09.01.2011 AB: Mengeneinheiten / Grundeinheiten
				if (is_array($this->languages_ids)) {
					foreach ($this->languages_ids as $languages_id) {
						//$this->import[TABLE_PRODUCTS_DESCRIPTION]['language_id'] 	= $languages_id;                            //wird nun über AAT geregelt...
						//$this->do_SQL(array(TABLE_PRODUCTS_DESCRIPTION=>$this->import[TABLE_PRODUCTS_DESCRIPTION]));          //wird nun über AAT geregelt...
						$vario_grundeinheit = $this->get_exp_value('GRUNDEINHEIT');
						if ($vario_grundeinheit) {
							$this->import[TABLE_PRODUCTS_VPE]['products_vpe_id']	= $products_vpe_id;
							$this->import[TABLE_PRODUCTS_VPE]['language_id'] 		= $languages_id;
							$this->import[TABLE_PRODUCTS_VPE]['products_vpe_name']	= $vario_grundeinheit;
							$this->do_SQL(array(TABLE_PRODUCTS_VPE=>$this->import[TABLE_PRODUCTS_VPE]));
						}
					}
				}
			}
			
			
			if ($this->artikelart == 'V' && VARIO_KEINE_SLAVE_ATTRIBUTE <> 1) {					
			// if ($this->artikelart == 'V') {					
				// bei einem Variantenhauptartikel werden jetzt der Attributnamen eingetragen 
				if ($action <> 'D') {
					$this->import_product_options();
				    //erst mal die product_attributes von den bestehenden Varianten leeren... (VARIO muss immer die Varianten nach dem Master-Artikel mitliefern!!!!!)
        		    $sql = "DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id='".$this->products_id."'";
        		    _debug($sql, 'deleteProductsAttributes');
            	    vDB::query($sql);
				}
			}

			if ($this->parent_id && VARIO_KEINE_SLAVE_ATTRIBUTE <> 1) {
				
				$this->import_product_options_values($internetjn);
			}

			// *************************** START BLUEGATE SUMA OPTIMIZER ************************* //
			$bgsql = "select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'SEARCH_ENGINE_FRIENDLY_URLS'";
			//_debug($bgsql,'      art_import: search_friendly_urls request');
			$sfurls = vDB::fetchOne($bgsql); 
			//_debug($sfurls,'      art_import: search_friendly_urls response');

			$bgtex = table_exists('bluegate_seo_url');
			//_debug($bgtex,'      art_import: bluegate-table-exists');
			
			if ($bgtex && ($sfurls == 'true')) {
			
				require_once (DIR_FS_INC.'bluegate_seo.inc.php');

				if ($internetjn == 'J') {
					//_debug($this->products_id, '      art_import: Create SUMA-Entry for : ');
					$bluegateSeo = new BluegateSeo();
					$bluegateSeo->updateSeoDBTable('product', 'update', $this->products_id);
				}
			
			 }
			// *************************** END OF BLUEGATE SUMA OPTIMIZER ****************** //
			 
		} else {
			// DELETE ARTIKEL
			//$catfunc = new categories();
			//$catfunc->remove_product($this->products_id);
			
			$sql = "DELETE FROM ".TABLE_PRODUCTS." WHERE products_id='".$this->get_exp_value('WEBSHOP_ID')."'";
			_debug($sql, 'deleteProduct');
			vDB::query($sql);

			if ( true /* Properties Combis */) {
			
				// Alle Informationen zum löschen der properties selektieren:
				$sql = "
					SELECT distinct ppc.products_properties_combis_id, ppc.products_id,  ppi.properties_values_id, pvd.properties_values_description_id, ppcv.products_properties_combis_values_id
					  FROM " . TABLE_PRODUCTS_PROPERTIES_COMBIS    . " ppc
					  JOIN " . TABLE_PRODUCTS_PROPERTIES_INDEX     . " ppi ON ppc.products_properties_combis_id = ppi.products_properties_combis_id
					  JOIN " . TABLE_PROPERTIES_VALUES_DESCRIPTION . " pvd ON ppi.properties_values_id = pvd.properties_values_id
					  JOIN " . TABLE_PRODUCTS_PROPERTIES_COMBIS_VALUES   . " ppcv ON ppc.products_properties_combis_id = ppcv.products_properties_combis_id
					 WHERE ppc.products_properties_combis_id = " . $this->get_exp_value('WEBSHOP_ID') . "
				";
				
				$select_combis_data = @mysql_fetch_array( mysql_query( $sql ) );

				_debug($sql, 'Selektiere alle Daten zum Löschen der Properties-Informationen');
				

				if( !empty( $select_combis_data ) )
				{
					$sql = "DELETE FROM " . TABLE_PRODUCTS_PROPERTIES_COMBIS . " WHERE products_properties_combis_id = '" . $this->get_exp_value('WEBSHOP_ID') . "'";
					vDB::query($sql);
					_debug($sql, 'delete');
					
					// Von AB weiter unten kopiert:
					$coo_properties_data_agent = MainFactory::create_object('PropertiesDataAgent');
					$coo_properties_data_agent->rebuild_properties_index( $select_combis_data['products_id'] );				// !!! parent_id ist hier die products_id !!!
				}
			}

		}

		$queryEndTime = array_sum(explode(" ",microtime()));
		$processTime = $queryEndTime - $queryStartTime;
		_debug('  End art_import: '.$queryEndTime.' Duration: '.$processTime);

    }
		
		
    function validate($current_table, $current_structure) {
        if(isset($current_structure['products_id']) && empty($current_structure['products_id'])) return false;
        switch($current_table) {
            case TABLE_PRODUCTS_DESCRIPTION :
                if($current_structure['language_id']==0) return false;
                break;
            case TABLE_PRODUCTS_ATTRIBUTES :
                if(empty($current_structure['products_id']) || empty($current_structure['options_values_id']) || empty($current_structure['options_id'])) return false;
                break;
            case TABLE_PRODUCTS_OPTIONS_VALUES :
                if(( empty($current_structure['products_options_values_name']) && $current_structure['products_options_values_name'] != 0 ) || empty($current_structure['products_options_values_id'])) return false;
                 break;
            case TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS :
                if(empty($current_structure['products_options_values_id']) || empty($current_structure['products_options_id'])) return false;
                break;
            default:
                return true;
                break;
        }
        return true;
    }


    function import_product_options() {
		_debug($this->products_id, 'art_import:import_options');
		
        /**
         * $this->products_id ist die die id des Variantenhauptartikels
         * 
    	 * TABLE_PRODUCTS_OPTIONS
    	 *   products_options_id 
    	 *   language_id 
    	 *   products_options_name 
    	 */
		$this->setAction('U');
		$max_att_number = VARIO_ART_MAX_SXX_ATT_NUMMER;
		if (!$max_att_number) $max_att_number = 10; 
		_debug($max_att_number, 'art_import:import_options - max_att_number');
		for ($i = 1; $i<=$max_att_number; $i++) {
			if (is_array($this->languages_ids)) {

				if ( true /* properties combis */) {
					$att_field 		= sprintf("S%02d_ATT_ID", $i);				// Feldname ais SP für ID aus VAR_ATT
					$att_var_id 	= $this->get_exp_value($att_field);			// VAR_ATT.ID
					
					if( !empty ( $att_var_id ) ) {								// 11.09.201 AB: Nur gefüllte beachten
						
						$this->import[TABLE_PROPERTIES]['properties_id']	= $att_var_id;
						$this->import[TABLE_PROPERTIES]['sort_order']		= $i;
						$this->do_SQL(array(TABLE_PROPERTIES=>$this->import[TABLE_PROPERTIES]));
						foreach ($this->languages_ids as $languages_id) {
							$att_field 			= sprintf("S%02d", $i);					// S01
							$att_field_value 	= $this->get_exp_value($att_field);		// var_att.id
							if ($att_field_value) {
								// ID besorgen, falls Eintrag schon vorhanden
								$pd_id = vDB::fetchone(
									 "select properties_description_id " 
									."  from ".TABLE_PROPERTIES_DESCRIPTION
									." where properties_id = $att_var_id "
									."   and language_id = $languages_id"
									);
								if ($pd_id)                                                                                                                   
									$this->import[TABLE_PROPERTIES_DESCRIPTION]['properties_description_id'] = $pd_id; 
								else
									$this->import[TABLE_PROPERTIES_DESCRIPTION]['properties_description_id'] = null;

								$this->import[TABLE_PROPERTIES_DESCRIPTION]['properties_id']				= $att_var_id;
								$this->import[TABLE_PROPERTIES_DESCRIPTION]['language_id']					= $languages_id;
								$this->import[TABLE_PROPERTIES_DESCRIPTION]['properties_name']				= $att_field_value;
								$this->do_SQL(array(TABLE_PROPERTIES_DESCRIPTION=>$this->import[TABLE_PROPERTIES_DESCRIPTION]));
							}
						}

					}
					
				} else {

					// *** products_attributes ***					
					$po_id = ($this->products_id + 0) * VARIO_ATTR_OFFSET + $i;
					$sxx_field 			= sprintf("S%02d", $i);	// Sxx
					$sxx_field_value 	= $this->get_exp_value($sxx_field);


					foreach ($this->languages_ids as $languages_id) {
						$sxx_field 			= sprintf("S%02d", $i);	// Sxx
						$sxx_field_value 	= $this->get_exp_value($sxx_field);
						//_debug($sxx_field_value, "Feld $sxx_field($languages_id)");
						if ($sxx_field_value) {
							$this->import[TABLE_PRODUCTS_OPTIONS]['products_options_id'] 	= ($this->products_id + 0) * VARIO_ATTR_OFFSET + $i;					// das ist der Parent, der Master
							$this->import[TABLE_PRODUCTS_OPTIONS]['language_id'] 			= $languages_id;
							$this->import[TABLE_PRODUCTS_OPTIONS]['products_options_name'] 	= $sxx_field_value; // . " ($this->products_id + $i)"; 
							//_debug($this->import[TABLE_PRODUCTS_OPTIONS], 'TABLE_PRODUCTS_OPTIONS');
							$this->do_SQL(array(TABLE_PRODUCTS_OPTIONS=>$this->import[TABLE_PRODUCTS_OPTIONS]));
						}
					}

				}
				
			}
        }
    }
		
    function import_product_options_values($internetjn) {
		_debug('','art_import:import_options_values');
		
        /**
         * $this->products_id ist die id des Artikels, der die Variantenwerte hat, $this->parent_id ist der Variantenhauptartikel
         * 
    	 * TABLE_PRODUCTS_OPTIONS_VALUES_STRUCTURE
    	 *   products_options_values_id 
    	 *   language_id 
    	 *   products_options_values_name
    	 *   
    	 *    ACHTUNG: Keine automatische Vergabe der ISs, vielmehr: IDs sind Products-ID * VARIO_ATTR_OFFSET + Attribut-Nummerm Bsp.: 4711 -> 471103 für dirttes Attribut
    	 */
		$this->setAction('U');
		if ($internetjn == 'N') $this->setAction('D');

		$max_att_number = VARIO_ART_MAX_SXX_ATT_NUMMER;
		if (!$max_att_number) $max_att_number = 10; 
        for ($i = 1; $i<=$max_att_number; $i++) {
			if (is_array($this->languages_ids)) {
				// _debug($this->languages_ids, 'Sprachen gefunden');
				
				if ( true /* properties combis */ ) {
					$att_field 		= sprintf("S%02d_ATT_ID", $i);				// Feldname aus SP für ID aus VAR_ATT
					$att_var_id 	= $this->get_exp_value($att_field);			// VAR_ATT.ID
					$val_field 		= sprintf("S%02d_VAL_ID", $i);				// Feldname aus SP für ID aus VAR_VAL
					$val_var_id 	= $this->get_exp_value($val_field);			// VAR_VAL.ID
					$sort_field 	= sprintf("S%02d_VAL_SORT", $i);			// Feldname aus SP für SORT aus VAR_VAL		02.04.2012 AB
					$sort_order 	= $this->get_exp_value($sort_field);		// 
					
					if( !empty ( $val_var_id ) ) {
						
						$this->import[TABLE_PROPERTIES_VALUES]['properties_values_id']	= $val_var_id;
						$this->import[TABLE_PROPERTIES_VALUES]['properties_id']			= $att_var_id;		
						// $this->import[TABLE_PROPERTIES_VALUES]['sort_order']			= $this->get_exp_value('PRODUCTS_SORT');	// 11.10.2011 AB, statt $i; 
						$this->import[TABLE_PROPERTIES_VALUES]['sort_order']			= $sort_order;								// 02.04.2012 AB 
						$this->import[TABLE_PROPERTIES_VALUES]['value_model']			= '';				// wofür ist das
						$this->import[TABLE_PROPERTIES_VALUES]['value_price_type']		= 'fix';			// kein Aufschlag
						$this->import[TABLE_PROPERTIES_VALUES]['value_price']			= 0;				// null wäre treffender
						
						if( $this->getAction() <> 'D' ) {
							$this->do_SQL(array(TABLE_PROPERTIES_VALUES=>$this->import[TABLE_PROPERTIES_VALUES]));
						}

						foreach ($this->languages_ids as $languages_id) {
							$val_field 			= sprintf("S%02d", $i);					// S01
							$val_field_value 	= $this->get_exp_value($val_field);		// var_att.id
							if ($val_field_value) {
								// ID besorgen, falls Eintrag schon vorhanden
								$ppvd_id = vDB::fetchone(
									 "select properties_values_description_id " 
									."  from ".TABLE_PROPERTIES_VALUES_DESCRIPTION
									." where properties_values_id = $val_var_id "
									."   and language_id = $languages_id"
									);
								if ($ppvd_id)								
									$this->import[TABLE_PROPERTIES_VALUES_DESCRIPTION]['properties_values_description_id']	= $ppvd_id; 
								else
   							        $this->import[TABLE_PROPERTIES_VALUES_DESCRIPTION]['properties_values_description_id']	= null; 
								$this->import[TABLE_PROPERTIES_VALUES_DESCRIPTION]['properties_values_id']				= $val_var_id;
								$this->import[TABLE_PROPERTIES_VALUES_DESCRIPTION]['language_id']						= $languages_id;
								$this->import[TABLE_PROPERTIES_VALUES_DESCRIPTION]['values_name']						= $val_field_value;
								$this->import[TABLE_PROPERTIES_VALUES_DESCRIPTION]['values_image']						= '';		// wofür ?
								
								if( $this->getAction() <> 'D' ) {
									$this->do_SQL(array(TABLE_PROPERTIES_VALUES_DESCRIPTION=>$this->import[TABLE_PROPERTIES_VALUES_DESCRIPTION]));
								}
							}
						}
						$attributes_model	= $this->get_exp_value('ARTIKELNR');
						$attributes_sort	= $this->get_exp_value('PRODUCTS_SORT');
						$attributes_status	= $this->get_exp_value('WEBSHOP_STATUS');	// 20.11.2013 AB
						$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]['products_properties_combis_id']	= $this->products_id;		// WEBSHIOP_ID des SLAVES 
						$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]['products_id'] 						= $this->parent_id;			// WEBSHIOP_ID des MASTERs
						$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]['sort_order'] 						= $attributes_sort;
						$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]['combi_model'] 						= $attributes_model;
						$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]['combi_shipping_status_id'] 		= $attributes_status;	// 20.11.2013 AB
						$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]['combi_quantity'] 					= $this->getProductsQuantity($this->get_exp_value(VARIO_ARIKELANZAHL_FELD));
						$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]['combi_quantity_type'] 				= 'fix';

						if (!defined('GAMBIOGX2_PROPERTIES_COMBIS_PRICEMODE')) define('GAMBIOGX2_PROPERTIES_COMBIS_PRICEMODE', 'fix');

						if( strtoupper( GAMBIOGX2_PROPERTIES_COMBIS_PRICEMODE ) <> 'FIX' ) {
							
							// Aufschlag oder Abschlag
							$products_price 		= $this->get_products_parent_price();
							$options_values_price 	= $this->calculate_price_with_vario_preiseinheit(
														$this->getNettoPrice($this->get_exp_value('VKBRUTTO'), 
														$this->get_exp_value('MWSTSATZ')));
							
							if ($products_price <= $options_values_price) {
								$options_values_price = $options_values_price - $products_price;
								$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]['combi_price_type'] = 'plus';
							} else {
								$options_values_price = $products_price - $options_values_price;
								$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]['combi_price_type'] = 'minus';
							}
							
							if (abs($options_values_price) <= VARIO_PREIS_RUNDEN ) {
								$options_values_price = 0.0;
								$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]['combi_price_type'] = 'plus';
							}
							
						} else {

							// Fix-Preis
							$options_values_price = $this->calculate_price_with_vario_preiseinheit(
													$this->getNettoPrice($this->get_exp_value('VKBRUTTO'), 
													$this->get_exp_value('MWSTSATZ')));
						}

						$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]['combi_price']		= $options_values_price;

						$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]['products_vpe_id']	= null;
						$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]['vpe_value']		= null;
						
						$this->do_SQL(array(TABLE_PRODUCTS_PROPERTIES_COMBIS=>$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS]));
						
						// ID besorgen, falls Eintrag schon vorhanden
						$ppc_id = vDB::fetchone(
							 "select products_properties_combis_values_id " 
							."  from ".TABLE_PRODUCTS_PROPERTIES_COMBIS_VALUES
							." where products_properties_combis_id = $this->products_id "
							."   and properties_values_id = $val_var_id"
							);
						if ($ppc_id) 
							$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS_VALUES]['products_properties_combis_values_id']	= $ppc_id;
						$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS_VALUES]['products_properties_combis_id']			= 	$this->products_id;
						$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS_VALUES]['properties_values_id']					=	$val_var_id; 
						
						if( $this->getAction() <> 'D' ) {
							$this->do_SQL(array(TABLE_PRODUCTS_PROPERTIES_COMBIS_VALUES=>$this->import[TABLE_PRODUCTS_PROPERTIES_COMBIS_VALUES]));
						}

						/*
						Frage an CSEO:

						$coo_properties_data_agent = MainFactory::create_object('PropertiesDataAgent');
						$coo_properties_data_agent->rebuild_properties_index($this->parent_id);
						*/
					}
					
				}
			
			}
			
        }
        
    }
		
    function art_highlight_to_products_startpage($highlight) {
        if($highlight=="J" || $highlight=="j" || $highlight==1) return 1;
        else return 0;
    }

    function get_products_parent_price() {
        $sql =   "SELECT products_price FROM ". TABLE_PRODUCTS ." WHERE products_id = '$this->parent_id' ";
    	if ($result = vDB::query($sql)) {
            if ($row = vDB::fetch_assoc($result)) {
                return $row['products_price'];
            }
        }
        return $this->get_exp_value('VKNETTO');
    }

    function get_products_parent_weight() {
        $sql =   "SELECT products_weight FROM ". TABLE_PRODUCTS ." WHERE products_id = '$this->parent_id' ";
    	if ($result = vDB::query($sql)) {
            if ($row = vDB::fetch_assoc($result)) {
                return $row['products_weight'];
            }
        }
        return 0;
    }

    function getVpeValue($faktor) {
        if ($faktor) return 1/$faktor;
        return 1; // null; 03.11.2009 AB; null macht keinen Sinn! 1 ist neutral.
    }

    function get_products_vpe_id($me, $l_id = false) {
    	// if ($GLOBALS['vario_me'][$me]) return $GLOBALS['vario_me'][$me];		was war das denn ?
    	// if (!$me) return 'Stk.';												was war das denn ?
    	// Mit $GLOBALS['vario_me'][$me] erneuten Zugriff auf MySQL vermieden
        $l_id = ($l_id===false)?get_vario_default_language_id():$l_id;
        $sql  =  "SELECT products_vpe_id "
        		."  FROM ".TABLE_PRODUCTS_VPE
        		." WHERE products_vpe_name='$me' "
        		."   AND language_id='$l_id' limit 1";
        if($result = vDB::query($sql)){
            if($row = vDB::fetch_assoc($result)){
                // return $GLOBALS['vario_me'][$me] = $row['products_vpe_id'];	was war das denn ?
            	return $row['products_vpe_id'];
            } else {
                return $this->gen_vpe_id();		// keine Mengeninehit gefunden, also neue anlegen
            }
        }
        return null;
    }

    function gen_vpe_id() {
        $sql =   "SELECT max(products_vpe_id) maxid "
        		."  FROM " . TABLE_PRODUCTS_VPE;
        if ($result = vDB::query($sql)) {
            if ($row = vDB::fetch_assoc($result)) {
                return ++$row['maxid'];
            }
        }
        return 1;
    }

    function get_products_vpe_status($vpe) {
        if (!empty($vpe)) return 1;
        return 0;
    }

    function getSpecialsStatus($aktion_bis) {
		if (!$aktion_bis) return 1;	// nicht gesetztes Dateum bedeutet: Sonderpreis gilt immer!
        $expires_ts = strtotime(vario_date_to_xtc_date($aktion_bis, 'd.m.Y', 'Y-m-d'));
        $now_ts     = time();
        return ($expires_ts<$now_ts) ? 0 : 1 ;

	
	}

	function getManufacturersId($manufacturer) {
        _debug($manufacturer, 'getManufacturersId');
		if (!$manufacturer) {
	    	return 0; // unbekannt
	    }

		$manufacturer   = trim($manufacturer);
		$herstellerbild = trim($this->get_exp_value('HERSTELLERBILD'));
		// check manufacturers table
        if ($id = vDB::fetchOne(
        					 "SELECT manufacturers_id "
        					."  FROM ".TABLE_MANUFACTURERS." "
        					." WHERE manufacturers_name = '{$manufacturer}'")) {
        	if ($herstellerbild > '') {
        		_debug($herstellerbild, 'getManufacturersId');
        		$sql = "UPDATE ".TABLE_MANUFACTURERS." SET " 
            			."  manufacturers_image = '$herstellerbild', last_modified = now() WHERE manufacturers_id = $id"; 
        		_debug($sql, 'getManufacturersId');
            	vDB::query($sql);
        		$GLOBALS['affected_rows'][TABLE_MANUFACTURERS]['UPDATE'] ++;
            }
        	return $id;
        } else {
            // if manufacturer not exists: insert
        	_debug($herstellerbild, 'getManufacturersId');
        	$sql = "INSERT INTO manufacturers ( " 
            			."  manufacturers_name, manufacturers_image, date_added ) VALUES ( '$manufacturer', '$herstellerbild', now())";
        	_debug($sql, 'getManufacturersId');
            if (vDB::query($sql)) {
				$GLOBALS['affected_rows'][TABLE_MANUFACTURERS]['INSERT'] ++;
            	return vDB::insert_id();
            }
        }
	}

	function getProductsQuantity( $quantity ) {
		if( (int)$this->artikelart == 3 || (int)$this->artikelart == 2 )
		{
			return 999999;
		} else {
			if( (int)$this->get_exp_value('LF_MELDEBESTAND') > 0 ) {
				$quantity = $quantity + $this->get_exp_value('LF_MELDEBESTAND');
			}
			return $quantity;
		}
	}

}
?>
