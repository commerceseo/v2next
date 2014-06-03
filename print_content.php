<?php
/* -----------------------------------------------------------------
 * 	$Id: print_content.php 810 2014-01-26 15:12:12Z akausch $
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

require ('includes/application_top.php');

$content_query = xtDBquery("SELECT content_heading, content_file, content_text FROM " . TABLE_CONTENT_MANAGER . " WHERE content_group='" . (int) $_GET['coID'] . "' and languages_id = '" . $_SESSION['languages_id'] . "'");
$content_data = xtc_db_fetch_array($content_query, true);
?>
<!DOCTYPE html>
<html lang ="<?php echo $_SESSION['language_code']; ?>">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>" />
        <title><?php echo $content_data['content_heading']; ?></title>
        <base href="<?php echo (getenv('HTTPS') == 'on' ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo 'templates/' . CURRENT_TEMPLATE . '/stylesheet2.css'; ?>" />
		<meta name="robots" content="noindex,nofollow,noodp" />
    </head>
    <body onload="window.print()">
        <h2><?php echo $content_data['content_heading']; ?></h2>

        <?php
        if ($content_data['content_file'] != '') {
            if (strpos($content_data['content_file'], '.txt'))
                echo '<pre>';

            include (DIR_FS_CATALOG . 'media/content/' . $content_data['content_file']);

            if (strpos($content_data['content_file'], '.txt'))
                echo '</pre>';
        } else {
            $content = str_replace("\n", "<br />", $content_data['content_text']);
            echo $content;
        }
        ?>
        <br /><br />
        <a href="#" onClick='window.close();'><?php echo TEXT_CLOSE_WINDOW ?></a>

    </body>
</html>