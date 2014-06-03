<?php
/* -----------------------------------------------------------------
 * 	$Id: stats_keywords_all.php 577 2013-08-26 14:47:41Z akausch $
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
require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td>
                        <table class="table_pageHeading" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td class="pageHeading" width="100%">
                                    <?php echo HEADING_TITLE ?>
                                </td>
                            </tr>
                        </table></td>
                </tr>
                <tr>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <tr>
                                <td valign="top">
                                    <?php
                                    if (LOG_SEARCH_RESULTS == 'false')
                                        echo DESC_1;
                                    ?>
                                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                        <tr>
                                            <td width="100">
                                                <?php
                                                echo xtc_draw_form('delete', 'stats_keywords_all.php');
                                                echo xtc_draw_hidden_field('action', 'deletedb');
                                                ?>
                                                <input type="submit" value="<?php echo CLEAR_DATABASE ?>" class="button" />
                                                </form>
                                            </td>
                                            <td width="100">
                                                <?php
                                                echo xtc_draw_form('update', 'stats_keywords_all.php');
                                                echo xtc_draw_hidden_field('action', 'updatedb') . xtc_draw_hidden_field('page', ($_GET['page'] != '' ? $_GET['page'] : '1'));
                                                ?>
                                                <input type="submit" value="<?php echo UPDATE_DATABASE ?>" class="button" />
                                                </form>
                                            </td>
                                            <td width="200"><a class="button" onclick="javascript:window.open('<?php echo xtc_href_link(FILENAME_STATS_KEYWORDS_ALL_PRINT); ?>', 'popup', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=500, height=600')"><?php echo PRINT_STATS ?></a>
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </table></td>
                            </tr>
                            <tr>
                                <td width="100%">
                                    <?php
                                    if ($_POST['action'] == 'deletedb')
                                        xtc_db_query("DELETE FROM search_queries_sorted");

                                    elseif ($_POST['action'] == 'updatedb') {
                                        $sql_q = xtc_db_query("SELECT search_id, search_text, search_result FROM search_queries_all ORDER BY search_text");
                                        while ($keywords = xtc_db_fetch_array($sql_q)) {
                                            $update_q = xtc_db_query("SELECT search_count AS total FROM search_queries_sorted WHERE search_text = '" . addslashes($keywords['search_text']) . "'");

                                            if (xtc_db_num_rows($update_q, true) > 0) {
                                                $update = xtc_db_fetch_array($update_q);
                                                xtc_db_query("UPDATE search_queries_sorted SET search_count = ('" . $update['total'] . "' + 1), search_result = '" . addslashes($keywords['search_result']) . "' WHERE search_text = '" . addslashes($keywords['search_text']) . "'");
                                            }
                                            else
                                                xtc_db_query("INSERT INTO search_queries_sorted (search_text, search_count, search_result) VALUES ('" . addslashes($keywords['search_text']) . "', '1', '" . $keywords['search_result'] . "')");

                                            xtc_db_query("DELETE FROM search_queries_all WHERE search_id = '" . $keywords['search_id'] . "' ");
                                        }
                                    }
                                    ?>
                                    <table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                                        <tr class="dataTableHeadingRow">
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_NUMBER; ?></th>
                                            <th class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_SEARCH_TOTAL; ?></th>
                                            <th class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_KEYWORD; ?></th>
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_RETURNED; ?></th>
                                        </tr>
                                        <?php
                                        if ($_GET['page'] > 1)
                                            $rows = ($_GET['page'] * '65' - '65');

                                        $keywords_query_raw = "SELECT search_text, search_count, search_result FROM search_queries_sorted ORDER BY search_count";
                                        $keywords_split = new splitPageResults($_GET['page'], '65', $keywords_query_raw, $keywords_query_numrows);
                                        $keywords_query = xtc_db_query($keywords_query_raw);
                                        $i = 1;
                                        while ($keywords_sorted = xtc_db_fetch_array($keywords_query)) {
                                            $rows++;
                                            echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '">';
                                            ?>
                                            <td><?php echo $rows; ?>.</td>
                                            <td><?php echo $keywords_sorted['search_count']; ?></td>
                                            <td><?php echo $keywords_sorted['search_text']; ?></td>
                                            <td><?php echo $keywords_sorted['search_result']; ?></td>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                            </tr></table>
                    </td>
                </tr>
                <tr><td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <tr>
                                <td class="smallText" valign="top"><?php echo $keywords_split->display_count($keywords_query_numrows, '65', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_KEYWORDS); ?></td>
                                <td class="smallText" align="right"><?php echo $keywords_split->display_links($keywords_query_numrows, '65', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                            </tr>
                        </table></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</td>
</tr>
</table>
</table>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
