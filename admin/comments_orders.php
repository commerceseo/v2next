<?php
/* ------------------------------------------------------------------------------
  $Id: comments_orders.php 420 2013-06-19 18:04:39Z akausch $

  XTC-CC - Contribution for XT-Commerce http://www.xt-commerce.com
  modified by http://www.netz-designer.de

  Copyright (c) 2003 netz-designer
  -----------------------------------------------------------------------------
  based on:
  $Id: blacklist.php,v 1.00 2003/04/10 BMC

  Copyright (c) 2003 BMC
  http://www.mainframes.co.uk

  Released under the GNU General Public License
  ------------------------------------------------------------------------------ */

require('includes/application_top.php');


switch ($_GET['action']) {
    case 'insert':
    case 'save':
        $comments_orders_id = xtc_db_prepare_input($_GET['coID']);
        $comments_orders_title = xtc_db_prepare_input($_POST['title']);
        $comments_orders_mail_text = xtc_db_prepare_input($_POST['mail_text']);

        $sql_data_array = array('title' => $comments_orders_title);

        if ($_GET['action'] == 'insert') {
            $insert_sql_data = array('mail_text' => $comments_orders_mail_text);
            $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
            xtc_db_perform('mail_templates', $sql_data_array);
            $comments_orders_id = xtc_db_insert_id();
        } elseif ($_GET['action'] == 'save') {
            $update_sql_data = array(
                'mail_text' => $comments_orders_mail_text,
                'title' => $comments_orders_title
            );
            $sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
            xtc_db_perform('mail_templates', $sql_data_array, 'update', "id = '" . xtc_db_input($comments_orders_id) . "'");
        }

        xtc_redirect(xtc_href_link('comments_orders.php', 'page=' . $_GET['page'] . '&coID=' . $comments_orders_id));
        break;
    case 'deleteconfirm':
        $comments_orders_id = xtc_db_prepare_input($_GET['coID']);
        xtc_db_query("delete from mail_templates where id = '" . xtc_db_input($comments_orders_id) . "'");
        xtc_redirect(xtc_href_link('comments_orders.php', 'page=' . $_GET['page']));
        break;
}
require(DIR_WS_INCLUDES . 'header.php');
?>
<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td width="100%">
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE_COMMENTS_ORDERS; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                                        <tr class="dataTableHeadingRow">
                                            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COMMENT_ORDER; ?></td>
                                            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                                        </tr>
                                        <?php
                                        $comments_orders_query_raw = "SELECT * FROM mail_templates ORDER BY title";
                                        $comments_orders_split = new splitPageResults($_GET['page'], '20', $comments_orders_query_raw, $comments_orders_query_numrows);
                                        $comments_orders_query = xtc_db_query($comments_orders_query_raw);
                                        while ($commentsorders = xtc_db_fetch_array($comments_orders_query)) {
                                            if (((!$_GET['coID']) || (@$_GET['coID'] == $commentsorders['id'])) && (!$bInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
                                                $comments_orders_numbers_query = xtc_db_query("select count(*) as comments_orders_count from mail_templates where id = '" . $commentsorders['id'] . "'");
                                                $comments_orders_numbers = xtc_db_fetch_array($comments_orders_numbers_query);

                                                $bInfo_array = xtc_array_merge($commentsorders, $comments_orders_numbers);
                                                $bInfo = new objectInfo($bInfo_array);
                                                #print_r ($bInfo);
                                            }

                                            if ((is_object($bInfo)) && ($commentsorders['id'] == $bInfo->id)) {
                                                echo '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link('comments_orders.php', 'page=' . $_GET['page'] . '&coID=' . $commentsorders['id'] . '&action=edit') . '\'">' . "\n";
                                            } else {
                                                echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link('comments_orders.php', 'page=' . $_GET['page'] . '&coID=' . $commentsorders['id']) . '\'">' . "\n";
                                            }
                                            ?>
                                            <td class="dataTableContent"><?php echo $commentsorders['title']; ?></td>
                                            <td class="dataTableContent" align="right"><?php if ((is_object($bInfo)) && ($commentsorders['id'] == $bInfo->id)) {
                                            echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif');
                                        } else {
                                            echo '<a href="' . xtc_href_link('comments_orders.php', 'page=' . $_GET['page'] . '&coID=' . $commentsorders['id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                                        } ?>&nbsp;</td>
    <?php
    echo '</tr>';
}
?>
                                        <tr>
                                            <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                                                    <tr>
                                                        <td class="smallText" valign="top"><?php echo $comments_orders_split->display_count($comments_orders_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BLACKLIST_CARDS); ?></td>
                                                        <td class="smallText" align="right"><?php echo $comments_orders_split->display_links($comments_orders_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                                                    </tr>
                                                </table></td>
                                        </tr>
<?php
if ($_GET['action'] != 'new') {
    ?>
                                            <tr>
                                                <td align="right" colspan="2" class="smallText"><?php echo '<a class="button" onClick="this.blur();" href="' . xtc_href_link('comments_orders.php', 'page=' . $_GET['page'] . '&coID=' . $bInfo->id . '&action=new') . '">' . BUTTON_INSERT . '</a>'; ?></td>
                                            </tr>
                                    <?php
                                }
                                ?>
                                    </table></td>
                                <?php
                                $heading = array();
                                $contents = array();
                                switch ($_GET['action']) {
                                    case 'new':
                                        $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_COMMENTS_ORDERS . '</b>');

                                        $contents = array('form' => xtc_draw_form('commentsordersform', 'comments_orders.php', 'action=insert', 'post', 'enctype="multipart/form-data"'));
                                        $contents[] = array('text' => TEXT_NEW_INTRO);
                                        $contents[] = array('text' => '<br />' . TEXT_COMMENTS_ORDERS_TITLE . '<br />' . xtc_draw_input_field('title'));
                                        $contents[] = array('text' => '<br />' . TEXT_COMMENTS_ORDERS_MAIL_TEXT . '<br />' . xtc_draw_textarea_field('mail_text', 10, 100, 150));

                                        $comments_orders_inputs_string = '';

                                        $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link('comments_orders.php', 'page=' . $_GET['page'] . '&coID=' . $_GET['coID']) . '">' . BUTTON_CANCEL . '</a>');
                                        break;
                                    case 'edit':
                                        $heading[] = array('text' => '<b>' . TEXT_HEADING_EDIT_COMMENTS_ORDERS . '</b>');

                                        $contents = array('form' => xtc_draw_form('commentsordersform', 'comments_orders.php', 'page=' . $_GET['page'] . '&coID=' . $bInfo->id . '&action=save', 'post', 'enctype="multipart/form-data"'));
                                        $contents[] = array('text' => TEXT_EDIT_INTRO);
                                        $contents[] = array('text' => '<br />' . TEXT_COMMENTS_ORDERS_TITLE . '<br />' . xtc_draw_input_field('title', $bInfo->title));
                                        $contents[] = array('text' => '<br />' . TEXT_COMMENTS_ORDERS_MAIL_TEXT . '<br />' . xtc_draw_textarea_field('mail_text', 10, 100, 150, $bInfo->mail_text));

                                        $comments_orders_inputs_string = '';

                                        $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link('comments_orders.php', 'page=' . $_GET['page'] . '&coID=' . $mInfo->id) . '">' . BUTTON_CANCEL . '</a>');
                                        break;
                                    case 'delete':
                                        $heading[] = array('text' => '<b>' . TEXT_HEADING_DELETE_COMMENTS_ORDERS . '</b>');

                                        $contents = array('form' => xtc_draw_form('commentsordersform', 'comments_orders.php', 'page=' . $_GET['page'] . '&coID=' . $bInfo->id . '&action=deleteconfirm'));
                                        $contents[] = array('text' => TEXT_DELETE_INTRO);
                                        $contents[] = array('text' => '<br /><b>' . $bInfo->title . '</b>');


                                        $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_DELETE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link('comments_orders.php', 'page=' . $_GET['page'] . '&coID=' . $bInfo->id) . '">' . BUTTON_CANCEL . '</a>');
                                        break;
                                    default:
                                        if (is_object($bInfo)) {
                                            $heading[] = array('text' => '<b>' . $bInfo->title . '</b>');

                                            $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link('comments_orders.php', 'page=' . $_GET['page'] . '&coID=' . $bInfo->id . '&action=edit') . '">' . BUTTON_EDIT . '</a> <a class="button" onClick="this.blur();" href="' . xtc_href_link('comments_orders.php', 'page=' . $_GET['page'] . '&coID=' . $bInfo->id . '&action=delete') . '">' . BUTTON_DELETE . '</a>');
                                            $contents[] = array('text' => '<br />' . TEXT_TITLE . ' ' . $bInfo->title);
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
            </table></td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
