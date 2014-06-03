<?php

/* -----------------------------------------------------------------
 * 	$Id: products_media.php 657 2013-10-07 14:26:08Z akausch $
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

$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
$module_content = array();
$filename = '';

// check if allowed to see
require_once (DIR_FS_INC . 'xtc_in_array.inc.php');
$check_query = xtDBquery("SELECT DISTINCT products_id FROM " . TABLE_PRODUCTS_CONTENT . " WHERE languages_id='" . (int) $_SESSION['languages_id'] . "'");

$check_data = array();
$i = '0';
while ($content_data = xtc_db_fetch_array($check_query, true)) {
    $check_data[$i] = $content_data['products_id'];
    $i++;
}
if (xtc_in_array($product->data['products_id'], $check_data)) {
    // get content data

    require_once (DIR_FS_INC . 'xtc_filesize.inc.php');

    if (GROUP_CHECK == 'true')
        $group_check = "group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%' AND";

    //get download
    $content_query = xtDBquery("SELECT * FROM " . TABLE_PRODUCTS_CONTENT . " WHERE products_id='" . $product->data['products_id'] . "' AND " . $group_check . " languages_id='" . (int) $_SESSION['languages_id'] . "'");

    while ($content_data = xtc_db_fetch_array($content_query, true)) {
        $filename = '';
        if ($content_data['content_link'] != '') {
            $icon = xtc_image(DIR_WS_IMAGES . 'icons/icon_link.gif');
        } else {
            $icon = xtc_image(DIR_WS_IMAGES . 'icons/icon_' . str_replace('.', '', strstr($content_data['content_file'], '.')) . '.gif');
        }

        if ($content_data['content_link'] != '')
            $filename = '<a href="' . $content_data['content_link'] . '" target="new">';
        $filename .= $content_data['content_name'];
        if ($content_data['content_link'] != '')
            $filename .= '</a>';
        $button = '';
        if ($content_data['content_link'] == '') {
            if (preg_match('/.html/i', $content_data['content_file']) or preg_match('/.htm/i', $content_data['content_file']) or preg_match('/.txt/i', $content_data['content_file']) or preg_match('/.bmp/i', $content_data['content_file']) or preg_match('/.jpg/i', $content_data['content_file']) or preg_match('/.gif/i', $content_data['content_file']) or preg_match('/.png/i', $content_data['content_file']) or preg_match('/.tif/i', $content_data['content_file'])) {
                $button = '<a class="group1" href="' . xtc_href_link(FILENAME_MEDIA_CONTENT, 'coID=' . $content_data['content_id']) . '">' . xtc_image_button('button_view.gif', TEXT_VIEW) . '</a>';
            } else {
                $button = '<a href="' . xtc_href_link('media/products/' . $content_data['content_file']) . '">' . xtc_image_button('button_download.gif', TEXT_DOWNLOAD) . '</a>';
            }
        }
        $module_content[] = array('ICON' => $icon, 'FILENAME' => $filename, 'DESCRIPTION' => $content_data['file_comment'], 'FILESIZE' => xtc_filesize($content_data['content_file']), 'BUTTON' => $button, 'HITS' => $content_data['content_read']);
    }

    $module_smarty->assign('language', $_SESSION['language']);
    $module_smarty->assign('DEVMODE', USE_TEMPLATE_DEVMODE);
    $module_smarty->assign('module_content', $module_content);

    $module_smarty->caching = false;
    $module = $module_smarty->fetch(cseo_get_usermod(CURRENT_TEMPLATE . '/module/products_media.html', USE_TEMPLATE_DEVMODE));

    $info_smarty->assign('MODULE_products_media', $module);
}
