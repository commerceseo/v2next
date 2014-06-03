<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_update_banner_click_count.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Update the banner click statistics
function xtc_update_banner_click_count($banner_id) {
    xtc_db_query("UPDATE " . TABLE_BANNERS_HISTORY . " SET banners_clicked = banners_clicked + 1 WHERE banners_id = '" . (int) $banner_id . "' AND date_format(banners_history_date, '%Y%m%d') = date_format(now(), '%Y%m%d');");
}
