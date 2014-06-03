<?php
/* -----------------------------------------------------------------
 * 	$Id: content_preview.php 420 2013-06-19 18:04:39Z akausch $
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

if ($_GET['pID'] == 'media') {
    $content_query = xtc_db_query("SELECT
 					content_file,
 					content_name,
 					file_comment
 					FROM " . TABLE_PRODUCTS_CONTENT . "
 					WHERE content_id='" . (int) $_GET['coID'] . "'");
    $content_data = xtc_db_fetch_array($content_query);
} else {
    $content_query = xtc_db_query("SELECT
 					content_title,
 					content_heading,
 					content_text,
 					content_file
 					FROM " . TABLE_CONTENT_MANAGER . "
 					WHERE content_id='" . (int) $_GET['coID'] . "'");
    $content_data = xtc_db_fetch_array($content_query);
}
?>

<html <?php echo HTML_PARAMS; ?>>
    <head>
        <title><?php echo $page_title; ?></title>
        <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    </head>
    <body>
        <div class="pageHeading"><?php echo $content_data['content_heading']; ?></div><br>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
                <td class="main">
                    <?php
                    if ($content_data['content_file'] != '') {
                        if (strpos($content_data['content_file'], '.txt'))
                            echo '<pre>';
                        if ($_GET['pID'] == 'media') {
// display image
                            if (preg_match('/.gif/i', $content_data['content_file']) or preg_match('/.jpg/i', $content_data['content_file']) or preg_match('/.png/i', $content_data['content_file']) or preg_match('/.tif/i', $content_data['content_file']) or preg_match('/.bmp/i', $content_data['content_file'])) {
                                echo xtc_image(DIR_WS_CATALOG . 'media/products/' . $content_data['content_file']);
                            } else {
                                include(DIR_FS_CATALOG . 'media/products/' . $content_data['content_file']);
                            }
                        } else {
                            include(DIR_FS_CATALOG . 'media/content/' . $content_data['content_file']);
                        }
                        if (strpos($content_data['content_file'], '.txt'))
                            echo '</pre>';
                    } else {
                        echo $content_data['content_text'];
                    }
                    ?>
                </td>
            </tr>
        </table>
    </body>
</html>