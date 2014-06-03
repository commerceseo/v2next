<?php
/*-----------------------------------------------------------------
* 	$Id: social_bookmarks.php 371 2013-06-10 12:44:12Z akausch $
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
	
if (!CacheCheck() && !FORCE_CACHE) {
	$cache = false;
	$box_smarty->caching = false;
} else {
	$cache = true;
	$box_smarty->caching = true;
	$box_smarty->cache_lifetime = CACHE_LIFETIME;
	$box_smarty->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'].'social_bookmarks';
}

if(!$box_smarty->isCached(CURRENT_TEMPLATE.'/boxes/box_social_bookmarks.html', $cache_id) || !$cache){

	$box_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/'); 
	
	$box_smarty->assign('language', $_SESSION['language']);
	
	$box_social .= '
		<script type="text/javascript">
			<!-- 
				function displayBookmark(text) {
					myDiv = document.getElementById(\'bookmark\');
					myDiv.innerHTML = text;
				}
				function AddFavorite() {
					if(navigator.appName != "Microsoft Internet Explorer"){
						window.external.addPanel(\''.STORE_NAME.'\',\''. HTTP_SERVER.DIR_WS_CATALOG .'\', \'\')
					} else {
						window.external.addFavorite(location.href, \''.STORE_NAME .'\');
					}							
				}
			//-->
		</script>';
	$box_social .= '<p class="bookmark db ac">Bookmark bei: <span id="bookmark"></span></p>';
	$box_social .= '<a rel="nofollow" title="Zu Browser Favoriten hinzuf&uuml;gen" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Browser Favoriten\')" href="javascript:AddFavorite();">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_favorites.gif','Browser Favoriten','','','class="bookmark"').'</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="Bei Twitter eintragen" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Twitter\')" onclick="window.open(\'http://twitter.com/home?status=\'+encodeURIComponent(location.href)+\'&amp;title=\'+encodeURIComponent(document.title));return false;" href="http://twitter.com/">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_twitter.gif','Twitter','','','class="bookmark"').'
		</a>';	
	$box_social .= '<a rel="nofollow" target="_blank" title="Bei Mr. Wong bookmarken" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Mr. Wong\')" onclick="window.open(\'http://www.mister-wong.de/index.php?action=addurl&amp;bm_url=\'+encodeURIComponent(location.href)+\'&amp;bm_notice=&amp;bm_description=\'+encodeURIComponent(document.title));return false;" href="http://www.mister-wong.de/">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_wong.gif','Mister Wong','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="Bei Oneview bookmarken" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Oneview\')" onclick="window.open(\'http://www.oneview.de/quickadd/neu/addBookmark.jsf?URL=\'+encodeURIComponent(location.href)+\'&amp;title=\'+encodeURIComponent(document.title));return false;" href="http://www.oneview.de/">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_oneview.gif','Oneview','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="Bei Webnews bookmarken" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Webnews\')" onclick="window.open(\'http://www.webnews.de/einstellen?url=\'+encodeURIComponent(document.location)+\'&amp;title=\'+encodeURIComponent(document.title));return false;" href="http://www.webnews.de/">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_webnews.gif','Webnews','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="Bei Yigg bookmarken" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Yigg\')" onclick="window.open(\'http://yigg.de/neu?exturl=\'+encodeURIComponent(location.href));return false" href="http://yigg.de/">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_yigg.gif','Browser Favoriten','','','class="Yigg"').'
		</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="Bei Furl bookmarken" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Furl\')" onclick="window.open(\'http://www.furl.net/storeIt.jsp?u=\'+encodeURIComponent(location.href)+\'&amp;keywords=&amp;t=\'+encodeURIComponent(document.title));return false;" href="http://www.furl.net/">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_furl.gif','Furl','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="Bei Linkarena bookmarken" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Linkarena\')" onclick="window.open(\'http://linkarena.com/bookmarks/addlink/?url=\'+encodeURIComponent(location.href)+\'&amp;title=\'+encodeURIComponent(document.title));return false;" href="http://www.linkarena.com/">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_linkarena.gif','Linkarena','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" title="Digg it" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Digg\')" onclick="window.open(\'http://digg.com/submit?phase=2&amp;url=\'+encodeURIComponent(location.href)+\'&amp;bodytext=&amp;tags=&amp;title=\'+encodeURIComponent(document.title));return false;" href="http://digg.com/">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_digg.gif','Digg','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="del.icio.us" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Del.icio.us\')" onclick="window.open(\'http://del.icio.us/post?v=2&amp;url=\'+encodeURIComponent(location.href)+\'&amp;notes=&amp;tags=&amp;title=\'+encodeURIComponent(document.title));return false;" href="http://del.icio.us/">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_delicious.gif','del.icio.us','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="slashdot" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Slashdot\')" onclick="window.open(\'http://slashdot.org/bookmark.pl?url=\'+encodeURIComponent(location.href)+\'&amp;title=\'+encodeURIComponent(document.title));return false;" href="http://slashdot.org/">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_slashdot.gif','slashdot','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="My Yahoo" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Yahoo\')" onclick="window.open(\'http://myweb2.search.yahoo.com/myresults/bookmarklet?t=\'+encodeURIComponent(document.title)+\'&amp;d=&amp;tag=&amp;u=\'+encodeURIComponent(location.href));return false;" href="http://www.yahoo.com/">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_yahoo.gif','Yahoo','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="bei iGoogle bookmarken" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Google\')" onclick="window.open(\'http://www.google.com/bookmarks/mark?op=add&amp;hl=de&amp;bkmk=\'+encodeURIComponent(location.href)+\'&amp;annotation=&amp;labels=&amp;title=\'+encodeURIComponent(document.title));return false;" href="http://www.google.com/">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_google.gif','Google','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="Bei Technorati bookmarken" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Technorati\')" onclick="window.open(\'http://technorati.com/faves?add=\'+encodeURIComponent(location.href));return false;" href="http://www.technorati.com/">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_technorati.gif','Technorati','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="Bei Favoriten.de bookmarken" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Favoriten.de\')" onclick="window.open(\'http://www.favoriten.de/url-hinzufuegen.html?bm_url=\'+encodeURIComponent(location.href)+\'&amp;bm_title=\'+encodeURIComponent(document.title));return false;" href="http://www.favoriten.de">
		'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_favoritende.gif','Favoriten.de','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" title="Windows live Favoriten" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Windows Live\')" onclick="window.open(\'https://favorites.live.com/quickadd.aspx?marklet=1&amp;mkt=de&amp;url=\'+encodeURIComponent(location.href)+\'&amp;title=\'+encodeURIComponent(document.title)+\'&amp;top=1\');return false;" href="https://favorites.live.com">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_live.gif','Windows Live','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="Bei Facebook merken" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'Facebook\')" onclick="window.open(\'http://www.facebook.com/share.php?u=\'+encodeURIComponent(location.href)+\'&amp;t=\'+encodeURIComponent(document.title));return false;" href="http://www.facebook.com">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_facebook.gif','Facebook','','','class="bookmark"').'
		</a>';
	$box_social .= '<a rel="nofollow" target="_blank" title="StumbledUpon" onmouseout="displayBookmark(\'\')" onmouseover="displayBookmark(\'StumbleUpon\')" onclick="window.open(\'http://www.stumbleupon.com/submit?url=\'+encodeURIComponent(location.href)+\'&amp;title=\'+encodeURIComponent(document.title));return false;" href="http://www.stumbleupon.com">
			'.xtc_image('templates/'.CURRENT_TEMPLATE.'/img/bookmark/book_su.gif','StumbleUpon','','','class="bookmark"').'
		</a>';
	
	$box_smarty->assign('BOX_CONTENT', $box_social);
}
$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
$box_smarty->assign('box_name', getBoxName('social_bookmarks'));
$box_smarty->assign('box_class_name', getBoxCSSName('social_bookmarks'));

if (!$cache) {
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
} else {
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html', $cache_id);
}
