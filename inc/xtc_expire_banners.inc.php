<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_expire_banners.inc.php 866 2014-03-17 12:07:35Z akausch $
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

require_once(DIR_FS_INC . 'xtc_set_banner_status.inc.php');

// Auto expire banners
function xtc_expire_banners() {
    $banners_query = xtc_db_query("SELECT b.banners_id, b.expires_date, b.expires_impressions, sum(bh.banners_shown) AS banners_shown FROM " . TABLE_BANNERS . " b, " . TABLE_BANNERS_HISTORY . " bh WHERE b.status = '1' AND b.banners_id = bh.banners_id GROUP BY b.banners_id;");
    if (xtc_db_num_rows($banners_query)) {
        while ($banners = xtc_db_fetch_array($banners_query)) {
            if (xtc_not_null($banners['expires_date'])) {
                if (date('Y-m-d H:i:s') >= $banners['expires_date']) {
                    xtc_set_banner_status($banners['banners_id'], '0');
                }
            } elseif (xtc_not_null($banners['expires_impressions'])) {
                if ($banners['banners_shown'] >= $banners['expires_impressions']) {
                    xtc_set_banner_status($banners['banners_id'], '0');
                }
            }
        }
    }
}
