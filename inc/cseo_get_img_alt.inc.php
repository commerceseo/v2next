<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_get_img_alt.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function cseo_get_img_alt($pID, $lID, $imgNr = '') {
    if ($pID != '' && $lID != '') {
        $alt = xtc_db_fetch_array(xtDBquery("SELECT alt_langID_" . $lID . " FROM " . TABLE_PRODUCTS_IMAGES . " WHERE products_id = '" . (int) $pID . "' AND image_nr = '" . (int) $imgNr . "';"));
        return $alt['alt_langID_' . $lID];
    } else
        return false;
}
