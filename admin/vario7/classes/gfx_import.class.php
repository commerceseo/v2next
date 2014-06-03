<?php
	/**
	 * @version $Id: gfx_import.class.php,v 1.2 2011-07-20 14:03:41 ag Exp $
	 * @version $Revision: 1.2 $
	 * @copyright Copyright (c) 2009 VARIO Software GmbH
	 * 
	 * TODO: Löschen kann noch verbessert werden
	 *
	 
	 $Log: not supported by cvs2svn $

	 *  15.04.2014 SV
	 *  Der Bildindex kommt, wenn ein Delete übergeben wird und kein Bild mehr veröffentlicht ist, als leerstring '' an. Wir ziehen -1 ab und der Bildindex ist -1. 
	    Daher müssen wir den $bildindex auf kleiner gleich '<=' prüfen, damit das bild gelöscht werden kann.
	 *  04.03.2014 SV
	 *  VARIO_GFX-Datensatz löschen wenn ein Bild gelösch wird
	 *  07.09.2011 SV
	 *  Bei jedem Bild wird nun, wenn der Artikel nicht in der products existiert, geprüft ob zu dieser webshop_id ein Eintrag in der vario_art existiert. Wenn dem so ist, dann wird das Bild erzeugt und ein Eintrag in der vario_gfx angelegt.
	 *  13.09.2010 AB
	 *    Falls WEBSHOP_ID bei ART nicht gefüllt, Satz verwerfen
	 *	12.09.2010 AB
	 *    Bild für HST (hersteller)
	 *   14.06.2010 AB
	 *     1. Bild auch in vario_gfx speichern
	 * 
	 */

	include_once("../admin/includes/classes/".IMAGE_MANIPULATOR);

	class gfx_import extends vario_import {

		function gfx_import($exp_input){
			_debug($exp_input, 'START gfx_import -- Übergeben wurde der Datensatz $exp_input');
			$this->set_exp_source($exp_input);

			/* Das steht in der GFX, wenn denn die SQL in der RWEB_TABS stimmt
			 * ID
			 * TABELLE
			 * SCHLUESSEL
			 * BILDINDEX
			 * DAEINAME
			 */
			
			$aktion 		= $this->get_exp_value('AKTION');
			$tabelle 		= $this->get_exp_value('TABELLE');
			$bildindex 		= (int)$this->get_exp_value('BILDINDEX') - 1; 	// der xtc fängt bei 0 an
			$dateiname 		= $this->get_exp_value('DATEINAME');			// Fallback Dateiname macht schon der Export aus V7
			
			if ($tabelle == 'ART') {
				
				$ref_id = $this->get_exp_value('WEBSHOP_ID');
				
				_debug($ref_id, "  ART gfx_import -- products_id");
				
				if (!$ref_id) {
					echo '--> Import wegen fehlender WEBSHOP_ID ignoriert ...';
					return;
				}
				
				if ($ref_id) {
					// Prüfen, ob es den Artikel mit der ID überhaupt gibt.
					// Evtl. kommt hier eine Bild eines nicht im Shop vorhandenen Artikels an
					$products_id = vDB::fetchone("select products_id from ".TABLE_PRODUCTS." where products_id = $ref_id");

					if ( !$products_id ) {

						return;
					}
				}

				// das erste Bild wird in der products, alle weiteren Bilder in der products_images gespeichert
				if ((int)$bildindex <= 0) {	// oben wurde schon -1 abgezogen, es darf auch gelöscht werden ($aktion == 'D')
					/*
					 *   products_id 
					 *   products_image 
					 */
					
					$this->set_fields( array(
						'WEBSHOP_ID'	=> array(create_field_info(TABLE_PRODUCTS, 'products_id')),
						'DATEINAME'		=> array(create_field_info(TABLE_PRODUCTS, 'products_image')),
					));
					
					if ($aktion <> 'D') {
						$this->create_products_images($dateiname);	
					} else {
						$this->setField(TABLE_PRODUCTS, 'products_image', '');	// Bilddatename einfach leeren, Datei wird unten gelöscht.
						$this->setAction('U');	// nicht löschen, nur updaten
						$aktion = 'U';
						$this->delete_image_files($dateiname);
					}
					
					$this->assign_field_values();
					$this->do_SQL(array(TABLE_PRODUCTS=>$this->import[TABLE_PRODUCTS])); //
				} else {
					/*
					 *   image_id 
					 *   products_id 
					 *   image_nr 
					 *   image_name 
					 */
					$this->set_fields( array(
						'ID'			=> array(create_field_info(TABLE_PRODUCTS_IMAGES, 'image_id')),
						'WEBSHOP_ID'	=> array(create_field_info(TABLE_PRODUCTS_IMAGES, 'products_id')),
						'DATEINAME'		=> array(create_field_info(TABLE_PRODUCTS_IMAGES, 'image_name')),
					));
					$this->setField(TABLE_PRODUCTS_IMAGES, 'image_nr', $bildindex);
					
					if ($aktion <> 'D') {
						$this->create_products_images($dateiname);	
					} else {
						$this->delete_image_files($dateiname);
					}
					
					// NEU: Vor dem Import evtl. vermeintlich doppelte löschen
					$this->sql = "delete from ".TABLE_PRODUCTS_IMAGES." where products_id = ".$ref_id." and image_nr = ".$bildindex;
					_debug($this->sql,'      gfx_import: DELETE Doubles');			
					xtc_db_query($this->sql);
					
					$this->assign_field_values();
					$this->do_SQL(array(TABLE_PRODUCTS_IMAGES=>$this->import[TABLE_PRODUCTS_IMAGES])); //
				}	 
			} 
			
			if ($tabelle == 'KAT') {
				/*
				 *   categories_id 
				 *   categories_image 
				 */
				$ref_id 		= $this->get_exp_value('KAT_ID');
				_debug($ref_id, "  KAT gfx_import -- categories_id:");
				
				$this->set_fields( array(
						'KAT_ID'		=> array(create_field_info(TABLE_CATEGORIES, 'categories_id')),
						'DATEINAME'		=> array(create_field_info(TABLE_CATEGORIES, 'categories_image')),
				));
				
				if ($aktion <> 'D') {
					// nothing to do
				} else {	
					$this->setField(TABLE_CATEGORIES, 'categories_image', '');	// Bilddatename einfach leeren, Datei wird unten gelöscht.
					$this->setAction('U');	// nicht löschen, nur updaten
					$aktion = 'U';
					$this->delete_image_files($dateiname);
				}
				
				$this->assign_field_values();
				$this->do_SQL(array(TABLE_CATEGORIES=>$this->import[TABLE_CATEGORIES])); //
				
			} 
			
			if ($tabelle == 'HST') {
				/*
				 *   manufacturers_id 
				 *   manufacturers_image 
				 */
				$ref_id 		= $this->get_exp_value('HST_ID');
				_debug($ref_id, "  HST gfx_import -- manufacturers_id");
				
				$this->set_fields( array(
						'HST_ID'		=> array(create_field_info(TABLE_MANUFACTURERS, 'manufacturers_id')),
						'DATEINAME'		=> array(create_field_info(TABLE_MANUFACTURERS, 'manufacturers_image')),
				));
				
				if ($aktion <> 'D') {
					// nothing to do
				} else {	
					$this->setField(TABLE_MANUFACTURERS, 'manufacturers_image', '');	// Bilddatename einfach leeren, Datei wird unten gelöscht.
					$this->setAction('U');	// nicht löschen, nur updaten
					$aktion = 'U';
					$this->delete_image_files($dateiname);
				}
				
				$this->assign_field_values();
				$this->do_SQL(array(TABLE_MANUFACTURERS=>$this->import[TABLE_MANUFACTURERS])); //
				
			} 
			
			if ($aktion == 'D') {	// ... dies ist ein Löschbefehl
					/*
					 *   image_id 
					 *   products_id 
					 *   image_nr 
					 *   image_name 
					 */
				$ref_id    = $this->get_exp_value('ID');
				_debug($ref_id, "  GFX gfx_import -- DELETE VARIO-ID:");
				
				// Dateiname besorgen
				$this->sql = "select image_name from ".TABLE_PRODUCTS_IMAGES." where image_id = ".$ref_id;
				$dateiname		= vDB::fetchOne($this->sql);
				_debug($dateiname,'      gfx_import: DATEINAME der zu löschenden Date: ');			
				
				$this->sql = "delete from ".TABLE_PRODUCTS_IMAGES." where image_id = ".$ref_id;
				_debug($this->sql,'      gfx_import: DELETE:');			
				xtc_db_query($this->sql);
				$GLOBALS['affected_rows'][TABLE_PRODUCTS_IMAGES]['DELETE'] += 1; 
	
				$this->delete_image_files($dateiname);
			} 

		}

		function create_products_images($img_filename){
			
			$image_path = DIR_FS_CATALOG_ORIGINAL_IMAGES.$img_filename;
			$image_path = str_replace("//", "/", 	$image_path);
			$image_path = str_replace('\\\\', "/", 	$image_path);
			$image_path = str_replace('\\', "/", 	$image_path);
			
			_debug($image_path, "      create_products_images -- img_filename");
			if (file_exists($image_path)) {
				
				$products_image_name = $img_filename; // $products_image_name ist die Variable in den Includes	

				//image processing
				include_once (DIR_FS_CATALOG.'admin/includes/classes/class.image_manipulator_gd2.php');
				include (DIR_FS_CATALOG.'admin/includes/product_thumbnail_images.php');
				require (DIR_FS_CATALOG.'admin/includes/product_info_images.php');
				require (DIR_FS_CATALOG.'admin/includes/product_popup_images.php');
				require (DIR_FS_CATALOG.'admin/includes/product_mini_images.php');
				
				if (VARIO_SHOP_USED == 'GAMBIOGX2' ) {
					 require (DIR_FS_CATALOG.'admin/includes/product_gallery_images.php');
				}

			} else _debug($image_path, __LINE__.", of ".__FUNCTION__." image doesn't exist");
		}

		function delete_image_files($image) {
			echo "  DEL gfx_import ".DIR_FS_CATALOG_ORIGINAL_IMAGES.$image."\r\n";
			if (file_exists(DIR_FS_CATALOG_ORIGINAL_IMAGES.$image)) {
				@ unlink(DIR_FS_CATALOG_ORIGINAL_IMAGES.$image);
			}
			_debug(DIR_FS_CATALOG_ORIGINAL_IMAGES.$image, "  DEL gfx_import");
			if (file_exists(DIR_FS_CATALOG_POPUP_IMAGES.$image)) {
				@ unlink(DIR_FS_CATALOG_POPUP_IMAGES.$image);
			}
			_debug(DIR_FS_CATALOG_POPUP_IMAGES.$image, "  DEL gfx_import");
			if (file_exists(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$image)) {
				@ unlink(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$image);
			}
			_debug(DIR_FS_CATALOG_ORIGINAL_IMAGES.$image, "  DEL gfx_import");
			if (file_exists(DIR_FS_CATALOG_INFO_IMAGES.$image)) {
				@ unlink(DIR_FS_CATALOG_INFO_IMAGES.$image);
			}
			_debug(DIR_FS_CATALOG_INFO_IMAGES.$image, "  DEL gfx_import");
		}

	}
?>
