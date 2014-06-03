<?php
/* -----------------------------------------------------------------
 * 	$Id: janolaw.inc.php 866 2014-03-17 12:07:35Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	S.Bräutigam www.indiv-style.de
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

function xtc_draw_categories_pull_down($name, $parameters = '', $exclude = '') {
	global $currencies;

	if ($exclude == '') {
		$exclude = array ();
	}
	$select_string = '<select name="'.$name.'"';
	if ($parameters) {
		$select_string .= ' '.$parameters;
	}
	$select_string .= '>';
	$categories_query = xtc_db_query("SELECT categories_id, categories_name FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' ORDER BY categories_id");
	while ($categories = xtc_db_fetch_array($categories_query)) {
		$select_string .= '<option value="'.$categories['categories_id'].'">'.$categories['categories_name'].'</option>';
	}
	$select_string .= '</select>';
	return $select_string;
}

function xtc_draw_manufacturers_pull_down($name, $parameters = '', $exclude = '') {
	global $currencies;
	if ($exclude == '') {
		$exclude = array ();
	}

	$select_string = '<select name="'.$name.'"';
	if ($parameters) {
		$select_string .= ' '.$parameters;
	}
	$select_string .= '>';
	$manufacturers_query = xtc_db_query("select manufacturers_id, manufacturers_name from ".TABLE_MANUFACTURERS." ");
	while ($manufacturers = xtc_db_fetch_array($manufacturers_query)) {
                    $select_string .= '<option value="'.$manufacturers['manufacturers_id'].'" >'.$manufacturers['manufacturers_name'].'</option>';
	}
	$select_string .= '</select>';
	return $select_string;
}

function xtc_set_specials_gratis_status($specials_gratis_id, $status) {
	if ($status == '1') {
		return xtc_db_query("UPDATE ".TABLE_SPECIALS_GRATIS." SET status = '1', date_status_change = now() WHERE specials_gratis_id = '".$specials_gratis_id."';");
	} elseif ($status == '0') {
		return xtc_db_query("UPDATE ".TABLE_SPECIALS_GRATIS." SET status = '0', date_status_change = now() WHERE specials_gratis_id = '".$specials_gratis_id."';");
	} else {
		return -1;
	}
}

function xtc_get_gratis_description($specials_gratis_description, $language_id) {
	$product = xtc_db_fetch_array(xtc_db_query("SELECT specials_gratis_description FROM ".TABLE_SPECIALS_GRATIS_DESCRIPTION." WHERE specials_gratis_id = '".$product_id."';"));
	return $product['specials_gratis_description'];
}
