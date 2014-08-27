<?php
/**
 * @version $Id: kat_spr_import.class.php,v 1.1 2011-07-15 12:33:34 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 *
 
 $Log: not supported by cvs2svn $
 Revision 1.4  2011/02/02 16:42:03  ag
 - Funktion vario_sprach_id_to_xtc_languages_id gibt nun nicht mehr 2 zurück, wenn das Mapping für die Sprache nicht gefunden wurde, sondern -1.
   Die Import-Funktionen brechen mit dem Import des Datensatzes ab, wenn -1 zurückgegeben wird.


    10.12.2013 SV
    - Neue Felder für GambioGX2
 
 * 21.09.2010 AB: set_toignore_fields: products_name bei Systemsprache
 *
 */

class kat_spr_import extends vario_import {
		
		function kat_spr_import($exp_input) {
			_debug($exp_input, 'START kat_spr_import -- Übergeben wurde der Datensatz $exp_input');
			$this->set_exp_source($exp_input);
			
			$this->set_fields( array(
				'KAT_ID'						=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_id')),
				'SPR_ID'						=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'language_id', 				'vario_sprach_id_to_xtc_languages_id')),
				'TEXT'							=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_name', 			'$this->plaintext($this->exp_source[\'TEXT\'], 32)')),
				'BESCHREIBUNG'					=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_description')),
				'UEBERSCHRIFT'					=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_heading_title', 	'$this->plaintext($this->exp_source[\'UEBERSCHRIFT\'], 255)')),
				'META_TITEL'					=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_meta_title', 		'$this->plaintext($this->exp_source[\'META_TITEL\'], 100)')),
				'META_BESCHREIBUNG'				=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_meta_description',	'$this->plaintext($this->exp_source[\'META_BESCHREIBUNG\'], 255)')),
				'META_KEYWORDS'					=> array(create_field_info(TABLE_CATEGORIES_DESCRIPTION, 'categories_meta_keywords', 	'$this->plaintext($this->exp_source[\'META_KEYWORDS\'], 255)')),
				));

			// Werte aus  KAT (Systemsprache) nicht überschreiben
			$languages_id = vario_sprach_id_to_xtc_languages_id($this->get_exp_value('SPR_ID'));
			if ($languages_id==-1)
			{
			  return;
			}

			_debug($languages_id, '      kat_import -- SPR_ID');
			_debug(get_vario_default_language_id(), '      kat_import -- DEF_ID');
			if(get_vario_default_language_id() == $languages_id) {
                $ignore_this_fields = array(TABLE_CATEGORIES_DESCRIPTION=>array('categories_name'));
				_debug($ignore_this_fields, '      kat_import -- Ignore Fields');
			} else { 
				$ignore_this_fields = array();
			}
			$this->set_toignore_fields($ignore_this_fields, 'UPDATE');	
				
			$this->assign_field_values();
			$this->do_SQL(array(TABLE_CATEGORIES_DESCRIPTION=>$this->import[TABLE_CATEGORIES_DESCRIPTION]));
			_debug('', ' ENDE kat_import --');
		}
	
}
?>
