<?php
/*-----------------------------------------------------------------
* 	$Id: currencies.php 371 2013-06-10 12:44:12Z akausch $
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

$uri = $_REQUEST['linkurl'];

if (substr($uri, 0, 8) != 'checkout') {
	require_once(DIR_FS_INC . 'xtc_hide_session_id.inc.php');
	if(isset($xtPrice) && is_object($xtPrice)) {
		$count_cur='';
		reset($xtPrice->currencies);
		$currencies_array = array();
		while (list($key, $value) = each($xtPrice->currencies)) {
			$count_cur++;
			$currencies_array[] = array('id' => $key, 'text' => $value['title']);
		}
		
		$hidden_get_variables = '';
		reset($_GET);
		while (list($key, $value) = each($_GET)) {
			if ( ($key != 'currency') && ($key != xtc_session_name()) && ($key != 'x') && ($key != 'y') )
				$hidden_get_variables .= xtc_draw_hidden_field($key, $value);
		}
	}
	
	// dont show box if there's only 1 currency
	if ($count_cur > 1 ) {
		// reset var
		$box_smarty = new smarty;
		$box_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
		$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
		$box_currencies = '';
		$box_currencies = xtc_draw_form('currencies', xtc_href_link($uri, $parameters, $request_type, false), 'get').xtc_draw_pull_down_menu('currency', $currencies_array, $_SESSION['currency'], 'onchange="this.form.submit();" style="width: 100%"') . xtc_hide_session_id().'</form>';
		
		$box_smarty->assign('box_name', getBoxName('currencies'));
		$box_smarty->assign('box_class_name', getBoxCSSName('currencies'));
		$box_smarty->assign('BOX_CONTENT', $box_currencies);
		$box_smarty->assign('language', $_SESSION['language']);
		$box_smarty->caching = false;
		$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
	}
}
