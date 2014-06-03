<?php
/*-----------------------------------------------------------------
* 	$Id: newsletter.php 371 2013-06-10 12:44:12Z akausch $
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
$box_smarty->assign('language', $_SESSION['language']);
$box_smarty->assign('html_tpl_path', CURRENT_TEMPLATE.'/html');
$box_smarty->assign('box_name', getBoxName('newsletter'));
$box_smarty->assign('box_class_name', getBoxCSSName('newsletter'));

$box_newsletter = xtc_draw_form('sign_in', xtc_href_link(FILENAME_NEWSLETTER, 'action=box', 'NONSSL'));
$box_newsletter .= xtc_draw_input_field('email', '', 'id="newsletteremail" required autocomplete="off" maxlength="50" value="" placeholder="'.BOX_EMAIL_VALUE.'"');
$box_newsletter .= '<br><br>';
$box_newsletter .= xtc_draw_radio_field('check', 'inp', '' ,'title="'.WCAG_REGISTER.'"').' '.TEXT_IN.' ';
$box_newsletter .= xtc_draw_radio_field('check', 'del', '' ,'title="'.WCAG_UNREGISTER.'"').' '.TEXT_OUT;
$box_newsletter .= '<br><br>'.xtc_image_submit('button_send.gif', IMAGE_BUTTON_SEND);
$box_newsletter .= '</form>';

$box_smarty->assign('BOX_CONTENT', $box_newsletter);

// set cache ID
if (!CacheCheck()) {
	$box_smarty->caching = false;
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
} else {
	$box_smarty->caching = true;	
	$box_smarty->cache_lifetime=CACHE_LIFETIME;
	$box_smarty->cache_modified_check=CACHE_CHECK;
	$cache_id = $_SESSION['language'].'newsletter';
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box.html',$cache_id);
}
