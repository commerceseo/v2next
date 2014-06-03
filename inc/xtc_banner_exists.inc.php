<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_banner_exists.inc.php 866 2014-03-17 12:07:35Z akausch $
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

require_once(DIR_FS_INC . 'xtc_random_select.inc.php');

function xtc_banner_exists($action, $identifier) {
    if ($action == 'dynamic') {
        return xtc_random_select("SELECT banners_id, banners_title, banners_image, banners_html_text FROM " . TABLE_BANNERS . " WHERE status = '1' AND banners_group = '" . $identifier . "';");
    } elseif ($action == 'static') {
        $banner_query = xtc_db_fetch_array(xtc_db_query("SELECT banners_id, banners_title, banners_image, banners_html_text FROM " . TABLE_BANNERS . " WHERE status = '1' AND banners_id = '" . $identifier . "';"));
        return $banner_query;
    } else {
        return false;
    }
}
