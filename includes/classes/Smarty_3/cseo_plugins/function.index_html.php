<?php
/*-----------------------------------------------------------------
* 	$Id: function.index_html.php 397 2013-06-17 19:36:21Z akausch $
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


function smarty_function_index_html($Params = array(), &$smarty) {
	$show = false;
	if(!empty($Params)) {
		$file = $Params['file'];
		$type = $Params['type'];
		unset($Params);

		if(PRODUCT_ID > 0 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
			$p = xtc_db_fetch_array(xtc_db_query("SELECT products_col_".$type." FROM ".TABLE_PRODUCTS." WHERE products_id = '".intval(PRODUCT_ID)."';"));
			if($p['products_col_'.$type] == '1') {
				$show = true;
			}

		} elseif(CONTENT_ID > 0 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
			$co = xtc_db_fetch_array(xtc_db_query("SELECT content_col_".$type." FROM ".TABLE_CONTENT_MANAGER." WHERE content_group = '".intval(CONTENT_ID)."' AND languages_id = '".intval($_SESSION['languages_id'])."';"));
			if($co['content_col_'.$type] == '1') {
				$show = true;
			}

		} elseif($_GET['coID'] == 5 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
			$co = xtc_db_fetch_array(xtc_db_query("SELECT content_col_".$type." FROM ".TABLE_CONTENT_MANAGER." WHERE content_group = '5' AND languages_id = '".intval($_SESSION['languages_id'])."';"));
			if($co['content_col_'.$type] == '1') {
				$show = true;
			}

		} elseif(CAT_ID > 0 && strstr($_SERVER['REQUEST_URI'], FILENAME_SHOPPING_CART) === false && strstr($_SERVER['REQUEST_URI'], FILENAME_WISH_LIST) === false) {
			$catID = explode('_', CAT_ID);
			array_reverse($catID);
			$ca = xtc_db_fetch_array(xtc_db_query("SELECT categories_col_".$type." FROM ".TABLE_CATEGORIES." WHERE categories_id = '".intval($catID[0])."';"));
			if($ca['categories_col_'.$type] == '1') {
				$show=true;
			}
			
		} else {
			echo $smarty->fetch(TEMPLATE_SNIPPETS.$file);}
			if($show) {
				echo $smarty->fetch(TEMPLATE_SNIPPETS.$file);
			} else {
				return;
			}
		
	}
}
