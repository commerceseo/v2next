<?php
/*-----------------------------------------------------------------
* 	$Id: cat_sort.php 420 2013-06-19 18:04:39Z akausch $
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
include_once('../configure.php');

$connect = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD) or die('Die Verbindung zur Datenbank konnte nicht hergestellt werden!');
mysql_select_db(DB_DATABASE);

$action 		= mysql_real_escape_string($_POST['action']); 
$updateCatSort 	= $_POST['cat_id'];

if ($action == "updateCatSort"){
	$listingCounter = 1;
	foreach ($updateCatSort as $recordIDValue) {
		$query = "UPDATE categories SET sort_order = " . $listingCounter . " WHERE categories_id = " . $recordIDValue;
		mysql_query($query) or die('Die Daten konnten nicht gespeichert werden!');
		$listingCounter = $listingCounter + 1;	
	}
}

?>