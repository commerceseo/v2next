<?php
/*-----------------------------------------------------------------
* 	$Id: function.html.php 397 2013-06-17 19:36:21Z akausch $
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

function smarty_function_html($Params=array(), &$smarty) {
	if(!empty($Params)) {
		$File = $Params['file'];
		unset($Params['file']);
		$Class = $Params['class'];
		unset($Params['class']);
		$smarty->_smarty_include(array('smarty_include_tpl_file' => TEMPLATE_SNIPPETS .$File, 'smarty_include_vars' => $Params));
	}
}
