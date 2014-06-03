<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_expire_specials.inc.php 866 2014-03-17 12:07:35Z akausch $
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

require_once(DIR_FS_INC . 'xtc_set_specials_status.inc.php');

// Auto expire products on special
function xtc_expire_specials() {
    $specials_query = xtc_db_query("SELECT specials_id FROM " . TABLE_SPECIALS . " WHERE status = '1' AND now() >= expires_date AND expires_date > 0;");
    if (xtc_db_num_rows($specials_query)) {
        while ($specials = xtc_db_fetch_array($specials_query)) {
            xtc_set_specials_status($specials['specials_id'], '0');
        }
    }
}
