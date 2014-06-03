<?php
/* -----------------------------------------------------------------
 * 	$Id: products_parameters.php 420 2013-06-19 18:04:39Z akausch $
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
        $name = xtc_db_prepare_input($_POST['name']);
        $sort_order = xtc_db_prepare_input($_POST['sort_order']);
        xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_PARAMETERS_GROUPS . " (sort_order) VALUES (" . $sort_order . ")");
        $insert_id = xtc_db_insert_id();
        foreach ($name as $key => $value) {
            xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_PARAMETERS_GROUPS_DESCRIPTION . " (group_id, language_id, group_name) VALUES (" . $insert_id . ", " . $key . ", '" . $value . "')");
        }
        xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'page=' . $_GET['page']) . '&products_id=' . $_GET['products_id']);
        break;

    case 'save':
        $gID = xtc_db_prepare_input($_GET['gID']);
        $name = xtc_db_prepare_input($_POST['name']);
        $sort_order = xtc_db_prepare_input($_POST['sort_order']);

        xtc_db_query("UPDATE " . TABLE_PRODUCTS_PARAMETERS_GROUPS . " SET sort_order = '" . xtc_db_input($sort_order) . "' WHERE group_id = " . $gID);
        foreach ($name as $key => $value) {
            xtc_db_query("UPDATE " . TABLE_PRODUCTS_PARAMETERS_GROUPS_DESCRIPTION . " SET group_name = '" . xtc_db_input($value) . "' WHERE group_id = " . $gID . " AND language_id=" . $key);
        }
        xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'page=' . $_GET['page'] . '&products_id=' . $_GET['products_id'] . '&gID=' . $_GET['gID']));
        break;

    case 'deleteconfirm':
        $gID = xtc_db_prepare_input($_GET['gID']);
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_PARAMETERS_GROUPS . " WHERE group_id = '" . xtc_db_input($gID) . "'");
        xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_PARAMETERS_GROUPS_DESCRIPTION . " WHERE group_id = '" . xtc_db_input($gID) . "'");
        xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'page=' . $_GET['page']) . '&products_id=' . $_GET['products_id']);
        break;

    case 'delete':

        break;
}
require(DIR_WS_INCLUDES . 'header.php');
if ($_GET['action'] == 'new') {
    ?>
    <script type="text/javascript">
        <!--
                    function checkIt(form) {
            if (form.sort_order.value != 0)
                return true;
            else {
                alert('<?php echo CHOOSE_SORT_ORDER; ?>');
                return false;
            }
        }
        //-->
    </script>
<?php } ?>

<table class="outerTable" cellspacing="0" cellpadding="0">
    <tr>
        <td class="boxCenter" width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td width="100%">
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%" align="right">
                        <a class="button" href="categories.php?cPath=<?php echo $_GET['category_path']; ?>&pID=<?php echo $_GET['products_id']; ?>">zur&uuml;ck zum Produkt</a>
                    </td>
                </tr>
                <td>
                    <?php echo xtc_draw_form('search', FILENAME_PRODUCTS_PARAMETERS_EDIT, '', 'post') . xtc_draw_hidden_field('products_id', $_GET['products_id']) . xtc_draw_hidden_field('category_path', $_GET['category_path']) . "\n"; ?>
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td valign="top">
                                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                    <tr class="dataTableHeadingRow">
                                        <td class="dataTableHeadingContent" width="1%"><?php echo TABLE_HEADING_SELECT; ?></td>
                                        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PARAMETERS_NAME; ?> </td>
                                        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SORT_ORDER; ?></td>
                                        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                                    </tr>
                                    <?php
                                    $groups_query_raw = "SELECT g.group_id, g.sort_order, gd.group_name FROM " . TABLE_PRODUCTS_PARAMETERS_GROUPS . " g, " . TABLE_PRODUCTS_PARAMETERS_GROUPS_DESCRIPTION . " gd WHERE g.group_id=gd.group_id AND gd.language_id=" . $_SESSION['languages_id'] . " ORDER BY g.sort_order";
                                    $groups_split = new splitPageResults($_GET['page'], $_GET['anzahl'] != '' ? $_GET['anzahl'] : '100', $groups_query_raw, $groups_query_numrows);
                                    $groups_query = xtc_db_query($groups_query_raw);
                                    $languages = xtc_get_languages();
                                    while ($groups = xtc_db_fetch_array($groups_query)) {
                                        $rows++;
                                        if (((!$_GET['gID']) || (@$_GET['gID'] == $groups['group_id'])) && (!$gInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
                                            $gInfo = new objectInfo($groups);
                                            $names_query = xtc_db_query("SELECT group_name, language_id FROM " . TABLE_PRODUCTS_PARAMETERS_GROUPS_DESCRIPTION . " WHERE group_id=" . $groups['group_id']);
                                            $name = array();
                                            while ($names = xtc_db_fetch_array($names_query)) {
                                                $name[$names['language_id']] = $names['group_name'];
                                            }
                                        }
                                        $select_query = xtc_db_query("SELECT count(parameters_id) as count FROM " . TABLE_PRODUCTS_PARAMETERS . " WHERE group_id=" . $groups['group_id'] . " AND products_id=" . $_GET['products_id']);
                                        $select_result = xtc_db_fetch_array($select_query);
                                        $selected = $select_result['count'] > 0 ? true : false;
                                        if ((is_object($gInfo)) && ($groups['group_id'] == $gInfo->group_id)) {
                                            echo '<tr class="dataTableRowSelected">' . "\n";
                                        } else {
                                            echo '<tr class="' . (($i % 2 == 0) ? 'dataTableRow' : 'dataWhite') . '">' . "\n";
                                        }
                                        echo '<td class="dataTableContent">' . xtc_draw_checkbox_field('sel[]', $groups['group_id'], $selected) . '</td>' . "\n";
                                        echo '<td class="dataTableContent">' . $groups['group_name'] . '</td>' . "\n";
                                        ?>
                                        <td class="dataTableContent"><?php echo $groups['sort_order']; ?></td>
                                        <td class="dataTableContent" align="right">
                                            <?php
                                            if ((is_object($gInfo)) && ($groups['group_id'] == $gInfo->group_id)) {
                                                echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif');
                                            } else {
                                                echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'page=' . $_GET['page'] . '&products_id=' . $_GET['products_id'] . '&gID=' . $groups['group_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>&nbsp;';
                                                echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'page=' . $_GET['page'] . '&products_id=' . $_GET['products_id'] . ' &gID=' . $groups['group_id'] . '&action=edit') . '">' . xtc_image(DIR_WS_IMAGES . 'icon_edit.gif') . '</a>';
                                            }
                                            ?>&nbsp;
                                        </td>
                            </tr>
<?php } ?>
                        <tr>
                            <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                                    <tr>
                                        <td class="smallText" valign="top"><?php echo $groups_split->display_count($groups_query_numrows, ($_GET['anzahl'] != '' ? $_GET['anzahl'] : '100'), $_GET['page'], TEXT_DISPLAY_NUMBER_OF_GROUPS); ?></td>
                                        <td class="smallText" align="right"><?php echo $groups_split->display_links($groups_query_numrows, ($_GET['anzahl'] != '' ? $_GET['anzahl'] : '100'), MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                                    </tr>
                                </table></td>
                        </tr>
                    </table></form></td>
                <?php
                $heading = array();
                $contents = array();
                switch ($_GET['action']) {
                    case 'new':
                        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_GROUP . '</b>');
                        $contents = array('form' => xtc_draw_form('languages', FILENAME_PRODUCTS_PARAMETERS, 'action=insert&products_id=' . $_GET['products_id'], 'POST', 'onsubmit="return checkIt(this);"'));
                        $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                            $contents[] = array('text' => '<br />' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . " " . TEXT_INFO_GROUP_NAME . '<br />' . xtc_draw_input_field('name[' . $languages[$i]['id'] . ']'));
                        }
                        $contents[] = array('text' => '<br />' . TEXT_INFO_GROUP_SORT_ORDER . '<br />' . xtc_draw_input_field('sort_order'));
                        $contents[] = array('align' => 'center', 'text' => '<br /><input class="button" type="submit" value="' . BUTTON_INSERT . '" /> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'page=' . $_GET['page'] . '&products_id=' . $_GET['products_id'] . '&gID=' . $_GET['gID']) . '">' . BUTTON_CANCEL . '</a>');
                        break;

                    case 'edit':
                        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_GROUP . '</b>');
                        $contents = array('form' => xtc_draw_form('languages', FILENAME_PRODUCTS_PARAMETERS, 'page=' . $_GET['page'] . '&products_id=' . $_GET['products_id'] . '&gID=' . $gInfo->group_id . '&action=save'));
                        $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
                        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                            $contents[] = array('text' => '<br />' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . " " . TEXT_INFO_GROUP_NAME . '<br />' . xtc_draw_input_field('name[' . $languages[$i]['id'] . ']', $name[$languages[$i]['id']]));
                        };
                        $contents[] = array('text' => '<br />' . TEXT_INFO_GROUP_SORT_ORDER . '<br />' . xtc_draw_input_field('sort_order', $gInfo->sort_order));
                        $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'page=' . $_GET['page'] . '&products_id=' . $_GET['products_id'] . '&gID=' . $gInfo->group_id) . '">' . BUTTON_CANCEL . '</a>');
                        break;

                    case 'delete':
                        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_GROUP . '</b>');
                        $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
                        $contents[] = array('text' => '<br /><b>' . $gInfo->name . '</b>');
                        $contents[] = array('align' => 'center', 'text' => '<br />' . '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'page=' . $_GET['page'] . '&products_id=' . $_GET['products_id'] . '&gID=' . $gInfo->group_id . '&action=deleteconfirm') . '">' . BUTTON_DELETE . '</a><a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'page=' . $_GET['page'] . '&products_id=' . $_GET['products_id'] . '&gID=' . $gInfo->group_id) . '">' . BUTTON_CANCEL . '</a>');
                        break;

                    default:
                        if (is_object($gInfo)) {
                            $heading[] = array('text' => '<b>' . $gInfo->group_name . '</b>');
                            $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'page=' . $_GET['page'] . '&products_id=' . $_GET['products_id'] . '&gID=' . $gInfo->group_id . '&action=edit') . '">' . BUTTON_EDIT . '</a> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'page=' . $_GET['page'] . '&products_id=' . $_GET['products_id'] . '&gID=' . $gInfo->group_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>');
                            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                $contents[] = array('text' => '<br />' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . " " . TEXT_INFO_GROUP_NAME . $name[$languages[$i]['id']]);
                            };
                            $contents[] = array('text' => '<br />' . TEXT_INFO_GROUP_SORT_ORDER . ' ' . $gInfo->sort_order);
                        }
                        break;
                }

                if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
                    echo '            <td width="25%" valign="top">' . "\n";
                    $box = new box;
                    echo $box->infoBox($heading, $contents);
                    echo '            </td>' . "\n";
                }
                $parameters_page_dropdown = '<form name="anzahl" action="' . $_SERVER['REQUEST_URI'] . '" method="GET">' . "\n";

                if ($_GET['oID'] != '')
                    $parameters_page_dropdown .= xtc_draw_hidden_field('oID', $_GET['oID']);
                if ($_GET['page'] != '')
                    $parameters_page_dropdown .= xtc_draw_hidden_field('page', $_GET['page']) . "\n";

                $parameters_dropdown_options = array();

                $parameters_dropdown_options[] = array('id' => '100', 'text' => '100');

                $parameters_page_dropdown .= xtc_draw_pull_down_menu('anzahl', $parameters_dropdown_options, ($_GET['anzahl'] != '' ? $_GET['anzahl'] : '20'), 'onchange="this.form.submit()"') . "\n";

                $parameters_page_dropdown .= '</form>' . "\n";
                ?>
    </tr>
    <tr>
        <td colspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <?php if (!$_GET['action']) { ?>
                        <td>
                            <input type="submit" class="button" onClick="this.blur();" value="<?php echo BUTTON_EDIT; ?>"/>
                        </td>
                        <td align="right">
                            <?php echo '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_PARAMETERS, 'page=' . $_GET['page'] . '&products_id=' . $_GET['products_id'] . '&gID=' . $gInfo->group_id . '&action=new') . '">' . BUTTON_NEW_GROUP . '</a>'; ?>
                        </td>
                    <?php } ?>
                    <td align="right">
                        Parameter pro Seite: <?php echo $parameters_page_dropdown; ?>
                    </td>
                </tr>
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
