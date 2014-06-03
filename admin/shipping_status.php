<?php
/* -----------------------------------------------------------------
 * 	$Id: shipping_status.php 486 2013-07-15 22:08:14Z akausch $
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

switch ($_GET['action']) {
    case 'insert':
    case 'save':
        $shipping_status_id = xtc_db_prepare_input($_GET['oID']);

        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $shipping_status_name_array = $_POST['shipping_status_name'];
            $language_id = $languages[$i]['id'];

            $sql_data_array = array('shipping_status_name' => xtc_db_prepare_input($shipping_status_name_array[$language_id]));

            if ($_GET['action'] == 'insert') {
                if (!xtc_not_null($shipping_status_id)) {
                    $next_id_query = xtc_db_query("SELECT max(shipping_status_id) AS shipping_status_id FROM " . TABLE_SHIPPING_STATUS . "");
                    $next_id = xtc_db_fetch_array($next_id_query);
                    $shipping_status_id = $next_id['shipping_status_id'] + 1;
                }

                $insert_sql_data = array('shipping_status_id' => $shipping_status_id,
                    'language_id' => $language_id);
                $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
                xtc_db_perform(TABLE_SHIPPING_STATUS, $sql_data_array);
            } elseif ($_GET['action'] == 'save') {
                xtc_db_perform(TABLE_SHIPPING_STATUS, $sql_data_array, 'update', "shipping_status_id = '" . xtc_db_input($shipping_status_id) . "' and language_id = '" . $language_id . "'");
            }
        }

        if ($shipping_status_image = &xtc_try_upload('shipping_status_image', '../' . DIR_WS_ICONS)) {
            xtc_db_query("update " . TABLE_SHIPPING_STATUS . " set shipping_status_image = '" . $shipping_status_image->filename . "' where shipping_status_id = '" . xtc_db_input($shipping_status_id) . "'");
        }

        if ($_POST['default'] == 'on') {
            xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($shipping_status_id) . "' where configuration_key = 'DEFAULT_SHIPPING_STATUS_ID'");
        }

        xtc_redirect(xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $shipping_status_id));
        break;

    case 'deleteconfirm':
        $oID = xtc_db_prepare_input($_GET['oID']);

        $shipping_status_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'DEFAULT_SHIPPING_STATUS_ID'");
        $shipping_status = xtc_db_fetch_array($shipping_status_query);
        if ($shipping_status['configuration_value'] == $oID) {
            xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_SHIPPING_STATUS_ID'");
        }

        xtc_db_query("delete from " . TABLE_SHIPPING_STATUS . " where shipping_status_id = '" . xtc_db_input($oID) . "'");

        xtc_redirect(xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page']));
        break;

    case 'delete':
        $oID = xtc_db_prepare_input($_GET['oID']);


        $remove_status = true;
        if ($oID == DEFAULT_SHIPPING_STATUS_ID) {
            $remove_status = false;
            $messageStack->add(ERROR_REMOVE_DEFAULT_SHIPPING_STATUS, 'error');
        } else {
            
        }
        break;
}

require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellpadding="0" cellspacing="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading">
                                    <?php echo BOX_SHIPPING_STATUS; ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top"><table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                                        <tr class="dataTableHeadingRow">
                                            <th class="dataTableHeadingContent" width="1"><?php echo TABLE_HEADING_SHIPPING_STATUS; ?></th>
                                            <th class="dataTableHeadingContent" width="100%">&nbsp;</th>
                                            <th class="dataTableHeadingContent last" align="right"><?php echo TABLE_HEADING_ACTION; ?></th>
                                        </tr>
                                        <?php
                                        $shipping_status_query_raw = "select shipping_status_id, shipping_status_name,shipping_status_image from " . TABLE_SHIPPING_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "' order by shipping_status_id";
                                        $shipping_status_split = new splitPageResults($_GET['page'], '20', $shipping_status_query_raw, $shipping_status_query_numrows);
                                        $shipping_status_query = xtc_db_query($shipping_status_query_raw);
                                        $i = 1;
                                        while ($shipping_status = xtc_db_fetch_array($shipping_status_query)) {
                                            if (((!$_GET['oID']) || ($_GET['oID'] == $shipping_status['shipping_status_id'])) && (!$oInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
                                                $oInfo = new objectInfo($shipping_status);
                                            }
                                            if ((is_object($oInfo)) && ($shipping_status['shipping_status_id'] == $oInfo->shipping_status_id)) {
                                                echo '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id . '&action=edit') . '\'">' . "\n";
                                            } else {
                                                echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $shipping_status['shipping_status_id']) . '\'">' . "\n";
                                            }

                                            if (DEFAULT_SHIPPING_STATUS_ID == $shipping_status['shipping_status_id']) {
                                                echo '<td class="dataTableContent" align="center">';
                                                if ($shipping_status['shipping_status_image'] != '') {
                                                    echo xtc_image('../' . DIR_WS_ICONS . $shipping_status['shipping_status_image'], IMAGE_ICON_INFO);
                                                }
                                                else
                                                    echo '-';
                                                echo '</td>';
                                                echo '<td class="dataTableContent"><b>' . $shipping_status['shipping_status_name'] . ' (' . TEXT_DEFAULT . ')</b></td>' . "\n";
                                            } else {

                                                echo '<td class="dataTableContent last" align="center">';
                                                if ($shipping_status['shipping_status_image'] != '') {
                                                    echo xtc_image('../' . DIR_WS_ICONS . $shipping_status['shipping_status_image'], IMAGE_ICON_INFO);
                                                }
                                                else
                                                    echo '-';
                                                echo '</td>';
                                                echo '<td class="dataTableContent">' . $shipping_status['shipping_status_name'] . '</td>' . "\n";
                                            }
                                            ?>
                                            <td class="dataTableContent" align="right"><?php if ((is_object($oInfo)) && ($shipping_status['shipping_status_id'] == $oInfo->shipping_status_id)) {
                                            echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', '');
                                        } else {
                                            echo '<a href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $shipping_status['shipping_status_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                                        } ?>&nbsp;</td>
    <?php
    $i++;
    echo '</tr>';
}
?>
                                    </table>
                                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                        <tr>
                                            <td class="smallText" valign="top"><?php echo $shipping_status_split->display_count($shipping_status_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SHIPPING_STATUS); ?></td>
                                            <td class="smallText" align="right"><?php echo $shipping_status_split->display_links($shipping_status_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                                        </tr>
                                        <?php
                                        if (substr($_GET['action'], 0, 3) != 'new') {
                                            ?>
                                            <tr>
                                                <td colspan="2" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&action=new') . '">' . BUTTON_INSERT . '</a>'; ?></td>
                                            </tr>
                                    <?php
                                }
                                ?>
                                    </table>
                                </td>
                                <?php
                                $heading = array();
                                $contents = array();
                                switch ($_GET['action']) {
                                    case 'new':
                                        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_SHIPPING_STATUS . '</b>');

                                        $contents = array('form' => xtc_draw_form('status', FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&action=insert', 'post', 'enctype="multipart/form-data"'));
                                        $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);

                                        $shipping_status_inputs_string = '';
                                        $languages = xtc_get_languages();
                                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                            $shipping_status_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('shipping_status_name[' . $languages[$i]['id'] . ']');
                                        }
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_SHIPPING_STATUS_IMAGE . '<br />' . xtc_draw_file_field('shipping_status_image'));
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_SHIPPING_STATUS_NAME . $shipping_status_inputs_string);
                                        $contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
                                        $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_INSERT . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>');
                                        break;

                                    case 'edit':
                                        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_SHIPPING_STATUS . '</b>');

                                        $contents = array('form' => xtc_draw_form('status', FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
                                        $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);

                                        $shipping_status_inputs_string = '';
                                        $languages = xtc_get_languages();
                                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                            $shipping_status_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('shipping_status_name[' . $languages[$i]['id'] . ']', xtc_get_shipping_status_name($oInfo->shipping_status_id, $languages[$i]['id']));
                                        }
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_SHIPPING_STATUS_IMAGE . '<br />' . xtc_draw_file_field('shipping_status_image', $oInfo->shipping_status_image));
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_SHIPPING_STATUS_NAME . $shipping_status_inputs_string);
                                        if (DEFAULT_SHIPPING_STATUS_ID != $oInfo->shipping_status_id)
                                            $contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
                                        $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id) . '">' . BUTTON_CANCEL . '</a>');
                                        break;

                                    case 'delete':
                                        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SHIPPING_STATUS . '</b>');

                                        $contents = array('form' => xtc_draw_form('status', FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id . '&action=deleteconfirm'));
                                        $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
                                        $contents[] = array('text' => '<br /><b>' . $oInfo->shipping_status_name . '</b>');
                                        if ($remove_status)
                                            $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_DELETE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id) . '">' . BUTTON_CANCEL . '</a>');
                                        break;

                                    default:
                                        if (is_object($oInfo)) {
                                            $heading[] = array('text' => '<b>' . $oInfo->shipping_status_name . '</b>');

                                            $contents[] = array('align' => 'center', 'text' => '<br /><a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id . '&action=edit') . '">' . BUTTON_EDIT . '</a> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>');

                                            $shipping_status_inputs_string = '';
                                            $languages = xtc_get_languages();
                                            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                                $shipping_status_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/' . $languages[$i]['image']) . '&nbsp;' . xtc_get_shipping_status_name($oInfo->shipping_status_id, $languages[$i]['id']);
                                            }

                                            $contents[] = array('text' => $shipping_status_inputs_string);
                                        }
                                        break;
                                }

                                if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
                                    echo '            <td width="25%" class="border" valign="top">' . "\n";

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
