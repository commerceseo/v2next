<?php
/* -----------------------------------------------------------------
 * 	$Id: countries.php 486 2013-07-15 22:08:14Z akausch $
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

if ($_GET['action']) {
    switch ($_GET['action']) {
        case 'insert':
            $countries_name = xtc_db_prepare_input($_POST['countries_name']);
            $countries_iso_code_2 = xtc_db_prepare_input($_POST['countries_iso_code_2']);
            $countries_iso_code_3 = xtc_db_prepare_input($_POST['countries_iso_code_3']);
            $address_format_id = xtc_db_prepare_input($_POST['address_format_id']);

            xtc_db_query("INSERT INTO " . TABLE_COUNTRIES . " (countries_name, countries_iso_code_2, countries_iso_code_3, address_format_id) values ('" . xtc_db_input($countries_name) . "', '" . xtc_db_input($countries_iso_code_2) . "', '" . xtc_db_input($countries_iso_code_3) . "', '" . xtc_db_input($address_format_id) . "')");
            xtc_redirect(xtc_href_link(FILENAME_COUNTRIES));
            break;
        case 'save':
            $countries_id = xtc_db_prepare_input($_GET['cID']);
            $countries_name = xtc_db_prepare_input($_POST['countries_name']);
            $countries_iso_code_2 = xtc_db_prepare_input($_POST['countries_iso_code_2']);
            $countries_iso_code_3 = xtc_db_prepare_input($_POST['countries_iso_code_3']);
            $address_format_id = xtc_db_prepare_input($_POST['address_format_id']);

            xtc_db_query("UPDATE " . TABLE_COUNTRIES . " set countries_name = '" . xtc_db_input($countries_name) . "', countries_iso_code_2 = '" . xtc_db_input($countries_iso_code_2) . "', countries_iso_code_3 = '" . xtc_db_input($countries_iso_code_3) . "', address_format_id = '" . xtc_db_input($address_format_id) . "' where countries_id = '" . xtc_db_input($countries_id) . "'");
            xtc_redirect(xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries_id));
            break;
        case 'deleteconfirm':
            $countries_id = xtc_db_prepare_input($_GET['cID']);

            xtc_db_query("DELETE FROM " . TABLE_COUNTRIES . " where countries_id = '" . xtc_db_input($countries_id) . "'");
            xtc_redirect(xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page']));
            break;
        case 'setlflag':
            $countries_id = xtc_db_prepare_input($_GET['cID']);
            $status = xtc_db_prepare_input($_GET['flag']);
            xtc_db_query("UPDATE " . TABLE_COUNTRIES . " set status = '" . xtc_db_input($status) . "' where countries_id = '" . xtc_db_input($countries_id) . "'");
            xtc_redirect(xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries_id));
            break;
        case 'setflag_all':
            $status = xtc_db_prepare_input($_GET['flag']);
            xtc_db_query("UPDATE " . TABLE_COUNTRIES . " SET status = '" . xtc_db_input($status) . "'");
            xtc_redirect(xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page']));
            break;
    }
}
require(DIR_WS_INCLUDES . 'header.php');
?>

<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td>
                        <table class="table_pageHeading" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE_COUNTRIES; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        <a class="button" href="<?php echo xtc_href_link(FILENAME_COUNTRIES, xtc_get_all_get_params(array('page', 'action', 'cID')) . 'action=setflag_all&flag=1&page=' . $_GET['page']); ?>">alle aktivieren</a> 
                        <a class="button" href="<?php echo xtc_href_link(FILENAME_COUNTRIES, xtc_get_all_get_params(array('page', 'action', 'cID')) . 'action=setflag_all&flag=0&page=' . $_GET['page']); ?>">alle deaktivieren</a>
                    </td>
                </tr>
                <tr>
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top"><table width="100%" cellspacing="0" cellpadding="0" class="dataTable">
                                        <tr class="dataTableHeadingRow">
                                            <th class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_COUNTRY_NAME; ?></th>
                                            <th class="dataTableHeadingContent" align="center" colspan="2"><?php echo TABLE_HEADING_COUNTRY_CODES; ?></th>
                                            <th class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></th>                
                                            <th class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?></th>
                                        </tr>
                                        <?php
                                        $countries_query_raw = "SELECT countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, status, address_format_id 
										 FROM countries 
											ORDER BY countries_name";
                                        $countries_split = new splitPageResults($_GET['page'], ($_GET['anzahl'] != '' ? $_GET['anzahl'] : '20'), $countries_query_raw, $countries_query_numrows);
                                        $countries_query = xtc_db_query($countries_query_raw);
                                        while ($countries = xtc_db_fetch_array($countries_query)) {
                                            $rows++;
                                            if (((!$_GET['cID']) || (@$_GET['cID'] == $countries['countries_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new'))
                                                $cInfo = new objectInfo($countries);

                                            if ((is_object($cInfo)) && ($countries['countries_id'] == $cInfo->countries_id))
                                                echo '<tr class="dataTableRowSelected" onclick="document.location.href=\'' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=edit') . '\'">' . "\n";
                                            else {
                                                echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '" onclick="document.location.href=\'' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries['countries_id']) . '\'">' . "\n";
                                            }
                                            if (file_exists('images/flaggen/' . strtolower($countries['countries_iso_code_2']) . '.png'))
                                                $flagge = '<img src="images/flaggen/' . strtolower($countries['countries_iso_code_2']) . '.png" alt="' . $countries['countries_iso_code_2'] . '" />';
                                            else
                                                $flagge = '&nbsp;';
                                            ?>
                                            <td align="center" valign="middle" width="1">
    <?php echo $flagge; ?> 
                                            </td>
                                            <td><?php echo $countries['countries_name']; ?></td>
                                            <td align="center" width="40"><?php echo $countries['countries_iso_code_2']; ?></td>
                                            <td align="center" width="40"><?php echo $countries['countries_iso_code_3']; ?></td>
                                            <td align="center">
                                                <?php
                                                if ($countries['status'] == '1') {
                                                    echo xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . xtc_href_link(FILENAME_COUNTRIES, xtc_get_all_get_params(array('page', 'action', 'cID')) . 'action=setlflag&flag=0&cID=' . $countries['countries_id'] . '&page=' . $_GET['page']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                                                } else {
                                                    echo '<a href="' . xtc_href_link(FILENAME_COUNTRIES, xtc_get_all_get_params(array('page', 'action', 'cID')) . 'action=setlflag&flag=1&cID=' . $countries['countries_id'] . '&page=' . $_GET['page']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                                                }
                                                ?>
                                            </td>                
                                            <td align="right"><?php if ((is_object($cInfo)) && ($countries['countries_id'] == $cInfo->countries_id)) {
                                                    echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', '');
                                                } else {
                                                    echo '<a href="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries['countries_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                                                } ?>&nbsp;</td>
    <?php
    echo '</tr>';
}
?>
                                        <tr>
                                        </tr>
                                    </table>
                                </td>
                                <?php
                                $heading = array();
                                $contents = array();
                                switch ($_GET['action']) {
                                    case 'new':
                                        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_COUNTRY . '</b>');

                                        $contents = array('form' => xtc_draw_form('countries', FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&action=insert'));
                                        $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_NAME . '<br />' . xtc_draw_input_field('countries_name'));
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_2 . '<br />' . xtc_draw_input_field('countries_iso_code_2'));
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_3 . '<br />' . xtc_draw_input_field('countries_iso_code_3'));
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_ADDRESS_FORMAT . '<br />' . xtc_draw_pull_down_menu('address_format_id', xtc_get_address_formats()));
                                        $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>&nbsp;<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>');
                                        break;
                                    case 'edit':
                                        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_COUNTRY . '</b>');

                                        $contents = array('form' => xtc_draw_form('countries', FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=save'));
                                        $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_NAME . '<br />' . xtc_draw_input_field('countries_name', $cInfo->countries_name));
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_2 . '<br />' . xtc_draw_input_field('countries_iso_code_2', $cInfo->countries_iso_code_2));
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_3 . '<br />' . xtc_draw_input_field('countries_iso_code_3', $cInfo->countries_iso_code_3));
                                        $contents[] = array('text' => '<br />' . TEXT_INFO_ADDRESS_FORMAT . '<br />' . xtc_draw_pull_down_menu('address_format_id', xtc_get_address_formats(), $cInfo->address_format_id));
                                        $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>&nbsp;<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id) . '">' . BUTTON_CANCEL . '</a>');
                                        break;
                                    case 'delete':
                                        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_COUNTRY . '</b>');

                                        $contents = array('form' => xtc_draw_form('countries', FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=deleteconfirm'));
                                        $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
                                        $contents[] = array('text' => '<br /><b>' . $cInfo->countries_name . '</b>');
                                        $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>&nbsp;<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id) . '">' . BUTTON_CANCEL . '</a>');
                                        break;
                                    default:
                                        if (is_object($cInfo)) {
                                            $heading[] = array('text' => '<b>' . $cInfo->countries_name . '</b>');

                                            $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=edit') . '">' . BUTTON_EDIT . '</a> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>');
                                            $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_NAME . '<br />' . $cInfo->countries_name);
                                            $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_2 . ' ' . $cInfo->countries_iso_code_2);
                                            $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_3 . ' ' . $cInfo->countries_iso_code_3);
                                            $contents[] = array('text' => '<br />' . TEXT_INFO_ADDRESS_FORMAT . ' ' . $cInfo->address_format_id);
                                        }
                                        break;
                                }

                                if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
                                    echo '            <td width="25%" class="border" valign="top">' . "\n";

                                    $box = new box;
                                    echo $box->infoBox($heading, $contents);

                                    echo '            </td>' . "\n";
                                }

                                $page_dropdown = '<form name="anzahl" action="' . $_SERVER['REQUEST_URI'] . '" method="GET">' . "\n";

                                if ($_GET['page'] != '')
                                    $page_dropdown .= xtc_draw_hidden_field('page', $_GET['page']) . "\n";

                                $page_options = Array();

                                $page_options[] = Array('id' => '10', 'text' => '10');
                                $page_options[] = Array('id' => '20', 'text' => '20');
                                $page_options[] = Array('id' => '50', 'text' => '50');
                                $page_options[] = Array('id' => '100', 'text' => '100');
                                $page_options[] = Array('id' => '1000', 'text' => 'alle');

                                $page_dropdown .= xtc_draw_pull_down_menu('anzahl', $page_options, ($_GET['anzahl'] != '' ? $_GET['anzahl'] : '20'), 'onchange="this.form.submit()"') . "\n";

                                $page_dropdown .= '</form>' . "\n";
                                ?>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                        <tr>
                                            <td class="smallText" valign="top" align="left">
<?php echo $countries_split->display_count($countries_query_numrows, $_GET['anzahl'], $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?>
                                            </td>
                                            <td class="smallText" align="center">
                                        <?php echo $countries_split->display_links($countries_query_numrows, ($_GET['anzahl'] != '') ? $_GET['anzahl'] : '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page'], xtc_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?>
                                            </td>
                                            <td class="smallText" align="right">
                                                L&auml;nder pro Seite: <?php echo $page_dropdown; ?>
                                            </td>
                                        </tr>
                                        <?php
                                        if (!$_GET['action']) {
                                            ?>
                                            <tr>
                                                <td colspan="2" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&action=new') . '">' . BUTTON_NEW_COUNTRY . '</a>'; ?></td>
                                            </tr>
    <?php
}
?>
                                    </table>
                                </td>
                            </tr>
                        </table></td>
                </tr>
            </table></td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
