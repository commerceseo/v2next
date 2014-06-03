<?php

/* -----------------------------------------------------------------
 * 	$Id: newsletter.php 420 2013-06-19 18:04:39Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */
/*
 * xt:Commerce v 3.x PostFinance  Zahlungs-Modul
 *  
 * @author             customweb GmbH
 * @url                http://www.customweb.com
 *  
 * @copyright          Copyright (c) 2013 customweb GmbH
 * @release            2013-05-31
 */

require ('includes/application_top.php');

$smarty = new Smarty;

// include boxes  	      		    			  
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

require (DIR_WS_INCLUDES . 'header.php');

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
$smarty->caching = false;

$smarty->assign('main_content', '$$$PAYMENT ZONE$$$');

$smarty->display(cseo_get_usermod(CURRENT_TEMPLATE . '/index.html', USE_TEMPLATE_DEVMODE));

include ('includes/application_bottom.php');
