<?php
/**
 * @version $Id: vDB.class.php,v 1.1 2011-07-15 12:33:34 ag Exp $
 * @version $Revison$
 * @copyright VARIO SOFTWARE GmbH (c) 2009
 */

class vDB {
	var $link=null, $error_reporting=true;
	
	function connect($host=null, $user=null, $pass=null, $link = null){
		if(!empty($link)){
			$this->link = $link;
			$this->link = mysql_connect($host, $user, $pass);
		} else {
			$this->link = null;
			mysql_connect($host, $user, $pass);
		}
	}
	
	function select_db($db_name){
		if($this->link){
			mysql_select_db($db_name, $this->link);
		} else {
			mysql_select_db($db_name);
		}
		if (VARIO_CONVERT_TO_UTF8 == 1) {
			mysql_query("SET NAMES utf8_general_ci;");		// Zeichensatz in MySQL-DB ist auf UTF8 eingestellt
		}
	}
	
	function query($query, $link = 'db_link'){
	    $timer_start = microtime();
        $time_start = explode(' ', $timer_start);
		//_debug('Start-query: '.$timer_start);
		//_debug($query,'      query: ');

	    $result = mysql_query($query);
	    
        $timer_stop = microtime();
        $time_end = explode(' ', $timer_stop);
        $timer_total = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
		//_debug(' Stop-query: '.$timer_stop.' Dauer:'.$timer_total.' MySQL-Error: '.$result);

		if($result){
			return $result;
		}
		return false;
	}
	
	function fetch_assoc($result){
		return mysql_fetch_assoc($result);
	}
	
	function fetch_array($result, $result_type = null){
		
		if(!empty($result_type)) {
			return mysql_fetch_array($result, $result_type);
		} else {
			return mysql_fetch_array($result);
		}
		
	}
	
	function fetch_row($result){
		return mysql_fetch_row($result);
	}
	
	function num_rows($result){
		return mysql_num_rows($result);
	}
	
	function error($link = 'db_link'){
		return mysql_error();
	}
	
	function escape_string($to_be_escaped){
		return mysql_escape_string($to_be_escaped);
	}
	
	function insert_id($link = 'db_link'){
		return mysql_insert_id();
	}
	
	function affected_rows($link = 'db_link'){
		return mysql_affected_rows();
	}
	
	function fetchOne($sql_query) {
	    if ($result = vDB::query($sql_query)) {
	        $row = vDB::fetch_array($result);
	        if (is_array($row) && count($row)>0) {
	        	return $row[0];
	        }
	    }
	    return false;
	}

	function fetchAll($query) {
		$result = self::query($query);
		
		$res = array();
		while ($row = self::fetch_assoc($result)) {
			$res[] = $row;
		}
		return $res;
	}
	
	function fetchCol($query)
	{
		$result = self::query($query);
		
		$res = array();
		while ($row = self::fetch_assoc($result)) {
			$res[] = current($row);
		}
		return $res;
	}
	
	function fetchPairs($query)
	{
		$result = self::query($query);
		
		$res = array();
		while ($row = self::fetch_row($result)) {
			$res[$row[0]] = $row[1];
		}
		return $res;
	}
}
?>
