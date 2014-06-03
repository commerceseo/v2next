<?php
/**
 * @version $Id: adr_import.class.php,v 1.3 2011-07-21 13:50:12 ag Exp $
 * @version $Revision: 1.3 $
 * @copyright Copyright (c) 2005 VARIO Software GmbH
 *
 * 22.07.2010 SV
 * - trim für E-Mail, da die E-Mail teilweise mit vielen Leerzeichen in VARIO eingetragen wird und Adressen daher doppelt angelegt werden. Als Workaround zu sehen.
 *
 * 12.07.2013 SV
 * Bei ABC-Kennung = '' return DEFAULT_CUSTOMERS_STATUS_ID
 *
 * 17.07.2010 SV
 * - Methoden get_customers_id_from_customers_cid und get_customers_id_from_email_address korrigiert. Die "web_id" in der vario_adr wird jetzt auch mit der korrekt ermittelten customers_id überschrieben.
 *
 * 03.07.2010 AB
 * - vario_adr
 *
 * 03.06.2010 AB
 * - FAKTURIERUNGSART, OHNEMWST	
 * 
 * 09.05.2010 AB
 * - adr_import.php: getFirstName, getLastName, getCompanyName entfernt, RWEB_TABS[ADR] regelt das und liefert korrekt Vorname, Nachname, Firma
 * * 
 * 05.09.2011 SV
 * - adr_import.php: Adminstatus geht nun nicht mehr verloren
 *
 */

require_once(DIR_FS_INC . 'xtc_encrypt_password.inc.php');
include_once('./vario7/html2text.inc.php');

class adr_import extends vario_import {

	var $customers_id, $address_book_id;
	
	function adr_import($exp_input) {
		
		_debug($exp_input, 'START adr_import -- Übergeben wurde der Datensatz $exp_input');
		$this->set_exp_source($exp_input);
				
		// Parameter besorgen
        $VARIO_NEWSLETTER_FELD = get_one_configuration_value('VARIO_NEWSLETTER_FELD');
		$VARIO_GEBURTSTAGS_FELD = get_one_configuration_value('VARIO_GEBURTSTAGS_FELD');

		//!!! WICHTIG !!! -> Niemals über die WEB_ID gehen, da VARIO einen anderen Zählerkreis hat als der Shop und so kommt es zu Kollisionen!!!
		//$this->customers_id = $this->get_exp_value("WEB_ID"); // customers_id
		
		$action = $this->get_exp_value('AKTION');	 
		// TODO Notaus!
		if ($action == 'D') return; 
		
		// customers_id - Handling, WEB_ID
		// Ist die KUNDENNR gefüllt, so ist KUNDENNR = customers_cid
		// Ist die EMAIL gefüllt, so ist EMAIL = customers_email_address
		if (!$this->customers_id) {
			$this->customers_id = $this->get_customers_id_from_customers_cid($this->get_exp_value("KUNDENNR"));
			if ($this->customers_id === 'ERROR')
			{
			  return;
			}
		}
		if (!$this->customers_id) {
			$this->customers_id = $this->get_customers_id_from_email_address(trim($this->get_exp_value("EMAIL")));
			if ($this->customers_id === 'ERROR')
			{
			  return;
			}
		}

		
		$this->set_fields( array(
			// TABLE_CUSTOMERS ist das LOGIN, nicht die Adresse
			// RWEB_TABS[ADR] liefert die Felder ANREDE VORNAME NACHNAME
    	    'WEB_ID'			=> array (create_field_info(TABLE_CUSTOMERS, 		'customers_id')),
    	    'KUNDENNR'			=> array (create_field_info(TABLE_CUSTOMERS, 		'customers_cid')),
			'ANREDE'			=> array(	create_field_info(TABLE_CUSTOMERS, 		'customers_gender', 	'vario_anrede_to_gender')),
			'VORNAME'			=> array(	create_field_info(TABLE_CUSTOMERS, 		'customers_firstname')),
			'NACHNAME'			=> array(	create_field_info(TABLE_CUSTOMERS, 		'customers_lastname')),
			'EMAIL'				=> array(	create_field_info(TABLE_CUSTOMERS, 		'customers_email_address')),
			'TELEFON1'			=> array(	create_field_info(TABLE_CUSTOMERS, 		'customers_telephone')),
			'TELEFAX1'			=> array(	create_field_info(TABLE_CUSTOMERS, 		'customers_fax')),
			'USTIDNR'			=> array(	create_field_info(TABLE_CUSTOMERS, 		'customers_vat_id')),
			'RL_CSV_FELDLISTE' 	=> array(	create_field_info(TABLE_CUSTOMERS, 		'shipping_unallowed')),
			'RZ_CSV_FELDLISTE' 	=> array(	create_field_info(TABLE_CUSTOMERS, 		'payment_unallowed')),
		));

		// Ein paar Sonderbehandlungen
		$this->setField(TABLE_CUSTOMERS, 		'customers_id', 			$this->customers_id);					// leer oder WEB_ID 
		$this->setField(TABLE_CUSTOMERS, 		'customers_status', 		$this->set_customers_status()); 		// set_customers_status unterscheidet PREISGRUPPE und ABC_KENNUNG
		
		$DATUMZEIT = date('Y').'-'.date('m').'-'.date('d').' '.date('G').':'.date('i').':'.date('s');				// 2004-02-12T15:19:21
		
        $this->setIgnoredField(TABLE_CUSTOMERS, 'customers_date_added', 	'UPDATE');
		$this->setField(TABLE_CUSTOMERS, 		'customers_date_added', 	$DATUMZEIT);
		$this->setField(TABLE_CUSTOMERS, 		'customers_last_modified', 	$DATUMZEIT);

		$this->setIgnoredField(TABLE_CUSTOMERS, 'customers_password', 		'UPDATE');
        $this->setField(TABLE_CUSTOMERS, 		'customers_password', 		xtc_encrypt_password($this->get_exp_value('WEB_KENNWORT')));
        
		$this->setField(TABLE_CUSTOMERS, 		'customers_newsletter', 	vario_bool_to_xtc_bool($this->get_exp_value($VARIO_NEWSLETTER_FELD)));
		$this->setField(TABLE_CUSTOMERS, 		'customers_dob', 		 	$this->get_exp_value($VARIO_GEBURTSTAGS_FELD));
		
		$this->assign_field_values();
    	$this->do_SQL(array(TABLE_CUSTOMERS=>$this->import[TABLE_CUSTOMERS]));

    	// Jetzt ist auf jeden Fall eine customers_id da!
		// customers_id - Handling, WEB_ID
		// Ist die KUNDENNR gefüllt, so ist KUNDENNR = customers_cid
		// Ist die EMAIL gefüllt, so ist EMAIL = customers_email_address
		if (!$this->customers_id) {
			$this->customers_id = $this->get_customers_id_from_customers_cid($this->get_exp_value("KUNDENNR"));
			if ($this->customers_id === 'ERROR')
			{
			  return;
			}
		}
    	if (!$this->customers_id) {
			$this->customers_id = $this->get_customers_id_from_email_address(trim($this->get_exp_value("EMAIL")));
			if ($this->customers_id === 'ERROR')
			{
			  return;
			}
		}
		// Falls $this->customers_id = null, dann wird eine neue beim Einfügen erzeugt

		_debug($this->customers_id, '==>> adr_import: TABLE_CUSTOMERS');
		
		$this->address_book_id = $this->get_default_address_book_id_form_customers_id($this->customers_id);

		$this->set_fields( array(
			// RWEB_TABS[ADR] liefert die Felder ANREDE FIRMA VORNAME NACHNAME
			// ''				=> array(	create_field_info(TABLE_ADDRESS_BOOK, 	'address_book_id', 		$this->address_book_id)),
			'ANREDE'		=> array(	create_field_info(TABLE_ADDRESS_BOOK, 	'entry_gender', 		'vario_anrede_to_gender')),
			'FIRMA'			=> array(	create_field_info(TABLE_ADDRESS_BOOK, 	'entry_company')),
			'VORNAME'		=> array(	create_field_info(TABLE_ADDRESS_BOOK, 	'entry_firstname')),
			'NACHNAME'		=> array(	create_field_info(TABLE_ADDRESS_BOOK, 	'entry_lastname')),
			'STRASSE'		=> array(	create_field_info(TABLE_ADDRESS_BOOK, 	'entry_street_address')),
			'PLZ'			=> array(	create_field_info(TABLE_ADDRESS_BOOK, 	'entry_postcode')),
			'LKZ'			=> array(	create_field_info(TABLE_ADDRESS_BOOK, 	'entry_country_id', 	'vario_iso_land_to_country_id')),
			'ORT'			=> array(	create_field_info(TABLE_ADDRESS_BOOK, 	'entry_city')),
		));

		$this->setField(TABLE_ADDRESS_BOOK, 		'address_book_id', 			$this->address_book_id); 
		$this->setField(TABLE_ADDRESS_BOOK, 		'customers_id', 			$this->customers_id); 
        $this->setIgnoredField(TABLE_ADDRESS_BOOK, 	'address_date_added', 		'UPDATE');
		$this->setField(TABLE_ADDRESS_BOOK, 		'address_date_added', 		$DATUMZEIT);
		$this->setField(TABLE_ADDRESS_BOOK, 		'address_last_modified ', 	$DATUMZEIT);
		
		$this->assign_field_values();
		$this->do_SQL(array(TABLE_ADDRESS_BOOK=>$this->import[TABLE_ADDRESS_BOOK]));
		
		_debug($this->address_book_id, '==>> adr_import: TABLE_ADDRESS_BOOK');
		
		$this->setField(TABLE_CUSTOMERS_INFO, 		 'customers_info_id', 							$this->customers_id); 
        $this->setIgnoredField(TABLE_CUSTOMERS_INFO, 'customers_info_date_account_created', 		'UPDATE');
        $this->setField(TABLE_CUSTOMERS_INFO, 		 'customers_info_date_account_created', 		$DATUMZEIT); 
		$this->setField(TABLE_CUSTOMERS_INFO, 		 'customers_info_date_account_last_modified',	$DATUMZEIT); 
		
		$this->do_SQL(array(TABLE_CUSTOMERS_INFO=>$this->import[TABLE_CUSTOMERS_INFO]));
		_debug($this->customers_id, '==>> adr_import: TABLE_CUSTOMERS_INFO');
    	
		// Wenn Adresse neu, dann Referenzen nachtragen
		if (!$this->address_book_id) {
    		$this->address_book_id = $this->get_default_address_book_id_form_customers_id($this->customers_id);
			$this->setField(TABLE_CUSTOMERS, 	'customers_id', 				$this->customers_id); 
    		$this->setField(TABLE_CUSTOMERS, 	'customers_default_address_id', $this->address_book_id); 
    		$this->do_SQL(array(TABLE_CUSTOMERS=>$this->import[TABLE_CUSTOMERS]));
			_debug($this->customers_id, '==>> adr_import: TABLE_CUSTOMERS - 2');

			// $this->setField(TABLE_ADDRESS_BOOK, 'address_book_id', 				$this->address_book_id); 
    		// $this->setField(TABLE_ADDRESS_BOOK, 'customers_id', 				$this->customers_id); 
    		// $this->do_SQL(array(TABLE_ADDRESS_BOOK=>$this->import[TABLE_ADDRESS_BOOK]));
			//_debug($this->address_book_id, '==>> adr_import: TABLE_ADDRESS_BOOK - 2');
		} 
		$queryEndTime = array_sum(explode(" ",microtime()));
		$processTime = $queryEndTime - $queryStartTime;
		_debug('  End adr_import: '.$queryEndTime.' Duration: '.$processTime);
    }
	
	
	function set_customers_status() {
		// $customers_status = DEFAULT_CUSTOMERS_STATUS_ID;

		// Sonderfall Admin: Ein Admin-Status wird nicht von VARIO wieder zurückgesetzt
		$sql =   "SELECT customers_status FROM ". TABLE_CUSTOMERS
				." WHERE customers_id = '".$this->customers_id."'";
		if($result = vDB::query($sql)){
			if($row = vDB::fetch_assoc($result)){
				if( $row['customers_status'] == DEFAULT_CUSTOMERS_STATUS_ID_ADMIN) {
					$customers_status = DEFAULT_CUSTOMERS_STATUS_ID_ADMIN;
					return $customers_status;
				}
			}
		}

		// Spezialfall VARIO-ABC-Kennung = xtc-Kundengruppe
		if (VARIO_CUSTOMERS_STATUS_BY_ABC_KENNUNG != 0) {
            $customers_status = customers_status_name_to_customers_status_id($this->get_exp_value('ABC_KENNUNG'));
            if ($customers_status !== null) {
             	return $customers_status;
            } else {
			
				return DEFAULT_CUSTOMERS_STATUS_ID;
			}
		} else {
			// Standardfall Vario-Preisgruppe = xtc-Kundengruppe
			
			/* Scooter Deluxe GmbH - Preisgruppen-Switch */
			$Preisgruppenname = $this->get_exp_value('PREISGRUPPENNAME');

			if( $this->get_exp_value('OHNEMWST') == 'J' ){

				$Preisgruppenname .= 'N';
			}

	    	$customers_status = customers_status_name_to_customers_status_id( $Preisgruppenname );

	    	if ($customers_status) {
	     		return $customers_status;
	    	} else {
				// Falls nix gefunden: Standard, z.B. Neuanlage durch VARIO
				return DEFAULT_CUSTOMERS_STATUS_ID;
	    	}
		}
	    
	}

	function get_customers_id_from_email_address($email_address) {
		$sql =   "SELECT customers_id FROM ". TABLE_CUSTOMERS
				." WHERE customers_email_address = '".$email_address."'";
		_debug($sql, 'get_customers_id_from_email_address-q');
		$value = null;
		if($result = vDB::query($sql)){
			if($row = vDB::fetch_assoc($result)){
				if (mysql_num_rows($result) > 1)
				{
				  _debug('[ERROR] count of customers_email_address > 1', 'get_customers_id_from_email_address-a');  
				  echo '<font size=4 color=red><b>[ERROR] count of customers_email_address &gt; 1</b></font><br>';
				  return "ERROR";
				}
				$value = $row['customers_id'];
			}
		}
		_debug($value, 'get_customers_id_from_email_address-r');
		return $value;
	}
	
	function get_customers_id_from_customers_cid($customers_cid) {
		$sql =   "SELECT customers_id FROM ". TABLE_CUSTOMERS
				." WHERE customers_cid = '$customers_cid'";
		_debug($sql, 'get_customers_id_from_customers_cid-q');
		$value = null;
		if($result = vDB::query($sql)){
			if($row = vDB::fetch_assoc($result)){
				if (mysql_num_rows($result) > 1)
				{
				  _debug('[ERROR] count of customers_cid > 1', 'get_customers_id_from_customers_cid-a');  
				  echo '<font size=4 color=red><b>[ERROR] count of customers_cid &gt; 1</b></font><br>';
				  return "ERROR";
				}
				$value = $row['customers_id'];
			}
		}
		_debug($value, 'get_customers_id_from_customers_cid-r');
		return $value;
	}
	
	function get_default_address_book_id_form_customers_id($customers_id) {
		$sql =   "SELECT address_book_id FROM ". TABLE_ADDRESS_BOOK
				." WHERE customers_id = '$customers_id'";
		_debug($sql, 'get_default_address_book_id_form_customers_id-q');
		$value = null;
		if($result = vDB::query($sql)){
			if($row = vDB::fetch_assoc($result)){
				$value = $row['address_book_id'];
			}
		}
		_debug($value, 'get_default_address_book_id_form_customers_id-r');
		return $value;
	}
	
	function onDelete($cutstomers_id) {
		// For furure use
	    $sql[] = "DELETE FROM " . TABLE_ADDRESS_BOOK . " WHERE customers_id='$cutstomers_id'";
	    $sql[] = "DELETE FROM " . TABLE_CUSTOMERS_INFO . " WHERE customers_info_id='$cutstomers_id'";
		// TODO:
	    //foreach ($sql as $sq) {
	    //    vDB::query($sq);		
	    //}
	}

}
?>
