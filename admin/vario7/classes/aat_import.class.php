<?php
/**
 * @version $Id: aat_import.class.php,v 1.1 2011-07-15 12:33:32 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 *
 11.09.2011 AB: TODO: GAMBIOGX2, erst mal Attirbutes unterdrückt.


    10.12.2013 SV
    - Neue Felder für GambioGX2

 Revision 1.15  2011/04/12 10:04:34  ag
 -Korrekturen

 Revision 1.14  2011/02/07 09:52:37  ag
 7.1.005.003:
 - products_name muss auch für die Standard-Sprache (deutsch) gesetzt werden, da art_import nicht zwangsläufig stattfindet wenn aat_import stattfindet!

 Revision 1.13  2011/02/02 16:42:03  ag
 - Funktion vario_sprach_id_to_xtc_languages_id gibt nun nicht mehr 2 zurück, wenn das Mapping für die Sprache nicht gefunden wurde, sondern -1.
   Die Import-Funktionen brechen mit dem Import des Datensatzes ab, wenn -1 zurückgegeben wird.

 
 * 05.12.2010 AB: Neue Hersteller-ID Zuordnung
 * 20.11.2010 AB: SPR_ID je nach Version 0/1 oder N/J (ab VARIO 7.1)
 * 21.09.2010 AB: set_toignore_fields: products_name bei Systemsprache
 * 13.09.2010 AB: HERSTELLER*, SRACH_ID
 * 04.08.2010 AB: SPR_ID
 * 27.03.2010 AB: Umstellung auf WEBSHOP_ID
 */

class aat_import extends vario_import {
	
	function aat_import($exp_input) {
		
		$queryStartTime = array_sum(explode(" ",microtime()));
		_debug($exp_input, "START aat_import -- Übergeben wurde der Datensatz exp_input um $queryStartTime.");
		
		$this->set_exp_source($exp_input);
		//_debug($this->exp_source, '      art_import -- exp_source');
		
		$action = $this->get_exp_value('AKTION');	 
		if ($action <> 'D') { 
			
			$spr_id = vario_sprach_id_to_xtc_languages_id($this->get_exp_value('SPRACH_ID'));
			if ( VARIO_PRODUCT_USED == 'VARIO7.1' && $spr_id==-1) {
				$spr_id = vario_sprach_id_to_xtc_languages_id($this->get_exp_value('SPR_ID'));
  			}
			else
			{
			  if ($spr_id==-1)
			  {
			    return;
			  }
			}

			$this->set_fields( array (
			
				// TABLE_PRODUCTS_DESCRIPTION
				'WEBSHOP_ID'			=> array(create_field_info(TABLE_PRODUCTS_DESCRIPTION, 	'products_id')),
				// 'SPRACH_ID'				=> array(create_field_info(TABLE_PRODUCTS_DESCRIPTION, 	'language_id', 'vario_sprach_id_to_xtc_languages_id')),
				'ARTIKELBEZEICHNUNG1'	=> array(create_field_info(TABLE_PRODUCTS_DESCRIPTION, 	'products_name')),
				'KURZBESCHREIBUNG'		=> array(create_field_info(TABLE_PRODUCTS_DESCRIPTION, 	'products_short_description')),
				'BESCHREIBUNG'			=> array(create_field_info(TABLE_PRODUCTS_DESCRIPTION, 	'products_description')),
				'META_TITEL'			=> array(create_field_info(TABLE_PRODUCTS_DESCRIPTION, 	'products_meta_title')),
				'META_BESCHREIBUNG'		=> array(create_field_info(TABLE_PRODUCTS_DESCRIPTION, 	'products_meta_description')),
				'META_KEYWORDS'			=> array(create_field_info(TABLE_PRODUCTS_DESCRIPTION, 	'products_meta_keywords')),
				'SUCHBEGRIFFE'			=> array(create_field_info(TABLE_PRODUCTS_DESCRIPTION, 	'products_keywords')),
				'PRODUKTURL'			=> array(create_field_info(TABLE_PRODUCTS_DESCRIPTION, 	'products_url')),
	           ));

		    $this->setField(TABLE_PRODUCTS_DESCRIPTION, 'language_id', 	  $spr_id);

			// Werte aus ART (Systemsprache) nict überschreiben
			$languages_id = vario_sprach_id_to_xtc_languages_id($this->get_exp_value('SPRACH_ID'));
			if ($languages_id==-1)
			{
			  return;
			}			
			// 07.02.2011 - AG products_name muss auch für die Standard-Sprache (deutsch) gesetzt werden, da art_import nicht zwangsläufig stattfindet wenn aat_import stattfindet!
	        /*if(get_vario_default_language_id() == $languages_id) {
                $ignore_this_fields = array(TABLE_PRODUCTS_DESCRIPTION=>array('products_name'));
			} else { 
				$ignore_this_fields = array();
			}
			$this->set_toignore_fields($ignore_this_fields, 'UPDATE');	*/
	          
			$this->assign_field_values();
    	    $this->do_SQL(array(TABLE_PRODUCTS_DESCRIPTION=>$this->import[TABLE_PRODUCTS_DESCRIPTION]));

			
			$max_att_number = VARIO_ART_MAX_SXX_ATT_NUMMER;
			if (!$max_att_number) $max_att_number = 10; 			
			
			// TABLE_VARIO_ART steht nicht mehr zur Verfügung für die Artikelart!
			/*
			$artikelart = '';
			$sql =   "SELECT va.artikelart FROM ".TABLE_VARIO_ART." va where va.webshop_id='".vDB::escape_string ($this->get_exp_value('WEBSHOP_ID'))."' LIMIT 1";
			if ($result = vDB::query($sql)) 
			{
			  if (vDB::num_rows($result) > 0 && $row = vDB::fetch_assoc($result)) 
			  {
				$artikelart = $row['artikelart'];
			  }
			}
			
			for ($i = 1; $i<=$max_att_number; $i++) 
			{
				$sxx_field 			= sprintf("S%02d", $i);	// Sxx
				$sxx_field_value 	= $this->get_exp_value($sxx_field);
				$pov_id = ($this->get_exp_value('WEBSHOP_ID') + 0) * VARIO_ATTR_OFFSET + $i;
			    $po_id = ($this->get_exp_value('WEBSHOP_ID') + 0) * VARIO_ATTR_OFFSET + $i;
				
				if ($sxx_field_value) 
				{
					$pov_id = ($this->get_exp_value('WEBSHOP_ID') + 0) * VARIO_ATTR_OFFSET + $i;
					$po_id = ($this->get_exp_value('WEBSHOP_ID') + 0) * VARIO_ATTR_OFFSET + $i;
				
					if ($artikelart == 'V')
					{
						$this->import[TABLE_PRODUCTS_OPTIONS]['products_options_id'] 	= $po_id;
						$this->import[TABLE_PRODUCTS_OPTIONS]['language_id'] 			= $languages_id;
						$this->import[TABLE_PRODUCTS_OPTIONS]['products_options_name'] 	= $sxx_field_value; // . " ($this->products_id + $i)"; 
						//_debug($this->import[TABLE_PRODUCTS_OPTIONS], 'TABLE_PRODUCTS_OPTIONS');
						$this->do_SQL(array(TABLE_PRODUCTS_OPTIONS=>$this->import[TABLE_PRODUCTS_OPTIONS]));
					}
					else
					{
					  if ($artikelart != '')
					  {
						$this->import[TABLE_PRODUCTS_OPTIONS_VALUES]['products_options_values_id'] 		= $pov_id;
						$this->import[TABLE_PRODUCTS_OPTIONS_VALUES]['products_options_values_name']	= $sxx_field_value; //  . " ($this->products_id + $i / $attributes_model)"; 
						//_debug($this->import[TABLE_PRODUCTS_OPTIONS_VALUES], 'TABLE_PRODUCTS_OPTIONS');
							
						$this->import[TABLE_PRODUCTS_OPTIONS_VALUES]['language_id'] 					= $languages_id;
						$this->do_SQL(array(TABLE_PRODUCTS_OPTIONS_VALUES=>$this->import[TABLE_PRODUCTS_OPTIONS_VALUES]));					
					  }
					}
				}
			}
			*/

			if ( VARIO_PRODUCT_USED < 'VARIO7.1' ) {
    	    	// HERSTELLERURL
	        	$hersteller    = $this->get_exp_value('HERSTELLER'); 
	        	$herstellerurl = $this->get_exp_value('HERSTELLERURL');
	        	$manufacturers_id = vDB::fetchOne(
        						 "SELECT manufacturers_id "
        						."  FROM ".TABLE_MANUFACTURERS." "
        						." WHERE manufacturers_name = '{$hersteller}'");
        		if ($manufacturers_id) {
					$this->setField(TABLE_MANUFACTURERS_INFO, 'manufacturers_id', $manufacturers_id);
					$this->setField(TABLE_MANUFACTURERS_INFO, 'languages_id', 
										vario_sprach_id_to_xtc_languages_id($this->get_exp_value('SPRACH_ID')));
					$this->setField(TABLE_MANUFACTURERS_INFO, 'manufacturers_url', $herstellerurl);
					$this->do_SQL(array(TABLE_MANUFACTURERS_INFO=>$this->import[TABLE_MANUFACTURERS_INFO]));
        		}
			}  
	        
		} else {
			// TODO			
		}

		$queryEndTime = array_sum(explode(" ",microtime()));
		$processTime = $queryEndTime - $queryStartTime;
		_debug('  End art_import: '.$queryEndTime.' Duration: '.$processTime);

    }

}
?>
