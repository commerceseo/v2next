<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_activate_banners.inc.php 866 2014-03-17 12:07:35Z akausch $
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

// Auto activate banners
function xtc_activate_banners() {
    $banners_query = xtc_db_query("SELECT banners_id, date_scheduled FROM " . TABLE_BANNERS . " WHERE date_scheduled != '';");
    if (xtc_db_num_rows($banners_query)) {
        while ($banners = xtc_db_fetch_array($banners_query)) {
            if (date('Y-m-d H:i:s') >= $banners['date_scheduled']) {
                xtc_set_banner_status($banners['banners_id'], '1');
            }
        }
    }
}
