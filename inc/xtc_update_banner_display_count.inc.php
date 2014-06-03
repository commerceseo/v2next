<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_update_banner_display_count.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Update the banner display statistics
function xtc_update_banner_display_count($banner_id) {
    $banner_check = xtc_db_fetch_array(xtc_db_query("SELECT COUNT(*) AS count FROM " . TABLE_BANNERS_HISTORY . " WHERE banners_id = '" . (int) $banner_id . "' AND date_format(banners_history_date, '%Y%m%d') = date_format(now(), '%Y%m%d');"));
    if ($banner_check['count'] > 0) {
        xtc_db_query("UPDATE " . TABLE_BANNERS_HISTORY . " SET banners_shown = banners_shown + 1 WHERE banners_id = '" . (int) $banner_id . "' AND date_format(banners_history_date, '%Y%m%d') = date_format(now(), '%Y%m%d');");
    } else {
        xtc_db_query("INSERT INTO " . TABLE_BANNERS_HISTORY . " (banners_id, banners_shown, banners_history_date) VALUES ('" . (int) $banner_id . "', 1, now());");
    }
}
