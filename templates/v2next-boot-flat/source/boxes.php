<?php
/*-----------------------------------------------------------------
* 	$Id: boxes.php 434 2013-06-25 17:30:40Z akausch $
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

$smarty = new smarty;
$smarty->assign('tpl_path', DIR_WS_CATALOG . 'templates/'.CURRENT_TEMPLATE.'/');
$smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');

$request_type = (getenv('HTTPS') == '1' || getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

define('DIR_WS_BOXES',DIR_FS_CATALOG .'templates/'.CURRENT_TEMPLATE. '/source/boxes/');
define('TEMPLATE_SNIPPETS',DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE. '/html/');
define('PRODUCT_ID', $_GET['products_id']);
define('CAT_ID', $_GET['cPath']);
define('CONTENT_ID', $_GET['coID']);
define('BLOG_CAT', $_GET['blog_cat']);
define('BLOG_ITEM', $_GET['blog_item']);
define('FORCE_CACHE',true);

function countBoxesPostion() {
	$pos_query = xtc_db_query("SELECT id, position_name FROM boxes_positions;");
	$pos_array = array();
	while($pos = xtc_db_fetch_array($pos_query))
		$pos_array[] = array('id' => $pos['id'], 'name' => $pos['position_name']);
	return $pos_array;
}

function FootercountBoxesPostion() {
	$pos_query = xtc_db_query("SELECT id, position_name FROM boxes_positions WHERE position_name = 'footer';");
	$pos_array = array();
	while($pos = xtc_db_fetch_array($pos_query))
		$pos_array[] = array('id' => $pos['id'], 'name' => $pos['position_name']);
	return $pos_array;
}

function getBoxesPerPosition($pos) {
	$count_query = xtc_db_query("SELECT id FROM boxes WHERE position = '".$pos."' AND status = '1' ");
	return xtc_db_num_rows($count_query, true);
}

function getBoxTitle($box_name) {
	$name_query = xtc_db_query("SELECT box_name FROM boxes_names WHERE box_name = '".$box_name."' AND language_id = '".(int)$_SESSION['languages_id']."' AND status = '1';");
	if(xtc_db_num_rows($name_query)) {
		$name = xtc_db_fetch_array($name_query);
		$title = $name['box_name'];
		return $title;
	} else {
		return false;
	}
}
function getBoxCSSName($box_name) {
	$name_query = xtc_db_query("SELECT box_name FROM boxes_names WHERE box_name = '".$box_name."' AND language_id = '".(int)$_SESSION['languages_id']."';");
	if(xtc_db_num_rows($name_query)) {
		$name = xtc_db_fetch_array($name_query);
		$title = $name['box_name'];
		return $title;
	} else {
		return false;
	}
}

function getBoxName($box_name) {
	$name_query = xtc_db_query("SELECT box_name FROM boxes_names WHERE box_name = '".$box_name."' AND language_id = '".(int)$_SESSION['languages_id']."' AND status = '1';");
	if(xtc_db_num_rows($name_query)) {
		$name = xtc_db_fetch_array($name_query);
		$name = $name['box_name'];
		return $name;
	} else {
		return false;
	}
}

function getBoxContent($box_name) {
	$desc_query = xtc_db_query("SELECT box_desc FROM boxes_names WHERE box_name = '".$box_name."' AND language_id = '".(int)$_SESSION['languages_id']."';");
	if(xtc_db_num_rows($desc_query)){
		$desc = xtc_db_fetch_array($desc_query);
		return $desc['box_desc'];
	} else {
		return false;
	}
}

function getBoxFlag($box_name) {
	$data = xtc_db_query("SELECT file_flag FROM boxes WHERE box_name = '".$box_name."'"); 
	if(xtc_db_num_rows($data)){
		$data_flag = xtc_db_fetch_array($data);
		return $data_flag['file_flag'];
	} else {
		return false;
	}
}


if(!((strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) ||
	strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SHIPPING) ||
	strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT) ||
	strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_CONFIRMATION) ||
	strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_PAYMENT) ||
	strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_PAYMENT_ADDRESS) ||
	strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SHIPPING_ADDRESS) ||
	strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_PAYMENT_ADDRESS) ||
	strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SUCCESS)) && BOXLESS_CHECKOUT == 'true')) {

	$boxes = countBoxesPostion();

	foreach($boxes AS $box_names => $name ) {
		$mobile = "";
		if($browser->getBrowser() == Browser::BROWSER_IPHONE) {
			$mobile = " AND mobile = '1'";
		}
		$get_box_query = xtc_db_query("SELECT * FROM boxes WHERE position = '".$name['name']."' AND status = '1' ".$mobile." ORDER BY sort_id ASC;");

		if(xtc_db_num_rows($get_box_query, true)){
			${$name['name']} = '';

			while($get_box = xtc_db_fetch_array($get_box_query)){
				$box_content = '';
				if($get_box['box_type'] == 'file') {
					include_once(DIR_WS_BOXES.strtolower($get_box['box_name']).'.php');
				} elseif($get_box['box_type'] == 'database') {
					$box_name = '';
					$box_name = $get_box['box_name'];
					include(DIR_WS_BOXES.'box.php');
				} else
					echo 'Beim einbinden der Boxen ist ein Fehler aufgetreten!';

				${$name['name']} .= $box_content;
			}
			$smarty->assign('BOXES_'.$name['name'],${$name['name']});
			$smarty->assign('BOXES_'.$name['name'].'_count',getBoxesPerPosition($name['name']));
		}
	}

} else {
	$boxes = FootercountBoxesPostion();

	foreach($boxes AS $box_names => $name ) {
		$get_box_query = xtc_db_query("SELECT box_name, position, sort_id, box_type, status FROM boxes WHERE position = 'footer' AND status = '1' ORDER BY sort_id ASC;");

		if(xtc_db_num_rows($get_box_query, true)){
			${$name['name']} = '';

			while($get_box = xtc_db_fetch_array($get_box_query)){
				$box_content = '';
				if($get_box['box_type'] == 'file') {
					include_once(DIR_WS_BOXES.strtolower($get_box['box_name']).'.php');
				} elseif($get_box['box_type'] == 'database') {
					$box_name = '';
					$box_name = $get_box['box_name'];
					include(DIR_WS_BOXES.'box.php');
				} else
					echo 'Beim einbinden der Boxen ist ein Fehler aufgetreten!';

				${$name['name']} .= $box_content;
			}
			$smarty->assign('BOXES_'.$name['name'],${$name['name']});
			$smarty->assign('BOXES_'.$name['name'].'_count',getBoxesPerPosition($name['name']));
		}
	}
}

//Dieser Copyright darf nicht ohne Genehmigung entfernt werden.
$smarty->assign('copyright','&copy; '.date('Y').' - <a href="'.DIR_WS_CATALOG.'">'. STORE_NAME .'</a><br><a href="http://www.commerce-seo.de/" target="_blank" title="Onlineshop Software by commerce:seo">Onlineshop Software by commerce:seo</a>');

if ($product->isProduct()) {
	require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');
	require_once (DIR_FS_INC.'xtc_get_products_image.inc.php');
}

if (GROUP_CHECK == 'true')
    $group_check = " AND c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";

if (file_exists(DIR_WS_BOXES.'header_cartbox.php')) {
	include(DIR_WS_BOXES.'header_cartbox.php');
}
if (file_exists(DIR_WS_BOXES.'header_wishbox.php')) {
	include(DIR_WS_BOXES.'header_wishbox.php');
}
if (file_exists(DIR_WS_BOXES.'header_loginbox.php')) {
	include(DIR_WS_BOXES.'header_loginbox.php');
}
if (file_exists(DIR_WS_BOXES.'cat_nav.php')) {
	include(DIR_WS_BOXES.'cat_nav.php');
}

