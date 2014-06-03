<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_get_categories.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function xtc_get_categories($categories_array = '', $parent_id = '0', $indent = '') {
    $parent_id = xtc_db_prepare_input($parent_id);

    if (!is_array($categories_array))
        $categories_array = array();

    $categories_query = xtDBquery("SELECT
							c.categories_id,
							cd.categories_name
						FROM 
							" . TABLE_CATEGORIES . " c
						INNER JOIN
							" . TABLE_CATEGORIES_DESCRIPTION . " cd ON(c.categories_id = cd.categories_id AND cd.language_id = '" . (int) $_SESSION['languages_id'] . "')
						WHERE 
							parent_id = '" . xtc_db_input($parent_id) . "'
						AND 
							c.categories_status != 0
						ORDER BY sort_order, cd.categories_name;");

    while ($categories = xtc_db_fetch_array($categories_query, true)) {
        $categories_array[] = array('id' => $categories['categories_id'], 'text' => $indent . $categories['categories_name']);
        if ($categories['categories_id'] != $parent_id) {
            $categories_array = xtc_get_categories($categories_array, $categories['categories_id'], $indent . '&nbsp;&nbsp;');
        }
    }

    return $categories_array;
}
