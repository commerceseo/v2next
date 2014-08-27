<?php
/**
 * @version $Id: kat_import.class.php,v 1.1 2011-07-15 12:33:34 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 */

 // 24.03.2014 SV
 // gm_show_qty_info (VERFUEGBARKEIT_ANZEIGEN_JN) und gm_show_qty (MENGENEINGABE_ANZEIGEN_JN) waren vertauscht.

 // 28.01.2014 SV
 // categories_name von 32 auf 255 erweitert
 
 // 10.12.2013 SV
 // Neue Felder für GambioGX2
 
 // 03.07.2013
 // Sortierung mit ART.ANG_AM = p.products_date_added berücksichtigen.

  	// include_once('vario/html2text.inc.php');								// 

	class kat_import extends vario_import {
		
		function kat_import($exp_input){
			_debug($exp_input, 'START kat_import -- Übergeben wurde der Datensatz $exp_input');
			$this->set_exp_source($exp_input);
			
			$this->set_fields( array(
				'ID'							=> array(
														create_field_info(TABLE_CATEGORIES, 'categories_id'), 
														create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_id'),
														// create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'language_id', 					'get_vario_default_language_id'),
													),
				'PARENT_ID'						=> array(create_field_info(TABLE_CATEGORIES, 'parent_id')),
				'SORTIERUNG'					=> array(create_field_info(TABLE_CATEGORIES, 'sort_order'),),
				'ARTIKELSORTIERUNG_FELD'		=> array(create_field_info(TABLE_CATEGORIES, 'products_sorting', 						'$this->product_sorting'),),
				'ARTIKELSORTIERUNG_RICHTUNG'	=> array(create_field_info(TABLE_CATEGORIES, 'products_sorting2'),),
				'KATEGORIEUEBERSICHT'			=> array(create_field_info(TABLE_CATEGORIES, 'categories_template'),),
				'ARTIKELUEBERSICHT'				=> array(create_field_info(TABLE_CATEGORIES, 'listing_template'),),
				'AKTIVJN'						=> array(create_field_info(TABLE_CATEGORIES, 'categories_status', 						'vario_bool_to_xtc_bool')),
				'TEXT'							=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_name', 			'$this->plaintext($this->exp_source[\'TEXT\'], 255)')),
				'BESCHREIBUNG'					=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_description')),
				'UEBERSCHRIFT'					=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_heading_title', 	'$this->plaintext($this->exp_source[\'UEBERSCHRIFT\'], 255)')),
				'META_TITEL'					=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_meta_title', 		'$this->plaintext($this->exp_source[\'META_TITEL\'], 100)')),
				'META_BESCHREIBUNG'				=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_meta_description',	'$this->plaintext($this->exp_source[\'META_BESCHREIBUNG\'], 255)')),
				'META_KEYWORDS'					=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_meta_keywords', 	'$this->plaintext($this->exp_source[\'META_KEYWORDS\'], 255)')),
													));


			// Ein bisschen Mist von osCommerce abfangen: Dynamsiche Felder für Kunde/Preisgruppen
			// VARIO hat noch keine Verwaltung dafür, so dass alle Kunden alle Kategorien sehen dürfen
			$customers_status_ids = get_all_customers_status_ids();
			if (is_array($customers_status_ids)) {
				foreach ($customers_status_ids as $group_id) {
					$this->setField(TABLE_CATEGORIES, "group_permission_$group_id", 1);
				}
			}
			$this->assign_field_values();

			// $this->do_SQL(); // dies würde auch die TABLE_CATEGORIES_DESCRIPTION setzen
			$this->do_SQL(array(TABLE_CATEGORIES=>$this->import[TABLE_CATEGORIES])); // Kategorien werden angelegt, permissions gesetzt
			
			/**
			 *  noch nicht vorhandene Sprachen mit Systemsprache füllen, falls Shop mehr Sprachen als VARIO hat
			 */

			$languages_ids = get_all_languages_ids();
			if (is_array($languages_ids)) {
				foreach ($languages_ids as $languages_id) {
					$this->import[TABLE_CATEGORIES_DESCRIPTION]['language_id'] = $languages_id;
					if(get_vario_default_language_id() != $languages_id) {
						$ignore_this_fields = array(TABLE_CATEGORIES_DESCRIPTION=>array('categories_description', 'categories_name'));
					} else { 
						$ignore_this_fields = array();
					}
					$this->set_toignore_fields($ignore_this_fields, 'UPDATE');	
					$this->do_SQL(array(TABLE_CATEGORIES_DESCRIPTION=>$this->import[TABLE_CATEGORIES_DESCRIPTION]));
				}
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

				if ($this->get_exp_value('AKTIVJN') == 'J') {
					//_debug($this->products_id, '      kat_import: Create SUMA-Entry for : ');
					$bluegateSeo = new BluegateSeo();
					$bluegateSeo->updateSeoDBTable('category', 'update', $this->get_exp_value('ID'));
				}
			
			 }
			// *************************** END OF BLUEGATE SUMA OPTIMIZER ****************** //
			
			_debug('', ' ENDE kat_import --');
		}

	    function product_sorting($sorting){
    	    if(!empty($sorting)){
				if ($sorting == 'ITM.SORTIERUNG') 			return 'p.products_sort';
				if ($sorting == 'ART.BESTAND') 				return 'p.products_quantity';
				if ($sorting == 'ART.GEWICHT') 				return 'p.products_weight';
				if ($sorting == 'ART.VKNETTO') 				return 'p.products_price';
				if ($sorting == 'BESTELLTEARTIKEL') 		return 'p.products_ordered';
				if ($sorting == 'ART.ARTIKELBEZEICHNUNG1') 	return 'pd.products_name';
				if ($sorting == 'ART.ANG_AM') 				return 'p.products_date_added';
        	    return 'p.products_sort';
			} else {
        	    return 'p.products_sort';
		    }
    	}

	}
?>
