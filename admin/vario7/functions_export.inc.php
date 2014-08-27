<?php
/**
 * @version $Id: functions_export.inc.php,v 1.1 2011-07-15 12:33:32 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2005 VARIO Software GmbH
 * 
 * Versionshistorie
 *
 * 15.11.2011 SV
 * - Bei Gastbestellungen kommt kein Datum mit und das Datum bei xtc_date_to_vario_date war = '..', jetzt übergibt er in diesen Sonderfall das aktuelle Datum
 * 17.03.2011 AB
 * - neue Funktion customers_status_to_vario_preisgruppe
 * 16.12.2010 AB
 * - copy/paste-Fehler: gender f = Anrede Frau, stand auf Herr
 * 02.04.2010 AB
 * - customers_status_to_preisgruppe obsolete, PREISGRUPPE wird jetzt über den Namen dynamisch bestimmt
 * 
 */

function create_field_info($source_field, $dest_field, $default = "", $function = null) {
    return array(
	    'source' 		=> $source_field,
    	'destination'	=> $dest_field,
	    'default'		=> $default,
    	'function'		=> $function,
    );
}

function get_kundennr($ds) {
	if (strlen($ds['customers_cid']) == 0) {
		$return = '-'.$ds['customers_id'];
	} else {
		$return = $ds['customers_cid'];
	}
	return $return;
}

function get_matchcode($ds) {
    if (strlen($ds['entry_company']) == 0) {
        $Matchcode = trim(substr($ds['customers_lastname'], 0, 4));
    } else {
        $Matchcode = trim(substr($ds['entry_company'], 0, 4));
    }
    $Matchcode .= trim(substr($ds['entry_city'], 0, 4));
    return strtoupper($Matchcode);
}

function get_anrede($ds) {
    if( !empty($ds['entry_company']) ) {
        return "Firma";
    } elseif($ds['entry_gender']=="m") {
        return "Herr";
    } elseif($ds['entry_gender']=="f") {
        return "Frau";
    }
}

function get_nameX($zeile, $ds) {
    $nz1 = '';
    $nz2 = '';
    $nz3 = '';
    
	if (strlen($ds['entry_company']) >= 1) {
	    	
	    // FIRMA!
		if (VARIO_CFL_TO_NAME123 == 1) {
			$nz1 = 	$ds['entry_company'];
			$nz2 = 	$ds['entry_firstname'];
			$nz3 = 	$ds['entry_lastname'];
			
	       	if ($zeile == 3 && $nz2 <> $nz3) {
       			$nz3 = '';
	       	}
	       	
	    } else {
	    	
	        $nz1 = $ds['entry_company'];
	
	        if ((strlen($ds['entry_lastname']) >= 1)
	        && (strlen($ds['entry_firstname']) >= 1)) {
	            if ($zeile==1) {
	                $nz2 = $ds['entry_lastname'] .", ". $ds['entry_firstname'];
	            } else {
	                $nz2 = $ds['entry_firstname'] . ' ' . $ds['entry_lastname'];
	            }
	        } elseif (strlen($ds['entry_lastname']) >= 1) {
	            $nz2 = $ds['entry_lastname'];
	        } else {
	            $nz2 = $ds['entry_firstname'];
	        }
	    }
	    
    } else {

    	// PERSON
	    if ((strlen($ds['entry_lastname']) >= 1)
	        && (strlen($ds['entry_firstname']) >= 1)) {
	        $nz1 = $ds['entry_lastname'] .", ". $ds['entry_firstname'];
	    } elseif (strlen($ds['entry_lastname']) >= 1) {
	        $nz1 = $ds['entry_lastname'];
	    } else {
	        $nz1 = $ds['entry_firstname'];
	    }
	}
	
	if ($zeile == 1) return $nz1;
	if ($zeile == 2) return $nz2;
	if ($zeile == 3) return $nz3;
	return '';
	    
}

function get_briefanrede($ds) {
	$lastname = $ds['customers_lastname'];
    if (strlen($ds['entry_company']) >= 1) {
    	$briefanrede = "Sehr geehrte Damen und Herren,";
    } else {
    	if($ds['entry_gender']=="m") { 
    		$briefanrede = "Sehr geehrter Herr $lastname,"; 
    	} elseif ($ds['entry_gender']=="f") {
    		$briefanrede = "Sehr geehrter Frau $lastname,";	
    	} else {
    		$briefanrede = "Sehr geehrte Damen und Herren,";
    	}
    }
	return $briefanrede;
}

function check_kundenkonto($ds) {
    if ($ds['customers_status'] == 1) {		// 1 ist das Gastkonto
        return 'N';
    } else {
        return 'J';
    }
}

function get_belegnr($nr) {
    $length = strlen($nr);
    if ($length >= 8) {
        $nr = substr($nr, $length -7);
    }
    return sprintf("T%07d", $nr);
}

function get_belegschluessel($nr) {
    return "00". get_belegnr($nr);
}

function xtc_date_to_vario_date($date, $wt = false){
	if ($wt == false) {
		$date = substr($date, 0, 10);	
    	$date = explode('-', $date);
    	$ret_date = $date[2] .".". $date[1] .".". $date[0];
	} else {
		$time = substr($date, 11, 8);
		$date = substr($date, 0, 10);
		$date = explode('-', $date);
		$time = explode(':', $time);
    	$ret_date = $date[2] .".". $date[1] .".". $date[0] ." ".$time[0] .".". $time[1] .".". $time[2];
	}
    if ($ret_date!='00.00.0000' && $ret_date!='..') {
        return $ret_date;
    }
	elseif ( $ret_date=='..' ) {
				   return date( 'd.m.Y' ,time());
	}

    return "";

}

function vario_date_to_xtc_date($date, $wt = false){
    $date = substr($date, 0, 10);
    $date = explode('.', $date);
    $ret_date = $date[2] ."-". $date[1] ."-". $date[0];
    if ($ret_date != '0000-00-00') {
        return $ret_date;
    }
    return "";
}

function xtc_to_vario_float($value) {
	// Falls Einsatz International, dann Kommentar deaktivieren
    //require_once (DIR_FS_INC.'xtc_get_currencies_values.inc.php');
    //$currency = xtc_get_currencies_values('EUR');					// nur Euro wird von VARIO unterstützt
    $decimal_point = (isset($currency['decimal_point']))?$currency['decimal_point']:',';
    $value = round($value, 2);
    return replace_dot_to_comma($value);
}

function xtc_bool_to_vario_bool($val) {
	$val = (($val == 1)?'J':'N');
	return $val;
} 

function replace_dot_to_comma( $float ) {
    return str_replace('.', ',', $float);
}

function get_beleg_report($br) {
	//_debug($br, 'START get_beleg_report');
	if (strlen($br) == 0) $br = 'BELEG';	// der Standard bei VARIO
	//_debug($br, ' ENDE get_beleg_report');
	return $br;
}

function customers_status_to_vario_preisgruppe($cs_id) {
    $sql =   "select c.configuration_key "
    		."  from configuration c "
    		." inner join customers_status cs "
    		."    on cs.customers_status_name = c.configuration_value " 
    		."   and cs.language_id = ".VARIO_XTC_DE_LANGUAGE_ID  
    		." where cs.customers_status_id = " . $cs_id;  
    if ($result = vDB::query($sql))  {
    	if($row = vDB::fetch_assoc($result)){
        	$configuration_key  = explode('=', $row['configuration_key']);
        	return $configuration_key[1];	// VARIOPG=3
    	}
    }
    return 0;	// keine Preisgruppe gefunden, z.B. bei Gast
}

?>
