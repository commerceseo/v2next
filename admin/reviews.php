<?php
/* -----------------------------------------------------------------
 * 	ID:						reviews.php
 * 	Letzter Stand:			v2.2 R365
 * 	zuletzt geaendert von:	akausch
 * 	Datum:					2012/07/03
 *
 * 	Copyright (c) since 2010 commerce:SEO by Webdesign Erfurt
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
require_once (DIR_FS_INC . 'xtc_set_reviews_status.inc.php');

if (isset($_GET['flag']) && ($_GET['action'] == 'edit')) {
    xtc_set_reviews_status($_GET['id'], $_GET['flag']);
    xtc_redirect(xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&action=edit&rID=' . $_GET['rID'] . '&id=' . $_GET['rID'], 'NONSSL'));
}

if ($_GET['action']) {
    switch ($_GET['action']) {
        case 'setflag':
            xtc_set_reviews_status($_GET['id'], $_GET['flag']);
            xtc_redirect(xtc_href_link(FILENAME_REVIEWS, '', 'NONSSL'));
            break;

        case 'update':
            $reviews_id = xtc_db_prepare_input($_GET['rID']);
            $reviews_rating = xtc_db_prepare_input($_POST['reviews_rating']);
            $last_modified = xtc_db_prepare_input($_POST['last_modified']);
            $reviews_text = xtc_db_prepare_input($_POST['reviews_text']);
            $customers_name = xtc_db_prepare_input($_POST['customers_name']);
            $date_added = xtc_db_prepare_input($_POST['date_added']);

            xtc_db_query("UPDATE " . TABLE_REVIEWS . " SET reviews_rating = '" . xtc_db_input($reviews_rating) . "', customers_name = '" . xtc_db_input($customers_name) . "', date_added = '" . xtc_db_input($date_added) . "', last_modified = now() where reviews_id = '" . xtc_db_input($reviews_id) . "'");
            xtc_db_query("update " . TABLE_REVIEWS_DESCRIPTION . " set reviews_text = '" . xtc_db_input($reviews_text) . "' where reviews_id = '" . xtc_db_input($reviews_id) . "'");

            xtc_redirect(xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews_id));
            break;

        case 'deleteconfirm':
            $reviews_id = xtc_db_prepare_input($_GET['rID']);

            xtc_db_query("delete from " . TABLE_REVIEWS . " where reviews_id = '" . xtc_db_input($reviews_id) . "'");
            xtc_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . xtc_db_input($reviews_id) . "'");

            xtc_redirect(xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page']));
            break;
    }
}
require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="100%"><table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                            </tr>
                        </table></td>
                </tr>
                <?php
                if ($_GET['action'] == 'edit') {
                    $rID = xtc_db_prepare_input($_GET['rID']);

                    $reviews_query = xtc_db_query("select r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, rd.reviews_text, r.reviews_rating, r.reviews_status from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . xtc_db_input($rID) . "' and r.reviews_id = rd.reviews_id");
                    $reviews = xtc_db_fetch_array($reviews_query);
                    $products_query = xtc_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $reviews['products_id'] . "'");
                    $products = xtc_db_fetch_array($products_query);

                    $products_name_query = xtc_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $reviews['products_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
                    $products_name = xtc_db_fetch_array($products_name_query);

                    $rInfo_array = xtc_array_merge($reviews, $products, $products_name);
                    $rInfo = new objectInfo($rInfo_array);
                    ?>
                    <tr><?php echo xtc_draw_form('review', FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=preview'); ?>
                        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="main" valign="top">
                                        <b><?php echo ENTRY_PRODUCT; ?></b> 
                                        <?php echo $rInfo->products_name; ?><br />
                                        <b><?php echo ENTRY_FROM; ?></b> <?php echo xtc_draw_input_field('customers_name', $rInfo->customers_name, 'maxlength="32"', false); ?><br /><br />

                                        <b><?php echo ENTRY_DATE; ?></b> <?php echo xtc_draw_input_field('date_added', $rInfo->date_added, 'maxlength="32"', false); ?><br />
                                        <b>Status dieser Bewertung:</b> <?php
                                        if ($rInfo->reviews_status == '1') {
                                            echo xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . xtc_href_link(FILENAME_REVIEWS, 'flag=3&page=' . $_GET['page'] . '&action=edit&rID=' . $_GET['rID'] . '&id=' . $_GET['rID'], 'NONSSL') . '">' . xtc_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                                        } else {
                                            echo '<a href="' . xtc_href_link(FILENAME_REVIEWS, 'flag=1&page=' . $_GET['page'] . '&action=edit&rID=' . $_GET['rID'] . '&id=' . $_GET['rID'], 'NONSSL') . '">' . xtc_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                                        }
                                        ?>
                                    </td>
                                    <td class="main" align="right" valign="top"><?php echo xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_INFO_IMAGES . $rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?></td>
                                </tr>
                            </table></td>
                    </tr>
                    <tr>
                        <td><table witdh="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="main" valign="top"><b><?php echo ENTRY_REVIEW; ?></b><br /><br /><?php echo xtc_draw_textarea_field('reviews_text', 'soft', '60', '15', $rInfo->reviews_text); ?></td>
                                </tr>
                                <tr>
                                    <td class="smallText" align="right"><?php echo ENTRY_REVIEW_TEXT; ?></td>
                                </tr>
                            </table></td>
                    </tr>
                    <tr>
                        <td class="main"><b><?php echo ENTRY_RATING; ?></b>&nbsp;<?php echo TEXT_BAD; ?>&nbsp;<?php for ($i = 1; $i <= 5; $i++)
                                            echo xtc_draw_radio_field('reviews_rating', $i, '', $rInfo->reviews_rating) . '&nbsp;'; echo TEXT_GOOD; ?></td>
                    </tr>
                    <tr>
                        <td align="right" class="main"><?php echo xtc_draw_hidden_field('reviews_id', $rInfo->reviews_id) . xtc_draw_hidden_field('products_id', $rInfo->products_id) . xtc_draw_hidden_field('products_name', $rInfo->products_name) . xtc_draw_hidden_field('products_image', $rInfo->products_image) . '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_PREVIEW . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID']) . '">' . BUTTON_CANCEL . '</a>'; ?></td>
                        </form></tr>
                    <?php
                } elseif ($_GET['action'] == 'preview') {
                    if ($_POST) {
                        $rInfo = new objectInfo($_POST);
                    } else {
                        $reviews_query = xtc_db_query("select r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, rd.reviews_text, r.reviews_rating from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . $_GET['rID'] . "' and r.reviews_id = rd.reviews_id");
                        $reviews = xtc_db_fetch_array($reviews_query);
                        $products_query = xtc_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $reviews['products_id'] . "'");
                        $products = xtc_db_fetch_array($products_query);

                        $products_name_query = xtc_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $reviews['products_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
                        $products_name = xtc_db_fetch_array($products_name_query);

                        $rInfo_array = xtc_array_merge($reviews, $products, $products_name);
                        $rInfo = new objectInfo($rInfo_array);
                    }
                    ?>
                    <tr><?php echo xtc_draw_form('update', FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=update', 'post', 'enctype="multipart/form-data"'); ?>
                        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="main" valign="top">
                                        <b><?php echo ENTRY_PRODUCT; ?></b> <?php echo $rInfo->products_name; ?><br />
                                        <b><?php echo ENTRY_FROM; ?></b> <?php echo xtc_db_output($rInfo->customers_name); ?><br /><br />
                                        <b><?php echo ENTRY_DATE; ?></b> <?php echo xtc_date_short($rInfo->date_added); ?></td>
                                    <td class="main" align="right" valign="top">
    <?php echo xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . $rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?>
                                    </td>
                                </tr>
                            </table>
                    </tr>
                    <tr>
                        <td><table witdh="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top" class="main"><b><?php echo ENTRY_REVIEW; ?></b><br /><br /><?php echo nl2br(xtc_db_output(xtc_break_string($rInfo->reviews_text, 15))); ?></td>
                                </tr>
                            </table></td>
                    </tr>
                    <tr>
                        <td class="main"><b><?php echo ENTRY_RATING; ?></b>&nbsp;<?php echo xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/stars_' . $rInfo->reviews_rating . '.gif', sprintf(TEXT_OF_5_STARS, $rInfo->reviews_rating)); ?>&nbsp;<small>[<?php echo sprintf(TEXT_OF_5_STARS, $rInfo->reviews_rating); ?>]</small></td>
                    </tr>
                    <?php
                    if ($_POST) {
                        // Re-Post all POST'ed variables
                        reset($_POST);
                        while (list($key, $value) = each($_POST))
                            echo '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars(stripslashes($value)) . '">';
                        ?>
                        <tr>
                            <td align="right" class="smallText"><?php echo '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=edit') . '">' . BUTTON_BACK . '</a> <input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id) . '">' . BUTTON_CANCEL . '</a>'; ?></td>
                            </form></tr>
                        <?php
                    } else {
                        if ($_GET['origin']) {
                            $back_url = $_GET['origin'];
                            $back_url_params = '';
                        } else {
                            $back_url = FILENAME_REVIEWS;
                            $back_url_params = 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id;
                        }
                        ?>
                        <tr>
                            <td align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . xtc_href_link($back_url, $back_url_params, 'NONSSL') . '">' . BUTTON_BACK . '</a>'; ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                                            <tr class="dataTableHeadingRow">
                                                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                                                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_RATING; ?></td>
                                                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
                                                <td class="dataTableHeadingContent" align="center" width='5%'><?php echo TABLE_HEADING_STATUS; ?></td>
                                                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                                            </tr>
                                            <?php
                                            $reviews_query_raw = "select reviews_status, reviews_id, products_id, date_added, last_modified, reviews_rating from " . TABLE_REVIEWS . " order by date_added DESC";
                                            $reviews_split = new splitPageResults($_GET['page'], '20', $reviews_query_raw, $reviews_query_numrows);
                                            $reviews_query = xtc_db_query($reviews_query_raw);
                                            while ($reviews = xtc_db_fetch_array($reviews_query)) {
                                                if (((!$_GET['rID']) || ($_GET['rID'] == $reviews['reviews_id'])) && (!$rInfo)) {
                                                    $reviews_text_query = xtc_db_query("select r.reviews_read, r.customers_name, rd.reviews_text, length(rd.reviews_text) as reviews_text_size from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . $reviews['reviews_id'] . "' and r.reviews_id = rd.reviews_id");
                                                    $reviews_text = xtc_db_fetch_array($reviews_text_query);

                                                    $products_image_query = xtc_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $reviews['products_id'] . "'");
                                                    $products_image = xtc_db_fetch_array($products_image_query);

                                                    $products_name_query = xtc_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $reviews['products_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
                                                    $products_name = xtc_db_fetch_array($products_name_query);

                                                    $reviews_average_query = xtc_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . $reviews['products_id'] . "'");
                                                    $reviews_average = xtc_db_fetch_array($reviews_average_query);

                                                    $review_info = xtc_array_merge($reviews_text, $reviews_average, $products_name);
                                                    $rInfo_array = xtc_array_merge($reviews, $review_info, $products_image);
                                                    $rInfo = new objectInfo($rInfo_array);
                                                }

                                                if ((is_object($rInfo)) && ($reviews['reviews_id'] == $rInfo->reviews_id)) {
                                                    echo '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=preview') . '\'">' . "\n";
                                                } else {
                                                    echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id']) . '\'">' . "\n";
                                                }
                                                ?>
                                                <td class="dataTableContent"><?php echo '<a href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id'] . '&action=preview') . '">' . xtc_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . xtc_get_products_name($reviews['products_id']); ?></td>
                                                <td class="dataTableContent" align="right"><?php echo xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/stars_' . $reviews['reviews_rating'] . '.gif'); ?></td>
                                                <td class="dataTableContent" align="right"><?php echo xtc_date_short($reviews['date_added']); ?></td>
                                                <td  class="dataTableContent" align="center">
                                                    <?php
                                                    if ($reviews['reviews_status'] == '1') {
                                                        echo xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . xtc_href_link(FILENAME_REVIEWS, 'action=setflag&flag=3&id=' . $reviews['reviews_id'], 'NONSSL') . '">' . xtc_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                                                    } else {
                                                        echo '<a href="' . xtc_href_link(FILENAME_REVIEWS, 'action=setflag&flag=1&id=' . $reviews['reviews_id'], 'NONSSL') . '">' . xtc_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                                                    }
                                                    ?>
                                                </td>
                                                <td class="dataTableContent" align="right"><?php if ((is_object($rInfo)) && ($reviews['reviews_id'] == $rInfo->reviews_id)) {
                                                        echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif');
                                                    } else {
                                                        echo '<a href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                                                    } ?>&nbsp;</td>
                                    </tr>
        <?php
    }
    ?>
                                <tr>
                                    <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td class="smallText" valign="top"><?php echo $reviews_split->display_count($reviews_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
                                                <td class="smallText" align="right"><?php echo $reviews_split->display_links($reviews_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                                            </tr>
                                        </table></td>
                                </tr>
                            </table></td>
                        <?php
                        $heading = array();
                        $contents = array();
                        switch ($_GET['action']) {
                            case 'delete':
                                $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_REVIEW . '</b>');

                                $contents = array('form' => xtc_draw_form('reviews', FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=deleteconfirm'));
                                $contents[] = array('text' => TEXT_INFO_DELETE_REVIEW_INTRO);
                                $contents[] = array('text' => '<br /><b>' . $rInfo->products_name . '</b>');
                                $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_DELETE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id) . '">' . BUTTON_CANCEL . '</a>');
                                break;

                            default:
                                if (is_object($rInfo)) {
                                    $heading[] = array('text' => '<b>' . $rInfo->products_name . '</b>');

                                    $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=edit') . '">' . BUTTON_EDIT . '</a> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>');
                                    $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . xtc_date_short($rInfo->date_added));
                                    if (xtc_not_null($rInfo->last_modified))
                                        $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . xtc_date_short($rInfo->last_modified));
                                    $contents[] = array('text' => '<br />' . xtc_product_thumb_image($rInfo->products_image, $rInfo->products_name));
                                    $contents[] = array('text' => '<br />' . TEXT_INFO_REVIEW_AUTHOR . ' ' . $rInfo->customers_name);
                                    $contents[] = array('text' => TEXT_INFO_REVIEW_RATING . ' ' . xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/stars_' . $rInfo->reviews_rating . '.gif'));
                                    $contents[] = array('text' => TEXT_INFO_REVIEW_READ . ' ' . $rInfo->reviews_read);
                                    $contents[] = array('text' => '<br />' . TEXT_INFO_REVIEW_SIZE . ' ' . $rInfo->reviews_text_size . ' bytes');
                                    $contents[] = array('text' => '<br />' . strip_tags($rInfo->reviews_text));
                                    $contents[] = array('text' => '<br />' . TEXT_INFO_PRODUCTS_AVERAGE_RATING . ' ' . number_format($rInfo->average_rating, 2) . '%');
                                }
                                break;
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
    <?php
}
?>
</table></td>
</tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
