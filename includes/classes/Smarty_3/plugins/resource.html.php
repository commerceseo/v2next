<?php
/*-----------------------------------------------------------------
* 	$Id: resource.html.php 397 2013-06-17 19:36:21Z akausch $
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

function smarty_resource_html_source ($tpl_name, &$tpl_source, &$smarty_obj){
	$tpl_data = xtc_db_fetch_array(xtc_db_query(" SELECT email_content_html FROM emails WHERE email_name = '".$tpl_name."' AND languages_id = '".$_SESSION['languages_id']."' "));
  	if (sizeof($tpl_data) == 1) {
  		$tpl_source = $tpl_data['email_content_html'];
  		return true;
	} else {
		return false;
	}
}
  
function smarty_resource_html_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)	{
	$tpl_data = xtc_db_fetch_array(xtc_db_query(" SELECT email_timestamp FROM emails WHERE email_name = '".$tpl_name."' AND languages_id = '".$_SESSION['languages_id']."' "));
  	if (sizeof($tpl_data['email_timestamp']) == 1) {
  		$tpl_timestamp = $tpl_data['email_timestamp'];
  		return true;
  	} else {
  		return false;
  	}
}
  
function smarty_resource_html_secure($tpl_name, &$smarty_obj) {
	return true;
}
  
function smarty_resource_html_trusted($tpl_name, &$smarty_obj) {

}
