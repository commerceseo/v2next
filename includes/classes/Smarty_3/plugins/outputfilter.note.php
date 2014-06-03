<?php
/*-----------------------------------------------------------------
* 	$Id: outputfilter.note.php 397 2013-06-17 19:36:21Z akausch $
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




function smarty_outputfilter_note($tpl_output, &$smarty) {
    /*
    The following copyright announcement is in compliance
    to section 2c of the GNU General Public License, and
    thus can not be removed, or can only be modified
    appropriately.*/

$tpl_output = preg_replace_callback("/(<a[^>]*href=\"|<form[^>]*action=\")(.*)(\"[^<]*>)/Usi","AmpReplace",$tpl_output);
$tpl_output = preg_replace_callback("/(<a[^>]*href='|<form[^>]*action=')(.*)('[^<]*>)/Usi","AmpReplace",$tpl_output);
$tpl_output = preg_replace_callback("/(javascript[^>]*http|<form[^>]*action=\")(.*)(\"[^<]*>)/Usi","AmpReplace",$tpl_output);
$tpl_output = preg_replace_callback("/(<javascript[^>]*http'|<form[^>]*action=')(.*)('[^<]*>)/Usi","AmpReplace",$tpl_output);

return $tpl_output;
}
?>