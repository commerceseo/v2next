<?php
/*-----------------------------------------------------------------
* 	$Id: function.bootstrapContainer.php 943 2014-04-08 13:26:37Z akausch $
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

function smarty_function_bootstrapContainer($Params=array(), &$smarty) {
	$left = false;
	$right = false;
	
	$links_query = xtDBquery("SELECT id FROM boxes WHERE position = 'nav' AND status = '1';");
	$rechts_query = xtDBquery("SELECT id FROM boxes WHERE position = 'boxen' AND status = '1';");

	if(PRODUCT_ID > 0 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
		$p = xtc_db_fetch_array(xtc_db_query("SELECT products_col_top, products_col_left, products_col_right, products_col_bottom FROM ".TABLE_PRODUCTS." WHERE products_id = '".intval(PRODUCT_ID)."';"));
		if($p['products_col_left'] == '1' && xtc_db_num_rows($links_query, true) > 0) {
			$left = true;
		}
		if($p['products_col_right'] == '1' && xtc_db_num_rows($rechts_query, true) > 0) {
			$right = true;
		}
		if ($left && $right) {
			$css = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
		} elseif (($left && !$right) || (!$left && $right)) {
			$css = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
		} else {
			$css = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
		}
		return '<div class="'.$css.'">';

	} elseif(CONTENT_ID > 0 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
		$c = xtc_db_fetch_array(xtc_db_query("SELECT content_col_top, content_col_left, content_col_right, content_col_bottom FROM ".TABLE_CONTENT_MANAGER." WHERE content_group = '".intval(CONTENT_ID)."';"));
		if($c['content_col_left'] == '1' && xtc_db_num_rows($links_query, true) > 0) {
			$left = true;
		}
		if($c['content_col_right'] == '1' && xtc_db_num_rows($rechts_query, true) > 0) {
			$right = true;
		}

		if ($left && $right) {
			$css = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
		} elseif (($left && !$right) || (!$left && $right)) {
			$css = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
		} else {
			$css = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
		}
		return '<div class="'.$css.'">';

	} elseif($_GET['coID'] == 5 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
		$c5 = xtc_db_fetch_array(xtc_db_query("SELECT content_col_top, content_col_left, content_col_right, content_col_bottom FROM ".TABLE_CONTENT_MANAGER." WHERE content_group = '5' AND languages_id = '".intval($_SESSION['languages_id'])."';"));
		if($c5['content_col_left'] == '1' && xtc_db_num_rows($links_query, true) > 0) {
			$left = true;
		}
		if($c5['content_col_right'] == '1' && xtc_db_num_rows($rechts_query, true) > 0) {
			$right = true;
		}

		if ($left && $right) {
			$css = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
		} elseif (($left && !$right) || (!$left && $right)) {
			$css = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
		} else {
			$css = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
		}
		return '<div class="'.$css.'">';

	} elseif(CAT_ID > 0 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
		$catID = explode('_', CAT_ID);
		array_reverse($catID);
		$ca = xtc_db_fetch_array(xtc_db_query("SELECT categories_col_top, categories_col_left, categories_col_right, categories_col_bottom FROM ".TABLE_CATEGORIES." WHERE categories_id = '".intval($catID[0])."';"));
		if($ca['categories_col_left'] == '1' && xtc_db_num_rows($links_query, true) > 0) {
			$left = true;
		}
		if($ca['categories_col_right'] == '1' && xtc_db_num_rows($rechts_query, true) > 0) {
			$right = true;
		}
		if ($left && $right) {
			$css = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
		} elseif (($left && !$right) || (!$left && $right)) {
			$css = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
		} else {
			$css = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
		}
		return '<div class="'.$css.'">';

	} elseif ((strstr($_SERVER['REQUEST_URI'], FILENAME_LOGIN) || 
			strstr($_SERVER['REQUEST_URI'], FILENAME_ACCOUNT) || 
			strstr($_SERVER['REQUEST_URI'], FILENAME_CREATE_ACCOUNT) || 
			strstr($_SERVER['REQUEST_URI'], FILENAME_CREATE_ACCOUNT_SUCCESS) || 
			strstr($_SERVER['REQUEST_URI'], FILENAME_CREATE_GUEST_ACCOUNT))) {
			if(xtc_db_num_rows($links_query, true) > 0) {
				$left = true;
			}
			if(xtc_db_num_rows($rechts_query, true) > 0) {
				$right = true;
			}
			
			if ($left && $right) {
				$css = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
			} elseif (($left && !$right) || (!$left && $right)) {
				$css = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
			} else {
				$css = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
			}
			return '<div class="'.$css.'">';
	

	} elseif (!((strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SHIPPING) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_CONFIRMATION) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_PAYMENT) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_PAYMENT_ADDRESS) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SHIPPING_ADDRESS) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_PAYMENT_ADDRESS) ||
			strstr($_SERVER['REQUEST_URI'], FILENAME_CHECKOUT_SUCCESS)) && BOXLESS_CHECKOUT == 'true')) {
			if(xtc_db_num_rows($links_query, true) > 0) {
				$left = true;
			}
			if(xtc_db_num_rows($rechts_query, true) > 0) {
				$right = true;
			}

			if ($left && $right) {
				$css = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
			} elseif (($left && !$right) || (!$left && $right)) {
				$css = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
			} else {
				$css = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
			}
			return '<div class="'.$css.'">';

	
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
			$css = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
			return '<div class="'.$css.'">';		
	} else {
		$css = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
		return '<div class="'.$css.'">';
	}
}
