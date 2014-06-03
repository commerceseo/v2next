<?php
// Copyright (c) 2009 VARIO Software AG

class vario_export {
 	/* Array mit den Daten, die exportiert werden. */
 	var $export = array();

	/* Array mit den Feldzuordnungen. see set_fields(), create_field_info */
	var $fields = array();

	/* Array, das den aktuellen Datensatz beinhaltet. */
	var $ds = array();

	/* Integer beinhaltet die fortlaufende Datensatznummer beginnent bei 1. @see vario_export::do_action() */
	var $ds_counter;

	/* String beinhaltet den Klassennamen. @see vario_export::set_class_name() */
	var $class_name;

	/* String beinhaltet den Pfad für die exportierten Dateien */
	var $files_path;
	
	var $tax_rate = null;
	var $fakturierungsart = null;
	var $ohne_mwst = null;
		
 	function vario_export() {
	}

	function set_class_name($name) {
		$this->class_name = $name;
	}

	function set_fields($args) {
		(array)$this->fields = $args;
	}

	function set_field($tabelle, $args) {
		if (!empty($tabelle) && is_array($args)) {
			$this->fields[$tabelle]['fields'][] = $args;
			return true;
		}
		return false;
	}

	function write_exp() {
		$FeldTrennZeichen = Chr(23);
		$SatzTrennZeichen = Chr(25);

		$this->files_path = DIR_FS_DOCUMENT_ROOT . 'export/vario/files/';
		$file_name = $this->get_file_name();
		$this->src_file = $this->files_path . $file_name . ".exp";
		$this->tmp_file = $this->files_path . $file_name . ".texp";

		if (file_exists($this->src_file)) {
		    $exp_content = file_get_contents($this->src_file);
		    rename($this->src_file, $this->tmp_file);
			$file = fopen($this->tmp_file, "w");
			flock($file, LOCK_EX);
		} else {
		    $exp_content = '';
		 	$file = fopen($this->tmp_file, "w");
		 	flock($file, LOCK_EX);
		}

		$rows = explode("", $exp_content);
		array_pop($rows); // remove empty item
		$keys_str = array_shift($rows);
		$keys_arr1 = explode("", $keys_str);

        array_shift($keys_arr1); //Tab
        array_shift($keys_arr1); //KeyFeldNr
        array_shift($keys_arr1); //Aktion

        $keys_arr2 = array_keys($this->export[1]);

        $keys_arr3 = array_merge($keys_arr2, $keys_arr1);
        $keys_arr3 = array_unique($keys_arr3);

        $data = array();
        $row_key = 1;
		foreach( $rows as $rowstr ){
			$row = explode("", $rowstr);
            array_shift($row); //Tab
            array_shift($row); //KeyFeldNr
            array_shift($row); //Aktion

            $data[$row_key] = array_fill_keys($keys_arr3, '');
			foreach( $row as $cell_key=>$cell_value ) {
			    $data[$row_key][$keys_arr1[$cell_key]] = $cell_value;
			}
			$row_key++;
		}

		foreach( $this->export as $row ){
            $data[$row_key] = array_fill_keys($keys_arr3, '');
			foreach( $row as $cell_key=>$cell_value ) {
			    $data[$row_key][$cell_key] = $cell_value;
			}
			$row_key++;
		}

    	$field_names = array_keys($data[1]);
    	$aktZeile = "Tab". $FeldTrennZeichen ."KeyFeldNr". $FeldTrennZeichen ."Aktion". $FeldTrennZeichen;

    	foreach ($field_names as $field) {
    		$aktZeile .= $field . $FeldTrennZeichen;
    	}
    	$aktZeile .= $SatzTrennZeichen;
    	fwrite($file, $aktZeile);

		foreach ($data as $aktSatz) {
			$aktZeile = $this->class_name . $FeldTrennZeichen ."3". $FeldTrennZeichen ."I". $FeldTrennZeichen;
			foreach ($aktSatz as $field) {
				if (VARIO_CONVERT_TO_UTF8 == 1) {
					$field = utf8_decode($field);			// UTF nach ANSI
				}
				$aktZeile .= $field . $FeldTrennZeichen;
			}
			$aktZeile .= $SatzTrennZeichen;
			fwrite($file, $aktZeile);
		}

		flock($file, LOCK_UN);
		fclose($file);
		rename($this->files_path . $file_name .".texp", $this->files_path . $file_name .".exp");
		chmod($this->files_path . $file_name .".exp", 0777);
	}

	
	function assign_field_values($id) {
		_debug($id, '--> START assign_field_values (id)');
		$this->ds_counter = 1;
		$table = 'export_view';

		foreach ($this->fields[$table]['fields'] as $field_info) {
			if (strlen($field_info['default']) > 0) {
		    	$value = $field_info['default'];
			} else {
				$value = "" ;
			}
			if (array_key_exists($field_info['source'], $this->ds)) {
				$value = $this->ds[$field_info['source']];
			}
			if (!empty($field_info['function'])) {
				if (strpos($field_info['function'], '(') > 0) {
				    $value = eval("return {$field_info['function']};");
				} else {
					$value = eval("return {$field_info['function']}('{$value}');");
				}
			}
			$this->export[$this->ds_counter][$field_info['destination']] = $value;
		}
		_debug($this->export, '--> ENDE assign_field_values');
	}

	function set_file_name($file_name) {
	    $this->file_name = $file_name;
	}

	function get_file_name() {
	    if ($this->file_name) {
	    	return $this->file_name;
	    } else {
	       return $this->class_name;
	    }
	}

    function select_customers_data($customer_id, $orders_id) {
    	_debug($customer_id, '--> START select_customers_data');
    	if (!$orders_id) $orders_id = -1;
    		$query = "
select 
  c.customers_id, c.customers_cid, c.customers_vat_id, c.customers_vat_id_status, c.customers_warning,  
  c.customers_status,  
  c.customers_gender, c.customers_firstname, c.customers_lastname, c.customers_dob,  
  c.customers_email_address,  
  c.customers_default_address_id,  
  c.customers_telephone, c.customers_fax,  
  c.customers_password,  
  c.customers_newsletter,  
  c.customers_newsletter_mode,  
  c.member_flag,  
  c.delete_user,  
  c.account_type,  
  c.password_request_key,  
  c.payment_unallowed,  
  c.shipping_unallowed,  
  c.refferers_id,  
  c.customers_date_added,  
  c.customers_last_modified,
  a.address_book_id,  
  a.entry_gender, a.entry_company, a.entry_firstname, a.entry_lastname,   
  a.entry_street_address, a.entry_suburb, a.entry_postcode, a.entry_city, a.entry_state,  a.entry_country_id, a.entry_zone_id,  
  a.address_date_added,  
  a.address_last_modified,
  case 
    when l.countries_iso_code_2 is not null then l.countries_iso_code_2
	else 'DE'
  end as countries_iso_code_2,
  s.customers_status_name,
  s.customers_status_show_price_tax,
  s.customers_status_add_tax_ot, 
  i.customers_host,
  b.banktransfer_owner, b.banktransfer_number, b.banktransfer_bankname, b.banktransfer_blz,

  " . (
		( $this->getVARIOCfgKey('MODULE_PAYMENT_SEPA_STATUS') == 'True' )
			? "se.sepa_owner, se.sepa_iban, se.sepa_bankname, se.sepa_bic,"
			: ""
	  ) . "

  o.cc_number, o.cc_cvv, o.cc_start, o.cc_expires, o.cc_owner, 
  p1.configuration_value as BELEG_KUNDE,
  p2.configuration_value as BXX,
  p3.configuration_value as DXX,
  case
    when not p4.configuration_key is null then SUBSTRING(p4.configuration_key, 10)
	else 1
  end as PREISGRUPPE,
  vadr.vertreter1
 from customers c 
inner join address_book a on c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id      
 left join countries l on l.countries_id = a.entry_country_id      
inner join customers_status s on s.customers_status_id = c.customers_status and s.language_id = 2
 left join customers_ip i on i.customers_id = c.customers_id        
 left join vario_adr vadr on vadr.web_id = c.customers_id        
 left join orders o on o.customers_id = c.customers_id and o.orders_id = $orders_id
 left join orders_total os on os.orders_id = o.orders_id and os.class = 'ot_tax'
 left join banktransfer b on b.orders_id = o.orders_id

  " . (
		( $this->getVARIOCfgKey('MODULE_PAYMENT_SEPA_STATUS') == 'True' )
			? "left join sepa se on se.orders_id = o.orders_id"
			: ""
	  ) . "
 
 left join configuration p1 on p1.configuration_key    = 'VARIO_BELEG_KUNDE'
 left join configuration p2 on p2.configuration_key    = 'VARIO_NEWSLETTER_FELD'
 left join configuration p3 on p3.configuration_key    = 'VARIO_GEBURTSTAGS_FELD'
 left join configuration p4 on p4.configuration_key LIKE 'VARIO_PG=%' and p4.configuration_value = s.customers_status_name 
where c.customers_id = $customer_id
order by i.customers_ip_id desc limit 0, 1";      
            $result = xtc_db_query($query);
            $data 	= xtc_db_fetch_array($result);
    	_debug($data, '--> ENDE select_customers_data');
        return $data;
    }
    
	
    function getFakturierungsart( $orders_id = null ) {
		// ergänzt 08.07.2009 AB: OhneMwst führt auf jeden Fall zu Fakturierungsart = 0, z.B. CH
		// NEU 03.06.2010: customers_status_add_tax_ot 
		$this->ohne_mwst = $this->getOhneMwst( $orders_id );
		if (($this->ds['customers_status_show_price_tax'] == 0) || ($this->ohne_mwst == 'J')) {
			return 0;
	    } else {
		    return 1;
	    }
	}

	function getOhneMwst( $orders_id = null ) {

		if( empty( $orders_id ) )
		{
			// Alte Methode, denn es steht keine Bestellnummer zur Verfügung (Beim ADR-Datensatz)

			$zones_query = xtDBquery("SELECT tax_class_id as class FROM ".TABLE_TAX_CLASS);

			while ($zones_data = xtc_db_fetch_array($zones_query, true)) {
				$sql =  "select ab.entry_country_id, ab.entry_zone_id " 
						."  from " . TABLE_ADDRESS_BOOK . " ab "
						."  left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) "
						." where ab.customers_id    = '" . $this->ds['customers_id'] . "' "
						."   and ab.address_book_id = '" . $this->ds['address_book_id'] . "'";

				$tax_address_query = xtc_db_query($sql);
				$tax_address = xtc_db_fetch_array($tax_address_query);
				$tax_rate    = xtc_get_tax_rate($zones_data['class'],$tax_address['entry_country_id'], $tax_address['entry_zone_id']);
			}

				if ($tax_rate == 0 || $this->ds['customers_status_add_tax_ot'] == 0)
					$this->ohne_mwst = 'J';
				else  
					$this->ohne_mwst = 'N';
				return $this->ohne_mwst;
		}
		else
		{
			// Es steht eine Bestellnummer zur Verfügung, also in orders_total auf MwSt. prüfen

			$sql =  "select * from " . TABLE_ORDERS_TOTAL . " where orders_id = " . $orders_id . " AND class = 'ot_tax'";
			$orders_total_query = xtc_db_query($sql);
			$orders_total_query_result = mysql_num_rows( $orders_total_query );
			
			if( $orders_total_query_result == 0 )
			{
				$this->ohne_mwst = 'J';
			}
			else
			{
				$this->ohne_mwst = 'N';
			}
			
			return $this->ohne_mwst;
		}
	}

	function getVARIOCfgKey ($ckey = 'VARIO_VERSION_TAG')
	{
	    $query = "select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = '$ckey'";
		$result = xtc_db_query($query);
		$data 	= xtc_db_fetch_array($result);
  		return $data['configuration_value'];
	}
		
 }

?>
