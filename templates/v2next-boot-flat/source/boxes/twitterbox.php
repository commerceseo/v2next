<?php
/*-----------------------------------------------------------------
* 	$Id: twitterbox.php 371 2013-06-10 12:44:12Z akausch $
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

if (TWITTERBOX_STATUS == 'true') {
	$box_smarty = new smarty;
	$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
	
	$box_twitter = '
	<script src="http://widgets.twimg.com/j/2/widget.js"></script>
	<script>
	new TWTR.Widget({
	  version: 2,
	  type: \'profile\',
	  rpp: 4,
	  interval: '.TWITTER_BOX_INTERVAL.',
	  width: \''.TWITTER_BOX_WIDTH.'\',
	  height: '.TWITTER_BOX_HEIGHT.',
	  theme: {
		shell: {
		  background: \''.TWITTER_SHELL_BACKGROUND.'\',
		  color: \''.TWITTER_SHELL_COLOR.'\'
		},
		tweets: {
		  background: \''.TWITTER_TWEETS_BACKGROUND.'\',
		  color: \''.TWITTER_TWEETS_COLOR.'\',
		  links: \''.TWITTER_TWEETS_LINKS.'\'
		}
	  },
	  features: {
		scrollbar: '.TWITTER_SCROLLBAR.',
		loop: '.TWITTER_LOOP.',
		live: '.TWITTER_LIVE.',
		hashtags: '.TWITTER_HASHTAGS.',
		timestamp: '.TWITTER_TIMESTAMP.',
		avatars: '.TWITTER_AVATARS.',
		behavior: \''.TWITTER_BEHAVIOR.'\'
	  }
	}).render().setUser(\''.TWITTER_ACCOUNT.'\').start();
	</script>
	<br />
	<a href="http://twitter.com/'.TWITTER_ACCOUNT.'" class="twitter-follow-button" data-show-count="false" data-lang="de">Follow @'.TWITTER_ACCOUNT.'</a>
	';
	
	$box_smarty->assign('BOX_CONTENT',$box_twitter);
	$box_smarty->assign('language', $_SESSION['language']);
	$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
	$box_smarty->assign('box_name', getBoxName('twitterbox'));
	$box_smarty->assign('box_class_name', getBoxCSSName('twitterbox'));

	
	if (!CacheCheck()) {
		$box_smarty->caching = false;
		$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
		
	} else {
		$box_smarty->caching = true;	
		$box_smarty->cache_lifetime=CACHE_LIFETIME;
		$box_smarty->cache_modified_check=CACHE_CHECK;
		$cache_id = $_SESSION['language'].'twitterbox';
		$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html',$cache_id);
	}
}
