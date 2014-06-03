<?php
/* -----------------------------------------------------------------
 * 	$Id: stats_keywords_all_print.php 420 2013-06-19 18:04:39Z akausch $
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
require(DIR_FS_INC . 'xtc_image_submit.inc.php');
require(DIR_WS_INCLUDES . 'metatag.php');
?>
</head>
<body onload="window.print()" style="background: #fff">
    <table border="0" width="100%" cellspacing="2" cellpadding="2">
        <tr>
            <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="main" valign="top"><?php echo HEADING_TITLE ?></td>
                                </tr>
                            </table></td>
                    </tr>
                    <tr>
                        <td>
                            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                <tr>          
                                    <td valign="top">
                                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td width=105><?php
                                                    echo xtc_draw_form('text_order', 'stats_keywords_all_print.php');
                                                    echo xtc_draw_hidden_field('action', 'show_searches');
                                                    echo xtc_draw_hidden_field('sortorder', 'search_text');
                                                    echo xtc_draw_hidden_field('ascdsc', 'ASC');
                                                    echo '<input type="submit" value="' . SORT_BY_NAME . '" class="button" />';
                                                    echo "</form>\n</td>\n<td width=105>\n";

                                                    echo xtc_draw_form('text_order', 'stats_keywords_all_print.php');
                                                    echo xtc_draw_hidden_field('action', 'show_searches');
                                                    echo xtc_draw_hidden_field('sortorder', 'search_count');
                                                    echo xtc_draw_hidden_field('ascdsc', 'DESC');
                                                    echo '<input type="submit" value="' . SORT_BY_COUNT . '" class="button" />';
                                                    echo "</form>\n";
                                                    ?>				 </td>

                                            </tr>
                                        </table></td>
                                </tr>
                                <tr>
                                    <td width="100%">
                                        <?php
                                        if ($_POST['action'] == 'deletedb') {
                                            xtc_db_query("delete from search_queries_sorted");
                                        } // delete db


                                        if ($_POST['action'] == 'updatedb') {
                                            $sql_q = xtc_db_query("select search_id, search_text, search_result from search_queries_all order by search_text");
                                            while ($keywords = xtc_db_fetch_array($sql_q)) {

                                                /*
                                                  $sql_count = xtc_db_query("select count(*) as total from search_queries_all where search_text = '" .
                                                  $keywords['search_text'] . "'");
                                                  $sql_count_result = xtc_db_fetch_array($sql_count);
                                                 */
                                                $update_q = xtc_db_query("select search_text, search_count, search_result from search_queries_sorted where search_text = '" . $keywords['search_text'] . "'");
                                                $update_q_result = xtc_db_fetch_array($update_q);
                                                $count = 1 + $update_q_result['search_count'];

                                                if ($update_q_result['search_count'] != '') {
                                                    xtc_db_query("update ignore search_queries_sorted set search_count = '" .
                                                            $count . "' where search_text = '" .
                                                            $keywords['search_text'] . "'");
                                                } else {
                                                    xtc_db_query("insert ignore into search_queries_sorted (search_text, search_count, search_result) values ('" .
                                                            $keywords['search_text'] . "','1','" . $keywords['search_result'] . "')");
                                                } // search_count

                                                xtc_db_query("delete from search_queries_all where search_id = '" . $keywords['search_id'] . "'");
                                            } // while
                                        } // updatedb
                                        ?>
                                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                            <tr class="dataTableHeadingRow">
                                                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NUMBER; ?></td>
                                                <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_SEARCH_TOTAL; ?></td>
                                                <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_KEYWORD; ?></td>
                                                <?php if (LOG_PRODUCT_RETURNS == 'true') { ?>
                                                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_RETURNED; ?></td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                            if ($_POST['action'] == 'show_searches')
                                                show_search($_POST['sortorder'], $_POST['ascdsc']);
                                            else
                                                show_search('search_count', 'DESC');

                                            function show_search($search_by, $sort_order) {

                                                $keywords_query_raw = "select search_text, search_count, search_result from " . search_queries_sorted . " order by '" . $search_by . "' " . $sort_order . "";
                                                $keywords_query = xtc_db_query($keywords_query_raw);
                                                while ($keywords = xtc_db_fetch_array($keywords_query)) {
                                                    $rows++;

                                                    if (strlen($rows) < 2) {
                                                        $rows = '0' . $rows;
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td class="dataTableContent"><?php echo $rows; ?>.</td>
                                                        <td class="dataTableContent">&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $keywords['search_count']; ?></td>
                                                        <td class="dataTableContent">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $keywords['search_text']; ?></td>
                                                        <?php
                                                        if (LOG_PRODUCT_RETURNS == 'true')
                                                            echo '<td class="dataTableContent">' . $keywords['search_result'] . '</td>';
                                                    }
                                                }
                                                ?></tr></table>
                                    </td>
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
</body>
</html>
<?php
require(DIR_WS_INCLUDES . 'application_bottom.php');

