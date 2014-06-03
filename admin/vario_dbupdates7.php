<?php  
	// Copyright (c) 2014 VARIO Software AG

  	include_once('vario7/configure.inc.php');
  	include_once('vario7/configure_import.inc.php');
  	include_once('vario7/functions.inc.php');
	include_once('vario7/classes/vDB/mysql/vDB.class.php');
	include_once('vario7/classes/sql.class.php');
	include_once('vario7/functions_import.inc.php');
	include_once('includes/application_top.php');					

  	// Hilfsfunktionen
	function i2c ($key, $value) {

		$datumzeit = date('Y').'-'.date('m').'-'.date('d').' '.date('G').':'.date('i').':'.date('s');

		$sql =   "INSERT INTO `".TABLE_CONFIGURATION."` ( "
				."`configuration_key`, `configuration_value`, " 
				."`configuration_group_id`, `sort_order`, " 
				."`last_modified`, `date_added`, `use_function`, `set_function` "
				.") VALUES ('$key', '$value', 91, NULL, NULL, '$datumzeit', NULL, NULL)";

		xtc_db_query($sql);

  		echo "$sql ausgeführt</br>";
	}
  	
	function u2c ($key, $value) {

		$datumzeit = date('Y').'-'.date('m').'-'.date('d').' '.date('G').':'.date('i').':'.date('s');
		$sql = 
			"UPDATE `".TABLE_CONFIGURATION."` SET `configuration_value` = '$value', `last_modified` = '$datumzeit' WHERE `configuration_key` = '$key'";
		xtc_db_query($sql);
  		echo "$sql ausgeführt</br>";
	}
	
	function gvt ($ckey = 'VARIO_VERSION_TAG') {

  		return vDB::fetchone("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = '$ckey'");
	}

	// DB-Update Start
	echo "<pre>";  	
	echo "<b>START Datenbankupdate für VARIO7 - CSEO-Shopanbindung ...</br>";  	
	echo "----------------------------------------------------------------</b></br>";  	

  	$version = gvt();

  	if (!$version) {

  		i2c('VARIO_VERSION_TAG', '7.4.0.000');
  	} else {

		echo "<b>$version</b> gefunden!</br></br>";
  	}

	if ($version < '7.4.0.001') {

		i2c('VARIO_BELEG_KUNDE', 'BELEG');

		$version = '7.0.0.001'; u2c('VARIO_VERSION_TAG', $version);

  		echo "<b>$version</b> ausgeführt</br>";
	}

	if ($version < '7.4.0.002') {

		i2c('VARIO_NEWSLETTER_FELD', 'B01');

		$version = '7.4.0.002'; u2c('VARIO_VERSION_TAG', $version);

  		echo "<b>$version</b> ausgeführt</br>";
	}

	if ($version < '7.0.0.003') {

		i2c('VARIO_GEBURTSTAGS_FELD', 'D01');

		$version = '7.0.0.003'; u2c('VARIO_VERSION_TAG', $version);

  		echo "<b>$version</b> ausgeführt</br>";
	}

	$languages_ids = get_all_languages_ids();

	if ($version < '7.4.0.004') {

		i2c('VARIO_SPRACH_ID=1', 'de');
		i2c('VARIO_SPRACH_ID=0', 'de');

		$version = '7.4.0.004'; u2c('VARIO_VERSION_TAG', $version);

  		echo "<b>$version</b> ausgeführt!</br>";
	}

  	if ($version < '7.4.0.008') {

		$sql = "UPDATE ".TABLE_CONFIGURATION." SET configuration_value = 'false' WHERE configuration_key = 'DELETE_GUEST_ACCOUNT'";

  		xtc_db_query($sql);

  		echo "$sql ausgeführt!</br>";
  				
  		$version = '7.4.0.008'; u2c('VARIO_VERSION_TAG', $version);

  		echo "<b>$version</b> ausgeführt!</br>";
	}

	if (!gvt('DEFAULT_LANGUAGE'))
	{
		$sql = "UPDATE ".TABLE_CONFIGURATION." SET configuration_value='de' WHERE configuration_key='DEFAULT_LANGUAGE'";
		xtc_db_query($sql);
		if (!gvt('DEFAULT_LANGUAGE'))
		{
		  $sql = "INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('DEFAULT_LANGUAGE', 'de',  6, 0, NULL, '', NULL, NULL)";
		  xtc_db_query($sql);
		}
		echo "$sql ausgeführt!</br>";
		
		$version = 'DEFAULT_LANGUAGE';
		echo "<b>$version:</b> ausgeführt</br>";
	}

  	echo "<b>-------------------------------------------------------------</br>";  	
  	echo "ENDE Datenbankupdate für VARIO7 - CSEO-Shopanbindung !</b>";  	
  	echo "</pre>";  	
?>
