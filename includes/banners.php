<?php
/*-----------------------------------------------------------------
* 	$Id: banners.php 420 2013-06-19 18:04:39Z akausch $
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

require_once(DIR_FS_INC . 'xtc_banner_exists.inc.php');
require_once(DIR_FS_INC . 'xtc_display_banner.inc.php');
require_once(DIR_FS_INC . 'xtc_update_banner_display_count.inc.php');

if ($banner = xtc_banner_exists('dynamic', 'banner')) {
	$smarty->assign('BANNER',xtc_display_banner('static', $banner));
}
