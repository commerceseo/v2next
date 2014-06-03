<?php
/*-----------------------------------------------------------------
* 	$Id: infobox.php 371 2013-06-10 12:44:12Z akausch $
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

$box_smarty = new smarty;
$box_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');

if ($_SESSION['customers_status']['customers_status_image']!='') {
	$loginboxcontent = xtc_image('images/icons/'.$_SESSION['customers_status']['customers_status_image']).'<br />';
}

$loginboxcontent .= cseo_get_customer_name().'<br />';
$loginboxcontent .= BOX_LOGINBOX_STATUS.' <b>'.$_SESSION['customers_status']['customers_status_name'].'</b><br />';
if ($_SESSION['customers_status']['customers_status_show_price'] == 0) {
	$loginboxcontent .= NOT_ALLOWED_TO_SEE_PRICES_TEXT;
} else  {
	if ($_SESSION['customers_status']['customers_status_discount'] != '0.00') {
		$loginboxcontent.= BOX_LOGINBOX_DISCOUNT.' '.$_SESSION['customers_status']['customers_status_discount']. '%<br />';
	}
	if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1  && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00') {
		$loginboxcontent .= BOX_LOGINBOX_DISCOUNT_TEXT.' '.$_SESSION['customers_status']['customers_status_ot_discount'].' % ' .BOX_LOGINBOX_DISCOUNT_OT. '<br />';
	}
}

$box_smarty->assign('BOX_CONTENT', $loginboxcontent);
$box_smarty->assign('language', $_SESSION['language']);
$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
$box_smarty->assign('box_name', getBoxName('infobox'));
$box_smarty->assign('box_class_name', getBoxCSSName('infobox'));
// set cache ID
if (!CacheCheck()) {
	$box_smarty->caching = false;
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
} else {
	$box_smarty->caching = true;
	$box_smarty->cache_lifetime=CACHE_LIFETIME;
	$box_smarty->cache_modified_check=CACHE_CHECK;
	$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'].'infobox';
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html',$cache_id);
}
