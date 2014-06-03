<?php
/*-----------------------------------------------------------------
* 	$Id: news_ticker.php 371 2013-06-10 12:44:12Z akausch $
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

$ticker_query = xtc_db_query("SELECT ticker_text FROM news_ticker WHERE language_id = '".(int)$_SESSION['languages_id']."' AND status = '1' ");

if(xtc_db_num_rows($ticker_query)) {
	$box_news_ticker .= '<div id="ticker-area"><ul>';
		while($ticker = xtc_db_fetch_array($ticker_query)) {
			$box_news_ticker .= '<li>'.$ticker['ticker_text'].'</li>';
		}
	$box_news_ticker .= '</ul></div>';
}
$box_smarty->assign('BOX_CONTENT',$box_news_ticker);

$box_smarty->assign('language', $_SESSION['language']);
$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
$box_smarty->assign('box_name', getBoxName('news_ticker'));
$box_smarty->assign('box_class_name', getBoxCSSName('news_ticker'));

if (!CacheCheck()) {
	$box_smarty->caching = false;
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
	
} else {
	$box_smarty->caching = true;	
	$box_smarty->cache_lifetime=CACHE_LIFETIME;
	$box_smarty->cache_modified_check=CACHE_CHECK;
	$cache_id = $_SESSION['language'].'news_ticker';
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html',$cache_id);
}
