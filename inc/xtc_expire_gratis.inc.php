<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_expire_gratis.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_set_gratis_status($gratis_id, $status) {
    return xtc_db_query("UPDATE " . TABLE_SPECIALS_GRATIS . " SET status = '" . (int) $status . "', date_status_change = now() WHERE specials_gratis_id = '" . (int) $gratis_id . "';");
}

// Auto expire products on special
function xtc_expire_gratis() {
    $gratis_query = xtc_db_query("SELECT specials_gratis_id FROM " . TABLE_SPECIALS_GRATIS . " WHERE status = '1' AND now() >= expires_date AND expires_date > 0;");
    if (xtc_db_num_rows($gratis_query)) {
        while ($gratis = xtc_db_fetch_array($gratis_query)) {
            xtc_set_gratis_status($gratis['specials_gratis_id'], '0');
        }
    }
}
