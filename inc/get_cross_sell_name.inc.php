<?php

/* -----------------------------------------------------------------
 * 	$Id: get_cross_sell_name.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_cross_sell_name($cross_sell_group, $language_id = '') {
    if (!$language_id) {
        $language_id = (int) $_SESSION['languages_id'];
    }
    $cross_sell = xtc_db_fetch_array(xtDBquery("SELECT groupname FROM " . TABLE_PRODUCTS_XSELL_GROUPS . " WHERE products_xsell_grp_name_id = '" . (int) $cross_sell_group . "' AND language_id = '" . (int) $language_id . "';"));

    return $cross_sell['groupname'];
}
