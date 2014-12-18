<?php
/*-----------------------------------------------------------------
* 	$Id: function.outerContainer.php 468 2013-07-09 08:29:52Z akausch $
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

function smarty_function_outerContainer($Params=array(), &$smarty) {
	$left = '';
	$right = '';
	$top = '';
	$bottom = '';
	
	$links_query = xtDBquery("SELECT id FROM boxes WHERE position = 'nav' AND status = '1';");
	$rechts_query = xtDBquery("SELECT id FROM boxes WHERE position = 'boxen' AND status = '1';");
	$unten_query = xtDBquery("SELECT id FROM boxes WHERE position = 'footer' AND status = '1';");
	
	if(PRODUCT_ID > 0 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
		$p = xtc_db_fetch_array(xtc_db_query("SELECT products_col_top, products_col_left, products_col_right, products_col_bottom FROM ".TABLE_PRODUCTS." WHERE products_id = '".intval(PRODUCT_ID)."';"));
		if($p['products_col_left'] == '1' && xtc_db_num_rows($links_query, true) > 0)
			$left = '_left';
		if($p['products_col_right'] == '1' && xtc_db_num_rows($rechts_query, true) > 0)
			$right = '_right';
		if($p['products_col_top'] == '1')
			$top = '_top';
		if($p['products_col_bottom'] == '1' && xtc_db_num_rows($unten_query, true) > 0)
			$bottom = '_bottom';
		return '<div id="outerContainer'.$left.$right.$top.$bottom.'">';

	} elseif(CONTENT_ID > 0 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
		$c = xtc_db_fetch_array(xtc_db_query("SELECT content_col_top, content_col_left, content_col_right, content_col_bottom FROM ".TABLE_CONTENT_MANAGER." WHERE content_group = '".intval(CONTENT_ID)."';"));
		if($c['content_col_left'] == '1' && xtc_db_num_rows($links_query, true) > 0)
			$left = '_left';
		if($c['content_col_right'] == '1' && xtc_db_num_rows($rechts_query, true) > 0)
			$right = '_right';
		if($c['content_col_top'] == '1')
			$top = '_top';
		if($c['content_col_bottom'] == '1' && xtc_db_num_rows($unten_query, true) > 0)
			$bottom = '_bottom';
		return '<div id="outerContainer'.$left.$right.$top.$bottom.'">';

	} elseif($_GET['coID'] == 5 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
		$c5 = xtc_db_fetch_array(xtc_db_query("SELECT content_col_top, content_col_left, content_col_right, content_col_bottom FROM ".TABLE_CONTENT_MANAGER." WHERE content_group = '5' AND languages_id = '".intval($_SESSION['languages_id'])."';"));
		if($c5['content_col_left'] == '1' && xtc_db_num_rows($links_query, true) > 0)
			$left = '_left';
		if($c5['content_col_right'] == '1' && xtc_db_num_rows($rechts_query, true) > 0)
			$right = '_right';
		if($c5['content_col_top'] == '1')
			$top = '_top';
		if($c5['content_col_bottom'] == '1' && xtc_db_num_rows($unten_query, true) > 0)
			$bottom = '_bottom';
		return '<div id="outerContainer'.$left.$right.$top.$bottom.'">';

	} elseif(CAT_ID > 0 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
		$catID = explode('_', CAT_ID);
		array_reverse($catID);
		$ca = xtc_db_fetch_array(xtc_db_query("SELECT categories_col_top, categories_col_left, categories_col_right, categories_col_bottom FROM ".TABLE_CATEGORIES." WHERE categories_id = '".intval($catID[0])."';"));
		if($ca['categories_col_left'] == '1' && xtc_db_num_rows($links_query, true) > 0)
			$left = '_left';
		if($ca['categories_col_right'] == '1' && xtc_db_num_rows($rechts_query, true) > 0)
			$right = '_right';
		if($ca['categories_col_top'] == '1')
			$top = '_top';
		if($ca['categories_col_bottom'] == '1' && xtc_db_num_rows($unten_query, true) > 0)
			$bottom = '_bottom';
		return '<div id="outerContainer'.$left.$right.$top.$bottom.'">';

	} elseif ((strstr($_SERVER['REQUEST_URI'], FILENAME_LOGIN) || 
			strstr($_SERVER['REQUEST_URI'], FILENAME_ACCOUNT) || 
			strstr($_SERVER['REQUEST_URI'], FILENAME_CREATE_ACCOUNT) || 
			strstr($_SERVER['REQUEST_URI'], FILENAME_CREATE_ACCOUNT_SUCCESS) || 
			strstr($_SERVER['REQUEST_URI'], FILENAME_CREATE_GUEST_ACCOUNT))) {
			if(xtc_db_num_rows($links_query, true) > 0)
				$left = '_left';
			if(xtc_db_num_rows($rechts_query, true) > 0)
				$right = '_right';
			if(xtc_db_num_rows($unten_query, true) > 0)
				$bottom = '_bottom';
			return '<div id="outerContainer'.$left.$right.'_top'.$bottom.'">';	
	

	} elseif (!((strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SHIPPING) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_CONFIRMATION) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_PAYMENT) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_PAYMENT_ADDRESS) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SHIPPING_ADDRESS) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_PAYMENT_ADDRESS) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SUCCESS)) && BOXLESS_CHECKOUT == 'true')) {
			if(xtc_db_num_rows($links_query, true) > 0)
				$left = '_left';
			if(xtc_db_num_rows($rechts_query, true) > 0)
				$right = '_right';
			if(xtc_db_num_rows($unten_query, true) > 0)
				$bottom = '_bottom';
			return '<div id="outerContainer'.$left.$right.'_top'.$bottom.'">';

	
	} elseif (!((strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SHIPPING) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_CONFIRMATION) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_PAYMENT) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_PAYMENT_ADDRESS) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SHIPPING_ADDRESS) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_PAYMENT_ADDRESS) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SUCCESS)) 
			&& BOXLESS_CHECKOUT == 'false')) {
		if(xtc_db_num_rows($unten_query, true) > 0)
			$bottom = '_bottom';
		return '<div id="outerContainer_top'.$bottom.'">';		
	}
	else
		return '<div id="outerContainer_top_bottom" class="standard">';
}
