<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_vpe_name.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_vpe_name($vpeID) {
    $vpe = xtc_db_fetch_array(xtDBquery("SELECT products_vpe_name FROM " . TABLE_PRODUCTS_VPE . " WHERE language_id='" . (int) $_SESSION['languages_id'] . "' AND products_vpe_id='" . (int) $vpeID . "'"));
    return $vpe['products_vpe_name'];
}
