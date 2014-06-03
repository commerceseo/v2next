<?php
/* -----------------------------------------------------------------
 * 	$Id: delete_cache.php 420 2013-06-19 18:04:39Z akausch $
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

function delfiles($dir, $desc) {
    if (is_dir($dir)) {
        $error = false;
        $no_file = false;
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '..' && $file != '.' && $file != 'index.html' && $file != '.htaccess') {
                    if (unlink($dir . $file))
                        $i++;
                    else {
                        $error = true;
                        $filename .= $dir . $file . ' <b>konnte nicht gel&ouml;scht werden</b><br />';
                    }
                }
                $files = $i;
            }
            closedir($dh);
        }
        if ($error)
            return $filename;
        else {
            if ($i > 0)
                return '<br />Aus dem Verzeichnis <em>' . $dir . '</em> - ' . $desc . ' wurden <b>' . $i . '</b> Dateien gel&ouml;scht.<br />';
            else
                return '<br />Das Verzeichnis <em>' . $dir . '</em> - ' . $desc . ' ist bereits leer.<br />';
        }
    }
    else
        return 'Verzeichnis <em>' . $dir . '</em> nicht gefunden';
}

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td width="100%" height="100%" valign="top">
            <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="pageHeading">Cachedateien wurden gel&ouml;scht!</td>
                </tr>
            </table>
            <?php
            $del_array = array(array('templates_c/', 'Template Cache im Admin (Emails)'),
                array(DIR_FS_CATALOG . 'cache/', 'Haupt-Cache Ordner im Shoproot'),
                array('cache/', 'Admin-Cache Ordner'),
                array(DIR_FS_CATALOG . 'templates_c/', 'Template Cache im Shoproot'));

            foreach ($del_array AS $key => $value)
                $response .= delfiles($value[0], $value[1]);

            echo '<div class="test">' . $response . '</div>';
            ?>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
