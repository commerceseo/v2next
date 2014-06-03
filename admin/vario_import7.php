<?php	
/**
 * @version $Id: vario_import7.php,v 1.2 2011-07-18 14:27:48 ag Exp $
 * 
 * 30.11.2012 SV: Bei GambioGX2 nach Replikation Cache leeren
 * 26.10.2011 SV: Alle Klassennamen aufgrund des Dateinamens richtig identifizieren, also auch art_ff
 * 20.11.2010 AB: neue Version VARIO7.1 
 * 06.08.2010 AB: SPRACH_ID
 * 04.08.2010 AB: SPR_ID
 * 
 * TODO: 
 * 		Errorcodes bei der Rückgabe definieren
 * 		adr_import
 * 		ap1_import
 * 		ap3_import
 * 		ap5_import
 * 		ard_import
 * 		arg_import (nur dann, wenn auch noch mal runter auf VF6 gegangen wird bei Textil
 * 		art_xse_import
 * 		par_import
 * 		spr_import
 * 		zgt_import
 */

  	$timer_start 	= microtime();
	$aktion 		= $_GET['Aktion'];
	$DateiName 		= $_GET['DateiName'];
	if (!empty($_GET['Show'])) $show = $_GET['Show'];	
	
	@set_time_limit(0);
	set_include_path(get_include_path() . PATH_SEPARATOR . './' . PATH_SEPARATOR . './vario7/classes');
	
  	include_once('vario7/configure.inc.php');								// 
  	include_once('vario7/configure_import.inc.php');						// 
  	include_once('vario7/functions.inc.php');								// 

	include_once('includes/application_top.php');	
  	include_once("vario7/functions_import.inc.php");
  	
	
    switch($aktion){
		
		case "ClearFiles":

			$delete_pattern[] = DIR_FS_ADMIN."vario7/logs/*.log";
            $delete_pattern[] = DIR_FS_CATALOG."import/vario/files/*.exp*";
            $delete_pattern[] = DIR_FS_CATALOG."export/vario/files/*.exp*";
            foreach ($delete_pattern as $to_delete){
            	echo "<p><B>Delete: </B>$to_delete ($show)</p>";
            	rm($to_delete, $show);
            	echo "</p>";
            }
			
		break;
			
		case "ClearTables":

            $delete_pattern[] = DIR_FS_CATALOG_ORIGINAL_IMAGES."*.jpg";
            $delete_pattern[] = DIR_FS_CATALOG_THUMBNAIL_IMAGES."*.jpg";
            $delete_pattern[] = DIR_FS_CATALOG_INFO_IMAGES."*.jpg";
            $delete_pattern[] = DIR_FS_CATALOG_POPUP_IMAGES."*.jpg";
            $delete_pattern[] = DIR_FS_ADMIN."vario7/logs/*.log";
            $delete_pattern[] = DIR_FS_CATALOG."download/*.*";
            $delete_pattern[] = DIR_FS_CATALOG."import/vario/files/*.ex*";
            $delete_pattern[] = DIR_FS_CATALOG."export/vario/files/*.ex*";
            $delete_pattern[] = DIR_FS_CATALOG."templates_c/*.*";
            // $delete_pattern[] = DIR_FS_CATALOG."media/products/*.*";
            foreach ($delete_pattern as $to_delete){
            	echo "<p><B>Delete: </B>$to_delete</p>";
            	rm($to_delete, $show);
            }
            if (VARIO_SHOP_USED == 'GAMBIOGX2') {
            	$execute_sql = DIR_FS_CATALOG."/admin/vario7/clear_tables_gambiogx2.sql";
            } else {
            $execute_sql = DIR_FS_CATALOG."/admin/vario7/clear_tables.sql";
            } 

            $sql_lines = file_get_contents($execute_sql);
            $sql_linesArr = explode(";",$sql_lines);
			foreach ($sql_linesArr as $line=>$sql){
				if (trim($sql) > '') {
					echo $sql."<BR>";
					if($result = mysql_query($sql)){
						"Line $line: ".$sql."<BR>";
					} else {
						echo "mysql error, line $line: ".mysql_error();
					}
				}
			}
						
		break;
			
		case "Replikation":
			
			//erstmal ein Datenbank-Update durchführen, falls notwendig...
			include_once(DIR_FS_ADMIN."vario_dbupdates7.php");
			//erstmal ein Datenbank-Update durchführen, falls notwendig... --- ENDE
			
  			// _debug("START Replikation mit EXP-Daten aus Datei ".$DateiName);
			if (!isset($DateiName) || trim($DateiName) == ''){
  				_debug("Variable $DateiName nicht gesetzt!", __LINE__);
  				echo "ERROR: Datei $DateiName nicht gesetzt!";
  				exit;
  			}
  			
			include_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'xtcPrice.php');
			
  			// anhand des Dateinamens die Klasse identifizieren
			$import_class_prefix    = explode('_', $DateiName);

			unset ( $import_class_prefix[count ( $import_class_prefix ) - 1] );

			$import_class_prefix = implode( "_", $import_class_prefix );

			$import_class_prefix	= strtolower($import_class_prefix);
			$time 			= date("Ymd-His");
			$logfilename 	= (empty($DateiName))?"log.$time.log":"$time.$DateiName.log";
    		
			// je nach Einsatz abweichende includes laden
			// Standard
			$classesdir  = "vario7/classes";
			$classesdirm = $classesdir;
			if ( VARIO_PRODUCT_USED == 'VARIO7.1' ) {
  			}
			if ( VARIO_PRODUCT_USED == 'VARIO7' ) {
  			}
  			if ( VARIO_PRODUCT_USED == 'FAKTURA' ) {
  				if ($import_class_prefix == 'art') $classesdirm = "vario6/classes";	// V7-Version mit Sonderbehandlung für Sxx
  			}
  			if ( VARIO_PRODUCT_USED == 'TEXTIL' ) {
  				if ($import_class_prefix == 'art') $classesdirm = "vario6/classes";	// V6-Version mit Sonderbehandlung für FARBE, LÄNGE und Sxx
  				if ($import_class_prefix == 'arg') $classesdirm = "vario6/classes";	
  				if ($import_class_prefix == 'gp3') $classesdirm = "vario6/classes";	
  			}
  			if ( VARIO_PRODUCT_USED == 'COMPACT' ) {
  				if ($import_class_prefix == 'art') $classesdirm = "vario6/classes";	// V6-Version mit Sonderbehandlung für Sxx
  			}
  			
  			include_once($classesdir."/vDB/mysql/vDB.class.php");
			include_once($classesdir."/sql.class.php");
			include_once($classesdir."/vario_import.class.php");

			$class_file_name = $classesdirm.'/'.$import_class_prefix.'_import.class.php';
			if (in_array($import_class_prefix, $valid_tables)) {
  			
				echo '--> Importklasse ' . $class_file_name . ' geladen ...';
				require_once($class_file_name);
				
				$files_path = DIR_FS_DOCUMENT_ROOT."import/vario/files/";
				$Inhalt =  get_file($files_path.$DateiName);
	  			// _debug($Inhalt, "     Replikation mit EXP-Daten aus Datei ".$DateiName);
	  			
				if ($Inhalt <> False) {
	  				
					if (VARIO_CONVERT_TO_UTF8 == 1) {
						$Inhalt = utf8_encode($Inhalt);		// ANSI --> UTF8
					}
					
					$FeldTrennZeichen = Chr(23);
	  				$SatzTrennZeichen = Chr(25);
					$DateiInhalt = explode($SatzTrennZeichen, $Inhalt);
					
					$Counter = 0;
					$GLOBALS['affected_rows'] = array();
					
					foreach ($DateiInhalt as $aktZeile) {
			  			// _debug($aktZeile, "EXP-ZEILE");
						
						if ($Counter == 0) {
							
							// Erste Zeile hat Spaltennamen
				    		$DateiFelder = explode($FeldTrennZeichen, StrToUpper($aktZeile));
				    		
						} elseif(trim($aktZeile)!='') {
							
							// und hier die Daten
							$aktDS = explode($FeldTrennZeichen, $aktZeile);	// Felder haben nur Nummern
							//_debug($DateiFelder, '-- vario_import7.php: DateiFelder');
							//_debug($aktDS,       '-- vario_import7.php: aktDS');
							$expDS = array_combine($DateiFelder, $aktDS);	// Feldnamen setzen
							unset($expDS['']);								// Irgendeinen leeren Eintrag wegmachen
							$classname = ($expDS['TAB']!="")?strtolower($expDS['TAB']."_import"):$classname;
							if(class_exists($classname)){
								$obj = new $classname($expDS);	// dieser Aufruf macht die ganze Arbeit!
								$obj->after_each();
							} else {
								_debug("Cannot instantiate non-existent class: $classname ", __LINE__);
							}
						}
						$Counter++;
					}
					if (method_exists($obj, 'after')) $obj->after();
					
					if (is_array($GLOBALS['affected_rows'])) {
						echo "\r\nAusgeführte Operationen:";
						foreach ($GLOBALS['affected_rows'] as $table=>$actions) {
							echo "\r\n  $table : ";
							foreach ($actions as $action=>$count)
								echo "$count $action(s), ";
						}
					}
				} else {
	  				echo "ERROR: Dateiname $DateiName gefunden, aber Datei nicht da oder kein Inhalt!";
	  				_debug($Inhalt, "ERROR: Datei $DateiName gefunden, aber kein Inhalt!", __LINE__);
				}
				
  			} else {
				echo '--> Importklasse ' . $class_file_name . ' ignoriert ...';
  			}

			if (VARIO_SHOP_USED == 'GAMBIOGX2') {

  				_debug( DIR_FS_CATALOG . "cache", "Leere Cache GambioGX2", __LINE__);
				loesche_cache_gambiogx2( DIR_FS_CATALOG . "cache" );
			}

			$timer_stop		= microtime();
			$time_start		= explode(' ', $timer_start);
			$time_end		= explode(' ', $timer_stop);
			$timer_total	= number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
			echo "\r\nDauer: $timer_total Sekunden.\r\n";
  			_debug('',' ENDE Replikation');
			
		break;
		default:
			_debug("Aktion $aktion nicht vorgesehen.");
  			echo "ERROR: $aktion nicht definiert!";
			break;
    }
   
	$timer_stop		= microtime();
	$time_start		= explode(' ', $timer_start);
	$time_end		= explode(' ', $timer_stop);
	$timer_total	= number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
	
	require(DIR_WS_INCLUDES . 'application_bottom.php'); 
?>
