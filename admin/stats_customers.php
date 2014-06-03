<?php
/* -----------------------------------------------------------------
 * 	$Id: stats_customers.php 420 2013-06-19 18:04:39Z akausch $
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
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <tr>
                                <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                                        <tr class="dataTableHeadingRow">
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_NUMBER; ?></th>
                                            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS; ?></th>
                                            <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_PURCHASED; ?>&nbsp;</th>
                                        </tr>
                                        <?php
                                        if ($_GET['page'] > 1)
                                            $rows = $_GET['page'] * '20' - '20';
                                        $customers_query_raw = "select c.customers_firstname, c.customers_lastname, sum(op.final_price) as ordersum from " . TABLE_CUSTOMERS . " c, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_ORDERS . " o where c.customers_id = o.customers_id and o.orders_id = op.orders_id group by c.customers_firstname, c.customers_lastname order by ordersum DESC";
                                        $customers_split = new splitPageResults($_GET['page'], '20', $customers_query_raw, $customers_query_numrows);
                                        // fix counted customers
                                        $customers_query_numrows = xtc_db_query("select customers_id from " . TABLE_ORDERS . " group by customers_id");
                                        $customers_query_numrows = xtc_db_num_rows($customers_query_numrows);

                                        $customers_query = xtc_db_query($customers_query_raw);
                                        $i = 1;
                                        while ($customers = xtc_db_fetch_array($customers_query)) {
                                            $rows++;

                                            if (strlen($rows) < 2) {
                                                $rows = '0' . $rows;
                                            }
                                            ?>
                                            <tr class="<?php echo (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite'); ?>" onclick="document.location.href = '<?php echo xtc_href_link(FILENAME_CUSTOMERS, 'search=' . $customers['customers_lastname'], 'NONSSL'); ?>'">
                                                <td class="dataTableContent"><?php echo $rows; ?>.</td>
                                                <td class="dataTableContent"><?php echo '<a href="' . xtc_href_link(FILENAME_CUSTOMERS, 'search=' . $customers['customers_lastname'], 'NONSSL') . '">' . $customers['customers_firstname'] . ' ' . $customers['customers_lastname'] . '</a>'; ?></td>
                                                <td class="dataTableContent" align="right"><?php echo $currencies->format($customers['ordersum']); ?>&nbsp;</td>
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
                                            <td class="smallText" valign="top"><?php echo $customers_split->display_count($customers_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                                            <td class="smallText" align="right"><?php echo $customers_split->display_links($customers_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>&nbsp;</td>
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
