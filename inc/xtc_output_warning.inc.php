<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_output_warning.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_output_warning($warning) {
    new errorBox(array(array('text' => '<li style="display:block;padding:10px;margin-bottom:2px;border:1px solid #ccc; background:url(images/error_bg.gif) center left repeat-x;color:#fff;font-weight:700"> ' . $warning . '</li>')));
}
