<?php
/*-----------------------------------------------------------------
* 	$Id: function.box_name.php 743 2013-11-18 10:14:09Z akausch $
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

function smarty_function_box_name($params=array(), &$smarty) {
	$name = $params['name'];
	$class = $params['class'];
	unset($params['name']); unset($params['class']);
	$name_query = xtc_db_query("SELECT 
								box_title 
							FROM 
								boxes_names 
							WHERE 
								box_name = '".$name."' 
							AND 
								language_id = '".(int)$_SESSION['languages_id']."' 
							AND 
								status = '1' ");
		
	$title = '';
	if(xtc_db_num_rows($name_query) > 0) {	
		$box_name = xtc_db_fetch_array($name_query);
		$title = $box_name['box_title'];
		return $title;
	} else {
		return $title;
	} 
	// echo $title;
}
