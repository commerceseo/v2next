<?php
/* -----------------------------------------------------------------
 * 	$Id: stats_products_purchased.php 420 2013-06-19 18:04:39Z akausch $
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
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top">
                                    <table border="0" width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                                        <tr class="dataTableHeadingRow">
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_NUMBER; ?></th>
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></th>
                                            <th class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PURCHASED; ?>&nbsp;</th>
                                        </tr>
                                        <?php
                                        if ($_GET['page'] > 1)
                                            $rows = $_GET['page'] * '20' - '20';
                                        $products_query_raw = "select p.products_id, p.products_ordered, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.products_ordered > 0 group by pd.products_id order by p.products_ordered DESC, pd.products_name";
                                        $products_split = new splitPageResults($_GET['page'], '20', $products_query_raw, $products_query_numrows);

                                        $products_query = xtc_db_query($products_query_raw);
                                        $i = 1;
                                        while ($products = xtc_db_fetch_array($products_query)) {
                                            $rows++;

                                            if (strlen($rows) < 2) {
                                                $rows = '0' . $rows;
                                            }
                                            ?>
                                            <tr class="<?php echo (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite'); ?>" onclick="document.location.href = '<?php echo xtc_href_link(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $products['products_id'] . '&origin=' . FILENAME_STATS_PRODUCTS_PURCHASED . '?page=' . $_GET['page'], 'NONSSL'); ?>'">
                                                <td class="dataTableContent"><?php echo $rows; ?>.</td>
                                                <td class="dataTableContent"><?php echo $products['products_name']; ?></td>
                                                <td class="dataTableContent" align="center"><?php echo $products['products_ordered']; ?>&nbsp;</td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </table></td>
                            </tr>
                            <tr>
                                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                                        <tr>
                                            <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                                            <td class="smallText" align="right"><?php echo $products_split->display_links($products_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>&nbsp;</td>
                                        </tr>
                                    </table></td>
                            </tr>
                        </table></td>
                </tr>
            </table></td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
