<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_findTitle.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_findTitle($current_pid, $languageFilter) {
    $result = xtDBquery("SELECT * FROM " . TABLE_PRODUCTS_DESCRIPTION . "  WHERE language_id = '" . (int) $_SESSION['languages_id'] . "' AND products_id = '" . (int) $current_pid . "';");
    $matches = xtc_db_num_rows($result);
    if ($matches) {
        while ($line = xtc_db_fetch_array($result)) {
            $productName = $line['products_name'];
        }
        return $productName;
    } else {
        return "Something isn't right....";
    }
}
