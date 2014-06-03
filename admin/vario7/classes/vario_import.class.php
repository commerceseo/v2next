<?php
/**
 * @version $Id: vario_import.class.php,v 1.1 2011-07-15 12:33:34 ag Exp $
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 *
 * set_exp_source: 			setzt das Arbeits-Array $exp_source mit den EXP-Daten
 * setDefaultAction:		setzt die SQL-Aktion (I,U,D) aus der EXP-Datei
 * set_fields:				Mapped die Felder von VARIO zur MySQL-DB und füllt das fields-Array
 * assign_field_values:		baut das Array import mit Feldnamen und den Werten des Datensatzes auf und wendet evtl. angegeben Funktionen darauf an
 * do_action:			
 *
 * 23.09.2010 AB: 
 *   array_key_exists verhinderte, dass Nicht-EXP-Datei-Felder genutzt werden können, deaktiviert 
 *
 */
	include_once('vario7/html2text.inc.php');								//
	
	class vario_import {
		
		var  
			$exp_source			= array(),			// normierter EXP-Datensatz
			$fields 			= array(), 
			$constant_fields	= array(),			// Felder mit Werten, die nicht durch Werte aus der EXP gesetz wurden
			$import				= array(),			// Die aufbereitete Struktur 
			$toignore_fields	= array(), 
			$default_action		= array(),
			$all_actions 		= array();

		function vario_import(){			
		}

		function set_exp_source($args){
			$this->exp_source = $args;
		}
		
		function set_fields($args){
			$this->fields = $args;
		}
		
		function clear_fields(){
			$this->fields 			= array();
			$this->constant_fields 	= array();
			$this->toignore_fields	= array();
		}
		
		function set_constant_fields($args, $action='INSERT'){
			(array)$this->constant_fields[$action] = $args;
		}
		
		function set_toignore_fields($args, $action='INSERT'){
			(array)$this->toignore_fields[$action] = $args;
		}
		
		function setField($table, $fieldname, $fieldvalue, $actions = array('INSERT', 'UPDATE')){
			if(!is_array($actions))
				$actions = array($actions);
			
			foreach ($actions as $action) {	// 08.04.2009 AB: vorsichtshalber lowercase, ACHTUNG: action ist immer uppercase
				$table 		= strtolower($table);
				$fieldname	= strtolower($fieldname);
				$this->constant_fields[$action][$table][$fieldname] = $fieldvalue;
			}
		}
		
		function setIgnoredField($table, $fieldname, $actions = array('INSERT', 'UPDATE')){
			if(!is_array($actions))
				$actions = array($actions);
			
			foreach ($actions as $action) {	// 08.04.2009 AB: vorsichtshalber lowercase, ACHTUNG: action ist immer uppercase
				$table 		= strtolower($table);
				$fieldname	= strtolower($fieldname);
				$this->toignore_fields[$action][$table][] = $fieldname;
			}
		}
		
		function setDefaultAction(){
			$this->default_action = $this->exp_source['AKTION'];
		}
		
		function getDefaultAction(){
			return $this->default_action;
		}
		
		function setAction($current_action){
			$current_action = (empty($current_action))?$this->exp_source['AKTION']:$current_action;
			$this->all_actions[] = $current_action;
		}
		
		function getAction(){
			return $this->all_actions[count($this->all_actions)-1];
		}
		
		function getPreviousAction(){
			return $this->all_actions[count($this->all_actions)-2];
		}
		
		function resetPreviousAction(){
			array_pop($this->all_actions);
			return $this->getAction();
		}
		
		function get_exp_value($field_name){
			return $this->exp_source["$field_name"];
		}
		
		function assign_field_values() {
						
			foreach ($this->fields as $field_name=>$_fields)
				foreach ($_fields as $field_data)
					// 23.09.2010 AB: array_key_exists verhinderte, dass Nicht-EXP-Datei-Felder genutzt werden können, deaktiviert
					if (array_key_exists($field_name, $this->exp_source)) {
						
						if( !empty($field_data['function']) ) { // calls function if defined
							
							if( $field_data['function']{strlen(trim($field_data['function']))-1}!= ')')
									$function_string = $field_data['function']."('%s')";
							else 	$function_string = $field_data['function'];
									$function_string = sprintf($function_string, vDB::escape_string($this->exp_source[$field_name]));
							//_debug($function_string, 'assign_field_values-function_string');
							$eval_str = "return {$function_string};";
							//_debug($eval_str, 'assign_field_values-eval_str');
							$value = eval($eval_str); 
							//_debug($value, 'assign_field_values-value');
						} else {
							 
							$value = $this->exp_source[$field_name];
							
						} 
						$this->import[$field_data['table']][$field_data['field']] = trim($value);		// ???, trim gefährlich ? 
					} else {
						// 23.09.2010 AB: Auch nicht EXP-Felder erlauben
						$value = $this->exp_source[$field_name];
						$this->import[$field_data['table']][$field_data['field']] = trim($value);		// ???, trim gefährlich ? 
					}
		}
		
		function validate($current_table, $current_structure){
			return true;
		}
		
		function after(){
			return true;
		}
		
		function after_each(){
			return true;
		}
		
		
		function do_SQL($table_structure = null) {
			//_debug($this->default_action, 'START do_SQL -- $default_action');
			if ($this->getAction() == '') {
				$this->setDefaultAction();
				$this->setAction($this->getDefaultAction());
			}
			// _debug($this->getAction(), '      do_SQL -- Action');

			// _debug($table_structure, '     do_SQL -- $table_structure');
			//if(empty($table_structure))
			//	$table_structure = $this->get_table_structure();
			//_debug($table_structure, '     do_SQL -- $table_structure');
				
			$dbq = new dbQuery();
			$dbq->set_toignore_fields($this->toignore_fields);
			$dbq->set_constant_fields($this->constant_fields);
		
			foreach ($table_structure as $table => $structrue) {
				//_debug($structrue, "     do_SQL -- $table -> structrue");
				
				if($this->validate($table, $structrue)){
					$dbq->set_table($table);
					$dbq->set_fields($structrue);
					$dbq->set_pri_keys(get_pri_key($table));
					//_debug($dbq->pri_keys,'   -> do_SQL-PXs');
					
					$check_rows = 0;
					$affected_rows = 0;
					
					switch (strtoupper($this->getAction())){
						case "U" :
								$sql = $dbq->create_update_sql(); 
								_debug($sql,'   -> do_SQL-Update');
								$result = vDB::query($sql);
								// $affected_rows = vDB::affected_rows();	// das geht nicht, weil = 0, wenn nix geändert
								// _debug($dbq->pri_keys,'   -> do_SQL-PXs');
								$req = $dbq->create_select_sql();
								_debug($req,'   -> do_SQL-Update Request');
								$qry = vDB::query($req);
								$affected_rows = vDB::num_rows($qry);
								_debug($affected_rows,'   -> do_SQL-Update Return');
								$GLOBALS['affected_rows'][$table]['UPDATE'] += $affected_rows;
								break;
						case "I" :
								$sql = $dbq->create_insert_sql();
								_debug($sql,'   -> do_SQL-Insert');
								$qry = vDB::query($dbq->create_select_sql());
								$check_rows = vDB::num_rows($qry);
								if($check_rows == 0) {	// Datensatz ist tatsächlich nicht da!
									$result = vDB::query($sql);
									$GLOBALS['affected_rows'][$table]['INSERT'] += 1;
								}
								break;
						case "D" :
								$sql = $dbq->create_delete_sql();
								_debug($sql,'   -> do_SQL-Delete');
								$qry = vDB::query($sql);
								//_debug($qry,'  -> do_SQL-Delete-2');
								$affected_rows = 0;
								if (is_array($qry)) { 
									$affected_rows = vDB::num_rows($qry);
								}
								$GLOBALS['affected_rows'][$table]['DELETE'] += $affected_rows; 
								break;
					}
					
					if( ($this->getAction() == 'U' && $affected_rows == 0) ||
						($this->getAction() == 'I' && $check_rows > 0) ) {
						_debug($this->getAction(), "do_SQL: Failed Insert/Update U:$affected_rows I:$check_rows");
						switch ($this->getAction()) {
							// AUTO-SWITCH INSERT/UPDATE
							case "I" :
									$sql = $dbq->create_update_sql(); 
									_debug($sql, '  -> do_SQL-UPDATE-after-failed-INSERT');
									$result = vDB::query($sql);
									$GLOBALS['affected_rows'][$table]['UPDATE']++;
									// $GLOBALS['affected_rows'][$table]['INSERT']--;  // wurde oben auch nicht gezählt
									break;
							case "U" :
									$sql = $dbq->create_insert_sql();
									_debug($sql, '  -> do_SQL-INSERT-after-failed-UPDATE');
									$result = vDB::query($sql); 
									// $GLOBALS['affected_rows'][$table]['UPDATE']--;
									$GLOBALS['affected_rows'][$table]['INSERT']++;
									break;
						}
					}
				}
			}
		}

		// Hier, damit die Funktion allen Klassen zu Verfügung stehet
		function getNettoPrice($bruttoPrice, $tax_class_id = 1) {						// tax-class = 1 ist Standardsatz
        	$xtcPrice = new xtcPrice(DEFAULT_CURRENCY, 3);								// 3 = DEFAULT_CUSTOMERS_STATUS_ID ist der Neukunde (brutto)
	        $tax = $xtcPrice->TAX[$tax_class_id];
        	$netto =  round($bruttoPrice / ( ($tax/100) + 1 ), 4);		// PRICE_PRECISION 4 ist der Standard bei xtc;
	        return $netto;
    	}
		
	    function calculate_price_with_vario_preiseinheit($price) {
    		// xtc hat keine Preiseinheit, deshalb hier berechnen
        	$_pe = $this->get_exp_value('PREISEINHEIT');
	        if (! $_pe) {
    	    	$pe = 1;
        	} else {
        		$pe = $_pe;
	        }
    	    return ($price / $pe);
    	}

    	function date_added(){
			$ANG_AM = $this->get_exp_value('ANG_AM');
			$ANG_AM = vario_date_to_xtc_date($ANG_AM, 'd.m.Y', 'Y-m-d');
			$ANG_UM = $this->get_exp_value('ANG_UM');
			return "$ANG_AM $ANG_UM";
		}
		
		function plaintext($text, $len) {
			// _debug($text, "START plaintext($len)");
			$h2t = new Html2Text($text, $len); 
			$text = $h2t->convert();
			$text = substr($text, 0, $len); 
			return $text; 
		}
	}
?>
