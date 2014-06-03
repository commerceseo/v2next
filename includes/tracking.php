<?php
/*-----------------------------------------------------------------
* 	$Id: tracking.php 420 2013-06-19 18:04:39Z akausch $
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

$ref_url = parse_url($_SERVER['HTTP_REFERER']);
if ($_SESSION['tracked'] != true) { // if this visitor has not been tracked
$_SESSION['tracking']['http_referer']= $ref_url;
	$_SESSION['tracked'] = true; // set tracked so they are only logged once
}

 if (!isset($_SESSION['tracking']['ip'])) 
    $_SESSION['tracking']['ip'] = $_SERVER['REMOTE_ADDR'];

if (!isset ($_SESSION['tracking']['refID'])) {	
	// check if referer exists
	if (isset($_GET['refID'])) {
		      $campaign_check_query_raw = "SELECT *
			                            FROM ".TABLE_CAMPAIGNS." 
			                            WHERE campaigns_refID = '".xtc_db_input($_GET['refID'])."'";
			$campaign_check_query = xtc_db_query($campaign_check_query_raw);
		if (xtc_db_num_rows($campaign_check_query) > 0) {			
			$_SESSION['tracking']['refID'] = xtc_db_input($_GET['refID']);		
			
			// count hit (block IP for 1 hour)
			$insert_sql = array('user_ip'=>$_SESSION['tracking']['ip'],'campaign'=>xtc_db_input($_GET['refID']),'time'=>'now()');
			
			$check_date = mktime(0, date("i")-1, 0, date("m"), date("d"), date("Y"));
			$ip_query = xtc_db_query("SELECT * FROM ".TABLE_CAMPAIGNS_IP." WHERE campaign='".xtc_db_input($_GET['refID'])."' and user_ip='".$_SESSION['tracking']['ip']."' and time > '".$check_date."'");
			if (!xtc_db_num_rows($ip_query)) 
			xtc_db_perform(TABLE_CAMPAIGNS_IP,$insert_sql);	
			} 	
	}
}
if (!isset ($_SESSION['tracking']['date']))
	$_SESSION['tracking']['date'] = (date("Y-m-d H:i:s"));
if (!isset ($_SESSION['tracking']['browser']))
	$_SESSION['tracking']['browser'] = $_SERVER["HTTP_USER_AGENT"];



$i = count($_SESSION['tracking']['pageview_history']);
if ($i > 6) {
	array_shift($_SESSION['tracking']['pageview_history']);
	$_SESSION['tracking']['pageview_history'][6] = $ref_url;
} else {
	$_SESSION['tracking']['pageview_history'][$i] = $ref_url;
}

if ($_SESSION['tracking']['pageview_history'][$i] == $_SESSION['tracking']['http_referer'])
	array_shift($_SESSION['tracking']['pageview_history']);

