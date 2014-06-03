<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_get_content_child.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function cseo_get_content_child($content_id) {
    $data = xtc_db_query("SELECT
							content_title,
							content_group,
							content_out_link,
							content_link_target,
							content_link_type
						FROM
							" . TABLE_CONTENT_MANAGER . "
						WHERE
							parent_id = '" . (int) $content_id . "'
						AND
							languages_id = '" . (int) $_SESSION['languages_id'] . "'
						ORDER BY
							sort_order ");

    if (xtc_db_num_rows($data)) {
        while ($data_array = xtc_db_fetch_array($data, true)) {
            if ($data_array['content_out_link']) {
                $content_child[] = array('content_title' => $data_array['content_title'],
                    'content_out_link' => $data_array['content_out_link'],
                    'content_link_target' => $data_array['content_link_target'],
                    'content_link_type' => $data_array['content_link_type']);
            } else {
                $content_child[] = array('content_group' => $data_array['content_group'],
                    'content_title' => $data_array['content_title']);
            }
        }
        return $content_child;
    } else
        return false;
}
