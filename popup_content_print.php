<?php
/* -----------------------------------------------------------------
 * 	$Id: popup_content_print.php 420 2013-06-19 18:04:39Z akausch $
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

include('includes/application_top.php');

$content_query = xtDBquery("SELECT content_heading,content_file,content_text FROM " . TABLE_CONTENT_MANAGER . " WHERE content_group='" . (int) $_GET['coID'] . "' and languages_id = '" . $_SESSION['languages_id'] . "'");
$content_data = xtc_db_fetch_array($content_query, true);
?>
<!DOCTYPE html>
<html lang ="<?php echo $_SESSION['language_code']; ?>">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>" />
        <title><?php echo $content_data['content_heading']; ?></title>
        <base href="<?php echo (getenv('HTTPS') == 'on' ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo 'templates/' . CURRENT_TEMPLATE . '/stylesheet2.css'; ?>" />
    </head>
    <body onload="window.print()">
        <?php
        echo '<h2>' . $content_data['content_heading'] . '</h2>';

        if ($content_data['content_file'] != '') {
            if (strpos($content_data['content_file'], '.txt'))
                echo '<pre>';
            include (DIR_FS_CATALOG . 'media/content/' . $content_data['content_file']);
            if (strpos($content_data['content_file'], '.txt'))
                echo '</pre>';
        }
        else
            echo '<div class="content_body">' . $content_data['content_text'] . '</div>';
        ?>
        <br /><br />
    </body>
</html>
