<?php
/**
 * VARIO Import, dbQuery Class
 * @version $Id: sql.class.php,v 1.1 2011-07-15 12:33:34 ag Exp $
 * @version $Revision: 1.1 $
 * 
 * 29.10.2010 AB
 * - NULL-Werte ermöglichen
 * 
 */

class dbQuery {
	var $table;
	var $fields;
	var $pri_keys;
	var $toignore_fields;
	var $constant_fields;

	function dbQuery(){
	}

	function set_table($arg) {
		(array)$this->table = $arg;
	}
	
	function set_fields($arg) {
		(array)$this->fields = $arg;
	}
	
	function set_toignore_fields($arg) {
		(array)$this->toignore_fields = $arg;
	}
	
	function set_constant_fields($arg) {
		(array)$this->constant_fields = $arg;
	}
	
	function set_pri_keys($arg) {
		(array)$this->pri_keys = $arg;
	}
	
	function create_select_sql() {
		return $sql = "SELECT * FROM {$this->table} ".$this->create_where_sql();
	}
	
	function create_insert_sql() {
		$fields 			= (is_array($this->fields))?$this->fields:array();
		$constant_fields 	= (is_array($this->constant_fields['INSERT'][$this->table]))?$this->constant_fields['INSERT'][$this->table]:array();
		// gemappte Felder und vorbesetzte/konstante Felder zusammenfügen
		$tmp_fields 		= array_merge($fields, $constant_fields);
		$sep = "";
		foreach ($tmp_fields as $field=>$val){
			if(!in_array($field, (array)$this->toignore_fields['INSERT'][$this->table])){
				// Neu: NULL-Werte
				$field_value = vDB::escape_string($val);
				if (strtoupper($field_value) == 'NULL') {
					$fields_sql .= $sep.$field."=".$field_value;
				} else {
					$fields_sql .= $sep.$field."='".$field_value."'";
				}
				$sep = ", ";
			}
		}
		return $sql = "INSERT INTO {$this->table} SET $fields_sql ";
	}
	
	function create_update_sql() {
		$fields 			= (is_array($this->fields))?$this->fields:array();
		$constant_fields 	= (is_array($this->constant_fields['UPDATE'][$this->table]))?$this->constant_fields['UPDATE'][$this->table]:array();
		// gemappte Felder und vorbesetzte/konstante Felder zusammenfügen
		$tmp_fields 		= array_merge($fields, $constant_fields); // 06.11.2009 AB, BUG array_merge($this->fields, $constant_fields);
		$sep = "";
		foreach ($tmp_fields as $field=>$val){
			if(!in_array($field, (array)$this->toignore_fields['UPDATE'][$this->table])) {
				// Neu: NULL-Werte
				$field_value = vDB::escape_string($val);
				if (strtoupper($field_value) == 'NULL') {
					$fields_sql .= $sep.$field."=".$field_value;
				} else {
					$fields_sql .= $sep.$field."='".$field_value."'";
				}
				$sep = ", ";
			}
		}
		return $sql = "UPDATE {$this->table} SET $fields_sql ".$this->create_where_sql('UPDATE');
	}
	
	function create_delete_sql() {
		$where = $this->create_where_sql('DELETE');
		if(!empty($where)) // Do not allow delete Full table
			return $sql = "DELETE FROM {$this->table} ".$where;
		else return false;
	}
	
	function create_where_sql($action = "UPDATE") {				// 15.05.2010 AB, was für ein BUG, Default-Wert UPDATE fehlte!
		$fields_sql = null;
		$fields 			= (is_array($this->fields))?$this->fields:array();
		$constant_fields 	= (is_array($this->constant_fields[$action][$this->table]))?$this->constant_fields[$action][$this->table]:array();
		$tmp_fields 		= array_merge($fields, $constant_fields);
		//_debug($fields, 'create_where_sql ===>>> fields');
		//_debug($constant_fields, 'create_where_sql ===>>> constant_fields');
		$sep = "";
		//if ($tmp_fields[0] > '') {
			foreach ($this->pri_keys as $keyname){
				$keyname = strtolower($keyname); // 07.04.2009 AB
				$fields_sql .= $sep.$keyname." = '".vDB::escape_string($tmp_fields[$keyname])."'";
				$sep = " AND ";
			}
		//}
		// 22.02.2010 AB
		return (!empty($fields_sql))?"WHERE $fields_sql":"";
	}
	
}
?>
