<?php
/**
	 * VARIO functions file. 
	 * 
	 * @version $Id: functions_import.inc.php,v 1.1 2011-07-15 12:33:32 ag Exp $
	 * @author Andreas Büsch <ab@vario-software.de>
	 * @since 2005
	 * @copyright Copyright (c) 2009 VARIO Software GmbH
	 *
	 
	 Revision 1.15  2012/11/30  sv
	 - Bei GambioGX2 Cache nach Replikation leeren

	 $Log: not supported by cvs2svn $
	 Revision 1.15  2011/03/16 09:03:22  ag
	 - BUG bei get_all_customers_status_ids beseitigt, die Korrektur vom 15.03.2011 war ein Irrweg
	
	 Revision 1.14  2011/03/15 21:27:49  AB
	 15.03.2011 AB (7.1.010.004)
	 - BUG bei get_all_customers_status_ids beseitigt, die Korrektur vom 02.02.2011 war ein Irrweg
	
	 Revision 1.13  2011/02/07 16:54:48  ag
	 -vario_date_to_xtc_date beachtet nun, ob sich das übergebene Datum bereits im Zielformat befindet
	
	 Revision 1.12  2011/02/02 16:42:02  ag
	 - Funktion vario_sprach_id_to_xtc_languages_id gibt nun nicht mehr 2 zurück, wenn das Mapping für die Sprache nicht gefunden wurde, sondern -1.
	   Die Import-Funktionen brechen mit dem Import des Datensatzes ab, wenn -1 zurückgegeben wird.
	
	 Revision 1.11  2011/02/02 11:23:42  ag
	 -Die Funktion get_all_customers_status_ids hat den customers_status als Wert im Ergebnis-Array gespeichert, nicht als Key
	
	 
	 * 19.09.2010 AB
	 *   get_vario_default_language_id liefert VARIO_XTC_DE_LANGUAGE_ID 
	 * 09.05.2010 AB
 	 *   einstellige LKZ abgefangen
	 */


function get_file($DateiName) {
	// TODO: Fehler abfangen
    if (File_Exists($DateiName)) {
        $Datei = FOpen($DateiName, "rb");
        $Inhalt = FRead($Datei, FileSize($DateiName));
        FClose($Datei);
        return $Inhalt;
    } else {
        return False;
    }
}

function create_field_info($table, $field, $function = null) {
    return array(
    	'table' 	=> $table,
    	'field'		=> $field,
    	'function'	=> $function,
    );
}

function get_pri_key($table) {
    $result = vDB::query("DESCRIBE $table");
    while($row = vDB::fetch_array($result)){
        if($row['Key']=='PRI') $PRIKEYS[]=$row['Field'];
    }
    return $PRIKEYS;
}

function get_vario_default_language_id() {
    return VARIO_XTC_DE_LANGUAGE_ID; 
}

function get_all_customers_status_ids() {
    $sql = "select distinct customers_status_id from ".TABLE_CUSTOMERS_STATUS;
    if( $result = (vDB::query($sql)) ) {
    	while($row = vDB::fetch_assoc($result)){
        	//$return[$row['customers_status_id']] = 1;		// diese Korrektur war nicht korrekt
        	//$return[] = $row['customers_status_id'];			// 14.03.2011 AB  //diese Korrektur war nicht korrekt
			$return[$row['customers_status_id']] = $row['customers_status_id'];			// 16.03.2011 AG
    	}
    }
    return $return;
}

function get_all_languages_ids() {
    $sql = "select distinct languages_id from ".TABLE_LANGUAGES;
    if( $result = (vDB::query($sql)) ) {
    	while($row = vDB::fetch_assoc($result)){
        	$return[] = $row['languages_id'];
    	}
    }
    return $return;
}

function customers_status_name_to_customers_status_id($customers_status_name) {
    $sql = "select customers_status_id from ".TABLE_CUSTOMERS_STATUS." where customers_status_name = '$customers_status_name'";
    _debug($sql, 'customers_status_name_to_customers_status_id-q');
    if( $result = (vDB::query($sql)) ) {
    	if ($row = vDB::fetch_assoc($result)){
        	$return = $row['customers_status_id'];
    	}
    }
    _debug($return, 'customers_status_name_to_customers_status_id-r');
    return $return;
}

function get_one_configuration_value($key) {
    $sql = "SELECT configuration_value FROM ". TABLE_CONFIGURATION ." WHERE configuration_key = '$key'";
    $value = '';
    if( $result = (vDB::query($sql)) ) {
    	if ($row = vDB::fetch_assoc($result)){
        	$value = $row['configuration_value'];
    	}
    }
    return $value;
}

function vario_name1_to_firstname($name1){
    if(strpos($name1, ",")!== false){
        $names = explode(",", $name1);
        return trim($names[1]);
    }else{
        $names = explode(" ", $name1);
        return trim($names[0]);
    }
    return "";
}

function vario_name1_to_lastname($name1){
    if(strpos($name1, ",")!== false){
        $names = explode(",", $name1);
        return trim($names[0]);
    }else{
        $names = explode(" ", $name1);
        return trim($names[1]);
    }
    return "";
}

function vario_iso_land_to_country_id($lkz) {
	//_debug($lkz, 'vario_iso_land_to_country_id-q');
    switch (strtoupper($lkz)){
        case 'A':
            $lkz = 'AT';
            break;
        case 'B':
            $lkz = 'BE';
            break;
        case 'C':
            $lkz = 'CH';
            break;
        case 'D':
            $lkz = 'DE';
            break;
        case 'E':
            $lkz = 'ES';
            break;
        case 'F':
            $lkz = 'FR';
            break;
        case 'FIN':
            $lkz = 'FI';
            break;
        case 'G':
            $lkz = 'GR';
            break;
        case 'H':
            $lkz = 'HU';
            break;
        case 'I':
            $lkz = 'IT';
            break;
        case 'L':
            $lkz = 'LU';
            break;
        case 'N':
            $lkz = 'NO';
            break;
        case 'P':
            $lkz = 'PL';
            break;
        case 'R':
            $lkz = 'RU';
            break;
        case 'S':
            $lkz = 'SE';
            break;
        case 'S':
            $lkz = 'SE';
            break;
        case 'USA':
            $lkz = 'US';
            break;
        case 'VCR':
            $lkz = 'CN';
            break;
    }
	
	$countries_id = null;
    $sql =   "SELECT countries_id FROM ". TABLE_COUNTRIES
    		." WHERE '$lkz' in (countries_iso_code_2, countries_iso_code_3) LIMIT 0, 1";
    $result = vDB::query($sql);
    if ($row = vDB::fetch_array($result)){
        $countries_id = $row['countries_id'];
    }
	//_debug($countries_id, 'vario_iso_land_to_country_id-r');
    return $countries_id;
}

function  vario_sprach_id_to_xtc_languages_id($SPRACH_ID) {
	// falscher Name, alt war: vario_sprachkuerzel_to_xtc_languages_id
	$sql = "select " 
		."    l.languages_id " 
		."  from ".TABLE_CONFIGURATION." c " 
		."  left join ".TABLE_LANGUAGES." l " 
		."    on c.configuration_value = l.code "   
	    ." where c.configuration_key = 'VARIO_SPRACH_ID=".$SPRACH_ID."' " ;
	
	//_debug($sql,'      vario_sprach_id_to_xtc_languages_id');			
	$languages_id = vDB::fetchone($sql);
	// Fallback bei fehlendem Eintrag VARIO_SPRACH_ID=0 de 
	if (!$languages_id) $languages_id = -1;		// -1, damit wird die Import-Funktion abgebrochen...
    return $languages_id;
}

function vario_anrede_to_gender($anrede){
    $anrede = strtolower($anrede);
    if (strpos($anrede, 'herr')!==false) {
        return 'm';
    } elseif (strpos($anrede, 'frau')!==false) {
        return 'f';
    }
}

function vario_date_to_xtc_date($input_date, $input_format = 'd.m.Y', $output_format = 'Y-m-d') {
    if (strpos($input_date,'-'))
	{
		$output_date = $input_date;	
	}
	else
	{
		if(empty($input_format) or empty($input_date)) return ;
		preg_match("/^([\w]*)/i", $input_date, $regs);
		$sep = substr($input_date, strlen($regs[0]), 1);
		$label = explode($sep, $input_format);
		$value = explode($sep, $input_date);
		$array_date = array_combine($label, $value);
		if (in_array('Y', $label)) {
			$year = $array_date['Y'];
		} elseif (in_array('y', $label)) {
			$year = $year = $array_date['y'];
		} else {
			return false;
		}
		$output_date = date($output_format, mktime(0,0,0,$array_date['m'], $array_date['d'], $year));
	}
    return $output_date;
}

function vario_bool_to_xtc_bool($jn, $val_if_empty = 'N'){
	if ($jn == '') $jn =  $val_if_empty;
    $bool = (($jn=='J')?1:0);
    return $bool;
}

function vario_empty_to_null($val){
    $val = (($val=='')?'NULL':$val);
    return $val;
}


function int_int_divide($x, $y) {
	// DIV Funktion für Integerwerte
    return ($x - ($x % $y)) / $y;
}

function lowercase($s) {
    return strtolower($s);
}

function loesche_cache_gambiogx2( $dirname ) {

	$directory_handle = opendir ($dirname );

	while( $filename = readdir ( $directory_handle ) ){

		if  ( !in_array( end(explode('.', $filename)), array( 'css', 'htaccess' ))	// keine "css" und "htaccess"-Dateien löschen
		 && ( !in_array( $filename, array('.', '..')))								// "." und ".." nicht löschen
		 && ( substr($filename,0,4) != 'sess')										// keine Sessions löschen
		 && ( time() - filectime ( $dirname . '/' . $filename ) ) > 60 ){			// Dateien nur löschen wenn sie älter als 60 Sekunden sind

			unlink ( $dirname . '/' . $filename );
		}
	}
}

?>
