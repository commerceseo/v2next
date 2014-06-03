<?php
/*-----------------------------------------------------------------
* 	$Id: application_top_export.php 420 2013-06-19 18:04:39Z akausch $
* 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
* 	http://www.commerce-seo.de
* ------------------------------------------------------------------
* 	based on:
* 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
* 	(c) 2002-2003 osCommerce - www.oscommerce.com
* 	(c) 2003     nextcommerce - www.nextcommerce.org
* 	(c) 2005     xt:Commerce - www.xt-commerce.com
* 	Released under the GNU General Public License
* ---------------------------------------------------------------*/




  // start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());

  // set the level of error reporting
  error_reporting(E_ALL & ~E_NOTICE);
//  error_reporting(E_ALL);


// Set the local configuration parameters - mainly for developers - if exists else the mainconfigure
if(file_exists('../includes/local/configure.php') && filesize('../includes/local/configure.php') !== false) {
	include('../includes/local/configure.php');
} elseif(file_exists('../includes/configure.php') && filesize('../includes/configure.php') !== false) {
	include('../includes/configure.php');
} else {
	header('Location: ../installer/');
	exit;
}


$php4_3_10 = (0 == version_compare(phpversion(), "4.3.10"));
define('PHP4_3_10', $php4_3_10);
// define the project version
define('PROJECT_VERSION', 'commerce:SEO v2.0.0 Plus');

// set the type of request (secure or not)
$request_type = (getenv('HTTPS') == '1' || getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

if(basename($_SERVER['PHP_SELF'])!='commerce_seo_url.php')
	$PHP_SELF = basename($_SERVER['PHP_SELF']);
else
	$PHP_SELF = $aktuelle_datei;

// include the list of project filenames
require(DIR_WS_INCLUDES . 'filenames.php');

// include the list of project database tables
require(DIR_WS_INCLUDES . 'database_tables.php');


// Store DB-Querys in a Log File
define('STORE_DB_TRANSACTIONS', 'false');

// include used functions
require_once (DIR_FS_INC.'cseo_db.inc.php');

// modification for new graduated system


// make a connection to the database... now
xtc_db_connect() or die('Der Datenbankserver konnte nicht erreicht werden!');

// set the application parameters
$configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);
while ($configuration = xtc_db_fetch_array($configuration_query)) {
  define($configuration['cfgKey'], $configuration['cfgValue']);
}


function xtDBquery($query) {
	if (DB_CACHE == 'true') {
		$result = xtc_db_queryCached($query);
	} else {
		$result = xtc_db_query($query);
	}
	return $result;
}

// if gzip_compression is enabled, start to buffer the output
if ( (GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded = extension_loaded('zlib')) && (PHP_VERSION >= '4') ) {
  if (($ini_zlib_output_compression = (int)ini_get('zlib.output_compression')) < 1) {
    ob_start('ob_gzhandler');
  } else {
    ini_set('zlib.output_compression_level', GZIP_LEVEL);
  }
}

  // Include Template Engine
require(DIR_WS_CLASSES . 'Smarty_3/Smarty.class.php');


?>