<?php

/* -----------------------------------------------------------------
 * 	$Id: cseo_get_content.inc.php 866 2014-03-17 12:07:35Z akausch $
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

function cseo_get_content($flag) {

    if (GROUP_CHECK == 'true')
        $group_check = "AND group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";

    $content_data = xtDBquery("SELECT
								content_id,
								categories_id,
								parent_id,
								content_title,
								content_out_link,
								content_link_target,
								content_link_type,
								content_group
							FROM
								" . TABLE_CONTENT_MANAGER . "
							WHERE
								languages_id='" . (int) $_SESSION['languages_id'] . "'
							AND
								parent_id = '0'
							AND
								file_flag = '" . (int) $flag . "'
								" . $group_check . "
							AND
								content_status = '1'
							ORDER BY
								sort_order");

    $content_string = '<ul>';
    while ($content_data_while = xtc_db_fetch_array($content_data, true)) {
        if ($content_data_while['content_out_link'])
            $content_string .= '<li class="list"><a title="' . $content_data_while['content_title'] . '" rel="' . $content_data_while['content_link_type'] . '" target="' . $content_data_while['content_link_target'] . '" href="' . $content_data_while['content_out_link'] . '">' . $content_data_while['content_title'] . '</a>';
        else
            $content_string .= '<li class="list"><a title="' . $content_data_while['content_title'] . '" href="' . xtc_href_link(FILENAME_CONTENT, 'coID=' . $content_data_while['content_group']) . '">' . $content_data_while['content_title'] . '</a>';

        $content_string .= '</li>';
    }
    $content_string .= '</ul>';
    return $content_string;
}
