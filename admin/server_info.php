<?php
/* -----------------------------------------------------------------
 * 	$Id: server_info.php 420 2013-06-19 18:04:39Z akausch $
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

$system = xtc_get_system_information();
require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                            </tr>
                        </table></td>
                </tr>
                <tr>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <tr>
                                <td align="center">
                                    <table border="0" cellspacing="0" cellpadding="3">
                                        <tr>
                                            <td class="smallText"><b><?php echo TITLE_SERVER_HOST; ?></b></td>
                                            <td class="smallText"><?php echo $system['host'] . ' (' . $system['ip'] . ')'; ?></td>
                                            <td class="smallText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo TITLE_DATABASE_HOST; ?></b></td>
                                            <td class="smallText"><?php echo $system['db_server'] . ' (' . $system['db_ip'] . ')'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="smallText"><b><?php echo TITLE_SERVER_OS; ?></b></td>
                                            <td class="smallText"><?php echo $system['system'] . ' ' . $system['kernel']; ?></td>
                                            <td class="smallText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo TITLE_DATABASE; ?></b></td>
                                            <td class="smallText"><?php echo $system['db_version']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="smallText"><b><?php echo TITLE_SERVER_DATE; ?></b></td>
                                            <td class="smallText"><?php echo $system['date']; ?></td>
                                            <td class="smallText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo TITLE_DATABASE_DATE; ?></b></td>
                                            <td class="smallText"><?php echo $system['db_date']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="smallText"><b><?php echo TITLE_SERVER_UP_TIME; ?></b></td>
                                            <td colspan="3" class="smallText"><?php echo $system['uptime']; ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="smallText"><b><?php echo TITLE_HTTP_SERVER; ?></b></td>
                                            <td colspan="3" class="smallText"><?php echo $system['http_server']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="smallText"><b><?php echo TITLE_PHP_VERSION; ?></b></td>
                                            <td colspan="3" class="smallText"><?php echo $system['php'] . ' (' . TITLE_ZEND_VERSION . ' ' . $system['zend'] . ')'; ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <?php
                        ob_start();
                        phpinfo();
                        preg_match('%<style type="text/css">(.*?)</style>.*?<body>(.*?)</body>%s', ob_get_clean(), $matches);
                        echo "<div class='phpinfodisplay'><style type='text/css'>\n",
                        join("\n", array_map(
                                        create_function(
                                                '$i', 'return ".phpinfodisplay " . preg_replace_callback( "/,/", ",.phpinfodisplay ", $i );'
                                        ), preg_split('/\n/', trim(preg_replace_callback("/\nbody/", "\n", $matches[1])))
                                )
                        ),
                        "</style>\n",
                        $matches[2],
                        "\n</div>\n";
                        ?>
                    </td>
                </tr>
            </table></td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
