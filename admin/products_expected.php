<?php
/* -----------------------------------------------------------------
 * 	$Id: products_expected.php 420 2013-06-19 18:04:39Z akausch $
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
xtc_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_date_available = '' WHERE to_days(now()) > to_days(products_date_available)");
require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                </tr>
            </table></td>
    </tr>
    <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <tr class="dataTableHeadingRow">
                                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_DATE_EXPECTED; ?></td>
                                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                            </tr>
                            <?php
                            $products_query_raw = "SELECT pd.products_id, pd.products_name, p.products_date_available FROM " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p WHERE p.products_id = pd.products_id AND p.products_date_available != '' AND p.products_date_available != '0000-00-00 00:00:00' AND pd.language_id = '" . $_SESSION['languages_id'] . "' ORDER BY p.products_date_available DESC";
                            $products_split = new splitPageResults($_GET['page'], '20', $products_query_raw, $products_query_numrows);
                            $products_query = xtc_db_query($products_query_raw);
                            while ($products = xtc_db_fetch_array($products_query)) {
                                if (((!$_GET['pID']) || ($_GET['pID'] == $products['products_id'])) && (!$pInfo)) {
                                    $pInfo = new objectInfo($products);
                                }

                                if ((is_object($pInfo)) && ($products['products_id'] == $pInfo->products_id)) {
                                    echo '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link(FILENAME_CATEGORIES, 'pID=' . $products['products_id'] . '&action=new_product') . '\'">' . "\n";
                                } else {
                                    echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link(FILENAME_PRODUCTS_EXPECTED, 'page=' . $_GET['page'] . '&pID=' . $products['products_id']) . '\'">' . "\n";
                                }
                                ?>
                                <td class="dataTableContent"><?php echo $products['products_name']; ?></td>
                                <td class="dataTableContent" align="center"><?php echo xtc_date_short($products['products_date_available']); ?></td>
                                <td class="dataTableContent" align="right"><?php if ((is_object($pInfo)) && ($products['products_id'] == $pInfo->products_id)) {
                                echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif');
                            } else {
                                echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_EXPECTED, 'page=' . $_GET['page'] . '&pID=' . $products['products_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                            } ?>&nbsp;</td>
                    </tr>
<?php } ?>
                <tr>
                    <td colspan="3">
                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <tr>
                                <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED); ?></td>
                                <td class="smallText" align="right"><?php echo $products_split->display_links($products_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table></td>
        <?php
        $heading = array();
        $contents = array();
        if (is_object($pInfo)) {
            $heading[] = array('text' => '<b>' . $pInfo->products_name . '</b>');

            $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CATEGORIES, 'pID=' . $pInfo->products_id . '&action=new_product') . '">' . BUTTON_EDIT . '</a>');
            $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_EXPECTED . ' ' . xtc_date_short($pInfo->products_date_available));
        }

        if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
            echo '            <td width="25%" valign="top">' . "\n";

            $box = new box;
            echo $box->infoBox($heading, $contents);

            echo '            </td>' . "\n";
        }
        ?>
    </tr>
</table></td>
</tr>
</table>

<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');