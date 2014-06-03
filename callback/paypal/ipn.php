<?php
/*-----------------------------------------------------------------
* 	$Id: ipn.php 432 2013-06-24 11:41:59Z akausch $
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


include('../../includes/application_top_callback.php');
include(DIR_WS_CLASSES . 'class.language.php');
$lng = new language_ORIGINAL(xtc_input_validation($_GET['language'], 'char', ''));
if(!isset($_GET['language']))
  $lng->get_browser_language();
include(DIR_WS_LANGUAGES.$lng->language['directory'].'/'.$lng->language['directory'].'.php');
require_once('../../includes/classes/class.paypal_checkout.php');
$o_paypal = new paypal_checkout_ORIGINAL();
if(is_array($_POST))$response = $o_paypal->callback_process($_POST,$lng->language['language_charset']);
?>