<?php
/*-----------------------------------------------------------------
* 	$Id: outputfilter.note.php 1293 2014-12-10 16:26:10Z akausch $
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

$tpl_output = preg_replace_callback("/(<a[^>]*href=\"|<form[^>]*action=\")(.*)(\"[^<]*>)/Usi","AmpReplace",$tpl_output);
$tpl_output = preg_replace_callback("/(<a[^>]*href='|<form[^>]*action=')(.*)('[^<]*>)/Usi","AmpReplace",$tpl_output);
$tpl_output = preg_replace_callback("/(javascript[^>]*http|<form[^>]*action=\")(.*)(\"[^<]*>)/Usi","AmpReplace",$tpl_output);
$tpl_output = preg_replace_callback("/(<javascript[^>]*http'|<form[^>]*action=')(.*)('[^<]*>)/Usi","AmpReplace",$tpl_output);

return $tpl_output;
}
