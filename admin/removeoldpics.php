<?php
/* -----------------------------------------------------------------
 * 	$Id: removeoldpics.php 420 2013-06-19 18:04:39Z akausch $
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

require('includes/application_top.php');
require(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/removeoldpics.php');

function remove_old_pics($path = '') {
// Images product table
    $pics_array = array();
    $pics_query = xtc_db_query("SELECT products_image FROM " . TABLE_PRODUCTS . "");
    while ($pics = xtc_db_fetch_array($pics_query)) {
        if ($pics['products_image'] != '' || $pics['products_image'] != NULL) {
            $pics_array[] = $pics['products_image'];
        }
    }
// Images product_images table
    $pics_query = xtc_db_query("SELECT image_name FROM " . TABLE_PRODUCTS_IMAGES . "");
    while ($pics = xtc_db_fetch_array($pics_query)) {
        if ($pics['image_name'] != '' || $pics['image_name'] != NULL) {
            $pics_array[] = $pics['image_name'];
        }
    }
    switch ($path) {
        case 'original' :
            $path = DIR_FS_CATALOG_ORIGINAL_IMAGES;
            $path1 = DIR_WS_CATALOG_ORIGINAL_IMAGES;
            break;
        case 'info' :
            $path = DIR_FS_CATALOG_INFO_IMAGES;
            $path1 = DIR_WS_CATALOG_INFO_IMAGES;
            break;
        case 'thumbnail' :
            $path = DIR_FS_CATALOG_THUMBNAIL_IMAGES;
            $path1 = DIR_WS_CATALOG_THUMBNAIL_IMAGES;
            break;
        case 'mini' :
            $path = DIR_FS_CATALOG_MINI_IMAGES;
            $path1 = DIR_WS_CATALOG_MINI_IMAGES;
            break;
        case 'popup' :
            $path = DIR_FS_CATALOG_POPUP_IMAGES;
            $path1 = DIR_WS_CATALOG_POPUP_IMAGES;
            break;
    }

    if ($path != "") {
        $handle = opendir($path);
        while ($datei = readdir($handle)) {
            if (!in_array($datei, $pics_array) && ($datei != '.') && ($datei != '..') && ($datei != 'index.html') && ($datei != 'noimage.gif') && ($datei != 'no_img_big.jpg') && ($datei != 'no_img.jpg')) {
                unlink($path . $datei);
            }
        }
        closedir($handle);
    }
}

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete') {
        if (isset($_GET['path'])) {
            remove_old_pics($_GET['path']);
        }
    }
}
require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td width="100%">
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <a href="./removeoldpics.php?action=delete&path=original"><?php echo LINK_ORIGINAL; ?></a>&nbsp;|&nbsp;
                                    <a href="./removeoldpics.php?action=delete&path=info"><?php echo LINK_INFO; ?></a>&nbsp;|&nbsp;
                                    <a href="./removeoldpics.php?action=delete&path=thumbnail"><?php echo LINK_THUMBNAIL; ?></a>&nbsp;|&nbsp;
                                    <a href="./removeoldpics.php?action=delete&path=mini"><?php echo LINK_MINI; ?></a>&nbsp;|&nbsp;
                                    <a href="./removeoldpics.php?action=delete&path=popup"><?php echo LINK_POPUP; ?></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
